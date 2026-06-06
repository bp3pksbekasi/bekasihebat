<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MateriDigital extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENIS_OPTIONS = [
        'poster' => 'Poster',
        'video' => 'Video Pendek',
        'infografis' => 'Infografis',
        'dokumen' => 'Dokumen',
        'flyer' => 'Flyer',
    ];

    protected $fillable = [
        'judul', 'jenis', 'deskripsi', 'file_path', 'thumbnail',
        'topik', 'distribusi_count', 'status', 'created_by',
    ];

    public function distribusis(): HasMany
    {
        return $this->hasMany(DistribusiMateri::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
