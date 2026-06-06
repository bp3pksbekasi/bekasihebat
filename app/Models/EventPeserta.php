<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPeserta extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'event_id',
        'nama',
        'no_hp',
        'no_wa',
        'alamat',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nomor_rt',
        'target_wilayah_id',
        'kader_id',
        'metode',
        'synced_sapa_warga',
        'kontak_warga_id',
        'catatan',
        'aspirasi',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'synced_sapa_warga' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(Kader::class);
    }

    public function kontakWarga(): BelongsTo
    {
        return $this->belongsTo(KontakWarga::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
