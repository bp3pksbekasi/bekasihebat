<?php

namespace App\Livewire\Public;

use App\Models\DataRw;
use App\Models\TargetWilayah;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use Livewire\Component;

class InputInfrastruktur extends Component
{
    // Wilayah Fields
    public $dapilOptions = [];
    public $kecamatanOptions = [];
    public $desaOptions = [];
    public $rwOptions = [];

    public $dapil = '';
    public $kecamatan = '';
    public $desa = '';
    public $data_rw_id = '';

    // Form Fields
    public $infraType = 'korwe';
    public $infraNama = '';
    public $infraHp = '';
    public $infraRt = '';
    public $infraTarget = '';

    public $isSubmitted = false;

    public function mount()
    {
        $this->dapilOptions = TargetWilayah::select('dapil')->distinct()->orderBy('dapil')->pluck('dapil')->toArray();
    }

    public function updatedDapil()
    {
        $this->kecamatan = '';
        $this->desa = '';
        $this->data_rw_id = '';
        
        $this->kecamatanOptions = TargetWilayah::where('dapil', $this->dapil)
            ->select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan')->toArray();
    }

    public function updatedKecamatan()
    {
        $this->desa = '';
        $this->data_rw_id = '';
        
        $this->desaOptions = TargetWilayah::where('kecamatan', $this->kecamatan)
            ->select('desa')->distinct()->orderBy('desa')->pluck('desa')->toArray();
    }

    public function updatedDesa()
    {
        $this->data_rw_id = '';
        
        $this->rwOptions = DataRw::where('desa', $this->desa)
            ->select('id', 'nomor_rw')->orderBy('nomor_rw')->get()->toArray();
    }

    public function submit()
    {
        $this->validate([
            'data_rw_id' => 'required',
            'infraType' => 'required|in:korwe,korte,penggalang',
            'infraNama' => 'required|string|max:255',
            'infraHp' => 'required|string|max:255',
        ], [
            'data_rw_id.required' => 'Nomor RW wajib dipilih.',
            'infraType.required' => 'Jenis infrastruktur wajib dipilih.',
            'infraNama.required' => 'Nama wajib diisi.',
            'infraHp.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        if ($this->infraType === 'korte' || $this->infraType === 'penggalang') {
            $this->validate([
                'infraRt' => 'required|string|max:10',
            ], [
                'infraRt.required' => 'Nomor RT wajib diisi.',
            ]);
        }

        if ($this->infraType === 'penggalang') {
            $this->validate([
                'infraTarget' => 'required|integer|min:1',
            ], [
                'infraTarget.required' => 'Target jangkauan wajib diisi.',
                'infraTarget.integer' => 'Target jangkauan harus berupa angka.',
                'infraTarget.min' => 'Target jangkauan minimal 1.',
            ]);
        }

        $dataRw = DataRw::with('targetWilayah')->find($this->data_rw_id);
        if (!$dataRw) {
            $this->addError('data_rw_id', 'RW tidak ditemukan.');
            return;
        }

        $baseData = [
            'target_wilayah_id' => $dataRw->target_wilayah_id,
            'nomor_rw' => $dataRw->nomor_rw,
            'nama_koordinator' => $this->infraNama,
            'no_hp' => $this->infraHp,
            'status' => 'belum',
        ];

        if ($this->infraType === 'korwe') {
            Korwe::create($baseData);
        } elseif ($this->infraType === 'korte') {
            $baseData['nomor_rt'] = str_pad($this->infraRt ?: '000', 3, '0', STR_PAD_LEFT);
            Korte::create($baseData);
        } elseif ($this->infraType === 'penggalang') {
            PenggalangSuara::create([
                'target_wilayah_id' => $dataRw->target_wilayah_id,
                'dapil' => $dataRw->targetWilayah->dapil,
                'kecamatan' => $dataRw->targetWilayah->kecamatan,
                'desa' => $dataRw->targetWilayah->desa,
                'nomor_rw' => $dataRw->nomor_rw,
                'rt' => str_pad($this->infraRt ?: '000', 3, '0', STR_PAD_LEFT),
                'nama' => $this->infraNama,
                'no_hp' => $this->infraHp,
                'no_wa' => $this->infraHp,
                'target_jangkauan' => (int) $this->infraTarget,
                'titik_koordinat' => null,
            ]);
        }

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.public.input-infrastruktur')
            ->layout('components.layouts.form', ['title' => 'Form Input Infrastruktur Publik']);
    }
}
