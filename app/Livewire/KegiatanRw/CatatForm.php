<?php

namespace App\Livewire\KegiatanRw;

use App\Models\DataRw;
use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class CatatForm extends Component
{
    use WithFileUploads;

    public string $formTanggal = '';
    public string $formDesaId = '';
    public string $formRw = '';
    public string $formJenis = '';
    public string $formSegmen = '';
    public string $formSegmenOther = '';
    public string $formDprRi = '';
    public string $formDprdProv = '';
    public string $formDprdKab = '';
    public string $formTempat = '';
    public string $formTempatOther = '';
    public string $formKeteranganTambahan = '';
    public string $formPelaksana = '';
    public string $formJumlahWarga = '';
    public string $formCatatan = '';
    public string $formTokoh = '';
    public string $formTindakLanjut = '';
    public string $formJadwalBerikutnya = '';
    public bool $formJadikanEvent = false;
    public bool $formTampilGaleri = false;
    public array $formFoto = [];
    public array $existingFoto = [];
    public ?string $editId = null;
    public bool $showForm = false;

    public function mount()
    {
        $this->formTanggal = now()->format('Y-m-d\TH:i');
    }

    #[On('open-catat-kegiatan-form')]
    public function openForm(?string $targetWilayahId = null, ?string $nomorRw = null, ?string $editId = null): void
    {
        $this->resetForm();
        
        if ($targetWilayahId !== null) {
            $this->formDesaId = $targetWilayahId;
        }

        if ($nomorRw !== null) {
            $this->formRw = ltrim($nomorRw, '0');
        }

        if ($editId !== null) {
            $this->loadKegiatan($editId);
        } else {
            $this->formTanggal = now()->format('Y-m-d\TH:i');
        }

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm(): void
    {
        $this->reset([
            'formDesaId', 'formRw', 'formJenis', 'formSegmen', 'formSegmenOther', 'formPelaksana', 'formJumlahWarga',
            'formDprRi', 'formDprdProv', 'formDprdKab', 'formTempat', 'formTempatOther', 'formKeteranganTambahan',
            'formCatatan', 'formTokoh', 'formTindakLanjut', 'formJadwalBerikutnya',
            'formJadikanEvent', 'formTampilGaleri', 'formFoto', 'existingFoto', 'editId'
        ]);
        $this->formTanggal = now()->format('Y-m-d\TH:i');
    }

    public function loadKegiatan(string $id): void
    {
        $kegiatan = KegiatanRw::query()->findOrFail($id);
        $this->editId = $kegiatan->id;
        $this->formDesaId = $kegiatan->target_wilayah_id;
        $this->formRw = ltrim((string) $kegiatan->nomor_rw, '0');
        $this->formJenis = $kegiatan->jenis_kegiatan;
        if (in_array($kegiatan->segmen, KegiatanRw::SEGMEN_KEGIATAN)) {
            $this->formSegmen = $kegiatan->segmen;
        } else if ($kegiatan->segmen) {
            $this->formSegmen = 'Other';
            $this->formSegmenOther = $kegiatan->segmen;
        }
        $this->formDprRi = $kegiatan->dpr_ri_hadir ?? '';
        $this->formDprdProv = $kegiatan->dprd_prov_hadir ?? '';
        $this->formDprdKab = $kegiatan->dprd_kab_hadir ?? '';
        if (in_array($kegiatan->tempat_kegiatan, KegiatanRw::TEMPAT_KEGIATAN)) {
            $this->formTempat = $kegiatan->tempat_kegiatan;
        } else if ($kegiatan->tempat_kegiatan) {
            $this->formTempat = 'Other';
            $this->formTempatOther = $kegiatan->tempat_kegiatan;
        }
        $this->formKeteranganTambahan = $kegiatan->keterangan_tambahan ?? '';
        $this->formTanggal = date('Y-m-d\TH:i', strtotime($kegiatan->tanggal_kegiatan));
        $this->formPelaksana = $kegiatan->pelaksana;
        $this->formJumlahWarga = (string) $kegiatan->jumlah_warga;
        $this->formCatatan = $kegiatan->catatan ?? '';
        $this->formTokoh = $kegiatan->tokoh_ditemui ?? '';
        $this->formTindakLanjut = $kegiatan->tindak_lanjut ?? '';
        $this->formJadwalBerikutnya = $kegiatan->jadwal_berikutnya ? date('Y-m-d', strtotime($kegiatan->jadwal_berikutnya)) : '';
        $this->formTampilGaleri = (bool) $kegiatan->tampil_galeri;
        $this->existingFoto = $kegiatan->foto ?? [];
    }

    public function removeExistingFoto(int $index): void
    {
        unset($this->existingFoto[$index]);
        $this->existingFoto = array_values($this->existingFoto);
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        $user = auth()->user();
        $query = TargetWilayah::query();

        return TargetWilayah::query()
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get(['id', 'desa', 'kecamatan'])
            ->map(fn (TargetWilayah $target): array => [
                'id' => $target->id,
                'label' => $target->desa . ' - ' . $target->kecamatan,
            ]);
    }

    #[Computed]
    public function rwOptions(): Collection
    {
        if ($this->formDesaId === '') {
            return collect();
        }

        return DataRw::query()
            ->where('target_wilayah_id', $this->formDesaId)
            ->orderBy('nomor_rw')
            ->pluck('nomor_rw')
            ->map(fn($rw) => ltrim((string)$rw, '0'));
    }

    public function simpanKegiatan()
    {
        $validated = $this->validate([
            'formDesaId' => ['required', 'string'],
            'formRw' => ['required', 'string', 'max:10'],
            'formJenis' => ['required', 'string'],
            'formSegmen' => ['required', 'string'],
            'formSegmenOther' => ['required_if:formSegmen,Other', 'nullable', 'string', 'max:255'],
            'formDprRi' => ['required', 'string'],
            'formDprdProv' => ['required', 'string'],
            'formDprdKab' => ['required', 'string'],
            'formTempat' => ['required', 'string'],
            'formTempatOther' => ['required_if:formTempat,Other', 'nullable', 'string', 'max:255'],
            'formKeteranganTambahan' => ['nullable', 'string'],
            'formTanggal' => ['required', 'date'],
            'formPelaksana' => ['required', 'string', 'max:255'],
            'formJumlahWarga' => ['nullable', 'integer', 'min:0'],
            'formCatatan' => ['nullable', 'string'],
            'formTokoh' => ['nullable', 'string'],
            'formTindakLanjut' => ['nullable', 'string'],
            'formJadwalBerikutnya' => ['nullable', 'date'],
            'formJadikanEvent' => ['nullable', 'boolean'],
            'formTampilGaleri' => ['nullable', 'boolean'],
            'formFoto' => ['nullable', 'array', 'max:5'],
            'formFoto.*' => ['image', 'max:4096'],
        ], [], [
            'formDesaId' => 'desa',
            'formRw' => 'RW',
            'formJenis' => 'jenis kegiatan',
            'formSegmen' => 'segmen',
            'formSegmenOther' => 'segmen lainnya',
            'formDprRi' => 'anggota DPR RI hadir',
            'formDprdProv' => 'anggota DPRD Provinsi hadir',
            'formDprdKab' => 'anggota DPRD Kab/Kota hadir',
            'formTempat' => 'tempat kegiatan',
            'formTempatOther' => 'tempat kegiatan lainnya',
            'formKeteranganTambahan' => 'keterangan tambahan',
            'formTanggal' => 'tanggal kegiatan',
            'formPelaksana' => 'pelaksana',
            'formJumlahWarga' => 'jumlah warga',
            'formCatatan' => 'catatan',
            'formTokoh' => 'tokoh yang ditemui',
            'formTindakLanjut' => 'tindak lanjut',
            'formJadwalBerikutnya' => 'jadwal berikutnya',
            'formFoto' => 'foto',
        ]);

        $targetWilayah = TargetWilayah::query()->findOrFail($validated['formDesaId']);
        $fotoPaths = $this->existingFoto;

        foreach ($this->formFoto as $foto) {
            $fotoPaths[] = $foto->store('kegiatan-rw', 'public');
        }

        $payload = [
            'target_wilayah_id' => $targetWilayah->id,
            'dapil' => $targetWilayah->dapil,
            'kecamatan' => $targetWilayah->kecamatan,
            'desa' => $targetWilayah->desa,
            'nomor_rw' => str_pad($validated['formRw'], 3, '0', STR_PAD_LEFT),
            'jenis_kegiatan' => $validated['formJenis'],
            'segmen' => $validated['formSegmen'] === 'Other' ? $validated['formSegmenOther'] : $validated['formSegmen'],
            'dpr_ri_hadir' => $validated['formDprRi'],
            'dprd_prov_hadir' => $validated['formDprdProv'],
            'dprd_kab_hadir' => $validated['formDprdKab'],
            'tempat_kegiatan' => $validated['formTempat'] === 'Other' ? $validated['formTempatOther'] : $validated['formTempat'],
            'keterangan_tambahan' => $validated['formKeteranganTambahan'] !== '' ? $validated['formKeteranganTambahan'] : null,
            'tanggal_kegiatan' => $validated['formTanggal'],
            'pelaksana' => $validated['formPelaksana'],
            'jumlah_warga' => (int) ($validated['formJumlahWarga'] ?? 0),
            'catatan' => $validated['formCatatan'] !== '' ? $validated['formCatatan'] : null,
            'tokoh_ditemui' => $validated['formTokoh'] !== '' ? $validated['formTokoh'] : null,
            'tindak_lanjut' => $validated['formTindakLanjut'] !== '' ? $validated['formTindakLanjut'] : null,
            'jadwal_berikutnya' => $validated['formJadwalBerikutnya'] !== '' ? $validated['formJadwalBerikutnya'] : null,
            'foto' => $fotoPaths !== [] ? array_values($fotoPaths) : null,
            'tampil_galeri' => (bool) ($validated['formTampilGaleri'] ?? false),
            'created_by' => auth()->id(),
        ];

        if ($this->editId !== null) {
            KegiatanRw::query()->findOrFail($this->editId)->update($payload);
            session()->flash('message', 'Kegiatan berhasil diupdate.');
        } else {
            KegiatanRw::query()->create($payload);
            session()->flash('message', 'Kegiatan baru berhasil dicatat.');
        }

        $this->closeForm();
        $this->dispatch('kegiatan-saved');
    }

    public function render()
    {
        return view('livewire.kegiatan-rw.catat-form');
    }
}
