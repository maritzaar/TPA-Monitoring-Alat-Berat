<?php

namespace App\Exports;

use App\Models\MasterAset;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EfficiencyExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $bulan = $this->filters['bulan'] ?? 'May';
        $tahun = $this->filters['tahun'] ?? 2026;

        $telemetrySub = DB::table('data_alat');
        if (! empty($bulan) && $bulan !== 'ALL') {
            $telemetrySub->where('bulan', $bulan);
        }
        if (! empty($tahun) && $tahun !== 'ALL') {
            $telemetrySub->where('tahun', $tahun);
        }
        $telemetrySub = $telemetrySub->select(
            'id_aset',
            DB::raw('SUM(waktu_kerja) as total_kerja'),
            DB::raw('SUM(waktu_operasi) as total_operasi'),
            DB::raw('SUM(waktu_idle) as total_idle')
        )
        ->groupBy('id_aset');

        $fuelSub = DB::table('fuel_transactions');
        if (! empty($bulan) && $bulan !== 'ALL') {
            $fuelSub->where(function ($q) use ($bulan) {
                $q->where('bulan', $bulan)->orWhere('bulan', substr($bulan, 0, 3));
            });
        }
        if (! empty($tahun) && $tahun !== 'ALL') {
            $fuelSub->where('tahun', $tahun);
        }
        $fuelSub = $fuelSub->select(
            'unit_code',
            DB::raw('SUM(total_quantity) as total_solar')
        )
        ->groupBy('unit_code');

        $query = MasterAset::query()
            ->select(
                'master_asets.unit_code as id_aset',
                'master_asets.group_aset',
                'master_asets.area',
                'master_asets.pt',
                'master_asets.internal_order',
                'master_asets.group_internal_order',
                'master_asets.group_desc',
                'telemetry.total_kerja',
                'telemetry.total_operasi',
                'telemetry.total_idle',
                'fuel.total_solar'
            )
            ->leftJoinSub($telemetrySub, 'telemetry', 'master_asets.unit_code', '=', 'telemetry.id_aset')
            ->leftJoinSub($fuelSub, 'fuel', 'master_asets.unit_code', '=', 'fuel.unit_code');

        $query->where(function ($q) {
            $q->whereNotNull('telemetry.total_kerja')
              ->orWhereNotNull('fuel.total_solar');
        });

        if (! empty($this->filters['id_aset']) && $this->filters['id_aset'] !== 'ALL') {
            $query->where('master_asets.unit_code', $this->filters['id_aset']);
        }
        if (! empty($this->filters['group_aset']) && $this->filters['group_aset'] !== 'ALL') {
            $query->where('master_asets.group_aset', $this->filters['group_aset']);
        }
        if (! empty($this->filters['area']) && $this->filters['area'] !== 'ALL') {
            $query->where('master_asets.area', $this->filters['area']);
        }
        if (! empty($this->filters['group_internal_order']) && $this->filters['group_internal_order'] !== 'ALL') {
            $query->where('master_asets.group_internal_order', $this->filters['group_internal_order']);
        }
        if (! empty($this->filters['internal_order']) && $this->filters['internal_order'] !== 'ALL') {
            $query->where('master_asets.internal_order', $this->filters['internal_order']);
        }
        if (! empty($this->filters['group_desc']) && $this->filters['group_desc'] !== 'ALL') {
            $query->where('master_asets.group_desc', $this->filters['group_desc']);
        }
        if (! empty($this->filters['pt']) && $this->filters['pt'] !== 'ALL') {
            $query->where('master_asets.pt', $this->filters['pt']);
        }

        return $query->get()->map(function ($row) use ($bulan, $tahun) {
            $row->bulan = $bulan === 'ALL' ? 'Semua' : $bulan;
            $row->tahun = $tahun === 'ALL' ? 'Semua' : $tahun;
            $row->total_kerja = (float) ($row->total_kerja ?? 0);
            $row->total_operasi = (float) ($row->total_operasi ?? 0);
            $row->total_idle = (float) ($row->total_idle ?? 0);
            $row->total_solar = (float) ($row->total_solar ?? 0);
            $row->avg_idle = $row->total_operasi > 0 ? ($row->total_idle / $row->total_operasi) * 100 : 0;
            $row->efficiency = $row->total_kerja > 0 ? ($row->total_solar / $row->total_kerja) : null;
            return $row;
        })->sortByDesc(function ($item) {
            return $item->efficiency ?? -1;
        })->values();
    }

    public function headings(): array
    {
        return [
            'Bulan',
            'Tahun',
            'Unit Code',
            'Group Aset',
            'Area',
            'PT',
            'Internal Order',
            'Group IO',
            'Group Desc',
            'Total Jam Kerja (Jam)',
            'Total Waktu Operasi (Jam)',
            'Total Waktu Idle (Jam)',
            'Rata-rata Idle (%)',
            'Total Solar (L)',
            'Efisiensi Solar (L/Jam)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->bulan,
            $row->tahun,
            $row->id_aset,
            $row->group_aset,
            $row->area,
            $row->pt,
            $row->internal_order,
            $row->group_internal_order,
            $row->group_desc,
            $row->total_kerja,
            $row->total_operasi,
            $row->total_idle,
            round($row->avg_idle, 2) . '%',
            $row->total_solar,
            is_null($row->efficiency) ? 'N/A' : round($row->efficiency, 2)
        ];
    }
}
