<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataRw;
use App\Models\PemiluPeriod;
use Illuminate\Support\Str;

class CompileCalegPayload extends Command
{
    protected $signature = 'pemilu:compile-caleg-payload {period=fallback-2024}';
    protected $description = 'Compile caleg summary payload including RW map from CSV';

    public function handle()
    {
        ini_set('memory_limit', '2G');
        
        $periodId = $this->argument('period');
        $period = PemiluPeriod::find($periodId);
        if (!$period) {
            $this->error("Period $periodId not found.");
            return;
        }

        $csvPath = public_path('data/pemilu/tps_dprd.csv');
        if (!file_exists($csvPath)) {
            $this->error("CSV file not found: $csvPath");
            return;
        }

        $this->info("Loading TPS to RW mapping from pemilu_desa_summaries...");
        $summaries = \Illuminate\Support\Facades\DB::table('pemilu_desa_summaries')
            ->where('pemilu_period_id', $period->id)
            ->get();
            
        $tpsToRwMap = [];
        foreach ($summaries as $summary) {
            $dapil = $this->normalizeKey($summary->dapil);
            $kec = $this->normalizeKey($summary->kecamatan);
            $desa = $this->normalizeKey($summary->desa);
            $villageKey = "{$dapil}__{$kec}__{$desa}";
            
            if (!isset($tpsToRwMap[$villageKey])) {
                $tpsToRwMap[$villageKey] = [];
            }
            
            $rwRows = json_decode($summary->rw_rows, true) ?: [];
            foreach ($rwRows as $rwRow) {
                foreach ($rwRow['tps_list'] ?? [] as $tpsName) {
                    $num = preg_replace('/[^\d]/', '', (string) $tpsName);
                    if ($num !== '') {
                        $tpsToRwMap[$villageKey][$num] = ltrim((string)$rwRow['rw'], '0');
                    }
                }
            }
        }

        $this->info("Parsing CSV...");
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->error("Cannot open CSV file.");
            return;
        }

        $headers = fgetcsv($handle, 0, ';');
        if (!$headers) {
            return;
        }
        $headers = array_map(fn($h) => str_replace(' ', '_', strtolower(trim($h))), $headers);

        $dataset = [
            'dapils' => [],
            'totalRows' => 0,
            'allPartyNames' => [],
        ];
        
