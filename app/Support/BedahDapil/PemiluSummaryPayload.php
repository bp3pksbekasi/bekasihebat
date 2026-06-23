<?php

declare(strict_types=1);

namespace App\Support\BedahDapil;

use App\Models\PemiluDesaSummary;
use App\Models\PemiluPeriod;

class PemiluSummaryPayload
{
    use \App\Traits\WithWilayahScope;

    /**
     * @return array{period:array<string,mixed>,villages:list<array<string,mixed>>}
     */
    public function buildJson(PemiluPeriod $period): string
    {
        $query = \Illuminate\Support\Facades\DB::table('pemilu_desa_summaries')
            ->where('pemilu_period_id', $period->id)
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa');

        $query = $this->applyUserScope($query, ['dapil', 'kecamatan', 'desa']);

        $villagesJson = [];
        foreach ($query->cursor() as $summary) {
            $village = '{'
                . '"scope_key":' . json_encode($summary->scope_key) . ','
                . '"dapil":' . json_encode($summary->dapil) . ','
                . '"kecamatan":' . json_encode($summary->kecamatan) . ','
                . '"desa":' . json_encode($summary->desa) . ','
                . '"total_dpt":' . ((int) $summary->total_dpt) . ','
                . '"total_laki":' . ((int) $summary->total_laki) . ','
                . '"total_perempuan":' . ((int) $summary->total_perempuan) . ','
                . '"gen_z":' . ((int) $summary->gen_z) . ','
                . '"millennial":' . ((int) $summary->millennial) . ','
                . '"gen_x":' . ((int) $summary->gen_x) . ','
                . '"boomer":' . ((int) $summary->boomer) . ','
                . '"age_unknown":' . ((int) $summary->age_unknown) . ','
                . '"total_tps":' . ((int) $summary->total_tps) . ','
                . '"total_rw":' . ((int) $summary->total_rw) . ','
                . '"total_rt":' . ((int) $summary->total_rt) . ','
                . '"total_votes":' . ((int) $summary->total_votes) . ','
                . '"pks_votes":' . ((int) $summary->pks_votes) . ','
                . '"pks_party_votes":' . ((int) $summary->pks_party_votes) . ','
                . '"pks_candidate_votes":' . ((int) $summary->pks_candidate_votes) . ','
                . '"pks_share":' . ((float) $summary->pks_share) . ','
                . '"pks_rank":' . ((int) $summary->pks_rank) . ','
                . '"pks_gap_share":' . ((float) $summary->pks_gap_share) . ','
                . '"status_wilayah":' . json_encode($summary->status_wilayah) . ','
                . '"estimated_seats":' . ((int) $summary->estimated_seats) . ','
                . '"party_rows":' . ($summary->party_rows ?: '[]') . ','
                . '"top_candidates":' . ($summary->top_candidates ?: '[]') . ','
                . '"tps_rows":' . ($summary->tps_rows ?: '[]') . ','
                . '"rw_rows":' . ($summary->rw_rows ?: '[]') . ','
                . '"rt_rows":' . ($summary->rt_rows ?: '[]') . ','
                . '"meta":' . ($summary->meta ?: '[]')
                . '}';
            $villagesJson[] = $village;
        }

        $periodArr = [
            'id' => $period->id,
            'tahun' => (int) $period->tahun,
            'label' => $period->label,
            'jenis' => $period->jenis,
            'is_default' => (bool) $period->is_default,
        ];

        return '{"period":' . json_encode($periodArr) . ',"villages":[' . implode(',', $villagesJson) . ']}';
    }
}
