<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = 'd:/BP3BEKASI/kbh-main-project-3/kabupatenbekasi-hebat/storage/app/private/import/data_anggota_pelopor.xlsx';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    
    echo "Total rows in file: " . $highestRow . "\n";
    
    $successMatch = 0;
    $noMatch = 0;
    
    // Test the first 20 rows
    for ($rowNum = 4; $rowNum <= 24; $rowNum++) {
        $nama = trim((string)$sheet->getCell('B' . $rowNum)->getValue());
        $phone = trim((string)$sheet->getCell('C' . $rowNum)->getValue());
        $kecamatan = trim((string)$sheet->getCell('D' . $rowNum)->getValue());
        $kelurahan = trim((string)$sheet->getCell('E' . $rowNum)->getValue());
        $rw = trim((string)$sheet->getCell('F' . $rowNum)->getValue());
        $rt = trim((string)$sheet->getCell('G' . $rowNum)->getValue());
        $nia = trim((string)$sheet->getCell('H' . $rowNum)->getValue());
        
        if (empty($nama)) continue;
        
        // Search in target_wilayahs
        $match = DB::table('target_wilayahs')
            ->where('kecamatan', '=', mb_strtoupper($kecamatan))
            ->where('desa', '=', mb_strtoupper($kelurahan))
            ->first();
            
        if ($match) {
            $successMatch++;
            echo "Row $rowNum: MATCH! $kecamatan -> $kelurahan | Dapil: {$match->dapil} | Target ID: {$match->id}\n";
        } else {
            $noMatch++;
            echo "Row $rowNum: NO MATCH! $kecamatan -> $kelurahan\n";
        }
    }
    
    echo "Summary (first 20 rows): Matches: $successMatch, No Match: $noMatch\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
