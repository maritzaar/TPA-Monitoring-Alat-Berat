<?php

namespace App\Http\Controllers;

use App\Models\DataAlat;
use App\Models\FuelTransaction;
use App\Models\MasterAset;

class HomeController extends Controller
{
    public function index()
    {
        // Get the latest month available in the working hours data
        $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
        $bulan = $latestData ? $latestData->bulan : now()->format('F');
        $tahun = $latestData ? $latestData->tahun : now()->year;

        // Total Assets Monitored
        $totalAset = MasterAset::count();

        // Avg % Idle overall
        $avgIdle = DataAlat::avg('persen_idle') ?? 0;

        // Total Fuel overall
        $totalFuel = FuelTransaction::sum('total_quantity') ?? 0;

        // Total Working Hours overall (Bonus: adding this since they want overall data)
        $totalKerja = DataAlat::sum('waktu_kerja') ?? 0;

        return view('home', compact('totalAset', 'avgIdle', 'totalFuel', 'totalKerja'));
    }
}
