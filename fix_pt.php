<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$knownMaps = [
    '1100' => '1100-TBP',
    '1200' => '1200-SBA',
    '1300' => '1300-BP1',
    '1400' => '1400-API',
    '1500' => '1500-FMS',
    '1600' => '1600-TPS',
    '1610' => '1610-MJA',
    '1700' => '1700-DPP',
    '1800' => '1800-SAE',
    '1900' => '1900-TSA',
    '3100' => '3100-TPA',
    '3200' => '3200-TPE',
    '3300' => '3300-TLE',
];

$alatFixed = 0;
$masterFixed = 0;

foreach ($knownMaps as $code => $fullPt) {
    // Fix MasterAsets where company_code is the code
    $masterFixed += DB::table('master_asets')
        ->where('company_code', $code)
        ->update(['pt' => $fullPt]);

    // Fix MasterAsets where pt is just the code
    $masterFixed += DB::table('master_asets')
        ->where('pt', $code)
        ->update(['pt' => $fullPt]);

    // Fix DataAlat where pt is just the code
    $alatFixed += DB::table('data_alat')
        ->where('pt', $code)
        ->update(['pt' => $fullPt]);
}

// Clean up any empty/null/'-' pt in DataAlat using MasterAset's company code
$emptyAlats = DB::table('data_alat')
    ->whereNull('pt')
    ->orWhere('pt', '')
    ->orWhere('pt', '-')
    ->get();

foreach ($emptyAlats as $row) {
    $m = DB::table('master_asets')->where('unit_code', $row->id_aset)->first();
    if ($m && ! empty($m->company_code) && isset($knownMaps[$m->company_code])) {
        DB::table('data_alat')
            ->where('id', $row->id)
            ->update(['pt' => $knownMaps[$m->company_code]]);
        $alatFixed++;
    }
}

echo "Fixed DataAlat: $alatFixed, MasterAset: $masterFixed\n";
