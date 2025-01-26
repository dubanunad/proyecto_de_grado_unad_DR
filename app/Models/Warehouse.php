<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function materials()
    {
        return $this->hasManyThrough(Material::class, Inventory::class);
    }
}
