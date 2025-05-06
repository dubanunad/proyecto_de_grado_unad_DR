<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ont extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'olt_id',
        'contract_id',
        'slot',
        'port',
        'onu_id',
        'service_port',
        'sn',
        'description',
        'status',
        'rx_power',
        'model',
        'vlan',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }


}
