<?php

namespace App\Imports;

use App\Models\DataAlat;
use App\Models\MasterAset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class DataAlatImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private string $sumber;
    private ?int $importLogId;
    private int $processedRows = 0;
    private int $validRows = 0;
    private array $skipReasons = [];
    private array $periods = [];
    private array $assetIds = [];
    private array $assetsMap = [];

    public function __construct(string $sumber = 'CATERPILLAR', ?int $importLogId = null)
    {
        $this->sumber = $sumber;
        $this->importLogId = $importLogId;

        // Load master assets to map telemetry metadata
        try {
            $masters = MasterAset::all();
            foreach ($masters as $m) {
                // Map by full unit code (e.g., E004-BTE)
                $unitCodeUpper = strtoupper($m->unit_code);
                $this->assetsMap[$unitCodeUpper] = [
                    'unit_code' => $m->unit_code,
                    'group_aset' => $m->group_aset,
                    'area' => $m->area,
                    'internal_order' => $m->internal_order,
                    'group_internal_order' => $m->group_internal_order,
                    'pt' => $m->pt
                ];

                // Map by serial number (e.g., LKR00269)
                if (!empty($m->nomor_seri)) {
                    $serialUpper = strtoupper($m->nomor_seri);
                    $this->assetsMap[$serialUpper] = [
                        'unit_code' => $m->unit_code,
                        'group_aset' => $m->group_aset,
                        'area' => $m->area,
                        'internal_order' => $m->internal_order,
                        'group_internal_order' => $m->group_internal_order,
                        'pt' => $m->pt
                    ];
                }

                // Map by short name parsed from unit code (e.g. "E004-BTE" -> "BTE")
                $parts = explode('-', $m->unit_code);
                $short = strtoupper($parts[1] ?? $parts[0] ?? '');
                if (!empty($short) && !isset($this->assetsMap[$short])) {
                    $this->assetsMap[$short] = [
                        'unit_code' => $m->unit_code,
                        'group_aset' => $m->group_aset,
                        'area' => $m->area,
                        'internal_order' => $m->internal_order,
                        'group_internal_order' => $m->group_internal_order,
                        'pt' => $m->pt
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silence if table is not migrated or migrated on-the-fly
        }
    }

    public function model(array $row)
    {
        $this->processedRows++;

        // 1. Skip error rows (containing insufficient runtime, invalid value, etc.)
        $keteranganLower = strtolower($row['keterangan'] ?? '');
        if (empty($keteranganLower) ||
            str_contains($keteranganLower, 'insufficient') ||
            str_contains($keteranganLower, 'invalid') ||
            str_contains($keteranganLower, 'missing') ||
            str_contains($keteranganLower, 'value includes') ||
            str_contains($keteranganLower, 'accumulated')
        ) {
            $this->recordSkip('Baris error/keterangan tidak valid');
            return null;
        }

        // 2. Parse date with fallbacks
        try {
            $tanggalRaw = $row['tanggal'] ?? $row['date'] ?? $row['time'] ?? $row['timestamp'] ?? $row['tgl'] ?? null;
            if (empty($tanggalRaw) && (!empty($row['tahun']) || !empty($row['year'])) && (!empty($row['bulan']) || !empty($row['month']))) {
                $monthStr = $row['bulan'] ?? $row['month'];
                $yearVal = $row['tahun'] ?? $row['year'];
                $tanggal = Carbon::parse("1 $monthStr $yearVal");
            } else {
                $tanggal = $this->parseDate($tanggalRaw);
            }
            if (!$tanggal) {
                $this->recordSkip('Tanggal tidak valid');
                return null;
            }
        } catch (\Exception $e) {
            $this->recordSkip('Tanggal tidak valid');
            return null;
        }

        $bulan = $tanggal->format('F');
        $tahun = $tanggal->year;

        // 3. Columns shift left by 1 column for healthy rows, map them correctly!
        $keteranganVal = $row['keterangan'] ?? null; // Asset Name, e.g. "BTE 01"
        $serialVal = $row['id_aset'] ?? null; // Serial Number, e.g. "LKR00269"
        $buatanVal = $row['nomor_seri_aset'] ?? 'CAT'; // Manufacturer, e.g. "CAT"
        $modelVal = $row['buatan'] ?? 'UNKNOWN'; // Model, e.g. "320-05GX"
        $meteranJam = $this->parseNumeric($row['model'] ?? null); // Hour Meter, e.g. 3437.59
        
        $waktuTerakhir = $this->parseDateTime($row['meteran_jam_jam'] ?? null); // Last reported meter time
        $laporanPemanfaatan = $this->parseDateTime($row['waktu_terakhir_dilaporkan_meteran_jam'] ?? null); // Utilization report time
        
        $zonaWaktu = $row['laporan_pemanfaatan_terakhir'] ?? null; // timezone offset
        $namaZona = $row['offset_zona_waktu'] ?? null; // timezone name
        $waktuOperasi = $this->parseNumeric($row['nama_tampilan_zona_waktu'] ?? null); // Operating Hours
        $waktuIdle = $this->parseNumeric($row['waktu_operasi_jam'] ?? null); // Idle Hours
        $waktuKerja = $this->parseNumeric($row['waktu_idle_jam'] ?? null); // Working Hours
        
        $persenIdle = $this->parseNumeric($row['waktu_kerja_jam'] ?? null); // Idle %
        if (is_null($persenIdle) && $waktuOperasi > 0) {
            $persenIdle = ($waktuIdle / $waktuOperasi) * 100;
        }
        
        $totalBahanBakar = $this->parseNumeric($row['idle'] ?? null); // Total Fuel
        $lajuBakar = $this->parseNumeric($row['total_bahan_bakar_yang_terbakar_l'] ?? null); // Average Fuel Rate
        if (is_null($lajuBakar) && $totalBahanBakar && $waktuOperasi > 0) {
            $lajuBakar = $totalBahanBakar / $waktuOperasi;
        }

        // 4. Resolve unit code (id_aset) and metadata (Group, Area, IO) using assetsMap
        $short = '';
        if ($keteranganVal) {
            $parts = explode(' ', trim($keteranganVal));
            $short = strtoupper($parts[0] ?? '');
        }

        $mapped = null;
        $serialUpper = $serialVal ? strtoupper($serialVal) : '';
        
        if (!empty($serialUpper) && isset($this->assetsMap[$serialUpper])) {
            $mapped = $this->assetsMap[$serialUpper];
        } elseif (!empty($short) && isset($this->assetsMap[$short])) {
            $mapped = $this->assetsMap[$short];
        }

        if ($mapped) {
            $idAset = $mapped['unit_code'];
            $groupAset = $mapped['group_aset'];
            $area = $mapped['area'];
            $internalOrder = $mapped['internal_order'];
            $groupInternalOrder = $mapped['group_internal_order'];
            $pt = $mapped['pt'];
        } else {
            // Fallback
            $idAset = $serialVal ?? $keteranganVal ?? 'UNKNOWN';
            $groupAset = null;
            $area = null;
            $internalOrder = null;
            $groupInternalOrder = null;
            $pt = $row['pt'] ?? null;
        }

        $this->validRows++;
        $this->periods[$bulan . ' ' . $tahun] = true;
        $this->assetIds[$idAset] = true;

        return new DataAlat([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'tanggal' => $tanggal,
            'keterangan' => $keteranganVal,
            'id_aset' => $idAset,
            'nomor_seri' => $serialVal,
            'buatan' => $buatanVal,
            'model' => $modelVal,
            'group_aset' => $groupAset,
            'area' => $area,
            'pt' => $pt,
            'internal_order' => $internalOrder,
            'group_internal_order' => $groupInternalOrder,
            'group_desc' => $row['group_desc'] ?? null,
            'meteran_jam' => $meteranJam,
            'waktu_terakhir' => $waktuTerakhir,
            'laporan_pemanfaatan' => $laporanPemanfaatan,
            'zona_waktu' => $zonaWaktu,
            'nama_zona' => $namaZona,
            'waktu_operasi' => $waktuOperasi,
            'waktu_idle' => $waktuIdle,
            'waktu_kerja' => $waktuKerja,
            'persen_idle' => $persenIdle,
            'total_bahan_bakar' => $totalBahanBakar,
            'laju_bakar' => $lajuBakar,
            'daya_dihasilkan' => $this->parseNumeric($row['laju_total_pembakaran_bahan_bakar_l_jam'] ?? null),
            'beban_harian' => $this->parseNumeric($row['daya_dihasilkan_kwh'] ?? null),
            'daya_per_unit' => $this->parseNumeric($row['beban_harian_rata-rata'] ?? null),
            'sumber_data' => $this->sumber,
            'import_log_id' => $this->importLogId,
        ]);
    }

    private function recordSkip(string $reason): void
    {
        $this->skipReasons[$reason] = ($this->skipReasons[$reason] ?? 0) + 1;
    }

    public function summary(): array
    {
        return [
            'processed_rows' => $this->processedRows,
            'valid_rows' => $this->validRows,
            'skipped_rows' => max(0, $this->processedRows - $this->validRows),
            'skip_reasons' => $this->skipReasons,
            'periods' => array_keys($this->periods),
            'unique_assets' => count($this->assetIds),
        ];
    }

    private function parseNumeric($value)
    {
        if (is_null($value) || $value === '' || $value === ' ') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = str_replace(' ', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function parseDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($value - 2);
            }

            $formats = ['d/m/Y H:i:s', 'm/d/Y H:i:s', 'Y-m-d H:i:s', 'd/m/Y', 'm/d/Y', 'Y-m-d'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($value - 2);
            }

            $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd/m/Y H:i:s', 'm/d/Y H:i:s', 'Y-m-d H:i:s'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
