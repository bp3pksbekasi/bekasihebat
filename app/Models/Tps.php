<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tps extends Model
{
    use HasFactory;

    protected $table = 'tps';

    protected $fillable = [
        'code',
        'dapil_id',
        'kecamatan_code',
        'kelurahan_code',
        'tps_number',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function dapil()
    {
        return $this->belongsTo(Dapil::class);
    }

    public function dptSummaries()
    {
        return $this->hasMany(DptSummary::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }

    public function getTotalDptAttribute(): int
    {
        return (int) $this->dptSummaries()->sum('total_dpt');
    }

    public function getTotalSuaraAttribute(): int
    {
        return (int) $this->voteResults()->sum('suara');
    }
}
