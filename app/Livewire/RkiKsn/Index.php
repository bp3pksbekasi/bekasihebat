<?php

declare(strict_types=1);

namespace App\Livewire\RkiKsn;

use App\Models\DataRw;
use App\Models\LogSesi;
use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\TitikRki;
use App\Models\TitikSenam;
use App\Traits\WithWilayahScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
    use WithWilayahScope;

    public string $menuMode = 'rki';

    public string $activeTab = 'rki';

    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public string $selectedFilterDesaId = '';

    public ?string $selectedDesaId = null;

    public bool $showRkiForm = false;

    public ?string $rkiEditId = null;

    public string $rkiRw = '';

    public string $rkiPenggerak = '';

    public string $rkiHp = '';

    public string $rkiLokasi = '';

    public string $rkiHari = '';

    public string $rkiJam = '';

    /** @var array<int, string> */
    public array $rkiJenis = [];

    public string $rkiStatus = 'pembentukan';

    public bool $showKsnForm = false;

    public ?string $ksnEditId = null;

    public string $ksnNamaTitik = '';

    public string $ksnInstruktur = '';

    public string $ksnHpInstruktur = '';

    public string $ksnInstruktur2 = '';

    public string $ksnHari = '';

    public string $ksnJam = '';

    public string $ksnLokasiRw = '';

    public string $ksnDesaId = '';

    public string $ksnStatus = 'pembentukan';

    public bool $showLogForm = false;

    public string $logTitikId = '';

    public string $logTitikType = '';

    public string $logTanggal = '';

    public int $logPeserta = 0;

    public string $logPelaksana = '';

    public string $logCatatan = '';

    /** @var array<int, mixed> */
    public array $logFoto = [];

    public string $expandedLogKey = '';

    protected $queryString = [
        'activeTab',
        'selectedDapil',
    ];

    public function mount(?string $mode = null): void
    {
        if (in_array($mode, ['rki', 'ksn'], true)) {
            $this->menuMode = $mode;
            $this->activeTab = $mode;
        }
        
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil') {
            $this->selectedDapil = (string) ($scope['locked_dapil'] ?? '');
            if (!empty($scope['kecamatan'])) {
                $this->selectedKecamatan = $scope['kecamatan'];
            }
            if (!empty($scope['desa'])) {
                // We need to fetch TargetWilayah ID for this desa, since selectedFilterDesaId requires the ID
                $desa = TargetWilayah::where('dapil', $this->selectedDapil)
                    ->where('kecamatan', $this->selectedKecamatan)
                    ->where('desa', $scope['desa'])
                    ->first();
                if ($desa) {
                    $this->selectedFilterDesaId = (string) $desa->id;
                }
            }
        }

        $this->logTanggal = now()->format('Y-m-d\TH:i');
    }

    public function getRkiKpiProperty(): array
    {
        $aktif = (clone $this->filteredRkiQuery())->where('status', 'aktif')->count();
        $pembentukan = (clone $this->filteredRkiQuery())->where('status', 'pembentukan')->count();
        $totalRw = (clone $this->filteredDataRwQuery())->count();
        $penggerak = (clone $this->filteredRkiQuery())->whereIn('status', ['aktif', 'pembentukan'])->count();
        $kegiatanBulanIni = (clone $this->filteredLogSesiQuery(TitikRki::class))
            ->whereMonth('tanggal_sesi', now()->month)
            ->whereYear('tanggal_sesi', now()->year)
            ->count();
        $pesertaBulanIni = (clone $this->filteredLogSesiQuery(TitikRki::class))
            ->whereMonth('tanggal_sesi', now()->month)
            ->whereYear('tanggal_sesi', now()->year)
            ->sum('jumlah_peserta');

        return compact('aktif', 'pembentukan', 'totalRw', 'penggerak', 'kegiatanBulanIni', 'pesertaBulanIni');
    }

    public function getKsnKpiProperty(): array
    {
        $aktif = (clone $this->filteredSenamQuery())->where('status', 'aktif')->count();
        $pembentukan = (clone $this->filteredSenamQuery())->where('status', 'pembentukan')->count();
        $totalDesa = (clone $this->filteredTargetQuery())->count();
        $instruktur = (clone $this->filteredSenamQuery())->whereIn('status', ['aktif', 'pembentukan'])->count();
        $sesiBulanIni = (clone $this->filteredLogSesiQuery(TitikSenam::class))
            ->whereMonth('tanggal_sesi', now()->month)
            ->whereYear('tanggal_sesi', now()->year)
            ->count();
        $pesertaBulanIni = (clone $this->filteredLogSesiQuery(TitikSenam::class))
            ->whereMonth('tanggal_sesi', now()->month)
            ->whereYear('tanggal_sesi', now()->year)
            ->sum('jumlah_peserta');

        return compact('aktif', 'pembentukan', 'totalDesa', 'instruktur', 'sesiBulanIni', 'pesertaBulanIni');
    }

    public function getRkiDesaListProperty(): LengthAwarePaginator
    {
        return $this->filteredTargetQuery()
            ->select('target_wilayahs.*')
            ->withCount(['titikRkis as rki_aktif' => fn (Builder $query) => $query->where('status', 'aktif')])
            ->withCount(['titikRkis as rki_total'])
            ->orderByDesc('rki_aktif')
            ->orderBy('desa')
            ->paginate(15, pageName: 'rkiPage');
    }

    public function getKsnDesaListProperty(): LengthAwarePaginator
    {
        return $this->filteredTargetQuery()
            ->select('target_wilayahs.*')
            ->withCount(['titikSenams as ksn_aktif' => fn (Builder $query) => $query->where('status', 'aktif')])
            ->withCount(['titikSenams as ksn_total'])
            ->orderByDesc('ksn_aktif')
            ->orderBy('desa')
            ->paginate(15, pageName: 'ksnPage');
    }

    public function getRkiDetailProperty(): ?Collection
    {
        if (! $this->selectedDesaId) {
            return null;
        }

        return TitikRki::query()
            ->where('target_wilayah_id', $this->selectedDesaId)
            ->with(['logSesis' => fn ($query) => $query->latest('tanggal_sesi')->limit(3)])
            ->orderByRaw("FIELD(status, 'aktif', 'pembentukan', 'nonaktif')")
            ->orderBy('nomor_rw')
            ->get();
    }

    public function getKsnDetailProperty(): ?Collection
    {
        if (! $this->selectedDesaId) {
            return null;
        }

        $targetWilayah = TargetWilayah::query()->find($this->selectedDesaId);
        if (! $targetWilayah) {
            return null;
        }

        return TitikSenam::query()
            ->where('kecamatan', $targetWilayah->kecamatan)
            ->where('desa', $targetWilayah->desa)
            ->with(['logSesis' => fn ($query) => $query->latest('tanggal_sesi')->limit(5)])
            ->orderByRaw("FIELD(status, 'aktif', 'pembentukan', 'nonaktif')")
            ->latest('updated_at')
            ->get();
    }

    public function getRwBelumRkiProperty(): Collection
    {
        if (! $this->selectedDesaId) {
            return collect();
        }

        $rwDenganRki = TitikRki::query()
            ->where('target_wilayah_id', $this->selectedDesaId)
            ->pluck('nomor_rw');

        return DataRw::query()
            ->where('target_wilayah_id', $this->selectedDesaId)
            ->whereNotIn('nomor_rw', $rwDenganRki)
            ->orderBy('nomor_rw')
            ->pluck('nomor_rw');
    }

    public function getSelectedDesaProperty(): ?TargetWilayah
    {
        if (! $this->selectedDesaId) {
            return null;
        }

        return TargetWilayah::query()->find($this->selectedDesaId);
    }

    public function getDapilOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    public function getKecamatanOptionsProperty(): Collection
    {
        return $this->baseTargetQuery()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getFilterDesaOptionsProperty(): Collection
    {
        return $this->baseTargetQuery()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get()
            ->map(fn (TargetWilayah $targetWilayah) => [
                'id' => $targetWilayah->id,
                'label' => $targetWilayah->desa.' - '.$targetWilayah->kecamatan,
            ]);
    }

    public function getRwOptionsProperty(): Collection
    {
        if (! $this->selectedDesaId) {
            return collect();
        }

        return DataRw::query()
            ->where('target_wilayah_id', $this->selectedDesaId)
            ->orderBy('nomor_rw')
            ->pluck('nomor_rw');
    }

    public function getDesaOptionsProperty(): Collection
    {
        return $this->filteredTargetQuery()
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get()
            ->map(fn (TargetWilayah $targetWilayah) => [
                'id' => $targetWilayah->id,
                'label' => $targetWilayah->desa.' - '.$targetWilayah->kecamatan,
            ]);
    }

    public function selectDesa(string $id): void
    {
        $this->selectedDesaId = $this->selectedDesaId === $id ? null : $id;
        $this->expandedLogKey = '';
        $this->showLogForm = false;
        $this->resetErrorBag();

        if ($this->activeTab === 'ksn' && $this->selectedDesaId) {
            $this->ksnDesaId = $this->selectedDesaId;
        }
    }

    public function setActiveTab(string $tab): void
    {
        if (! in_array($tab, ['rki', 'ksn'], true)) {
            return;
        }

        if ($tab !== $this->menuMode) {
            return;
        }

        if ($this->activeTab === $tab) {
            return;
        }

        $this->activeTab = $tab;
        $this->selectedDesaId = null;
        $this->expandedLogKey = '';
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
    }

    public function resetFilters(): void
    {
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') !== 'dapil') {
            $this->selectedDapil = '';
            $this->selectedKecamatan = '';
        }
        $this->selectedFilterDesaId = '';
        $this->selectedDesaId = null;
        $this->expandedLogKey = '';
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
    }

    public function updatedSelectedFilterDesaId(string $value): void
    {
        $this->selectedDesaId = $value !== '' ? $value : null;
        $this->expandedLogKey = '';
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
    }

    public function openRkiForm(?string $editId = null): void
    {
        $this->resetRkiForm();
        $this->resetErrorBag();
        $this->showRkiForm = true;

        if (! $editId) {
            return;
        }

        $rki = TitikRki::query()->findOrFail($editId);
        $this->rkiEditId = $rki->id;
        $this->rkiRw = $rki->nomor_rw;
        $this->rkiPenggerak = $rki->nama_penggerak;
        $this->rkiHp = $rki->no_hp_penggerak ?? '';
        $this->rkiLokasi = $rki->lokasi ?? '';
        $this->rkiHari = $rki->hari_kegiatan ?? '';
        $this->rkiJam = $rki->jam_kegiatan ?? '';
        $this->rkiJenis = $rki->jenis_kegiatan ?? [];
        $this->rkiStatus = $rki->status;
        $this->showRkiForm = true;
    }

    public function openKsnForm(?string $editId = null): void
    {
        $this->resetKsnForm();
        $this->resetErrorBag();
        $this->showKsnForm = true;
        $this->ksnDesaId = $this->selectedDesaId ?? '';

        if (! $editId) {
            return;
        }

        $ksn = TitikSenam::query()->findOrFail($editId);
        $this->ksnEditId = $ksn->id;
        $this->ksnNamaTitik = $ksn->nama_titik;
        $this->ksnInstruktur = $ksn->instruktur;
        $this->ksnHpInstruktur = $ksn->no_hp_instruktur ?? '';
        $this->ksnInstruktur2 = $ksn->instruktur_2 ?? '';
        $this->ksnHari = $ksn->hari_senam ?? '';
        $this->ksnJam = $ksn->jam_senam ?? '';
        $this->ksnLokasiRw = $ksn->lokasi_rw ?? '';
        $this->ksnDesaId = $ksn->target_wilayah_id ?? '';
        $this->ksnStatus = $ksn->status;
        $this->showKsnForm = true;
    }

    public function simpanRki(): void
    {
        $this->validate([
            'selectedDesaId' => ['required'],
            'rkiRw' => [
                'required',
                Rule::unique('titik_rkis', 'nomor_rw')
                    ->where(fn ($query) => $query->where('target_wilayah_id', $this->selectedDesaId))
                    ->ignore($this->rkiEditId),
            ],
            'rkiPenggerak' => ['required', 'string', 'max:255'],
            'rkiHp' => ['nullable', 'string', 'max:255'],
            'rkiLokasi' => ['nullable', 'string', 'max:255'],
            'rkiHari' => ['nullable', 'string', 'max:255'],
            'rkiJam' => ['nullable', 'string', 'max:255'],
            'rkiJenis' => ['array'],
            'rkiStatus' => ['required', 'in:pembentukan,aktif,nonaktif'],
        ]);

        $targetWilayah = TargetWilayah::query()->findOrFail($this->selectedDesaId);

        $data = [
            'target_wilayah_id' => $targetWilayah->id,
            'dapil' => $targetWilayah->dapil,
            'kecamatan' => $targetWilayah->kecamatan,
            'desa' => $targetWilayah->desa,
            'nomor_rw' => $this->rkiRw,
            'nama_penggerak' => $this->rkiPenggerak,
            'no_hp_penggerak' => $this->rkiHp ?: null,
            'lokasi' => $this->rkiLokasi ?: null,
            'hari_kegiatan' => $this->rkiHari ?: null,
            'jam_kegiatan' => $this->rkiJam ?: null,
            'jenis_kegiatan' => $this->rkiJenis,
            'status' => $this->rkiStatus,
            'tanggal_aktif' => $this->rkiStatus === 'aktif' ? now()->toDateString() : null,
            'created_by' => auth()->id(),
        ];

        if ($this->rkiEditId) {
            TitikRki::query()->findOrFail($this->rkiEditId)->update($data);
        } else {
            TitikRki::query()->create($data);
        }

        ProfilRw::query()->updateOrCreate(
            [
                'target_wilayah_id' => $targetWilayah->id,
                'nomor_rw' => $this->rkiRw,
            ],
            [
                'dapil' => $targetWilayah->dapil,
                'kecamatan' => $targetWilayah->kecamatan,
                'desa' => $targetWilayah->desa,
                'rki_status' => $this->rkiStatus === 'aktif' ? 'sudah' : 'belum',
                'rki_nama' => $this->rkiPenggerak,
            ]
        );

        $this->resetRkiForm();
        session()->flash('message', 'Titik RKI berhasil disimpan.');
    }

    public function simpanKsn(): void
    {
        $this->validate([
            'ksnNamaTitik' => ['required', 'string', 'max:255'],
            'ksnInstruktur' => ['required', 'string', 'max:255'],
            'ksnHpInstruktur' => ['nullable', 'string', 'max:255'],
            'ksnInstruktur2' => ['nullable', 'string', 'max:255'],
            'ksnHari' => ['nullable', 'string', 'max:255'],
            'ksnJam' => ['nullable', 'string', 'max:255'],
            'ksnLokasiRw' => ['nullable', 'string', 'max:255'],
            'ksnDesaId' => ['required'],
            'ksnStatus' => ['required', 'in:pembentukan,aktif,nonaktif'],
        ]);

        $targetWilayah = TargetWilayah::query()->findOrFail($this->ksnDesaId);

        $data = [
            'target_wilayah_id' => $targetWilayah->id,
            'dapil' => $targetWilayah->dapil,
            'kecamatan' => $targetWilayah->kecamatan,
            'desa' => $targetWilayah->desa,
            'nama_titik' => $this->ksnNamaTitik,
            'instruktur' => $this->ksnInstruktur,
            'no_hp_instruktur' => $this->ksnHpInstruktur ?: null,
            'instruktur_2' => $this->ksnInstruktur2 ?: null,
            'hari_senam' => $this->ksnHari ?: null,
            'jam_senam' => $this->ksnJam ?: null,
            'lokasi_rw' => $this->ksnLokasiRw ?: null,
            'status' => $this->ksnStatus,
            'tanggal_aktif' => $this->ksnStatus === 'aktif' ? now()->toDateString() : null,
            'created_by' => auth()->id(),
        ];

        if ($this->ksnEditId) {
            TitikSenam::query()->findOrFail($this->ksnEditId)->update($data);
        } else {
            TitikSenam::query()->create($data);
        }

        $this->selectedDesaId = $targetWilayah->id;

        if ($this->ksnLokasiRw !== '') {
            ProfilRw::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $targetWilayah->id,
                    'nomor_rw' => $this->ksnLokasiRw,
                ],
                [
                    'dapil' => $targetWilayah->dapil,
                    'kecamatan' => $targetWilayah->kecamatan,
                    'desa' => $targetWilayah->desa,
                    'senam_status' => $this->ksnStatus === 'aktif' ? 'sudah' : 'belum',
                    'senam_nama' => $this->ksnInstruktur,
                ]
            );
        }

        $this->resetKsnForm();
        session()->flash('message', 'Titik senam berhasil disimpan.');
    }

    public function simpanLog(): void
    {
        $this->validate([
            'logTitikId' => ['required'],
            'logTitikType' => ['required', 'in:'.TitikRki::class.','.TitikSenam::class],
            'logTanggal' => ['required', 'date'],
            'logPeserta' => ['required', 'integer', 'min:1'],
            'logFoto.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $fotoPaths = [];
        foreach ($this->logFoto as $foto) {
            $fotoPaths[] = $foto->store('log-sesi', 'public');
        }

        LogSesi::query()->create([
            'loggable_type' => $this->logTitikType,
            'loggable_id' => $this->logTitikId,
            'tanggal_sesi' => $this->logTanggal,
            'jumlah_peserta' => $this->logPeserta,
            'pelaksana' => $this->logPelaksana ?: null,
            'catatan' => $this->logCatatan ?: null,
            'foto' => $fotoPaths !== [] ? $fotoPaths : null,
            'created_by' => auth()->id(),
        ]);

        $titik = $this->resolveLoggable($this->logTitikType, $this->logTitikId);
        if ($titik) {
            $avg = (float) $titik->logSesis()->avg('jumlah_peserta');
            $titik->update(['avg_peserta' => (int) round($avg)]);
        }

        $this->resetLogForm();
        session()->flash('message', 'Sesi berhasil dicatat.');
    }

    public function openLogForm(string $titikId, string $type): void
    {
        $this->resetLogForm();
        $this->resetErrorBag();
        $this->logTitikId = $titikId;
        $this->logTitikType = $type;
        $this->logTanggal = now()->format('Y-m-d\TH:i');
        $this->expandedLogKey = $type.'|'.$titikId;
        $this->showLogForm = true;
    }

    public function closeLogForm(): void
    {
        $this->resetLogForm();
    }

    public function resetRkiForm(): void
    {
        $this->showRkiForm = false;
        $this->rkiEditId = null;
        $this->rkiRw = '';
        $this->rkiPenggerak = '';
        $this->rkiHp = '';
        $this->rkiLokasi = '';
        $this->rkiHari = '';
        $this->rkiJam = '';
        $this->rkiJenis = [];
        $this->rkiStatus = 'pembentukan';
    }

    public function resetKsnForm(): void
    {
        $this->showKsnForm = false;
        $this->ksnEditId = null;
        $this->ksnNamaTitik = '';
        $this->ksnInstruktur = '';
        $this->ksnHpInstruktur = '';
        $this->ksnInstruktur2 = '';
        $this->ksnHari = '';
        $this->ksnJam = '';
        $this->ksnLokasiRw = '';
        $this->ksnDesaId = $this->selectedDesaId ?? '';
        $this->ksnStatus = 'pembentukan';
    }

    public function resetLogForm(): void
    {
        $this->showLogForm = false;
        $this->logTitikId = '';
        $this->logTitikType = '';
        $this->logTanggal = now()->format('Y-m-d\TH:i');
        $this->logPeserta = 0;
        $this->logPelaksana = '';
        $this->logCatatan = '';
        $this->logFoto = [];
        $this->expandedLogKey = '';
    }

    public function updatingActiveTab(): void
    {
        $this->selectedDesaId = null;
        $this->expandedLogKey = '';
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
    }

    public function updatingSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedFilterDesaId = '';
        $this->selectedDesaId = null;
        $this->expandedLogKey = '';
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
    }

    public function updatingSelectedKecamatan(): void
    {
        $this->selectedFilterDesaId = '';
        $this->selectedDesaId = null;
        $this->expandedLogKey = '';
        $this->resetRkiForm();
        $this->resetKsnForm();
        $this->resetLogForm();
        $this->resetPage('rkiPage');
        $this->resetPage('ksnPage');
    }

    public function updatedKsnDesaId(string $value): void
    {
        if ($value !== '') {
            $this->selectedDesaId = $value;
        }
    }

    public function render()
    {
        return view('livewire.rki-ksn.index')
            ->layout('components.layouts.app.sidebar');
    }

    private function filteredTargetQuery(): Builder
    {
        return $this->applyUserScope($this->baseTargetQuery())
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedFilterDesaId !== '', fn (Builder $query) => $query->where('id', $this->selectedFilterDesaId));
    }

    private function filteredDataRwQuery(): Builder
    {
        return $this->applyUserScope(DataRw::query())
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedFilterDesaId !== '', fn (Builder $query) => $query->where('target_wilayah_id', $this->selectedFilterDesaId));
    }

    private function filteredRkiQuery(): Builder
    {
        return $this->applyUserScope(TitikRki::query())
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedFilterDesaId !== '', fn (Builder $query) => $query->where('target_wilayah_id', $this->selectedFilterDesaId));
    }

    private function filteredSenamQuery(): Builder
    {
        return $this->applyUserScope(TitikSenam::query())
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedFilterDesaId !== '', function (Builder $query): void {
                $targetWilayah = TargetWilayah::query()->find($this->selectedFilterDesaId);

                if ($targetWilayah) {
                    $query->where('desa', $targetWilayah->desa)
                        ->where('kecamatan', $targetWilayah->kecamatan);
                }
            });
    }

    private function filteredLogSesiQuery(string $type): Builder
    {
        return LogSesi::query()
            ->where('loggable_type', $type)
            ->whereHasMorph('loggable', [$type], function (Builder $morph): void {
                $this->applyUserScope($morph)
                    ->when($this->selectedDapil !== '', fn (Builder $q) => $q->where('dapil', $this->selectedDapil))
                    ->when($this->selectedKecamatan !== '', fn (Builder $q) => $q->where('kecamatan', $this->selectedKecamatan));
            });
    }

    private function resolveLoggable(string $type, string $id): ?Model
    {
        if (! in_array($type, [TitikRki::class, TitikSenam::class], true)) {
            return null;
        }

        return $type::query()->find($id);
    }

    private function baseTargetQuery(): Builder
    {
        return TargetWilayah::query();
    }

    public function getMapImageProperty(): string
    {
        if ($this->selectedKecamatan !== '') {
            $slug = str_replace(' ', '-', strtolower($this->selectedKecamatan));
            return "/images/peta/kecamatan/{$slug}.png";
        }

        if ($this->selectedDapil !== '') {
            $num = str_replace('BEKASI ', '', strtoupper($this->selectedDapil));
            return "/images/peta/dapil{$num}.png";
        }

        return "/images/peta/kabupaten-bekasi.png";
    }

    public function getMapMarkersProperty(): array
    {
        $configs = (new \App\Livewire\Kaderisasi\Index())->getMapConfigs();
        $config = null;

        if ($this->selectedKecamatan !== '') {
            $config = $configs[strtoupper($this->selectedKecamatan)] ?? null;
        } elseif ($this->selectedDapil !== '') {
            $config = $configs[strtoupper($this->selectedDapil)] ?? null;
        }

        if (!$config) {
            return [];
        }

        $wilayahs = TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->get();

        if ($wilayahs->isEmpty()) {
            return [];
        }

        $targetIds = $wilayahs->pluck('id');

        $rwCounts = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->selectRaw('target_wilayah_id, COUNT(*) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        if ($this->activeTab === 'rki') {
            $aktifCounts = TitikRki::query()
                ->whereIn('target_wilayah_id', $targetIds)
                ->where('status', 'aktif')
                ->selectRaw('target_wilayah_id, COUNT(*) as total')
                ->groupBy('target_wilayah_id')
                ->pluck('total', 'target_wilayah_id');
        } else {
            $aktifCounts = TitikSenam::query()
                ->where('status', 'aktif')
                ->selectRaw('desa, kecamatan, COUNT(*) as total')
                ->groupBy('desa', 'kecamatan')
                ->get()
                ->keyBy(fn ($item) => strtolower($item->desa) . '|' . strtolower($item->kecamatan))
                ->map(fn ($item) => $item->total);
        }

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                if ($this->activeTab === 'rki') {
                    $target = (int) ($rwCounts[$w->id] ?? 0);
                    $actual = (int) ($aktifCounts[$w->id] ?? 0);
                    $label = "{$w->desa} · {$actual}/{$target} RW RKI Aktif";
                    if ($target > 0) {
                        if ($actual >= $target) {
                            $color = '#22c55e';
                        } elseif ($actual > 0) {
                            $color = '#eab308';
                        } else {
                            $color = '#ef4444';
                        }
                    } else {
                        $color = '#22c55e';
                    }
                    $size = 12 + ($target > 0 ? (int) round(($actual / $target) * 12) : 12);
                } else {
                    $target = 1;
                    $key = strtolower($w->desa) . '|' . strtolower($w->kecamatan);
                    $actual = (int) ($aktifCounts[$key] ?? 0);
                    $label = "{$w->desa} · {$actual} KSN Aktif";
                    if ($actual >= $target) {
                        $color = '#22c55e';
                    } else {
                        $color = '#ef4444';
                    }
                    $size = 12 + ($actual > 0 ? 12 : 0);
                }

                $markers[] = [
                    'id' => $w->id,
                    'key' => $w->id,
                    'label' => $label,
                    'x' => $config[$desaUpper]['x'],
                    'y' => $config[$desaUpper]['y'],
                    'size' => $size,
                    'color' => $color,
                    'actual' => $actual,
                    'target' => $target,
                    'desa' => $w->desa,
                    'kecamatan' => $w->kecamatan
                ];
            }
        }

        return $markers;
    }

    public function getVillageListProperty(): Collection
    {
        $wilayahs = TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        if ($wilayahs->isEmpty()) {
            return collect();
        }

        $targetIds = $wilayahs->pluck('id');

        $rwCounts = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->selectRaw('target_wilayah_id, COUNT(*) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        if ($this->activeTab === 'rki') {
            $aktifCounts = TitikRki::query()
                ->whereIn('target_wilayah_id', $targetIds)
                ->where('status', 'aktif')
                ->selectRaw('target_wilayah_id, COUNT(*) as total')
                ->groupBy('target_wilayah_id')
                ->pluck('total', 'target_wilayah_id');
        } else {
            $aktifCounts = TitikSenam::query()
                ->where('status', 'aktif')
                ->selectRaw('desa, kecamatan, COUNT(*) as total')
                ->groupBy('desa', 'kecamatan')
                ->get()
                ->keyBy(fn ($item) => strtolower($item->desa) . '|' . strtolower($item->kecamatan))
                ->map(fn ($item) => $item->total);
        }

        return $wilayahs->map(function (TargetWilayah $w) use ($rwCounts, $aktifCounts): array {
            if ($this->activeTab === 'rki') {
                $target = (int) ($rwCounts[$w->id] ?? 0);
                $actual = (int) ($aktifCounts[$w->id] ?? 0);
                $pct = $target > 0 ? (int) round(($actual / $target) * 100) : 0;
            } else {
                $target = 1;
                $key = strtolower($w->desa) . '|' . strtolower($w->kecamatan);
                $actual = (int) ($aktifCounts[$key] ?? 0);
                $pct = $actual >= 1 ? 100 : 0;
            }

            return [
                'id' => $w->id,
                'desa' => $w->desa,
                'kecamatan' => $w->kecamatan,
                'dapil' => $w->dapil,
                'target' => $target,
                'actual' => $actual,
                'pct' => $pct,
            ];
        });
    }

    public function getSelectedVillageDetailProperty(): ?array
    {
        if (! $this->selectedDesaId) {
            return null;
        }

        $w = TargetWilayah::find($this->selectedDesaId);
        if (! $w) {
            return null;
        }

        $rwCount = DataRw::where('target_wilayah_id', $w->id)->count();
        $rkiCount = TitikRki::where('target_wilayah_id', $w->id)->count();
        $rkiAktif = TitikRki::where('target_wilayah_id', $w->id)->where('status', 'aktif')->count();
        
        $ksnCount = TitikSenam::where('desa', $w->desa)->where('kecamatan', $w->kecamatan)->count();
        $ksnAktif = TitikSenam::where('desa', $w->desa)->where('kecamatan', $w->kecamatan)->where('status', 'aktif')->count();

        return [
            'id' => $w->id,
            'desa' => $w->desa,
            'kecamatan' => $w->kecamatan,
            'dapil' => $w->dapil,
            'jumlah_rw' => $rwCount,
            'rki_total' => $rkiCount,
            'rki_aktif' => $rkiAktif,
            'ksn_total' => $ksnCount,
            'ksn_aktif' => $ksnAktif,
        ];
    }

    public function closeVillageDetail(): void
    {
        $this->selectedDesaId = null;
    }
}
