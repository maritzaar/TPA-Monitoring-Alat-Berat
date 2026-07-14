<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_summary', function (Blueprint $table) {
            $table->id();
            $table->string('id_aset', 50);
            $table->date('tanggal');
            $table->string('group_aset', 50)->nullable();
            $table->string('area', 50)->nullable();
            $table->decimal('total_waktu_kerja', 10, 2)->default(0);
            $table->decimal('total_waktu_operasi', 10, 2)->default(0);
            $table->decimal('total_waktu_idle', 10, 2)->default(0);
            $table->decimal('rata_idle', 5, 2)->default(0);
            $table->decimal('total_bahan_bakar', 15, 2)->default(0);
            $table->decimal('rata_bahan_bakar', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['id_aset', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_summary');
    }
};
