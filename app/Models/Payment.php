<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id', 'user_id', 'cash_register_id', 'payment_date',
        'amount', 'payment_method', 'status', 'reference_number',
        'notes', 'created_by'
    ];

    protected $casts = [
        'payment_date' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            $payment->createAuditLog('created');
            $payment->updateInvoiceBalance();
        });

        static::updated(function ($payment) {
            $payment->createAuditLog('updated');
            $payment->updateInvoiceBalance();
        });

        static::deleted(function ($payment) {
            $payment->createAuditLog('deleted');
            $payment->updateInvoiceBalance();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function createAuditLog($action)
    {
        PaymentAudit::create([
            'payment_id' => $this->id,
            'action' => $action,
            'old_values' => $this->getOriginal(),
            'new_values' => $this->getAttributes(),
            'user_id' => auth()->id()
        ]);
    }

    public function updateInvoiceBalance()
    {
        if ($this->invoice) {
            $totalPaid = $this->invoice->payments()
                ->where('status', 'completed')
                ->sum('amount');

            $this->invoice->pending_invoice_amount = $this->invoice->total - $totalPaid;
            $this->invoice->save();
        }
    }
}
