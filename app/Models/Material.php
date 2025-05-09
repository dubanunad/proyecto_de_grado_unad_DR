<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'is_equipment'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function technicalOrders(){
       return $this->hasMany(TechnicalOrder::class);
    }

}
