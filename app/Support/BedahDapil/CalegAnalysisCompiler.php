<?php

declare(strict_types=1);

namespace App\Support\BedahDapil;

use Illuminate\Support\Str;
use RuntimeException;

class CalegAnalysisCompiler
{
    private const SEATS_PER_DAPIL = 8; // Standard from client-side UI

    /**
     * Compile candidate analysis data from tps_dprd.csv into a unified payload array.
     *
     * @return array<string, mixed>
     */
    public function compile(string $csvPath): array
    {
        $handle = fopen($csvPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Gagal membuka file TPS: {$csvPath}");
        }

        $headers = fgetcsv($handle, 0, ';') ?: [];
        $headers = array_map(fn ($h) => $this->normalizeHeader($h), $headers);

        $dapils = [];
        $allPartyNames = [];
        $totalRows = 0;
        $seenTps = [];

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $record = $this->combineRow($headers, $row);

            $nomorUrut = (int) ($record['nomor_urut'] ?? 0);
            if ($nomorUrut <= 0) {
                continue;
            }

            $dapil = $this->normalizeKey($record['dapil'] ?? '');
            $partaiId = trim((string) ($record['partai_id'] ?? ''));
            $partai = $this->normalizePartyName($record['partai'] ?? '');
            $nama = trim((string) ($record['nama'] ?? ''));
            $gender = strtoupper(trim((string) ($record['gender'] ?? '')));
            $kecamatan = $this->normalizeKey($record['kecamatan'] ?? '');
            $desa = $this->normalizeKey($record['desa'] ?? '');
            $suara = (int) ($record['suara'] ?? 0);
            $tps = trim((string) ($record['tps'] ?? ''));

            if ($dapil === '' || $partai === '') {
                continue;
            }

            $totalRows++;
            $allPartyNames[$partai] = true;

            $calegKey = "{$dapil}__{$partaiId}__{$nomorUrut}__".$this->normalizeKey($nama);
            $villageKey = "{$dapil}__{$kecamatan}__{$desa}";

            if ($tps !== '') {
                $seenTps[$calegKey][$tps] = true;
            }

            if (! isset($dapils[$dapil])) {
                $dapils[$dapil] = [
                    'dapil' => $dapil,
                    'calegMap' => [],
                    'partyMap' => [],
                    'villagePartyMap' => [],
                ];
            }

            // Caleg
            if (! isset($dapils[$dapil]['calegMap'][$calegKey])) {
                $dapils[$dapil]['calegMap'][$calegKey] = [
                    'key' => $calegKey,
                    'nama' => $nama,
                    'partaiId' => $partaiId,
                    'partai' => $partai,
                    'nomorUrut' => $nomorUrut,
                    'gender' => $gender,
                    'totalSuara' => 0,
                    'kecamatanMap' => [],
                    'desaMap' => [],
                    'tpsCount' => 0,
                ];
            }
            $dapils[$dapil]['calegMap'][$calegKey]['totalSuara'] += $suara;

            // Caleg Kecamatan
            if ($suara > 0) {
                if (! isset($dapils[$dapil]['calegMap'][$calegKey]['kecamatanMap'][$kecamatan])) {
                    $dapils[$dapil]['calegMap'][$calegKey]['kecamatanMap'][$kecamatan] = 0;
                }
                $dapils[$dapil]['calegMap'][$calegKey]['kecamatanMap'][$kecamatan] += $suara;

                // Caleg Desa
                $villageShortKey = "{$kecamatan}__{$desa}";
                if (! isset($dapils[$dapil]['calegMap'][$calegKey]['desaMap'][$villageShortKey])) {
                    $dapils[$dapil]['calegMap'][$calegKey]['desaMap'][$villageShortKey] = 0;
                }
                $dapils[$dapil]['calegMap'][$calegKey]['desaMap'][$villageShortKey] += $suara;
            }

            // Party Map
            $partyKey = $partaiId !== '' ? $partaiId : $partai;
            if (! isset($dapils[$dapil]['partyMap'][$partyKey])) {
                $dapils[$dapil]['partyMap'][$partyKey] = [
                    'partaiId' => $partyKey,
                    'partai' => $partai,
                    'totalSuaraCaleg' => 0,
                    'lakilaki' => 0,
                    'perempuan' => 0,
                    'seenCalegs' => [],
                ];
            }
            $dapils[$dapil]['partyMap'][$partyKey]['totalSuaraCaleg'] += $suara;
            if (! isset($dapils[$dapil]['partyMap'][$partyKey]['seenCalegs'][$calegKey])) {
                $dapils[$dapil]['partyMap'][$partyKey]['seenCalegs'][$calegKey] = true;
                if ($gender === 'L') {
                    $dapils[$dapil]['partyMap'][$partyKey]['lakilaki']++;
                } elseif ($gender === 'P') {
                    $dapils[$dapil]['partyMap'][$partyKey]['perempuan']++;
                }
            }

            // Village Party Map
            if (! isset($dapils[$dapil]['villagePartyMap'][$villageKey])) {
                $dapils[$dapil]['villagePartyMap'][$villageKey] = [
                    'desa' => $this->toTitleCase($desa),
                    'kecamatan' => $this->toTitleCase($kecamatan),
                    'partyTotals' => [],
                ];
            }
            if (! isset($dapils[$dapil]['villagePartyMap'][$villageKey]['partyTotals'][$partai])) {
                $dapils[$dapil]['villagePartyMap'][$villageKey]['partyTotals'][$partai] = 0;
            }
            $dapils[$dapil]['villagePartyMap'][$villageKey]['partyTotals'][$partai] += $suara;
        }
        fclose($handle);

