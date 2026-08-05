<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanMonev extends Model
{
    use HasUuids;

    public const STATUS_TERBUKA = 'terbuka';
    public const STATUS_SELESAI = 'selesai';

    public const SUMBER_OTOMATIS = 'otomatis';
    public const SUMBER_MANUAL   = 'manual';

    public const JENIS_OPTIONS = [
        'sisir_stagnan'       => 'Sisir RW Stagnan',
        'korwe_korte_stagnan' => 'Korwe/Korte Stagnan',
        'penggalang_pasif'    => 'Penggalang Pasif',
        'profil_belum_lengkap'=> 'Profil RW Belum Lengkap',
        'lainnya'             => 'Lainnya',
    ];

    public const LEVEL_OPTIONS = [
        'dpra' => 'DPRA',
        'dpc'  => 'DPC',
        'dpd'  => 'DPD',
    ];

    protected $fillable = [
        'target_wilayah_id',
        'nomor_rw',
        'jenis_temuan',
        'sumber',
        'temuan',
        'tindak_lanjut',
        'status',
        'level_penanggung_jawab',
        'pic_nama',
        'closed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'closed_at'  => 'datetime',
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

    /**
     * Umur catatan dalam hari:
     * - Jika sudah selesai: selisih created_at → closed_at
     * - Jika masih terbuka: selisih created_at → sekarang
     */
    public function getUmurHariAttribute(): int
    {
        $end = $this->closed_at ?? Carbon::now();
        return (int) $this->created_at->diffInDays($end);
    }

    public function isTerbuka(): bool
    {
        return $this->status === self::STATUS_TERBUKA;
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_OPTIONS[$this->jenis_temuan] ?? $this->jenis_temuan;
    }
}
