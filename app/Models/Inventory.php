<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'quantity',
        'unit_of_measurement',
    ];

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function material() {
        return $this->belongsTo(Material::class);
    }
}
