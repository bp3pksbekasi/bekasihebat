<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemiluPeriod extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'tahun',
        'label',
        'slug',
        'jenis',
        'status',
        'is_default',
        'source_meta',
        'catatan',
        'caleg_summary_payload',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'is_default' => 'boolean',
            'source_meta' => 'array',
            'caleg_summary_payload' => 'array',
        ];
    }

    public function desaSummaries(): HasMany
    {
        return $this->hasMany(PemiluDesaSummary::class);
    }

    public function scopeForJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_default')
            ->orderByDesc('tahun')
            ->orderBy('label');
    }
}
