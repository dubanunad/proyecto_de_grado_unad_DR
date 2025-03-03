<?php

namespace App\Http\Controllers;

use App\Models\AditionalCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdditionalChargeController extends Controller
{
    //Proteger rutas
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:additionalCharges.index')->only('index');
        $this->middleware('check.permission:additionalCharges.create')->only('create', 'store');
        $this->middleware('check.permission:additionalCharges.edit')->only('edit', 'update');
        $this->middleware('check.permission:additionalCharges.destroy')->only('destroy');
    }
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'user_id' => Auth::user()->id,
        ]);
        $contract = $request->contract_id;
        // Validación de los datos recibidos
        $validatedData = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id', // Debe existir en la tabla contracts
            'user_id' => 'nullable|exists:users,id', // Debe existir en la tabla users
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:pendiente,Facturado,Anulado', // Opcional, valores permitidos
        ]);



        AditionalCharge::create($validatedData);

        return redirect()->route('contracts.show', $contract)
                    ->with('success', 'Se ha guardado correctamente el cargo adicional');
    }

    /**
     * Display the specified resource.
     */
    public function show(AditionalCharge $aditionalCharge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AditionalCharge $aditionalCharge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AditionalCharge $aditionalCharge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AditionalCharge $aditionalCharge)
    {
        //
    }
}
