<?php

namespace App\Livewire\ApprovalRw;

use App\Models\DataRw;
use App\Models\ProfilRw;
use App\Models\RwProfileSubmission;
use App\Models\TargetWilayah;
use App\Traits\WithWilayahScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithWilayahScope;

    public string $search = '';
    public string $selectedDapil = '';
    public string $selectedKecamatan = '';
    public string $selectedDesa = '';

    public ?int $selectedSubmissionId = null;
    public bool $showDetail = false;

    public function mount(): void
    {
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil') {
            $this->selectedDapil = (string) ($scope['locked_dapil'] ?? '');
        }
    }

    public function updatingSearch(): void { $this->resetPage(); }
    
    public function updatedSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedSelectedKecamatan(): void
    {
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedSelectedDesa(): void
    {
        $this->resetPage();
    }

    private function baseTargetQuery(): Builder
    {
        return $this->applyUserScope(TargetWilayah::query());
    }

    public function getDapilOptionsProperty(): array
    {
        return $this->baseTargetQuery()->select('dapil')->distinct()->orderBy('dapil')->pluck('dapil')->all();
    }

    public function getKecamatanOptionsProperty(): Collection
    {
        return $this->baseTargetQuery()
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
    }

    public function getDesaOptionsProperty(): Collection
    {
        return $this->baseTargetQuery()
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->select('desa')->distinct()->orderBy('desa')->pluck('desa');
    }

    public function viewDetail($id): void
    {
        $this->selectedSubmissionId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedSubmissionId = null;
    }

    public function approve($id): void
    {
        $record = RwProfileSubmission::find($id);

        if (!$record || $record->status !== 'pending') {
            return;
        }

        $rw = DataRw::find($record->data_rw_id);
        if ($rw) {
            $rw->update([
                'dpt' => $record->dpt ?? 0,
                'dpt_laki' => $record->dpt_laki ?? 0,
                'dpt_perempuan' => $record->dpt_perempuan ?? 0,
                'gen_z' => $record->gen_z ?? 0,
                'millennial' => $record->millennial ?? 0,
                'gen_x' => $record->gen_x ?? 0,
                'boomer' => $record->boomer ?? 0,
                'jumlah_rt' => $record->jumlah_rt ?? 0,
                'jumlah_tps' => $record->jumlah_tps ?? 0,
                'estimasi_pks' => $record->estimasi_pks ?? 0,
                'estimasi_share' => $record->estimasi_share ?? 0,
                'estimasi_ranking' => $record->estimasi_ranking ?? 0,
                'status_wilayah' => $record->status_wilayah ?? 'ZONA BERAT',
                'prioritas_urutan' => $record->prioritas_urutan ?? 5,
                'target_suara_per_rw' => $record->target_suara_per_rw ?? 0,
            ]);

            ProfilRw::updateOrCreate(
                [
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw' => $rw->nomor_rw,
                ],
                [
                    'dapil' => $rw->dapil,
                    'kecamatan' => $rw->kecamatan,
                    'desa' => $rw->desa,
                    'tipologi' => $record->tipologi,
                    'ekonomi_dominan' => $record->ekonomi_dominan,
                    'profil_warga' => $record->profil_warga,
                    'profil_warga_keterangan' => $record->profil_warga_keterangan,
                    'suara_pks_2019' => (int) ($record->suara_pks_2019 ?? 0),
                    'faktor_penyebab' => $record->faktor_penyebab,
                    'faktor_penyebab_keterangan' => $record->faktor_penyebab_keterangan,
                    'anggota_pks' => $record->anggota_pks,
                    'jumlah_kta' => (int) ($record->jumlah_kta ?? 0),
                    'upa_rw_status' => $record->upa_rw_status ?? 'belum',
                    'upa_rw_nama' => $record->upa_rw_nama,
                    'rki_status' => $record->rki_status ?? 'belum',
                    'rki_nama' => $record->rki_nama,
                    'senam_status' => $record->senam_status ?? 'belum',
                    'senam_nama' => $record->senam_nama,
                    'relawan_milenial_status' => $record->relawan_milenial_status ?? 'belum',
                    'relawan_milenial_nama' => $record->relawan_milenial_nama,
                    'caleg_terpilih_ada' => (bool) ($record->caleg_terpilih_ada ?? false),
                    'caleg_terpilih_nama' => $record->caleg_terpilih_nama,
                    'afiliasi_rw_rt' => $record->afiliasi_rw_rt,
                    'afiliasi_posyandu_dkm' => $record->afiliasi_posyandu_dkm,
                    'kompetitor_status' => $record->kompetitor_status ?? 'tidak_tahu',
                    'kompetitor_detail' => $record->kompetitor_detail,
                    'tim_sukses_status' => $record->tim_sukses_status ?? 'tidak_tahu',
                    'tim_sukses_detail' => $record->tim_sukses_detail,
                    'strategi' => $record->strategi,
                    'strategi_keterangan' => $record->strategi_keterangan,
                    'penanggung_jawab' => $record->penanggung_jawab,
                    'keterangan_lain' => $record->keterangan_lain,
                    'is_complete' => true,
                    'completion_percent' => 100, // Roughly complete if approved from public form
                ]
            );
        }
        
        $record->update(['status' => 'approved']);
        
        $this->closeDetail();
        session()->flash('message', 'Data pengajuan profil RW atas nama ' . $record->nama_pengisi . ' berhasil disetujui.');
    }

    public function reject($id): void
    {
        $record = RwProfileSubmission::find($id);

        if (!$record || $record->status !== 'pending') {
            return;
        }

        $record->update(['status' => 'rejected']);
        
        $this->closeDetail();
        session()->flash('message', 'Data pengajuan profil RW atas nama ' . $record->nama_pengisi . ' telah ditolak.');
    }

    public function render()
    {
        $query = RwProfileSubmission::query()->latest();
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_pengisi', 'like', '%' . $this->search . '%')
                  ->orWhere('desa', 'like', '%' . $this->search . '%')
                  ->orWhere('no_hp_pengisi', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.approval-rw.index', [
            'submissions' => $query->when($this->selectedDapil !== '', fn($q) => $q->where('dapil', $this->selectedDapil))
                ->when($this->selectedKecamatan !== '', fn($q) => $q->where('kecamatan', $this->selectedKecamatan))
                ->when($this->selectedDesa !== '', fn($q) => $q->where('desa', $this->selectedDesa))
                ->paginate(10),
            'detailRecord' => $this->selectedSubmissionId ? RwProfileSubmission::find($this->selectedSubmissionId) : null,
        ])->layout('components.layouts.app', ['title' => 'Antrean Profil RW']);
    }
}
