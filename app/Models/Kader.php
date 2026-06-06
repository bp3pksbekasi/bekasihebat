<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kader extends Model
{
    use HasFactory;
    use HasUuids;

    public const JENJANG_OPTIONS = [
        'penggerak' => ['label' => 'Penggerak', 'color' => '#93c5fd', 'gradient' => 'linear-gradient(135deg,#bfdbfe 0%,#93c5fd 100%)', 'text' => '#1e3a5f', 'order' => 1],
        'pendukung' => ['label' => 'Pendukung', 'color' => '#60a5fa', 'gradient' => 'linear-gradient(135deg,#93c5fd 0%,#60a5fa 100%)', 'text' => '#1e3a5f', 'order' => 2],
        'pelopor' => ['label' => 'Pelopor', 'color' => '#3b82f6', 'gradient' => 'linear-gradient(135deg,#60a5fa 0%,#3b82f6 100%)', 'text' => 'white', 'order' => 3],
        'madya' => ['label' => 'Madya', 'color' => '#2563eb', 'gradient' => 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)', 'text' => 'white', 'order' => 4],
        'dewasa' => ['label' => 'Dewasa', 'color' => '#1e40af', 'gradient' => 'linear-gradient(135deg,#2563eb 0%,#1e40af 100%)', 'text' => 'white', 'order' => 5],
        'purna' => ['label' => 'Purna', 'color' => '#1e3a5f', 'gradient' => 'linear-gradient(135deg,#1e40af 0%,#1e3a5f 100%)', 'text' => 'white', 'order' => 6],
    ];

    public const KEAHLIAN_OPTIONS = [
        'public_speaking' => 'Public Speaking',
        'medsos' => 'Media Sosial',
        'organisasi' => 'Organisasi',
        'dakwah' => 'Dakwah',
        'pendidikan' => 'Pendidikan',
        'kesehatan' => 'Kesehatan',
        'ekonomi' => 'Ekonomi/UMKM',
        'teknologi' => 'Teknologi',
    ];

    protected $fillable = [
        'nama', 'no_hp', 'no_wa', 'email', 'nik', 'no_kta', 'nia', 'bidang_slug',
        'jenjang', 'tanggal_jenjang',
        'dapil', 'kecamatan', 'desa', 'nomor_rw', 'nomor_rt', 'target_wilayah_id',
        'is_korwe', 'is_korte', 'is_upa', 'jabatan_upa', 'is_penggalang', 'is_saksi',
        'keahlian', 'bisa_deploy', 'status', 'is_activated', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_jenjang' => 'date',
            'keahlian' => 'array',
            'is_korwe' => 'boolean',
            'is_korte' => 'boolean',
            'is_upa' => 'boolean',
            'is_penggalang' => 'boolean',
            'is_saksi' => 'boolean',
            'bisa_deploy' => 'boolean',
            'is_activated' => 'boolean',
        ];
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pelatihans(): BelongsToMany
    {
        return $this->belongsToMany(Pelatihan::class, 'pelatihan_pesertas')
            ->withPivot('status', 'naik_jenjang');
    }

    public function deploymentLogs(): HasMany
    {
        return $this->hasMany(DeploymentLog::class);
    }

    public function getJenjangConfigAttribute(): array
    {
        return self::JENJANG_OPTIONS[$this->jenjang] ?? self::JENJANG_OPTIONS['penggerak'];
    }

    public function getRolesAttribute(): array
    {
        $roles = [];

        if ($this->is_korwe) {
            $roles[] = 'KORWE';
        }
        if ($this->is_korte) {
            $roles[] = 'KORTE';
        }
        if ($this->is_upa) {
            $roles[] = 'UPA '.($this->jabatan_upa ?? '');
        }
        if ($this->is_penggalang) {
            $roles[] = 'Penggalang';
        }
        if ($this->is_saksi) {
            $roles[] = 'Saksi TPS';
        }

        return $roles;
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByDapil($query, $value)
    {
        return $query->where('dapil', $value);
    }

    public function scopeByJenjang($query, $value)
    {
        return $query->where('jenjang', $value);
    }

    public function scopeDeployable($query)
    {
        return $query->where('bisa_deploy', true)->where('status', 'aktif');
    }
}