        // Process and compile
        $compiledDapils = [];
        foreach ($dapils as $dapil => $dapilData) {
            $allCalegs = array_values($dapilData['calegMap']);
            usort($allCalegs, fn ($a, $b) => $b['totalSuara'] <=> $a['totalSuara']);

            $totalSuaraDapil = array_sum(array_column($allCalegs, 'totalSuara'));

            // Set rank and tpsCount for all calegs in dapil
            foreach ($allCalegs as $index => &$c) {
                $c['rank'] = $index + 1;
                $c['tpsCount'] = isset($seenTps[$c['key']]) ? count($seenTps[$c['key']]) : 0;
            }
            unset($c);

            // Re-map calegMap after sorting
            $sortedCalegMap = [];
            foreach ($allCalegs as $c) {
                $sortedCalegMap[$c['key']] = $c;
            }

            $parties = [];
            foreach ($dapilData['partyMap'] as $pKey => $pData) {
                $totalPartyVotes = $pData['totalSuaraCaleg'];
                $estimatedSeats = $this->estimatePartySeats($totalPartyVotes, $totalSuaraDapil, self::SEATS_PER_DAPIL);

                // Re-sort party calegs to assign partyRank and isElectedEstimate
                $partyCalegs = array_filter($allCalegs, fn ($c) => $c['partaiId'] === $pData['partaiId']);
                usort($partyCalegs, fn ($a, $b) => $b['totalSuara'] <=> $a['totalSuara']);

                foreach ($partyCalegs as $index => $c) {
                    $key = $c['key'];
                    $sortedCalegMap[$key]['partyRank'] = $index + 1;
                    $sortedCalegMap[$key]['partyTotalSuara'] = $totalPartyVotes;
                    $sortedCalegMap[$key]['isElectedEstimate'] = $estimatedSeats > 0 && $index < $estimatedSeats;
                }

                unset($pData['seenCalegs']);
                $parties[] = $pData;
            }

            // Convert villagePartyMap Map into list
            $villageParties = array_values($dapilData['villagePartyMap']);

            $compiledDapils[] = [
                'dapil' => $dapil,
                'totalSuara' => $totalSuaraDapil,
                'calegs' => array_values($sortedCalegMap),
                'parties' => $parties,
                'villageParties' => $villageParties,
            ];
        }

        return [
            'totalRows' => $totalRows,
            'allPartyNames' => array_keys($allPartyNames),
            'dapils' => $compiledDapils,
        ];
    }

    private function normalizeHeader(string $header): string
    {
        return str_replace(' ', '_', strtolower(trim($header)));
    }

    private function normalizeKey(string $value): string
    {
        $text = strtoupper(trim($value));

        return preg_replace('/[^A-Z0-9]+/u', ' ', $text);
    }

    private function normalizePartyName(string $name): string
    {
        $name = trim($name);
        if ($name === 'NasDem') {
            return 'Nasdem';
        }

        return $name;
    }

    private function toTitleCase(string $text): string
    {
        return Str::title(strtolower(trim($text)));
    }

    private function combineRow(array $headers, array $row): array
    {
        $record = [];
        foreach ($headers as $index => $header) {
            $record[$header] = $row[$index] ?? null;
        }

        return $record;
    }

    private function estimatePartySeats(int $partyVotes, int $totalVotes, int $seatCount): int
    {
        if ($partyVotes === 0 || $totalVotes === 0) {
            return 0;
        }
        $seatEstimate = (int) round(($partyVotes / $totalVotes) * $seatCount);
        if ($seatEstimate <= 0 && ($partyVotes / $totalVotes) >= 0.08) {
            return 1;
        }

        return max(0, $seatEstimate);
    }
}
