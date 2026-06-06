<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tps_id',
        'political_party_id',
        'caleg_id',
        'election_year',
        'suara',
    ];

    protected function casts(): array
    {
        return [
            'election_year' => 'integer',
            'suara' => 'integer',
        ];
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }

    public function party()
    {
        return $this->belongsTo(PoliticalParty::class, 'political_party_id');
    }

    public function caleg()
    {
        return $this->belongsTo(Caleg::class);
    }

    public function scopeYear($query, int $year)
    {
        return $query->where('election_year', $year);
    }
}
