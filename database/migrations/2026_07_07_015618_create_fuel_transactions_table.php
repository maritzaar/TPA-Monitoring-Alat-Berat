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
        Schema::create('fuel_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->onDelete('cascade');
            $table->integer('tahun');
            $table->string('bulan', 15);
            $table->string('company_code', 50)->nullable();
            $table->string('unit_code', 50);
            $table->string('internal_order', 50)->nullable();
            $table->string('material_number', 50)->nullable();
            $table->string('material_description', 150)->nullable();
            $table->decimal('total_quantity', 15, 3);
            $table->string('uom', 10)->nullable();
            $table->string('group_aset', 50)->nullable();
            $table->string('area', 50)->nullable();
            $table->string('code_company', 50)->nullable();
            $table->string('code_unit', 50)->nullable();
            $table->timestamps();

            $table->index(['tahun', 'bulan', 'unit_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_transactions');
    }
};
