<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = 256;
    
    $currentDapil = '';
    $currentKecamatan = '';
    
    $emptyTargetSuara = 0;
    $emptyKorweTarget = 0;
    $emptyKorteTarget = 0;
    $emptyPenggalangTarget = 0;
    
    for ($row = 10; $row <= $highestRow; $row++) {
        $dapilVal = trim((string)$sheet->getCell('B' . $row)->getValue());
        $kecVal = trim((string)$sheet->getCell('C' . $row)->getValue());
        $desaVal = trim((string)$sheet->getCell('D' . $row)->getValue());
        
        if ($dapilVal !== '') {
            $currentDapil = $dapilVal;
        }
        if ($kecVal !== '') {
            $currentKecamatan = $kecVal;
        }
        
        $dapil = strtoupper($currentDapil);
        $kecamatan = strtoupper($currentKecamatan);
        $desa = strtoupper($desaVal);
        
        if ($desa === 'TOTAL' || $desa === '' || 
            str_contains($kecamatan, 'TOTAL') || str_contains($dapil, 'TOTAL') ||
            $desa === 'DESA/KELURAHAN' || str_contains($desa, 'DESA/KELURAHAN') ||
            str_contains($desa, 'KECAMATAN') || str_contains($desa, 'DAPIL')
        ) {
            continue;
        }
        
        $suara2029 = $sheet->getCell('F' . $row)->getValue();
        $korwe2029 = $sheet->getCell('T' . $row)->getValue();
        $korte2029 = $sheet->getCell('X' . $row)->getValue();
        $penggalang2029 = $sheet->getCell('AC' . $row)->getValue();
        
        if ($suara2029 === null || trim((string)$suara2029) === '') {
            $emptyTargetSuara++;
        }
        if ($korwe2029 === null || trim((string)$korwe2029) === '') {
            $emptyKorweTarget++;
        }
        if ($korte2029 === null || trim((string)$korte2029) === '') {
            $emptyKorteTarget++;
        }
        if ($penggalang2029 === null || trim((string)$penggalang2029) === '') {
            $emptyPenggalangTarget++;
        }
    }
    
    echo "Results:\n";
    echo "Empty target suara: $emptyTargetSuara\n";
    echo "Empty KORWE 2029: $emptyKorweTarget\n";
    echo "Empty KORTE 2029: $emptyKorteTarget\n";
    echo "Empty Penggalang 2029: $emptyPenggalangTarget\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
