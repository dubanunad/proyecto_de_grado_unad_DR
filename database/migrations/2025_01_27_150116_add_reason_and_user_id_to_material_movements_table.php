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
        Schema::table('material_movements', function (Blueprint $table) {
            $table->string('reason')->nullable()->after('type');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_movements', function (Blueprint $table) {
            $table->dropColumn('reason');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
