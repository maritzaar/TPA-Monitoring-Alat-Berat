<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FuelImportCollection implements WithHeadingRow
{
    // This class is used in Excel::toCollection to parse multiple sheets with heading rows
}
