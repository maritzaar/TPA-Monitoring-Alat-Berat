<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = \App\Models\DataAlat::select('id_aset', 'nomor_seri', 'model')->distinct()->take(20)->get();
foreach($data as $d) {
    echo "ID: {$d->id_aset}, Seri: {$d->nomor_seri}, Model: {$d->model}\n";
}
