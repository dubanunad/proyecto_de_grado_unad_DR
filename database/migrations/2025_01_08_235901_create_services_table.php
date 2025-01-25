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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->decimal('base_price');
            $table->decimal('tax_percentage');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('SET NULL'); // Relación con la tabla users para guardar el usuario que creó el servicio
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); // Relación con la tabla users para guardar el usuario que creó el servicio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
