<?php

declare(strict_types=1);

namespace App\Support\BedahDapil;

use App\Models\PemiluDesaSummary;
use App\Models\PemiluPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PemiluSummaryCompiler
{
    private const PKS_PARTY_ID = '12';
    private const PKS_PARTY_NAME = 'PKS';
    private const TOTAL_DPRD_SEATS = 55;

    /**
     * @var array<string, string>
     */
    private array $normalizeMap = [
        'SERANGBARU' => 'SERANG BARU',
        'KARANGBAHAGIA' => 'KARANG BAHAGIA',
        'KEDUNGWARINGIN' => 'KEDUNG WARINGIN',
        'CIKARANGKOTA' => 'CIKARANG KOTA',
    ];

    /**
     * @param list<string> $dptFiles
     * @return array{period:\App\Models\PemiluPeriod,total_desa:int,dpt_files:list<string>,tps_file:string}
     */
    public function compile(
        int $tahun,
        string $jenis = 'dprd',
        ?string $label = null,
        ?string $tpsFile = null,
        array $dptFiles = [],
        bool $setDefault = false,
        ?callable $progress = null
    ): array {
        $resolvedDptFiles = $this->resolveDptFiles($tahun, $dptFiles);
        $resolvedTpsFile = $this->resolveTpsFile($tahun, $tpsFile);

        if ($resolvedDptFiles === []) {
            throw new RuntimeException("File DPT untuk tahun {$tahun} tidak ditemukan.");
        }

        if ($resolvedTpsFile === null) {
            throw new RuntimeException("File TPS untuk tahun {$tahun} tidak ditemukan.");
        }

        $villages = [];
        if ($progress !== null) {
            $progress("Mulai baca TPS: {$resolvedTpsFile}");
        }
        $this->readTpsFile($resolvedTpsFile, $villages);
        if ($progress !== null) {
            $progress('TPS selesai. Desa terdeteksi: '.count($villages));
        }
        $this->readDptFiles($resolvedDptFiles, $villages, $progress);
        if ($progress !== null) {
            $progress('Memulai kompilasi analisa caleg...');
        }
        $calegAnalysisPayload = (new CalegAnalysisCompiler())->compile($resolvedTpsFile);
        if ($progress !== null) {
            $progress('Kompilasi analisa caleg selesai.');
        }

        // Reconnect after long CSV processing to avoid idle MySQL connections dropping
        // before the transaction begins.
        DB::purge();
        DB::reconnect();

        $period = DB::transaction(function () use ($tahun, $jenis, $label, $setDefault, $resolvedDptFiles, $resolvedTpsFile, $calegAnalysisPayload, &$villages): PemiluPeriod {
            if ($setDefault) {
                PemiluPeriod::query()
                    ->where('jenis', $jenis)
                    ->update(['is_default' => false]);
            }

            $period = PemiluPeriod::query()->updateOrCreate(
                ['tahun' => $tahun, 'jenis' => $jenis],
                [
                    'label' => $label ?: strtoupper($jenis).' '.$tahun,
                    'slug' => Str::slug($jenis.'-'.$tahun),
                    'status' => 'published',
                    'is_default' => $setDefault,
                    'source_meta' => [
                        'dpt_files' => array_map('basename', $resolvedDptFiles),
                        'tps_file' => basename($resolvedTpsFile),
                    ],
                    'caleg_summary_payload' => $calegAnalysisPayload,
                ]
            );

            PemiluDesaSummary::query()->where('pemilu_period_id', $period->id)->delete();

            foreach ($villages as $summary) {
                $payload = $this->buildVillageSummaryPayload($summary);
                PemiluDesaSummary::query()->create([
                    'pemilu_period_id' => $period->id,
                    ...$payload,
                ]);
            }

            return $period;
        });

        return [
            'period' => $period,
            'total_desa' => count($villages),
            'dpt_files' => $resolvedDptFiles,
            'tps_file' => $resolvedTpsFile,
        ];
    }

    /**
     * @param list<string> $explicitFiles
     * @return list<string>
     */
    private function resolveDptFiles(int $tahun, array $explicitFiles = []): array
    {
        if ($explicitFiles !== []) {
            return array_values(array_filter($explicitFiles, static fn ($file): bool => is_file($file)));
        }

        $patterns = [
            storage_path("app/private/import/dpt_pileg{$tahun}_bekasi_*.csv"),
            storage_path("app/private/import/dpt_pileg{$tahun}_bekasi *.csv"),
            storage_path("app/private/import/DPT{$tahun}/dpt_pileg{$tahun}_bekasi_*.csv"),
            storage_path("app/private/import/DPT{$tahun}/dpt_pileg{$tahun}_bekasi *.csv"),
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

    private function resolveTpsFile(int $tahun, ?string $explicitFile = null): ?string
    {
        if ($explicitFile !== null && is_file($explicitFile)) {
            return $explicitFile;
        }

        $candidates = [
            storage_path("app/private/import/tps_dprd_{$tahun}.csv"),
            storage_path('app/private/import/tps_dprd.csv'),
            storage_path("app/private/import/KOKAB{$tahun}/tps_dprd_{$tahun}.csv"),
            storage_path("app/private/import/KOKAB{$tahun}/tps_dprd.csv"),
            public_path('data/pemilu/tps_dprd.csv'),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param array<string, array<string, mixed>> $villages
     */
    private function readTpsFile(string $file, array &$villages): void
    {
        $handle = fopen($file, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File TPS tidak bisa dibaca: '.$file);
        }

        $headers = fgetcsv($handle, 0, ';') ?: [];
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $record = $this->combineRow($headers, $row);
            $dapil = $this->resolveLatestDapil($record['dapil'] ?? null, $record['kecamatan'] ?? null);
            $kecamatan = $this->normalizeRegion($record['kecamatan'] ?? null);
            $desa = $this->normalizeRegion($record['desa'] ?? null);
            $tps = $this->normalizeTps($record['tps'] ?? null);
            $partyId = trim((string) ($record['partai_id'] ?? ''));
            $partyName = trim((string) ($record['partai'] ?? ''));
            $candidateName = trim((string) ($record['nama'] ?? ''));
            $nomorUrut = $this->parseInt($record['nomor_urut'] ?? null);
            $suara = $this->parseInt($record['suara'] ?? null);

            if ($dapil === '' || $kecamatan === '' || $desa === '' || $partyName === '') {
                continue;
            }

            $key = $this->scopeKey($dapil, $kecamatan, $desa);
            $village = &$this->getVillage($villages, $key, $dapil, $kecamatan, $desa);
            $partyKey = $partyId !== '' ? $partyId : $this->normalizeKey($partyName);
            $isAggregate = $tps === 'TPS 000' || $nomorUrut === 0;

            if (! isset($village['party_map'][$partyKey])) {
                $village['party_map'][$partyKey] = $this->makePartyEntry($partyId, $partyName);
            }

            if ($isAggregate) {
                $village['party_map'][$partyKey]['party_votes'] += $suara;
                continue;
            }

            if (! isset($village['tps_map'][$tps])) {
                $village['tps_map'][$tps] = [
                    'label' => $tps,
                    'total_votes' => 0,
                    'party_map' => [],
                ];
            }

            if (! isset($village['tps_map'][$tps]['party_map'][$partyKey])) {
                $village['tps_map'][$tps]['party_map'][$partyKey] = $this->makePartyEntry($partyId, $partyName);
            }

            $candidateKey = $candidateName !== '' ? $candidateName : 'No.'.$nomorUrut;

            $village['party_map'][$partyKey]['candidate_votes'] += $suara;
            $village['party_map'][$partyKey]['candidates'][$candidateKey] = ($village['party_map'][$partyKey]['candidates'][$candidateKey] ?? 0) + $suara;

            $village['tps_map'][$tps]['party_map'][$partyKey]['candidate_votes'] += $suara;
            $village['tps_map'][$tps]['party_map'][$partyKey]['candidates'][$candidateKey] = ($village['tps_map'][$tps]['party_map'][$partyKey]['candidates'][$candidateKey] ?? 0) + $suara;
            $village['tps_map'][$tps]['total_votes'] += $suara;
        }

        fclose($handle);
    }

    /**
     * @param list<string> $files
     * @param array<string, array<string, mixed>> $villages
     */
    private function readDptFiles(array $files, array &$villages, ?callable $progress = null): void
    {
        foreach ($files as $file) {
            if ($progress !== null) {
                $progress('Baca DPT file: '.basename($file));
            }
            $handle = fopen($file, 'rb');

            if ($handle === false) {
                if ($progress !== null) {
                    $progress('Lewati file DPT yang gagal dibuka: '.basename($file));
                }
                continue;
            }

            $headers = fgetcsv($handle) ?: [];
            $processedRows = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $processedRows++;
                $record = $this->combineRow($headers, $row);
                $dapil = $this->resolveLatestDapil($record['dapil'] ?? null, $record['kecamatan'] ?? null);
                $kecamatan = $this->normalizeRegion($record['kecamatan'] ?? null);
                $desa = $this->normalizeRegion($record['desa'] ?? null);
                $tps = $this->normalizeTps($record['tps'] ?? null);
                $rw = $this->normalizeNumber($record['rw'] ?? null);
                $rt = $this->normalizeNumber($record['rt'] ?? null);
                $usia = $this->parseInt($record['usia'] ?? null);
                $dptLk = $this->parseInt($record['dpt_lk'] ?? null);
                $dptPr = $this->parseInt($record['dpt_pr'] ?? null);
                $dptTot = max(1, $this->parseInt($record['dpt_tot'] ?? null));

                if ($dapil === '' || $kecamatan === '' || $desa === '') {
                    continue;
                }

                $key = $this->scopeKey($dapil, $kecamatan, $desa);
                $village = &$this->getVillage($villages, $key, $dapil, $kecamatan, $desa);
                $village['total_dpt']++;
                $village['total_laki_float'] += $dptLk / $dptTot;
                $village['total_perempuan_float'] += $dptPr / $dptTot;
                $this->incrementAgeBucket($village, $usia);

                if ($tps !== '') {
                    $village['tps_dpt_count'][$tps] = ($village['tps_dpt_count'][$tps] ?? 0) + 1;
                }

                if ($rw !== '000') {
                    if (! isset($village['rw_map'][$rw])) {
                        $village['rw_map'][$rw] = $this->makeAreaEntry('rw', $dapil, $kecamatan, $desa, $rw, null);
                    }

                    $village['rw_map'][$rw]['total_dpt']++;
                    $village['rw_map'][$rw]['total_laki_float'] += $dptLk / $dptTot;
                    $village['rw_map'][$rw]['total_perempuan_float'] += $dptPr / $dptTot;
                    $this->incrementAgeBucket($village['rw_map'][$rw], $usia);

                    if ($tps !== '') {
                        $village['rw_map'][$rw]['tps_set'][$tps] = true;
                        $village['tps_rw_count'][$tps.'|'.$rw] = ($village['tps_rw_count'][$tps.'|'.$rw] ?? 0) + 1;
                    }
                }

                if ($rw !== '000' && $rt !== '000') {
                    $rtKey = $rw.'|'.$rt;
                    if (! isset($village['rt_map'][$rtKey])) {
                        $village['rt_map'][$rtKey] = $this->makeAreaEntry('rt', $dapil, $kecamatan, $desa, $rw, $rt);
                    }

                    $village['rt_map'][$rtKey]['total_dpt']++;
                    $village['rt_map'][$rtKey]['total_laki_float'] += $dptLk / $dptTot;
                    $village['rt_map'][$rtKey]['total_perempuan_float'] += $dptPr / $dptTot;
                    $this->incrementAgeBucket($village['rt_map'][$rtKey], $usia);

                    if ($tps !== '') {
                        $village['rt_map'][$rtKey]['tps_set'][$tps] = true;
                        $village['tps_rt_count'][$tps.'|'.$rtKey] = ($village['tps_rt_count'][$tps.'|'.$rtKey] ?? 0) + 1;
                    }
                }

                if ($progress !== null && $processedRows % 50000 === 0) {
                    $progress('Progress DPT '.basename($file).': '.number_format($processedRows).' baris');
                }
            }

            fclose($handle);
            if ($progress !== null) {
                $progress('Selesai DPT file: '.basename($file).' ('.number_format($processedRows).' baris)');
            }
        }
    }

    /**
     * @param array<string, mixed> $summary
     * @return array<string, mixed>
     */
    private function buildVillageSummaryPayload(array $summary): array
    {
        $partyRows = $this->finalizePartyRows($summary['party_map']);
        $pksCandidates = $this->extractPksCandidatesFromPartyMap($summary['party_map']);
        $analytics = $this->analyzePks($partyRows, $pksCandidates);
        $tpsRows = $this->buildTpsRows($summary['tps_map']);
        [$rwRows, $rtRows, $matchedTps, $missingTps] = $this->buildEstimatedAreaRows($summary, $summary['tps_map']);

        return [
            'dapil' => $summary['dapil'],
            'kecamatan' => $summary['kecamatan'],
            'desa' => $summary['desa'],
            'scope_key' => $summary['scope_key'],
            'total_dpt' => (int) $summary['total_dpt'],
            'total_laki' => (int) round((float) $summary['total_laki_float']),
            'total_perempuan' => (int) round((float) $summary['total_perempuan_float']),
            'gen_z' => (int) $summary['gen_z'],
            'millennial' => (int) $summary['millennial'],
            'gen_x' => (int) $summary['gen_x'],
            'boomer' => (int) $summary['boomer'],
            'age_unknown' => (int) $summary['age_unknown'],
            'total_tps' => count($summary['tps_map']),
            'total_rw' => count($summary['rw_map']),
            'total_rt' => count($summary['rt_map']),
            'total_votes' => (int) $analytics['total_votes'],
            'pks_votes' => (int) $analytics['pks_votes'],
            'pks_party_votes' => (int) $analytics['pks_party_votes'],
            'pks_candidate_votes' => (int) $analytics['pks_candidate_votes'],
            'pks_share' => $analytics['share'],
            'pks_rank' => (int) $analytics['rank'],
            'pks_gap_share' => $analytics['gap_share'],
            'status_wilayah' => $analytics['status'],
            'estimated_seats' => $this->estimateSeats((int) $analytics['pks_votes'], (int) $analytics['total_votes']),
            'party_rows' => $partyRows,
            'top_candidates' => $pksCandidates,
            'tps_rows' => $tpsRows,
            'rw_rows' => $rwRows,
            'rt_rows' => $rtRows,
            'meta' => [
                'total_scope_tps' => count($summary['tps_dpt_count']),
                'matched_tps' => $matchedTps,
                'missing_tps' => $missingTps,
            ],
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $tpsMap
     * @return list<array<string, mixed>>
     */
    private function buildTpsRows(array $tpsMap): array
    {
        $rows = [];

        foreach ($tpsMap as $label => $tps) {
            $partyRows = $this->finalizePartyRows($tps['party_map']);
            $analytics = $this->analyzePks($partyRows, $this->extractPksCandidatesFromPartyMap($tps['party_map']));
            $rows[] = [
                'label' => $label,
                'total_votes' => (int) $analytics['total_votes'],
                'pks_votes' => (int) $analytics['pks_votes'],
                'share' => $analytics['share'],
                'rank' => (int) $analytics['rank'],
                'status' => $analytics['status'],
            ];
        }

        usort($rows, fn (array $left, array $right): int => strnatcasecmp($left['label'], $right['label']));

        return $rows;
    }

    /**
     * @param array<string, mixed> $summary
     * @param array<string, array<string, mixed>> $tpsMap
     * @return array{0:list<array<string,mixed>>,1:list<array<string,mixed>>,2:int,3:int}
     */
    private function buildEstimatedAreaRows(array $summary, array $tpsMap): array
    {
        $matchedTps = 0;
        $missingTps = 0;

        foreach ($summary['tps_dpt_count'] as $tps => $totalDptTps) {
            if (! isset($tpsMap[$tps])) {
                $missingTps++;
                continue;
            }

            $matchedTps++;
            $partyMap = $tpsMap[$tps]['party_map'];

            foreach ($summary['rw_map'] as $rw => &$row) {
                $count = (int) ($summary['tps_rw_count'][$tps.'|'.$rw] ?? 0);
                if ($count <= 0 || $totalDptTps <= 0) {
                    continue;
                }

                $this->addScaledPartyMap($row['party_map'], $partyMap, $count / $totalDptTps);
            }
            unset($row);

            foreach ($summary['rt_map'] as $rtKey => &$row) {
                $count = (int) ($summary['tps_rt_count'][$tps.'|'.$rtKey] ?? 0);
                if ($count <= 0 || $totalDptTps <= 0) {
                    continue;
                }

                $this->addScaledPartyMap($row['party_map'], $partyMap, $count / $totalDptTps);
            }
            unset($row);
        }

        return [
            $this->finalizeAreaRows($summary['rw_map']),
            $this->finalizeAreaRows($summary['rt_map']),
            $matchedTps,
            $missingTps,
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private function finalizeAreaRows(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $partyRows = $this->finalizePartyRows($row['party_map']);
            $analytics = $this->analyzePks($partyRows, $this->extractPksCandidatesFromPartyMap($row['party_map']));
            $result[] = [
                'key' => $row['key'],
                'type' => $row['type'],
                'village' => $row['desa'],
                'district' => $row['kecamatan'],
                'rw' => $row['rw'],
                'rt' => $row['rt'],
                'total_dpt' => (int) $row['total_dpt'],
                'male' => (int) round((float) $row['total_laki_float']),
                'female' => (int) round((float) $row['total_perempuan_float']),
                'gen_z' => (int) $row['gen_z'],
                'millennial' => (int) $row['millennial'],
                'gen_x' => (int) $row['gen_x'],
                'boomer' => (int) $row['boomer'],
                'age_unknown' => (int) $row['age_unknown'],
                'tps_count' => count($row['tps_set']),
                'pks_votes' => (int) round((float) $analytics['pks_votes']),
                'share' => $analytics['share'],
                'rank' => (int) $analytics['rank'],
                'status' => $analytics['status'],
                'top_candidate' => $analytics['pks_candidates'][0] ?? null,
            ];
        }

        usort($result, function (array $left, array $right): int {
            $voteOrder = $right['pks_votes'] <=> $left['pks_votes'];

            if ($voteOrder !== 0) {
                return $voteOrder;
            }

            return strnatcasecmp($left['key'], $right['key']);
        });

        return $result;
    }

    /**
     * @param array<string, array<string, mixed>> $targetMap
     * @param array<string, array<string, mixed>> $sourceMap
     */
    private function addScaledPartyMap(array &$targetMap, array $sourceMap, float $factor): void
    {
        foreach ($sourceMap as $key => $entry) {
            if (! isset($targetMap[$key])) {
                $targetMap[$key] = $this->makePartyEntry((string) $entry['party_id'], (string) $entry['party_name']);
            }

            $targetMap[$key]['party_votes'] += (float) $entry['party_votes'] * $factor;
            $targetMap[$key]['candidate_votes'] += (float) $entry['candidate_votes'] * $factor;

            foreach ($entry['candidates'] as $name => $votes) {
                $targetMap[$key]['candidates'][$name] = ($targetMap[$key]['candidates'][$name] ?? 0) + ((float) $votes * $factor);
            }
        }
    }

    /**
     * @param array<string, array<string, mixed>> $partyMap
     * @return list<array<string, mixed>>
     */
    private function finalizePartyRows(array $partyMap): array
    {
        $rows = [];
        $totalVotes = 0.0;

        foreach ($partyMap as $entry) {
            $total = (float) $entry['party_votes'] + (float) $entry['candidate_votes'];
            $rows[] = [
                'party_id' => (string) $entry['party_id'],
                'party_name' => (string) $entry['party_name'],
                'party_votes' => (int) round((float) $entry['party_votes']),
                'candidate_votes' => (int) round((float) $entry['candidate_votes']),
                'total_votes' => (int) round($total),
            ];
            $totalVotes += $total;
        }

        usort($rows, fn (array $left, array $right): int => $right['total_votes'] <=> $left['total_votes']);

        foreach ($rows as &$row) {
            $row['share'] = $totalVotes > 0 ? round($row['total_votes'] / $totalVotes, 6) : 0.0;
        }
        unset($row);

        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $partyRows
     * @param list<array{name:string,votes:int}> $pksCandidates
     * @return array{total_votes:int,pks_votes:int,pks_party_votes:int,pks_candidate_votes:int,share:float,rank:int,gap_share:float,status:string,pks_candidates:list<array{name:string,votes:int}>}
     */
    private function analyzePks(array $partyRows, array $pksCandidates = []): array
    {
        $totalVotes = array_sum(array_map(static fn (array $row): int => (int) $row['total_votes'], $partyRows));
        $pksRow = null;
        $rank = count($partyRows) + 1;

        foreach ($partyRows as $index => $row) {
            $partyName = $this->normalizeKey($row['party_name']);
            if ((string) $row['party_id'] === self::PKS_PARTY_ID || $partyName === self::PKS_PARTY_NAME) {
                $pksRow = $row;
                $rank = $index + 1;
                break;
            }
        }

        $leaderShare = (float) ($partyRows[0]['share'] ?? 0);
        $secondShare = (float) ($partyRows[1]['share'] ?? 0);
        $pksShare = (float) ($pksRow['share'] ?? 0);
        $gapShare = $rank === 1 ? max(0.0, $pksShare - $secondShare) : max(0.0, $leaderShare - $pksShare);
        $status = $this->classifyPriority((int) ($pksRow['total_votes'] ?? 0), $pksShare, $rank, $gapShare);

        return [
            'total_votes' => $totalVotes,
            'pks_votes' => (int) ($pksRow['total_votes'] ?? 0),
            'pks_party_votes' => (int) ($pksRow['party_votes'] ?? 0),
            'pks_candidate_votes' => (int) ($pksRow['candidate_votes'] ?? 0),
            'share' => $pksShare,
            'rank' => $rank,
            'gap_share' => round($gapShare, 6),
            'status' => $status,
            'pks_candidates' => $pksCandidates,
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $partyMap
     * @return list<array{name:string,votes:int}>
     */
    private function extractPksCandidatesFromPartyMap(array $partyMap): array
    {
        foreach ($partyMap as $entry) {
            $partyName = $this->normalizeKey($entry['party_name'] ?? '');
            if (($entry['party_id'] ?? '') !== self::PKS_PARTY_ID && $partyName !== self::PKS_PARTY_NAME) {
                continue;
            }

            $rows = [];
            foreach (($entry['candidates'] ?? []) as $name => $votes) {
                $rows[] = [
                    'name' => (string) $name,
                    'votes' => (int) round((float) $votes),
                ];
            }

            usort($rows, fn (array $left, array $right): int => $right['votes'] <=> $left['votes']);

            return $rows;
        }

        return [];
    }

    private function classifyPriority(int $pksVotes, float $share, int $rank, float $gapShare): string
    {
        if ($pksVotes <= 0) {
            return 'ZONA BERAT';
        }

        if ($rank === 1 && $share >= 0.30) {
            return 'JAGA KUAT';
        }

        if ($rank === 1) {
            return 'AMANKAN';
        }

        if ($rank === 2 && $gapShare <= 0.05) {
            return 'REBUT REALISTIS';
        }

        if ($rank <= 3 || $share >= 0.12) {
            return 'GARAP INTENSIF';
        }

        return 'ZONA BERAT';
    }

    private function estimateSeats(int $pksVotes, int $totalVotes): int
    {
        if ($totalVotes <= 0) {
            return 0;
        }

        return (int) round(($pksVotes / $totalVotes) * self::TOTAL_DPRD_SEATS);
    }

    /**
     * @param array<string, mixed> $target
     */
    private function incrementAgeBucket(array &$target, int $age): void
    {
        if ($age <= 0) {
            $target['age_unknown']++;

            return;
        }

        if ($age <= 27) {
            $target['gen_z']++;
        } elseif ($age <= 43) {
            $target['millennial']++;
        } elseif ($age <= 59) {
            $target['gen_x']++;
        } else {
            $target['boomer']++;
        }
    }

    /**
     * @param array<string, array<string, mixed>> $villages
     * @return array<string, mixed>
     */
    private function &getVillage(array &$villages, string $key, string $dapil, string $kecamatan, string $desa): array
    {
        if (! isset($villages[$key])) {
            $villages[$key] = [
                'scope_key' => $key,
                'dapil' => $dapil,
                'kecamatan' => $kecamatan,
                'desa' => $desa,
                'party_map' => [],
                'tps_map' => [],
                'rw_map' => [],
                'rt_map' => [],
                'tps_dpt_count' => [],
                'tps_rw_count' => [],
                'tps_rt_count' => [],
                'total_dpt' => 0,
                'total_laki_float' => 0.0,
                'total_perempuan_float' => 0.0,
                'gen_z' => 0,
                'millennial' => 0,
                'gen_x' => 0,
                'boomer' => 0,
                'age_unknown' => 0,
            ];
        }

        return $villages[$key];
    }

    /**
     * @return array<string, mixed>
     */
    private function makeAreaEntry(string $type, string $dapil, string $kecamatan, string $desa, string $rw, ?string $rt): array
    {
        $suffix = $type === 'rw' ? $rw : $rw.'|'.$rt;

        return [
            'key' => $this->scopeKey($dapil, $kecamatan, $desa).'|'.$type.'|'.$suffix,
            'type' => $type,
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'rw' => $rw,
            'rt' => $rt,
            'total_dpt' => 0,
            'total_laki_float' => 0.0,
            'total_perempuan_float' => 0.0,
            'gen_z' => 0,
            'millennial' => 0,
            'gen_x' => 0,
            'boomer' => 0,
            'age_unknown' => 0,
            'tps_set' => [],
            'party_map' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function makePartyEntry(string $partyId, string $partyName): array
    {
        return [
            'party_id' => $partyId,
            'party_name' => $partyName,
            'party_votes' => 0.0,
            'candidate_votes' => 0.0,
            'candidates' => [],
        ];
    }

    /**
     * @param list<string> $headers
     * @param list<string|null> $row
     * @return array<string, string|null>
     */
    private function combineRow(array $headers, array $row): array
    {
        $record = [];

        foreach ($headers as $index => $header) {
            $record[(string) $header] = $row[$index] ?? null;
        }

        return $record;
    }

    private function scopeKey(string $dapil, string $kecamatan, string $desa): string
    {
        return implode('|', [$dapil, $this->normalizeKey($kecamatan), $this->normalizeKey($desa)]);
    }

    private function resolveLatestDapil(mixed $rawDapil, mixed $rawKecamatan): string
    {
        $district = $this->normalizeKey((string) $rawKecamatan);
        if ($district === 'CIKARANG SELATAN') {
            return 'BEKASI 1';
        }

        $normalized = $this->normalizeKey((string) $rawDapil);
        if (preg_match('/BEKASI\s*([1-7])/', $normalized, $matches) === 1) {
            return 'BEKASI '.$matches[1];
        }

        return $normalized;
    }

    private function normalizeRegion(mixed $value): string
    {
        $text = strtoupper(trim((string) ($value ?? '')));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $compact = str_replace(' ', '', $text);
        $mapped = $this->normalizeMap[$text] ?? $this->normalizeMap[$compact] ?? $text;

        return preg_replace('/\s+/', ' ', trim($mapped)) ?? $mapped;
    }

    private function normalizeTps(mixed $value): string
    {
        $text = strtoupper(trim((string) ($value ?? '')));

        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }

    private function normalizeKey(mixed $value): string
    {
        $text = $this->normalizeRegion($value);
        $text = preg_replace('/[^A-Z0-9 ]+/', ' ', $text) ?? $text;

        return preg_replace('/\s+/', ' ', trim($text)) ?? $text;
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
}
