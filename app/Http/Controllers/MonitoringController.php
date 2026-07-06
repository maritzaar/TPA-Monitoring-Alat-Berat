<?php

namespace App\Http\Controllers;

use App\Models\DataAlat;
use App\Models\MonitoringSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
   public function index(Request $request)
{
    $bulan = $request->get('bulan');
    $tahun = $request->get('tahun');

    if (!$bulan || !$tahun) {
        $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
        if ($latestData) {
            $bulan = $latestData->bulan;
            $tahun = $latestData->tahun;
        } else {
            $bulan = now()->format('F');
            $tahun = now()->year;
        }
    }

    // Statistik agregat - gunakan DataAlat langsung
    $stats = DataAlat::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->select(
            DB::raw('COUNT(DISTINCT id_aset) as total_aset'),
            DB::raw('SUM(waktu_kerja) as total_waktu_kerja'),
            DB::raw('SUM(waktu_operasi) as total_waktu_operasi'),
            DB::raw('SUM(waktu_idle) as total_waktu_idle'),
            DB::raw('AVG(persen_idle) as avg_idle'),
            DB::raw('SUM(total_bahan_bakar) as total_bahan_bakar')
        )
        ->first();

    // Data per aset dengan grouping
    $perAset = DataAlat::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->select(
            'id_aset',
            'model',
            'group_aset',
            'area',
            DB::raw('SUM(waktu_kerja) as total_kerja'),
            DB::raw('SUM(waktu_operasi) as total_operasi'),
            DB::raw('SUM(waktu_idle) as total_idle'),
            DB::raw('AVG(persen_idle) as avg_idle'),
            DB::raw('SUM(total_bahan_bakar) as total_bakar')
        )
        ->groupBy('id_aset', 'model', 'group_aset', 'area')
        ->orderBy('total_kerja', 'desc')
        ->get();

    return view('monitoring.index', compact('stats', 'perAset', 'bulan', 'tahun'));
}

    public function chart(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
            if ($latestData) {
                $bulan = $latestData->bulan;
                $tahun = $latestData->tahun;
            } else {
                $bulan = now()->format('F');
                $tahun = now()->year;
            }
        }

        $chartData = DataAlat::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->select(
                'tanggal',
                DB::raw('SUM(waktu_kerja) as total_kerja'),
                DB::raw('SUM(waktu_operasi) as total_operasi'),
                DB::raw('SUM(waktu_idle) as total_idle')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json($chartData);
    }

    public function detail(Request $request, $idAset)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
            if ($latestData) {
                $bulan = $latestData->bulan;
                $tahun = $latestData->tahun;
            } else {
                $bulan = now()->format('F');
                $tahun = now()->year;
            }
        }

        $alat = DataAlat::where('id_aset', $idAset)->first();

        $data = DataAlat::where('id_aset', $idAset)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('monitoring.detail', compact('alat', 'data', 'bulan', 'tahun'));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
            if ($latestData) {
                $bulan = $latestData->bulan;
                $tahun = $latestData->tahun;
            } else {
                $bulan = now()->format('F');
                $tahun = now()->year;
            }
        }

        $fileName = sprintf('laporan_monitoring_alat_%s_%s.xlsx', strtolower($bulan), $tahun);

        return Excel::download(new \App\Exports\DataAlatExport($bulan, $tahun), $fileName);
    }
}