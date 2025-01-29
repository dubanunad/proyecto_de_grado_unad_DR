<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('technical_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_assigned')->nullable(); // Técnico asignado
            $table->unsignedBigInteger('created_by')->nullable(); // Usuario que crea la orden
            $table->enum('type', ['Servicio', 'Incidencia']); // Tipo de orden
            $table->enum('status', ['pending', 'assigned', 'rejected', 'pre_finalized', 'closed'])->default('pending');
            $table->text('rejection_reason')->nullable(); // Motivo del rechazo
            $table->text('detail'); // Detalles de la orden
            $table->text('observations_technical')->nullable(); // Observaciones del técnico
            $table->text('client_observation')->nullable(); // Observaciones del cliente
            $table->text('solution')->nullable(); // Solución brindada
            $table->timestamps(); // created_at y updated_at
            // Claves foráneas
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('user_assigned')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_orders');
    }
};
