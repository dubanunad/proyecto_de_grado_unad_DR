<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identity_number',
        'name',
        'last_name',
        'number_phone',
        'address',
        'email',
        'password',
        'selected_branch_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //Relación con la tabla sucursales
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch'); // user_branch es la tabla pivote
    }

    //Relación con clientes

    public function clients()
    {
        return $this->hasMany(User::class);
    }

    //Relación con servicios
    public function services(){
        return $this->hasMany(Service::class);
    }

    //Relación con planes
    public function plans(){
        return $this->hasMany(Plan::class);
    }

    //Relación con contratos
    public function contracts(){
        return $this->hasMany(Contract::class);
    }

    //Relación con pagos
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    //Relación con cargos adicionales

    public function additionalCharges()
    {
        return $this->hasMany(Contract::class);
    }

    //Relacion con facturas
    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    //Relación con cajas
    public function cashRegisters(){
        return $this->hasMany(CashRegister::class);
    }

    //Relación con trasacciones de caja
    public function cashRegisterTransactions()
    {
        return $this->hasMany(CashRegisterTransaction::class);
    }

    //Relación con almacenes
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function materialMovements()
    {
        return $this->hasMany(MaterialMovement::class);
    }

    //Método para asignar sucursal a un usuario:

    public function assignBranch(string $branchName)
    {
        $branch = Branch::where('name', $branchName)->first();

        if ($branch) {
            $this->branches()->attach($branch);
        } else {
            throw new \Exception("Branch with name '{$branchName}' not found.");
        }
    }



}
