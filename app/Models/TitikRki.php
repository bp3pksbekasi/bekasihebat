<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TitikRki extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENIS_KEGIATAN_OPTIONS = [
        'posyandu' => 'Posyandu',
        'kerajinan' => 'Kerajinan Tangan',
        'paud' => 'PAUD / Pendidikan Anak',
        'alquran' => 'Belajar Al-Quran',
        'sembako' => 'Sembako Murah',
        'arisan' => 'Arisan Ibu',
        'kesehatan' => 'Layanan Kesehatan',
        'konsultasi' => 'Konsultasi Keluarga',
        'keterampilan' => 'Pelatihan Keterampilan',
        'lainnya' => 'Lainnya',
    ];

    public const STATUS_CONFIG = [
        'aktif' => ['label' => 'Aktif', 'bg' => '#dcfce7', 'text' => '#14532d', 'color' => '#16a34a'],
        'pembentukan' => ['label' => 'Pembentukan', 'bg' => '#fef3c7', 'text' => '#92400e', 'color' => '#d97706'],
        'nonaktif' => ['label' => 'Nonaktif', 'bg' => '#fee2e2', 'text' => '#991b1b', 'color' => '#dc2626'],
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nama_penggerak',
        'no_hp_penggerak',
        'lokasi',
        'hari_kegiatan',
        'jam_kegiatan',
        'jenis_kegiatan',
        'avg_peserta',
        'status',
        'tanggal_aktif',
        'catatan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jenis_kegiatan' => 'array',
            'tanggal_aktif' => 'date',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logSesis(): MorphMany
    {
        return $this->morphMany(LogSesi::class, 'loggable');
    }

    /**
     * @return array{label:string,bg:string,text:string,color:string}
     */
    public function getStatusConfigAttribute(): array
    {
        return self::STATUS_CONFIG[$this->status] ?? self::STATUS_CONFIG['pembentukan'];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByDapil(Builder $query, string $value): Builder
    {
        return $query->where('dapil', $value);
    }

    public function scopeByDesa(Builder $query, string $targetWilayahId): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId);
    }
}
