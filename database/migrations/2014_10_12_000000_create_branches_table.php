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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 20);
            $table->string('name', 40)->unique();
            $table->string('country', 60);
            $table->string('department', 60);
            $table->string('municipality', 60);
            $table->string('address', 255);
            $table->string('number_phone', 20);
            $table->string('additional_number', 20)->nullable();
            $table->string('image')->nullable();
            $table->decimal('moving_price')->nullable();
            $table->decimal('reconnection_price')->nullable();
            $table->text('message_custom_invoice')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
