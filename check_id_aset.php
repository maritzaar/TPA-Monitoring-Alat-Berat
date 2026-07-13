<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$latestImportId = \App\Models\DataAlat::max('import_log_id');
$data = \App\Models\DataAlat::where('import_log_id', $latestImportId)
    ->select('id_aset', 'nomor_seri', 'model')
    ->take(10)
    ->get();

foreach ($data as $d) {
    echo "ID: {$d->id_aset}, Seri: {$d->nomor_seri}, Model: {$d->model}\n";
}
