<?php

namespace App\Livewire\Public;

use App\Models\DataRw;
use App\Models\TargetWilayah;
use App\Models\RwProfileSubmission;
use Livewire\Component;

class RwProfileForm extends Component
{
    public $dapilOptions = [];
    public $kecamatanOptions = [];
    public $desaOptions = [];
    public $rwOptions = [];

    public $dapil = '';
    public $kecamatan = '';
    public $desa = '';
    public $data_rw_id = '';

    public $nama_pengisi = '';
    public $no_hp_pengisi = '';

    public $dpt;
    public $dpt_laki;
    public $dpt_perempuan;
    public $jumlah_rt;
    public $jumlah_tps;
    public $estimasi_pks;
    
    public $gen_z;
    public $millennial;
    public $gen_x;
    public $boomer;
    public $estimasi_share;
    public $estimasi_ranking;
    public $status_wilayah;
    public $prioritas_urutan;
    public $target_suara_per_rw;

    // UI fields for splitting Name and Phone
    public $upa_rw_nama_input = '';
    public $upa_rw_no_hp_input = '';
    public $rki_nama_input = '';
    public $rki_no_hp_input = '';

    // Profil RW Fields
    public $tipologi;
    public array $ekonomi_dominan = [];
    public array $profil_warga = [];
    public $profil_warga_keterangan = '';
    public $suara_pks_2019;
    public array $faktor_penyebab = [];
    public $faktor_penyebab_keterangan = '';
    public $anggota_pks = '';
    public $jumlah_kta;
    public $upa_rw_status = 'belum';
    public $rki_status = 'belum';
    public $senam_status = 'belum';
    public $senam_nama_input = '';
    public $senam_no_hp_input = '';
    public $relawan_milenial_status = 'belum';
    public $relawan_milenial_nama_input = '';
    public $relawan_milenial_no_hp_input = '';
    public $caleg_terpilih_ada = 0;
    public $caleg_terpilih_nama = '';
    public $afiliasi_rw_rt = '';
    public $afiliasi_posyandu_dkm = '';
    public $partai_dominan = '';
    public $afiliasi_ketua_rw = '';
    public $afiliasi_mayoritas_rt = '';
    public $afiliasi_tomas = '';
    public $afiliasi_toga = '';
    public $afiliasi_pemuda = '';
    public $kompetitor_status = 'tidak_tahu';
    public $kompetitor_detail = '';
    public $tim_sukses_status = 'tidak_tahu';
    public $tim_sukses_detail = '';
    public $strategi = '';
    public $strategi_keterangan = '';
    public $penanggung_jawab = '';
    public $keterangan_lain = '';

    public $isSubmitted = false;

    public function mount()
    {
        $this->dapilOptions = TargetWilayah::select('dapil')->distinct()->pluck('dapil')->toArray();
    }

    public function updatedDapil()
    {
        $this->kecamatan = '';
        $this->desa = '';
        $this->data_rw_id = '';
        
        $this->kecamatanOptions = TargetWilayah::where('dapil', $this->dapil)
            ->select('kecamatan')->distinct()->pluck('kecamatan')->toArray();
    }

    public function updatedKecamatan()
    {
        $this->desa = '';
        $this->data_rw_id = '';
        
        $this->desaOptions = TargetWilayah::where('kecamatan', $this->kecamatan)
            ->select('desa')->distinct()->pluck('desa')->toArray();
    }

    public function updatedDesa()
    {
        $this->data_rw_id = '';
        
        $this->rwOptions = DataRw::where('desa', $this->desa)
            ->select('id', 'nomor_rw')->get()->toArray();
    }

