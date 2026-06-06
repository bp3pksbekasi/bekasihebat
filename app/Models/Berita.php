<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;
    use HasUuids;

    public const KATEGORI_OPTIONS = [
        'kegiatan' => 'Kegiatan',
        'program' => 'Program',
        'dakwah' => 'Dakwah',
        'opini' => 'Opini',
        'pengumuman' => 'Pengumuman',
    ];

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'thumbnail',
        'kategori',
        'penulis',
        'is_featured',
        'is_published',
        'published_at',
        'views',
        'event_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'views' => 'integer',
            'event_id' => 'integer',
            'created_by' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $berita): void {
            if ($berita->slug) {
                return;
            }

            $base = Str::slug($berita->judul) ?: 'berita';
            $slug = $base;
            $i = 1;

            while (static::query()->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i;
                $i++;
            }

            $berita->slug = $slug;
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }
}
