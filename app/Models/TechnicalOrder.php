<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalOrder extends Model
{
    use HasFactory;

    // Campos asignables masivamente
    protected $fillable = [
        'contract_id',
        'branch_id',
        'user_assigned',
        'type',
        'status',
        'rejection_reason',
        'detail',
        'observations_technical',
        'client_observation',
        'solution',
        'initial_comment',
        'created_by'
    ];

    // Relaciones
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_assigned');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials()
    {
        return $this->hasMany(TechnicalOrderMaterial::class, 'technical_order_id');
    }

    public function verifications()
    {
        return $this->hasMany(TechnicalOrderVerification::class, 'technical_order_id');
    }
}
