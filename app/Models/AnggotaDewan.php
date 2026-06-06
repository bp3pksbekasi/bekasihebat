<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnggotaDewan extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'nama', 'jabatan', 'dapil', 'suara_2024', 'status_petahana',
        'jabatan_fraksi', 'jabatan_dprd', 'jabatan_partai', 'wilayah_dapil',
        'no_hp', 'foto',
        'instagram', 'ig_followers', 'tiktok', 'tt_followers',
        'youtube', 'yt_subscribers', 'twitter', 'tw_followers',
        'facebook', 'fb_followers',
        'skor_popularitas', 'target_popularitas',
        'tim_media_nama', 'tim_media_hp',
        'status', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status_petahana' => 'boolean',
        ];
    }

    public function kontens(): HasMany
    {
        return $this->hasMany(KontenMedsos::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalFollowersAttribute(): int
    {
        return (int) $this->ig_followers
            + (int) $this->tt_followers
            + (int) $this->yt_subscribers
            + (int) $this->tw_followers
            + (int) $this->fb_followers;
    }

    public function getPlatformsAttribute(): array
    {
        $platforms = [];

        if ($this->instagram) {
            $platforms[] = ['name' => 'instagram', 'icon' => 'brand-instagram', 'color' => '#e1306c', 'followers' => $this->ig_followers, 'user' => $this->instagram];
        }
        if ($this->tiktok) {
            $platforms[] = ['name' => 'tiktok', 'icon' => 'brand-tiktok', 'color' => '#000', 'followers' => $this->tt_followers, 'user' => $this->tiktok];
        }
        if ($this->youtube) {
            $platforms[] = ['name' => 'youtube', 'icon' => 'brand-youtube', 'color' => '#ff0000', 'followers' => $this->yt_subscribers, 'user' => $this->youtube];
        }
        if ($this->twitter) {
            $platforms[] = ['name' => 'twitter', 'icon' => 'brand-twitter', 'color' => '#1da1f2', 'followers' => $this->tw_followers, 'user' => $this->twitter];
        }
        if ($this->facebook) {
            $platforms[] = ['name' => 'facebook', 'icon' => 'brand-facebook', 'color' => '#1877f2', 'followers' => $this->fb_followers, 'user' => $this->facebook];
        }

        return $platforms;
    }

    public function getDisplayJabatanAttribute(): string
    {
        if ($this->jabatan_dprd) {
            return (string) $this->jabatan_dprd;
        }

        if ($this->jabatan_fraksi) {
            return (string) $this->jabatan_fraksi;
        }

        return (string) $this->jabatan;
    }

    public function getKecamatanListAttribute(): array
    {
        if (! $this->wilayah_dapil) {
            return [];
        }

        return array_map('trim', explode(',', (string) $this->wilayah_dapil));
    }

    public function hitungPopularitas(): int
    {
        $kontenBulanIni = $this->kontens()
            ->whereMonth('tanggal_posting', now()->month)
            ->whereYear('tanggal_posting', now()->year)
            ->count();
        $videoPelayanan = $this->kontens()
            ->where('is_video_pelayanan', true)
            ->whereMonth('tanggal_posting', now()->month)
            ->count();
        $avgEngagement = $this->kontens()
            ->whereMonth('tanggal_posting', now()->month)
            ->selectRaw('AVG(likes + comments + shares) as avg_eng')
            ->value('avg_eng') ?? 0;

        $skorFrekuensi = min($kontenBulanIni / 8 * 100, 100) * 0.4;
        $skorVideo = min($videoPelayanan / 2 * 100, 100) * 0.3;
        $skorEngagement = min($avgEngagement / 100 * 100, 100) * 0.3;

        return (int) round($skorFrekuensi + $skorVideo + $skorEngagement);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
