<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Galeri extends Model
{
    use HasFactory;
    use HasUuids;

    public const KATEGORI_OPTIONS = [
        'kegiatan' => 'Kegiatan',
        'event' => 'Event',
        'baksos' => 'Baksos',
        'senam' => 'Senam PKS',
        'rki' => 'RKI',
        'dakwah' => 'Dakwah',
    ];

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'thumbnail',
        'tipe',
        'kategori',
        'lokasi',
        'tanggal',
        'is_featured',
        'is_published',
        'urutan',
        'event_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'urutan' => 'integer',
            'event_id' => 'integer',
            'created_by' => 'integer',
        ];
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
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
