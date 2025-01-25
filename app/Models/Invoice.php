<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'user_id',
        'billed_period',
        'billed_period_short',
        'billed_month_name',
        'billed_year_month',
        'issue_date',
        'due_date',
        'suspension_date',
        'pending_invoice_amount',
        'tax',
        'total',
        'status',
    ];



    //Relación con usuarios
    public function user(){
        return $this->belongsTo(User::class);
    }

    //Relación con contrato
    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    //Relación con pagos
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    //Relación con Items de factura
    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function generateInvoices()
    {
        $branch_id = session('branch_id');

        $contracts = Contract::whereHas('client', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })->with(['plan.services', 'additionalCharges'])->get();

        foreach ($contracts as $contract) {
            $totalInvoice = 0;

            // Crear la factura base
            $invoice = Invoice::create([
                'contract_id' => $contract->id,
                'user_id' => Auth::id(),
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'total' => 0, // Se calcula al final
                'status' => 'Generada',
            ]);

            // Crear los ítems de la factura
            foreach ($contract->plan->services as $service) {
                $unitPrice = $service->base_price;
                $taxPercentage = $service->tax_percentage;
                $quantity = 1; // Por defecto, un mes

                $totalItem = $unitPrice * (1 + $taxPercentage / 100);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $service->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'percentage_tax' => $taxPercentage,
                    'total' => $totalItem,
                ]);

                $totalInvoice += $totalItem;
            }

            // Agregar cargos adicionales
            foreach ($contract->additionalCharges as $charge) {
                $totalInvoice += $charge->amount; // Suponiendo que tienes un campo `amount` en cargos adicionales
            }

            // Actualizar el total de la factura
            $invoice->update(['total' => $totalInvoice]);
        }
    }
    public function getPendingAmount()
    {
        return $this->total - $this->payments()
                ->where('status', 'completed')
                ->sum('amount');
    }

    public function canReceivePayment()
    {
        return $this->getPendingAmount() > 0;
    }
}
