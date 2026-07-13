<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$latestImportId = \App\Models\DataAlat::max('import_log_id');
$data = \App\Models\DataAlat::where('import_log_id', $latestImportId)
    ->whereNull('internal_order')
    ->select('id_aset', 'nomor_seri', 'model')
    ->distinct()
    ->get();

echo "Count missing internal_order: " . $data->count() . "\n";
foreach ($data->take(10) as $d) {
    echo "ID: {$d->id_aset}, Seri: {$d->nomor_seri}, Model: {$d->model}\n";
    $master = \App\Models\MasterAset::where('unit_code', $d->id_aset)
        ->orWhere('nomor_seri', $d->nomor_seri)
        ->first();
    if ($master) {
        echo "  -> Found in Master: unit_code={$master->unit_code}, internal_order=" . ($master->internal_order ?? 'NULL') . "\n";
    } else {
        echo "  -> NOT FOUND IN MASTER\n";
    }
}