    public function updatedDataRwId()
    {
        if ($this->data_rw_id) {
            $rw = DataRw::find($this->data_rw_id);
            if ($rw) {
                $this->dpt = $rw->dpt;
                $this->dpt_laki = $rw->dpt_laki;
                $this->dpt_perempuan = $rw->dpt_perempuan;
                $this->jumlah_rt = $rw->jumlah_rt;
                $this->jumlah_tps = $rw->jumlah_tps;
                $this->estimasi_pks = $rw->estimasi_pks;
                
                $this->gen_z = $rw->gen_z;
                $this->millennial = $rw->millennial;
                $this->gen_x = $rw->gen_x;
                $this->boomer = $rw->boomer;
                $this->estimasi_share = $rw->estimasi_share;
                $this->estimasi_ranking = $rw->estimasi_ranking;
                $this->status_wilayah = $rw->status_wilayah;
                $this->prioritas_urutan = $rw->prioritas_urutan;
                $this->target_suara_per_rw = $rw->target_suara_per_rw;

                // Load existing Profil RW if exists
                $profil = \App\Models\ProfilRw::where('target_wilayah_id', $rw->target_wilayah_id)
                    ->where('nomor_rw', $rw->nomor_rw)
                    ->first();
                    
                if ($profil) {
                    $tipologiMap = [
                        'perkampungan' => 'kampung_tradisional',
                        'perkotaan' => 'pusat_perdagangan',
                        'pesisir' => 'pesisir_pertanian',
                        'industri' => 'kawasan_pekerja',
                    ];
                    $this->tipologi = $tipologiMap[$profil->tipologi] ?? $profil->tipologi;
                    
                    $ekonomiMap = [
                        'pertanian' => 'Petani / Buruh Tani',
                        'pabrik' => 'Buruh / Pekerja Industri',
                        'informal' => 'Pekerja Informal / Harian',
                        'pedagang' => 'Pedagang / Wiraswasta / UMKM',
                        'pns' => 'ASN / TNI / Polri / BUMD',
                        'nelayan' => 'Nelayan / Petambak',
                        'campuran' => 'Pegawai Swasta / Perkantoran',
                    ];
                    
                    $this->ekonomi_dominan = [];
                    $dbEkonomi = $profil->ekonomi_dominan ?? '';
                    
                    // Old single value
                    if (isset($ekonomiMap[$dbEkonomi])) {
                        $this->ekonomi_dominan[] = $ekonomiMap[$dbEkonomi];
                    } else {
                        // Comma separated string mapping
                        foreach (\App\Models\ProfilRw::EKONOMI_OPTIONS as $ekoOption) {
                            if ($dbEkonomi && str_contains($dbEkonomi, $ekoOption)) {
                                $this->ekonomi_dominan[] = $ekoOption;
                            }
                        }
                    }
                    $oldMapping = [
                        'Agamis & Kondusif' => 'Aktif pengajian, Tokoh agama berpengaruh, Mudah digerakkan secara kolektif',
                        'Pragmatis & Transaksional' => 'Isu lapangan kerja dan bantuan ekonomi dominan, Responsif terhadap manfaat langsung',
                        'Nasionalis & Abangan' => 'Menghormati tokoh lokal, Kedekatan sosial kuat, Loyalitas cukup tinggi',
                        'Heterogen & Individualis' => 'Pendatang tinggi, Interaksi sosial rendah, Komunikasi digital lebih efektif',
                        'Kritis & Akademis' => 'Banyak ASN, guru, sarjana, Memerlukan data dan program nyata',
                        'Buruh & Pekerja' => 'Buruh pabrik, pegawai, pekerja informal, Isu upah, kesehatan, pendidikan anak menjadi perhatian utama',
                    ];
                    
                    $dbValue = $profil->profil_warga ?? '';
                    
                    // Convert old values in memory if they exist
                    foreach ($oldMapping as $old => $new) {
                        if (str_contains($dbValue, $old)) {
                            $dbValue = str_replace($old, $new, $dbValue);
                        }
                    }

                    foreach(\App\Models\ProfilRw::PROFIL_OPTIONS as $kategori => $options) {
                        foreach($options as $label) {
                            if ($dbValue && str_contains($dbValue, $label)) {
                                $this->profil_warga[] = $label;
                            }
                        }
                    }
                    
                    $this->profil_warga_keterangan = $profil->profil_warga_keterangan;
                    $this->suara_pks_2019 = $profil->suara_pks_2019;
                    
                    $this->faktor_penyebab = [];
                    $dbFaktor = $profil->faktor_penyebab ?? '';
                    $faktorMap = [
                        'Kekuatan Caleg Lokal' => 'Figur Caleg Lokal (Putra Daerah)',
                        'Ketokohan Tokoh Agama/Masyarakat' => 'Ketokohan Agama/Masyarakat yang Mendukung',
                        'Program Kerja & Bantuan Nyata' => 'Program Kerja & Advokasi Nyata (Bansos, Fogging, dll)',
                        'Pragmatisme Politik Uang' => 'Pragmatisme / Serangan Fajar (Politik Uang Lawan)',
                        'Keaktifan Kader & Relawan' => 'Jejaring Struktur / Kader PKS yang Solid', // Approximated
                        'Kurangnya Sosialisasi/Kehadiran' => 'Kurangnya Sosialisasi Caleg PKS',
                        'Dominasi Partai Lain' => 'Dominasi / Basis Kuat Partai Lain',
                    ];
                    
                    if (isset($faktorMap[$dbFaktor])) {
                        $this->faktor_penyebab[] = $faktorMap[$dbFaktor];
                    } else {
                        foreach (\App\Models\ProfilRw::FAKTOR_OPTIONS as $kategori => $options) {
                            foreach ($options as $label) {
                                if ($dbFaktor && str_contains($dbFaktor, $label)) {
                                    $this->faktor_penyebab[] = $label;
                                }
                            }
                        }
                    }
                    
                    $this->faktor_penyebab_keterangan = $profil->faktor_penyebab_keterangan;
                    $this->anggota_pks = $profil->anggota_pks;
                    $this->jumlah_kta = $profil->jumlah_kta;
                    
                    $this->upa_rw_status = $profil->upa_rw_status;
                    if($profil->upa_rw_nama) {
                        $parts = explode(' - ', $profil->upa_rw_nama);
                        $this->upa_rw_nama_input = $parts[0] ?? '';
                        $this->upa_rw_no_hp_input = $parts[1] ?? '';
                    }

                    $this->rki_status = $profil->rki_status;
                    if($profil->rki_nama) {
                        $parts = explode(' - ', $profil->rki_nama);
                        $this->rki_nama_input = $parts[0] ?? '';
                        $this->rki_no_hp_input = $parts[1] ?? '';
                    }

                    $this->senam_status = $profil->senam_status;
                    if($profil->senam_nama) {
                        $parts = explode(' - ', $profil->senam_nama);
                        $this->senam_nama_input = $parts[0] ?? '';
                        $this->senam_no_hp_input = $parts[1] ?? '';
                    }

                    $this->relawan_milenial_status = $profil->relawan_milenial_status;
                    if($profil->relawan_milenial_nama) {
                        $parts = explode(' - ', $profil->relawan_milenial_nama);
                        $this->relawan_milenial_nama_input = $parts[0] ?? '';
                        $this->relawan_milenial_no_hp_input = $parts[1] ?? '';
                    }

                    $this->caleg_terpilih_ada = $profil->caleg_terpilih_ada ? 1 : 0;
                    $this->caleg_terpilih_nama = $profil->caleg_terpilih_nama;
                    $this->afiliasi_rw_rt = $profil->afiliasi_rw_rt;
                    $this->afiliasi_posyandu_dkm = $profil->afiliasi_posyandu_dkm;
                    $this->partai_dominan = $profil->partai_dominan;
                    $this->afiliasi_ketua_rw = $profil->afiliasi_ketua_rw;
                    $this->afiliasi_mayoritas_rt = $profil->afiliasi_mayoritas_rt;
                    $this->afiliasi_tomas = $profil->afiliasi_tomas;
                    $this->afiliasi_toga = $profil->afiliasi_toga;
                    $this->afiliasi_pemuda = $profil->afiliasi_pemuda;
                    $this->kompetitor_status = $profil->kompetitor_status;
                    $this->kompetitor_detail = $profil->kompetitor_detail;
                    $this->tim_sukses_status = $profil->tim_sukses_status;
                    $this->tim_sukses_detail = $profil->tim_sukses_detail;
                    $this->strategi = $profil->strategi;
                    $this->strategi_keterangan = $profil->strategi_keterangan;
                    $this->penanggung_jawab = $profil->penanggung_jawab;
                    $this->keterangan_lain = $profil->keterangan_lain;
                }
            }
        } else {
            $this->resetFields();
        }
    }

