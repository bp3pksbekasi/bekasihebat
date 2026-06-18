<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    
    $highestColumn = $sheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    echo "Total Columns: $highestColumnIndex ($highestColumn)\n\n";
    
    // Rows to dump
    $rowsToDump = [6, 7, 8, 9, 10];
    
    foreach ($rowsToDump as $row) {
        echo "Row $row:\n";
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $val = $sheet->getCell($colLetter . $row)->getValue();
            if ($val !== null && trim((string)$val) !== '') {
                echo "  Column $colLetter ($col): " . trim((string)$val) . "\n";
            }
        }
        echo "---------------------------\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
