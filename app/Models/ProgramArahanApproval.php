<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramArahanApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_arahan_id',
        'level',
        'status',
        'approver_id',
        'catatan',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function programArahan(): BelongsTo
    {
        return $this->belongsTo(ProgramArahan::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
