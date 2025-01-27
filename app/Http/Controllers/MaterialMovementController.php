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

        DB::transaction(function () use ($request) {
            foreach ($request->materials as $materialData) {
                $material = Material::findOrFail($materialData['material_id']);
                $quantity = $materialData['quantity'];
                $isEquipment = $material->is_equipment;

                // Validar cantidad en inventario para salidas y transferencias
                if (in_array($request->type, ['Salida', 'Transferencia'])) {
                    $inventory = Inventory::where('warehouse_id', $request->warehouse_origin_id)
                        ->where('material_id', $material->id)
                        ->first();

                    if (!$inventory || $inventory->quantity < $quantity) {
                        return back()->withErrors(['quantity' => 'Cantidad insuficiente en el almacén de origen.'])->withInput();
                    }
                }

                // Crear movimiento
                if ($isEquipment && isset($materialData['serial_numbers'])) {
                    foreach ($materialData['serial_numbers'] as $serialNumber) {
                        $movement = MaterialMovement::create([
                            'type' => $request->type,
                            'material_id' => $material->id,
                            'quantity' => 1, // Cada equipo es un movimiento individual
                            'unit_of_measurement' => $materialData['unit_of_measurement'],
                            'warehouse_origin_id' => $request->warehouse_origin_id,
                            'warehouse_destination_id' => $request->warehouse_destination_id,
                            'serial_number' => $serialNumber,
                            'user_id' => auth()->id(),
                            'reason' => $request->reason,
                        ]);

                        // Actualizar inventario
                        $this->updateInventory($request->type, $request->warehouse_origin_id, $request->warehouse_destination_id, $material->id, 1, $materialData['unit_of_measurement']);
                    }
                } else {
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

                    // Actualizar inventario
                    $this->updateInventory($request->type, $request->warehouse_origin_id, $request->warehouse_destination_id, $material->id, $quantity, $materialData['unit_of_measurement']);
                }
            }
        });

        return redirect()->route('movements.index')->with('success-create', 'Movimiento registrado exitosamente.');
    }

    /**
     * Actualiza el inventario según el tipo de movimiento.
     */
    protected function updateInventory($type, $warehouseOriginId, $warehouseDestinationId, $materialId, $quantity, $unitOfMeasurement)
    {
        if ($type === 'Entrada') {
            Inventory::updateOrCreate(
                ['warehouse_id' => $warehouseDestinationId, 'material_id' => $materialId],
                [
                    'quantity' => DB::raw("quantity + $quantity"),
                    'unit_of_measurement' => $unitOfMeasurement,
                ]
            );
        } elseif ($type === 'Salida') {
            $inventory = Inventory::where('warehouse_id', $warehouseOriginId)
                ->where('material_id', $materialId)
                ->first();

            if ($inventory) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $quantity,
                    'unit_of_measurement' => $unitOfMeasurement,
                ]);
            }
        } elseif ($type === 'Transferencia') {
            $inventory = Inventory::where('warehouse_id', $warehouseOriginId)
                ->where('material_id', $materialId)
                ->first();

            if ($inventory) {
                $inventory->update([
                    'quantity' => $inventory->quantity - $quantity,
                    'unit_of_measurement' => $unitOfMeasurement,
                ]);
            }

            Inventory::updateOrCreate(
                ['warehouse_id' => $warehouseDestinationId, 'material_id' => $materialId],
                [
                    'quantity' => DB::raw("quantity + $quantity"),
                    'unit_of_measurement' => $unitOfMeasurement,
                ]
            );
        }
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
