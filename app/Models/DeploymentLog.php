<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentLog extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'kader_id', 'dari_dapil', 'dari_kecamatan', 'dari_desa', 'dari_rw',
        'ke_dapil', 'ke_kecamatan', 'ke_desa', 'ke_rw',
        'alasan', 'tanggal_deploy', 'status', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_deploy' => 'date',
        ];
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(Kader::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
