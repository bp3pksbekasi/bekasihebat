<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramKerja extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_CONFIG = [
        'belum_mulai' => ['label' => 'Belum mulai', 'bg' => '#f5f5f5', 'text' => '#888', 'color' => '#888'],
        'berjalan' => ['label' => 'Berjalan', 'bg' => '#dbeafe', 'text' => '#1e3a5f', 'color' => '#2563eb'],
        'selesai' => ['label' => 'Selesai', 'bg' => '#dcfce7', 'text' => '#14532d', 'color' => '#16a34a'],
        'tertunda' => ['label' => 'Tertunda', 'bg' => '#fef3c7', 'text' => '#92400e', 'color' => '#d97706'],
    ];

    protected $fillable = [
        'bidang_dpd_id',
        'nama_program',
        'deskripsi',
        'tahun',
        'target_teks',
        'target_angka',
        'realisasi',
        'satuan',
        'periode',
        'deadline',
        'pic_nama',
        'pic_hp',
        'status',
        'progress_pct',
        'catatan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(BidangDpd::class, 'bidang_dpd_id');
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(AgendaDpd::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return array{label:string,bg:string,text:string,color:string}
     */
    public function getStatusConfigAttribute(): array
    {
        return self::STATUS_CONFIG[$this->status] ?? self::STATUS_CONFIG['belum_mulai'];
    }

    public function updateProgress(): void
    {
        $pct = $this->target_angka > 0 ? (int) round(($this->realisasi / $this->target_angka) * 100) : 0;
        $status = $pct >= 100 ? 'selesai' : ($pct > 0 ? 'berjalan' : $this->status);

        $this->update([
            'progress_pct' => min($pct, 100),
            'status' => $status,
        ]);
    }
}
