<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_alat', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan', 10);
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->string('id_aset', 50);
            $table->string('nomor_seri', 50);
            $table->string('buatan', 50)->default('CAT');
            $table->string('model', 50);
            $table->string('group_aset', 50)->nullable();
            $table->string('area', 50)->nullable();
            $table->string('pt', 50)->nullable();
            $table->string('internal_order', 50)->nullable();
            $table->string('group_internal_order', 50)->nullable();
            $table->string('group_desc', 100)->nullable();
            $table->decimal('meteran_jam', 15, 4)->nullable();
            $table->datetime('waktu_terakhir')->nullable();
            $table->datetime('laporan_pemanfaatan')->nullable();
            $table->string('zona_waktu', 10)->nullable();
            $table->string('nama_zona', 50)->nullable();
            $table->decimal('waktu_operasi', 10, 3)->nullable();
            $table->decimal('waktu_idle', 10, 3)->nullable();
            $table->decimal('waktu_kerja', 10, 3)->nullable();
            $table->decimal('persen_idle', 5, 2)->nullable();
            $table->decimal('total_bahan_bakar', 15, 3)->nullable();
            $table->decimal('laju_bakar', 10, 4)->nullable();
            $table->decimal('daya_dihasilkan', 10, 3)->nullable();
            $table->decimal('beban_harian', 5, 2)->nullable();
            $table->decimal('daya_per_unit', 10, 4)->nullable();
            $table->string('sumber_data', 20);
            $table->timestamps();

            $table->index(['id_aset', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_alat');
    }
};