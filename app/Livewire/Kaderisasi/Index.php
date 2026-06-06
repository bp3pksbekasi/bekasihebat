<?php

declare(strict_types=1);

namespace App\Livewire\Kaderisasi;

use App\Models\DataRw;
use App\Models\DeploymentLog;
use App\Models\Kader;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\Pelatihan;
use App\Models\TargetWilayah;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $activeTab = 'database';
    public string $selectedDapil = '';
    public string $selectedKecamatan = '';
    public string $search = '';
    public string $filterJenjang = '';
    public string $filterStatus = 'aktif';

    public bool $showKaderForm = false;
    public ?string $kaderEditId = null;
    public string $kNama = '';
    public string $kHp = '';
    public string $kWa = '';
    public string $kNik = '';
    public string $kKta = '';
    public string $kJenjang = 'penggerak';
    public string $kDapil = '';
    public string $kKecamatan = '';
    public string $kDesa = '';
    public string $kRw = '';
    public string $kRt = '';
    public bool $kIsKorwe = false;
    public bool $kIsKorte = false;
    public bool $kIsUpa = false;
    public string $kJabatanUpa = 'anggota';
    public bool $kIsPenggalang = false;
    public bool $kIsSaksi = false;
    public array $kKeahlian = [];
    public bool $kBisaDeploy = true;
    public string $kStatus = 'aktif';

    public bool $showPelatihanForm = false;
    public ?string $pelEditId = null;
    public string $pelNama = '';
    public string $pelJenjangTarget = 'penggerak';
    public string $pelJenis = 'diklat';
    public string $pelTanggal = '';
    public string $pelLokasi = '';
    public string $pelInstruktur = '';
    public int $pelKapasitas = 0;

    public bool $showDeployForm = false;
    public string $deployKaderId = '';
    public string $deployKeDapil = '';
    public string $deployKeKecamatan = '';
    public string $deployKeDesa = '';
    public string $deployKeRw = '';
    public string $deployAlasan = 'kebutuhan_wilayah';

    public string $selectedPelatihanId = '';
    public string $pesertaKaderId = '';

    public function mount(): void
    {
        $this->pelTanggal = now()->format('Y-m-d');
    }

    public function getKpiProperty(): array
    {
        $kaderQuery = $this->filteredKaderQuery();
        $rwAdaKader = (int) (clone $kaderQuery)
            ->whereNotNull('target_wilayah_id')
            ->whereNotNull('nomor_rw')
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total');
        $totalRw = (int) $this->filteredDataRwQuery()
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total');
        $upaTerbentuk = (int) (clone $kaderQuery)
            ->where('is_upa', true)
            ->whereNotNull('target_wilayah_id')
            ->whereNotNull('nomor_rw')
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total');

        return [
            'totalKader' => (int) (clone $kaderQuery)->count(),
            'perJenjang' => $this->jenjangChart,
            'rwAdaKader' => $rwAdaKader,
            'upaTerbentuk' => $upaTerbentuk,
            'kaderAktif' => (int) (clone $kaderQuery)->where('status', 'aktif')->count(),
            'pelatihanBulanIni' => (int) $this->filteredPelatihanQuery()
                ->whereMonth('tanggal_mulai', now()->month)
                ->whereYear('tanggal_mulai', now()->year)
                ->count(),
            'rwKosong' => max($totalRw - $rwAdaKader, 0),
        ];
    }

    public function getJenjangChartProperty(): Collection
    {
        $counts = $this->filteredKaderQuery()
            ->selectRaw('jenjang, COUNT(*) as total')
            ->groupBy('jenjang')
            ->pluck('total', 'jenjang');

        return collect(Kader::JENJANG_OPTIONS)
            ->sortBy('order')
            ->map(fn (array $config, string $key) => [
                'key' => $key,
                'label' => $config['label'],
                'color' => $config['color'],
                'text' => $config['text'],
                'order' => $config['order'],
                'count' => (int) ($counts[$key] ?? 0),
            ])
            ->values();
    }

    public function getKaderListProperty(): LengthAwarePaginator
    {
        return $this->filteredKaderQuery()
            ->orderByRaw("FIELD(jenjang, 'purna', 'dewasa', 'madya', 'pelopor', 'pendukung', 'penggerak')")
            ->orderBy('nama')
            ->paginate(15, ['*'], 'kaderPage');
    }

    public function getUpaPerDapilProperty(): Collection
    {
        $totalRw = $this->filteredDataRwQuery()
            ->selectRaw("dapil, COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total_rw")
            ->groupBy('dapil')
            ->pluck('total_rw', 'dapil');

        $upaRw = $this->filteredKaderQuery()
            ->where('is_upa', true)
            ->whereNotNull('dapil')
            ->whereNotNull('target_wilayah_id')
            ->whereNotNull('nomor_rw')
            ->selectRaw("dapil, COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total_upa")
            ->groupBy('dapil')
            ->pluck('total_upa', 'dapil');

        return collect($totalRw)
            ->map(fn ($total, $dapil) => [
                'dapil' => $dapil,
                'rw_upa' => (int) ($upaRw[$dapil] ?? 0),
                'total_rw' => (int) $total,
                'pct' => (int) round(((int) ($upaRw[$dapil] ?? 0)) / max((int) $total, 1) * 100),
            ])
            ->values()
            ->sortBy('dapil')
            ->values();
    }

    public function getDeployRecommendationsProperty(): Collection
    {
        $rwPadat = $this->filteredKaderQuery()
            ->where('status', 'aktif')
            ->whereNotNull('target_wilayah_id')
            ->whereNotNull('nomor_rw')
            ->selectRaw('target_wilayah_id, dapil, kecamatan, desa, nomor_rw, COUNT(*) as total_kader')
            ->groupBy('target_wilayah_id', 'dapil', 'kecamatan', 'desa', 'nomor_rw')
            ->havingRaw('COUNT(*) > 3')
            ->orderByDesc('total_kader')
            ->get();

        return $rwPadat->map(function ($row) {
            $emptySameDesa = $this->emptyRwQuery($row->target_wilayah_id)->get();
            $emptyNearby = $emptySameDesa->isNotEmpty()
                ? $emptySameDesa
                : $this->emptyRwKecamatanCollection((string) $row->kecamatan, (string) $row->target_wilayah_id);
            $deployable = Kader::query()
                ->where('target_wilayah_id', $row->target_wilayah_id)
                ->where('nomor_rw', $row->nomor_rw)
                ->where('status', 'aktif')
                ->where('bisa_deploy', true)
                ->orderByRaw("FIELD(jenjang, 'purna', 'dewasa', 'madya', 'pelopor', 'pendukung', 'penggerak')")
                ->orderBy('nama')
                ->limit(5)
                ->get();
            $maxDeploy = min($deployable->count(), $emptyNearby->count());

            return [
                'summary' => sprintf(
                    '%s: %d kader di RW %s, bisa deploy %d ke %d RW kosong',
                    $row->desa,
                    (int) $row->total_kader,
                    $row->nomor_rw,
                    $maxDeploy,
                    $emptyNearby->count()
                ),
                'source' => $row,
                'empty_rws' => $emptyNearby,
                'candidates' => $deployable,
            ];
        })->filter(fn (array $item) => $item['candidates']->isNotEmpty() && $item['empty_rws']->isNotEmpty())->values();
    }

    public function getPelatihanListProperty(): LengthAwarePaginator
    {
        return $this->filteredPelatihanQuery()
            ->withCount('peserta')
            ->orderByDesc('tanggal_mulai')
            ->paginate(8, ['*'], 'pelatihanPage');
    }

    public function getPelatihanMendatangProperty(): Collection
    {
        return $this->filteredPelatihanQuery()
            ->where('status', 'dijadwalkan')
            ->whereDate('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->limit(5)
            ->get();
    }

    public function getPelatihanSummaryProperty(): array
    {
        $query = $this->filteredPelatihanQuery();

        return [
            'tahun_ini' => (int) (clone $query)->whereYear('tanggal_mulai', now()->year)->count(),
            'peserta_kumulatif' => (int) (clone $query)->sum('peserta_hadir'),
            'naik_jenjang' => (int) Kader::query()
                ->join('pelatihan_pesertas', 'pelatihan_pesertas.kader_id', '=', 'kaders.id')
                ->join('pelatihans', 'pelatihans.id', '=', 'pelatihan_pesertas.pelatihan_id')
                ->when($this->selectedDapil !== '', fn (Builder $builder) => $builder->where('kaders.dapil', $this->selectedDapil))
                ->where('pelatihan_pesertas.naik_jenjang', true)
                ->whereYear('pelatihans.tanggal_mulai', now()->year)
                ->count(),
        ];
    }

    public function getDeploymentRowsProperty(): Collection
    {
        return $this->filteredDeploymentQuery()
            ->latest('tanggal_deploy')
            ->limit(8)
            ->get();
    }

    public function getSelectedPelatihanProperty(): ?Pelatihan
    {
        if ($this->selectedPelatihanId === '') {
            return null;
        }

        return Pelatihan::query()
            ->with(['peserta' => fn ($query) => $query->orderBy('nama')])
            ->find($this->selectedPelatihanId);
    }

    public function getDapilOptionsProperty(): Collection
    {
        return TargetWilayah::query()->select('dapil')->distinct()->orderBy('dapil')->pluck('dapil');
    }

    public function getKecamatanOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getFormKecamatanOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->kDapil !== '', fn (Builder $query) => $query->where('dapil', $this->kDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getFormDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->kDapil !== '', fn (Builder $query) => $query->where('dapil', $this->kDapil))
            ->when($this->kKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->kKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    public function getFormRwOptionsProperty(): Collection
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->kDapil, $this->kKecamatan, $this->kDesa);

        if (! $targetWilayahId) {
            return collect();
        }

        return DataRw::query()->where('target_wilayah_id', $targetWilayahId)->orderBy('nomor_rw')->pluck('nomor_rw');
    }

    public function getFormRtOptionsProperty(): Collection
    {
        if ($this->kRw === '') {
            return collect();
        }

        return $this->buildRtOptions(
            $this->resolveTargetWilayahId($this->kDapil, $this->kKecamatan, $this->kDesa),
            $this->kRw
        );
    }

    public function getDeployKecamatanOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->deployKeDapil !== '', fn (Builder $query) => $query->where('dapil', $this->deployKeDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getDeployDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->deployKeDapil !== '', fn (Builder $query) => $query->where('dapil', $this->deployKeDapil))
            ->when($this->deployKeKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->deployKeKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    public function getDeployRwOptionsProperty(): Collection
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->deployKeDapil, $this->deployKeKecamatan, $this->deployKeDesa);

        if (! $targetWilayahId) {
            return collect();
        }

        return DataRw::query()->where('target_wilayah_id', $targetWilayahId)->orderBy('nomor_rw')->pluck('nomor_rw');
    }

    public function getEligiblePesertaProperty(): Collection
    {
        $selected = $this->selectedPelatihan;

        if (! $selected) {
            return collect();
        }

        $attachedIds = $selected->peserta->pluck('id');

        return $this->filteredKaderQuery()
            ->where('status', 'aktif')
            ->whereNotIn('id', $attachedIds)
            ->limit(100)
            ->get();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function selectPelatihan(string $id): void
    {
        $this->selectedPelatihanId = $this->selectedPelatihanId === $id ? '' : $id;
        $this->pesertaKaderId = '';
    }

    public function openKaderForm(): void
    {
        $this->resetKaderForm();
        $this->showKaderForm = true;
    }

    public function simpanKader(): void
    {
        $this->validate([
            'kNama' => 'required',
            'kJenjang' => 'required',
            'kDapil' => 'nullable',
        ]);

        $targetWilayahId = $this->resolveTargetWilayahId($this->kDapil, $this->kKecamatan, $this->kDesa);

        $data = [
            'nama' => $this->kNama,
            'no_hp' => $this->kHp,
            'no_wa' => $this->kWa,
            'nik' => $this->kNik,
            'no_kta' => $this->kKta,
            'jenjang' => $this->kJenjang,
            'tanggal_jenjang' => now(),
            'dapil' => $this->kDapil ?: null,
            'kecamatan' => $this->kKecamatan ?: null,
            'desa' => $this->kDesa ?: null,
            'nomor_rw' => $this->kRw ?: null,
            'nomor_rt' => $this->kRt ?: null,
            'target_wilayah_id' => $targetWilayahId,
            'is_korwe' => $this->kIsKorwe,
            'is_korte' => $this->kIsKorte,
            'is_upa' => $this->kIsUpa,
            'jabatan_upa' => $this->kIsUpa ? $this->kJabatanUpa : null,
            'is_penggalang' => $this->kIsPenggalang,
            'is_saksi' => $this->kIsSaksi,
            'keahlian' => $this->kKeahlian,
            'bisa_deploy' => $this->kBisaDeploy,
            'status' => $this->kStatus,
            'created_by' => auth()->id(),
        ];

        if ($this->kaderEditId) {
            $kader = Kader::query()->findOrFail($this->kaderEditId);
            $kader->update($data);
        } else {
            $kader = Kader::query()->create($data);
        }

        $this->syncKaderInfraRoles($kader);
        $this->resetKaderForm();
        session()->flash('message', 'Data kader berhasil disimpan.');
    }

    public function editKader(string $id): void
    {
        $kader = Kader::query()->findOrFail($id);

        $this->resetKaderForm();
        $this->kaderEditId = $id;
        $this->kNama = $kader->nama;
        $this->kHp = (string) $kader->no_hp;
        $this->kWa = (string) $kader->no_wa;
        $this->kNik = (string) $kader->nik;
        $this->kKta = (string) $kader->no_kta;
        $this->kJenjang = $kader->jenjang;
        $this->kDapil = (string) $kader->dapil;
        $this->kKecamatan = (string) $kader->kecamatan;
        $this->kDesa = (string) $kader->desa;
        $this->kRw = (string) $kader->nomor_rw;
        $this->kRt = (string) $kader->nomor_rt;
        $this->kIsKorwe = (bool) $kader->is_korwe;
        $this->kIsKorte = (bool) $kader->is_korte;
        $this->kIsUpa = (bool) $kader->is_upa;
        $this->kJabatanUpa = (string) ($kader->jabatan_upa ?: 'anggota');
        $this->kIsPenggalang = (bool) $kader->is_penggalang;
        $this->kIsSaksi = (bool) $kader->is_saksi;
        $this->kKeahlian = $kader->keahlian ?? [];
        $this->kBisaDeploy = (bool) $kader->bisa_deploy;
        $this->kStatus = $kader->status;
        $this->showKaderForm = true;
    }

    public function hapusKader(string $id): void
    {
        Kader::query()->findOrFail($id)->update(['status' => 'nonaktif']);
        session()->flash('message', 'Kader dinonaktifkan.');
    }

    public function openPelatihanForm(): void
    {
        $this->resetPelatihanForm();
        $this->showPelatihanForm = true;
    }

    public function editPelatihan(string $id): void
    {
        $pelatihan = Pelatihan::query()->findOrFail($id);

        $this->resetPelatihanForm();
        $this->pelEditId = $id;
        $this->pelNama = $pelatihan->nama_pelatihan;
        $this->pelJenjangTarget = $pelatihan->jenjang_target;
        $this->pelJenis = $pelatihan->jenis;
        $this->pelTanggal = $pelatihan->tanggal_mulai?->format('Y-m-d') ?? '';
        $this->pelLokasi = (string) $pelatihan->lokasi;
        $this->pelInstruktur = (string) $pelatihan->instruktur;
        $this->pelKapasitas = (int) $pelatihan->kapasitas;
        $this->showPelatihanForm = true;
    }

    public function simpanPelatihan(): void
    {
        $this->validate([
            'pelNama' => 'required',
            'pelJenjangTarget' => 'required',
            'pelTanggal' => 'required',
        ]);

        $data = [
            'nama_pelatihan' => $this->pelNama,
            'jenjang_target' => $this->pelJenjangTarget,
            'jenis' => $this->pelJenis,
            'tanggal_mulai' => $this->pelTanggal,
            'lokasi' => $this->pelLokasi,
            'instruktur' => $this->pelInstruktur,
            'kapasitas' => $this->pelKapasitas,
            'status' => 'dijadwalkan',
            'created_by' => auth()->id(),
        ];

        if ($this->pelEditId) {
            $pelatihan = Pelatihan::query()->findOrFail($this->pelEditId);
            $data['peserta_hadir'] = $pelatihan->peserta_hadir;
            $pelatihan->update($data);
            $this->selectedPelatihanId = $pelatihan->id;
        } else {
            $pelatihan = Pelatihan::query()->create($data);
            $this->selectedPelatihanId = $pelatihan->id;
        }

        $this->resetPelatihanForm();
        session()->flash('message', 'Pelatihan berhasil disimpan.');
    }

    public function tambahPeserta(string $pelatihanId, string $kaderId): void
    {
        $pelatihan = Pelatihan::query()->findOrFail($pelatihanId);
        $pelatihan->peserta()->syncWithoutDetaching([
            $kaderId => ['status' => 'terdaftar', 'naik_jenjang' => false],
        ]);
        $pelatihan->update(['peserta_hadir' => $pelatihan->peserta()->count()]);
        $this->pesertaKaderId = '';
        session()->flash('message', 'Peserta berhasil ditambahkan.');
    }

    public function luluskanPeserta(string $pelatihanId, string $kaderId): void
    {
        $pelatihan = Pelatihan::query()->findOrFail($pelatihanId);
        $pelatihan->peserta()->updateExistingPivot($kaderId, [
            'status' => 'lulus',
            'naik_jenjang' => true,
        ]);

        $kader = Kader::query()->findOrFail($kaderId);
        $nextJenjang = $this->nextJenjang($kader->jenjang);
        $this->naikkanJenjang($kaderId, $nextJenjang);
        $pelatihan->update(['peserta_hadir' => $pelatihan->peserta()->count()]);

        session()->flash('message', 'Peserta dinyatakan lulus dan jenjang kader diperbarui.');
    }

    public function openDeployForm(string $kaderId): void
    {
        $this->resetDeployForm();
        $this->deployKaderId = $kaderId;
        $this->showDeployForm = true;
    }

    public function deployKader(): void
    {
        $this->validate([
            'deployKaderId' => 'required',
            'deployKeDapil' => 'required',
            'deployKeKecamatan' => 'required',
            'deployKeDesa' => 'required',
            'deployKeRw' => 'required',
        ]);

        $kader = Kader::query()->findOrFail($this->deployKaderId);
        $targetWilayahId = $this->resolveTargetWilayahId($this->deployKeDapil, $this->deployKeKecamatan, $this->deployKeDesa);

        DeploymentLog::query()->create([
            'kader_id' => $kader->id,
            'dari_dapil' => $kader->dapil,
            'dari_kecamatan' => $kader->kecamatan,
            'dari_desa' => $kader->desa,
            'dari_rw' => $kader->nomor_rw,
            'ke_dapil' => $this->deployKeDapil,
            'ke_kecamatan' => $this->deployKeKecamatan,
            'ke_desa' => $this->deployKeDesa,
            'ke_rw' => $this->deployKeRw,
            'alasan' => $this->deployAlasan,
            'tanggal_deploy' => now()->toDateString(),
            'status' => 'selesai',
            'created_by' => auth()->id(),
        ]);

        $kader->update([
            'dapil' => $this->deployKeDapil,
            'kecamatan' => $this->deployKeKecamatan,
            'desa' => $this->deployKeDesa,
            'nomor_rw' => $this->deployKeRw,
            'target_wilayah_id' => $targetWilayahId,
        ]);

        $this->resetDeployForm();
        session()->flash('message', 'Kader berhasil dideploy ke wilayah baru.');
    }

    public function naikkanJenjang(string $kaderId, string $jenjangBaru): void
    {
        $kader = Kader::query()->findOrFail($kaderId);
        $kader->update([
            'jenjang' => $jenjangBaru,
            'tanggal_jenjang' => now(),
        ]);
    }

    public function importFromInfra(): void
    {
        Artisan::call('import:kaders-from-infra');
        session()->flash('message', trim(Artisan::output()) ?: 'Sinkronisasi infra selesai.');
    }

    public function updatingSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatingSelectedKecamatan(): void
    {
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatingFilterJenjang(): void
    {
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatingSearch(): void
    {
        $this->resetPage('kaderPage');
    }

    public function updatedKDapil(): void
    {
        $this->kKecamatan = '';
        $this->kDesa = '';
        $this->kRw = '';
        $this->kRt = '';
    }

    public function updatedKKecamatan(): void
    {
        $this->kDesa = '';
        $this->kRw = '';
        $this->kRt = '';
    }

    public function updatedKDesa(): void
    {
        $this->kRw = '';
        $this->kRt = '';
    }

    public function updatedKRw(): void
    {
        $this->kRt = '';
    }

    public function updatedDeployKeDapil(): void
    {
        $this->deployKeKecamatan = '';
        $this->deployKeDesa = '';
        $this->deployKeRw = '';
    }

    public function updatedDeployKeKecamatan(): void
    {
        $this->deployKeDesa = '';
        $this->deployKeRw = '';
    }

    public function updatedDeployKeDesa(): void
    {
        $this->deployKeRw = '';
    }

    public function resetFilters(): void
    {
        $this->selectedDapil = '';
        $this->selectedKecamatan = '';
        $this->search = '';
        $this->filterJenjang = '';
        $this->filterStatus = 'aktif';
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function resetKaderForm(): void
    {
        $this->showKaderForm = false;
        $this->kaderEditId = null;
        $this->kNama = '';
        $this->kHp = '';
        $this->kWa = '';
        $this->kNik = '';
        $this->kKta = '';
        $this->kJenjang = 'penggerak';
        $this->kDapil = '';
        $this->kKecamatan = '';
        $this->kDesa = '';
        $this->kRw = '';
        $this->kRt = '';
        $this->kIsKorwe = false;
        $this->kIsKorte = false;
        $this->kIsUpa = false;
        $this->kJabatanUpa = 'anggota';
        $this->kIsPenggalang = false;
        $this->kIsSaksi = false;
        $this->kKeahlian = [];
        $this->kBisaDeploy = true;
        $this->kStatus = 'aktif';
        $this->resetErrorBag();
    }

    public function resetPelatihanForm(): void
    {
        $this->showPelatihanForm = false;
        $this->pelEditId = null;
        $this->pelNama = '';
        $this->pelJenjangTarget = 'penggerak';
        $this->pelJenis = 'diklat';
        $this->pelTanggal = now()->format('Y-m-d');
        $this->pelLokasi = '';
        $this->pelInstruktur = '';
        $this->pelKapasitas = 0;
        $this->resetErrorBag();
    }

    public function resetDeployForm(): void
    {
        $this->showDeployForm = false;
        $this->deployKaderId = '';
        $this->deployKeDapil = '';
        $this->deployKeKecamatan = '';
        $this->deployKeDesa = '';
        $this->deployKeRw = '';
        $this->deployAlasan = 'kebutuhan_wilayah';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.kaderisasi.index')
            ->layout('components.layouts.app.sidebar');
    }

    private function filteredKaderQuery(): Builder
    {
        return Kader::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->filterJenjang !== '', fn (Builder $query) => $query->where('jenjang', $this->filterJenjang))
            ->when($this->filterStatus !== '', fn (Builder $query) => $query->where('status', $this->filterStatus))
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $nested): void {
                    $nested->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('no_hp', 'like', '%'.$this->search.'%')
                        ->orWhere('no_wa', 'like', '%'.$this->search.'%')
                        ->orWhere('desa', 'like', '%'.$this->search.'%');
                });
            });
    }

    private function filteredPelatihanQuery(): Builder
    {
        return Pelatihan::query()
            ->when($this->filterJenjang !== '', fn (Builder $query) => $query->where('jenjang_target', $this->filterJenjang))
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil_terkait', $this->selectedDapil))
            ->when($this->filterStatus !== '' && in_array($this->filterStatus, ['dijadwalkan', 'berlangsung', 'selesai', 'dibatalkan'], true), fn (Builder $query) => $query->where('status', $this->filterStatus));
    }

    private function filteredDeploymentQuery(): Builder
    {
        return DeploymentLog::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('ke_dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('ke_kecamatan', $this->selectedKecamatan))
            ->with('kader');
    }

    private function filteredDataRwQuery(): Builder
    {
        return DataRw::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function emptyRwQuery(?string $targetWilayahId): Builder
    {
        $usedRw = Kader::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('status', 'aktif')
            ->pluck('nomor_rw');

        return DataRw::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->whereNotIn('nomor_rw', $usedRw)
            ->orderBy('nomor_rw');
    }

    private function emptyRwKecamatanCollection(string $kecamatan, string $excludeTargetWilayahId): Collection
    {
        $usedRwKeys = Kader::query()
            ->where('kecamatan', $kecamatan)
            ->where('status', 'aktif')
            ->get()
            ->map(fn (Kader $kader) => $kader->target_wilayah_id.':'.$kader->nomor_rw);

        return DataRw::query()
            ->where('kecamatan', $kecamatan)
            ->where('target_wilayah_id', '!=', $excludeTargetWilayahId)
            ->get()
            ->reject(fn (DataRw $row) => $usedRwKeys->contains($row->target_wilayah_id.':'.$row->nomor_rw))
            ->sortBy('nomor_rw')
            ->values();
    }

    private function resolveTargetWilayahId(string $dapil, string $kecamatan, string $desa): ?string
    {
        if ($dapil === '' || $kecamatan === '' || $desa === '') {
            return null;
        }

        return TargetWilayah::query()
            ->where('dapil', $dapil)
            ->where('kecamatan', $kecamatan)
            ->where('desa', $desa)
            ->value('id');
    }

    private function buildRtOptions(?string $targetWilayahId, string $rw): Collection
    {
        if (! $targetWilayahId || $rw === '') {
            return collect();
        }

        $rtOptions = Korte::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $rw)
            ->whereNotNull('nomor_rt')
            ->pluck('nomor_rt')
            ->merge(
                Kader::query()
                    ->where('target_wilayah_id', $targetWilayahId)
                    ->where('nomor_rw', $rw)
                    ->whereNotNull('nomor_rt')
                    ->pluck('nomor_rt')
            )
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($rtOptions->isNotEmpty()) {
            return $rtOptions;
        }

        return collect(range(1, 20))->map(fn (int $value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT));
    }

    private function syncKaderInfraRoles(Kader $kader): void
    {
        if ($kader->is_korwe && $kader->target_wilayah_id && $kader->nomor_rw) {
            Korwe::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $kader->target_wilayah_id,
                    'nomor_rw' => $kader->nomor_rw,
                ],
                [
                    'nama_koordinator' => $kader->nama,
                    'no_hp' => $kader->no_wa ?: $kader->no_hp,
                    'status' => 'terbentuk',
                    'created_by' => auth()->id(),
                ]
            );
        }

        if ($kader->is_korte && $kader->target_wilayah_id && $kader->nomor_rw && $kader->nomor_rt) {
            Korte::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $kader->target_wilayah_id,
                    'nomor_rw' => $kader->nomor_rw,
                    'nomor_rt' => $kader->nomor_rt,
                ],
                [
                    'nama_koordinator' => $kader->nama,
                    'no_hp' => $kader->no_wa ?: $kader->no_hp,
                    'status' => 'terbentuk',
                    'is_saksi_tps' => $kader->is_saksi,
                    'status_saksi' => $kader->is_saksi ? 'terkonfirmasi' : 'belum',
                    'created_by' => auth()->id(),
                ]
            );
        }
    }

    private function nextJenjang(string $currentJenjang): string
    {
        $ordered = collect(Kader::JENJANG_OPTIONS)->sortBy('order')->keys()->values();
        $index = $ordered->search($currentJenjang);

        if ($index === false || $index === $ordered->count() - 1) {
            return $currentJenjang;
        }

        return (string) $ordered[$index + 1];
    }

}
