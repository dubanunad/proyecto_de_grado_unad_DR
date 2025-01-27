<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialMovement extends Model
{
    use HasFactory;

    protected $fillable = [
      'warehouse_origin_id',
      'warehouse_destination_id',
      'material_id',
      'quantity',
      'unit_of_measurement',
        'type',
        'serial_number',
        'user_id',
        'reason'
    ];

    public function material() {
        return $this->belongsTo(Material::class);
    }

    public function warehouseOrigin() {
        return $this->belongsTo(Warehouse::class, 'warehouse_origin_id');
    }

    public function warehouseDestination() {
        return $this->belongsTo(Warehouse::class, 'warehouse_destination_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
