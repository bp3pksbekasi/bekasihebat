<?php

namespace App\Livewire\Public;

use App\Models\TargetWilayah;
use App\Models\DataRw;
use App\Models\ProfilRw;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PemiluDesaSummary;
use App\Models\PemiluPeriod;
use Livewire\Component;

class PilkadesKarangsatria extends Component
{
    public $targetWilayah;
    public $rwData = [];
    public int $desaPanVotes = 0;
    public string $electionLabel = '';

    // Modal State
    public bool $showAfiliasiModal = false;
    public ?string $editRwId = null;
    public ?string $formAfiliasi = null;
    public ?string $formCalonLain = null;
    public ?string $formKorweNama = null;

    public function mount()
    {
        $this->targetWilayah = TargetWilayah::where('desa', 'KARANGSATRIA')
            ->where('kecamatan', 'TAMBUN UTARA')
            ->first();

        if ($this->targetWilayah) {
            $this->loadData();
        }
    }

    private function loadData()
    {
        $dataRws = DataRw::where('target_wilayah_id', $this->targetWilayah->id)
            ->orderBy('nomor_rw')
            ->get();

        $profilRws = ProfilRw::where('target_wilayah_id', $this->targetWilayah->id)
            ->get()
            ->keyBy(function ($item) {
                return ltrim((string) $item->nomor_rw, '0');
            });

        $korwes = Korwe::where('target_wilayah_id', $this->targetWilayah->id)->get();
        $kortes = Korte::where('target_wilayah_id', $this->targetWilayah->id)->get();

        $korweByRw = [];
        foreach ($korwes as $korwe) {
            $rwKey = ltrim((string) $korwe->nomor_rw, '0');
            $korweByRw[$rwKey] = ($korweByRw[$rwKey] ?? 0) + 1;
        }

        $korteByRw = [];
        foreach ($kortes as $korte) {
            $rwKey = ltrim((string) $korte->nomor_rw, '0');
            $korteByRw[$rwKey] = ($korteByRw[$rwKey] ?? 0) + 1;
        }

        // Ambil data pemilu per RW dari PemiluDesaSummary
        // Prioritas period: jenis=dprd, tahun 2024 > is_default > first
        $rwElectionData = [];
        $hasElectionData = false;

        $allPeriods = PemiluPeriod::forJenis('dprd')->ordered()->get();
        $period = $allPeriods->firstWhere('tahun', 2024)
            ?? $allPeriods->firstWhere('is_default', true)
            ?? $allPeriods->first();

        if ($period) {
            $this->electionLabel = "Pemilu DPRD {$period->tahun}";

            $summary = PemiluDesaSummary::where('pemilu_period_id', $period->id)
                ->where('desa', $this->targetWilayah->desa)
                ->first();

            if ($summary) {
                $this->desaPksVotes = 8621;
                $this->desaPanVotes = 6579;

                if (!empty($summary->rw_rows)) {
                    $hasElectionData = true;

                    foreach ($summary->rw_rows as $rwRow) {
                        // rw field format: "001", "010", "032" (3-digit dari normalizeNumber)
                        $rwKey = ltrim((string) ($rwRow['rw'] ?? ''), '0');
                        if ($rwKey === '') continue;

                        // pks_votes langsung dari rw_rows (field yang disimpan di finalizeAreaRows)
                        $suaraPks = (int) ($rwRow['pks_votes'] ?? 0);

                        // PAN dari party_rows (field 'votes' = total_votes per rw_row)
                        $suaraPan = 0;
                        $partyRows = $rwRow['party_rows'] ?? [];
                        usort($partyRows, fn($a, $b) => ($b['votes'] ?? 0) <=> ($a['votes'] ?? 0));

                        foreach ($partyRows as $p) {
                            $nama = strtoupper(trim($p['party_name'] ?? ''));
                            if ($suaraPks === 0 && (str_contains($nama, 'PKS') || str_contains($nama, 'KEADILAN'))) {
                                $suaraPks = (int) ($p['votes'] ?? 0);
                            }
                            if (str_contains($nama, 'PAN') || str_contains($nama, 'AMANAT')) {
                                $suaraPan = (int) ($p['votes'] ?? 0);
                            }
                        }

                        // 3 partai terkuat
                        $top3Partai = array_slice($partyRows, 0, 3);

                        // Caleg — top candidate dari masing-masing 3 partai terkuat
                        $top3Caleg = [];
                        foreach ($top3Partai as $p) {
                            if (!empty($p['candidates'])) {
                                $bestCand = null;
                                $maxVotes = -1;
                                foreach ($p['candidates'] as $cand) {
                                    $v = (int) ($cand['votes'] ?? 0);
                                    if ($v > $maxVotes && $v > 0) {
                                        $maxVotes = $v;
                                        $bestCand = $cand;
                                    }
                                }
                                
                                if ($bestCand) {
                                    $top3Caleg[] = [
                                        'name'  => $bestCand['name'] ?? '-',
                                        'votes' => $maxVotes,
                                        'party' => $p['party_name'] ?? '',
                                    ];
                                }
                            }
                        }

                        $rwElectionData[$rwKey] = [
                            'suara_pks'   => $suaraPks,
                            'suara_pan'   => $suaraPan,
                            'top3_partai' => $top3Partai,
                            'top3_caleg'  => $top3Caleg,
                        ];
                    }
                }
            }
        }

        // Data validasi fix dari lapangan (Manual Override untuk DPT dan Suara PKS/PAN)
        $validatedData = [
            1  => ['pks' => 601, 'pan' => 76,  'dpt' => 1870],
            2  => ['pks' => 210, 'pan' => 69,  'dpt' => 1358],
            3  => ['pks' => 169, 'pan' => 68,  'dpt' => 1574],
            4  => ['pks' => 236, 'pan' => 286, 'dpt' => 2707],
            5  => ['pks' => 329, 'pan' => 181, 'dpt' => 3445],
            6  => ['pks' => 324, 'pan' => 92,  'dpt' => 2167],
            7  => ['pks' => 732, 'pan' => 99,  'dpt' => 3473],
            8  => ['pks' => 629, 'pan' => 276, 'dpt' => 2123],
            9  => ['pks' => 305, 'pan' => 113, 'dpt' => 1869],
            10 => ['pks' => 324, 'pan' => 35,  'dpt' => 966],
            11 => ['pks' => 201, 'pan' => 344, 'dpt' => 1182],
            12 => ['pks' => 227, 'pan' => 444, 'dpt' => 1090],
            13 => ['pks' => 306, 'pan' => 269, 'dpt' => 959],
            14 => ['pks' => 410, 'pan' => 1323,'dpt' => 3457],
            15 => ['pks' => 164, 'pan' => 423, 'dpt' => 1168],
            16 => ['pks' => 401, 'pan' => 77,  'dpt' => 1385],
            17 => ['pks' => 439, 'pan' => 64,  'dpt' => 1663],
            18 => ['pks' => 352, 'pan' => 134, 'dpt' => 1845],
            19 => ['pks' => 191, 'pan' => 145, 'dpt' => 871],
            20 => ['pks' => 94,  'pan' => 126, 'dpt' => 852],
            21 => ['pks' => 567, 'pan' => 594, 'dpt' => 2842],
            22 => ['pks' => 206, 'pan' => 169, 'dpt' => 1076],
            23 => ['pks' => 123, 'pan' => 69,  'dpt' => 806],
            24 => ['pks' => 146, 'pan' => 46,  'dpt' => 520],
            25 => ['pks' => 58,  'pan' => 40,  'dpt' => 273],
            26 => ['pks' => 132, 'pan' => 34,  'dpt' => 819],
            27 => ['pks' => 220, 'pan' => 258, 'dpt' => 1027],
            28 => ['pks' => 179, 'pan' => 403, 'dpt' => 1214],
            29 => ['pks' => 115, 'pan' => 100, 'dpt' => 495],
            30 => ['pks' => 95,  'pan' => 98,  'dpt' => 515],
            31 => ['pks' => 76,  'pan' => 81,  'dpt' => 535],
            32 => ['pks' => 60,  'pan' => 43,  'dpt' => 487],
        ];

        $formattedData = [];

        for ($i = 1; $i <= 32; $i++) {
            $rwKey    = (string) $i;
            $paddedRw = str_pad($rwKey, 3, '0', STR_PAD_LEFT);

            $dataRw   = $dataRws->firstWhere('nomor_rw', $paddedRw);
            $profilRw = $profilRws->get($rwKey);
            $elec     = $rwElectionData[$rwKey] ?? null;
            $valid    = $validatedData[$i] ?? null;

            $suaraPks = $valid['pks'] ?? 0;
            $suaraPan = $valid['pan'] ?? 0;
            $estimasiDpt = $valid['dpt'] ?? ($dataRw?->dpt ?? 0);

            $juara1 = '';
            if (!empty($elec['top3_partai'][0])) {
                $partyName = strtoupper(trim($elec['top3_partai'][0]['party_name'] ?? ''));
                if (str_contains($partyName, 'PKS') || str_contains($partyName, 'KEADILAN')) {
                    $juara1 = 'PKS';
                } elseif (str_contains($partyName, 'PAN') || str_contains($partyName, 'AMANAT')) {
                    $juara1 = 'PAN';
                } else {
                    $shortName = str_replace('PARTAI ', '', $partyName);
                    if (str_contains($shortName, 'GOLONGAN KARYA')) $shortName = 'GOLKAR';
                    elseif (str_contains($shortName, 'GERAKAN INDONESIA RAYA')) $shortName = 'GERINDRA';
                    elseif (str_contains($shortName, 'DEMOKRASI INDONESIA PERJUANGAN')) $shortName = 'PDIP';
                    elseif (str_contains($shortName, 'KEBANGKITAN BANGSA')) $shortName = 'PKB';
                    elseif (str_contains($shortName, 'NASDEM')) $shortName = 'NASDEM';
                    elseif (str_contains($shortName, 'DEMOKRAT')) $shortName = 'DEMOKRAT';
                    elseif (str_contains($shortName, 'PERSATUAN PEMBANGUNAN')) $shortName = 'PPP';
                    elseif (str_contains($shortName, 'SOLIDARITAS INDONESIA')) $shortName = 'PSI';
                    elseif (str_contains($shortName, 'GELOMBANG RAKYAT')) $shortName = 'GELORA';
                    elseif (str_contains($shortName, 'PERINDO')) $shortName = 'PERINDO';
                    elseif (str_contains($shortName, 'HATI NURANI RAKYAT')) $shortName = 'HANURA';
                    elseif (str_contains($shortName, 'BURUH')) $shortName = 'BURUH';
                    elseif (str_contains($shortName, 'UMMAT')) $shortName = 'UMMAT';
                    
                    $juara1 = trim(explode(' ', $shortName)[0]);
                }
            }

            $formattedData[] = [
                'nomor_rw'    => $paddedRw,
                'nama_wilayah'=> $profilRw?->nama_wilayah ?: '-',
                'jumlah_rt'   => $dataRw?->jumlah_rt ?? 0,
                'estimasi_dpt'=> $estimasiDpt,
                'korwe_count' => $korweByRw[$rwKey] ?? 0,
                'korte_count' => $korteByRw[$rwKey] ?? 0,
                'suara_pks'   => $suaraPks,
                'suara_pan'   => $suaraPan,
                'pks_pan'     => $suaraPks + $suaraPan,
                'juara_1'     => $juara1,
                'dukungan_uno'=> ($profilRw?->afiliasi_pilkades === 'UNO'),
                'afiliasi'    => $profilRw?->afiliasi_pilkades ?? '',
                'calon_lain'  => $profilRw?->afiliasi_calon_lain ?? '',
                'korwe_nama'  => $profilRw?->korwe_pilkades_nama ?? '',
                'top3_partai' => $elec['top3_partai'] ?? [],
                'top3_caleg'  => $elec['top3_caleg'] ?? [],
            ];
        }

        $this->rwData = $formattedData;
    }

