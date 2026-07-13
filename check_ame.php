<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = \App\Models\MasterAset::where('unit_code', 'like', '%AME%')->get();
foreach($data as $d) {
    echo $d->unit_code . ' - ' . $d->nomor_seri . "\n";
}
