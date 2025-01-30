<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\TechnicalOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TechnicalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
    public function store(Request $request, TechnicalOrder $technicalOrder)
    {
        try {
            // Obtén el ID de la sucursal y el usuario autenticado
            $branchId = session('branch_id');
            $createdBy = Auth()->id();

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
            $technicalOrder->create([
                'contract_id' => $request->contract_id,
                'branch_id' => $branchId,
                'created_by' => $createdBy,
                'type' => $request->order_type,
                'detail' => $request->order_detail,
                'initial_comment' => $request->initial_comment,
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

            // Redirige a la ruta contracts.show con el ID del contrato y un mensaje de error
            return redirect()->route('contracts.show', $request->contract_id)
                ->with('error', 'Hubo un error al crear la orden técnica: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TechnicalOrder $technicalOrder)
    {
        //
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