    public function openAfiliasiModal($rwId)
    {
        $this->editRwId = $rwId;
        $paddedRw = str_pad($rwId, 3, '0', STR_PAD_LEFT);
        
        $profil = ProfilRw::where('target_wilayah_id', $this->targetWilayah->id)
            ->where('nomor_rw', $paddedRw)
            ->first();

        if ($profil) {
            $this->formAfiliasi = $profil->afiliasi_pilkades;
            $this->formCalonLain = $profil->afiliasi_calon_lain;
            $this->formKorweNama = $profil->korwe_pilkades_nama;
        } else {
            $this->formAfiliasi = null;
            $this->formCalonLain = null;
            $this->formKorweNama = null;
        }

        $this->showAfiliasiModal = true;
    }

    public function saveAfiliasi()
    {
        $paddedRw = str_pad($this->editRwId, 3, '0', STR_PAD_LEFT);

        $profil = ProfilRw::firstOrCreate(
            ['target_wilayah_id' => $this->targetWilayah->id, 'nomor_rw' => $paddedRw],
            ['dapil' => $this->targetWilayah->dapil, 'kecamatan' => $this->targetWilayah->kecamatan, 'desa' => $this->targetWilayah->desa]
        );

        $profil->afiliasi_pilkades = $this->formAfiliasi;
        $profil->afiliasi_calon_lain = ($this->formAfiliasi === 'Ke calon lain') ? $this->formCalonLain : null;
        $profil->korwe_pilkades_nama = $this->formKorweNama;
        $profil->save();

        $this->showAfiliasiModal = false;
        $this->loadData(); // Reload table data
    }

    public function render()
    {
        return view('livewire.public.pilkades-karangsatria')
            ->layout('components.layouts.public-fullscreen', ['title' => 'Pemetaan Strategi Pilkades - Karangsatria']);
    }
}
