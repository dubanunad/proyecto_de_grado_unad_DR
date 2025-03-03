<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:plans.index')->only('index');
        $this->middleware('check.permission:plans.create')->only('create', 'store');
        $this->middleware('check.permission:plans.edit')->only('edit', 'update');
        $this->middleware('check.permission:plans.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $plans = Plan::where('branch_id', session('branch_id'))
            ->simplePaginate(8);

        return view('gestisp.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //se llaman todos los servicios
        $services = Service::where('branch_id', session('branch_id'))->get();

        return view('gestisp.plans.create', compact('services'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate(['name' => 'required',]);

        $plan = Plan::create(['name' => $request->name, 'branch_id' => session('branch_id')]);

        $plan->services()->attach($request->services);

        return redirect()->action([PlanController::class, 'index'])
            ->with('success-create', 'Plan creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        //
        $services = Service::all();
        return view('gestisp.plans.edit', compact('plan', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'services' => 'array', // Opcional: Valida que sea un array
            'services.*' => 'exists:services,id', // Opcional: Valida que cada servicio exista en la tabla de servicios
        ]);

        // Actualiza el nombre del plan y ID de la sucursal
        $plan->update(['name' => $request->name, 'branch_id' => session('branch_id')]);

        // Sincroniza los servicios asociados al plan
        $plan->services()->sync($request->services ?? []);

        // Redirige con un mensaje de éxito
        return redirect()->action([PlanController::class, 'index'])
            ->with('success-update', 'Plan actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        //
        $plan->delete();
        // Redirige con un mensaje de éxito
        return redirect()->action([PlanController::class, 'index'])
            ->with('success-delete', 'Plan eliminado con éxito');

    }
}
