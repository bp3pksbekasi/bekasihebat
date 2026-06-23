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
    public $tipologi = '';
    public $ekonomi_dominan = '';
    public $profil_warga = '';
    public $profil_warga_keterangan = '';
    public $suara_pks_2019;
    public $faktor_penyebab = '';
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
                    $this->tipologi = $profil->tipologi;
                    $this->ekonomi_dominan = $profil->ekonomi_dominan;
                    $this->profil_warga = $profil->profil_warga;
                    $this->profil_warga_keterangan = $profil->profil_warga_keterangan;
                    $this->suara_pks_2019 = $profil->suara_pks_2019;
                    $this->faktor_penyebab = $profil->faktor_penyebab;
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
        $this->profil_warga = '';
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
            'ekonomi_dominan' => 'nullable|string',
            'profil_warga' => 'nullable|string',
            'profil_warga_keterangan' => 'nullable|string',
            'suara_pks_2019' => 'nullable|integer',
            'faktor_penyebab' => 'nullable|string',
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
                    'is_complete' => $isComplete,
                    'tipologi' => $this->tipologi,
                    'ekonomi_dominan' => $this->ekonomi_dominan,
                    'profil_warga' => trim($this->profil_warga . ($this->profil_warga_keterangan ? ' - ' . $this->profil_warga_keterangan : '')),
                    'suara_pks_2019' => $this->suara_pks_2019,
                    'faktor_penyebab' => trim($this->faktor_penyebab . ($this->faktor_penyebab_keterangan ? ' - ' . $this->faktor_penyebab_keterangan : '')),
                    'anggota_pks' => $this->anggota_pks,
                    'jumlah_kta' => $this->jumlah_kta,
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
