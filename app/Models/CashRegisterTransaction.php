<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegisterTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_register_id',
        'payment_id',
        'transaction_type',
        'amount',
        'payment_method',
        'description',
        'created_by',
    ];

    /**
     * The relationships to always load.
     *
     * @var array
     */
    protected $with = ['cashRegister', 'payment', 'user'];

    /**
     * Get the cash register associated with the transaction.
     */
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    /**
     * Get the payment associated with the transaction.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user who created the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
