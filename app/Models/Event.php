<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_MENUNGGU = 'pending_approval';
    public const STATUS_DISETUJUI = 'approved';
    public const STATUS_DITOLAK = 'rejected';
    public const STATUS_BERLANGSUNG = 'approved';
    public const STATUS_SELESAI = 'completed';
    public const STATUS_DIBATALKAN = 'cancelled';

    public const STATUS_CONFIG = [
        'draft' => ['label' => 'Draft', 'color' => '#888888', 'bg' => '#f5f5f5'],
        'pending_approval' => ['label' => 'Menunggu Approval', 'color' => '#d97706', 'bg' => '#fff7f1'],
        'approved' => ['label' => 'Disetujui', 'color' => '#16a34a', 'bg' => '#dcfce7'],
        'rejected' => ['label' => 'Ditolak', 'color' => '#dc2626', 'bg' => '#fee2e2'],
        'completed' => ['label' => 'Selesai', 'color' => '#16a34a', 'bg' => '#dcfce7'],
        'cancelled' => ['label' => 'Dibatalkan', 'color' => '#888888', 'bg' => '#f5f5f5'],
        'menunggu_approval' => ['label' => 'Menunggu Approval', 'color' => '#d97706', 'bg' => '#fff7f1'],
        'disetujui' => ['label' => 'Disetujui', 'color' => '#16a34a', 'bg' => '#dcfce7'],
        'ditolak' => ['label' => 'Ditolak', 'color' => '#dc2626', 'bg' => '#fee2e2'],
        'selesai' => ['label' => 'Selesai', 'color' => '#16a34a', 'bg' => '#dcfce7'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'color' => '#888888', 'bg' => '#f5f5f5'],
    ];

    public const JENIS_EVENT = [
        'baksos' => 'Bakti Sosial',
        'pengajian' => 'Pengajian / Kajian',
        'senam' => 'Senam PKS',
        'diskusi' => 'Diskusi Warga',
        'pelatihan' => 'Pelatihan / Workshop',
        'musyawarah' => 'Musyawarah / Rapat',
        'bedah_rumah' => 'Bedah Rumah',
        'kesehatan' => 'Layanan Kesehatan',
        'pendidikan' => 'Bantuan Pendidikan',
        'lainnya' => 'Lainnya',
    ];

    public const LEVEL_APPROVAL = [
        'dpra' => ['label' => 'DPRa', 'order' => 1],
        'dpc' => ['label' => 'DPC', 'order' => 2],
        'dpd' => ['label' => 'DPD', 'order' => 3],
        'selesai' => ['label' => 'Selesai', 'order' => 4],
    ];

    public const ORG_LEVEL_OPTIONS = [
        'dpra' => 'DPRa (Desa/Kelurahan)',
        'dpc'  => 'DPC (Kecamatan)',
        'dpd'  => 'DPD (Kabupaten Bekasi)',
    ];

    protected $fillable = [
        'uuid',
        'slug',
        'org_level',
        'bidang_dpd_id',
        'org_kecamatan',
        'org_desa',
        'judul',
        'deskripsi',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'lokasi_desa',
        'lokasi_kecamatan',
        'lokasi_dapil',
        'kapasitas',
        'is_public',
        'cover_image',
        'status',
        'level_approval',
        'penyelenggara',
        'pic_nama',
        'pic_hp',
        'kegiatan_rw_id',
        'created_by',
        'speakers',
        'funding_source',
        'target_program',
        'requirements',
        'budget_notes',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'is_public' => 'boolean',
            'kapasitas' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(EventApproval::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(EventBudgetItem::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(EventReport::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(EventPeserta::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(\App\Models\BidangDpd::class, 'bidang_dpd_id');
    }

    public function kegiatanRw(): BelongsTo
    {
        return $this->belongsTo(KegiatanRw::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByDapil(Builder $query, string $dapil): Builder
    {
        return $query->where('lokasi_dapil', $dapil);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('tanggal_mulai', '>=', now());
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeForUser(Builder $query, \App\Models\User $user): Builder
    {
        // DPD bisa lihat semua
        if ($user->isDpd()) {
            return $query;
        }

        // DPC hanya lihat program di kecamatannya
        if ($user->isDpc()) {
            return $query->where('org_kecamatan', $user->kecamatan);
        }

        // DPRa hanya lihat program di desanya sendiri
        return $query->where('org_desa', $user->desa);
    }

    public function getStatusConfigAttribute(): array
    {
        return self::STATUS_CONFIG[$this->status] ?? self::STATUS_CONFIG[self::STATUS_DRAFT];
    }

    public function getTotalBudgetAttribute(): float
    {
        if ($this->relationLoaded('budgetItems')) {
            return (float) $this->budgetItems->sum('subtotal');
        }

        return (float) $this->budgetItems()->sum('subtotal');
    }

    public function getRegistrationCountAttribute(): int
    {
        if ($this->relationLoaded('registrations')) {
            return (int) $this->registrations->count();
        }

        return $this->registrations()->count();
    }

    public function getPesertaCountAttribute(): int
    {
        if ($this->relationLoaded('pesertas')) {
            return (int) $this->pesertas->count();
        }

        return $this->pesertas()->count();
    }

    public function getTitleAttribute(): string
    {
        return (string) $this->judul;
    }

    public function getStartsAtAttribute()
    {
        return $this->tanggal_mulai;
    }

    public function getLocationNameAttribute(): string
    {
        return (string) ($this->lokasi_desa ?: $this->lokasi);
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_EVENT[$this->jenis] ?? self::JENIS_EVENT['lainnya'];
    }

    public function needsApproval(): bool
    {
        return $this->org_level === 'dpd';
    }

    public function getOrgLevelLabelAttribute(): string
    {
        return self::ORG_LEVEL_OPTIONS[$this->org_level] ?? $this->org_level;
    }
}
