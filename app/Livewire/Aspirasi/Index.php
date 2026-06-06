<?php

declare(strict_types=1);

namespace App\Livewire\Aspirasi;

use App\Models\AnggotaDewan;
use App\Models\Aspirasi;
use App\Models\AspirasiLog;
use App\Models\AspirasiReminder;
use App\Models\TargetWilayah;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $selectedDapil = '';

    public string $filterStatus = '';

    public string $filterKategori = '';

    public string $search = '';

    public string $activeTab = 'daftar';

    public ?string $selectedAspirasiId = null;

    public bool $showForm = false;

    public ?string $editId = null;

    public string $fJudul = '';

    public string $fDeskripsi = '';

    public string $fKategori = 'infrastruktur';

    public string $fUrgensi = 'sedang';

    public string $fDapil = '';

    public string $fKecamatan = '';

    public string $fDesa = '';

    public string $fRw = '';

    public string $fAlamat = '';

    public string $fNamaPelapor = '';

    public string $fHpPelapor = '';

    public string $fSumber = 'langsung';

    public string $fSumberId = '';

    public string $fCatatanInternal = '';

    public bool $showKonfirmasiSipd = false;

    public ?string $konfirmasiAspirasiId = null;

    public string $fNomorPokir = '';

    public $fScreenshotSipd;

    public bool $showUpdateStatus = false;

    public ?string $updateAspirasiId = null;

    public string $fNewStatus = 'verifikasi_bappeda';

    public string $fAnggaranNominal = '';

    public string $fTahunAnggaran = '';

    public $fFotoRealisasi;

    public string $fCatatan = '';

    /**
     * @var array<string, string>
     */
    public array $assignSelection = [];

    public function mount(): void
    {
        $this->selectedDapil = (string) request()->query('dapil', '');
        $this->selectedAspirasiId = request()->query('aspirasi');

        if (request()->filled('source')) {
            $this->showForm = true;
            $this->fSumber = (string) request()->query('source', 'langsung');
            $this->fSumberId = (string) request()->query('source_id', '');
            $this->fDapil = (string) request()->query('dapil', '');
            $this->fKecamatan = (string) request()->query('kecamatan', '');
            $this->fDesa = (string) request()->query('desa', '');
            $this->fRw = (string) request()->query('rw', '');
            $this->fNamaPelapor = (string) request()->query('pelapor', '');
            $this->fDeskripsi = (string) request()->query('deskripsi', '');
            $this->fJudul = (string) request()->query('judul', '');
        }
    }

    public function updatingSelectedDapil(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFDapil(): void
    {
        $this->fKecamatan = '';
        $this->fDesa = '';
    }

    public function updatedFKecamatan(): void
    {
        $this->fDesa = '';
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function selectAspirasi(string $aspirasiId): void
    {
        $this->selectedAspirasiId = $aspirasiId;
    }

    /**
     * @return array<int, string>
     */
    public function getDapilOptionsProperty(): array
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil')
            ->all();
    }

    public function getKecamatanOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->fDapil !== '', fn (Builder $query) => $query->where('dapil', $this->fDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->fDapil !== '', fn (Builder $query) => $query->where('dapil', $this->fDapil))
            ->when($this->fKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->fKecamatan))
            ->orderBy('desa')
            ->get(['id', 'desa', 'kecamatan', 'dapil']);
    }

    public function getAutoSuggestDewanProperty(): ?AnggotaDewan
    {
        if ($this->fDapil === '') {
            return null;
        }

        return AnggotaDewan::query()
            ->aktif()
            ->where('dapil', $this->fDapil)
            ->orderBy('nama')
            ->first();
    }

    public function getPipelineStagesProperty(): array
    {
        return collect(Aspirasi::STATUS_PIPELINE)
            ->reject(fn (array $config, string $status): bool => $status === 'ditolak')
            ->map(fn (array $config, string $status): array => [
                'key' => $status,
                'label' => $config['label'],
                'color' => $config['color'],
                'bg' => $config['bg'],
                'count' => (int) Aspirasi::query()->where('status', $status)->count(),
            ])
            ->values()
            ->all();
    }

    public function getPipelineSummaryProperty(): array
    {
        $counts = Aspirasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $counts->sum(),
            'belum_assign' => (int) Aspirasi::query()->belumAssign()->count(),
            'stuck' => (int) Aspirasi::query()->stuck(14)->count(),
            'diterima' => (int) ($counts['diterima'] ?? 0),
            'assigned' => (int) ($counts['assigned'] ?? 0),
            'input_sipd' => (int) ($counts['input_sipd'] ?? 0),
            'verifikasi_bappeda' => (int) ($counts['verifikasi_bappeda'] ?? 0),
            'dianggarkan' => (int) ($counts['dianggarkan'] ?? 0),
            'terealisasi' => (int) ($counts['terealisasi'] ?? 0),
        ];
    }

    public function getAspirasiListProperty(): LengthAwarePaginator
    {
        return Aspirasi::query()
            ->with(['assignedDewan', 'creator'])
            ->withCount('logs')
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->filterStatus !== '', fn (Builder $query) => $query->where('status', $this->filterStatus))
            ->when($this->filterKategori !== '', fn (Builder $query) => $query->where('kategori', $this->filterKategori))
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $nested): void {
                    $nested
                        ->where('judul', 'like', '%'.$this->search.'%')
                        ->orWhere('deskripsi', 'like', '%'.$this->search.'%')
                        ->orWhere('nama_pelapor', 'like', '%'.$this->search.'%')
                        ->orWhere('desa', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByRaw('CASE WHEN assigned_dewan_id IS NULL THEN 0 ELSE 1 END ASC')
            ->latest()
            ->paginate(10);
    }

    public function getDetailProperty(): ?Aspirasi
    {
        if (! $this->selectedAspirasiId) {
            return null;
        }

        return Aspirasi::query()
            ->with(['assignedDewan', 'creator', 'targetWilayah', 'logs.user', 'reminders.targetUser'])
            ->find($this->selectedAspirasiId);
    }

    public function getKinerjaDewanProperty(): Collection
    {
        $summary = Aspirasi::query()
            ->whereNotNull('assigned_dewan_id')
            ->selectRaw('assigned_dewan_id, status, COUNT(*) as total')
            ->groupBy('assigned_dewan_id', 'status')
            ->get()
            ->groupBy('assigned_dewan_id');

        return AnggotaDewan::query()
            ->aktif()
            ->orderBy('dapil')
            ->orderBy('nama')
            ->get()
            ->map(function (AnggotaDewan $dewan) use ($summary): array {
                $rows = $summary->get($dewan->id, collect());
                $assigned = (int) $rows->sum('total');
                $inputSipd = (int) ($rows->firstWhere('status', 'input_sipd')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'verifikasi_bappeda')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'dianggarkan')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'terealisasi')->total ?? 0);
                $verified = (int) ($rows->firstWhere('status', 'verifikasi_bappeda')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'dianggarkan')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'terealisasi')->total ?? 0);
                $dianggarkan = (int) ($rows->firstWhere('status', 'dianggarkan')->total ?? 0)
                    + (int) ($rows->firstWhere('status', 'terealisasi')->total ?? 0);
                $terealisasi = (int) ($rows->firstWhere('status', 'terealisasi')->total ?? 0);

                $score = ($assigned > 0 ? ($inputSipd / $assigned) * 40 : 0)
                    + ($inputSipd > 0 ? ($dianggarkan / $inputSipd) * 30 : 0)
                    + ($dianggarkan > 0 ? ($terealisasi / $dianggarkan) * 30 : 0);

                return [
                    'dewan' => $dewan,
                    'assigned' => $assigned,
                    'input_sipd' => $inputSipd,
                    'verifikasi_bappeda' => $verified,
                    'dianggarkan' => $dianggarkan,
                    'terealisasi' => $terealisasi,
                    'score' => (int) round($score),
                ];
            })
            ->sortByDesc('score')
            ->values();
    }

    public function getSumberBreakdownProperty(): Collection
    {
        return collect(Aspirasi::SUMBER_OPTIONS)
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
                'count' => (int) Aspirasi::query()->where('sumber', $key)->count(),
            ])
            ->values();
    }

    public function getKategoriBreakdownProperty(): Collection
    {
        return collect(Aspirasi::KATEGORI_OPTIONS)
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
                'count' => (int) Aspirasi::query()->where('kategori', $key)->count(),
            ])
            ->values();
    }

    public function getStuckAspirasiProperty(): Collection
    {
        return Aspirasi::query()
            ->with('assignedDewan')
            ->stuck(14)
            ->latest('assigned_at')
            ->limit(8)
            ->get();
    }

    public function getPublikPreviewProperty(): array
    {
        return [
            'total' => (int) Aspirasi::query()->count(),
            'sipd' => (int) Aspirasi::query()->whereIn('status', ['input_sipd', 'verifikasi_bappeda', 'dianggarkan', 'terealisasi'])->count(),
            'dianggarkan' => (int) Aspirasi::query()->whereIn('status', ['dianggarkan', 'terealisasi'])->count(),
            'terealisasi' => (int) Aspirasi::query()->where('status', 'terealisasi')->count(),
            'stories' => Aspirasi::query()
                ->with('assignedDewan')
                ->where('status', 'terealisasi')
                ->latest('realisasi_at')
                ->limit(5)
                ->get(),
        ];
    }

    public function getDewanGroupedByDapilProperty(): Collection
    {
        return AnggotaDewan::query()
            ->aktif()
            ->orderBy('dapil')
            ->orderBy('nama')
            ->get()
            ->groupBy(fn (AnggotaDewan $row) => $row->dapil ?: 'Tanpa Dapil');
    }

    public function simpanAspirasi(): void
    {
        $validated = $this->validate([
            'fJudul' => ['required', 'string', 'max:255'],
            'fDeskripsi' => ['required', 'string'],
            'fKategori' => ['required', 'string'],
            'fUrgensi' => ['required', 'string'],
            'fDapil' => ['required', 'string', 'max:50'],
            'fKecamatan' => ['nullable', 'string', 'max:100'],
            'fDesa' => ['nullable', 'string', 'max:255'],
            'fRw' => ['nullable', 'string', 'max:10'],
            'fAlamat' => ['nullable', 'string', 'max:255'],
            'fNamaPelapor' => ['required', 'string', 'max:255'],
            'fHpPelapor' => ['nullable', 'string', 'max:50'],
            'fSumber' => ['required', 'string', 'max:50'],
            'fSumberId' => ['nullable', 'string', 'max:255'],
            'fCatatanInternal' => ['nullable', 'string'],
        ]);

        $targetWilayah = $this->resolveTargetWilayah($validated['fDapil'], $validated['fKecamatan'], $validated['fDesa']);
        $autoSuggestDewan = $this->autoSuggestDewan;
        $isAutoAssigned = $autoSuggestDewan instanceof AnggotaDewan;

        $aspirasi = Aspirasi::query()->create([
            'judul' => trim($validated['fJudul']),
            'deskripsi' => trim($validated['fDeskripsi']),
            'kategori' => $validated['fKategori'],
            'urgensi' => $validated['fUrgensi'],
            'dapil' => $validated['fDapil'],
            'kecamatan' => $validated['fKecamatan'] !== '' ? $validated['fKecamatan'] : null,
            'desa' => $validated['fDesa'] !== '' ? $validated['fDesa'] : null,
            'nomor_rw' => $validated['fRw'] !== '' ? $this->normalizeRw($validated['fRw']) : null,
            'alamat_detail' => $validated['fAlamat'] !== '' ? $validated['fAlamat'] : null,
            'target_wilayah_id' => $targetWilayah?->id,
            'nama_pelapor' => trim($validated['fNamaPelapor']),
            'hp_pelapor' => $validated['fHpPelapor'] !== '' ? $this->normalizePhone($validated['fHpPelapor']) : null,
            'sumber' => $validated['fSumber'],
            'sumber_id' => $validated['fSumberId'] !== '' ? $validated['fSumberId'] : null,
            'assigned_dewan_id' => $autoSuggestDewan?->id,
            'assigned_at' => $isAutoAssigned ? now() : null,
            'status' => $isAutoAssigned ? 'assigned' : 'diterima',
            'catatan_internal' => $validated['fCatatanInternal'] !== '' ? $validated['fCatatanInternal'] : null,
            'created_by' => auth()->id(),
        ]);

        $aspirasi->update(['draft_pokir' => $aspirasi->generateDraftPokir()]);

        AspirasiLog::query()->create([
            'aspirasi_id' => $aspirasi->id,
            'dari_status' => null,
            'ke_status' => $isAutoAssigned ? 'assigned' : 'diterima',
            'aksi' => 'created',
            'catatan' => $isAutoAssigned
                ? 'Aspirasi baru dicatat dan otomatis di-assign ke '.$autoSuggestDewan->nama.'.'
                : 'Aspirasi baru dicatat.',
            'user_id' => auth()->id(),
        ]);

        if ($isAutoAssigned) {
            $targetUserId = $aspirasi->resolveReminderTargetUserId();

            if ($targetUserId) {
                AspirasiReminder::query()->create([
                    'aspirasi_id' => $aspirasi->id,
                    'target_user_id' => $targetUserId,
                    'channel' => 'system',
                    'pesan' => "Anda mendapat aspirasi baru untuk diinput ke SIPD: {$aspirasi->judul}",
                ]);
            }
        }

        $this->selectedAspirasiId = $aspirasi->id;
        $this->showForm = false;
        $this->resetForm();
        $this->resetPage();

        $message = 'Aspirasi berhasil disimpan dan draft POKIR otomatis dibuat.';

        if ($isAutoAssigned) {
            $message .= ' Otomatis di-assign ke '.$autoSuggestDewan->nama.'.';
        } elseif ($this->autoSuggestDewan instanceof AnggotaDewan) {
            $message .= ' Saran penugasan: '.$this->autoSuggestDewan->nama.'.';
        }

        session()->flash('message', $message);
    }

    public function assignDewan(string $aspirasiId, ?string $dewanId = null): void
    {
        $aspirasi = Aspirasi::query()->findOrFail($aspirasiId);
        $dewanId ??= $this->assignSelection[$aspirasiId] ?? null;

        if (! $dewanId) {
            session()->flash('message', 'Pilih anggota dewan terlebih dahulu.');

            return;
        }

        $dewan = AnggotaDewan::query()->findOrFail($dewanId);

        $aspirasi->assigned_dewan_id = $dewan->id;
        $aspirasi->assigned_at = now();
        $aspirasi->save();
        $aspirasi->updateStatus('assigned', 'Ditugaskan ke '.$dewan->nama, auth()->id());

        $targetUserId = $aspirasi->resolveReminderTargetUserId();

        if ($targetUserId) {
            AspirasiReminder::query()->create([
                'aspirasi_id' => $aspirasi->id,
                'target_user_id' => $targetUserId,
                'channel' => 'system',
                'pesan' => "Anda mendapat aspirasi baru untuk diinput ke SIPD: {$aspirasi->judul}",
            ]);
        }

        $this->selectedAspirasiId = $aspirasi->id;
        session()->flash('message', 'Aspirasi berhasil di-assign ke '.$dewan->nama.'.');
    }

    public function openKonfirmasiSipd(string $aspirasiId): void
    {
        $this->konfirmasiAspirasiId = $aspirasiId;
        $this->showKonfirmasiSipd = true;
        $this->fNomorPokir = '';
        $this->fScreenshotSipd = null;
    }

    public function konfirmasiInputSipd(): void
    {
        $validated = $this->validate([
            'fNomorPokir' => ['required', 'string', 'max:255'],
            'fScreenshotSipd' => ['nullable', 'image', 'max:4096'],
        ]);

        $aspirasi = Aspirasi::query()->findOrFail((string) $this->konfirmasiAspirasiId);
        $path = $this->fScreenshotSipd ? $this->fScreenshotSipd->store('aspirasi/sipd', 'public') : $aspirasi->screenshot_sipd;

        $aspirasi->update([
            'nomor_pokir' => trim($validated['fNomorPokir']),
            'input_sipd_at' => now(),
            'screenshot_sipd' => $path,
        ]);

        $aspirasi->updateStatus('input_sipd', 'POKIR: '.$validated['fNomorPokir'], auth()->id());

        $this->showKonfirmasiSipd = false;
        $this->konfirmasiAspirasiId = null;
        $this->fNomorPokir = '';
        $this->fScreenshotSipd = null;

        session()->flash('message', 'Konfirmasi input SIPD berhasil disimpan.');
    }

    public function openUpdateStatus(string $aspirasiId, string $targetStatus): void
    {
        $this->showUpdateStatus = true;
        $this->updateAspirasiId = $aspirasiId;
        $this->fNewStatus = $targetStatus;
        $this->fAnggaranNominal = '';
        $this->fTahunAnggaran = '';
        $this->fFotoRealisasi = null;
        $this->fCatatan = '';
    }

    public function updateStatus(?string $aspirasiId = null): void
    {
        $aspirasi = Aspirasi::query()->findOrFail($aspirasiId ?? (string) $this->updateAspirasiId);

        $rules = [
            'fNewStatus' => ['required', 'string'],
            'fCatatan' => ['nullable', 'string'],
        ];

        if ($this->fNewStatus === 'dianggarkan') {
            $rules['fAnggaranNominal'] = ['required', 'numeric', 'min:0'];
            $rules['fTahunAnggaran'] = ['required', 'string', 'max:10'];
        }

        if ($this->fNewStatus === 'terealisasi') {
            $rules['fFotoRealisasi'] = ['nullable', 'image', 'max:4096'];
        }

        $validated = $this->validate($rules);

        if ($this->fNewStatus === 'verifikasi_bappeda') {
            $aspirasi->verified_at = now();
        }

        if ($this->fNewStatus === 'dianggarkan') {
            $aspirasi->dianggarkan_at = now();
            $aspirasi->anggaran_nominal = (float) $validated['fAnggaranNominal'];
            $aspirasi->tahun_anggaran = $validated['fTahunAnggaran'];
        }

        if ($this->fNewStatus === 'terealisasi') {
            $aspirasi->realisasi_at = now();
            $aspirasi->foto_realisasi = $this->fFotoRealisasi
                ? $this->fFotoRealisasi->store('aspirasi/realisasi', 'public')
                : $aspirasi->foto_realisasi;
            $aspirasi->feedback_warga = $aspirasi->feedback_warga ?: 'Usulan warga sudah terealisasi dan ditindaklanjuti.';
            $aspirasi->notif_warga_sent = true;
        }

        $aspirasi->save();
        $aspirasi->updateStatus($this->fNewStatus, $validated['fCatatan'] !== '' ? $validated['fCatatan'] : null, auth()->id());

        $this->showUpdateStatus = false;
        $this->updateAspirasiId = null;
        $this->fNewStatus = 'verifikasi_bappeda';
        $this->fAnggaranNominal = '';
        $this->fTahunAnggaran = '';
        $this->fFotoRealisasi = null;
        $this->fCatatan = '';

        session()->flash('message', 'Status aspirasi berhasil diperbarui.');
    }

    public function copyDraftPokir(string $aspirasiId): void
    {
        $aspirasi = Aspirasi::query()->findOrFail($aspirasiId);
        $draft = $aspirasi->draft_pokir ?: $aspirasi->generateDraftPokir();

        if (! $aspirasi->draft_pokir) {
            $aspirasi->update(['draft_pokir' => $draft]);
        }

        $this->dispatch('aspirasi-copy-draft', text: $draft);
        session()->flash('message', 'Draft POKIR sudah dicopy, paste ke SIPD.');
    }

    public function bukaSipd(): void
    {
        $this->dispatch('aspirasi-open-sipd', url: 'https://sipd.kemendagri.go.id');
    }

    public function kirimReminderManual(string $aspirasiId): void
    {
        $aspirasi = Aspirasi::query()->with('assignedDewan')->findOrFail($aspirasiId);
        $targetUserId = $aspirasi->resolveReminderTargetUserId();

        if (! $aspirasi->assigned_dewan_id || ! $targetUserId) {
            session()->flash('message', 'Aspirasi ini belum memiliki penanggung jawab reminder.');

            return;
        }

        $days = $aspirasi->assigned_at?->diffInDays(now()) ?? 0;

        AspirasiReminder::query()->create([
            'aspirasi_id' => $aspirasi->id,
            'target_user_id' => $targetUserId,
            'channel' => 'system',
            'pesan' => "Aspirasi '{$aspirasi->judul}' sudah {$days} hari belum diinput SIPD. Segera input.",
        ]);

        AspirasiLog::query()->create([
            'aspirasi_id' => $aspirasi->id,
            'dari_status' => $aspirasi->status,
            'ke_status' => $aspirasi->status,
            'aksi' => 'reminder_sent',
            'catatan' => 'Reminder manual dikirim.',
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Reminder berhasil dikirim.');
    }

    public function render(): View
    {
        return view('livewire.aspirasi.index')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Aspirasi & POKIR']);
    }

    private function resolveTargetWilayah(string $dapil, string $kecamatan, string $desa): ?TargetWilayah
    {
        return TargetWilayah::query()
            ->where('dapil', $dapil)
            ->when($kecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $kecamatan))
            ->when($desa !== '', fn (Builder $query) => $query->where('desa', $desa))
            ->orderBy('desa')
            ->first();
    }

    private function normalizePhone(string $value): string
    {
        $phone = preg_replace('/[\s\-]/', '', trim($value)) ?? '';

        return $phone === '' ? '' : $phone;
    }

    private function normalizeRw(string $value): string
    {
        $clean = trim(str_ireplace(['RW', 'rw'], '', $value));

        if ($clean === '' || ! ctype_digit($clean)) {
            return $value;
        }

        return str_pad($clean, 3, '0', STR_PAD_LEFT);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->fJudul = '';
        $this->fDeskripsi = '';
        $this->fKategori = 'infrastruktur';
        $this->fUrgensi = 'sedang';
        $this->fDapil = $this->selectedDapil;
        $this->fKecamatan = '';
        $this->fDesa = '';
        $this->fRw = '';
        $this->fAlamat = '';
        $this->fNamaPelapor = '';
        $this->fHpPelapor = '';
        $this->fSumber = 'langsung';
        $this->fSumberId = '';
        $this->fCatatanInternal = '';
    }
}
