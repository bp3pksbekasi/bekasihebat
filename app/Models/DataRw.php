<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataRw extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'target_wilayah_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'dpt',
        'dpt_laki',
        'dpt_perempuan',
        'gen_z',
        'millennial',
        'gen_x',
        'boomer',
        'jumlah_rt',
        'jumlah_tps',
        'estimasi_pks',
        'estimasi_share',
        'estimasi_ranking',
        'status_wilayah',
        'prioritas_urutan',
        'target_suara_per_rw',
        'upa_rw_terbentuk',
        'nama_ketua_upa',
        'no_hp_ketua_upa',
        'tipologi_warga',
    ];

    protected function casts(): array
    {
        return [
            'dpt' => 'integer',
            'dpt_laki' => 'integer',
            'dpt_perempuan' => 'integer',
            'gen_z' => 'integer',
            'millennial' => 'integer',
            'gen_x' => 'integer',
            'boomer' => 'integer',
            'jumlah_rt' => 'integer',
            'jumlah_tps' => 'integer',
            'estimasi_pks' => 'integer',
            'estimasi_share' => 'decimal:4',
            'estimasi_ranking' => 'integer',
            'prioritas_urutan' => 'integer',
            'target_suara_per_rw' => 'integer',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    /**
     * @return array{label:string,bg:string,text:string,warna:string}
     */
    public function getStatusConfigAttribute(): array
    {
        $config = TargetWilayah::STATUS_CONFIG[$this->status_wilayah]
            ?? TargetWilayah::STATUS_CONFIG['ZONA BERAT'];

        return [
            ...$config,
            'warna' => $config['text'],
        ];
    }

    public function getTargetPenggalangAttribute(): int
    {
        if ($this->targetWilayah && $this->targetWilayah->target_suara_2029 > 0) {
            $ratio = $this->target_suara_per_rw / $this->targetWilayah->target_suara_2029;
            return (int) ceil($ratio * $this->targetWilayah->target_penggalang);
        }
        return (int) ceil($this->target_suara_per_rw / 10); // Fallback to 1:10 ratio if kelurahan target is missing
    }

    public function scopeByDesa(Builder $query, string $targetWilayahId): Builder
    {
        return $query->where('target_wilayah_id', $targetWilayahId);
    }

    public function scopeOrderByPrioritas(Builder $query): Builder
    {
        return $query
            ->orderBy('prioritas_urutan')
            ->orderByDesc('estimasi_pks');
    }
}
