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
        $rwElectionData = []; // indexed by rwKey (int string, no padding)
        $period = PemiluPeriod::where('is_default', true)->first();
        if ($period) {
            $summary = PemiluDesaSummary::where('pemilu_period_id', $period->id)
                ->where('desa', $this->targetWilayah->desa)
                ->first();

            if ($summary && !empty($summary->rw_rows)) {
                foreach ($summary->rw_rows as $rwRow) {
                    $rwKey = ltrim((string) ($rwRow['rw'] ?? ''), '0');
                    if ($rwKey === '') continue;

                    // Suara PKS & PAN dari party_rows
                    $suaraPks = 0;
                    $suaraPan = 0;
                    $partyRows = $rwRow['party_rows'] ?? [];

                    // Sort party rows descending by votes
                    usort($partyRows, fn($a, $b) => ($b['votes'] ?? 0) <=> ($a['votes'] ?? 0));

                    foreach ($partyRows as $p) {
                        $nama = strtoupper(trim($p['party_name'] ?? ''));
                        if (str_contains($nama, 'PKS') || str_contains($nama, 'KEADILAN')) {
                            $suaraPks = (int) ($p['votes'] ?? 0);
                        }
                        if (str_contains($nama, 'PAN') || str_contains($nama, 'AMANAT')) {
                            $suaraPan = (int) ($p['votes'] ?? 0);
                        }
                    }

                    // 3 partai terkuat
                    $top3Partai = array_slice($partyRows, 0, 3);

                    // 3 caleg pemenang: kumpulkan semua kandidat dari semua partai, sort by votes
                    $allCandidates = [];
                    foreach ($partyRows as $p) {
                        foreach ($p['candidates'] ?? [] as $cand) {
                            if (($cand['votes'] ?? 0) > 0) {
                                $allCandidates[] = [
                                    'name'  => $cand['name'] ?? '-',
                                    'votes' => (int) ($cand['votes'] ?? 0),
                                    'party' => $p['party_name'] ?? '',
                                ];
                            }
                        }
                    }
                    usort($allCandidates, fn($a, $b) => $b['votes'] <=> $a['votes']);
                    $top3Caleg = array_slice($allCandidates, 0, 3);

                    $rwElectionData[$rwKey] = [
                        'suara_pks'   => $suaraPks,
                        'suara_pan'   => $suaraPan,
                        'top3_partai' => $top3Partai,
                        'top3_caleg'  => $top3Caleg,
                    ];
                }
            }
        }

        $formattedData = [];

        for ($i = 1; $i <= 32; $i++) {
            $rwKey    = (string) $i;
            $paddedRw = str_pad($rwKey, 3, '0', STR_PAD_LEFT);

            $dataRw   = $dataRws->firstWhere('nomor_rw', $paddedRw);
            $profilRw = $profilRws->get($rwKey);
            $elec     = $rwElectionData[$rwKey] ?? null;

            $suaraPks = $elec['suara_pks'] ?? ($profilRw?->suara_pks_2019 ?? 0);
            $suaraPan = $elec['suara_pan'] ?? 0;

            $formattedData[] = [
                'nomor_rw'    => $paddedRw,
                'nama_wilayah'=> $profilRw?->nama_wilayah ?: '-',
                'jumlah_rt'   => $dataRw?->jumlah_rt ?? 0,
                'estimasi_dpt'=> $dataRw?->dpt ?? 0,
                'korwe_count' => $korweByRw[$rwKey] ?? 0,
                'korte_count' => $korteByRw[$rwKey] ?? 0,
                'suara_pks'   => $suaraPks,
                'suara_pan'   => $suaraPan,
                'pks_pan'     => $suaraPks + $suaraPan,
                'top3_partai' => $elec['top3_partai'] ?? [],
                'top3_caleg'  => $elec['top3_caleg'] ?? [],
            ];
        }

        $this->rwData = $formattedData;
    }

    public function render()
    {
        return view('livewire.public.pilkades-karangsatria')
            ->layout('components.layouts.public-fullscreen', ['title' => 'Pemetaan Strategi Pilkades - Karangsatria']);
    }
}
