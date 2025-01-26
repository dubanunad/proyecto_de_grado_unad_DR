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
        Schema::create('material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_origin_id')->nullable()->constrained('warehouses')->onDelete('SET NULL');
            $table->foreignId('warehouse_destination_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('unit_of_measurement');
            $table->enum('type', ['Entrada', 'Salida', 'Transferencia']);
            $table->string('serial_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_movements');
    }
};
