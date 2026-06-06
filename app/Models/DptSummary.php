<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DptSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'tps_id',
        'rt',
        'rw',
        'total_dpt',
        'male',
        'female',
        'gen_z',
        'millennial',
        'gen_x',
        'boomer',
        'age_known',
        'age_unknown',
    ];

    protected function casts(): array
    {
        return [
            'total_dpt' => 'integer',
            'male' => 'integer',
            'female' => 'integer',
            'gen_z' => 'integer',
            'millennial' => 'integer',
            'gen_x' => 'integer',
            'boomer' => 'integer',
        ];
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }
}
