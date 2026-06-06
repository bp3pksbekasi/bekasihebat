<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pelatihan extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENIS_OPTIONS = [
        'diklat' => 'Diklat',
        'workshop' => 'Workshop',
        'seminar' => 'Seminar',
        'kajian_rutin' => 'Kajian Rutin',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'nama_pelatihan', 'jenjang_target', 'jenis',
        'tanggal_mulai', 'tanggal_selesai', 'lokasi', 'dapil_terkait',
        'instruktur', 'kapasitas', 'peserta_hadir',
        'status', 'materi', 'catatan', 'foto', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'foto' => 'array',
        ];
    }

    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(Kader::class, 'pelatihan_pesertas')
            ->withPivot('status', 'naik_jenjang');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
