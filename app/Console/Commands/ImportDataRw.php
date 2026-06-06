<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DataRw;
use App\Models\TargetWilayah;
use Illuminate\Console\Command;

class ImportDataRw extends Command
{
    protected $signature = 'import:data-rw';

    protected $description = 'Import agregasi data per RW dari file DPT per-pemilih dan TPS pemilu';

    /**
     * @var array<string, string>
     */
    private array $normalizeMap = [
        'SERANGBARU' => 'SERANG BARU',
        'KARANGBAHAGIA' => 'KARANG BAHAGIA',
        'KEDUNGWARINGIN' => 'KEDUNG WARINGIN',
        'CIKARANGKOTA' => 'CIKARANG KOTA',
    ];

    public function handle(): int
    {
        $this->info('Step 1: Reading DPT files (per-person)...');

        $dptFiles = $this->resolveDptFiles();
        if ($dptFiles === []) {
            $this->error('Tidak ada file DPT di storage/app/private/import/.');

            return self::FAILURE;
        }

        $rwAgg = [];
        $tpsRwCount = [];
        $tpsDptCount = [];

        foreach ($dptFiles as $file) {
            $this->info('  Reading: ' . basename($file));
            $handle = fopen($file, 'rb');

            if ($handle === false) {
                $this->warn('    -> file tidak bisa dibaca, dilewati.');
                continue;
            }

            fgetcsv($handle);
            $lineCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $lineCount++;

                $kecamatan = $this->normalizeRegion($row[3] ?? null);
                $desa = $this->normalizeRegion($row[4] ?? null);
                $tps = $this->normalizeTps($row[5] ?? null);
                $usia = $this->parseInt($row[8] ?? null);
                $rw = $this->normalizeNumber($row[9] ?? null);
                $rt = $this->normalizeNumber($row[10] ?? null);
                $desaLk = $this->parseInt($row[11] ?? null);
                $desaPr = $this->parseInt($row[12] ?? null);
                $desaTot = $this->parseInt($row[13] ?? null);

                if ($kecamatan === '' || $desa === '' || $rw === '000') {
                    continue;
                }

                $key = "{$kecamatan}|{$desa}|{$rw}";

                if (! isset($rwAgg[$key])) {
                    $rwAgg[$key] = [
                        'kecamatan' => $kecamatan,
                        'desa' => $desa,
                        'rw' => $rw,
                        'dpt' => 0,
                        'gen_z' => 0,
                        'millennial' => 0,
                        'gen_x' => 0,
                        'boomer' => 0,
                        'rts' => [],
                        'tps_set' => [],
                        'desa_lk' => $desaLk,
                        'desa_pr' => $desaPr,
                        'desa_tot' => $desaTot,
                    ];
                }

                $rwAgg[$key]['dpt']++;

                if ($rt !== '000') {
                    $rwAgg[$key]['rts'][$rt] = true;
                }

                if ($tps !== '') {
                    $rwAgg[$key]['tps_set'][$tps] = true;
                    $tpsRwCount["{$kecamatan}|{$desa}|{$tps}|{$rw}"] = ($tpsRwCount["{$kecamatan}|{$desa}|{$tps}|{$rw}"] ?? 0) + 1;
                    $tpsDptCount["{$kecamatan}|{$desa}|{$tps}"] = ($tpsDptCount["{$kecamatan}|{$desa}|{$tps}"] ?? 0) + 1;
                }

                if ($usia <= 27) {
                    $rwAgg[$key]['gen_z']++;
                } elseif ($usia <= 43) {
                    $rwAgg[$key]['millennial']++;
                } elseif ($usia <= 59) {
                    $rwAgg[$key]['gen_x']++;
                } else {
                    $rwAgg[$key]['boomer']++;
                }
            }

            fclose($handle);
            $this->info("    -> {$lineCount} rows processed");
        }

        $this->info('Total unique RW: ' . number_format(count($rwAgg)));

        $this->info('Step 2: Reading TPS votes data...');
        $tpsFile = $this->resolveTpsFile();

