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
        Schema::table('data_alat', function (Blueprint $table) {
            $table->unsignedBigInteger('import_log_id')->nullable()->after('sumber_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_alat', function (Blueprint $table) {
            $table->dropColumn('import_log_id');
        });
    }
};
