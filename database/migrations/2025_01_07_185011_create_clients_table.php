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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('type_document');
            $table->string('identity_number', 20);
            $table->string('name', 40);
            $table->string('last_name', 40);
            $table->string('type_client', 40);
            $table->string('number_phone', 20);
            $table->string('aditional_phone', 20)->nullable();
            $table->string('email');
            $table->date('birthday');
            $table->foreignId('user_id')->default(1)->constrained()->onDelete('set default'); // Relación con la tabla users para guardar el usuario que creó el cliente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
