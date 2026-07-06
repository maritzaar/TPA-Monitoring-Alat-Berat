<?php

namespace App\Http\Controllers;

use App\Imports\DataAlatImport;
use App\Models\DataAlat;
use App\Models\MonitoringSummary;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index()
    {
        $data = DataAlat::orderBy('tanggal', 'desc')->paginate(50);
        $history = \App\Models\ImportLog::orderBy('created_at', 'desc')->take(10)->get();
        return view('import.index', compact('data', 'history'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'sumber' => 'required|in:CATERPILLAR,INTERNAL,SAP'
        ]);

        try {
            $filename = $request->file('file')->getClientOriginalName();
            $path = $request->file('file')->storeAs('debug', 'debug_upload.xlsx');
            
            // Buat log import terlebih dahulu untuk mendapatkan ID-nya
            $importLog = \App\Models\ImportLog::create([
                'filename' => $filename,
                'sumber' => $request->sumber,
                'rows_count' => 0
            ]);
            
            $countBefore = DataAlat::count();
            // Hubungkan import log ID ke importir
            Excel::import(new DataAlatImport($request->sumber, $importLog->id), \Illuminate\Support\Facades\Storage::disk('local')->path($path));
            
            $countAfter = DataAlat::count();
            $rowsImported = $countAfter - $countBefore;

            // Update jumlah baris terimport
            $importLog->update([
                'rows_count' => $rowsImported
            ]);

            // Update summary setelah import
            $this->updateSummary();

            return redirect()->back()->with('success', "Data berhasil diimport! ($rowsImported baris baru ditambahkan)");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function deleteLog($id)
    {
        try {
            $log = \App\Models\ImportLog::findOrFail($id);
            
            // Hapus data alat berat yang terkait dengan file ini
            DataAlat::where('import_log_id', $log->id)->delete();
            
            $filename = $log->filename;
            $log->delete();

            // Kosongkan dan hitung ulang summary stats
            MonitoringSummary::truncate();
            $this->updateSummary();

            return redirect()->back()->with('success', "Data dari file '$filename' berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    private function updateSummary()
    {
        // Dapatkan semua bulan dan tahun unik dari data_alat
        $monthsAndYears = DataAlat::select('bulan', 'tahun')
            ->groupBy('bulan', 'tahun')
            ->get();

        foreach ($monthsAndYears as $my) {
            $data = DataAlat::where('bulan', $my->bulan)
                ->where('tahun', $my->tahun)
                ->get();

            foreach ($data as $item) {
                if ($item->waktu_kerja !== null || $item->waktu_operasi !== null) {
                    MonitoringSummary::updateOrCreate(
                        [
                            'id_aset' => $item->id_aset,
                            'tanggal' => $item->tanggal,
                        ],
                        [
                            'group_aset' => $item->group_aset,
                            'area' => $item->area,
                            'total_waktu_kerja' => $item->waktu_kerja ?? 0,
                            'total_waktu_operasi' => $item->waktu_operasi ?? 0,
                            'total_waktu_idle' => $item->waktu_idle ?? 0,
                            'rata_idle' => $item->persen_idle ?? 0,
                            'total_bahan_bakar' => $item->total_bahan_bakar ?? 0,
                            'rata_bahan_bakar' => $item->laju_bakar ?? 0,
                        ]
                    );
                }
            }

            // Agregasi per aset per tanggal
            $aggregated = DataAlat::where('bulan', $my->bulan)
                ->where('tahun', $my->tahun)
                ->select(
                    'id_aset',
                    'tanggal',
                    DB::raw('AVG(waktu_kerja) as avg_waktu_kerja'),
                    DB::raw('AVG(waktu_operasi) as avg_waktu_operasi'),
                    DB::raw('AVG(waktu_idle) as avg_waktu_idle'),
                    DB::raw('AVG(persen_idle) as avg_idle'),
                    DB::raw('SUM(total_bahan_bakar) as sum_bahan_bakar'),
                    DB::raw('AVG(laju_bakar) as avg_bahan_bakar')
                )
                ->groupBy('id_aset', 'tanggal')
                ->get();

            foreach ($aggregated as $item) {
                MonitoringSummary::updateOrCreate(
                    [
                        'id_aset' => $item->id_aset,
                        'tanggal' => $item->tanggal,
                    ],
                    [
                        'total_waktu_kerja' => $item->avg_waktu_kerja ?? 0,
                        'total_waktu_operasi' => $item->avg_waktu_operasi ?? 0,
                        'total_waktu_idle' => $item->avg_waktu_idle ?? 0,
                        'rata_idle' => $item->avg_idle ?? 0,
                        'total_bahan_bakar' => $item->sum_bahan_bakar ?? 0,
                        'rata_bahan_bakar' => $item->avg_bahan_bakar ?? 0,
                    ]
                );
            }
        }
    }

    public function clearData()
    {
        DataAlat::truncate();
        MonitoringSummary::truncate();
        \App\Models\ImportLog::truncate();

        return redirect()->back()->with('success', 'Semua data dan riwayat import berhasil dihapus!');
    }
}