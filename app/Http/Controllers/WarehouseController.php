<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::where('branch_id', session('branch_id'))
            ->simplePaginate(8);
        //
        return view('gestisp.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branchId = session('branch_id');

        // Obtener los usuarios asociados a la sucursal
        $users = User::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->get();
        return view('gestisp.warehouses.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Warehouse $warehouse)
    {
        $branchId = session('branch_id');

        $warehouse->create([
            'description' => $request->description,
            'user_id' => $request->user_id,
            'branch_id' => $branchId,
        ]);

        return redirect()->action([WarehouseController::class, 'index'])
            ->with('success-create', 'El almacén se ha creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $branchId = session('branch_id');

        // Obtener los usuarios asociados a la sucursal
        $users = User::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->get();
        return view('gestisp.warehouses.edit', compact('warehouse', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $warehouse->update([
            'description' => $request->description,
            'user_id' => $request->user_id,
        ]);

        return redirect()->action([WarehouseController::class, 'index'])
            ->with('success-update', 'El almacén se ha modificado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->action([WarehouseController::class, 'index'])
            ->with('success-delete', 'El almacén se ha eliminado correctamente');
    }
}
