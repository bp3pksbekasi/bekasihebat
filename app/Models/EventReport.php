<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReport extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'event_id',
        'ringkasan',
        'peserta_hadir',
        'evaluasi',
        'tindak_lanjut',
        'foto',
        'realisasi_anggaran',
        'rating',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'peserta_hadir' => 'integer',
            'realisasi_anggaran' => 'decimal:2',
            'foto' => 'array',
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
}
