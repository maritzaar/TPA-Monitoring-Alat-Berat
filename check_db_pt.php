<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- master_asets ---\n";
$masterPts = DB::table('master_asets')->select('pt', 'company_code')->distinct()->orderBy('company_code')->get();
foreach ($masterPts as $row) {
    echo "company_code: " . ($row->company_code ?? 'NULL') . " | pt: " . ($row->pt ?? 'NULL') . "\n";
}

echo "\n--- data_alat ---\n";
$dataPts = DB::table('data_alat')->select('pt')->distinct()->orderBy('pt')->get();
foreach ($dataPts as $row) {
    echo "pt: " . ($row->pt ?? 'NULL') . "\n";
}

echo "\n--- fuel_transactions ---\n";
$fuelPts = DB::table('fuel_transactions')->select('code_company', 'company_code')->distinct()->get();
foreach ($fuelPts as $row) {
    echo "company_code: " . ($row->company_code ?? 'NULL') . " | code_company: " . ($row->code_company ?? 'NULL') . "\n";
}
