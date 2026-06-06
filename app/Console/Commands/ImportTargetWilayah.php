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

    /**
     * @var array<string, string>
     */
    private array $dapilMap = [
        'BOJONGMANGU' => 'BEKASI 1',
        'CIBARUSAH' => 'BEKASI 1',
        'CIKARANG PUSAT' => 'BEKASI 1',
        'SERANG BARU' => 'BEKASI 1',
        'SETU' => 'BEKASI 1',
        'CIBITUNG' => 'BEKASI 2',
        'CIKARANG BARAT' => 'BEKASI 2',
        'TAMBUN SELATAN' => 'BEKASI 3',
        'SUKATANI' => 'BEKASI 4',
        'SUKAWANGI' => 'BEKASI 4',
        'TAMBELANG' => 'BEKASI 4',
        'TAMBUN UTARA' => 'BEKASI 4',
        'BABELAN' => 'BEKASI 5',
        'MUARAGEMBONG' => 'BEKASI 5',
        'TARUMAJAYA' => 'BEKASI 5',
        'CABANGBUNGIN' => 'BEKASI 6',
        'KARANG BAHAGIA' => 'BEKASI 6',
        'KEDUNG WARINGIN' => 'BEKASI 6',
        'PEBAYURAN' => 'BEKASI 6',
        'SUKAKARYA' => 'BEKASI 6',
        'CIKARANG SELATAN' => 'BEKASI 7',
        'CIKARANG TIMUR' => 'BEKASI 7',
        'CIKARANG UTARA' => 'BEKASI 7',
    ];

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
        $dataRows = array_slice($rows, 5);

        $imported = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $currentKecamatan = '';
        $currentDapil = '';
        $importedKeys = [];
        $pendingVillageRows = [];
        $dapilExpectedTotals = [];

        $progressBar = $this->output->createProgressBar(count($dataRows));
        $progressBar->start();

        foreach ($dataRows as $row) {
            $progressBar->advance();

            if ($this->isEndOfMainTable($row)) {
                break;
            }

            $currentDapil = $this->extractDapilMarker($row) ?? $currentDapil;
            $offset = $this->detectColumnOffset($row);

            if ($this->isDapilSubtotalRow($row)) {
                $dapilExpectedTotals[$currentDapil] = (int) round($this->parseDecimal($row[14 + $offset] ?? null));
                $skipped++;
                continue;
            }

            $kecamatanCell = $this->normalizeText($row[2 + $offset] ?? null);
            if ($kecamatanCell !== '' && ! $this->containsAny($kecamatanCell, ['TOTAL', 'DAPIL', 'KECAMATAN'])) {
                $currentKecamatan = $kecamatanCell;
            }

            $desa = $this->normalizeText($row[3 + $offset] ?? null);
            $jumlahRw = $this->parseInteger($row[4 + $offset] ?? null);

            if ($desa === 'TOTAL' && $currentKecamatan !== '' && $currentDapil !== '') {
                $subtotalTarget = (int) round($this->parseDecimal($row[14 + $offset] ?? null));
                $subtotalKekurangan = (int) round($this->parseDecimal($row[15 + $offset] ?? null));
                $pendingKey = $this->kecamatanKey($currentDapil, $currentKecamatan);

                if ($subtotalTarget > 0 && isset($pendingVillageRows[$pendingKey])) {
                    [$createdCount, $updatedCount, $persistedKeys] = $this->persistDistributedKecamatanTargets(
                        rows: $pendingVillageRows[$pendingKey],
                        subtotalTarget: $subtotalTarget,
                        subtotalKekurangan: $subtotalKekurangan,
                    );

                    $created += $createdCount;
                    $updated += $updatedCount;
                    $imported += count($persistedKeys);

                    foreach ($persistedKeys as $key) {
                        $importedKeys[$key] = true;
                    }

                    unset($pendingVillageRows[$pendingKey]);
                }

                $skipped++;
                continue;
            }

            if (
                $desa === ''
                || $currentKecamatan === ''
                || $this->containsAny($desa, ['DESA', 'KELURAHAN', 'DAPIL'])
                || $jumlahRw <= 0
            ) {
                $skipped++;
                continue;
            }

            $dapil = $this->inferDapil($currentKecamatan, $currentDapil);
            if ($dapil === '') {
                $skipped++;
                continue;
            }

            $identity = [
                'dapil' => $dapil,
                'kecamatan' => $currentKecamatan,
                'desa' => $desa,
            ];

            $attributes = [
                'jumlah_rw' => $jumlahRw,
                'jumlah_rt' => $this->parseInteger($row[5 + $offset] ?? null),
                'jumlah_tps' => $this->parseInteger($row[6 + $offset] ?? null),
                'jumlah_dpt' => $this->parseInteger($row[7 + $offset] ?? null),
                'target_korwe_2026' => $this->parseInteger($row[21 + $offset] ?? null),
                'target_korwe_2027' => $this->parseInteger($row[22 + $offset] ?? null),
                'target_korwe_2028' => $this->parseInteger($row[23 + $offset] ?? null),
                'target_korwe_2029' => $this->parseInteger($row[24 + $offset] ?? null),
                'target_korte_2026' => $this->parseInteger($row[25 + $offset] ?? null),
                'target_korte_2027' => $this->parseInteger($row[26 + $offset] ?? null),
                'target_korte_2028' => $this->parseInteger($row[27 + $offset] ?? null),
                'target_korte_2029' => $this->parseInteger($row[28 + $offset] ?? null),
            ];

            if ($this->hasValue($row[10 + $offset] ?? null)) {
                $attributes['suara_pks_2024'] = $this->parseInteger($row[10 + $offset] ?? null);
            }

            if ($this->hasValue($row[11 + $offset] ?? null)) {
                $attributes['ranking_pks'] = $this->parseInteger($row[11 + $offset] ?? null);
            }

            if ($this->hasValue($row[12 + $offset] ?? null)) {
                $attributes['persentase_pks'] = $this->parseDecimal($row[12 + $offset] ?? null);
            }

            if ($this->hasValue($row[14 + $offset] ?? null)) {
                $attributes['target_suara_2029'] = (int) round($this->parseDecimal($row[14 + $offset] ?? null));
                $attributes['kekurangan_suara'] = (int) round($this->parseDecimal($row[15 + $offset] ?? null));
                $attributes['target_avg_per_rw'] = $this->parseDecimal($row[16 + $offset] ?? null);
                $attributes['target_avg_per_rt'] = $this->parseDecimal($row[17 + $offset] ?? null);
                $attributes['target_avg_per_tps'] = $this->parseDecimal($row[18 + $offset] ?? null);
                $attributes['target_avg_per_rumah'] = $this->parseDecimal($row[19 + $offset] ?? null);

                [$targetWilayah, $wasCreated] = $this->persistTargetWilayah($identity, $attributes);
                $imported++;
                $importedKeys[$this->buildImportKey($identity)] = true;

                if ($wasCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                $pendingVillageRows[$this->kecamatanKey($dapil, $currentKecamatan)][] = [
                    'identity' => $identity,
                    'attributes' => $attributes,
                ];
            }
        }

        foreach ($pendingVillageRows as $rows) {
            foreach ($rows as $pendingRow) {
                [$targetWilayah, $wasCreated] = $this->persistTargetWilayah($pendingRow['identity'], $pendingRow['attributes']);
                $imported++;
                $importedKeys[$this->buildImportKey($pendingRow['identity'])] = true;

                if ($wasCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        }

        TargetWilayah::query()->get()->each(function (TargetWilayah $targetWilayah) use ($importedKeys): void {
            $key = $this->buildImportKey([
                'dapil' => $targetWilayah->dapil,
                'kecamatan' => $targetWilayah->kecamatan,
                'desa' => $targetWilayah->desa,
            ]);

            if (! isset($importedKeys[$key])) {
                $targetWilayah->delete();
            }
        });

        $this->calibrateDapilTotals($dapilExpectedTotals);
        $this->recalculateTargetPenggalang();

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

    private function extractDapilMarker(array $row): ?string
    {
        $cells = array_map(
            fn ($value): string => $this->normalizeText($value),
            array_slice($row, 0, 4)
        );

        foreach ($cells as $cell) {
            if ($cell === '') {
                continue;
            }

            if (preg_match('/(?:TOTAL\s+)?DAPIL\s*([1-7])/', $cell, $matches) === 1) {
                return 'BEKASI ' . $matches[1];
            }
        }

        return null;
    }

    private function inferDapil(string $kecamatan, string $currentDapil): string
    {
        if (isset($this->dapilMap[$kecamatan])) {
            return $this->dapilMap[$kecamatan];
        }

        return $currentDapil;
    }

    private function detectColumnOffset(array $row): int
    {
        if ($this->parseInteger($row[4] ?? null) > 0) {
            return 0;
        }

        if ($this->parseInteger($row[3] ?? null) > 0) {
            return -1;
        }

        $firstColumn = $this->normalizeText($row[1] ?? null);

        return str_contains($firstColumn, 'DAPIL') ? 0 : -1;
    }

    private function isEndOfMainTable(array $row): bool
    {
        $first = $this->normalizeText($row[1] ?? null);
        $second = $this->normalizeText($row[2] ?? null);
        $third = $this->normalizeText($row[3] ?? null);

        return in_array('TOTAL DAPIL 1-7', [$first, $second, $third], true);
    }

    private function isDapilSubtotalRow(array $row): bool
    {
        $second = $this->normalizeText($row[2] ?? null);

        return preg_match('/^TOTAL DAPIL [1-7]$/', $second) === 1;
    }

    private function hasValue(mixed $value): bool
    {
        return ! in_array($value, [null, ''], true);
    }

    private function kecamatanKey(string $dapil, string $kecamatan): string
    {
        return $dapil . '|' . $kecamatan;
    }

    /**
     * @param array{dapil:string,kecamatan:string,desa:string} $identity
     * @param array<string,mixed> $attributes
     * @return array{0:TargetWilayah,1:bool}
     */
    private function persistTargetWilayah(array $identity, array $attributes): array
    {
        $targetWilayah = TargetWilayah::query()->updateOrCreate($identity, $attributes);

        return [$targetWilayah, $targetWilayah->wasRecentlyCreated];
    }

    /**
     * @param array<int, array{identity:array{dapil:string,kecamatan:string,desa:string},attributes:array<string,mixed>}> $rows
     * @return array{0:int,1:int,2:array<int,string>}
     */
    private function persistDistributedKecamatanTargets(array $rows, int $subtotalTarget, int $subtotalKekurangan): array
    {
        $weights = array_map(
            fn (array $row): int => max(1, (int) ($row['attributes']['jumlah_dpt'] ?? 0)),
            $rows
        );

        $targets = $this->distributeIntegerTotal($subtotalTarget, $weights);
        $kekurangan = $this->distributeIntegerTotal($subtotalKekurangan, $weights);
        $created = 0;
        $updated = 0;
        $keys = [];

        foreach ($rows as $index => $row) {
            $attributes = $row['attributes'];
            $target = $targets[$index] ?? 0;
            $kurang = $kekurangan[$index] ?? 0;

            $attributes['target_suara_2029'] = $target;
            $attributes['kekurangan_suara'] = $kurang;
            $attributes['target_avg_per_rw'] = ($attributes['jumlah_rw'] ?? 0) > 0 ? round($target / (int) $attributes['jumlah_rw'], 2) : 0;
            $attributes['target_avg_per_rt'] = ($attributes['jumlah_rt'] ?? 0) > 0 ? round($target / (int) $attributes['jumlah_rt'], 2) : 0;
            $attributes['target_avg_per_tps'] = ($attributes['jumlah_tps'] ?? 0) > 0 ? round($target / (int) $attributes['jumlah_tps'], 2) : 0;
            $attributes['target_avg_per_rumah'] = 0;

            [, $wasCreated] = $this->persistTargetWilayah($row['identity'], $attributes);
            $keys[] = $this->buildImportKey($row['identity']);

            if ($wasCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        return [$created, $updated, $keys];
    }

    /**
     * @param array<int, int> $weights
     * @return array<int, int>
     */
    private function distributeIntegerTotal(int $total, array $weights): array
    {
        $sumWeights = array_sum($weights);

        if ($sumWeights <= 0) {
            return array_fill(0, count($weights), 0);
        }

        $allocations = [];
        $remainders = [];
        $allocated = 0;

        foreach ($weights as $index => $weight) {
            $exact = ($total * $weight) / $sumWeights;
            $base = (int) floor($exact);
            $allocations[$index] = $base;
            $remainders[$index] = $exact - $base;
            $allocated += $base;
        }

        $remaining = $total - $allocated;
        arsort($remainders);

        foreach (array_keys($remainders) as $index) {
            if ($remaining <= 0) {
                break;
            }

            $allocations[$index]++;
            $remaining--;
        }

        ksort($allocations);

        return array_values($allocations);
    }

    /**
     * @param array{dapil:string,kecamatan:string,desa:string} $identity
     */
    private function buildImportKey(array $identity): string
    {
        return implode('|', [$identity['dapil'], $identity['kecamatan'], $identity['desa']]);
    }

    /**
     * @param array<string, int> $dapilExpectedTotals
     */
    private function calibrateDapilTotals(array $dapilExpectedTotals): void
    {
        foreach ($dapilExpectedTotals as $dapil => $expectedTotal) {
            if ($dapil === '') {
                continue;
            }

            $actualTotal = (int) TargetWilayah::query()->where('dapil', $dapil)->sum('target_suara_2029');
            $diff = $expectedTotal - $actualTotal;

            if ($diff === 0) {
                continue;
            }

            $targetWilayah = TargetWilayah::query()
                ->where('dapil', $dapil)
                ->orderByDesc('target_suara_2029')
                ->first();

            if (! $targetWilayah) {
                continue;
            }

            $targetWilayah->target_suara_2029 = max(0, (int) $targetWilayah->target_suara_2029 + $diff);
            $targetWilayah->kekurangan_suara = max(0, (int) $targetWilayah->kekurangan_suara + $diff);
            $targetWilayah->target_avg_per_rw = $targetWilayah->jumlah_rw > 0 ? round($targetWilayah->target_suara_2029 / $targetWilayah->jumlah_rw, 2) : 0;
            $targetWilayah->target_avg_per_rt = $targetWilayah->jumlah_rt > 0 ? round($targetWilayah->target_suara_2029 / $targetWilayah->jumlah_rt, 2) : 0;
            $targetWilayah->target_avg_per_tps = $targetWilayah->jumlah_tps > 0 ? round($targetWilayah->target_suara_2029 / $targetWilayah->jumlah_tps, 2) : 0;
            $targetWilayah->save();
        }
    }

    private function recalculateTargetPenggalang(): void
    {
        $totalRw = (int) TargetWilayah::query()->sum('jumlah_rw');
        $totalTarget = 35000;

        if ($totalRw <= 0) {
            TargetWilayah::query()->update(['target_penggalang' => 0]);

            return;
        }

        $rows = TargetWilayah::query()->orderBy('dapil')->orderBy('kecamatan')->orderBy('desa')->get();
        $allocations = [];
        $remainders = [];
        $allocated = 0;

        foreach ($rows as $item) {
            $exact = ($item->jumlah_rw / $totalRw) * $totalTarget;
            $base = (int) floor($exact);
            $allocations[$item->id] = $base;
            $remainders[$item->id] = $exact - $base;
            $allocated += $base;
        }

        $remaining = $totalTarget - $allocated;
        arsort($remainders);

        foreach (array_keys($remainders) as $id) {
            if ($remaining <= 0) {
                break;
            }

            $allocations[$id]++;
            $remaining--;
        }

        TargetWilayah::chunk(100, function ($items) use ($allocations): void {
            foreach ($items as $item) {
                $item->update([
                    'target_penggalang' => $allocations[$item->id] ?? 0,
                ]);
            }
        });
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

    /**
     * @param array<int, string> $needles
     */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