    private function resetFields()
    {
        $this->dpt = null;
        $this->dpt_laki = null;
        $this->dpt_perempuan = null;
        $this->jumlah_rt = null;
        $this->jumlah_tps = null;
        $this->estimasi_pks = null;
        
        $this->gen_z = null;
        $this->millennial = null;
        $this->gen_x = null;
        $this->boomer = null;
        $this->estimasi_share = null;
        $this->estimasi_ranking = null;
        $this->status_wilayah = null;
        $this->prioritas_urutan = null;
        $this->target_suara_per_rw = null;

        $this->tipologi = '';
        $this->ekonomi_dominan = '';
        $this->profil_warga = [];
        $this->profil_warga_keterangan = '';
        $this->suara_pks_2019 = null;
        $this->faktor_penyebab = '';
        $this->faktor_penyebab_keterangan = '';
        $this->anggota_pks = '';
        $this->jumlah_kta = null;
        $this->upa_rw_status = 'belum';
        $this->upa_rw_nama_input = '';
        $this->upa_rw_no_hp_input = '';
        $this->rki_status = 'belum';
        $this->rki_nama_input = '';
        $this->rki_no_hp_input = '';
        $this->senam_status = 'belum';
        $this->senam_nama_input = '';
        $this->senam_no_hp_input = '';
        $this->relawan_milenial_status = 'belum';
        $this->relawan_milenial_nama_input = '';
        $this->relawan_milenial_no_hp_input = '';
        $this->caleg_terpilih_ada = 0;
        $this->caleg_terpilih_nama = '';
        $this->afiliasi_rw_rt = '';
        $this->afiliasi_posyandu_dkm = '';
        $this->partai_dominan = '';
        $this->afiliasi_ketua_rw = '';
        $this->afiliasi_mayoritas_rt = '';
        $this->afiliasi_tomas = '';
        $this->afiliasi_toga = '';
        $this->afiliasi_pemuda = '';
        $this->kompetitor_status = 'tidak_tahu';
        $this->kompetitor_detail = '';
        $this->tim_sukses_status = 'tidak_tahu';
        $this->tim_sukses_detail = '';
        $this->strategi = '';
        $this->strategi_keterangan = '';
        $this->penanggung_jawab = '';
        $this->keterangan_lain = '';
    }

