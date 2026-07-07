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

    // ─── Laporan Dashboard ────────────────────────────────────────────────────

    public function laporan(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        
        if (!$bulan || !$tahun) {
            $latest = DataAlat::orderBy('tanggal', 'desc')->first();
            $bulan  = $latest?->bulan ?? now()->format('F');
            $tahun  = $latest?->tahun ?? now()->year;
        }

        $id_aset = $request->get('id_aset');
        $group_aset = $request->get('group_aset');
        $area = $request->get('area');
        $group_internal_order = $request->get('group_internal_order');

        // Extract 4-character prefix for exact asset code matching
        $assetPrefix = !empty($id_aset) ? substr($id_aset, 0, 4) : null;

        // 1. Build Telemetry Query
        $telemetryQuery = DataAlat::query();
        if (!empty($bulan) && $bulan !== 'ALL') {
            $telemetryQuery->where('bulan', $bulan);
        }
        if (!empty($tahun) && $tahun !== 'ALL') {
            $telemetryQuery->where('tahun', $tahun);
        }
        if (!empty($assetPrefix)) {
            $telemetryQuery->where('id_aset', 'like', $assetPrefix . '%');
        }
        if (!empty($group_aset)) {
            $telemetryQuery->where('group_aset', $group_aset);
        }
        if (!empty($area)) {
            $telemetryQuery->where('area', $area);
        }
        if (!empty($group_internal_order)) {
            $telemetryQuery->where('group_internal_order', $group_internal_order);
        }

        // Group by 4-char prefix of id_aset
        $telemetryRaw = $telemetryQuery->select(
            DB::raw('SUBSTRING(id_aset, 1, 4) as asset_code'),
            'model', 'group_aset', 'area', 'group_internal_order',
            DB::raw('SUM(waktu_kerja) as total_kerja'),
            DB::raw('SUM(waktu_operasi) as total_operasi'),
            DB::raw('SUM(waktu_idle) as total_idle'),
            DB::raw('AVG(persen_idle) as avg_idle'),
            DB::raw('SUM(total_bahan_bakar) as telemetry_fuel')
        )->groupBy(DB::raw('SUBSTRING(id_aset, 1, 4)'), 'model', 'group_aset', 'area', 'group_internal_order')
         ->get();

        // 2. Build Actual Fuel Query from transactions
        $fuelQuery = \App\Models\FuelTransaction::query();
        if (!empty($bulan) && $bulan !== 'ALL') {
            $shortBulan = substr($bulan, 0, 3);
            $fuelQuery->where('bulan', $shortBulan);
        }
        if (!empty($tahun) && $tahun !== 'ALL') {
            $fuelQuery->where('tahun', $tahun);
        }
        if (!empty($assetPrefix)) {
            $fuelQuery->where('unit_code', 'like', $assetPrefix . '%');
        }
        if (!empty($group_aset)) {
            $fuelQuery->where('group_aset', $group_aset);
        }
        if (!empty($area)) {
            $fuelQuery->where('area', $area);
        }
        if (!empty($group_internal_order)) {
            $fuelQuery->where('internal_order', 'like', '%' . $group_internal_order . '%');
        }

        // Group by 4-char prefix of unit_code
        $fuelRaw = $fuelQuery->select(
            DB::raw('SUBSTRING(unit_code, 1, 4) as asset_code'),
            'group_aset', 'area',
            DB::raw('SUM(total_quantity) as actual_fuel')
        )->groupBy(DB::raw('SUBSTRING(unit_code, 1, 4)'), 'group_aset', 'area')
         ->get();

        // 3. Merge Telemetry & Fuel Data (Full Outer Join in-memory)
        $telemetryMap = [];
        foreach ($telemetryRaw as $t) {
            $telemetryMap[$t->asset_code] = $t;
        }

        $fuelMap = [];
        foreach ($fuelRaw as $f) {
            $fuelMap[$f->asset_code] = $f;
        }

        $allAssetCodes = array_unique(array_merge(array_keys($telemetryMap), array_keys($fuelMap)));
        sort($allAssetCodes);

        $reports = collect();
        foreach ($allAssetCodes as $code) {
            $t = $telemetryMap[$code] ?? null;
            $f = $fuelMap[$code] ?? null;

            $reports->push((object)[
                'id_aset' => $t?->id_aset ?? $code, // Use the full telemetry ID if available, else prefix code
                'model' => $t?->model ?? '-',
                'group_aset' => $t?->group_aset ?? $f?->group_aset ?? '-',
                'area' => $t?->area ?? $f?->area ?? '-',
                'group_internal_order' => $t?->group_internal_order ?? '-',
                'total_kerja' => $t?->total_kerja ?? 0,
                'total_operasi' => $t?->total_operasi ?? 0,
                'total_idle' => $t?->total_idle ?? 0,
                'avg_idle' => $t?->avg_idle ?? 0,
                'telemetry_fuel' => $t?->telemetry_fuel ?? 0,
                'actual_fuel' => $f?->actual_fuel ?? 0,
            ]);
        }

        // 4. Calculate Stats Card
        $stats = (object)[
            'total_aset' => $reports->count(),
            'total_kerja' => $reports->sum('total_kerja'),
            'total_operasi' => $reports->sum('total_operasi'),
            'total_idle' => $reports->sum('total_idle'),
            'avg_idle' => $reports->count() > 0 ? $reports->avg('avg_idle') : 0,
            'telemetry_fuel' => $reports->sum('telemetry_fuel'),
            'actual_fuel' => $reports->sum('actual_fuel'),
        ];

        // 5. Dropdown lists from all data (unfiltered for options)
        $telemetryUnits = DataAlat::select('id_aset')->distinct()->pluck('id_aset')->toArray();
        $fuelUnits = \App\Models\FuelTransaction::select('unit_code')->distinct()->pluck('unit_code')->toArray();
        $filterUnits = array_unique(array_merge($telemetryUnits, $fuelUnits));
        sort($filterUnits);

        $telemetryGroups = DataAlat::select('group_aset')->whereNotNull('group_aset')->distinct()->pluck('group_aset')->toArray();
        $fuelGroups = \App\Models\FuelTransaction::select('group_aset')->whereNotNull('group_aset')->distinct()->pluck('group_aset')->toArray();
        $filterGroups = array_unique(array_merge($telemetryGroups, $fuelGroups));
        sort($filterGroups);

        $telemetryAreas = DataAlat::select('area')->whereNotNull('area')->distinct()->pluck('area')->toArray();
        $fuelAreas = \App\Models\FuelTransaction::select('area')->whereNotNull('area')->distinct()->pluck('area')->toArray();
        $filterAreas = array_unique(array_merge($telemetryAreas, $fuelAreas));
        sort($filterAreas);

        $filterIoGroups = DataAlat::select('group_internal_order')
            ->whereNotNull('group_internal_order')
            ->distinct()
            ->orderBy('group_internal_order')
            ->pluck('group_internal_order');

        return view('monitoring.laporan', compact(
            'reports', 'stats', 'bulan', 'tahun',
            'id_aset', 'group_aset', 'area', 'group_internal_order',
            'filterUnits', 'filterGroups', 'filterAreas', 'filterIoGroups'
        ));
    }
}