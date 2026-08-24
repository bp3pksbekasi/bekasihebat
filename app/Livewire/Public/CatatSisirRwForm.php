<?php

namespace App\Livewire\Public;

use App\Models\DataRw;
use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use App\Jobs\SyncKegiatanRwToGoogleSheets;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.form', ['title' => 'Form Sisir RW Publik'])]
class CatatSisirRwForm extends Component
{
    use WithFileUploads;

    public $dapilOptions = [];
    public $kecamatanOptions = [];
    public $desaOptions = [];
    public $rwOptions = [];

    public $dapil = '';
    public $kecamatan = '';
    public $desa = '';
    public $data_rw_id = '';

    // Sisir RW fields
    public string $formTanggal = '';
    public string $formJenis = '';
    public string $formSegmen = '';
    public string $formSegmenOther = '';
    public string $formDprRi = 'TIDAK ADA';
    public string $formDprdProv = 'TIDAK ADA';
    public string $formDprdKab = 'TIDAK ADA';
    public string $formTempat = '';
    public string $formTempatOther = '';
    public string $formKeteranganTambahan = '';
    public string $formPelaksana = '';
    public string $formJumlahWarga = '';
    public string $formCatatan = '';
    
    public $isSubmitted = false;

    public function mount()
    {
        $this->dapilOptions = TargetWilayah::select('dapil')->distinct()->pluck('dapil')->toArray();
        $this->formTanggal = now()->format('Y-m-d\TH:i');
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
            ->select('id', 'nomor_rw')->orderBy('nomor_rw')->get()->toArray();
    }

    public function simpan()
    {
        $this->validate([
            'dapil' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'data_rw_id' => 'required',
            'formTanggal' => 'required|date',
            'formJenis' => 'required',
            'formSegmen' => 'required',
            'formDprRi' => 'required',
            'formDprdProv' => 'required',
            'formDprdKab' => 'required',
            'formTempat' => 'required',
            'formPelaksana' => 'required',
            'formJumlahWarga' => 'required|numeric|min:1',
        ]);

        $rw = DataRw::find($this->data_rw_id);
        if (!$rw) return;

        $targetWilayah = TargetWilayah::where('kecamatan', $this->kecamatan)->where('desa', $this->desa)->first();

        $payload = [
            'target_wilayah_id' => $targetWilayah->id,
            'dapil' => $this->dapil,
            'kecamatan' => $this->kecamatan,
            'desa' => $this->desa,
            'nomor_rw' => $rw->nomor_rw,
            'jenis_kegiatan' => $this->formJenis,
            'segmen' => $this->formSegmen === 'Other' ? $this->formSegmenOther : $this->formSegmen,
            'tanggal_kegiatan' => $this->formTanggal,
            'pelaksana' => $this->formPelaksana,
            'jumlah_warga' => $this->formJumlahWarga,
            'catatan' => $this->formCatatan,
            'dpr_ri_hadir' => $this->formDprRi,
            'dprd_prov_hadir' => $this->formDprdProv,
            'dprd_kab_hadir' => $this->formDprdKab,
            'tempat_kegiatan' => $this->formTempat === 'Other' ? $this->formTempatOther : $this->formTempat,
            'keterangan_tambahan' => $this->formKeteranganTambahan,
            'created_by' => null, // Public user
        ];

        $kegiatan = KegiatanRw::query()->create($payload);

        try {
            SyncKegiatanRwToGoogleSheets::dispatch((string) $kegiatan->id)->delay(now()->addSeconds(5))->onQueue('default');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Google Sheets sync skipped: ' . $e->getMessage());
        }

        $this->isSubmitted = true;
    }

    public function resetForm()
    {
        $this->isSubmitted = false;
        $this->reset([
            'dapil', 'kecamatan', 'desa', 'data_rw_id',
            'formJenis', 'formSegmen', 'formSegmenOther',
            'formTempat', 'formTempatOther', 'formKeteranganTambahan',
            'formPelaksana', 'formJumlahWarga', 'formCatatan'
        ]);
        $this->formDprRi = 'TIDAK ADA';
        $this->formDprdProv = 'TIDAK ADA';
        $this->formDprdKab = 'TIDAK ADA';
        $this->formTanggal = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.public.catat-sisir-rw-form');
    }
}
