<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LogSesi extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'tanggal_sesi',
        'jumlah_peserta',
        'pelaksana',
        'catatan',
        'foto',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sesi' => 'datetime',
            'foto' => 'array',
        ];
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
