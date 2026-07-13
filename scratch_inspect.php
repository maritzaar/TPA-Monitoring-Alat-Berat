<?php
ini_set('memory_limit', '1024M');
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = __DIR__ . '/storage/app/private/debug/debug_upload.xlsx';

if (!file_exists($filePath)) {
    echo "File not found at: $filePath\n";
    exit(1);
}

echo "Loading spreadsheet...\n";
$spreadsheet = IOFactory::load($filePath);
$sheetNames = $spreadsheet->getSheetNames();

echo "Sheet Names:\n";
print_r($sheetNames);

foreach ($sheetNames as $name) {
    echo "\nSheet: $name\n";
    $sheet = $spreadsheet->getSheetByName($name);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    echo "Dimensions: $highestRow rows, $highestColumn columns\n";
    
    $rowsToPrint = min(5, $highestRow);
    for ($row = 1; $row <= $rowsToPrint; $row++) {
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
        echo "Row $row: " . json_encode($rowData[0]) . "\n";
    }
}

