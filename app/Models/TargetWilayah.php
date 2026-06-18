<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TargetWilayah extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_CONFIG = [
        'SANGAT KUAT' => [
            'label' => 'Sangat Kuat',
            'bg' => '#dcfce7',
            'text' => '#166534',
        ],
        'KUAT' => [
            'label' => 'Kuat',
            'bg' => '#dbeafe',
            'text' => '#1d4ed8',
        ],
        'POTENSIAL' => [
            'label' => 'Potensial',
            'bg' => '#fef3c7',
            'text' => '#b45309',
        ],
        'RAWAN' => [
            'label' => 'Rawan',
            'bg' => '#ffedd5',
            'text' => '#c2410c',
        ],
        'ZONA BERAT' => [
            'label' => 'Zona Berat',
            'bg' => '#f3f4f6',
            'text' => '#6b7280',
        ],
    ];

    protected $fillable = [
        'dapil',
        'kecamatan',
        'desa',
        'jumlah_rw',
        'jumlah_rt',
        'jumlah_tps',
        'jumlah_dpt',
        'suara_pks_2024',
        'ranking_pks',
        'persentase_pks',
        'target_suara_2029',
        'kekurangan_suara',
        'target_korwe_2026',
        'target_korwe_2027',
        'target_korwe_2028',
        'target_korwe_2029',
        'target_korte_2026',
        'target_korte_2027',
        'target_korte_2028',
        'target_korte_2029',
        'target_penggalang',
        'target_penggalang_2026',
        'target_penggalang_2027',
        'target_penggalang_2028',
        'target_penggalang_2029',
        'target_avg_per_rw',
        'target_avg_per_rt',
        'target_avg_per_tps',
        'target_avg_per_rumah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_rw' => 'integer',
            'jumlah_rt' => 'integer',
            'jumlah_tps' => 'integer',
            'jumlah_dpt' => 'integer',
            'suara_pks_2024' => 'integer',
            'ranking_pks' => 'integer',
            'persentase_pks' => 'decimal:4',
            'target_suara_2029' => 'integer',
            'kekurangan_suara' => 'integer',
            'target_korwe_2026' => 'integer',
            'target_korwe_2027' => 'integer',
            'target_korwe_2028' => 'integer',
            'target_korwe_2029' => 'integer',
            'target_korte_2026' => 'integer',
            'target_korte_2027' => 'integer',
            'target_korte_2028' => 'integer',
            'target_korte_2029' => 'integer',
            'target_penggalang' => 'integer',
            'target_penggalang_2026' => 'integer',
            'target_penggalang_2027' => 'integer',
            'target_penggalang_2028' => 'integer',
            'target_penggalang_2029' => 'integer',
            'target_avg_per_rw' => 'decimal:2',
            'target_avg_per_rt' => 'decimal:2',
            'target_avg_per_tps' => 'decimal:2',
            'target_avg_per_rumah' => 'decimal:2',
        ];
    }

    public function korwes(): HasMany
    {
        return $this->hasMany(Korwe::class);
    }

    public function kortes(): HasMany
    {
        return $this->hasMany(Korte::class);
    }

    public function dataRws(): HasMany
    {
        return $this->hasMany(DataRw::class);
    }

    public function profilRws(): HasMany
    {
        return $this->hasMany(ProfilRw::class);
    }

    public function kegiatanRws(): HasMany
    {
        return $this->hasMany(KegiatanRw::class);
    }

    public function penggalangSuaras(): HasMany
    {
        return $this->hasMany(PenggalangSuara::class);
    }

    public function upaRwMembers(): HasMany
    {
        return $this->hasMany(UpaRwMember::class);
    }

    public function kontakWargas(): HasMany
    {
        return $this->hasMany(KontakWarga::class);
    }

    public function titikRkis(): HasMany
    {
        return $this->hasMany(TitikRki::class);
    }

    public function titikSenams(): HasMany
    {
        return $this->hasMany(TitikSenam::class);
    }

    public function scopeByDapil(Builder $query, string $dapil): Builder
    {
        return $query->where('dapil', $dapil);
    }

    public function scopeByKecamatan(Builder $query, string $kecamatan): Builder
    {
        return $query->where('kecamatan', $kecamatan);
    }

    public function getProgressKorweAttribute(): float
    {
        $target = (int) $this->target_korwe_2029;

        if ($target <= 0) {
            return 0.0;
        }

        $terbentuk = $this->korwes()
            ->where('status', 'terbentuk')
            ->count();

        return round(($terbentuk / $target) * 100, 2);
    }

    public function getProgressKorteAttribute(): float
    {
        $target = (int) $this->target_korte_2029;

        if ($target <= 0) {
            return 0.0;
        }

        $terbentuk = $this->kortes()
            ->where('status', 'terbentuk')
            ->count();

        return round(($terbentuk / $target) * 100, 2);
    }

    /**
     * @return array{status:string,prioritas:int}
     */
    public static function classifyStatus(int $estimasiPks, int $ranking, float $share): array
    {
        if ($ranking <= 1 && $share >= 0.14 && $estimasiPks >= 35) {
            return ['status' => 'SANGAT KUAT', 'prioritas' => 1];
        }

        if ($ranking <= 2 && $share >= 0.1 && $estimasiPks >= 20) {
            return ['status' => 'KUAT', 'prioritas' => 2];
        }

        if ($ranking <= 3 && $share >= 0.07 && $estimasiPks >= 12) {
            return ['status' => 'POTENSIAL', 'prioritas' => 3];
        }

        if ($ranking <= 5 && $share >= 0.04 && $estimasiPks >= 6) {
            return ['status' => 'RAWAN', 'prioritas' => 4];
        }

        return ['status' => 'ZONA BERAT', 'prioritas' => 5];
    }
}
