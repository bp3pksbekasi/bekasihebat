<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RwProfileSubmission extends Model
{
    protected $fillable = [
        'data_rw_id',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'nama_pengisi',
        'no_hp_pengisi',
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
        'tipologi', 'ekonomi_dominan', 'profil_warga', 'profil_warga_keterangan', 'suara_pks_2019', 'faktor_penyebab', 'faktor_penyebab_keterangan',
        'anggota_pks', 'jumlah_kta', 'upa_rw_status', 'upa_rw_nama', 'rki_status', 'rki_nama',
        'senam_status', 'senam_nama', 'relawan_milenial_status', 'relawan_milenial_nama',
        'caleg_terpilih_ada', 'caleg_terpilih_nama', 'afiliasi_rw_rt', 'afiliasi_posyandu_dkm',
        'partai_dominan', 'afiliasi_ketua_rw', 'afiliasi_mayoritas_rt', 'afiliasi_tomas', 'afiliasi_toga', 'afiliasi_pemuda',
        'kompetitor_status', 'kompetitor_detail', 'tim_sukses_status', 'tim_sukses_detail',
        'strategi', 'strategi_keterangan', 'penanggung_jawab', 'keterangan_lain',
        'status',
    ];

    public function dataRw()
    {
        return $this->belongsTo(DataRw::class, 'data_rw_id');
    }
}