        $dapilsMap = [];

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) !== count($headers)) continue;
            $data = array_combine($headers, $row);

            $nomorUrut = (int) ($data['nomor_urut'] ?? 0);
            if ($nomorUrut <= 0) continue;

            $dapil = $this->normalizeKey($data['dapil'] ?? '');
            $partaiId = trim($data['partai_id'] ?? '');
            $partai = $this->normalizePartyName($data['partai'] ?? '');
            $nama = trim($data['nama'] ?? '');
            $gender = strtoupper(trim($data['gender'] ?? ''));
            $kecamatan = $this->normalizeKey($data['kecamatan'] ?? '');
            $desa = $this->normalizeKey($data['desa'] ?? '');
            $suara = (int) ($data['suara'] ?? 0);
            $tpsRaw = trim($data['tps'] ?? '');
            $tpsDigits = preg_replace('/[^\d]/', '', $tpsRaw);

            $calegKey = "{$dapil}__{$partaiId}__{$nomorUrut}__" . $this->normalizeKey($nama);
            $villageKey = "{$dapil}__{$kecamatan}__{$desa}";

            if (!isset($dapilsMap[$dapil])) {
                $dapilsMap[$dapil] = [
                    'dapil' => $dapil,
                    'calegMap' => [],
                    'partyMap' => [],
                    'villagePartyMap' => [],
                    'rwPartyMap' => [],
                ];
            }
            $dapilObj = &$dapilsMap[$dapil];

            $partyKey = $partaiId !== '' ? $partaiId : $partai;
            if (!isset($dapilObj['partyMap'][$partyKey])) {
                $dapilObj['partyMap'][$partyKey] = [
                    'partaiId' => $partyKey,
                    'partai' => $partai,
                    'totalSuaraCaleg' => 0,
                    'calegMap' => [],
                    'lakilaki' => 0,
                    'perempuan' => 0,
                ];
            }
            $partyObj = &$dapilObj['partyMap'][$partyKey];

            if (!isset($dapilObj['calegMap'][$calegKey])) {
                $entry = [
                    'key' => $calegKey,
                    'nama' => $nama,
                    'partaiId' => $partyKey,
                    'partai' => $partai,
                    'nomorUrut' => $nomorUrut,
                    'gender' => $gender,
                    'dapil' => $dapil,
                    'totalSuara' => 0,
                    'desaMap' => [],
                    'kecamatanMap' => [],
                    'rwMap' => [],
                    'tpsSet' => [], // will be array of unique values
                    'partyTotalSuara' => 0,
                    'partyRank' => 0,
                    'rank' => 0,
                    'isElectedEstimate' => false,
                ];
                $dapilObj['calegMap'][$calegKey] = $entry;
                
                if ($gender === 'L') $partyObj['lakilaki']++;
                if ($gender === 'P') $partyObj['perempuan']++;
            }
            $calegObj = &$dapilObj['calegMap'][$calegKey];

            $dataset['totalRows']++;
            $dataset['allPartyNames'][$partai] = true;

            $calegObj['totalSuara'] += $suara;
            $tpsUniqueKey = "{$villageKey}__{$tpsRaw}";
            $calegObj['tpsSet'][$tpsUniqueKey] = true;

            // desaMap
            if (!isset($calegObj['desaMap'][$villageKey])) {
                $calegObj['desaMap'][$villageKey] = ['desa' => $this->toTitleCase($desa), 'kecamatan' => $this->toTitleCase($kecamatan), 'suara' => 0];
            }
            $calegObj['desaMap'][$villageKey]['suara'] += $suara;

            // kecamatanMap
            if (!isset($calegObj['kecamatanMap'][$kecamatan])) {
                $calegObj['kecamatanMap'][$kecamatan] = ['kecamatan' => $this->toTitleCase($kecamatan), 'suara' => 0];
            }
            $calegObj['kecamatanMap'][$kecamatan]['suara'] += $suara;

            // rw mapping
            $rwNum = $tpsToRwMap[$villageKey][$tpsDigits] ?? null;
            if ($rwNum) {
                $rwKey = "{$villageKey}__{$rwNum}";
                if (!isset($calegObj['rwMap'][$rwKey])) {
                    $calegObj['rwMap'][$rwKey] = ['rw' => $rwNum, 'desa' => $this->toTitleCase($desa), 'kecamatan' => $this->toTitleCase($kecamatan), 'suara' => 0];
                }
                $calegObj['rwMap'][$rwKey]['suara'] += $suara;

                if (!isset($dapilObj['rwPartyMap'][$rwKey])) {
                    $dapilObj['rwPartyMap'][$rwKey] = [
                        'rw' => $rwNum,
                        'desa' => $this->toTitleCase($desa),
                        'kecamatan' => $this->toTitleCase($kecamatan),
                        'partyTotals' => [],
                    ];
                }
                if (!isset($dapilObj['rwPartyMap'][$rwKey]['partyTotals'][$partai])) {
                    $dapilObj['rwPartyMap'][$rwKey]['partyTotals'][$partai] = ['partai' => $partai, 'suara' => 0];
                }
                $dapilObj['rwPartyMap'][$rwKey]['partyTotals'][$partai]['suara'] += $suara;
            }

            $partyObj['totalSuaraCaleg'] += $suara;

            if (!isset($dapilObj['villagePartyMap'][$villageKey])) {
                $dapilObj['villagePartyMap'][$villageKey] = [
                    'desa' => $this->toTitleCase($desa),
                    'kecamatan' => $this->toTitleCase($kecamatan),
                    'partyTotals' => [],
                ];
            }
            if (!isset($dapilObj['villagePartyMap'][$villageKey]['partyTotals'][$partai])) {
                $dapilObj['villagePartyMap'][$villageKey]['partyTotals'][$partai] = ['partai' => $partai, 'suara' => 0];
            }
            $dapilObj['villagePartyMap'][$villageKey]['partyTotals'][$partai]['suara'] += $suara;

            $rowCount++;
            if ($rowCount % 50000 === 0) {
                $this->info("Processed $rowCount rows...");
            }
        }
        fclose($handle);

        $this->info("Aggregating...");
        // Convert to JS-friendly structure (arrays instead of maps where JS expects arrays)
        $dataset['allPartyNames'] = array_keys($dataset['allPartyNames']);
        
        foreach ($dapilsMap as $dapil => &$dapilObj) {
            $calegsArray = array_values($dapilObj['calegMap']);
            usort($calegsArray, fn($a, $b) => $b['totalSuara'] <=> $a['totalSuara']);
            
            $totalSuaraDapil = 0;
            foreach ($calegsArray as $idx => &$caleg) {
                $caleg['rank'] = $idx + 1;
                $totalSuaraDapil += $caleg['totalSuara'];
            }
            unset($caleg);
            
            $dapilObj['totalSuara'] = $totalSuaraDapil;

            foreach ($dapilObj['partyMap'] as $partyKey => &$partyObj) {
                // Find calegs for this party
                $partyCalegs = array_filter($calegsArray, fn($c) => ($c['partaiId'] ?? $c['partai']) === $partyKey);
                usort($partyCalegs, fn($a, $b) => $b['totalSuara'] <=> $a['totalSuara']);
                
                $estimatedSeats = $this->estimatePartySeats($partyObj['totalSuaraCaleg'], $totalSuaraDapil, 8); // approximate
                
                $partyObj['calegCount'] = count($partyCalegs);
                
                // Update the main caleg object inside calegMap
                foreach (array_values($partyCalegs) as $idx => $pc) {
                    $actualCaleg = &$dapilObj['calegMap'][$pc['key']];
                    $actualCaleg['partyRank'] = $idx + 1;
                    $actualCaleg['partyTotalSuara'] = $partyObj['totalSuaraCaleg'];
                    $actualCaleg['isElectedEstimate'] = ($estimatedSeats > 0 && $idx < $estimatedSeats);
                }
            }
            unset($partyObj);

            // Structure to match JS payload
            $formattedDapil = [
                'dapil' => $dapil,
                'totalSuara' => $totalSuaraDapil,
                'calegs' => [],
                'parties' => [],
                'villagePartyMap' => $dapilObj['villagePartyMap'],
                'rwPartyMap' => $dapilObj['rwPartyMap'],
            ];

            foreach ($dapilObj['calegMap'] as $caleg) {
                // Convert Map structures to objects for JSON
                $caleg['desaMap'] = (object) $caleg['desaMap'];
                $caleg['kecamatanMap'] = (object) $caleg['kecamatanMap'];
                $caleg['rwMap'] = (object) $caleg['rwMap'];
                $caleg['tpsSet'] = array_keys($caleg['tpsSet']); // Convert keys back to array
                $formattedDapil['calegs'][] = $caleg;
            }

            foreach ($dapilObj['partyMap'] as $party) {
                // We don't need to put calegMap inside party in the JSON because hydrateCalegPayload reconnects them!
                $formattedDapil['parties'][] = [
                    'partaiId' => $party['partaiId'],
                    'partai' => $party['partai'],
                    'totalSuaraCaleg' => $party['totalSuaraCaleg'],
                    'lakilaki' => $party['lakilaki'],
                    'perempuan' => $party['perempuan'],
                    'calegCount' => $party['calegCount'],
                ];
            }

            $dataset['dapils'][] = $formattedDapil;
        }

        $this->info("Saving to database...");
        
        $json = json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $size = strlen($json);
        $this->info("JSON size: " . round($size / 1024 / 1024, 2) . " MB");
        
        file_put_contents(storage_path('app/private/caleg_payload.json'), $json);
        
        \Illuminate\Support\Facades\DB::reconnect();
        \Illuminate\Support\Facades\DB::statement('SET GLOBAL max_allowed_packet=1073741824');
        \Illuminate\Support\Facades\DB::table('pemilu_periods')
            ->where('id', $period->id)
            ->update(['caleg_summary_payload' => $json]);

        $this->info("Done! Payload rebuilt and saved for period: {$period->label}");
    }

    private function normalizeKey($value)
    {
        // Simple NFD normalization equivalent and non-alphanumeric replacement
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $value);
        $value = preg_replace('/[^a-zA-Z0-9]+/', ' ', $value);
        return trim(strtoupper($value));
    }

    private function normalizePartyName($name)
    {
        $name = trim(strtoupper($name));
        $map = [
            'PKS' => 'PKS',
            'PARTAI KEADILAN SEJAHTERA' => 'PKS',
            'PDIP' => 'PDIP',
            'PDI PERJUANGAN' => 'PDIP',
            'PARTAI DEMOKRASI INDONESIA PERJUANGAN' => 'PDIP',
            'GOLKAR' => 'Golkar',
            'PARTAI GOLONGAN KARYA' => 'Golkar',
            'GERINDRA' => 'Gerindra',
            'PARTAI GERAKAN INDONESIA RAYA' => 'Gerindra',
            'NASDEM' => 'Nasdem',
            'PARTAI NASDEM' => 'Nasdem',
            'PKB' => 'PKB',
            'PARTAI KEBANGKITAN BANGSA' => 'PKB',
            'DEMOKRAT' => 'Demokrat',
            'PARTAI DEMOKRAT' => 'Demokrat',
            'PAN' => 'PAN',
            'PARTAI AMANAT NASIONAL' => 'PAN',
            'PPP' => 'PPP',
            'PARTAI PERSATUAN PEMBANGUNAN' => 'PPP',
            'PSI' => 'PSI',
            'PARTAI SOLIDARITAS INDONESIA' => 'PSI',
            'BURUH' => 'Buruh',
            'PARTAI BURUH' => 'Buruh',
            'GELORA' => 'Gelora',
            'PARTAI GELOMBANG RAKYAT INDONESIA' => 'Gelora',
            'PERINDO' => 'Perindo',
            'PARTAI PERINDO' => 'Perindo',
            'UMMAT' => 'Ummat',
            'PARTAI UMMAT' => 'Ummat',
            'PBB' => 'PBB',
            'PARTAI BULAN BINTANG' => 'PBB',
            'PKN' => 'PKN',
            'PARTAI KEBANGKITAN NUSANTARA' => 'PKN',
            'HANURA' => 'Hanura',
            'PARTAI HATI NURANI RAKYAT' => 'Hanura',
            'GARUDA' => 'Garuda',
            'PARTAI GARDA PERUBAHAN INDONESIA' => 'Garuda',
        ];
        return $map[$name] ?? ucwords(strtolower($name));
    }

    private function toTitleCase($string)
    {
        return mb_convert_case(trim($string), MB_CASE_TITLE, "UTF-8");
    }

    private function estimatePartySeats($partyVotes, $totalDapilVotes, $totalSeats)
    {
        if ($totalDapilVotes <= 0 || $partyVotes <= 0) return 0;
        $approxSeatCost = $totalDapilVotes / $totalSeats;
        $seats = 0;
        $currentVotes = $partyVotes;
        for ($i = 0; $i < $totalSeats; $i++) {
            if ($currentVotes > $approxSeatCost * 0.7) {
                $seats++;
                $currentVotes = $currentVotes / 3; 
            } else {
                break;
            }
        }
        return $seats;
    }
}
