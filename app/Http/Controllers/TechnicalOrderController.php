<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\TechnicalOrder;
use App\Models\TechnicalOrderMaterial;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TechnicalOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:technicals_orders.index')->only('index');
        $this->middleware('check.permission:technicals_orders.create')->only('create');
        $this->middleware('check.permission:technicals_orders.store')->only('store');
        $this->middleware('check.permission:technicals_orders.edit')->only('edit');
        $this->middleware('check.permission:technicals_orders.update')->only('update');
        $this->middleware('check.permission:technicals_orders.destroy')->only('destroy');
        $this->middleware('check.permission:technicals_orders.my_technical_orders')->only('myTechnicalOrders');
        $this->middleware('check.permission:technicals_orders.getSerialNumbers')->only('getSerialNumbers');
        $this->middleware('check.permission:technicals_orders.verification')->only('orderVerification');
        $this->middleware('check.permission:technicals_orders.process')->only('processOrder');
        $this->middleware('check.permission:technical_order.verification_process')->only('verificationOrderProcess');
        $this->middleware('check.permission:technical_orders.reject')->only('orderReject');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, TechnicalOrder $technicalOrder)
    {
        // Obtener los parámetros de filtrado
        $filterField = $request->input('filter_field');
        $filterValue = $request->input('filter_value');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 12); // Valor por defecto: 12

        /// Obtener los usuarios de la sucursal en sesión
        $branchId = Session('branch_id');
        $users = User::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId); // Filtrar por la sucursal en sesión
        })->get();

        // Iniciar la consulta base
        $query = TechnicalOrder::where('branch_id', Session('branch_id'));

        // Aplicar filtros dinámicos
        if ($filterField && $filterValue) {
            // Si el campo es 'assigned_user', buscar por el nombre del técnico asignado
            if ($filterField === 'assigned_user') {
                $query->whereHas('assignedUser', function ($q) use ($filterValue) {
                    $q->where('name', 'like', "%$filterValue%");
                });
            } else {
                // Para otros campos, aplicar el filtro directamente
                $query->where($filterField, 'like', "%$filterValue%");
            }
        }

        // Filtrar por rango de fechas (si se proporcionan ambas fechas)
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Paginar los resultados
        $technical_orders = $query->orderBy('created_at', 'desc')->simplePaginate($perPage);

        // Pasar los filtros actuales a la vista para mantenerlos en el formulario
        $filters = [
            'filter_field' => $filterField,
            'filter_value' => $filterValue,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'per_page' => $perPage,
        ];



        return view('gestisp.technicals_orders.index', compact('technical_orders', 'filters', 'users'));
    }

    //Ver ordenes para verificacion

    public function orderVerification(Request $request){

        /// Obtener los usuarios de la sucursal en sesión
        $branchId = Session('branch_id');
        $users = User::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId); // Filtrar por la sucursal en sesión
        })->get();

        $technical_orders = TechnicalOrder::where('branch_id', $branchId)
                ->where('status', 'Prefinalizada')
                ->orderBy('created_at', 'desc')
                ->simplePaginate(12);



        return view('gestisp.technicals_orders.verification_orders', compact('technical_orders', 'users'));
    }

    //Proceso de verificación de orden
    public function verificationOrderProcess(Request $request, TechnicalOrder $technicalOrder)
    {
        // Validar el comentario de verificación
        $request->validate([
            'verification_comment' => 'required|string',
        ]);

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Determinar la acción (Cerrar o Rechazar)
        if ($request->has('close_order')) {
            // Cambiar el estado de la orden a "Cerrado"
            $technicalOrder->status = 'Cerrada';
            $technicalOrder->save();

            // Guardar la verificación en la tabla TechnicalOrderVerification
            $technicalOrder->verifications()->create([
                'verified_by' => $user->id,
                'status' => 'Cerrada',
                'comments' => $request->input('verification_comment'),
            ]);

            //Si la orden que se está trabajando tiene como detalle 'Instalacion de servicio' o 'Reconexion', actualizar el estado del contrato al que pertece a Activo
            if($technicalOrder->detail == 'Instalacion de servicio' ||  $technicalOrder->detail == 'Reconexión' || $technicalOrder->detail == 'Instalación de servicio (creación automática)' ){
                $contract = Contract::where('id', $technicalOrder->contract_id)->first();
                $contract->update(['status' => 'Activo', 'activation_date' => now()]);

                //Si la orden es por corte o suspensión temporal
            }elseif ($technicalOrder->detail == 'Corte de servicio' ||  $technicalOrder->detail == 'Suspensión temporal'){
                $contract = Contract::where('id', $technicalOrder->contract_id)->first();
                $contract->update(['status' => 'Suspendido']);
            }

            return redirect()->route('technicals_orders.verification')->with('success', 'La orden ha sido cerrada exitosamente.');

        } elseif ($request->has('reject_order')) {
            // Cambiar el estado de la orden a "Pendiente"
            $technicalOrder->status = 'Pendiente';
            $technicalOrder->save();

            // Guardar la verificación en la tabla TechnicalOrderVerification
            $technicalOrder->verifications()->create([
                'verified_by' => $user->id,
                'status' => 'Pendiente',
                'comments' => $request->input('verification_comment'),
            ]);

            return redirect()->route('technicals_orders.verification')->with('warning', 'La orden ha sido rechazada y está pendiente de corrección.');
        }

        // Si no se selecciona ninguna acción, redirigir con un mensaje de error
        return redirect()->route('technicals_orders.verification')->with('error', 'No se seleccionó ninguna acción.');
    }

    //Rechazar orden por parte del técnico

    public function orderReject(TechnicalOrder $technicalOrder, Request $request){

        // Validar el motivo del rechazo
        $request->validate([
            'reason' => 'required|string',
        ]);

        // Cambiar el estado de la orden a "Pendiente"
        $technicalOrder->status = 'Rechazada';
        $technicalOrder->rejection_reason = $request->input('reason'); // Guardar el motivo del rechazo
        $technicalOrder->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('technicals_orders.my_technical_orders')->with('success', 'La orden ha sido rechazada.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contract $contract)
    {
        return view('gestisp.technicals_orders.create', compact('contract'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Obtén el ID de la sucursal y el usuario autenticado
            $branchId = session('branch_id');
            $createdBy = Auth::id();

            // Validar si existe una orden técnica en curso para el contrato
            $existingOrder = TechnicalOrder::where('contract_id', $request->contract_id)
                ->where('status', '!=', 'Cerrada')
                ->exists();

            if ($existingOrder) {
                return redirect()->route('contracts.show', $request->contract_id)
                    ->with('error', 'Ya existe una orden técnica en curso para este contrato.');
            }

            // Log: Información de depuración
            Log::info('Creando orden técnica', [
                'contract_id' => $request->contract_id,
                'branch_id' => $branchId,
                'created_by' => $createdBy,
                'order_type' => $request->order_type,
                'order_detail' => $request->order_detail,
                'initial_comment' => $request->initial_comment,
            ]);

            // Crea la orden técnica
            TechnicalOrder::create([
                'contract_id' => $request->contract_id,
                'branch_id' => $branchId,
                'created_by' => $createdBy,
                'type' => $request->order_type,
                'detail' => $request->order_detail,
                'initial_comment' => $request->initial_comment,
                'status' => 'Pendiente', // O cualquier estado inicial que uses
            ]);

            // Log: Éxito
            Log::info('Orden técnica creada exitosamente.');

            // Redirige a la ruta contracts.show con el ID del contrato
            return redirect()->route('contracts.show', $request->contract_id)
                ->with('success', 'La orden técnica se ha creado correctamente.');
        } catch (\Exception $e) {
            // Log: Error
            Log::error('Error al crear la orden técnica: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Redirige con un mensaje de error
            return redirect()->route('contracts.show', $request->contract_id)
                ->with('error', 'Hubo un error al crear la orden técnica: ' . $e->getMessage());
        }
    }


    //Manejar las órdenes del usuario técnico

    public function myTechnicalOrders(TechnicalOrder $technicalOrder){

        // Obtener el ID de la sucursal del usuario en sesión
        $branchId = session('branch_id');

        // Obtener el almacén de la sucursal
        $warehouse = Warehouse::where('user_id', Auth::user()->id)->first();

        if ($warehouse) {
            // Obtener los materiales que tienen inventario en ese almacén
            $materials = Material::whereHas('inventories', function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id)
                    ->where('quantity', '>', 0); // Solo materiales con cantidad mayor a 0
            })->with(['inventories' => function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id);
            }])->get();

            // Totalizar las cantidades para materiales de tipo "equipo"
            foreach ($materials as $material) {
                if ($material->is_equipment) {
                    $totalQuantity = $material->inventories->sum('quantity');
                    $material->total_quantity = $totalQuantity;
                } else {
                    $material->total_quantity = $material->inventories->first()->quantity ?? 0;
                }
            }
        } else {
            // Si no hay almacén, devolver una colección vacía
            $materials = collect();
        }

        $technical_orders = TechnicalOrder::where('branch_id', Session('branch_id'))
            ->where('user_assigned', Auth::user()->id)
            ->where('status', 'Asignada')
            ->simplePaginate(12);


        return view('gestisp.technicals_orders.my_technical_orders', compact('technical_orders', 'materials'));

    }

    public function getSerialNumbers($materialId)
    {
        // Obtener el almacén del usuario en sesión
        $warehouse = Warehouse::where('user_id', Auth::user()->id)->first();

        if ($warehouse) {
            // Obtener los números de serie disponibles para el material en el almacén
            $serialNumbers = Inventory::where('warehouse_id', $warehouse->id)
                ->where('material_id', $materialId)
                ->whereNotNull('serial_number')
                ->pluck('serial_number');

            return response()->json($serialNumbers);
        }

        return response()->json([]);
    }

    public function processOrder(Request $request, $id)
    {

        try {
            // Validar la solicitud
            $request->validate([
                'observations_technical' => 'required|string',
                'client_observation' => 'required|string',
                'solution' => 'required|string',
                'material_id' => 'nullable|array',
                'quantity' => 'nullable|array',
                'serial_number' => 'nullable|array',
                'images' => 'nullable|array'
            ]);

            // Iniciar transacción
            DB::beginTransaction();

            // Obtener la orden técnica
            $technicalOrder = TechnicalOrder::findOrFail($id);

            // Obtener el almacén del usuario en sesión
            $warehouse = Warehouse::where('user_id', Auth::user()->id)->first();

            if (!$warehouse) {
                throw new \Exception('No se encontró un almacén asociado al usuario.');
            }

            if($request->hasFile('images')){

                $imagPaths = [];

                foreach ($request->file('images') as $image) {
                    $path = $image->store('technical_orders/images', 'public');
                    $imagPaths[]= 'storage/' .$path;
                }

                // Actualizar la orden técnica
                $technicalOrder->update([
                    'observations_technical' => $request->input('observations_technical'),
                    'client_observation' => $request->input('client_observation'),
                    'solution' => $request->input('solution'),
                    'status' => 'Prefinalizada',
                    'images' => json_encode($imagPaths),

                ]);

            }else{

                // Actualizar la orden técnica sin imágenes
                $technicalOrder->update([
                    'observations_technical' => $request->input('observations_technical'),
                    'client_observation' => $request->input('client_observation'),
                    'solution' => $request->input('solution'),
                    'status' => 'Prefinalizada',
                ]);
            }



            // Procesar los materiales
            if ($request->has('material_id')) {
                foreach ($request->input('material_id') as $index => $materialId) {
                    if (empty($materialId)) continue; // Saltar entradas vacías

                    $quantity = $request->input('quantity')[$index];
                    $serialNumber = $request->input('serial_number')[$index] ?? null;

                    // Obtener el inventario actual del material
                    $inventory = Inventory::where('warehouse_id', $warehouse->id)
                        ->where('material_id', $materialId)
                        ->when($serialNumber, function ($query) use ($serialNumber) {
                            return $query->where('serial_number', $serialNumber);
                        })
                        ->first();

                    if (!$inventory) {
                        throw new \Exception('No se encontró inventario para el material seleccionado.');
                    }

                    // Verificar si hay suficiente stock
                    if ($inventory->quantity < $quantity) {
                        throw new \Exception("No hay suficiente stock para el material ID: {$materialId}");
                    }

                    // Crear el registro en technical_orders_materials
                    $technicalOrder->materials()->create([
                        'material_id' => $materialId,
                        'quantity' => $quantity,
                        'serial_number' => $serialNumber,
                    ]);

                    // Actualizar el inventario
                    $inventory->quantity -= $quantity;
                    $inventory->save();

                    // Si es un equipo y tiene número de serie, actualizar o eliminar el registro específico
                    if ($serialNumber) {
                        if ($quantity == 1) {
                            $inventory->delete(); // Eliminar el registro si se usa completamente
                        } else {
                            throw new \Exception('Los equipos con número de serie solo pueden tener cantidad 1.');
                        }
                    }
                }
                //Insertar el número de SN que trae la orden al contrato
                $contract = Contract::where('id', $technicalOrder->contract_id)->first();
                $contract->update(['cpe_sn' => $serialNumber]);
            }



            // Confirmar transacción
            DB::commit();

            // Redirigir con mensaje de éxito
            return redirect()->route('technicals_orders.my_technical_orders')
                ->with('success', 'Orden procesada correctamente.');

        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();

            // Redirigir con mensaje de error
            return redirect()->back()
                ->with('error', 'Error al procesar la orden: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(TechnicalOrder $technicalOrder)
    {
        // Obtener el almacén del usuario
        $warehouse = Warehouse::where('user_id', Auth::user()->id)->first();

        if ($warehouse) {
            // Obtener los materiales que tienen inventario en ese almacén
            $materials = Material::whereHas('inventories', function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id)
                    ->where('quantity', '>', 0); // Solo materiales con cantidad mayor a 0
            })->with(['inventories' => function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id);
            }])->get();

            // Totalizar las cantidades para materiales de tipo "equipo"
            foreach ($materials as $material) {
                if ($material->is_equipment) {
                    $totalQuantity = $material->inventories->sum('quantity');
                    $material->total_quantity = $totalQuantity;
                } else {
                    $material->total_quantity = $material->inventories->first()->quantity ?? 0;
                }
            }
        } else {
            // Si no hay almacén, devolver una colección vacía
            $materials = collect();
        }

        return view('gestisp.technicals_orders.show_and_process_order', compact('technicalOrder', 'materials', 'warehouse'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TechnicalOrder $technicalOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TechnicalOrder $technicalOrder)
    {
        // Validar la solicitud
        $request->validate([
            'assigned_user_id' => 'required|exists:users,id', // Asegurar que el usuario exista
        ]);

        // Actualizar la orden
        $technicalOrder->update([
            'user_assigned' => $request->input('assigned_user_id'),
            'status' => 'Asignada', // Cambiar el estado a "Asignada"
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('technicals_orders.index')
            ->with('success', 'Orden asignada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TechnicalOrder $technicalOrder)
    {
        //
    }
}
