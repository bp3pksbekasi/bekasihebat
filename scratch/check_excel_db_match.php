<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = 'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = 256;
    
    $currentDapil = '';
    $currentKecamatan = '';
    
    $matched = 0;
    $unmatched = [];
    
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
        
        // Skip subtotal / total / header rows
        if ($desa === 'TOTAL' || $desa === '' || 
            str_contains($kecamatan, 'TOTAL') || str_contains($dapil, 'TOTAL') ||
            $desa === 'DESA/KELURAHAN' || str_contains($desa, 'DESA/KELURAHAN') ||
            str_contains($desa, 'KECAMATAN') || str_contains($desa, 'DAPIL')
        ) {
            continue;
        }
        
        // Search in target_wilayahs
        $dbMatch = DB::table('target_wilayahs')
            ->where('kecamatan', '=', $kecamatan)
            ->where('desa', '=', $desa)
            ->first();
            
        if ($dbMatch) {
            $matched++;
        } else {
            $unmatched[] = [
                'row' => $row,
                'dapil' => $dapil,
                'kecamatan' => $kecamatan,
                'desa' => $desa,
            ];
        }
    }
    
    echo "Matched rows: $matched\n";
    echo "Unmatched rows: " . count($unmatched) . "\n\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
