<?php

namespace App\Http\Controllers;

use App\Exports\MaterialsMovementsExport;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:movements.index')->only('index');
        $this->middleware('check.permission:movements.create')->only('create', 'store');
        $this->middleware('check.permission:movements.edit')->only('edit', 'update');
        $this->middleware('check.permission:movements.destroy')->only('destroy');
        $this->middleware('check.permission:movements.query_sn')->only('getAvailableSerialNumbers');
        $this->middleware('check.permission:movements.material_quantity')->only('getAvailableQuantity');
        $this->middleware('check.permission:movements.history')->only('history');
        $this->middleware('check.permission:movements.history_data')->only('history');
        $this->middleware('check.permission:movements.pdf')->only('exportMovementsPDF');
        $this->middleware('check.permission:movements.excel')->only('export');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::all();
        $warehouses = Warehouse::all();
        return view('gestisp.materials.movements.index', compact('materials', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Iniciando registro de movimiento de material', ['request' => $request->all()]);

            $request->validate([
                'type' => 'required|in:Entrada,Salida,Transferencia',
                'materials.*.material_id' => 'required|exists:materials,id',
                'materials.*.quantity' => 'required|numeric|min:1',
                'materials.*.unit_of_measurement' => 'required|string',
                'warehouse_origin_id' => 'nullable|exists:warehouses,id',
                'warehouse_destination_id' => 'nullable|exists:warehouses,id',
                'materials.*.serial_numbers.*' => 'nullable|string|required_if:is_equipment,1',
                'reason' => 'required|string|max:100'
            ]);

            \Log::info('Validación pasada correctamente');

            $movements = [];
            $result = DB::transaction(function () use ($request, &$movements) {
                try {
                    foreach ($request->materials as $materialData) {
                        \Log::info('Procesando material', ['material_data' => $materialData]);

                        $material = Material::findOrFail($materialData['material_id']);
                        $quantity = $materialData['quantity'];
                        $isEquipment = $material->is_equipment;

                        // Validar cantidad en inventario para salidas y transferencias
                        if (in_array($request->type, ['Salida', 'Transferencia'])) {
                            if ($isEquipment) {
                                // Para equipos, contar el número total de equipos disponibles
                                $availableQuantity = Inventory::where('warehouse_id', $request->warehouse_origin_id)
                                    ->where('material_id', $material->id)
                                    ->count();

                                \Log::info('Verificando cantidad de equipos', [
                                    'disponible' => $availableQuantity,
                                    'solicitada' => $quantity
                                ]);

                                if ($availableQuantity < $quantity) {
                                    throw new \Exception("Cantidad insuficiente de equipos en el almacén de origen. Disponibles: {$availableQuantity}, Solicitados: {$quantity}");
                                }

                                // Validar que la cantidad de números de serie coincida con la cantidad solicitada
                                if (!isset($materialData['serial_numbers']) || count($materialData['serial_numbers']) != $quantity) {
                                    throw new \Exception("La cantidad de números de serie seleccionados debe ser igual a la cantidad solicitada");
                                }
                            } else {
                                // Para materiales normales
                                $inventory = Inventory::where('warehouse_id', $request->warehouse_origin_id)
                                    ->where('material_id', $material->id)
                                    ->first();

                                \Log::info('Verificando inventario de material', [
                                    'inventory' => $inventory,
                                    'required_quantity' => $quantity
                                ]);

                                if (!$inventory || $inventory->quantity < $quantity) {
                                    throw new \Exception("Cantidad insuficiente en el almacén de origen. Disponible: {$inventory->quantity}, Solicitado: {$quantity}");
                                }
                            }
                        }

                        // Crear movimiento
                        if ($isEquipment && isset($materialData['serial_numbers'])) {
                            \Log::info('Procesando equipo con números de serie', [
                                'serial_numbers' => $materialData['serial_numbers']
                            ]);

                            foreach ($materialData['serial_numbers'] as $serialNumber) {
                                $movement = MaterialMovement::create([
                                    'type' => $request->type,
                                    'material_id' => $material->id,
                                    'quantity' => 1,
                                    'unit_of_measurement' => $materialData['unit_of_measurement'],
                                    'warehouse_origin_id' => $request->warehouse_origin_id,
                                    'warehouse_destination_id' => $request->warehouse_destination_id,
                                    'serial_number' => $serialNumber,
                                    'user_id' => auth()->id(),
                                    'reason' => $request->reason,
                                ]);

                                \Log::info('Movimiento creado para equipo', ['movement' => $movement]);

                                // Actualizar inventario
                                $this->updateInventory(
                                    $request->type,
                                    $request->warehouse_origin_id,
                                    $request->warehouse_destination_id,
                                    $material->id,
                                    1,
                                    $materialData['unit_of_measurement'],
                                    $serialNumber
                                );

                                $movements[] = $movement;
                            }
                        } else {
                            \Log::info('Procesando material sin números de serie');

                            $movement = MaterialMovement::create([
                                'type' => $request->type,
                                'material_id' => $material->id,
                                'quantity' => $quantity,
                                'unit_of_measurement' => $materialData['unit_of_measurement'],
                                'warehouse_origin_id' => $request->warehouse_origin_id,
                                'warehouse_destination_id' => $request->warehouse_destination_id,
                                'user_id' => auth()->id(),
                                'reason' => $request->reason,
                            ]);

                            \Log::info('Movimiento creado', ['movement' => $movement]);

                            // Actualizar inventario
                            $this->updateInventory(
                                $request->type,
                                $request->warehouse_origin_id,
                                $request->warehouse_destination_id,
                                $material->id,
                                $quantity,
                                $materialData['unit_of_measurement']
                            );

                            $movements[] = $movement;
                        }
                    }

                    return true;
                } catch (\Exception $e) {
                    \Log::error('Error en la transacción', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            \Log::info('Movimiento completado exitosamente');

            // Generar el PDF utilizando la vista
            $pdf = Pdf::loadView('gestisp.materials.movements.pdf_summary', compact('movements'));

            // Guardar el PDF en un archivo temporal
            $pdfPath = storage_path('app/public/movimiento_' . time() . '.pdf');
            $pdf->save($pdfPath);

            return redirect()->route('movements.index')->with([
                'success-create' => 'Movimiento registrado exitosamente.',
                'pdfPath' => $pdfPath
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al procesar el movimiento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mostrar el error en pantalla de manera amigable
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    protected function updateInventory($type, $warehouseOriginId, $warehouseDestinationId, $materialId, $quantity, $unitOfMeasurement, $serialNumber = null)
    {
        try {
            \Log::info('Iniciando actualización de inventario', [
                'type' => $type,
                'warehouse_origin' => $warehouseOriginId,
                'warehouse_destination' => $warehouseDestinationId,
                'material' => $materialId,
                'quantity' => $quantity,
                'serial_number' => $serialNumber
            ]);

            if ($type === 'Entrada') {
                if ($serialNumber) {
                    $inventory = Inventory::create([
                        'warehouse_id' => $warehouseDestinationId,
                        'material_id' => $materialId,
                        'quantity' => 1,
                        'unit_of_measurement' => $unitOfMeasurement,
                        'serial_number' => $serialNumber
                    ]);
                    \Log::info('Entrada de equipo creada', ['inventory' => $inventory]);
                } else {
                    $inventory = Inventory::updateOrCreate(
                        [
                            'warehouse_id' => $warehouseDestinationId,
                            'material_id' => $materialId,
                            'serial_number' => null
                        ],
                        [
                            'quantity' => DB::raw("COALESCE(quantity, 0) + $quantity"),
                            'unit_of_measurement' => $unitOfMeasurement,
                        ]
                    );
                    \Log::info('Entrada de material actualizada', ['inventory' => $inventory]);
                }
            } elseif ($type === 'Salida') {
                if ($serialNumber) {
                    $inventory = Inventory::where('warehouse_id', $warehouseOriginId)
                        ->where('material_id', $materialId)
                        ->where('serial_number', $serialNumber)
                        ->first();

                    if ($inventory) {
                        $inventory->delete();
                        \Log::info('Equipo eliminado del inventario', ['serial_number' => $serialNumber]);
                    }
                } else {
                    $inventory = Inventory::where('warehouse_id', $warehouseOriginId)
                        ->where('material_id', $materialId)
                        ->first();

                    if ($inventory) {
                        $inventory->update([
                            'quantity' => $inventory->quantity - $quantity
                        ]);
                        \Log::info('Cantidad actualizada en salida', ['inventory' => $inventory]);
                    }
                }
            } elseif ($type === 'Transferencia') {
                if ($serialNumber) {
                    $inventory = Inventory::where('warehouse_id', $warehouseOriginId)
                        ->where('material_id', $materialId)
                        ->where('serial_number', $serialNumber)
                        ->first();

                    if ($inventory) {
                        $inventory->update(['warehouse_id' => $warehouseDestinationId]);
                        \Log::info('Equipo transferido', ['inventory' => $inventory]);
                    }
                } else {
                    DB::transaction(function () use ($warehouseOriginId, $warehouseDestinationId, $materialId, $quantity, $unitOfMeasurement) {
                        // Reducir en origen
                        $originInventory = Inventory::where('warehouse_id', $warehouseOriginId)
                            ->where('material_id', $materialId)
                            ->first();

                        if ($originInventory) {
                            $originInventory->update([
                                'quantity' => $originInventory->quantity - $quantity
                            ]);
                            \Log::info('Cantidad reducida en origen', ['inventory' => $originInventory]);
                        }

                        // Aumentar en destino
                        $destinationInventory = Inventory::updateOrCreate(
                            [
                                'warehouse_id' => $warehouseDestinationId,
                                'material_id' => $materialId,
                                'serial_number' => null
                            ],
                            [
                                'quantity' => DB::raw("COALESCE(quantity, 0) + $quantity"),
                                'unit_of_measurement' => $unitOfMeasurement,
                            ]
                        );
                        \Log::info('Cantidad aumentada en destino', ['inventory' => $destinationInventory]);
                    });
                }
            }

            \Log::info('Actualización de inventario completada exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error en actualización de inventario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getAvailableSerialNumbers($warehouseId, $materialId)
    {
        $serialNumbers = Inventory::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->whereNotNull('serial_number')
            ->pluck('serial_number');

        return response()->json($serialNumbers);
    }

    //Obtener la cantidad de un material

    public function getAvailableQuantity($warehouseId, $materialId)
    {
        // Obtener la cantidad total disponible en el inventario
        $material = Material::findOrFail($materialId);
        $quantity = 0;

        if ($material->is_equipment) {
            // Contar el número de equipos individuales en el inventario
            $quantity = Inventory::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->count();
        } else {
            // Sumar la cantidad total del material en el inventario
            $quantity = Inventory::where('warehouse_id', $warehouseId)
                ->where('material_id', $materialId)
                ->sum('quantity');
        }

        return response()->json(['quantity' => $quantity]);
    }

    //Mostrar historial de movimientos de almacén:

    public function history(Request $request)
    {
        // Inicializar la consulta base con las relaciones necesarias
        $query = MaterialMovement::with(['warehouseOrigin', 'warehouseDestination', 'material', 'user']);

        // Inicializar la consulta base con las relaciones necesarias
        $query = MaterialMovement::with(['warehouseOrigin', 'warehouseDestination', 'material', 'user']);

        // Aplicar filtro por campo específico si se ha establecido
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $query->where($request->filter_field, 'LIKE', "%{$request->filter_value}%");
        }

        // Aplicar filtro de rango de fechas si se han establecido
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Aplicar paginación
        $perPage = $request->input('per_page', 12);
        $movements = $query->orderBy('created_at', 'desc')->simplePaginate($perPage);

        // Retornar la vista con los movimientos filtrados
        return view('gestisp.materials.movements.history', compact('movements'));
    }

    //Exportar filtrado en PDF
    public function exportMovementsPDF(Request $request)
    {
        // Inicializar la consulta base con las relaciones necesarias
        $query = MaterialMovement::with(['warehouseOrigin', 'warehouseDestination', 'material', 'user']);

        if (session()->has('branch_id')) {
            $query->whereHas('warehouseDestination', function ($query) {
                $query->where('branch_id', session('branch_id'));
            });
        }


        // Aplicar filtro por campo específico si se ha establecido
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $query->where($request->filter_field, 'LIKE', "%{$request->filter_value}%");
        }

        // Aplicar filtro de rango de fechas si se han establecido
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Obtener los movimientos filtrados sin paginación
        $movements = $query->orderBy('created_at', 'desc')->get();

        // Generar el PDF utilizando la vista
        $pdf = Pdf::loadView('gestisp.materials.movements.pdf', compact('movements'));

        // Descargar el PDF con un nombre de archivo adecuado
        return $pdf->download('historial_movimientos.pdf');
    }

    //Exportar todos los movimientos en PDF

    public function export()
    {
        //Función para exportar los movimientos a un excel
        return (new MaterialsMovementsExport)->download('listado_de_movimientos_de_almacen.xlsx');
    }
    /**
     * Display the specified resource.
     */
    public function show(MaterialMovement $materialMovement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialMovement $materialMovement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialMovement $materialMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialMovement $materialMovement)
    {
        //
    }
}
