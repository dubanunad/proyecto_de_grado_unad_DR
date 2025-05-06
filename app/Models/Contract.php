<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'client_id',
        'plan_id',
        'neighborhood',
        'address',
        'home_type',
        'nap_port',
        'cpe_sn',
        'user_pppoe',
        'password_pppoe',
        'status',
        'social_stratum',
        'permanence_clause',
        'ssid_wifi',
        'password_wifi',
        'comment',
        'activation_date',
        'overdue_invoices_count', //Me cuenta las facturas vencidas
        'user_id',
        'municipality',
        'department'
    ];



    /**
     * Relación con la tabla Clients (Clientes)
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con la tabla Plans
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relación con la tabla Users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación con cargos adicionales

    public function additionalCharges()
    {
        return $this->hasMany(AditionalCharge::class);
    }

    //Relación con la tabla sucursal
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    //Relacion con ont
    public function ont()
    {
        return $this->hasOne(Ont::class);
    }


}
