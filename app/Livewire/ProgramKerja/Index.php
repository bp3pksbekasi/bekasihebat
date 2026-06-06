<?php

namespace App\Livewire\ProgramKerja;

use App\Models\AgendaDpd;
use App\Models\BidangDpd;
use App\Models\ProgramKerja;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $selectedTahun;
    public $selectedBidangId = null;

    public $showProgramForm = false;
    public $pgEditId = null;
    public $pgBidangId = '';
    public $pgNama = '';
    public $pgDeskripsi = '';
    public $pgTargetTeks = '';
    public $pgTargetAngka = 0;
    public $pgSatuan = '';
    public $pgPeriode = 'sepanjang_tahun';
    public $pgDeadline = '';
    public $pgPicNama = '';
    public $pgStatus = 'belum_mulai';

    public $showAgendaForm = false;
    public $agEditId = null;
    public $agBidangId = '';
    public $agProgramId = '';
    public $agJudul = '';
    public $agJenis = 'rapat';
    public $agTanggal = '';
    public $agLokasi = '';
    public $agDapil = '';
    public $agPesertaTarget = 0;
    public $agCatatan = '';

    public $showRealisasiForm = false;
    public $realProgramId = '';
    public $realAngka = 0;

    public function mount(): void
    {
        $this->selectedTahun = date('Y');
        $this->agTanggal = now()->format('Y-m-d\TH:i');
    }

    public function getBidangListProperty(): Collection
    {
        return BidangDpd::query()
            ->orderBy('urutan')
            ->withCount(['programKerjas as program_count' => fn ($query) => $query->where('tahun', $this->selectedTahun)])
            ->get()
            ->map(function (BidangDpd $bidang) {
                $programs = ProgramKerja::query()
                    ->where('bidang_dpd_id', $bidang->id)
                    ->where('tahun', $this->selectedTahun);

                $bidang->progress = $programs->count() > 0 ? (int) round((float) $programs->avg('progress_pct')) : 0;

                return $bidang;
            });
    }

    public function getProgramListProperty(): Collection
    {
        if (! $this->selectedBidangId) {
            return collect();
        }

        return ProgramKerja::query()
            ->where('bidang_dpd_id', $this->selectedBidangId)
            ->where('tahun', $this->selectedTahun)
            ->with(['agendas' => fn ($query) => $query->orderByDesc('tanggal_mulai')->limit(3)])
            ->orderByRaw("FIELD(status, 'berjalan', 'tertunda', 'belum_mulai', 'selesai')")
            ->get();
    }

    public function getBidangLainProperty(): Collection
    {
        if (! $this->selectedBidangId) {
            return collect();
        }

        return $this->bidangList->where('id', '!=', $this->selectedBidangId)->values();
    }

    public function getAgendaMendatangProperty(): Collection
    {
        return AgendaDpd::query()
            ->where('tanggal_mulai', '>=', now())
            ->where('tanggal_mulai', '<=', now()->copy()->addDays(7))
            ->where('status', 'dijadwalkan')
            ->with('bidang')
            ->orderBy('tanggal_mulai')
            ->limit(5)
            ->get();
    }

    public function getKegiatanTerbaruProperty(): Collection
    {
        return AgendaDpd::query()
            ->where('status', 'selesai')
            ->with('bidang')
            ->orderByDesc('tanggal_mulai')
            ->limit(5)
            ->get();
    }

    public function getSummaryProperty(): array
    {
        $programQuery = ProgramKerja::query()->where('tahun', $this->selectedTahun);
        $agendaQuery = AgendaDpd::query();
        $agendaMendatangQuery = AgendaDpd::query()
            ->where('status', 'dijadwalkan')
            ->where('tanggal_mulai', '>=', now())
            ->where('tanggal_mulai', '<=', now()->copy()->addDays(7));

        return [
            'totalProgram' => (int) (clone $programQuery)->count(),
            'berjalan' => (int) (clone $programQuery)->where('status', 'berjalan')->count(),
            'selesai' => (int) (clone $programQuery)->where('status', 'selesai')->count(),
            'avgProgress' => (int) round((float) ((clone $programQuery)->avg('progress_pct') ?? 0)),
            'agendaBulanIni' => (int) (clone $agendaQuery)->whereMonth('tanggal_mulai', now()->month)->whereYear('tanggal_mulai', now()->year)->count(),
            'agendaPending' => (int) (clone $agendaMendatangQuery)->count(),
        ];
    }

    public function getSelectedBidangProperty(): ?BidangDpd
    {
        if (! $this->selectedBidangId) {
            return null;
        }

        return $this->bidangList->firstWhere('id', $this->selectedBidangId);
    }

    public function getBidangOptionsProperty(): Collection
    {
        return BidangDpd::query()->orderBy('urutan')->get();
    }

    public function getProgramOptionsProperty(): Collection
    {
        return ProgramKerja::query()
            ->when($this->agBidangId, fn ($query) => $query->where('bidang_dpd_id', $this->agBidangId))
            ->where('tahun', $this->selectedTahun)
            ->orderBy('nama_program')
            ->get();
    }

    public function selectBidang($id): void
    {
        $this->selectedBidangId = $this->selectedBidangId === $id ? null : $id;
    }

    public function openProgramForm($bidangId = null): void
    {
        $this->resetProgramForm();
        $this->pgBidangId = $bidangId ?: $this->selectedBidangId ?: '';
        $this->showProgramForm = true;
    }

    public function simpanProgram(): void
    {
        $this->validate([
            'pgBidangId' => 'required',
            'pgNama' => 'required',
        ]);

        $data = [
            'bidang_dpd_id' => $this->pgBidangId,
            'nama_program' => $this->pgNama,
            'deskripsi' => $this->pgDeskripsi,
            'tahun' => (string) $this->selectedTahun,
            'target_teks' => $this->pgTargetTeks,
            'target_angka' => (int) $this->pgTargetAngka,
            'satuan' => $this->pgSatuan,
            'periode' => $this->pgPeriode,
            'deadline' => $this->pgDeadline ?: null,
            'pic_nama' => $this->pgPicNama,
            'status' => $this->pgStatus,
            'progress_pct' => 0,
            'created_by' => auth()->id(),
        ];

        if ($this->pgEditId) {
            $program = ProgramKerja::query()->findOrFail($this->pgEditId);
            $data['progress_pct'] = $program->progress_pct;
            $data['realisasi'] = $program->realisasi;
            $program->update($data);
            $program->refresh()->updateProgress();
        } else {
            $program = ProgramKerja::query()->create($data);
            $program->updateProgress();
        }

        $this->selectedBidangId = $this->pgBidangId;
        $this->resetProgramForm();
        session()->flash('message', 'Program berhasil disimpan.');
    }

    public function editProgram($id): void
    {
        $program = ProgramKerja::query()->findOrFail($id);

        $this->resetProgramForm();
        $this->selectedBidangId = $program->bidang_dpd_id;
        $this->pgEditId = $id;
        $this->pgBidangId = $program->bidang_dpd_id;
        $this->pgNama = $program->nama_program;
        $this->pgDeskripsi = (string) $program->deskripsi;
        $this->pgTargetTeks = (string) $program->target_teks;
        $this->pgTargetAngka = (int) $program->target_angka;
        $this->pgSatuan = (string) $program->satuan;
        $this->pgPeriode = (string) ($program->periode ?: 'sepanjang_tahun');
        $this->pgDeadline = $program->deadline?->format('Y-m-d') ?? '';
        $this->pgPicNama = (string) $program->pic_nama;
        $this->pgStatus = $program->status;
        $this->showProgramForm = true;
    }

    public function hapusProgram($id): void
    {
        ProgramKerja::query()->findOrFail($id)->delete();
        session()->flash('message', 'Program dihapus.');
    }

    public function openRealisasi($programId): void
    {
        $program = ProgramKerja::query()->findOrFail($programId);
        $this->realProgramId = $programId;
        $this->realAngka = (int) $program->realisasi;
        $this->showRealisasiForm = true;
        $this->resetErrorBag();
    }

    public function simpanRealisasi(): void
    {
        $this->validate([
            'realProgramId' => 'required',
            'realAngka' => 'required|integer|min:0',
        ]);

        $program = ProgramKerja::query()->findOrFail($this->realProgramId);
        $program->update(['realisasi' => (int) $this->realAngka]);
        $program->updateProgress();

        $this->showRealisasiForm = false;
        $this->realProgramId = '';
        $this->realAngka = 0;

        session()->flash('message', 'Realisasi diupdate.');
    }

    public function openAgendaForm($bidangId = null, $programId = null): void
    {
        $this->resetAgendaForm();
        $this->agBidangId = $bidangId ?: $this->selectedBidangId ?: '';
        $this->agProgramId = $programId ?: '';
        $this->agTanggal = now()->format('Y-m-d\TH:i');
        $this->showAgendaForm = true;
    }

    public function simpanAgenda(): void
    {
        $this->validate([
            'agJudul' => 'required',
            'agTanggal' => 'required',
        ]);

        AgendaDpd::query()->create([
            'bidang_dpd_id' => $this->agBidangId ?: null,
            'program_kerja_id' => $this->agProgramId ?: null,
            'judul' => $this->agJudul,
            'jenis' => $this->agJenis,
            'tanggal_mulai' => $this->agTanggal,
            'lokasi' => $this->agLokasi,
            'dapil_terkait' => $this->agDapil,
            'peserta_target' => (int) $this->agPesertaTarget,
            'catatan' => $this->agCatatan,
            'status' => 'dijadwalkan',
            'created_by' => auth()->id(),
        ]);

        $this->resetAgendaForm();
        session()->flash('message', 'Agenda berhasil ditambahkan.');
    }

    public function selesaikanAgenda($id): void
    {
        $agenda = AgendaDpd::query()->with('programKerja')->findOrFail($id);
        $agenda->update(['status' => 'selesai']);

        if ($agenda->program_kerja_id && $agenda->programKerja) {
            $agenda->programKerja->increment('realisasi');
            $agenda->programKerja->refresh()->updateProgress();
        }

        session()->flash('message', 'Agenda ditandai selesai.');
    }

    public function updatedSelectedTahun(): void
    {
        $this->selectedBidangId = null;
        $this->resetProgramForm();
        $this->resetAgendaForm();
        $this->showRealisasiForm = false;
        $this->realProgramId = '';
        $this->realAngka = 0;
        $this->resetErrorBag();
    }

    public function updatedAgBidangId(): void
    {
        $this->agProgramId = '';
    }

    public function resetProgramForm(): void
    {
        $this->showProgramForm = false;
        $this->pgEditId = null;
        $this->pgBidangId = '';
        $this->pgNama = '';
        $this->pgDeskripsi = '';
        $this->pgTargetTeks = '';
        $this->pgTargetAngka = 0;
        $this->pgSatuan = '';
        $this->pgPeriode = 'sepanjang_tahun';
        $this->pgDeadline = '';
        $this->pgPicNama = '';
        $this->pgStatus = 'belum_mulai';
        $this->resetErrorBag();
    }

    public function resetAgendaForm(): void
    {
        $this->showAgendaForm = false;
        $this->agEditId = null;
        $this->agBidangId = '';
        $this->agProgramId = '';
        $this->agJudul = '';
        $this->agJenis = 'rapat';
        $this->agTanggal = now()->format('Y-m-d\TH:i');
        $this->agLokasi = '';
        $this->agDapil = '';
        $this->agPesertaTarget = 0;
        $this->agCatatan = '';
        $this->resetErrorBag();
    }

    public function resetRealisasiForm(): void
    {
        $this->showRealisasiForm = false;
        $this->realProgramId = '';
        $this->realAngka = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.program-kerja.index')
            ->layout('components.layouts.app.sidebar');
    }
}
