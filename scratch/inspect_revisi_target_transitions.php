<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    
    for ($row = 15; $row <= 25; $row++) {
        $dapil = trim((string)$sheet->getCell('B' . $row)->getValue());
        $kecamatan = trim((string)$sheet->getCell('C' . $row)->getValue());
        $desa = trim((string)$sheet->getCell('D' . $row)->getValue());
        echo "Row $row: B='$dapil', C='$kecamatan', D='$desa'\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
