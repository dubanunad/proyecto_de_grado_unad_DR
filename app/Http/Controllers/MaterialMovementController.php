<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialMovementController extends Controller
{
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

            $result = DB::transaction(function () use ($request) {
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
            return redirect()->route('movements.index')->with('success-create', 'Movimiento registrado exitosamente.');

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
