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
        Schema::create('master_asets', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 50)->unique();
            $table->string('nomor_seri', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('group_aset', 50)->nullable();
            $table->string('area', 50)->nullable();
            $table->string('internal_order', 50)->nullable();
            $table->string('group_internal_order', 50)->nullable();
            $table->string('pt', 50)->nullable();
            $table->string('group_desc', 100)->nullable();
            $table->string('company_code', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_asets');
    }
};
