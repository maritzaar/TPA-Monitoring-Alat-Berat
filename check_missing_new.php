<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$latestImportId = \App\Models\DataAlat::max('import_log_id');
echo "Latest Import ID: $latestImportId\n";

$missing = \App\Models\DataAlat::where('import_log_id', $latestImportId)
    ->whereNull('internal_order')
    ->select('id_aset', 'nomor_seri', 'model')
    ->distinct()
    ->get();

echo "Total Missing: " . $missing->count() . "\n";
foreach($missing as $d) {
    echo $d->id_aset . ' - ' . $d->nomor_seri . "\n";
}
