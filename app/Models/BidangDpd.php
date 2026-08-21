<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BidangDpd extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'nama',
        'slug',
        'icon',
        'color',
        'pic_nama',
        'pic_hp',
        'urutan',
        'kabid',
        'nohpkabid',
        'sekbid',
        'nohpsekbid',
        'periode',
        'is_dpd',
        'is_dpc',
        'is_dpra',
        'is_active',
    ];

    protected $casts = [
        'is_dpd' => 'boolean',
        'is_dpc' => 'boolean',
        'is_dpra' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function programKerjas(): HasMany
    {
        return $this->hasMany(ProgramKerja::class);
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(AgendaDpd::class);
    }

    public function getProgressAttribute(): int
    {
        $programs = $this->programKerjas()->where('tahun', date('Y'));
        $total = $programs->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round((float) $programs->avg('progress_pct'));
    }
}
