<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramArahanReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_arahan_id',
        'ringkasan',
        'jumlah_korwe_terbentuk',
        'jumlah_korte_terbentuk',
        'jumlah_penggalang_terekrut',
        'evaluasi',
        'tindak_lanjut',
        'foto',
        'realisasi_anggaran',
        'rating',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_korwe_terbentuk' => 'integer',
            'jumlah_korte_terbentuk' => 'integer',
            'jumlah_penggalang_terekrut' => 'integer',
            'realisasi_anggaran' => 'decimal:2',
            'foto' => 'array',
        ];
    }

    public function programArahan(): BelongsTo
    {
        return $this->belongsTo(ProgramArahan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
