<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontakWarga extends Model
{
    use HasFactory;
    use HasUuids;

    public const TARGET_PER_RW = 200;

    public const SUMBER_OPTIONS = [
        'manual' => 'Input manual',
        'bulk' => 'Bulk paste',
        'import' => 'Import file',
        'wa_export' => 'WA export',
        'event' => 'Dari event',
        'penggalang' => 'Via penggalang',
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nama',
        'no_wa',
        'no_hp',
        'rt',
        'alamat',
        'sumber',
        'penggalang_id',
        'catatan',
        'status',
        'created_by',
    ];

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function penggalang(): BelongsTo
    {
        return $this->belongsTo(PenggalangSuara::class, 'penggalang_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByDapil(Builder $query, string $value): Builder
    {
        return $query->where('dapil', $value);
    }

    public function scopeByDesa(Builder $query, string $targetWilayahId): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId);
    }

    public function scopeByRw(Builder $query, string $targetWilayahId, string $rw): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId)->where('nomor_rw', $rw);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}
