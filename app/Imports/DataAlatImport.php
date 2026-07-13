<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataAlatImport implements WithMultipleSheets
{
    protected $sumber;
    protected $importLogId;
    protected ?DataAlatSheetImport $mainSheet = null;

    public function __construct($sumber = 'CATERPILLAR', $importLogId = null)
    {
        $this->sumber = $sumber;
        $this->importLogId = $importLogId;
        $this->mainSheet = new DataAlatSheetImport($sumber, $importLogId);
    }

    public function sheets(): array
    {
        // Only import the first sheet (index 0), skip all other sheets
        return [
            0 => $this->mainSheet,
        ];
    }

    public function summary(): array
    {
        return $this->mainSheet->summary();
    }
}