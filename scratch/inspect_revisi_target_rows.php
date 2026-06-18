<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    
    echo "Total Rows: $highestRow\n\n";
    
    for ($row = 10; $row <= $highestRow; $row++) {
        $dapil = trim((string)$sheet->getCell('B' . $row)->getValue());
        $kecamatan = trim((string)$sheet->getCell('C' . $row)->getValue());
        $desa = trim((string)$sheet->getCell('D' . $row)->getValue());
        
        // Print rows that contain totals or look like markers
        if (str_contains(strtoupper($dapil), 'TOTAL') || 
            str_contains(strtoupper($kecamatan), 'TOTAL') || 
            str_contains(strtoupper($desa), 'TOTAL') ||
            $row > $highestRow - 10 ||
            $row <= 15
        ) {
            echo "Row $row: Dapil='$dapil' | Kec='$kecamatan' | Desa='$desa'\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
