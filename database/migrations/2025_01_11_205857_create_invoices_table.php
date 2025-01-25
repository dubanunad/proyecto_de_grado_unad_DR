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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('billed_period')->nullable();//Almacenar el periodo facturado, por ejemplo (del 20 al 30 de mes)
            $table->string('billed_period_short')->nullable();
            $table->string('billed_month_name')->nullable();
            $table->string('billed_year_month')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('suspension_date')->nullable();
            $table->decimal('pending_invoice_amount')->default(0);
            $table->decimal('tax')->default(0);
            $table->decimal('total');
            $table->string('status')->default('Generada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
