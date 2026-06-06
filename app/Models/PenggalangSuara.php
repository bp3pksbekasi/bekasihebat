<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggalangSuara extends Model
{
    use HasFactory;
    use HasUuids;

    public const SUMBER_OPTIONS = [
        'korwe' => 'Dari KORWE',
        'korte' => 'Dari KORTE',
        'upa' => 'Dari UPA RW',
        'warga' => 'Warga rekrutan',
        'event' => 'Dari event/kegiatan',
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nama',
        'no_hp',
        'no_wa',
        'rt',
        'sumber',
        'target_jangkauan',
        'realisasi_jangkauan',
        'status',
        'catatan',
        'created_by',
    ];

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByDapil(Builder $query, string $value): Builder
    {
        return $query->where('dapil', $value);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}
