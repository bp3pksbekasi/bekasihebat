<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontenMedsos extends Model
{
    use HasFactory;
    use HasUuids;

    public const PLATFORM_OPTIONS = [
        'instagram' => ['label' => 'Instagram', 'icon' => 'brand-instagram', 'color' => '#e1306c'],
        'tiktok' => ['label' => 'TikTok', 'icon' => 'brand-tiktok', 'color' => '#000'],
        'youtube' => ['label' => 'YouTube', 'icon' => 'brand-youtube', 'color' => '#ff0000'],
        'twitter' => ['label' => 'Twitter/X', 'icon' => 'brand-twitter', 'color' => '#1da1f2'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'brand-facebook', 'color' => '#1877f2'],
    ];

    public const JENIS_KONTEN_OPTIONS = [
        'reels' => 'Reels',
        'post' => 'Post/Feed',
        'story' => 'Story',
        'video' => 'Video',
        'short' => 'Short',
        'tweet' => 'Tweet',
        'live' => 'Live',
    ];

    public const TOPIK_OPTIONS = [
        'pelayanan' => 'Pelayanan Warga',
        'reses' => 'Reses',
        'aspirasi' => 'Aspirasi',
        'edukasi' => 'Edukasi',
        'campaign' => 'Campaign/Sosialisasi',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'anggota_dewan_id', 'platform', 'jenis_konten', 'caption', 'url',
        'tanggal_posting', 'likes', 'comments', 'shares', 'views',
        'topik', 'dapil_terkait', 'rw_terkait', 'desa_terkait',
        'is_video_pelayanan', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_posting' => 'date',
            'is_video_pelayanan' => 'boolean',
        ];
    }

    public function anggotaDewan(): BelongsTo
    {
        return $this->belongsTo(AnggotaDewan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPlatformConfigAttribute(): array
    {
        return self::PLATFORM_OPTIONS[$this->platform] ?? ['label' => $this->platform, 'icon' => 'world', 'color' => '#888'];
    }

    public function getTotalEngagementAttribute(): int
    {
        return (int) $this->likes + (int) $this->comments + (int) $this->shares;
    }

    public function scopeByPlatform($query, $value)
    {
        return $query->where('platform', $value);
    }

    public function scopeVideoPelayanan($query)
    {
        return $query->where('is_video_pelayanan', true);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_posting', now()->month)->whereYear('tanggal_posting', now()->year);
    }
}
