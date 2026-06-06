<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aspirasi extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_PIPELINE = [
        'diterima' => ['label' => 'Diterima', 'color' => '#0ea5e9', 'bg' => '#e0f2fe', 'icon' => 'inbox', 'order' => 1],
        'assigned' => ['label' => 'Assigned Dewan', 'color' => '#2563eb', 'bg' => '#dbeafe', 'icon' => 'user-check', 'order' => 2],
        'input_sipd' => ['label' => 'Input SIPD', 'color' => '#7c3aed', 'bg' => '#ede9fe', 'icon' => 'database', 'order' => 3],
        'verifikasi_bappeda' => ['label' => 'Verifikasi Bappeda', 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => 'checkbox', 'order' => 4],
        'dianggarkan' => ['label' => 'Dianggarkan APBD', 'color' => '#16a34a', 'bg' => '#dcfce7', 'icon' => 'coin', 'order' => 5],
        'terealisasi' => ['label' => 'Terealisasi', 'color' => '#15803d', 'bg' => '#bbf7d0', 'icon' => 'check', 'order' => 6],
        'ditolak' => ['label' => 'Ditolak', 'color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => 'x', 'order' => 99],
    ];

    public const KATEGORI_OPTIONS = [
        'infrastruktur' => 'Infrastruktur',
        'kesehatan' => 'Kesehatan',
        'pendidikan' => 'Pendidikan',
        'ekonomi' => 'Ekonomi',
        'lingkungan' => 'Lingkungan',
        'sosial' => 'Sosial',
    ];

    public const SUMBER_OPTIONS = [
        'sisir_rw' => 'Sisir RW',
        'reses' => 'Reses DPRD',
        'event' => 'Event',
        'sapa_warga' => 'Sapa Warga',
        'langsung' => 'Input Langsung',
        'website' => 'Website Publik',
    ];

    public const URGENSI_OPTIONS = [
        'rendah' => ['label' => 'Rendah', 'color' => '#6b7280', 'bg' => '#f4f4f5'],
        'sedang' => ['label' => 'Sedang', 'color' => '#d97706', 'bg' => '#fff7ed'],
        'tinggi' => ['label' => 'Tinggi', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'mendesak' => ['label' => 'Mendesak', 'color' => '#7f1d1d', 'bg' => '#fee2e2'],
    ];

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'urgensi',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'alamat_detail',
        'target_wilayah_id',
        'nama_pelapor',
        'hp_pelapor',
        'sumber',
        'sumber_id',
        'assigned_dewan_id',
        'assigned_at',
        'nomor_pokir',
        'input_sipd_at',
        'screenshot_sipd',
        'status',
        'verified_at',
        'dianggarkan_at',
        'anggaran_nominal',
        'tahun_anggaran',
        'realisasi_at',
        'foto_realisasi',
        'draft_pokir',
        'feedback_warga',
        'notif_warga_sent',
        'catatan_internal',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'input_sipd_at' => 'datetime',
            'verified_at' => 'datetime',
            'dianggarkan_at' => 'datetime',
            'realisasi_at' => 'datetime',
            'notif_warga_sent' => 'boolean',
            'anggaran_nominal' => 'decimal:0',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function assignedDewan(): BelongsTo
    {
        return $this->belongsTo(AnggotaDewan::class, 'assigned_dewan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AspirasiLog::class)->latest();
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(AspirasiReminder::class)->latest();
    }

    public function getStatusConfigAttribute(): array
    {
        return self::STATUS_PIPELINE[$this->status] ?? self::STATUS_PIPELINE['diterima'];
    }

    public function getUrgensiConfigAttribute(): array
    {
        return self::URGENSI_OPTIONS[$this->urgensi] ?? self::URGENSI_OPTIONS['sedang'];
    }

    public function getDurasiAttribute(): ?int
    {
        return $this->created_at
            ? (int) ceil((float) $this->created_at->diffInDays(now()))
            : null;
    }

    public function getDurasiSipdAttribute(): ?int
    {
        if (! $this->assigned_at || $this->status === 'diterima') {
            return null;
        }

        return (int) ceil((float) $this->assigned_at->diffInDays($this->input_sipd_at ?? now()));
    }

    public function generateDraftPokir(): string
    {
        $draft = "POKOK PIKIRAN DPRD\n";
        $draft .= "==========================================\n\n";
        $draft .= "Judul Usulan : {$this->judul}\n";
        $draft .= 'Kategori     : '.(self::KATEGORI_OPTIONS[$this->kategori] ?? $this->kategori)."\n";
        $draft .= 'Lokasi       : '.trim(sprintf('%s, Kec. %s, Kab. Bekasi', (string) $this->desa, (string) $this->kecamatan))."\n";

        if ($this->nomor_rw) {
            $draft .= "RW           : {$this->nomor_rw}\n";
        }

        if ($this->alamat_detail) {
            $draft .= "Detail Lokasi: {$this->alamat_detail}\n";
        }

        $draft .= "\nUraian Permasalahan:\n{$this->deskripsi}\n";
        $draft .= "\nSumber Aspirasi: ".(self::SUMBER_OPTIONS[$this->sumber] ?? $this->sumber)."\n";
        $draft .= "Pelapor      : {$this->nama_pelapor}\n";
        $draft .= "Dapil        : {$this->dapil}\n";
        $draft .= "\n==========================================\n";
        $draft .= "Diusulkan oleh Anggota DPRD PKS Kab. Bekasi\n";

        return $draft;
    }

    public function updateStatus(string $newStatus, ?string $catatan = null, ?int $userId = null): void
    {
        $oldStatus = $this->status;

        $this->status = $newStatus;
        $this->save();

        AspirasiLog::query()->create([
            'aspirasi_id' => $this->id,
            'dari_status' => $oldStatus,
            'ke_status' => $newStatus,
            'aksi' => $newStatus,
            'catatan' => $catatan,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    public function resolveReminderTargetUserId(): ?int
    {
        $dapil = $this->assignedDewan?->dapil ?: $this->dapil;

        if ($dapil) {
            $dapilUserId = User::query()
                ->where('status', 'aktif')
                ->where('dapil', $dapil)
                ->orderByRaw("CASE WHEN role = 'admin_dpd' THEN 0 WHEN role = 'dapil' THEN 1 ELSE 2 END")
                ->value('id');

            if ($dapilUserId) {
                return (int) $dapilUserId;
            }
        }

        if ($this->created_by) {
            return (int) $this->created_by;
        }

        $adminId = User::query()
            ->where('status', 'aktif')
            ->whereIn('role', ['admin_dpd', 'admin', 'dpd'])
            ->orderBy('id')
            ->value('id');

        return $adminId ? (int) $adminId : null;
    }

    public function scopeByDapil(Builder $query, string $value): Builder
    {
        return $query->where('dapil', $value);
    }

    public function scopeByStatus(Builder $query, string $value): Builder
    {
        return $query->where('status', $value);
    }

    public function scopeBelumAssign(Builder $query): Builder
    {
        return $query->whereNull('assigned_dewan_id');
    }

    public function scopeStuck(Builder $query, int $days = 14): Builder
    {
        return $query
            ->where('status', 'assigned')
            ->where('assigned_at', '<', now()->subDays($days));
    }
}
