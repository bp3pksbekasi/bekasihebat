<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caleg extends Model
{
    use HasFactory;

    protected $fillable = [
        'political_party_id',
        'dapil_id',
        'nomor_urut',
        'nama',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
            'gender' => UserGender::class,
        ];
    }

    public function party()
    {
        return $this->belongsTo(PoliticalParty::class, 'political_party_id');
    }

    public function dapil()
    {
        return $this->belongsTo(Dapil::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }
}
