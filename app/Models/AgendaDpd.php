<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaDpd extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENIS_OPTIONS = [
        'rapat' => 'Rapat / Koordinasi',
        'kunjungan' => 'Kunjungan Lapangan',
        'pelatihan' => 'Pelatihan / Workshop',
        'musyawarah' => 'Musyawarah',
        'sosialisasi' => 'Sosialisasi',
        'lainnya' => 'Lainnya',
    ];

    public const STATUS_CONFIG = [
        'dijadwalkan' => ['label' => 'Dijadwalkan', 'color' => '#2563eb'],
        'berlangsung' => ['label' => 'Berlangsung', 'color' => '#d97706'],
        'selesai' => ['label' => 'Selesai', 'color' => '#16a34a'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'color' => '#dc2626'],
    ];

    protected $fillable = [
        'bidang_dpd_id',
        'program_kerja_id',
        'judul',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'dapil_terkait',
        'peserta_target',
        'peserta_hadir',
        'status',
        'catatan',
        'hasil',
        'foto',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'foto' => 'array',
        ];
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(BidangDpd::class, 'bidang_dpd_id');
    }

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
