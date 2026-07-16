<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MonitoringExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_fuel_export_pdf_redirects_if_data_too_large()
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Seed 1505 transactions
        $importLogId = DB::table('import_logs')->insertGetId([
            'filename' => 'test.xlsx',
            'sumber' => 'fuel',
            'rows_count' => 1505,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $records = [];
        for ($i = 0; $i < 1505; $i++) {
            $records[] = [
                'import_log_id' => $importLogId,
                'tahun' => 2026,
                'bulan' => 'May',
                'unit_code' => 'UNIT-' . $i,
                'total_quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        foreach (array_chunk($records, 500) as $chunk) {
            DB::table('fuel_transactions')->insert($chunk);
        }

        // Log in the user and run the request
        $response = $this->actingAs($user)
            ->get('/monitoring/export-pdf?type=fuel');

        // It should redirect to monitoring.fuel route
        $response->assertRedirect(route('monitoring.fuel'));
        $response->assertSessionHas('error');
        
        $errorMessage = session('error');
        $this->assertStringContainsString('Ukuran data terlalu besar untuk diekspor ke PDF', $errorMessage);
    }

    public function test_working_hour_page_loads_without_memory_exhaustion()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->get('/monitoring/working-hour');

        $response->assertStatus(200);
    }
}
