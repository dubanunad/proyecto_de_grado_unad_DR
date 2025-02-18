<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CashRegisterController extends Controller
{
    //Proteger rutas
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:cashRegisters.index')->only('index');
        $this->middleware('can:cashRegisters.create')->only('create', 'store');
        $this->middleware('can:cashRegisters.edit')->only('edit', 'update');
        $this->middleware('can:cashRegisters.destroy')->only('destroy');
        $this->middleware('can:cashRegisters.status')->only('status');
        $this->middleware('can:cashRegisters.open')->only('status');
        $this->middleware('can:cashRegisters.close')->only('close');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('gestisp.cashRegisters.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CashRegister $cashRegister)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashRegister $cashRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashRegister $cashRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashRegister $cashRegister)
    {
        //
    }

    // Método para abrir una nueva caja
    public function open(Request $request)
    {
        $branchId = session('branch_id');
        // Validamos los datos de apertura
        $validated = $request->validate([
            'initial_amount' => 'required|numeric|min:0',  // Monto inicial no negativo
            'opening_notes' => 'nullable|string'           // Notas opcionales
        ]);

        // Verificamos que el usuario no tenga otra caja abierta
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if ($activeRegister) {
            return response()->json([
                'error' => 'Ya tienes una caja abierta'
            ], 422);
        }

        // Creamos el registro de la nueva caja
        $cashRegister = CashRegister::create([
            'branch_id' => $branchId,
            'user_id' => auth()->id(),
            'initial_amount' => $validated['initial_amount'],
            'opening_notes' => $validated['opening_notes'],
            'opened_at' => now(),
            'status' => 'open'
        ]);

        return response()->json([
            'message' => 'Caja abierta correctamente',
            'cash_register' => $cashRegister
        ]);
    }

    public function close(Request $request)
    {
        $branchId = session('branch_id');
        // Buscar la última caja abierta para el usuario autenticado
        $cashRegister = CashRegister::where('user_id', auth()->id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if (!$cashRegister) {
            return response()->json([
                'error' => 'No tienes ninguna caja abierta para cerrar'
            ], 422);
        }

        // Validar los datos de cierre
        $validated = $request->validate([
            'final_amount' => 'required|numeric|min:0',
            'closing_notes' => 'nullable|string'
        ]);

        // Actualizar el registro con los datos de cierre
        $cashRegister->update([
            'final_amount' => $validated['final_amount'],
            'closing_notes' => $validated['closing_notes'],
            'closed_at' => now(),
            'status' => 'closed'
        ]);

        // Calcular los totales finales
        $cashRegister->calculateTotals();

        // Generar el PDF
        $pdf = PDF::loadView('gestisp.cashRegisters.report', [
            'cashRegister' => $cashRegister->load(['transactions', 'user'])
        ]);

        // Guardar el PDF
        $pdfPath = 'cash_register_reports/cash_register_' . $cashRegister->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return response()->json([
            'message' => 'Caja cerrada correctamente',
            'cash_register' => $cashRegister->fresh(),
            'pdf_url' => asset('storage/' . $pdfPath)
        ]);
    }

    public function status()
    {
        $branchId = session('branch_id');
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if ($activeRegister) {
            return response()->json([
                'status' => 'open',
                'initial_amount' => $activeRegister->initial_amount,
                'expected_amount' => $activeRegister->expected_amount // Asegúrate de que este campo exista o cámbialo según la lógica
            ]);
        }

        return response()->json([
            'status' => 'closed'
        ]);
    }

    // Método para generar reportes
    public function report(Request $request)
    {
        $branchId = session('branch_id');
        // Validamos los filtros del reporte
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // Construimos la consulta base
        $query = CashRegister::with(['transactions', 'user', $branchId])
            ->whereBetween('opened_at', [
                $validated['start_date'],
                $validated['end_date'] . ' 23:59:59'
            ]);

        // Si se especifica un usuario, filtramos por él
        if (isset($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        $registers = $query->get();

        // Generamos el resumen
        $summary = [
            'total_income' => $registers->sum('total_income'),
            'total_expenses' => $registers->sum('total_expenses'),
            'total_difference' => $registers->sum('difference'),
            'register_count' => $registers->count(),
            // Agrupamos las transacciones por método de pago
            'by_payment_method' => CashRegisterTransaction::whereBetween('created_at', [
                $validated['start_date'],
                $validated['end_date'] . ' 23:59:59'
            ])
                ->selectRaw('payment_method, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get()
        ];

        return response()->json([
            'registers' => $registers,
            'summary' => $summary
        ]);
    }
}
