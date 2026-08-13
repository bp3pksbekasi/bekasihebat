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
    public $debugInfo = [];

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
        // Gunakan logika yang sama persis dengan bedah-dapil:
        // Prioritas: jenis=dprd, tahun 2024 > is_default > first
        $rwElectionData = [];
        $allPeriods = PemiluPeriod::forJenis('dprd')->ordered()->get();
        $period = $allPeriods->firstWhere('tahun', 2024)
            ?? $allPeriods->firstWhere('is_default', true)
            ?? $allPeriods->first();

        // DEBUG INFO — hapus setelah masalah teridentifikasi
        $this->debugInfo = [
            'all_periods'      => $allPeriods->map(fn($p) => "id={$p->id} tahun={$p->tahun} jenis={$p->jenis} default={$p->is_default} label={$p->label}")->toArray(),
            'selected_period'  => $period ? "id={$period->id} tahun={$period->tahun} jenis={$period->jenis}" : 'TIDAK DITEMUKAN',
            'desa_query'       => $this->targetWilayah->desa ?? 'NULL',
            'summary_found'    => false,
            'rw_rows_count'    => 0,
            'desa_pks_votes'   => 0,
        ];

        if ($period) {
            $summary = PemiluDesaSummary::where('pemilu_period_id', $period->id)
                ->where('desa', $this->targetWilayah->desa)
                ->first();

            $this->debugInfo['summary_found'] = $summary !== null;
            $this->debugInfo['rw_rows_count'] = $summary ? count($summary->rw_rows ?? []) : 0;
            $this->debugInfo['desa_pks_votes'] = $summary?->pks_votes ?? 0;
            if (!$summary) {
                // Coba tanpa filter period untuk lihat apakah ada summary sama sekali
                $anySummary = PemiluDesaSummary::where('desa', $this->targetWilayah->desa)->first();
                $this->debugInfo['any_summary_period'] = $anySummary ? "period_id={$anySummary->pemilu_period_id}" : 'TIDAK ADA SAMA SEKALI';
            }

            if ($summary && !empty($summary->rw_rows)) {
                // DEBUG: ambil sample rw_rows[0..2]
                $this->debugInfo['sample_rw_rows'] = array_map(fn($r) => [
                    'rw_raw'     => $r['rw'] ?? 'NULL',
                    'pks_votes'  => $r['pks_votes'] ?? 'N/A',
                    'rw_ltrim'   => ltrim((string)($r['rw'] ?? ''), '0'),
                ], array_slice($summary->rw_rows, 0, 4));

                foreach ($summary->rw_rows as $rwRow) {
                    $rwKey = ltrim((string) ($rwRow['rw'] ?? ''), '0');
                    if ($rwKey === '') continue;

                    // Di rw_rows, party_rows menggunakan field 'votes' (bukan total_votes)
                    // Referensi: PemiluSummaryCompiler.php baris ~493 finalizeAreaRows()
                    // $partyRow['votes'] = (int) $pr['total_votes']  — di-store sebagai 'votes'
                    $suaraPks = 0;
                    $suaraPan = 0;

                    // Gunakan pks_votes langsung dari rw_rows jika ada (lebih akurat)
                    if (isset($rwRow['pks_votes']) && $rwRow['pks_votes'] > 0) {
                        $suaraPks = (int) $rwRow['pks_votes'];
                    }

                    $partyRows = $rwRow['party_rows'] ?? [];

                    // Sort descending by 'votes' (field yang benar di rw_rows)
                    usort($partyRows, fn($a, $b) => ($b['votes'] ?? 0) <=> ($a['votes'] ?? 0));

                    foreach ($partyRows as $p) {
                        $nama = strtoupper(trim($p['party_name'] ?? ''));
                        // 'votes' adalah field yang benar di rw_rows party_rows
                        if ($suaraPks === 0 && (str_contains($nama, 'PKS') || str_contains($nama, 'KEADILAN'))) {
                            $suaraPks = (int) ($p['votes'] ?? 0);
                        }
                        if (str_contains($nama, 'PAN') || str_contains($nama, 'AMANAT')) {
                            $suaraPan = (int) ($p['votes'] ?? 0);
                        }
                    }

                    // 3 partai terkuat (sudah sorted descending)
                    $top3Partai = array_slice($partyRows, 0, 3);

                    // 3 caleg — dari top_candidate per RW atau candidates array
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

                // DEBUG: tampilkan keys yang terbentuk
                $this->debugInfo['election_keys'] = array_keys($rwElectionData);
                $this->debugInfo['election_sample_rw1'] = $rwElectionData['1'] ?? 'KEY 1 TIDAK ADA';
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
