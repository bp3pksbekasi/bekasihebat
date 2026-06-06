<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Korte extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'target_wilayah_id',
        'nomor_rt',
        'nomor_rw',
        'nama_koordinator',
        'no_hp',
        'status',
        'is_saksi_tps',
        'assigned_tps',
        'status_saksi',
        'catatan',
        'tanggal_terbentuk',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbentuk' => 'date',
            'is_saksi_tps' => 'boolean',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
