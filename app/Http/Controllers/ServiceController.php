<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $services = Service::where('branch_id', session('branch_id'))->simplePaginate(8);

        return view('gestisp.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('gestisp.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Service $service)
    {
        //

        $service->create([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'tax_percentage' => $request->tax_percentage,
            'user_id' => Auth::user()->id,
            'branch_id' => session('branch_id'),
        ]);

        return redirect()->action([ServiceController::class, 'index'])
            ->with('success-create', 'El servicio se ha creado correctamente');

    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
        return view('gestisp.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
        $service->update([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'tax_percentage' => $request->tax_percentage,
        ]);

        return redirect()->action([ServiceController::class, 'index'])
            ->with('success-update', 'El servicio se ha actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
        $service->delete();

        return redirect()->action([ServiceController::class, 'index'])
            ->with('success-update', 'El servicio se ha eliminado correctamente');
    }
}
