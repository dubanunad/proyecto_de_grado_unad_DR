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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Relación con la tabla clientes
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null'); // Relación con la tabla clientes
            $table->string('neighborhood', 255);
            $table->string('address', 255);
            $table->string('home_type', 255);
            $table->string('nap_port')->nullable();
            $table->string('cpe_sn',20)->nullable()->unique();
            $table->string('user_pppoe')->nullable()->unique();
            $table->string('password_pppoe')->nullable();
            $table->string('status')->default('Por Instalar');
            $table->string('social_stratum');
            $table->integer('permanence_clause')->nullable();
            $table->string('ssid_wifi')->nullable();
            $table->string('password_wifi')->nullable();
            $table->string('comment')->nullable();
            $table->date('activation_date')->nullable();
            $table->unsignedInteger('overdue_invoices_count')->nullable();
            $table->foreignId('user_id')->default(1)->constrained()->onDelete('SET DEFAULT'); // Relación con la tabla users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
