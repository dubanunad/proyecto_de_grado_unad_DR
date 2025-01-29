<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalOrderVerification extends Model
{
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'technical_order_id',
        'verified_by',
        'status',
        'comments',
    ];

    // Relaciones
    public function technicalOrder()
    {
        return $this->belongsTo(TechnicalOrder::class, 'technical_order_id');
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
