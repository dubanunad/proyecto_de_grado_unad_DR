<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\TechnicalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TechnicalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TechnicalOrder $technicalOrder)
    {
        //
    }
}
