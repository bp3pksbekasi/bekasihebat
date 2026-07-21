<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramArahanBudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_arahan_id',
        'item',
        'kategori',
        'qty',
        'satuan',
        'harga_satuan',
        'subtotal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function programArahan(): BelongsTo
    {
        return $this->belongsTo(ProgramArahan::class);
    }
}
