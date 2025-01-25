<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'type_document',
        'identity_number',
        'name',
        'last_name',
        'type_client',
        'number_phone',
        'aditional_phone',
        'email',
        'birthday',
        'user_id',
    ];

    //Relación con la tabla sucursales
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    //Relación con usuarios
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación con contratos
    public function contracts(){
        return $this->hasMany(Contract::class);
    }

}
