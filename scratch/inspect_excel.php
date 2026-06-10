<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'd:/BP3BEKASI/kbh-main-project-3/kabupatenbekasi-hebat/storage/app/private/import/data_anggota_pelopor.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    
    echo "Highest Row: " . $highestRow . "\n";
    echo "Highest Column: " . $highestColumn . "\n";
    
    // Check if there are other sheets
    $sheetNames = $spreadsheet->getSheetNames();
    echo "Sheets: " . implode(', ', $sheetNames) . "\n";
    
    // Let's print row 3 to check headers
    $headerRow = 3;
    $headers = [];
    for ($col = 'A'; $col <= $highestColumn; $col++) {
        $val = $sheet->getCell($col . $headerRow)->getValue();
        if ($val !== null) {
            $headers[$col] = $val;
        }
    }
    echo "Headers:\n";
    print_r($headers);
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
