<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cash_register_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('completed')->after('payment_method');
            $table->string('reference_number')->nullable()->after('status');
            $table->text('notes')->nullable()->after('reference_number');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['cash_register_id']);
            $table->dropColumn(['cash_register_id', 'status', 'reference_number', 'notes', 'created_by', 'deleted_at']);
        });
    }
};
