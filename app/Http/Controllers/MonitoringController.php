<?php

namespace App\Http\Controllers;

use App\Models\DataAlat;
use App\Models\FuelTransaction;
use App\Models\MasterAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    private function getFilters()
    {
        return [
            'filterUnits' => MasterAset::select('unit_code')->whereNotNull('unit_code')->distinct()->orderBy('unit_code')->pluck('unit_code'),
            'filterGroups' => MasterAset::select('group_aset')->whereNotNull('group_aset')->distinct()->orderBy('group_aset')->pluck('group_aset'),
            'filterAreas' => MasterAset::select('area')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area'),
            'filterIoGroups' => MasterAset::select('group_internal_order')->whereNotNull('group_internal_order')->distinct()->orderBy('group_internal_order')->pluck('group_internal_order'),
            'filterInternalOrders' => MasterAset::select('internal_order')->whereNotNull('internal_order')->distinct()->orderBy('internal_order')->pluck('internal_order'),
        ];
    }

    public function workingHour(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
            $bulan = $latestData ? $latestData->bulan : now()->format('F');
            $tahun = $latestData ? $latestData->tahun : now()->year;
        }

        $id_aset = $request->get('id_aset');
        $group_aset = $request->get('group_aset');
        $area = $request->get('area');
        $group_internal_order = $request->get('group_internal_order');
        $internal_order = $request->get('internal_order');

        $query = DataAlat::query();
        
        if (!empty($bulan) && $bulan !== 'ALL') $query->where('bulan', $bulan);
        if (!empty($tahun) && $tahun !== 'ALL') $query->where('tahun', $tahun);
        if (!empty($id_aset)) $query->where('id_aset', $id_aset);
        if (!empty($group_aset)) $query->where('group_aset', $group_aset);
        if (!empty($area)) $query->where('area', $area);
        if (!empty($group_internal_order)) $query->where('group_internal_order', $group_internal_order);
        if (!empty($internal_order)) $query->where('internal_order', $internal_order);

        $reports = $query->select(
                'id_aset', 'internal_order', 'model', 'group_aset', 'area', 'group_internal_order',
                DB::raw('SUM(waktu_kerja) as total_kerja'),
                DB::raw('SUM(waktu_operasi) as total_operasi'),
                DB::raw('SUM(waktu_idle) as total_idle'),
                DB::raw('AVG(persen_idle) as avg_idle')
            )
            ->groupBy('id_aset', 'internal_order', 'model', 'group_aset', 'area', 'group_internal_order')
            ->get();

        $stats = (object)[
            'total_aset' => $reports->count(),
            'total_kerja' => $reports->sum('total_kerja'),
            'total_operasi' => $reports->sum('total_operasi'),
            'total_idle' => $reports->sum('total_idle'),
            'avg_idle' => $reports->count() > 0 ? $reports->avg('avg_idle') : 0,
        ];

        $filters = $this->getFilters();

        return view('monitoring.working_hour', array_merge(compact(
            'reports', 'stats', 'bulan', 'tahun',
            'id_aset', 'group_aset', 'area', 'group_internal_order', 'internal_order'
        ), $filters));
    }

    public function workingHourDetail(Request $request, $idAset)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latestData = DataAlat::orderBy('tanggal', 'desc')->first();
            $bulan = $latestData ? $latestData->bulan : now()->format('F');
            $tahun = $latestData ? $latestData->tahun : now()->year;
        }

        $alat = DataAlat::where('id_aset', $idAset)->first() ?? DataAlat::where('id_aset', 'like', $idAset . '%')->first();

        $data = DataAlat::where(function($q) use ($idAset) {
                $q->where('id_aset', $idAset)
                  ->orWhere('id_aset', 'like', $idAset . '%');
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('monitoring.working_hour_detail', compact('alat', 'data', 'bulan', 'tahun', 'idAset'));
    }

    public function fuel(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        
        if (!$bulan || !$tahun) {
            $latest = FuelTransaction::orderBy('created_at', 'desc')->first();
            $bulan = $latest ? $latest->bulan : now()->format('F');
            $tahun = $latest ? $latest->tahun : now()->year;
        }

        $id_aset = $request->get('id_aset');
        $group_aset = $request->get('group_aset');
        $area = $request->get('area');
        $group_internal_order = $request->get('group_internal_order');
        $internal_order = $request->get('internal_order');

        $query = FuelTransaction::query();
        
        if (!empty($bulan) && $bulan !== 'ALL') {
            $query->where(function($q) use ($bulan) {
                $q->where('bulan', $bulan)->orWhere('bulan', substr($bulan, 0, 3));
            });
        }
        if (!empty($tahun) && $tahun !== 'ALL') $query->where('tahun', $tahun);
        if (!empty($id_aset)) $query->where('unit_code', $id_aset);
        if (!empty($group_aset)) $query->where('group_aset', $group_aset);
        if (!empty($area)) $query->where('area', $area);
        if (!empty($group_internal_order)) $query->where('internal_order', 'like', '%' . $group_internal_order . '%');
        if (!empty($internal_order)) $query->where('internal_order', $internal_order);

        $reports = $query->select(
                'unit_code as id_aset', 'internal_order', 'group_aset', 'area',
                DB::raw('SUM(total_quantity) as actual_fuel')
            )
            ->groupBy('unit_code', 'internal_order', 'group_aset', 'area')
            ->get();

        $stats = (object)[
            'total_aset' => $reports->count(),
            'actual_fuel' => $reports->sum('actual_fuel'),
        ];

        $filters = $this->getFilters();

        return view('monitoring.fuel', array_merge(compact(
            'reports', 'stats', 'bulan', 'tahun',
            'id_aset', 'group_aset', 'area', 'group_internal_order', 'internal_order'
        ), $filters));
    }

    public function fuelDetail(Request $request, $idAset)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if (!$bulan || !$tahun) {
            $latest = FuelTransaction::orderBy('created_at', 'desc')->first();
            $bulan = $latest ? $latest->bulan : now()->format('F');
            $tahun = $latest ? $latest->tahun : now()->year;
        }

        $alat = FuelTransaction::where('unit_code', $idAset)->first();
        
        $data = FuelTransaction::where('unit_code', $idAset)
            ->where(function($q) use ($bulan) {
                if ($bulan !== 'ALL') {
                    $q->where('bulan', $bulan)->orWhere('bulan', substr($bulan, 0, 3));
                }
            });
            
        if ($tahun !== 'ALL') {
            $data = $data->where('tahun', $tahun);
        }
            
        $data = $data->orderBy('created_at', 'asc')->get();

        return view('monitoring.fuel_detail', compact('alat', 'data', 'bulan', 'tahun', 'idAset'));
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
