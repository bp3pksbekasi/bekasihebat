<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KegiatanRw extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENIS_KEGIATAN = [
        'silaturahmi' => ['label' => 'Silaturahmi tokoh', 'icon' => 'heart-handshake', 'color' => '#16a34a'],
        'door_to_door' => ['label' => 'Door-to-door', 'icon' => 'door', 'color' => '#2563eb'],
        'baksos' => ['label' => 'Bakti sosial', 'icon' => 'heart', 'color' => '#2563eb'],
        'pengajian' => ['label' => 'Pengajian / kajian', 'icon' => 'book', 'color' => '#d97706'],
        'senam' => ['label' => 'Senam PKS', 'icon' => 'stretching', 'color' => '#ec4899'],
        'diskusi' => ['label' => 'Diskusi warga', 'icon' => 'messages', 'color' => '#8b5cf6'],
        'bedah_rumah' => ['label' => 'Bedah rumah', 'icon' => 'home-cog', 'color' => '#0891b2'],
        'pendidikan' => ['label' => 'Bantuan pendidikan', 'icon' => 'school', 'color' => '#0d9488'],
        'kesehatan' => ['label' => 'Layanan kesehatan', 'icon' => 'stethoscope', 'color' => '#dc2626'],
        'rekrutmen' => ['label' => 'Rekrutmen kader', 'icon' => 'user-plus', 'color' => '#fe5000'],
        'konsolidasi' => ['label' => 'Konsolidasi internal', 'icon' => 'users-group', 'color' => '#64748b'],
        'lainnya' => ['label' => 'Lainnya', 'icon' => 'dots', 'color' => '#888888'],
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'jenis_kegiatan',
        'tanggal_kegiatan',
        'pelaksana',
        'jumlah_warga',
        'catatan',
        'foto',
        'tampil_galeri',
        'tokoh_ditemui',
        'tindak_lanjut',
        'jadwal_berikutnya',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'datetime',
            'jadwal_berikutnya' => 'date',
            'foto' => 'array',
            'tampil_galeri' => 'boolean',
            'jumlah_warga' => 'integer',
            'created_by' => 'integer',
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

    public function event(): HasOne
    {
        return $this->hasOne(Event::class, 'kegiatan_rw_id');
    }

    /**
     * @return array{label:string,icon:string,color:string}
     */
    public function getJenisConfigAttribute(): array
    {
        return self::JENIS_KEGIATAN[$this->jenis_kegiatan] ?? self::JENIS_KEGIATAN['lainnya'];
    }

    public function getEventIdLinkedAttribute(): ?string
    {
        return $this->event?->getRouteKey();
    }

    public function scopeByDapil(Builder $query, string $dapil): Builder
    {
        return $query->where('dapil', $dapil);
    }

    public function scopeByDesa(Builder $query, string $kecamatan, string $desa): Builder
    {
        return $query->where('kecamatan', $kecamatan)->where('desa', $desa);
    }

    public function scopeByRw(Builder $query, string $targetWilayahId, string $nomorRw): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId)->where('nomor_rw', $nomorRw);
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereMonth('tanggal_kegiatan', now()->month)
            ->whereYear('tanggal_kegiatan', now()->year);
    }

    public function scopePeriode(Builder $query, int|string $bulan, int|string $tahun): Builder
    {
        return $query->whereMonth('tanggal_kegiatan', (int) $bulan)
            ->whereYear('tanggal_kegiatan', (int) $tahun);
    }
}
