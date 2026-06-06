<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemiluDesaSummary extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'pemilu_period_id',
        'dapil',
        'kecamatan',
        'desa',
        'scope_key',
        'total_dpt',
        'total_laki',
        'total_perempuan',
        'gen_z',
        'millennial',
        'gen_x',
        'boomer',
        'age_unknown',
        'total_tps',
        'total_rw',
        'total_rt',
        'total_votes',
        'pks_votes',
        'pks_party_votes',
        'pks_candidate_votes',
        'pks_share',
        'pks_rank',
        'pks_gap_share',
        'status_wilayah',
        'estimated_seats',
        'party_rows',
        'top_candidates',
        'tps_rows',
        'rw_rows',
        'rt_rows',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'total_dpt' => 'integer',
            'total_laki' => 'integer',
            'total_perempuan' => 'integer',
            'gen_z' => 'integer',
            'millennial' => 'integer',
            'gen_x' => 'integer',
            'boomer' => 'integer',
            'age_unknown' => 'integer',
            'total_tps' => 'integer',
            'total_rw' => 'integer',
            'total_rt' => 'integer',
            'total_votes' => 'integer',
            'pks_votes' => 'integer',
            'pks_party_votes' => 'integer',
            'pks_candidate_votes' => 'integer',
            'pks_share' => 'decimal:6',
            'pks_rank' => 'integer',
            'pks_gap_share' => 'decimal:6',
            'estimated_seats' => 'integer',
            'party_rows' => 'array',
            'top_candidates' => 'array',
            'tps_rows' => 'array',
            'rw_rows' => 'array',
            'rt_rows' => 'array',
            'meta' => 'array',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PemiluPeriod::class, 'pemilu_period_id');
    }

    public function scopeForPeriod(Builder $query, string $periodId): Builder
    {
        return $query->where('pemilu_period_id', $periodId);
    }
}
