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
        Schema::create('olts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('ip_address');
            $table->integer('ssh_port')->default(22);
            $table->integer('telnet_port')->default(23);
            $table->integer('snmp_port')->default(161);
            $table->string('read_snmp_comunity')->nullable();
            $table->string('write_snmp_comunity')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('brand')->default('huawei');
            $table->string('model')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('temperature')->nullable();
            $table->boolean('status')->default(true);
            $table->string('uptime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olts');
    }
};
