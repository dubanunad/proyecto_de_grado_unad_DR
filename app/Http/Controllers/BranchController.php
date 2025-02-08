<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BranchController extends Controller
{
    //Proteger rutas
    public function __construct()
    {
        $this->middleware('can:branches.index')->only('index');
        $this->middleware('can:branches.create')->only('create', 'store');
        $this->middleware('can:branches.edit')->only('edit', 'update');
        $this->middleware('can:branches.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //listar las sucursales
        $branches = Branch::simplePaginate(8);

        //Retornar en la vista
        return view('gestisp.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('gestisp.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        //
        $validated = $request->validate([
            'nit' => 'required|string|max:20',
            'name' => 'required|string|max:40|unique:branches',
            'country' => 'required|string|max:60',
            'department' => 'required|string|max:60',
            'municipality' => 'required|string|max:60',
            'address' => 'required|string|max:255',
            'number_phone' => 'required|string|max:20',
            'additional_number' => 'nullable|string|max:20',
            'image' => 'nullable|image',
            'moving_price' => 'nullable|numeric',
            'reconnection_price' => 'nullable|numeric',
            'message_custom_invoice' => 'nullable|string',
            'observation' => 'nullable|string',
        ]);

        //Validar si hay un archivo en el request
        if($request->hasFile('image')){
            $validated['image'] = $request->file('image')->store('branches', 'public');
        }

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Sucursal creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
        return view('gestisp.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        //
        return view('gestisp.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        //
        $validated = $request->validate([
            'nit' => 'required|string|max:20',
            'name' => 'required|string|max:40|unique:branches,name,' . $branch->id,
            'country' => 'required|string|max:60',
            'department' => 'required|string|max:60',
            'municipality' => 'required|string|max:60',
            'address' => 'required|string|max:255',
            'number_phone' => 'required|string|max:20',
            'additional_number' => 'nullable|string|max:20',
            'image' => 'nullable|image',
            'moving_price' => 'nullable|numeric',
            'reconnection_price' => 'nullable|numeric',
            'message_custom_invoice' => 'nullable|string',
            'observation' => 'nullable|string',
        ]);

        //Si el usuario sube una nueva imagen
        if ($request->hasFile('image')){
            //Eliminar la imagen anterior
            File::delete(public_path('storage/' . $branch->image));
            //Asigna la nueva imagen
            $validated['image'] = $request->file('image')->store('branches', 'public');
        }

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        //
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Sucursal eliminada exitosamente.');
    }
}
