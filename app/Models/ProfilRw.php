<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilRw extends Model
{
    use HasFactory;
    use HasUuids;

    public const TIPOLOGI_OPTIONS = [
        'perkampungan' => 'Perkampungan',
        'campuran' => 'Campuran (Kampung + Perumahan)',
        'perumahan' => 'Perumahan',
        'perkotaan' => 'Perkotaan',
        'pesisir' => 'Pesisir / Tambak',
        'industri' => 'Industri',
    ];

    public const EKONOMI_OPTIONS = [
        'pertanian' => 'Pertanian',
        'pabrik' => 'Pekerja Pabrik / Industri',
        'informal' => 'Pekerja Informal (Ojol/Freelance)',
        'pedagang' => 'Pedagang / Wiraswasta',
        'pns' => 'PNS / Karyawan',
        'nelayan' => 'Nelayan',
        'campuran' => 'Campuran',
    ];

    protected $fillable = [
        'target_wilayah_id',
        'nomor_rw',
        'dapil',
        'kecamatan',
        'desa',
        'tipologi',
        'ekonomi_dominan',
        'profil_warga',
        'suara_pks_2019',
        'faktor_penyebab',
        'anggota_pks',
        'jumlah_kta',
        'upa_rw_status',
        'upa_rw_nama',
        'rki_status',
        'rki_nama',
        'senam_status',
        'senam_nama',
        'relawan_milenial_status',
        'relawan_milenial_nama',
        'caleg_terpilih_ada',
        'caleg_terpilih_nama',
        'afiliasi_rw_rt',
        'afiliasi_posyandu_dkm',
        'kompetitor_status',
        'kompetitor_detail',
        'tim_sukses_status',
        'tim_sukses_detail',
        'strategi',
        'penanggung_jawab',
        'keterangan_lain',
        'is_complete',
        'completion_percent',
        'filled_by',
        'filled_at',
    ];

    protected function casts(): array
    {
        return [
            'caleg_terpilih_ada' => 'boolean',
            'is_complete' => 'boolean',
            'filled_at' => 'datetime',
            'suara_pks_2019' => 'integer',
            'jumlah_kta' => 'integer',
            'completion_percent' => 'integer',
            'filled_by' => 'integer',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function filledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by');
    }

    public function calculateCompletion(): int
    {
        $fields = [
            'tipologi',
            'ekonomi_dominan',
            'profil_warga',
            'faktor_penyebab',
            'anggota_pks',
            'upa_rw_status',
            'rki_status',
            'senam_status',
            'relawan_milenial_status',
            'afiliasi_rw_rt',
            'afiliasi_posyandu_dkm',
            'kompetitor_status',
            'tim_sukses_status',
            'strategi',
            'penanggung_jawab',
        ];

        $filled = 0;

        foreach ($fields as $field) {
            $value = $this->{$field};

            if ($value !== null && $value !== '' && $value !== 'belum' && $value !== 'tidak_tahu' && $value !== 0) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('is_complete', true);
    }

    public function scopeByDapil(Builder $query, string $dapil): Builder
    {
        return $query->where('dapil', $dapil);
    }
}
