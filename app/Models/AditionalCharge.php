<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AditionalCharge extends Model
{
    use HasFactory;

    protected $fillable = [
      'contract_id',
      'user_id',
      'description',
      'amount',
      'status'
    ];

    //Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación con contrato
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
