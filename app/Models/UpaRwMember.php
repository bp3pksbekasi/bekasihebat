<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpaRwMember extends Model
{
    use HasFactory;
    use HasUuids;

    public const JABATAN_OPTIONS = [
        'pembina' => 'Pembina',
        'ketua' => 'Ketua',
        'sekretaris' => 'Sekretaris',
        'anggota' => 'Anggota',
    ];

    public const ASAL_OPTIONS = [
        'korwe' => 'Dari KORWE',
        'korte' => 'Dari KORTE',
        'kader_baru' => 'Kader Baru',
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nama',
        'no_hp',
        'jabatan',
        'asal',
        'korwe_id',
        'korte_id',
        'status',
        'catatan',
        'created_by',
    ];

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function korwe(): BelongsTo
    {
        return $this->belongsTo(Korwe::class);
    }

    public function korte(): BelongsTo
    {
        return $this->belongsTo(Korte::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByRw(Builder $query, string $targetWilayahId, string $rw): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId)->where('nomor_rw', $rw);
    }
}
