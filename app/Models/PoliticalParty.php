<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliticalParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_urut',
        'code',
        'name',
        'full_name',
        'color_hex',
        'is_tracked',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
            'is_tracked' => 'boolean',
        ];
    }

    public function calegs()
    {
        return $this->hasMany(Caleg::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }

    public function scopeTracked($query)
    {
        return $query->where('is_tracked', true);
    }
}
