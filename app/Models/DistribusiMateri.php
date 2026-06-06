<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistribusiMateri extends Model
{
    use HasFactory;
    use HasUuids;

    public const CHANNEL_OPTIONS = [
        'wa_blast' => 'WA Blasting',
        'wa_grup_korwe' => 'WA Grup KORWE',
        'wa_grup_korte' => 'WA Grup KORTE',
        'medsos' => 'Media Sosial',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'materi_digital_id', 'channel', 'target_dapil',
        'target_rw_count', 'terkirim', 'terbaca',
        'tanggal_distribusi', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_distribusi' => 'date',
        ];
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(MateriDigital::class, 'materi_digital_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
