<?php

namespace App\Http\Controllers;

use App\Jobs\GeneratePendingInvoicesPdf;
use App\Models\AditionalCharge;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PdfReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS1DFacade;

class InvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:invoices.index')->only('index');
        $this->middleware('check.permission:invoices.create')->only('create', 'store');
        $this->middleware('check.permission:invoices.edit')->only('edit', 'update');
        $this->middleware('check.permission:invoices.show')->only('show');
        $this->middleware('check.permission:invoices.destroy')->only('destroy');
        $this->middleware('check.permission:invoices.generate')->only('generateInvoices');
        $this->middleware('check.permission:invoices.download-pdf')->only('downloadInvoicePdf');
        $this->middleware('check.permission:invoices.generate_max_pdf')->only('generatePendingInvoicesPdf');
        $this->middleware('check.permission:invoices.check-pdf-status')->only('checkPdfStatus');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $branchId = session('branch_id'); // ID de la sucursal en sesión

        $invoices = Invoice::join('contracts', 'invoices.contract_id', '=', 'contracts.id')
            ->join('clients', 'contracts.client_id', '=', 'clients.id')
            ->where('clients.branch_id', $branchId) // Filtra por sucursal
            ->where('contracts.branch_id', $branchId) // Filtra por sucursal
            ->select('invoices.*') // Solo selecciona columnas de la tabla invoices
            ->simplePaginate(10); // Paginación


        return view('gestisp.invoices.index', compact('invoices'));
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
    public function show(Invoice $invoice)
    {
        //
        //$items = InvoiceItem::where('invoice_id', $invoice)->get();

        // Usar el modelo Contract en lugar de DB::table
        $invoice->load(['contract.client', 'contract.plan.services', 'invoice_items']);

        $code = '123456789012'; // Número único de la factura.
        $barcode = DNS1DFacade::getBarcodeHTML($code, 'C128'); // Generar el código de barras.

        return view('gestisp.invoices.show', compact('invoice', 'barcode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function generateInvoices()
    {
        $amount_pending_invoice = 0;
        $branchId = session('branch_id');
        $today = now();

        // Formatear el mes en español
        $month_name = ucfirst($today->translatedFormat('F'));
        $year_month = $today->format('Y') . $today->format('m');

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Obtener contratos activos de la sucursal
        $contracts = Contract::with(['client', 'plan.services', 'additionalCharges'])
            ->whereIn('status', ['Activo', 'Pre-suspensión']) // Incluimos contratos en pre-suspensión
            ->whereHas('client', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get();

        if ($contracts->isEmpty()) {
            return redirect()->route('invoices.index')
                ->with('error', 'No hay contratos para generar facturas.');
        }

        foreach ($contracts as $contract) {
            // Contar facturas vencidas
            $overdueInvoices = Invoice::where('contract_id', $contract->id)
                ->where('status', 'Vencida')
                ->get();

            $overdueInvoicesCount = $overdueInvoices->count();

            // Si tiene 2 o más facturas vencidas, suspender el servicio
            if ($overdueInvoicesCount >= 2) {
                $contract->update([
                    'status' => 'Suspendido',
                    'suspension_date' => now(),
                    'overdue_invoices_count' => $overdueInvoicesCount
                ]);
                continue; // No generar nueva factura para contratos suspendidos
            }

            // Actualizar el contador en el contrato
            $contract->update([
                'overdue_invoices_count' => $overdueInvoicesCount,
            ]);

            // Validar si ya existe una factura para este contrato y período
            $existingInvoice = Invoice::where('contract_id', $contract->id)
                ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
                ->first();

            if ($existingInvoice) {
                continue;
            }

            $totalFactura = 0;
            $totalTax = 0;

            // Prorrateo si el contrato fue activado en el período actual
            $daysInMonth = $startOfMonth->diffInDays($endOfMonth) + 1;
            $prorateMultiplier = 1;

            $billedPeriod = $startOfMonth->format('d M') . ' al ' . $endOfMonth->format('d M Y');
            $billedPeriodShort = $startOfMonth->format('d') . ' al ' . $endOfMonth->format('d');

            if ($contract->activation_date > $startOfMonth) {
                $activationDate = now()->parse($contract->activation_date);
                $remainingDays = $activationDate->diffInDays($endOfMonth) + 1;
                $prorateMultiplier = $remainingDays / $daysInMonth;

                $billedPeriod = $activationDate->format('d M') . ' al ' . $endOfMonth->format('d M Y');
                $billedPeriodShort = $activationDate->format('d') . ' al ' . $endOfMonth->format('d');
            }

            // Determinar si hay riesgo de suspensión
            $hasOverdueInvoice = $overdueInvoicesCount > 0;
            $suspensionDate = $hasOverdueInvoice ? $today->copy()->addDays(24) : null;
            $status = $hasOverdueInvoice ? 'Pendiente con riesgo de corte' : 'Pendiente';

            $invoice = Invoice::create([
                'contract_id' => $contract->id,
                'user_id' => Auth::id(),
                'issue_date' => $today,
                'due_date' => $today->copy()->addDays(20),
                'billed_period' => $billedPeriod,
                'billed_period_short' => $billedPeriodShort,
                'billed_month_name' => $month_name,
                'billed_year_month' => $year_month,
                'suspension_date' => $suspensionDate,
                'tax' => 0,
                'total' => 0,
                'status' => $status,
                'service_suspension_warning' => $hasOverdueInvoice,
            ]);

            // Si hay factura vencida, actualizar el estado del contrato
            if ($hasOverdueInvoice) {
                $contract->update([
                    'status' => 'Pre-suspensión',
                    'suspension_warning_date' => $suspensionDate
                ]);
            }

            // Agregar servicios del plan
            foreach ($contract->plan->services as $service) {
                $basePrice = $service->base_price * $prorateMultiplier;
                $taxAmount = $service->tax_percentage > 0 ? $basePrice * ($service->tax_percentage / 100) : 0;
                $totalItem = $basePrice + $taxAmount;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $service->base_price,
                    'percentage_tax' => $service->tax_percentage,
                    'tax' => $taxAmount,
                    'total' => $totalItem,
                ]);

                $totalFactura += $totalItem;
                $totalTax += $taxAmount;
            }

            // Agregar cargos adicionales pendientes
            $pendingCharges = $contract->additionalCharges->where('status', 'pendiente');

            foreach ($pendingCharges as $charge) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $charge->description,
                    'quantity' => 1,
                    'unit_price' => $charge->amount,
                    'percentage_tax' => 0,
                    'tax' => 0,
                    'total' => $charge->amount,
                ]);

                $charge->update(['status' => 'Facturado']);
                $totalFactura += $charge->amount;
            }

            // Incluir facturas vencidas como ítems
            foreach ($overdueInvoices as $pendingInvoice) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Factura vencida #' . $pendingInvoice->id . ' - Período: ' . $pendingInvoice->billed_period,
                    'quantity' => 1,
                    'unit_price' => $pendingInvoice->total,
                    'percentage_tax' => 0,
                    'tax' => 0,
                    'total' => $pendingInvoice->total,
                ]);

                $totalFactura += $pendingInvoice->total;
                $amount_pending_invoice = $pendingInvoice->total;
                $pendingInvoice->update(['status' => 'Cargada a nueva factura']);
            }

            // Actualizar total y tax de la factura
            $invoice->update([
                'pending_invoice_amount' => $amount_pending_invoice,
                'total' => $totalFactura,
                'tax' => $totalTax,
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Facturas generadas correctamente.');
    }
    //Método para crear PDF

    public function downloadInvoicePdf($id)
    {
        // Buscar la factura con los datos relacionados
        $invoice = Invoice::with(['contract.client', 'invoice_items'])->findOrFail($id);


        $code = '0100' . $invoice->id . '000000' . $invoice->total; // Número único de la factura.
        $codeString = $code;

        // Generar la imagen como PNG
        $barcodeData = DNS1DFacade::getBarcodePNG($code, 'C128');

        // Guardar la imagen en un archivo temporal
        $barcodePath = 'barcodes/' . $code . '.png';
        Storage::disk('public')->put($barcodePath, base64_decode($barcodeData));

        // Pasar la ruta del archivo a la vista
        $barcodeUrl = asset('storage/' . $barcodePath);
        // Generar el PDF usando la vista
        $pdf = Pdf::loadView('gestisp.invoices.pdf', compact('invoice', 'barcodeUrl', 'codeString'));

        // Configurar tamaño media carta
        $pdf->setPaper([0, 0, 612.00, 419.53], 'portrait'); // Medidas en puntos (5.5" x 8.5")

        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        // Descargar el PDF
        return $pdf->download('factura_' . $invoice->id . '.pdf');
    }

    //PDF Masivo
    public function generatePendingInvoicesPdf()
    {
        $branchId = session('branch_id');

        // Dispatch the job
        GeneratePendingInvoicesPdf::dispatch($branchId);

        return redirect()->route('invoices.index')
            ->with('success', 'La generación del PDF de facturas pendientes ha sido encolada. No cierre ni recargue la página hasta ser notificado');
    }

    public function checkPdfStatus(Request $request)
    {
        // Obtener el ID de la sucursal desde la sesión
        $branchId = session('branch_id');

        // Buscar el último PDF generado para la sucursal
        $pdfReport = PdfReport::where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Devolver la ruta del PDF si está disponible
        if ($pdfReport) {
            return response()->json([
                'pdfPath' => asset("storage/{$pdfReport->pdf_path}"),
                'timestamp' => $pdfReport->created_at->timestamp,
            ]);
        }

        // Devolver un mensaje indicheck.permissiondo que el PDF aún no está listo
        return response()->json([
            'pdfPath' => null,
            'message' => 'El PDF aún no está listo.',
        ]);
    }
}
