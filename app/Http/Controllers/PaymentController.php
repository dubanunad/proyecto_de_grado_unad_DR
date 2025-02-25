<?php

namespace App\Http\Controllers;

use App\Exports\PaymentsExport;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TechnicalOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:payments.index')->only('index');
        $this->middleware('can:payments.create')->only('create', 'store');
        $this->middleware('can:payments.edit')->only('edit', 'update');
        $this->middleware('can:payments.destroy')->only('destroy');
        $this->middleware('can:payments.search')->only('search');
        $this->middleware('can:payments.searchView')->only('searchView');
        $this->middleware('can:payments.export')->only('exportPaymentsPDF');
        $this->middleware('can:payments.export-excel')->only('export');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::query();

        // Filtrar por la sucursal actual si está configurada en la sesión
        if (session()->has('branch_id')) {
            $branchId = session('branch_id');

            $query->whereHas('invoice.contract.branch', function ($query) use ($branchId) {
                $query->where('id', $branchId);
            });
        }

        // Verificar si hay filtros adicionales para la búsqueda
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->filter_field;
            $value = $request->filter_value;

            // Mapear los campos de relaciones para su correcto uso
            $fieldMappings = [
                'client.identity_number' => 'clients.identity_number',
                'client.name' => 'clients.name',
                'client.last_name' => 'clients.last_name',
                'payments.payment_date' => 'payments.payment_date',
            ];

            if (array_key_exists($field, $fieldMappings)) {
                $mappedField = $fieldMappings[$field];

                // Aplicar los filtros, manejando relaciones
                if (str_contains($mappedField, 'clients')) {
                    $query->whereHas('invoice.contract.client', function ($query) use ($mappedField, $value) {
                        $query->where(str_replace('clients.', '', $mappedField), 'like', '%' . $value . '%');
                    });
                } elseif (str_contains($mappedField, 'payments')) {
                    $query->where($mappedField, 'like', '%' . $value . '%');
                }
            }
        }

        // Filtrar por rango de fechas
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        // Paginación flexible
        $perPage = $request->get('per_page', 12);
        $payments = $query->simplePaginate($perPage);

        return view('gestisp.payments.index', compact('payments'));
    }
    public function exportPaymentsPDF(Request $request)
    {
        // Construir la consulta como en el método index
        $query = Payment::query();

        // Filtrar por la sucursal actual si está configurada en la sesión
        if (session()->has('branch_id')) {
            $query->where('branch_id', session('branch_id'));
        }

        // Verificar si hay filtros adicionales para la búsqueda
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->filter_field;
            $value = $request->filter_value;

            // Mapear los campos de relaciones para su correcto uso
            $fieldMappings = [
                'client.identity_number' => 'clients.identity_number',
                'client.name' => 'clients.name',
                'client.last_name' => 'clients.last_name',
                'payments.payment_date' => 'payments.payment_date',
            ];

            if (array_key_exists($field, $fieldMappings)) {
                $mappedField = $fieldMappings[$field];

                // Aplicar los filtros, manejando relaciones
                if (str_contains($mappedField, 'clients')) {
                    $query->whereHas('invoice.contract.client', function ($query) use ($mappedField, $value) {
                        $query->where(str_replace('clients.', '', $mappedField), 'like', '%' . $value . '%');
                    });
                } elseif (str_contains($mappedField, 'payments')) {
                    $query->where($mappedField, 'like', '%' . $value . '%');
                }
            }
        }

        // Filtrar por rango de fechas
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        // Obtener los resultados de la consulta
        $payments = $query->get();

        // Cargar una vista y pasar los datos de pagos a la vista
        $pdf = PDF::loadView('gestisp.payments.report_pdf', compact('payments'));

        // Retornar el PDF generado al navegador
        return $pdf->download('Reporte de pagos.pdf');
    }
    public function export()
    {
        //Función para exportar los datos de los clientes a un excel
        return (new PaymentsExport)->download('listado_de_pagos.xlsx');
    }


    public function searchView()
    {
        return view('gestisp.payments.search');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function search(Request $request)
    {
        // Validación básica
        $request->validate([
            'search_term' => 'required|string'
        ]);

        $searchTerm = $request->input('search_term');

        // Inicializa la consulta base
        $query = Invoice::query();

        // Filtrar por la sucursal actual si está configurada en la sesión
        if (session()->has('branch_id')) {
            $query->whereHas('contract', function ($q) {
                $q->where('branch_id', session('branch_id'));
            });
        }

        // Verificar si hay un término de búsqueda
        if ($request->filled('search_term')) {
            $term = $request->search_term;

            $query->whereHas('contract.client', function ($q) use ($term) {
                $q->where('identity_number', 'like', '%' . $term . '%')
                    ->orWhere('id', 'like', '%' . $term . '%');
            });
        }

        // Filtrar por estatus de las facturas
        $query->whereNotIn('status', ['Pagada', 'Cargada a nueva factura']);
        // Paginación flexible
        $perPage = $request->get('per_page', 8);
        $invoices = $query->simplePaginate($perPage);

        return view('gestisp.payments.search', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string',
                'reference_number' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            $invoice = Invoice::findOrFail($validated['invoice_id']);
            $pendingAmount = $invoice->getPendingAmount();

            if ($validated['amount'] > $pendingAmount) {
                throw new \Exception('El monto del pago excede el saldo pendiente.');
            }

            $activeCashRegister = CashRegister::where('status', 'open')
                ->where('user_id', auth()->id())
                ->first();

            if (!$activeCashRegister && in_array($validated['payment_method'], ['cash', 'card'])) {
                throw new \Exception('No hay una caja abierta para recibir pagos.');
            }

            $payment = Payment::create([
                'invoice_id' => $validated['invoice_id'],
                'user_id' => auth()->id(),
                'cash_register_id' => $activeCashRegister->id ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'status' => 'completed'
            ]);

            // Actualizar el estado de la factura
            if ($validated['amount'] >= $pendingAmount) {
                $invoice->update(['status' => 'Pagada']);

                // Verificar y actualizar el contrato si estaba en suspensión o pre-suspensión
                $contract = $invoice->contract;
                if ($contract && in_array($contract->status, ['Pre-suspensión'])) {
                    $contract->update([
                        'status' => 'Activo',
                        'overdue_invoices_count' => 0,
                        'suspension_warning_date' => null,
                        'suspension_date' => null
                    ]);
                }elseif ($contract && in_array($contract->status, ['Suspendido'])){
                    // Crear una orden técnica de reconexión
                    TechnicalOrder::create([
                        'contract_id' => $contract->id,
                        'branch_id' => session('branch_id'),
                        'type' => 'Servicio',
                        'detail' => 'Reconexión',
                        'initial_comment' => 'Orden de reconexión automática por pago'
                    ]);

                    // Actualizar el contrato
                    $contract->update([
                        'status' => 'Por Reconexión',
                        'overdue_invoices_count' => 0,
                        'suspension_warning_date' => null,
                        'suspension_date' => null
                    ]);
                }
            } else {
                $invoice->update(['status' => 'Pendiente Parcial']);
            }

            if ($activeCashRegister) {
                CashRegisterTransaction::create([
                    'cash_register_id' => $activeCashRegister->id,
                    'payment_id' => $payment->id,
                    'transaction_type' => 'Ingreso',
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'description' => "Pago de factura #{$invoice->id}",
                    'created_by' => auth()->id()
                ]);

                $activeCashRegister->calculateTotals();
            }

            // Asegurarse de que la carpeta temp existe
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }

            // Generar el PDF
            $pdf = PDF::loadView('gestisp.payments.payment-receipt', [
                'payment' => $payment->load(['invoice.contract.client', 'user']),
                'company' => [
                    'name' => config('app.company_name', 'Nombre de la Empresa'),
                    'address' => config('app.company_address', 'Dirección de la Empresa'),
                    'phone' => config('app.company_phone', 'Teléfono de la Empresa'),
                    'email' => config('app.company_email', 'Email de la Empresa'),
                ]
            ]);

            // Guardar el PDF
            $pdfPath = 'temp/payment_' . $payment->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());

            DB::commit();

            // Cargar las relaciones necesarias
            $payment->load('invoice');

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'payment' => [
                    'id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'amount' => number_format($payment->amount, 2),
                    'payment_method' => $payment->payment_method,
                ],
                'new_balance' => number_format($invoice->getPendingAmount(), 2),
                'pdf_url' => asset('storage/' . $pdfPath)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error en procesamiento de pago: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function generatePaymentReceipt(Payment $payment)
    {
        $pdf = PDF::loadView('pdf.payment_receipt', compact('payment'));
        $pdfPath = storage_path('app/public/payment_receipts/' . $payment->id . '.pdf');
        $pdf->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
