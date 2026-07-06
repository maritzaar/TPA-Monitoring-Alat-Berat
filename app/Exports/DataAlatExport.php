<?php

namespace App\Exports;

use App\Models\DataAlat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DataAlatExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function query()
    {
        return DataAlat::query()
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->orderBy('tanggal', 'asc');
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Bulan',
            'Tanggal',
            'Keterangan',
            'ID Aset',
            'Nomor Seri',
            'Buatan',
            'Model',
            'Group Aset',
            'Area',
            'PT',
            'Internal Order',
            'Group Internal Order',
            'Group Desc',
            'Meteran Jam (HM)',
            'Waktu Terakhir Dilaporkan',
            'Laporan Pemanfaatan Terakhir',
            'Zona Waktu',
            'Nama Zona',
            'Waktu Operasi (Jam)',
            'Waktu Idle (Jam)',
            'Waktu Kerja (Jam)',
            '% Idle',
            'Total Bahan Bakar (L)',
            'Laju Bakar (L/Jam)',
            'Daya Dihasilkan (kWh)',
            'Beban Harian Rata-rata',
            'Daya per Unit Bahan Bakar (kWh/L)',
            'Sumber Data'
        ];
    }

    public function map($row): array
    {
        return [
            $row->tahun,
            $row->bulan,
            $row->tanggal ? $row->tanggal->format('Y-m-d') : '',
            $row->keterangan,
            $row->id_aset,
            $row->nomor_seri,
            $row->buatan,
            $row->model,
            $row->group_aset,
            $row->area,
            $row->pt,
            $row->internal_order,
            $row->group_internal_order,
            $row->group_desc,
            $row->meteran_jam,
            $row->waktu_terakhir ? $row->waktu_terakhir->format('Y-m-d H:i:s') : '',
            $row->laporan_pemanfaatan ? $row->laporan_pemanfaatan->format('Y-m-d H:i:s') : '',
            $row->zona_waktu,
            $row->nama_zona,
            $row->waktu_operasi,
            $row->waktu_idle,
            $row->waktu_kerja,
            $row->persen_idle,
            $row->total_bahan_bakar,
            $row->laju_bakar,
            $row->daya_dihasilkan,
            $row->beban_harian,
            $row->daya_per_unit,
            $row->sumber_data
        ];
    }
}
