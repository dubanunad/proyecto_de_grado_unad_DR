<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalOrderMaterial extends Model
{
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'technical_order_id',
        'material_id',
        'quantity',
        'serial_number',
    ];

    // Relaciones
    public function technicalOrder()
    {
        return $this->belongsTo(TechnicalOrder::class, 'technical_order_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
