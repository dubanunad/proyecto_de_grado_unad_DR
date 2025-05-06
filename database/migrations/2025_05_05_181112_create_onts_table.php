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
        Schema::create('onts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('olt_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('slot')->nullable();
            $table->string('port')->nullable();
            $table->string('onu_id')->nullable();
            $table->string('service_port')->nullable();
            $table->string('sn')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->nullable();
            $table->string('rx_power')->nullable();
            $table->string('model')->nullable();
            $table->string('vlan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onts');
    }
};
