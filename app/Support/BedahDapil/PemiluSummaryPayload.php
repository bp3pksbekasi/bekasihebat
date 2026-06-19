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
    public function build(PemiluPeriod $period): array
    {
        $query = PemiluDesaSummary::query()
            ->forPeriod($period->id)
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa');

        $query = $this->applyUserScope($query, ['dapil', 'kecamatan']);

        $villages = $query->get()
            ->map(function (PemiluDesaSummary $summary): array {
                return [
                    'scope_key' => $summary->scope_key,
                    'dapil' => $summary->dapil,
                    'kecamatan' => $summary->kecamatan,
                    'desa' => $summary->desa,
                    'total_dpt' => (int) $summary->total_dpt,
                    'total_laki' => (int) $summary->total_laki,
                    'total_perempuan' => (int) $summary->total_perempuan,
                    'gen_z' => (int) $summary->gen_z,
                    'millennial' => (int) $summary->millennial,
                    'gen_x' => (int) $summary->gen_x,
                    'boomer' => (int) $summary->boomer,
                    'age_unknown' => (int) $summary->age_unknown,
                    'total_tps' => (int) $summary->total_tps,
                    'total_rw' => (int) $summary->total_rw,
                    'total_rt' => (int) $summary->total_rt,
                    'total_votes' => (int) $summary->total_votes,
                    'pks_votes' => (int) $summary->pks_votes,
                    'pks_party_votes' => (int) $summary->pks_party_votes,
                    'pks_candidate_votes' => (int) $summary->pks_candidate_votes,
                    'pks_share' => (float) $summary->pks_share,
                    'pks_rank' => (int) $summary->pks_rank,
                    'pks_gap_share' => (float) $summary->pks_gap_share,
                    'status_wilayah' => $summary->status_wilayah,
                    'estimated_seats' => (int) $summary->estimated_seats,
                    'party_rows' => $summary->party_rows ?? [],
                    'top_candidates' => $summary->top_candidates ?? [],
                    'tps_rows' => $summary->tps_rows ?? [],
                    'rw_rows' => $summary->rw_rows ?? [],
                    'rt_rows' => $summary->rt_rows ?? [],
                    'meta' => $summary->meta ?? [],
                ];
            })
            ->values()
            ->all();

        return [
            'period' => [
                'id' => $period->id,
                'tahun' => (int) $period->tahun,
                'label' => $period->label,
                'jenis' => $period->jenis,
                'is_default' => (bool) $period->is_default,
            ],
            'villages' => $villages,
        ];
    }
}
