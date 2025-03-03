<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:materials.index')->only('index');
        $this->middleware('check.permission:materials.create')->only('create', 'store');
        $this->middleware('check.permission:materials.edit')->only('edit', 'update');
        $this->middleware('check.permission:materials.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::simplePaginate(8);
        return view('gestisp.materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();

        return view('gestisp.materials.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Material $material)
    {
        $material->create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'is_equipment' => $request->is_equipment,
        ]);

        return redirect()->action([MaterialController::class, 'index'])
            ->with('success-create', 'Material creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $categories = Category::get();
        return view('gestisp.materials.edit', compact('material', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $material->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'is_equipment' => $request->is_equipment,
        ]);

        return redirect()->action([MaterialController::class, 'index'])
            ->with('success-update', 'Material editado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->action([MaterialController::class, 'index'])
            ->with('success-delete', 'Material eliminado correctamente');
    }
}
