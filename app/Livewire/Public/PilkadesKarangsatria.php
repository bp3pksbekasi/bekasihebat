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
    /** Suara PKS total tingkat desa dari PemiluDesaSummary (untuk referensi footer) */
    public int $desaPksVotes = 0;
    public int $desaPanVotes = 0;
    public int $unmappedPksVotes = 0;
    public int $unmappedPanVotes = 0;
    public string $electionLabel = '';

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
                // Simpan total desa-level untuk referensi di footer
                $this->desaPksVotes = (int) ($summary->pks_votes ?? 0);

                // Hitung PAN dari party_rows level desa
                foreach ($summary->party_rows ?? [] as $p) {
                    $nama = strtoupper(trim($p['party_name'] ?? ''));
                    if (str_contains($nama, 'PAN') || str_contains($nama, 'AMANAT')) {
                        $this->desaPanVotes = (int) ($p['total_votes'] ?? 0);
                        break;
                    }
                }

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

                        // Caleg — dari candidates array dalam party_rows
                        $top3Caleg = [];
                        foreach (array_slice($partyRows, 0, 10) as $p) {
                            if (!empty($p['candidates'])) {
                                foreach ($p['candidates'] as $cand) {
                                    if (($cand['votes'] ?? 0) > 0) {
                                        $top3Caleg[] = [
                                            'name'  => $cand['name'] ?? '-',
                                            'votes' => (int) ($cand['votes'] ?? 0),
                                            'party' => $p['party_name'] ?? '',
                                        ];
                                    }
                                }
                            }
                            if (count($top3Caleg) >= 3) break;
                        }
                        usort($top3Caleg, fn($a, $b) => $b['votes'] <=> $a['votes']);
                        $top3Caleg = array_slice($top3Caleg, 0, 3);

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

        $formattedData = [];

        for ($i = 1; $i <= 32; $i++) {
            $rwKey    = (string) $i;
            $paddedRw = str_pad($rwKey, 3, '0', STR_PAD_LEFT);

            $dataRw   = $dataRws->firstWhere('nomor_rw', $paddedRw);
            $profilRw = $profilRws->get($rwKey);
            $elec     = $rwElectionData[$rwKey] ?? null;

            // Jika ada data pemilu 2024, gunakan itu. Fallback ke suara_pks_2019 hanya jika tidak ada summary sama sekali.
            if ($hasElectionData) {
                $suaraPks = $elec['suara_pks'] ?? 0;
                $suaraPan = $elec['suara_pan'] ?? 0;
            } else {
                $suaraPks = $profilRw?->suara_pks_2019 ?? 0;
                $suaraPan = 0;
            }

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

        if ($hasElectionData) {
            $sumPks1to32 = array_sum(array_column($formattedData, 'suara_pks'));
            $sumPan1to32 = array_sum(array_column($formattedData, 'suara_pan'));
            $this->unmappedPksVotes = max(0, $this->desaPksVotes - $sumPks1to32);
            $this->unmappedPanVotes = max(0, $this->desaPanVotes - $sumPan1to32);
        }

        $this->rwData = $formattedData;
    }

    public function render()
    {
        return view('livewire.public.pilkades-karangsatria')
            ->layout('components.layouts.public-fullscreen', ['title' => 'Pemetaan Strategi Pilkades - Karangsatria']);
    }
}
