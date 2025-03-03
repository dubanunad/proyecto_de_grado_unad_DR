<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:warehouses.index')->only('index');
        $this->middleware('check.permission:warehouses.create')->only('create', 'store');
        $this->middleware('check.permission:warehouses.edit')->only('edit', 'update');
        $this->middleware('check.permission:warehouses.destroy')->only('destroy');
        $this->middleware('check.permission:warehouses.pdf')->only('generatePdf');
    }
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

    //Mostrar los inventarios

    public function show(Warehouse $warehouse)
    {
        $inventories = Inventory::where('warehouse_id', $warehouse->id)
            ->with('material')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                $material = $items->first()->material;
                $quantity = $items->sum('quantity');
                $unit = $items->first()->unit_of_measurement;
                $sns = $items->pluck('serial_number')->filter()->toArray();

                return [
                    'material' => $material,
                    'quantity' => $quantity,
                    'unit_of_measurement' => $unit,
                    'sns' => $sns
                ];
            });

        // Paginación manual
        $page = request()->get('page', 1);
        $perPage = 12;
        $paginatedData = new LengthAwarePaginator(
            $inventories->slice(($page - 1) * $perPage, $perPage)->values(),
            $inventories->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('gestisp.warehouses.show', [
            'inventoriesData' => $paginatedData,
            'warehouse' => $warehouse
        ]);
    }
    //Exportar inventario en PDF
    public function generatePdf(Warehouse $warehouse)
    {
        $inventories = Inventory::where('warehouse_id', $warehouse->id)
            ->with('material')
            ->get()
            ->groupBy('material_id')
            ->map(function ($items) {
                $material = $items->first()->material;
                $quantity = $items->sum('quantity');
                $unit = $items->first()->unit_of_measurement;
                $sns = $items->pluck('serial_number')->filter()->toArray(); // Lista de SN

                return [
                    'material' => $material->name, // Nombre del material
                    'quantity' => $quantity, // Cantidad total
                    'unit_of_measurement' => $unit, // Unidad de medida
                    'sns' => implode(', ', $sns) // Convertir SNs en una lista separada por comas
                ];
            });

        $data = [
            'inventoriesData' => $inventories,
            'warehouse' => $warehouse
        ];

        $pdf = Pdf::loadView('gestisp.warehouses.pdf', $data);

        return $pdf->download('Inventario_'.$warehouse->description.'.pdf');
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
