<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProgramArahanPersonel extends Model
{
    use HasFactory;

    protected $table = 'program_arahan_personel';

    protected $fillable = [
        'program_arahan_id',
        'infra_type',
        'infra_id',
    ];

    public function programArahan(): BelongsTo
    {
        return $this->belongsTo(ProgramArahan::class);
    }

    public function infra()
    {
        // Polimorfik manual
        switch ($this->infra_type) {
            case 'korwe':
                return $this->belongsTo(Korwe::class, 'infra_id');
            case 'korte':
                return $this->belongsTo(Korte::class, 'infra_id');
            case 'penggalang':
                return $this->belongsTo(PenggalangSuara::class, 'infra_id');
            default:
                return null;
        }
    }
}
