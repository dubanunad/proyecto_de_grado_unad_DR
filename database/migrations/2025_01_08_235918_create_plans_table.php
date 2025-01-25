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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('SET NULL'); // Relación con la tabla users para guardar el usuario que creó el plan
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); // Relación con la tabla sucursales para guardar el usuario que creó el plan
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
