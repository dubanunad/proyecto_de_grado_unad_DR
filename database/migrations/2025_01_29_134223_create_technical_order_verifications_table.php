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
        Schema::create('technical_order_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technical_order_id');
            $table->unsignedBigInteger('verified_by'); // Usuario que verifica
            $table->enum('status', ['Cerrada', 'Pendiente']); // Estado de la verificación
            $table->text('comments')->nullable(); // Comentarios si es devuelta
            $table->timestamps(); // created_at
            // Claves foráneas
            $table->foreign('technical_order_id')->references('id')->on('technical_orders')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_order_verifications');
    }
};
