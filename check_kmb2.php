<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = \App\Models\DataAlat::select('id_aset')->distinct()->get();
foreach($data as $d) {
    if(strpos(strtoupper($d->id_aset), 'KMB') !== false) {
        echo $d->id_aset . "\n";
    }
}
