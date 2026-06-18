<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\TargetWilayah;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportTargetWilayah extends Command
{
    protected $signature = 'import:target-wilayah {file?}';

    protected $description = 'Import data target wilayah dari file Excel ke tabel target_wilayahs';

    public function handle(): int
    {
        $filePath = $this->resolveFilePath($this->argument('file'));

        if ($filePath === null || ! is_file($filePath)) {
            $this->error('File Excel tidak ditemukan.');

            return self::FAILURE;
        }

        $this->info(sprintf('Membaca file: %s', $filePath));

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $this->resolveSheet($spreadsheet);
        $rows = $sheet->toArray(null, true, true, false);

        $imported = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $currentKecamatan = '';
        $currentDapil = '';
        $importedIds = [];

        // Data rows start from row 10 (index 9)
        $dataRows = array_slice($rows, 9);
        
        $progressBar = $this->output->createProgressBar(count($dataRows));
        $progressBar->start();

        foreach ($dataRows as $row) {
            $progressBar->advance();

            // End of main table check
            $firstCell = $this->normalizeText($row[1] ?? null);
            if ($firstCell === 'TOTAL DAPIL 1-7') {
                break;
            }

            $dapilVal = trim((string)($row[1] ?? ''));
            $kecVal = trim((string)($row[2] ?? ''));
            $desaVal = trim((string)($row[3] ?? ''));

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
                $skipped++;
                continue;
            }

            $identity = [
                'dapil' => $dapil,
                'kecamatan' => $kecamatan,
                'desa' => $desa,
            ];

            $attributes = [
                'jumlah_rw' => $this->parseInteger($row[30] ?? null),
                'jumlah_rt' => $this->parseInteger($row[31] ?? null),
                'jumlah_tps' => $this->parseInteger($row[32] ?? null),
                'jumlah_dpt' => $this->parseInteger($row[33] ?? null),
                'suara_pks_2024' => $this->parseInteger($row[36] ?? null),
                'ranking_pks' => $this->parseInteger($row[37] ?? null),
                'persentase_pks' => $this->parseDecimal($row[38] ?? null),
                
                'target_suara_2029' => $this->parseInteger($row[5] ?? null),
                'kekurangan_suara' => $this->parseInteger($row[6] ?? null),
                
                'target_korwe_2026' => $this->parseInteger($row[16] ?? null),
                'target_korwe_2027' => $this->parseInteger($row[17] ?? null),
                'target_korwe_2028' => $this->parseInteger($row[18] ?? null),
                'target_korwe_2029' => $this->parseInteger($row[19] ?? null),
                
                'target_korte_2026' => $this->parseInteger($row[20] ?? null),
                'target_korte_2027' => $this->parseInteger($row[21] ?? null),
                'target_korte_2028' => $this->parseInteger($row[22] ?? null),
                'target_korte_2029' => $this->parseInteger($row[23] ?? null),
                
                'target_penggalang_2026' => $this->parseInteger($row[25] ?? null),
                'target_penggalang_2027' => $this->parseInteger($row[26] ?? null),
                'target_penggalang_2028' => $this->parseInteger($row[27] ?? null),
                'target_penggalang_2029' => $this->parseInteger($row[28] ?? null),
                'target_penggalang' => $this->parseInteger($row[28] ?? null),
                
                'target_avg_per_rw' => $this->parseDecimal($row[7] ?? null),
                'target_avg_per_rt' => $this->parseDecimal($row[8] ?? null),
                'target_avg_per_tps' => $this->parseDecimal($row[9] ?? null),
                'target_avg_per_rumah' => 0.0,
            ];

            [$targetWilayah, $wasCreated] = $this->persistTargetWilayah($identity, $attributes);
            $imported++;
            $importedIds[] = $targetWilayah->id;

            if ($wasCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        // Cleanup records not in the import file using ID comparison to avoid case mismatches causing deletions
        TargetWilayah::query()->get()->each(function (TargetWilayah $targetWilayah) use ($importedIds): void {
            if (! in_array($targetWilayah->id, $importedIds, true)) {
                $targetWilayah->delete();
            }
        });

        $progressBar->finish();
        $this->newLine(2);
        $this->info('Import target wilayah selesai.');
        $this->line(sprintf('Total imported: %d', $imported));
        $this->line(sprintf('Total created: %d', $created));
        $this->line(sprintf('Total updated: %d', $updated));
        $this->line(sprintf('Total skipped: %d', $skipped));

        return self::SUCCESS;
    }

    private function resolveSheet(object $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getSheetByName('Sheet1');

        if ($sheet instanceof Worksheet) {
            return $sheet;
        }

        return $spreadsheet->getActiveSheet();
    }

    private function resolveFilePath(?string $fileArgument): ?string
    {
        if (is_string($fileArgument) && trim($fileArgument) !== '') {
            return $fileArgument;
        }

        $candidates = [
            'C:/Users/HP/Downloads/TARGET INFRA 2029 REVISI 1.xlsx',
            storage_path('app/private/import/REVISI_TARGET_SUARA_PER_DESA_KORWE_KORTE.xlsx'),
            base_path('data/TARGET SUARA PER DESA - KORWE - KORTE.xlsx'),
            storage_path('app/private/import/TARGET_SUARA_PER_DESA_-_KORWE_-_KORTE.xlsx'),
            storage_path('app/private/import/TARGET SUARA PER DESA - KORWE - KORTE.xlsx'),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return $candidates[0];
    }

    private function persistTargetWilayah(array $identity, array $attributes): array
    {
        // Use case-insensitive search by using where to find existing record before calling updateOrCreate
        $targetWilayah = TargetWilayah::query()
            ->where('kecamatan', '=', $identity['kecamatan'])
            ->where('desa', '=', $identity['desa'])
            ->first();

        if ($targetWilayah) {
            // Update existing record, keeping its original ID and names
            $targetWilayah->update($attributes);
            return [$targetWilayah, false];
        }

        // Create new if not found
        $targetWilayah = TargetWilayah::query()->create(array_merge($identity, $attributes));
        return [$targetWilayah, true];
    }

    private function normalizeText(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $text = trim((string) $value);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return strtoupper($text);
    }

    private function parseInteger(mixed $value): int
    {
        return (int) round($this->parseDecimal($value));
    }

    private function parseDecimal(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $string = trim((string) $value);
        if ($string === '') {
            return 0.0;
        }

        $string = str_replace(['%', ' '], '', $string);

        if (str_contains($string, ',') && str_contains($string, '.')) {
            $lastComma = strrpos($string, ',');
            $lastDot = strrpos($string, '.');

            if ($lastComma !== false && $lastDot !== false && $lastComma > $lastDot) {
                $string = str_replace('.', '', $string);
                $string = str_replace(',', '.', $string);
            } else {
                $string = str_replace(',', '', $string);
            }
        } elseif (str_contains($string, ',')) {
            if (preg_match('/^\d{1,3}(,\d{3})+$/', $string) === 1) {
                $string = str_replace(',', '', $string);
            } else {
                $string = str_replace(',', '.', $string);
            }
        }

        if (is_numeric($string)) {
            return (float) $string;
        }

        $normalized = preg_replace('/[^0-9eE\.\-+]/', '', $string) ?? '';

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }
}