        if ($tpsFile === null) {
            $this->error('File tps_dprd.csv tidak ditemukan.');

            return self::FAILURE;
        }

        $tpsVotes = [];
        $handle = fopen($tpsFile, 'rb');

        if ($handle === false) {
            $this->error('File tps_dprd.csv tidak bisa dibaca.');

            return self::FAILURE;
        }

        fgetcsv($handle, 0, ';');

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $kecamatan = $this->normalizeRegion($row[3] ?? null);
            $desa = $this->normalizeRegion($row[4] ?? null);
            $tps = $this->normalizeTps($row[5] ?? null);
            $partai = strtoupper(trim((string) ($row[7] ?? '')));
            $suara = $this->parseInt($row[11] ?? null);

            if ($kecamatan === '' || $desa === '' || $tps === '' || $partai === '') {
                continue;
            }

            $key = "{$kecamatan}|{$desa}|{$tps}";

            if (! isset($tpsVotes[$key])) {
                $tpsVotes[$key] = [];
            }

            $tpsVotes[$key][$partai] = ($tpsVotes[$key][$partai] ?? 0) + $suara;
        }

        fclose($handle);
        $this->info('Total unique TPS with votes: ' . number_format(count($tpsVotes)));

        $this->info('Step 3: Computing per-RW PKS estimates and inserting...');

        $targetMap = TargetWilayah::query()
            ->get(['id', 'dapil', 'kecamatan', 'desa', 'target_avg_per_rw'])
            ->keyBy(fn (TargetWilayah $item): string => $this->targetKey($item->kecamatan, $item->desa));

        $bar = $this->output->createProgressBar(count($rwAgg));
        $bar->start();

        $imported = 0;
        $skipped = 0;

        foreach ($rwAgg as $rw) {
            $bar->advance();

            $tw = $targetMap->get($this->targetKey($rw['kecamatan'], $rw['desa']));

            if (! $tw instanceof TargetWilayah) {
                $skipped++;
                continue;
            }

            $estimasiPerPartai = [];

            foreach (array_keys($rw['tps_set']) as $tps) {
                $tpsKey = "{$rw['kecamatan']}|{$rw['desa']}|{$tps}";
                $tpsRwKey = "{$rw['kecamatan']}|{$rw['desa']}|{$tps}|{$rw['rw']}";

                $dptRwDiTps = $tpsRwCount[$tpsRwKey] ?? 0;
                $dptTotalTps = $tpsDptCount[$tpsKey] ?? 0;
                $factor = $dptTotalTps > 0 ? $dptRwDiTps / $dptTotalTps : 0.0;

                foreach ($tpsVotes[$tpsKey] ?? [] as $partai => $suara) {
                    $estimasiPerPartai[$partai] = ($estimasiPerPartai[$partai] ?? 0) + (int) round($suara * $factor);
                }
            }

            $estimasiPks = (int) ($estimasiPerPartai['PKS'] ?? 0);
            $totalEstimasi = array_sum($estimasiPerPartai);
            $share = $totalEstimasi > 0 ? $estimasiPks / $totalEstimasi : 0.0;
            $ranking = $this->calculateRanking($estimasiPerPartai, 'PKS');
            $result = TargetWilayah::classifyStatus($estimasiPks, $ranking, $share);

            $genderRatio = ($rw['desa_tot'] ?? 0) > 0
                ? ((float) $rw['desa_lk']) / max(1, (int) $rw['desa_tot'])
                : 0.5;
            $dptLaki = (int) round($rw['dpt'] * $genderRatio);
            $dptPerempuan = max(0, (int) $rw['dpt'] - $dptLaki);

            DataRw::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $tw->id,
                    'nomor_rw' => $rw['rw'],
                ],
                [
                    'dapil' => $tw->dapil,
                    'kecamatan' => $rw['kecamatan'],
                    'desa' => $rw['desa'],
                    'dpt' => (int) $rw['dpt'],
                    'dpt_laki' => $dptLaki,
                    'dpt_perempuan' => $dptPerempuan,
                    'gen_z' => (int) $rw['gen_z'],
                    'millennial' => (int) $rw['millennial'],
                    'gen_x' => (int) $rw['gen_x'],
                    'boomer' => (int) $rw['boomer'],
                    'jumlah_rt' => count($rw['rts']),
                    'jumlah_tps' => count($rw['tps_set']),
                    'estimasi_pks' => $estimasiPks,
                    'estimasi_share' => round($share, 4),
                    'estimasi_ranking' => $ranking,
                    'status_wilayah' => $result['status'],
                    'prioritas_urutan' => $result['prioritas'],
                    'target_suara_per_rw' => (int) round((float) $tw->target_avg_per_rw),
                ]
            );

            $imported++;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Imported: {$imported}, Skipped: {$skipped}");

        $summary = DataRw::query()
            ->selectRaw('status_wilayah, count(*) as total, sum(dpt) as total_dpt, sum(estimasi_pks) as total_pks')
            ->groupBy('status_wilayah')
            ->orderBy('status_wilayah')
            ->get();

        $this->table(
            ['Status', 'Jumlah RW', 'Total DPT', 'Est. PKS'],
            $summary->map(fn (DataRw $item): array => [
                $item->status_wilayah,
                (string) $item->total,
                number_format((int) $item->total_dpt),
                number_format((int) $item->total_pks),
            ])->all()
        );

        $dapilSummary = DataRw::query()
            ->selectRaw('dapil, count(*) as total_rw')
            ->groupBy('dapil')
            ->orderBy('dapil')
            ->get();

        $this->table(
            ['Dapil', 'Total RW'],
            $dapilSummary->map(fn (DataRw $item): array => [$item->dapil, (string) $item->total_rw])->all()
        );

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function resolveDptFiles(): array
    {
        $patterns = [
            storage_path('app/private/import/dpt_pileg2024_bekasi_*.csv'),
            storage_path('app/private/import/dpt_pileg2024_bekasi *.csv'),
        ];

        $files = [];

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                $files[$file] = $file;
            }
        }

        ksort($files);

        return array_values($files);
    }

    private function resolveTpsFile(): ?string
    {
        $candidates = [
            storage_path('app/private/import/tps_dprd.csv'),
            storage_path('app/private/import/KOKAB2024/tps_dprd.csv'),
            public_path('data/pemilu/tps_dprd.csv'),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function targetKey(string $kecamatan, string $desa): string
    {
        return $this->canonicalRegion($kecamatan) . '|' . $this->canonicalRegion($desa);
    }

    private function normalizeRegion(mixed $value): string
    {
        $text = strtoupper(trim((string) ($value ?? '')));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $compact = str_replace(' ', '', $text);
        $mapped = $this->normalizeMap[$text] ?? $this->normalizeMap[$compact] ?? $text;

        return preg_replace('/\s+/', ' ', trim($mapped)) ?? $mapped;
    }

    private function canonicalRegion(mixed $value): string
    {
        $text = $this->normalizeRegion($value);
        $text = preg_replace('/[^A-Z0-9 ]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? $text;

        return $text;
    }

    private function normalizeTps(mixed $value): string
    {
        $text = strtoupper(trim((string) ($value ?? '')));

        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }

    private function normalizeNumber(mixed $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) ($value ?? '')) ?? '';
        $number = $digits === '' ? 0 : (int) $digits;

        return str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    private function parseInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        $string = trim((string) ($value ?? ''));
        if ($string === '') {
            return 0;
        }

        $normalized = str_replace([' ', ','], ['', '.'], $string);

        return is_numeric($normalized) ? (int) round((float) $normalized) : 0;
    }

    /**
     * @param array<string, int> $estimasiPerPartai
     */
    private function calculateRanking(array $estimasiPerPartai, string $partaiTarget): int
    {
        if ($estimasiPerPartai === [] || ! isset($estimasiPerPartai[$partaiTarget])) {
            return 99;
        }

        arsort($estimasiPerPartai);

        $ranking = 1;
        foreach ($estimasiPerPartai as $partai => $suara) {
            if ($partai === $partaiTarget) {
                return $ranking;
            }

            $ranking++;
        }

        return 99;
    }
}
