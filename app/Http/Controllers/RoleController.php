<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles = Role::simplePaginate(10);

        return view('gestisp.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $permmisions = Permission::all();

        return view('gestisp.roles.create', compact('permmisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate(['name' => 'required',]);

        $role = Role::create(['name' => $request->name]);

        $role->permissions()->sync($request->permissions);

        return redirect()->action([RoleController::class, 'index'])
            ->with('success-create', 'Rol creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
        //
        $permmisions = Permission::all();

        return view('gestisp.roles.edit', compact('permmisions', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //
        $request->validate(['name' => 'required',]);

        $role->update(['name' => $request->name]);

        $role->permissions()->sync($request->permissions);

        return redirect()->action([RoleController::class, 'index'])
            ->with('success-update', 'Rol modificado con éxito');



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
        $role->delete();

        return redirect()->action([RoleController::class, 'index'])
            ->with('success-delete', 'Rol eliminado con éxito');
    }
}
