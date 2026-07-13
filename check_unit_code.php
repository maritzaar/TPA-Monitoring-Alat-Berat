<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = \App\Models\MasterAset::whereNotNull('unit_code')->take(10)->get();
foreach($data as $d) {
    echo "Unit Code: " . $d->unit_code . " | Internal Order: " . $d->internal_order . "\n";
}
