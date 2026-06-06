<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RencanaAksi extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'wilayah_key',
        'dapil',
        'kecamatan',
        'desa',
        'rw',
        'status_wilayah',
        'program_index',
        'program_nama',
        'program_kategori',
        'target',
        'deadline',
        'pic',
        'status_pelaksanaan',
        'catatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeByDapil(Builder $query, string $dapil): Builder
    {
        return $query->where('dapil', $dapil);
    }

    public function scopeByWilayah(Builder $query, string $wilayahKey): Builder
    {
        return $query->where('wilayah_key', $wilayahKey);
    }

    public function scopeByStatus(Builder $query, string $statusWilayah): Builder
    {
        return $query->where('status_wilayah', $statusWilayah);
    }

    // TODO UPGRADE KE OPSI A:
    // 1. php artisan migrate
    // 2. Buat RencanaAksiController dengan API endpoints:
    //    - GET /api/rencana-aksi?dapil=BEKASI+1 -> load semua aksi per dapil
    //    - POST /api/rencana-aksi -> simpan/update status program
    //    - GET /api/rencana-aksi/export?dapil=BEKASI+1 -> export CSV server-side
    // 3. Update JS di rencana-aksi.blade.php:
    //    - Saat drawer dibuka -> GET /api/rencana-aksi?wilayah_key=xxx
    //    - Saat status di-klik -> POST /api/rencana-aksi
    //    - Fallback ke aksiConfig jika API gagal
    // 4. Data persisten - tidak hilang saat refresh
    // 5. Buat dashboard progress per dapil/kecamatan (server-side aggregation)
}
