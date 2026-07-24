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
        'upa_rw' => ['label' => 'UPA RW', 'icon' => 'users', 'color' => '#f97316'],
        'senam_pks' => ['label' => 'SENAM PKS', 'icon' => 'heart', 'color' => '#ec4899'],
        'pertemuan_rki' => ['label' => 'PERTEMUAN RKI', 'icon' => 'users', 'color' => '#ec4899'],
        'reses' => ['label' => 'RESES', 'icon' => 'microphone', 'color' => '#2563eb'],
        'konsolidasi_struktur' => ['label' => 'KONSOLIDASI STRUKTUR', 'icon' => 'building', 'color' => '#64748b'],
        'konsolidasi_korwe_korte' => ['label' => 'KONSOLIDASI KORWE KORTE', 'icon' => 'users-group', 'color' => '#0ea5e9'],
        'konsolidasi_relawan' => ['label' => 'KONSOLIDASI RELAWAN', 'icon' => 'users-group', 'color' => '#14b8a6'],
        'bantuan_posyandu' => ['label' => 'BANTUAN POSYANDU', 'icon' => 'stethoscope', 'color' => '#ef4444'],
        'silaturrahim_tokoh' => ['label' => 'SILATURRAHIM TOKOH', 'icon' => 'heart-handshake', 'color' => '#22c55e'],
        'layanan_ambulance' => ['label' => 'LAYANAN AMBULANCE', 'icon' => 'ambulance', 'color' => '#ef4444'],
        'tatap_muka_warga' => ['label' => 'TATAP MUKA WARGA', 'icon' => 'messages', 'color' => '#8b5cf6'],
        'sidak' => ['label' => 'SIDAK PEMBANGUNAN / ADVOKASI / ASPIRASI', 'icon' => 'alert-triangle', 'color' => '#eab308'],
        'rapat_ranting' => ['label' => 'RAPAT RANTING', 'icon' => 'users', 'color' => '#6366f1'],
        'pemberian_bantuan' => ['label' => 'PEMBERIAN BANTUAN / PROGRAM LAYANAN LAINNYA', 'icon' => 'gift', 'color' => '#10b981'],
    ];

    public const SEGMEN_KEGIATAN = [
        'RELAWAN',
        'IBU IBU',
        'MAJELIS TAKLIM',
        'PEMUDA',
        'TOKOH MASYARAKAT',
        'KOMUNITAS MASYARAKAT',
        'STRUKTUR DPC',
        'PENGURUS DPRA',
        'KORWE / KORTE',
        'ANGGOTA PKS (Pemilik KTA PKS)',
        'WARGA UMUM',
        'Other'
    ];

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'jenis_kegiatan',
        'segmen',
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
