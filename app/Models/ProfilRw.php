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

    protected static function booted(): void
    {
        static::saving(function (ProfilRw $profil) {
            $profil->completion_percent = $profil->calculateCompletion();
            
            // Check if required fields are filled for is_complete
            $requiredFields = ['tipologi', 'ekonomi_dominan', 'suara_pks_2019', 'kompetitor_status', 'tim_sukses_status'];
            $isComplete = true;
            foreach ($requiredFields as $field) {
                $val = $profil->{$field};
                // For string required fields, they cannot be empty string
                // For integers like suara_pks_2019, 0 is acceptable
                if ($val === null || (is_string($val) && trim($val) === '')) {
                    $isComplete = false;
                    break;
                }
            }
            $profil->is_complete = $isComplete;
        });
    }

    public const TIPOLOGI_OPTIONS = [
        'kampung_tradisional' => 'Kampung Tradisional',
        'perumahan' => 'Perumahan',
        'campuran' => 'Campuran',
        'kawasan_pekerja' => 'Kawasan Pekerja / Industri',
        'pusat_perdagangan' => 'Pusat Perdagangan',
        'pesisir_pertanian' => 'Pesisir / Pertanian',
    ];

    public const EKONOMI_OPTIONS = [
        'Buruh / Pekerja Industri',
        'Pedagang / Wiraswasta / UMKM',
        'Pekerja Informal / Harian',
        'Pegawai Swasta / Perkantoran',
        'ASN / TNI / Polri / BUMD',
        'Petani / Buruh Tani',
        'Nelayan / Petambak',
    ];

    public const PROFIL_OPTIONS = [
        'A. Religius-Komunal' => [
            'Aktif pengajian',
            'Tokoh agama berpengaruh',
            'Mudah digerakkan secara kolektif'
        ],
        'B. Nasionalis-Tradisional' => [
            'Menghormati tokoh lokal',
            'Kedekatan sosial kuat',
            'Loyalitas cukup tinggi'
        ],
        'C. Rasional-Kritis' => [
            'Banyak ASN, guru, sarjana',
            'Memerlukan data dan program nyata'
        ],
        'D. Pragmatis-Ekonomi' => [
            'Isu lapangan kerja dan bantuan ekonomi dominan',
            'Responsif terhadap manfaat langsung'
        ],
        'E. Urban-Individual' => [
            'Pendatang tinggi',
            'Interaksi sosial rendah',
            'Komunikasi digital lebih efektif'
        ],
        'F. Kelas Pekerja' => [
            'Buruh pabrik, pegawai, pekerja informal',
            'Isu upah, kesehatan, pendidikan anak menjadi perhatian utama'
        ]
    ];

    public const FAKTOR_OPTIONS = [
        'A. Faktor Pendorong Suara (Kekuatan Kita)' => [
            'Figur Caleg Lokal (Putra Daerah)',
            'Ketokohan Agama/Masyarakat yang Mendukung',
            'Jejaring Struktur / Kader PKS yang Solid',
            'Gerakan Relawan yang Masif',
            'Program Kerja & Advokasi Nyata (Bansos, Fogging, dll)'
        ],
        'B. Faktor Penghambat Suara (Kelemahan/Tantangan)' => [
            'Pragmatisme / Serangan Fajar (Politik Uang Lawan)',
            'Dominasi / Basis Kuat Partai Lain',
            'Kurangnya Sosialisasi Caleg PKS',
            'Mesin Partai / Struktur PKS Tidak Jalan',
            'Intimidasi / Tekanan Pihak Tertentu (Aparat/Preman)'
        ]
    ];

    public const STRATEGI_OPTIONS = [
        'sapa_warga' => 'Sapa Warga & Door to Door',
        'layanan_sosial' => 'Penyediaan Layanan Sosial',
        'tokoh_kunci' => 'Penguatan Tokoh Kunci',
        'event_komunitas' => 'Event / Kegiatan Komunitas',
        'kampanye_digital' => 'Kampanye Digital & Media Sosial',
        'pengawalan_suara' => 'Penguatan Saksi & Pengawalan Suara',
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
        'profil_warga_keterangan',
        'suara_pks_2019',
        'faktor_penyebab',
        'faktor_penyebab_keterangan',
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
        'partai_dominan',
        'afiliasi_ketua_rw',
        'afiliasi_mayoritas_rt',
        'afiliasi_tomas',
        'afiliasi_toga',
        'afiliasi_pemuda',
        'kompetitor_status',
        'kompetitor_detail',
        'tim_sukses_status',
        'tim_sukses_detail',
        'strategi',
        'strategi_keterangan',
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
            'partai_dominan',
            'afiliasi_ketua_rw',
            'afiliasi_mayoritas_rt',
            'afiliasi_tomas',
            'afiliasi_toga',
            'afiliasi_pemuda',
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