    public function submit()
    {
        $this->validate([
            'dapil' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'data_rw_id' => 'required',
            'nama_pengisi' => 'required|string|max:255',
            'no_hp_pengisi' => 'required|string|max:20',
            'dpt' => 'nullable|integer',
            'dpt_laki' => 'nullable|integer',
            'dpt_perempuan' => 'nullable|integer',
            'jumlah_rt' => 'nullable|integer',
            'jumlah_tps' => 'nullable|integer',
            'estimasi_pks' => 'nullable|integer',
            'gen_z' => 'nullable|integer',
            'millennial' => 'nullable|integer',
            'gen_x' => 'nullable|integer',
            'boomer' => 'nullable|integer',
            'estimasi_share' => 'nullable|string|max:255',
            'estimasi_ranking' => 'nullable|integer',
            'status_wilayah' => 'nullable|string|max:255',
            'prioritas_urutan' => 'nullable|integer',
            'target_suara_per_rw' => 'nullable|integer',
            
            // Validation for Profil RW
            'tipologi' => 'nullable|string',
            'ekonomi_dominan' => 'nullable|array',
            'profil_warga' => 'nullable|array',
            'profil_warga_keterangan' => 'nullable|string',
            'suara_pks_2019' => 'nullable|integer',
            'faktor_penyebab' => 'nullable|array',
            'faktor_penyebab_keterangan' => 'nullable|string',
            'anggota_pks' => 'nullable|string',
            'jumlah_kta' => 'nullable|integer',
            'upa_rw_status' => 'required|string',
            'upa_rw_nama_input' => 'nullable|string',
            'upa_rw_no_hp_input' => 'nullable|string',
            'rki_status' => 'required|string',
            'rki_nama_input' => 'nullable|string',
            'rki_no_hp_input' => 'nullable|string',
            'senam_status' => 'nullable|string',
            'senam_nama_input' => 'nullable|string',
            'senam_no_hp_input' => 'nullable|string',
            'relawan_milenial_status' => 'nullable|string',
            'relawan_milenial_nama_input' => 'nullable|string',
            'relawan_milenial_no_hp_input' => 'nullable|string',
            'caleg_terpilih_ada' => 'nullable|boolean',
            'caleg_terpilih_nama' => 'nullable|string',
            'afiliasi_rw_rt' => 'nullable|string',
            'afiliasi_posyandu_dkm' => 'nullable|string',
            'partai_dominan' => 'nullable|string',
            'afiliasi_ketua_rw' => 'nullable|string',
            'afiliasi_mayoritas_rt' => 'nullable|string',
            'afiliasi_tomas' => 'nullable|string',
            'afiliasi_toga' => 'nullable|string',
            'afiliasi_pemuda' => 'nullable|string',
            'kompetitor_status' => 'nullable|string',
            'kompetitor_detail' => 'nullable|string',
            'tim_sukses_status' => 'nullable|string',
            'tim_sukses_detail' => 'nullable|string',
            'strategi' => 'nullable|string',
            'strategi_keterangan' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
            'keterangan_lain' => 'nullable|string',
        ]);

        $rw = DataRw::find($this->data_rw_id);

        $upa_nama = trim($this->upa_rw_nama_input . ($this->upa_rw_no_hp_input ? ' - ' . $this->upa_rw_no_hp_input : ''), ' -');
        $rki_nama = trim($this->rki_nama_input . ($this->rki_no_hp_input ? ' - ' . $this->rki_no_hp_input : ''), ' -');
        $senam_nama = trim($this->senam_nama_input . ($this->senam_no_hp_input ? ' - ' . $this->senam_no_hp_input : ''), ' -');
        $relawan_milenial_nama = trim($this->relawan_milenial_nama_input . ($this->relawan_milenial_no_hp_input ? ' - ' . $this->relawan_milenial_no_hp_input : ''), ' -');

        if ($rw) {
            $rw->update([
                'dpt' => $this->dpt,
                'dpt_laki' => $this->dpt_laki,
                'dpt_perempuan' => $this->dpt_perempuan,
                'jumlah_rt' => $this->jumlah_rt,
                'jumlah_tps' => $this->jumlah_tps,
                'estimasi_pks' => $this->estimasi_pks,
                'gen_z' => $this->gen_z,
                'millennial' => $this->millennial,
                'gen_x' => $this->gen_x,
                'boomer' => $this->boomer,
                'estimasi_share' => $this->estimasi_share,
                'estimasi_ranking' => $this->estimasi_ranking,
                'status_wilayah' => $this->status_wilayah,
                'prioritas_urutan' => $this->prioritas_urutan,
                'target_suara_per_rw' => $this->target_suara_per_rw,
            ]);

            $isComplete = true;
            $requiredFields = ['tipologi', 'ekonomi_dominan', 'suara_pks_2019', 'kompetitor_status', 'tim_sukses_status'];
            foreach ($requiredFields as $field) {
                if (empty($this->$field)) {
                    $isComplete = false;
                    break;
                }
            }

            \App\Models\ProfilRw::updateOrCreate(
                [
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw' => $rw->nomor_rw,
                ],
                [
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw' => $rw->nomor_rw,
                    'dapil' => $rw->targetWilayah->dapil,
                    'kecamatan' => $rw->targetWilayah->kecamatan,
                    'desa' => $rw->targetWilayah->desa,
                    'is_complete' => $isComplete,
                    'tipologi' => $this->tipologi,
                    'ekonomi_dominan' => is_array($this->ekonomi_dominan) ? implode(', ', $this->ekonomi_dominan) : $this->ekonomi_dominan,
                    'profil_warga' => trim(implode(', ', $this->profil_warga) . ($this->profil_warga_keterangan ? ' - ' . $this->profil_warga_keterangan : '')),
                    'suara_pks_2019' => (int) $this->suara_pks_2019,
                    'faktor_penyebab' => trim((is_array($this->faktor_penyebab) ? implode(', ', $this->faktor_penyebab) : $this->faktor_penyebab) . ($this->faktor_penyebab_keterangan ? ' - ' . $this->faktor_penyebab_keterangan : '')),
                    'anggota_pks' => $this->anggota_pks,
                    'jumlah_kta' => (int) $this->jumlah_kta,
                    'upa_rw_status' => $this->upa_rw_status,
                    'upa_rw_nama' => $upa_nama,
                    'rki_status' => $this->rki_status,
                    'rki_nama' => $rki_nama,
                    'senam_status' => $this->senam_status,
                    'senam_nama' => $senam_nama,
                    'relawan_milenial_status' => $this->relawan_milenial_status,
                    'relawan_milenial_nama' => $relawan_milenial_nama,
                    'caleg_terpilih_ada' => $this->caleg_terpilih_ada,
                    'caleg_terpilih_nama' => $this->caleg_terpilih_nama,
                    'afiliasi_rw_rt' => $this->afiliasi_rw_rt,
                    'afiliasi_posyandu_dkm' => $this->afiliasi_posyandu_dkm,
                    'partai_dominan' => $this->partai_dominan,
                    'afiliasi_ketua_rw' => $this->afiliasi_ketua_rw,
                    'afiliasi_mayoritas_rt' => $this->afiliasi_mayoritas_rt,
                    'afiliasi_tomas' => $this->afiliasi_tomas,
                    'afiliasi_toga' => $this->afiliasi_toga,
                    'afiliasi_pemuda' => $this->afiliasi_pemuda,
                    'kompetitor_status' => $this->kompetitor_status,
                    'kompetitor_detail' => $this->kompetitor_detail,
                    'tim_sukses_status' => $this->tim_sukses_status,
                    'tim_sukses_detail' => $this->tim_sukses_detail,
                    'strategi' => trim($this->strategi . ($this->strategi_keterangan ? ' - ' . $this->strategi_keterangan : '')),
                    'penanggung_jawab' => $this->penanggung_jawab,
                    'keterangan_lain' => $this->keterangan_lain,
                ]
            );
        }

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.public.rw-profile-form')
            ->layout('components.layouts.form', ['title' => 'Form Profil RW Publik']);
    }
}
