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
        Schema::create('technical_order_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technical_order_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->integer('quantity')->nullable(); // Cantidad de material
            $table->string('serial_number')->nullable(); // SN si aplica
            $table->timestamps(); // created_at
            // Claves foráneas
            $table->foreign('technical_order_id')->references('id')->on('technical_orders')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_order_materials');
    }
};
