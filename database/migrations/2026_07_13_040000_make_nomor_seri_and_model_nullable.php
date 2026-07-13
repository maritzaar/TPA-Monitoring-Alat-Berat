<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_alat', function (Blueprint $table) {
            $table->string('nomor_seri', 50)->nullable()->change();
            $table->string('model', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_alat', function (Blueprint $table) {
            $table->string('nomor_seri', 50)->nullable(false)->change();
            $table->string('model', 50)->nullable(false)->change();
        });
    }
};
