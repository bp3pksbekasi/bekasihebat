<?php

declare(strict_types=1);

namespace App\Livewire\BedahDapil;

use App\Models\DataRw;
use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SisirRw extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public string $selectedDesa = '';

    public string $selectedRwStatus = '';

    public string $timelineTab = 'terbaru';

    public int $selectedBulan;

    public int $selectedTahun;

    public int $rwBelumPage = 1;

    public int $rwBelumPerPage = 10;

    public bool $showForm = false;

    public string $formDesaId = '';

    public string $formRw = '';

    public string $formJenis = 'silaturahmi';

    public string $formTanggal = '';

    public string $formPelaksana = '';

    public int $formJumlahWarga = 0;

    public string $formCatatan = '';

    public string $formTokoh = '';

    public string $formTindakLanjut = '';

    public string $formJadwalBerikutnya = '';

    public bool $formJadikanEvent = false;

    public bool $formTampilGaleri = false;

    /**
     * @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile>
     */
    public array $formFoto = [];

    /**
     * @var array<int, string>
     */
    public array $existingFoto = [];

    public ?string $editId = null;

    protected $queryString = ['selectedDapil', 'selectedKecamatan', 'selectedDesa'];

    public function mount(): void
    {
        $this->selectedBulan = (int) now()->month;
        $this->selectedTahun = (int) now()->year;
        $this->formTanggal = now()->format('Y-m-d\TH:i');

        if ($this->selectedDapil !== '') {
            $this->selectedDapil = $this->resolveDapilValue($this->selectedDapil);
        }

        $firstDapil = TargetWilayah::query()
            ->distinct()
            ->orderBy('dapil')
            ->value('dapil');

        if (is_string($firstDapil) && $firstDapil !== '') {
            $this->selectedDapil = $firstDapil;
        }
    }

    #[Computed]
    public function dapilOptions(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    #[Computed]
    public function kecamatanOptions(): Collection
    {
        $selectedDapil = $this->activeDapil();

        return TargetWilayah::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    #[Computed]
    public function filterDesaOptions(): Collection
    {
        $selectedDapil = $this->activeDapil();

        return TargetWilayah::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function summary(): array
    {
        $baseKegiatan = $this->filteredKegiatanQuery()
            ->periode($this->selectedBulan, $this->selectedTahun);

        $totalRw = $this->filteredRwQuery()->count();

        $rwTersisir = (int) ((clone $baseKegiatan)
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total') ?? 0);

        $kegiatanBulanIni = (clone $baseKegiatan)->count();
        $wargaTerjangkau = (int) ((clone $baseKegiatan)->sum('jumlah_warga') ?? 0);
        $rwBelum = max(0, $totalRw - $rwTersisir);

        return [
            'total_rw' => $totalRw,
            'rw_tersisir' => $rwTersisir,
            'pct_tersisir' => $totalRw > 0 ? (int) round(($rwTersisir / $totalRw) * 100) : 0,
            'kegiatan_bulan_ini' => $kegiatanBulanIni,
            'warga_terjangkau' => $wargaTerjangkau,
            'rw_belum' => $rwBelum,
        ];
    }

    #[Computed]
    public function heatmapData(): Collection
    {
        $desaList = $this->filteredTargetQuery()
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get(['id', 'kecamatan', 'desa']);

        if ($desaList->isEmpty()) {
            return collect();
        }

        $targetIds = $desaList->pluck('id');

        $rwGroups = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->orderBy('nomor_rw')
            ->get([
                'target_wilayah_id',
                'nomor_rw',
                'dpt',
                'estimasi_pks',
                'status_wilayah',
            ])
            ->groupBy('target_wilayah_id');

        $kegiatanCounts = KegiatanRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->periode($this->selectedBulan, $this->selectedTahun)
            ->selectRaw('target_wilayah_id, nomor_rw, COUNT(*) as total')
            ->groupBy('target_wilayah_id', 'nomor_rw')
            ->get()
            ->groupBy('target_wilayah_id')
            ->map(fn (Collection $rows) => $rows->pluck('total', 'nomor_rw'));

        return $desaList
            ->map(function (TargetWilayah $target) use ($rwGroups, $kegiatanCounts): array {
                $rwList = $rwGroups->get($target->id, collect());

                return [
                    'target_wilayah_id' => $target->id,
                    'kecamatan' => $target->kecamatan,
                    'desa' => $target->desa,
                    'rw_list' => $rwList->map(function (DataRw $rw) use ($kegiatanCounts, $target): array {
                        $countsByRw = $kegiatanCounts->get($target->id, collect());

                        return [
                            'nomor_rw' => $rw->nomor_rw,
                            'dpt' => (int) $rw->dpt,
                            'estimasi_pks' => (int) $rw->estimasi_pks,
                            'status' => $rw->status_wilayah,
                            'kegiatan_count' => (int) ($countsByRw[$rw->nomor_rw] ?? 0),
                        ];
                    }),
                ];
            })
            ->filter(fn (array $row): bool => $row['rw_list']->isNotEmpty())
            ->values();
    }

    #[Computed]
    public function timeline(): Collection
    {
        $query = $this->filteredKegiatanQuery()
            ->with(['creator', 'event'])
            ->orderByDesc('tanggal_kegiatan');

        if ($this->timelineTab === 'terbaru') {
            $query->periode($this->selectedBulan, $this->selectedTahun);
        } elseif ($this->timelineTab === 'arsip') {
            $query->where(function (Builder $builder): void {
                $builder
                    ->whereYear('tanggal_kegiatan', '<', $this->selectedTahun)
                    ->orWhere(function (Builder $nested): void {
                        $nested
                            ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                            ->whereMonth('tanggal_kegiatan', '<', $this->selectedBulan);
                    });
            });
        } elseif ($this->timelineTab === 'event') {
            $query->has('event');
        }

        return $query
            ->limit(6)
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function timelineTabs(): array
    {
        $baseQuery = $this->filteredKegiatanQuery();
        $periodeQuery = (clone $baseQuery)->periode($this->selectedBulan, $this->selectedTahun);
        $arsipQuery = (clone $baseQuery)->where(function (Builder $builder): void {
            $builder
                ->whereYear('tanggal_kegiatan', '<', $this->selectedTahun)
                ->orWhere(function (Builder $nested): void {
                    $nested
                        ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                        ->whereMonth('tanggal_kegiatan', '<', $this->selectedBulan);
                });
        });
        $eventQuery = (clone $baseQuery)->has('event');

        return [
            [
                'key' => 'terbaru',
                'label' => 'Terbaru',
                'count' => $periodeQuery->count(),
                'active' => $this->timelineTab === 'terbaru',
            ],
            [
                'key' => 'arsip',
                'label' => 'Arsip',
                'count' => $arsipQuery->count(),
                'active' => $this->timelineTab === 'arsip',
            ],
            [
                'key' => 'event',
                'label' => 'Sudah Jadi Event',
                'count' => $eventQuery->count(),
                'active' => $this->timelineTab === 'event',
            ],
        ];
    }

    #[Computed]
    public function timelineMeta(): array
    {
        return match ($this->timelineTab) {
            'arsip' => [
                'title' => 'Arsip kegiatan sebelumnya',
                'hint' => 'Menampilkan aktivitas sebelum periode aktif saat ini',
                'empty' => 'Belum ada arsip kegiatan pada scope ini.',
            ],
            'event' => [
                'title' => 'Kegiatan yang sudah jadi event',
                'hint' => 'Fokus pada aktivitas yang sudah ditindaklanjuti menjadi event',
                'empty' => 'Belum ada kegiatan yang dijadikan event pada scope ini.',
            ],
            default => [
                'title' => 'Timeline aktivitas lapangan',
                'hint' => 'Menampilkan aktivitas pada periode aktif saat ini',
                'empty' => 'Belum ada kegiatan tercatat pada periode aktif ini.',
            ],
        };
    }

    #[Computed]
    public function rwBelumTersisirAll(): Collection
    {
        $rwDenganKegiatan = $this->filteredKegiatanQuery()
            ->periode($this->selectedBulan, $this->selectedTahun)
            ->selectRaw("CONCAT(target_wilayah_id, ':', nomor_rw) as rw_key")
            ->distinct()
            ->pluck('rw_key')
            ->flip();

        $lastVisitMap = $this->filteredKegiatanQuery()
            ->selectRaw("CONCAT(target_wilayah_id, ':', nomor_rw) as rw_key")
            ->selectRaw('MAX(tanggal_kegiatan) as last_visit_at')
            ->groupBy('target_wilayah_id', 'nomor_rw')
            ->get()
            ->pluck('last_visit_at', 'rw_key');

        return $this->filteredRwQuery()
            ->orderBy('prioritas_urutan')
            ->orderByDesc('estimasi_pks')
            ->get()
            ->reject(function (DataRw $rw) use ($rwDenganKegiatan): bool {
                $key = $rw->target_wilayah_id . ':' . $rw->nomor_rw;

                return $rwDenganKegiatan->has($key);
            })
            ->map(function (DataRw $rw) use ($lastVisitMap): array {
                $key = $rw->target_wilayah_id . ':' . $rw->nomor_rw;

                return [
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw' => $rw->nomor_rw,
                    'desa' => $rw->desa,
                    'kecamatan' => $rw->kecamatan,
                    'dpt' => (int) $rw->dpt,
                    'estimasi_pks' => (int) $rw->estimasi_pks,
                    'prioritas_urutan' => (int) $rw->prioritas_urutan,
                    'status_key' => $rw->status_wilayah,
                    'status_config' => $rw->status_config,
                    'last_visit_at' => $lastVisitMap[$key] ?? null,
                ];
            })
            ->values();
    }

    #[Computed]
    public function rwBelumTersisirFiltered(): Collection
    {
        return $this->rwBelumTersisirAll
            ->when($this->selectedRwStatus !== '', fn (Collection $rows) => $rows->where('status_key', $this->selectedRwStatus))
            ->values();
    }

    #[Computed]
    public function rwBelumTersisirPage(): array
    {
        $total = $this->rwBelumTersisirFiltered->count();
        $lastPage = max(1, (int) ceil($total / $this->rwBelumPerPage));
        $currentPage = min(max(1, $this->rwBelumPage), $lastPage);
        $items = $this->rwBelumTersisirFiltered
            ->forPage($currentPage, $this->rwBelumPerPage)
            ->values();
        $from = $total > 0 ? (($currentPage - 1) * $this->rwBelumPerPage) + 1 : 0;
        $to = $total > 0 ? min($currentPage * $this->rwBelumPerPage, $total) : 0;

        return [
            'items' => $items,
            'total' => $total,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'from' => $from,
            'to' => $to,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $lastPage,
        ];
    }

    #[Computed]
    public function rwBelumStatusFilters(): array
    {
        $filters = [[
            'key' => '',
            'label' => 'Semua',
            'count' => $this->rwBelumTersisirAll->count(),
            'active' => $this->selectedRwStatus === '',
            'bg' => '#f5f5f5',
            'text' => '#525252',
            'border' => '#d4d4d8',
        ]];

        foreach (TargetWilayah::STATUS_CONFIG as $key => $config) {
            $filters[] = [
                'key' => $key,
                'label' => $config['label'],
                'count' => $this->rwBelumTersisirAll->where('status_key', $key)->count(),
                'active' => $this->selectedRwStatus === $key,
                'bg' => $config['bg'],
                'text' => $config['text'],
                'border' => $config['text'],
            ];
        }

        return $filters;
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        $selectedDapil = $this->activeDapil();

        return TargetWilayah::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
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
            ->pluck('nomor_rw');
    }

    public function openForm(?string $targetWilayahId = null, ?string $nomorRw = null): void
    {
        $this->resetForm();

        if ($targetWilayahId !== null) {
            $this->formDesaId = $targetWilayahId;
        }

        if ($nomorRw !== null) {
            $this->formRw = $nomorRw;
        }

        $this->showForm = true;
    }

    public function openFormForRw(string $targetWilayahId, string $nomorRw): void
    {
        $this->openForm($targetWilayahId, $nomorRw);
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function setRwStatusFilter(string $status = ''): void
    {
        if ($status !== '' && ! array_key_exists($status, TargetWilayah::STATUS_CONFIG)) {
            return;
        }

        $this->selectedRwStatus = $status;
        $this->rwBelumPage = 1;
    }

    public function setTimelineTab(string $tab): void
    {
        if (! in_array($tab, ['terbaru', 'arsip', 'event'], true)) {
            return;
        }

        $this->timelineTab = $tab;
    }

    public function prevRwBelumPage(): void
    {
        $this->rwBelumPage = max(1, $this->rwBelumPage - 1);
    }

    public function nextRwBelumPage(): void
    {
        $lastPage = $this->rwBelumTersisirPage['last_page'] ?? 1;
        $this->rwBelumPage = min($lastPage, $this->rwBelumPage + 1);
    }

    public function simpanKegiatan(): void
    {
        $validated = $this->validate([
            'formDesaId' => ['required', 'string'],
            'formRw' => ['required', 'string', 'max:10'],
            'formJenis' => ['required', 'string'],
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
            'nomor_rw' => $validated['formRw'],
            'jenis_kegiatan' => $validated['formJenis'],
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
            $kegiatan = KegiatanRw::query()->create($payload);

            if ($this->formJadikanEvent) {
                $this->closeForm();
                $this->redirectRoute('events.create', ['from_kegiatan' => $kegiatan->id], navigate: true);

                return;
            }

            session()->flash('message', 'Kegiatan berhasil dicatat.');
        }

        $this->closeForm();
    }

    public function editKegiatan(string $id): void
    {
        $kegiatan = KegiatanRw::query()->findOrFail($id);

        $this->editId = $kegiatan->id;
        $this->formDesaId = $kegiatan->target_wilayah_id;
        $this->formRw = $kegiatan->nomor_rw;
        $this->formJenis = $kegiatan->jenis_kegiatan;
        $this->formTanggal = $kegiatan->tanggal_kegiatan?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->formPelaksana = $kegiatan->pelaksana;
        $this->formJumlahWarga = (int) $kegiatan->jumlah_warga;
        $this->formCatatan = (string) ($kegiatan->catatan ?? '');
        $this->formTokoh = (string) ($kegiatan->tokoh_ditemui ?? '');
        $this->formTindakLanjut = (string) ($kegiatan->tindak_lanjut ?? '');
        $this->formJadwalBerikutnya = $kegiatan->jadwal_berikutnya?->format('Y-m-d') ?? '';
        $this->formTampilGaleri = (bool) $kegiatan->tampil_galeri;
        $this->formJadikanEvent = false;
        $this->existingFoto = $kegiatan->foto ?? [];
        $this->formFoto = [];
        $this->showForm = true;
    }

    public function hapusKegiatan(string $id): void
    {
        $kegiatan = KegiatanRw::query()->findOrFail($id);

        foreach (($kegiatan->foto ?? []) as $path) {
            Storage::disk('public')->delete($path);
        }

        $kegiatan->delete();
        session()->flash('message', 'Kegiatan dihapus.');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->formDesaId = '';
        $this->formRw = '';
        $this->formJenis = 'silaturahmi';
        $this->formTanggal = now()->format('Y-m-d\TH:i');
        $this->formPelaksana = '';
        $this->formJumlahWarga = 0;
        $this->formCatatan = '';
        $this->formTokoh = '';
        $this->formTindakLanjut = '';
        $this->formJadwalBerikutnya = '';
        $this->formJadikanEvent = false;
        $this->formTampilGaleri = false;
        $this->formFoto = [];
        $this->existingFoto = [];
    }

    public function updatingSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->selectedRwStatus = '';
        $this->rwBelumPage = 1;
        $this->resetPage();
    }

    public function updatingSelectedKecamatan(): void
    {
        $this->selectedDesa = '';
        $this->selectedRwStatus = '';
        $this->rwBelumPage = 1;
        $this->resetPage();
    }

    public function updatingSelectedDesa(): void
    {
        $this->selectedRwStatus = '';
        $this->rwBelumPage = 1;
        $this->resetPage();
    }

    public function updatingSelectedBulan(): void
    {
        $this->rwBelumPage = 1;
        $this->resetPage();
    }

    public function updatingSelectedTahun(): void
    {
        $this->rwBelumPage = 1;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.bedah-dapil.sisir-rw')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Sisir RW']);
    }

    private function filteredTargetQuery(): Builder
    {
        $selectedDapil = $this->activeDapil();

        return TargetWilayah::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa));
    }

    private function filteredRwQuery(): Builder
    {
        $selectedDapil = $this->activeDapil();

        return DataRw::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa));
    }

    private function filteredKegiatanQuery(): Builder
    {
        $selectedDapil = $this->activeDapil();

        return KegiatanRw::query()
            ->when($selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa));
    }

    private function activeDapil(): string
    {
        if ($this->selectedDapil === '') {
            return '';
        }

        return $this->resolveDapilValue($this->selectedDapil);
    }

    private function resolveDapilValue(string $value): string
    {
        $normalizedValue = $this->normalizeDapil($value);

        $matched = $this->dapilOptions->first(function (string $dapil) use ($normalizedValue): bool {
            return $this->normalizeDapil($dapil) === $normalizedValue;
        });

        return is_string($matched) && $matched !== '' ? $matched : trim($value);
    }

    private function normalizeDapil(string $value): string
    {
        $text = strtoupper(trim($value));
        $text = preg_replace('/\s+/', '', $text) ?? $text;

        return $text;
    }
}
