<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Kader;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = 'd:/BP3BEKASI/kbh-main-project-3/kabupatenbekasi-hebat/storage/app/private/import/data_anggota_pelopor.xlsx';

if (!file_exists($filePath)) {
    echo "File not found: " . $filePath . "\n";
    exit(1);
}

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    
    echo "Starting import for $highestRow rows...\n";
    
    $creatorId = User::query()->where('role', '=', 'admin_dpd')->value('id') ?? User::query()->value('id');
    
    $inserted = 0;
    $updated = 0;
    $skipped = 0;
    $noMatchWilayah = 0;
    
    DB::beginTransaction();
    
    for ($rowNum = 4; $rowNum <= $highestRow; $rowNum++) {
        $no = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
        $nama = trim((string)$sheet->getCell('B' . $rowNum)->getValue());
        $phone = trim((string)$sheet->getCell('C' . $rowNum)->getValue());
        $kecamatan = trim((string)$sheet->getCell('D' . $rowNum)->getValue());
        $kelurahan = trim((string)$sheet->getCell('E' . $rowNum)->getValue());
        $rw = trim((string)$sheet->getCell('F' . $rowNum)->getValue());
        $rt = trim((string)$sheet->getCell('G' . $rowNum)->getValue());
        $nia = trim((string)$sheet->getCell('H' . $rowNum)->getValue());
        
        if (empty($nama) || empty($nia)) {
            $skipped++;
            continue;
        }
        
        // Normalize phone: 628... -> 08...
        $normalizedPhone = $phone;
        if (str_starts_with($phone, '628')) {
            $normalizedPhone = '0' . substr($phone, 2);
        }
        
        // Normalize RT/RW
        $normalizedRw = str_pad($rw, 3, '0', STR_PAD_LEFT);
        $normalizedRt = str_pad($rt, 3, '0', STR_PAD_LEFT);
        
        // Resolve Dapil & Target Wilayah
        $targetWilayah = DB::table('target_wilayahs')
            ->where('kecamatan', '=', mb_strtoupper($kecamatan))
            ->where('desa', '=', mb_strtoupper($kelurahan))
            ->first();
            
        $dapil = null;
        $targetWilayahId = null;
        if ($targetWilayah) {
            $dapil = $targetWilayah->dapil;
            $targetWilayahId = $targetWilayah->id;
        } else {
            $noMatchWilayah++;
            // Try matching only kecamatan to get the dapil
            $anyDapil = DB::table('target_wilayahs')
                ->where('kecamatan', '=', mb_strtoupper($kecamatan))
                ->value('dapil');
            $dapil = $anyDapil;
        }
        
        // Check duplicate NIA
        $existing = Kader::query()->where('nia', '=', $nia)->first();
        
        if ($existing) {
            // Update only if not activated yet
            if (!$existing->is_activated) {
                $existing->update([
                    'nama' => $nama,
                    'no_hp' => $normalizedPhone,
                    'no_wa' => $normalizedPhone,
                    'jenjang' => 'pelopor', // update to pelopor
                    'dapil' => $dapil ?? $existing->dapil,
                    'kecamatan' => $kecamatan ?? $existing->kecamatan,
                    'desa' => $kelurahan ?? $existing->desa,
                    'nomor_rw' => $normalizedRw ?? $existing->nomor_rw,
                    'nomor_rt' => $normalizedRt ?? $existing->nomor_rt,
                    'target_wilayah_id' => $targetWilayahId ?? $existing->target_wilayah_id,
                ]);
                $updated++;
            } else {
                $skipped++;
            }
        } else {
            Kader::query()->create([
                'nama' => $nama,
                'no_hp' => $normalizedPhone,
                'no_wa' => $normalizedPhone,
                'email' => null,
                'nik' => null,
                'no_kta' => null,
                'nia' => $nia,
                'bidang_slug' => null,
                'jenjang' => 'pelopor',
                'tanggal_jenjang' => now(),
                'dapil' => $dapil,
                'kecamatan' => $kecamatan,
                'desa' => $kelurahan,
                'nomor_rw' => $normalizedRw,
                'nomor_rt' => $normalizedRt,
                'target_wilayah_id' => $targetWilayahId,
                'is_korwe' => false,
                'is_korte' => false,
                'is_upa' => false,
                'is_penggalang' => false,
                'is_saksi' => false,
                'bisa_deploy' => true,
                'status' => 'aktif',
                'is_activated' => false,
                'created_by' => $creatorId,
            ]);
            $inserted++;
        }
    }
    
    DB::commit();
    
    echo "\nMigration complete!\n";
    echo "Inserted new kaders: $inserted\n";
    echo "Updated existing: $updated\n";
    echo "Skipped: $skipped\n";
    echo "Wilayah mismatch warnings: $noMatchWilayah\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error during migration: " . $e->getMessage() . "\n";
}
