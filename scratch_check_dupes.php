<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FuelTransaction;
use Illuminate\Support\Facades\DB;

$unit = 'E031-LKE';

echo "=== DIAGNOSTICS FOR $unit ===\n";
$count = FuelTransaction::where('unit_code', $unit)->count();
$sumQty = FuelTransaction::where('unit_code', $unit)->sum('total_quantity');
$distinctMonths = FuelTransaction::where('unit_code', $unit)->select('bulan', 'tahun', DB::raw('count(*) as cnt'), DB::raw('sum(total_quantity) as sum_qty'))->groupBy('bulan', 'tahun')->get();

echo "Total records in DB: $count\n";
echo "Total sum of quantity: $sumQty L\n";
echo "By period:\n";
foreach ($distinctMonths as $dm) {
    echo "- {$dm->bulan} {$dm->tahun}: Count={$dm->cnt}, SumQty={$dm->sum_qty} L\n";
}

echo "\n=== SAMPLE RECORDS ===\n";
$samples = FuelTransaction::where('unit_code', $unit)->take(5)->get();
foreach ($samples as $s) {
    echo "ID: {$s->id} | Month: {$s->bulan} | Qty: {$s->total_quantity} | Company: {$s->company_code} | IO: {$s->internal_order}\n";
}
