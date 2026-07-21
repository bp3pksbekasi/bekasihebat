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

class ProgramArahan extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_CONFIG = [
        'belum_mulai' => ['label' => 'Belum Mulai', 'color' => '#888888', 'bg' => '#f5f5f5'],
        'berjalan' => ['label' => 'Berjalan', 'color' => '#d97706', 'bg' => '#fff7f1'],
        'selesai' => ['label' => 'Selesai', 'color' => '#16a34a', 'bg' => '#dcfce7'],
        'tertunda' => ['label' => 'Tertunda', 'color' => '#dc2626', 'bg' => '#fee2e2'],
    ];

    public const JENIS_PROGRAM = [
        'pembentukan_korwe' => 'Pembentukan Korwe',
        'pembentukan_korte' => 'Pembentukan Korte',
        'rekrutmen_penggalang' => 'Rekrutmen Penggalang',
        'sisir_rw' => 'Sisir RW',
        'penguatan_upa_rw' => 'Penguatan UPA RW',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'org_level',
        'bidang_dpd_id',
        'target_wilayah_id',
        'nomor_rw',
        'status_wilayah_snapshot',
        'judul',
        'deskripsi',
        'jenis_program',
        'target_angka',
        'satuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'penyelenggara',
        'pic_nama',
        'pic_hp',
        'status',
        'level_approval',
        'funding_source',
        'budget_notes',
        'cover_image',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'target_angka' => 'integer',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(BidangDpd::class, 'bidang_dpd_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ProgramArahanApproval::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProgramArahanBudgetItem::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(ProgramArahanReport::class);
    }

    public function personel(): HasMany
    {
        return $this->hasMany(ProgramArahanPersonel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRealisasiAttribute(): int
    {
        return $this->personel()->count();
    }

    public function getProgressPctAttribute(): int
    {
        $target = $this->target_angka;
        if ($target <= 0) {
            return 0;
        }

        $realisasi = $this->realisasi;
        $pct = (int) round(($realisasi / $target) * 100);
        return min($pct, 100);
    }

    public function getDataRwAttribute()
    {
        if (!$this->target_wilayah_id || !$this->nomor_rw) {
            return null;
        }
        
        return DataRw::where('target_wilayah_id', $this->target_wilayah_id)
            ->where('nomor_rw', $this->nomor_rw)
            ->first();
    }
}
