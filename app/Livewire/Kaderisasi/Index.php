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
use App\Models\UpaRwMember;
use App\Models\User;
use App\Models\EventPeserta;
use App\Models\TitikRki;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $activeTab = 'map';
    public string $selectedDapil = '';
    public string $selectedKecamatan = '';
    public string $selectedDesa = '';
    public ?string $selectedVillageId = null;
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

    // UPA Formation Properties
    public string $upaRw = '';
    public array $upaSelections = [];

    public function mount(): void
    {
        $this->pelTanggal = now()->format('Y-m-d');
        $this->selectedDapil = 'BEKASI 1';
        $this->selectedKecamatan = 'SETU';
        $this->selectedDesa = 'BURANGKENG';
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
            ->paginate(10, ['*'], 'kaderPage');
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
                    '%s: %d kader di RW %s, bisa mutasi %d ke %d RW kosong',
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

    public function getDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    public function getAllWilayahsProperty(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil', 'kecamatan', 'desa')
            ->orderBy('desa')
            ->get();
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
        session()->flash('message', 'Kader berhasil dimutasi ke wilayah baru.');
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

    public function updatedSelectedDapil(): void
    {
        if ($this->selectedDapil === 'BEKASI 1') {
            $this->selectedKecamatan = 'SETU';
            $this->selectedDesa = 'BURANGKENG';
        } else {
            $this->selectedKecamatan = '';
            $this->selectedDesa = '';
        }
        $this->selectedVillageId = null;
        $this->upaRw = '';
        $this->upaSelections = [];
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatedSelectedKecamatan(): void
    {
        if ($this->selectedDapil === 'BEKASI 1' && $this->selectedKecamatan === 'SETU') {
            $this->selectedDesa = 'BURANGKENG';
        } else {
            $this->selectedDesa = '';
        }
        $this->selectedVillageId = null;
        $this->upaRw = '';
        $this->upaSelections = [];
        $this->resetPage('kaderPage');
        $this->resetPage('pelatihanPage');
    }

    public function updatedSelectedDesa(): void
    {
        $this->selectedVillageId = null;
        $this->upaRw = '';
        $this->upaSelections = [];
        $this->resetPage('kaderPage');
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
        $this->selectedDesa = '';
        $this->selectedVillageId = null;
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
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa))
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

    public function selectVillage(string $id): void
    {
        $this->selectedVillageId = $id;
        $w = TargetWilayah::find($id);
        if ($w) {
            $this->selectedDesa = $w->desa;
            $this->selectedKecamatan = $w->kecamatan;
            $this->selectedDapil = $w->dapil;
            $this->resetPage('kaderPage');
        }
    }

    public function closeVillageDetail(): void
    {
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
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
        $configs = $this->getMapConfigs();
        $config = null;

        if ($this->selectedKecamatan !== '') {
            $config = $configs[strtoupper($this->selectedKecamatan)] ?? null;
        } elseif ($this->selectedDapil !== '') {
            $config = $configs[strtoupper($this->selectedDapil)] ?? null;
        }

        if (!$config) {
            return [];
        }

        // Query active kaders per target_wilayah_id
        $kaderCounts = Kader::query()
            ->where('status', 'aktif')
            ->select('target_wilayah_id')
            ->selectRaw('COUNT(*) as total_kader')
            ->groupBy('target_wilayah_id')
            ->pluck('total_kader', 'target_wilayah_id');

        $wilayahs = TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn ($q) => $q->byDapil($this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->byKecamatan($this->selectedKecamatan))
            ->get();

        $maxKaders = max(1, $kaderCounts->max());

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                $count = $kaderCounts[$w->id] ?? 0;
                $target = $w->target_korwe_2029 ?: $w->jumlah_rw ?: 1;

                if ($count >= $target) {
                    $color = '#22c55e'; // Green
                } elseif ($count > 0) {
                    $color = '#eab308'; // Yellow
                } else {
                    $color = '#ef4444'; // Red
                }

                $size = 12 + round(($count / $maxKaders) * 16);

                $markers[] = [
                    'id' => $w->id,
                    'key' => $w->id,
                    'label' => "{$w->desa} · {$count}/{$target} Kader",
                    'x' => $config[$desaUpper]['x'],
                    'y' => $config[$desaUpper]['y'],
                    'size' => $size,
                    'color' => $color,
                    'count' => $count,
                    'target' => $target,
                    'desa' => $w->desa,
                    'kecamatan' => $w->kecamatan
                ];
            }
        }

        return $markers;
    }

    public function getSelectedVillageDetailProperty(): ?array
    {
        if (!$this->selectedVillageId) {
            return null;
        }

        $w = TargetWilayah::find($this->selectedVillageId);
        if (!$w) {
            return null;
        }

        $kaders = Kader::query()
            ->where('target_wilayah_id', $w->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $target = $w->target_korwe_2029 ?: $w->jumlah_rw ?: 1;
        $count = $kaders->count();

        if ($count >= $target) {
            $statusLabel = 'Mencapai Target';
            $statusBg = '#dcfce7';
            $statusText = '#166534';
            $statusDot = '#22c55e';
        } elseif ($count > 0) {
            $statusLabel = 'Parsial';
            $statusBg = '#fef3c7';
            $statusText = '#b45309';
            $statusDot = '#eab308';
        } else {
            $statusLabel = 'Belum Ada Kader';
            $statusBg = '#fee2e2';
            $statusText = '#991b1b';
            $statusDot = '#ef4444';
        }

        return [
            'id' => $w->id,
            'desa' => $w->desa,
            'kecamatan' => $w->kecamatan,
            'dapil' => $w->dapil,
            'jumlah_rw' => $w->jumlah_rw,
            'jumlah_rt' => $w->jumlah_rt,
            'jumlah_tps' => $w->jumlah_tps,
            'jumlah_dpt' => $w->jumlah_dpt,
            'target' => $target,
            'count' => $count,
            'statusLabel' => $statusLabel,
            'statusBg' => $statusBg,
            'statusText' => $statusText,
            'statusDot' => $statusDot,
            'kaders' => $kaders
        ];
    }

    public function getMapConfigs(): array
    {
        return [
            'BEKASI 1' => [
                'CIJENGKOL' => ['x' => 18.2, 'y' => 18.6],
                'LUBANGBUAYA' => ['x' => 22.1, 'y' => 9.2],
                'CIBENING' => ['x' => 29.8, 'y' => 28.7],
                'BURANGKENG' => ['x' => 12.6, 'y' => 26.7],
                'TAMAN SARI' => ['x' => 15.0, 'y' => 39.0],
                'TAMAN RAHAYU' => ['x' => 4.5, 'y' => 39.4],
                'CIKARAGEMAN' => ['x' => 14.6, 'y' => 48.3],
                'RAGEMANUNGGAL' => ['x' => 9.8, 'y' => 54.9],
                'MUKTIJAYA' => ['x' => 20.4, 'y' => 56.1],
                'CILEDUG' => ['x' => 22.4, 'y' => 30.5],
                'KERTARAHAYU' => ['x' => 27.0, 'y' => 43.5],
                'JAYAMULYA' => ['x' => 32.8, 'y' => 56.1],
                'JAYASAMPURNA' => ['x' => 35.0, 'y' => 44.7],
                'SUKARAGAM' => ['x' => 44.0, 'y' => 53.3],
                'SUKASARI' => ['x' => 46.5, 'y' => 46.8],
                'SIRNAJAYA' => ['x' => 40.9, 'y' => 58.4],
                'CILANGKARA' => ['x' => 55.1, 'y' => 44.6],
                'NAGACIPTA' => ['x' => 51.1, 'y' => 60.2],
                'NAGASARI' => ['x' => 51.9, 'y' => 56.0],
                'CICAU' => ['x' => 61.6, 'y' => 35.3],
                'SUKAMAHI' => ['x' => 70.8, 'y' => 37.5],
                'JAYAMUKTI' => ['x' => 74.0, 'y' => 15.8],
                'HEGARMUKTI' => ['x' => 75.5, 'y' => 23.7],
                'PASIRANJI' => ['x' => 82.2, 'y' => 31.0],
                'PASIRTANJUNG' => ['x' => 86.9, 'y' => 24.2],
                'CIBATU' => ['x' => 74.1, 'y' => 44.5],
                'CIANTRA' => ['x' => 64.9, 'y' => 44.3],
                'SUKASEJATI' => ['x' => 82.8, 'y' => 58.8],
                'SUKADAMI' => ['x' => 65.9, 'y' => 61.0],
                'SUKARESMI' => ['x' => 74.8, 'y' => 67.0],
                'SERANG' => ['x' => 74.0, 'y' => 55.8],
                'PASIRSARI' => ['x' => 82.9, 'y' => 44.4],
                'CIBARUSAH JAYA' => ['x' => 31.3, 'y' => 76.0],
                'CIBARUSAH KOTA' => ['x' => 40.2, 'y' => 75.8],
                'SINDANGMULYA' => ['x' => 33.2, 'y' => 67.8],
                'WIBAWAMULYA' => ['x' => 43.8, 'y' => 68.4],
                'RIDOGALIH' => ['x' => 47.9, 'y' => 85.6],
                'RIDOMANAH' => ['x' => 58.8, 'y' => 77.0],
                'SIRNAJATI' => ['x' => 37.9, 'y' => 83.6],
                'MEDALKRISNA' => ['x' => 64.8, 'y' => 69.9],
                'SUKAMUKTI' => ['x' => 75.0, 'y' => 71.1],
                'SUKABUNGAH' => ['x' => 84.1, 'y' => 75.2],
                'KARANGINDAH' => ['x' => 62.0, 'y' => 86.1],
                'BOJONGMANGU' => ['x' => 70.9, 'y' => 80.2],
                'KARANGMULYA' => ['x' => 79.6, 'y' => 91.7]
            ],
            'BEKASI 4' => [
                'SUKARINGIN' => ['x' => 34.0, 'y' => 8.8],
                'SUKATENANG' => ['x' => 22.2, 'y' => 17.2],
                'SUKERTA' => ['x' => 51.8, 'y' => 18.0],
                'SUKAWANGI' => ['x' => 60.3, 'y' => 29.4],
                'SUKABUDI' => ['x' => 47.2, 'y' => 37.2],
                'SUKADAYA' => ['x' => 41.8, 'y' => 41.5],
                'SUKAMEKAR' => ['x' => 27.5, 'y' => 38.7],
                'SUKABAKTI' => ['x' => 56.2, 'y' => 50.0],
                'SUKAMAJU' => ['x' => 66.2, 'y' => 45.2],
                'SUKAMANTRI' => ['x' => 68.8, 'y' => 41.6],
                'SUKARAHAYU' => ['x' => 61.2, 'y' => 44.7],
                'SUKARAJA' => ['x' => 62.8, 'y' => 51.6],
                'SUKARAPIH' => ['x' => 66.7, 'y' => 53.1],
                'SUKAWIJAYA' => ['x' => 74.1, 'y' => 47.8],
                'SRIAMUR' => ['x' => 13.7, 'y' => 56.4],
                'SRIJAYA' => ['x' => 28.1, 'y' => 53.2],
                'SRIMAHI' => ['x' => 21.7, 'y' => 60.6],
                'SRIMUKTI' => ['x' => 20.4, 'y' => 49.1],
                'SATRIAMEKAR' => ['x' => 14.0, 'y' => 70.3],
                'JEJALENJAYA' => ['x' => 23.6, 'y' => 69.1],
                'SATRIAJAYA' => ['x' => 18.6, 'y' => 84.8],
                'KARANGSATRIA' => ['x' => 10.7, 'y' => 96.2],
                'BANJARSARI' => ['x' => 67.2, 'y' => 78.2],
                'SUKAASIH' => ['x' => 61.1, 'y' => 89.7],
                'SUKADARMA' => ['x' => 83.6, 'y' => 62.2],
                'SUKAHURIP' => ['x' => 73.5, 'y' => 57.7],
                'SUKAMANAH' => ['x' => 65.8, 'y' => 60.8],
                'SUKAMULYA' => ['x' => 79.4, 'y' => 72.6],
                'SUKARUKUN' => ['x' => 70.2, 'y' => 97.4]
            ],
            'SETU' => [
                'LUBANGBUAYA' => ['x' => 75.0, 'y' => 8.5],
                'CIJENGKOL' => ['x' => 56.0, 'y' => 18.5],
                'BURANGKENG' => ['x' => 36.0, 'y' => 28.0],
                'CILEDUG' => ['x' => 63.0, 'y' => 37.0],
                'CIBENING' => ['x' => 87.5, 'y' => 45.0],
                'TAMAN SARI' => ['x' => 48.0, 'y' => 55.0],
                'TAMAN RAHAYU' => ['x' => 13.5, 'y' => 58.5],
                'CIKARAGEMAN' => ['x' => 52.0, 'y' => 69.0],
                'KERTARAHAYU' => ['x' => 79.0, 'y' => 71.0],
                'RAGEMANUNGGAL' => ['x' => 33.0, 'y' => 84.0],
                'MUKTIJAYA' => ['x' => 58.0, 'y' => 87.0]
            ]
        ];
    }

    public function updatedUpaRw(): void
    {
        $this->upaSelections = [];
        $candidates = $this->upaCandidates;
        foreach ($candidates as $key => $c) {
            $this->upaSelections[$key] = [
                'selected' => false,
                'jabatan' => 'anggota',
            ];
        }
    }

    public function getUpaRwOptionsProperty(): Collection
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->selectedDapil, $this->selectedKecamatan, $this->selectedDesa);

        if (! $targetWilayahId) {
            return collect();
        }

        return DataRw::query()->where('target_wilayah_id', $targetWilayahId)->orderBy('nomor_rw')->pluck('nomor_rw');
    }

    private function isAlreadyUpa(string $name, string $phone, string $wa, Collection $existingUpaPhoneNames): bool
    {
        foreach ($existingUpaPhoneNames as $upa) {
            if ($phone !== '' && ($upa['phone'] === $phone || $upa['wa'] === $phone)) {
                return true;
            }
            if ($wa !== '' && ($upa['phone'] === $wa || $upa['wa'] === $wa)) {
                return true;
            }
            if ($upa['name'] === $name) {
                return true;
            }
        }
        return false;
    }

    public function getUpaCandidatesProperty(): Collection
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->selectedDapil, $this->selectedKecamatan, $this->selectedDesa);
        if (!$targetWilayahId || $this->upaRw === '') {
            return collect();
        }

        // 1. Get existing UPAs (so we exclude them from candidates)
        $existingUpas = Kader::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->where('status', 'aktif')
            ->where(function (Builder $query) {
                $query->where('is_upa', true)
                      ->orWhere('jenjang', 'pelopor');
            })
            ->get();
        
        $existingUpaPhoneNames = $existingUpas->map(function ($k) {
            return [
                'name' => strtolower(trim($k->nama)),
                'phone' => $k->no_hp ? preg_replace('/[^0-9]/', '', (string)$k->no_hp) : '',
                'wa' => $k->no_wa ? preg_replace('/[^0-9]/', '', (string)$k->no_wa) : '',
            ];
        });

        $existingUpaRwMembers = UpaRwMember::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->where('status', 'aktif')
            ->get();
            
        $existingUpaPhoneNames = $existingUpaPhoneNames->concat($existingUpaRwMembers->map(function ($m) {
            return [
                'name' => strtolower(trim($m->nama)),
                'phone' => $m->no_hp ? preg_replace('/[^0-9]/', '', (string)$m->no_hp) : '',
                'wa' => '',
            ];
        }));

        $candidatesMap = [];

        // Source 1: Kader (Kaderisasi)
        $kaders = Kader::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->where('status', 'aktif')
            ->where('jenjang', '!=', 'pelopor')
            ->where(function (Builder $query) {
                $query->where('is_upa', false)
                      ->orWhereNull('is_upa');
            })
            ->get();

        foreach ($kaders as $k) {
            $normalizedPhone = $k->no_hp ? preg_replace('/[^0-9]/', '', (string)$k->no_hp) : '';
            $normalizedWa = $k->no_wa ? preg_replace('/[^0-9]/', '', (string)$k->no_wa) : '';
            $normalizedName = strtolower(trim($k->nama));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, $normalizedWa, $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'no_hp' => $k->no_hp,
                    'no_wa' => $k->no_wa,
                    'jenjang' => $k->jenjang_config['label'] ?? 'Penggerak',
                    'sources' => ['Kaderisasi'],
                    'kader_id' => $k->id,
                    'type' => 'kader',
                ];
            } else {
                if (!in_array('Kaderisasi', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'Kaderisasi';
                }
                if (empty($candidatesMap[$key]['kader_id'])) {
                    $candidatesMap[$key]['kader_id'] = $k->id;
                    $candidatesMap[$key]['id'] = $k->id;
                    $candidatesMap[$key]['type'] = 'kader';
                }
            }
        }

        // Source 2: Member Web Publik (User)
        $users = User::query()
            ->where('desa', $this->selectedDesa)
            ->where('nomor_rw', $this->upaRw)
            ->where('role', 'kader')
            ->get();

        foreach ($users as $u) {
            $normalizedPhone = $u->phone ? preg_replace('/[^0-9]/', '', (string)$u->phone) : '';
            $normalizedName = strtolower(trim($u->name));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, '', $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $u->id,
                    'nama' => $u->name,
                    'no_hp' => $u->phone,
                    'no_wa' => $u->phone,
                    'jenjang' => 'Penggerak',
                    'sources' => ['Web Publik'],
                    'user_id' => $u->id,
                    'type' => 'user',
                ];
            } else {
                if (!in_array('Web Publik', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'Web Publik';
                }
                if (!empty($u->phone) && empty($candidatesMap[$key]['no_hp'])) {
                    $candidatesMap[$key]['no_hp'] = $u->phone;
                    $candidatesMap[$key]['no_wa'] = $u->phone;
                }
            }
        }

        // Source 3: Peserta Event (EventPeserta)
        $pesertas = EventPeserta::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->get();

        foreach ($pesertas as $p) {
            $normalizedPhone = $p->no_hp ? preg_replace('/[^0-9]/', '', (string)$p->no_hp) : '';
            $normalizedWa = $p->no_wa ? preg_replace('/[^0-9]/', '', (string)$p->no_wa) : '';
            $normalizedName = strtolower(trim($p->nama));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, $normalizedWa, $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'no_hp' => $p->no_hp,
                    'no_wa' => $p->no_wa,
                    'jenjang' => 'Penggerak',
                    'sources' => ['Peserta Event'],
                    'event_peserta_id' => $p->id,
                    'type' => 'event_peserta',
                ];
            } else {
                if (!in_array('Peserta Event', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'Peserta Event';
                }
            }
        }

        // Source 4: KORWE
        $korwes = Korwe::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->get();

        foreach ($korwes as $kw) {
            $normalizedPhone = $kw->no_hp ? preg_replace('/[^0-9]/', '', (string)$kw->no_hp) : '';
            $normalizedName = strtolower(trim($kw->nama_koordinator));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, '', $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $kw->id,
                    'nama' => $kw->nama_koordinator,
                    'no_hp' => $kw->no_hp,
                    'no_wa' => $kw->no_hp,
                    'jenjang' => 'Penggerak',
                    'sources' => ['KORWE'],
                    'korwe_id' => $kw->id,
                    'type' => 'korwe',
                ];
            } else {
                if (!in_array('KORWE', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'KORWE';
                }
            }
        }

        // Source 5: KORTE
        $kortes = Korte::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->get();

        foreach ($kortes as $kt) {
            $normalizedPhone = $kt->no_hp ? preg_replace('/[^0-9]/', '', (string)$kt->no_hp) : '';
            $normalizedName = strtolower(trim($kt->nama_koordinator));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, '', $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $kt->id,
                    'nama' => $kt->nama_koordinator,
                    'no_hp' => $kt->no_hp,
                    'no_wa' => $kt->no_hp,
                    'jenjang' => 'Penggerak',
                    'sources' => ['KORTE'],
                    'korte_id' => $kt->id,
                    'type' => 'korte',
                ];
            } else {
                if (!in_array('KORTE', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'KORTE';
                }
            }
        }

        // Source 6: Koordinator RKI (TitikRki)
        $rkis = TitikRki::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->get();

        foreach ($rkis as $rki) {
            $normalizedPhone = $rki->no_hp_penggerak ? preg_replace('/[^0-9]/', '', (string)$rki->no_hp_penggerak) : '';
            $normalizedName = strtolower(trim($rki->nama_penggerak));
            
            if ($this->isAlreadyUpa($normalizedName, $normalizedPhone, '', $existingUpaPhoneNames)) {
                continue;
            }
            
            $key = $normalizedPhone ?: $normalizedName;
            if (!isset($candidatesMap[$key])) {
                $candidatesMap[$key] = [
                    'id' => $rki->id,
                    'nama' => $rki->nama_penggerak,
                    'no_hp' => $rki->no_hp_penggerak,
                    'no_wa' => $rki->no_hp_penggerak,
                    'jenjang' => 'Penggerak',
                    'sources' => ['Koordinator RKI'],
                    'rki_id' => $rki->id,
                    'type' => 'rki',
                ];
            } else {
                if (!in_array('Koordinator RKI', $candidatesMap[$key]['sources'], true)) {
                    $candidatesMap[$key]['sources'][] = 'Koordinator RKI';
                }
            }
        }

        return collect($candidatesMap)->map(function ($c) {
            $c['source_label'] = implode(', ', $c['sources']);
            return $c;
        })->sortBy('nama');
    }

    public function getExistingUpaMembersProperty(): Collection
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->selectedDapil, $this->selectedKecamatan, $this->selectedDesa);
        if (!$targetWilayahId || $this->upaRw === '') {
            return collect();
        }

        $kaders = Kader::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->where('status', 'aktif')
            ->where(function (Builder $query) {
                $query->where('is_upa', true)
                      ->orWhere('jenjang', 'pelopor');
            })
            ->get();

        $rwMembers = UpaRwMember::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->where('nomor_rw', $this->upaRw)
            ->where('status', 'aktif')
            ->get();

        $merged = collect();

        foreach ($kaders as $k) {
            $key = strtolower(trim($k->nama));
            $merged[$key] = $k;
        }

        foreach ($rwMembers as $m) {
            $key = strtolower(trim($m->nama));
            if (!isset($merged[$key])) {
                $matchingKader = Kader::query()
                    ->where('target_wilayah_id', $targetWilayahId)
                    ->where('nomor_rw', $this->upaRw)
                    ->where('nama', $m->nama)
                    ->first();

                if ($matchingKader) {
                    $matchingKader->is_upa = true;
                    if (empty($matchingKader->jabatan_upa)) {
                        $matchingKader->jabatan_upa = $m->jabatan ?: 'anggota';
                    }
                    $merged[$key] = $matchingKader;
                } else {
                    $virtualKader = new class {
                        public string $nama;
                        public ?string $no_wa = null;
                        public ?string $no_hp = null;
                        public string $jenjang = 'penggerak';
                        public ?string $jabatan_upa = null;

                        public function __get($key) {
                            if ($key === 'jenjang_config') {
                                return [
                                    'label' => 'Penggerak',
                                    'color' => '#93c5fd',
                                    'gradient' => 'linear-gradient(135deg,#bfdbfe 0%,#93c5fd 100%)',
                                    'text' => '#1e3a5f',
                                    'order' => 1
                                ];
                            }
                            return null;
                        }

                        public function __isset($key) {
                            return $key === 'jenjang_config';
                        }
                    };
                    $virtualKader->nama = $m->nama;
                    $virtualKader->no_wa = $m->no_hp;
                    $virtualKader->no_hp = $m->no_hp;
                    $virtualKader->jabatan_upa = $m->jabatan ?: 'anggota';
                    $merged[$key] = $virtualKader;
                }
            } else {
                if (empty($merged[$key]->jabatan_upa)) {
                    $merged[$key]->jabatan_upa = $m->jabatan ?: 'anggota';
                }
            }
        }

        return $merged->sortBy('nama')->values();
    }

    public function bentukUpa(): void
    {
        $targetWilayahId = $this->resolveTargetWilayahId($this->selectedDapil, $this->selectedKecamatan, $this->selectedDesa);
        if (!$targetWilayahId || $this->upaRw === '') {
            session()->flash('error', 'Silakan pilih wilayah dengan lengkap.');
            return;
        }

        $selectedCount = 0;
        foreach ($this->upaSelections as $key => $data) {
            if (!empty($data['selected'])) {
                $candidate = $this->upaCandidates->get($key);
                if ($candidate) {
                    $jabatan = $data['jabatan'] ?? 'anggota';
                    
                    // 1. Ensure a Kader record exists
                    $kader = null;
                    if (!empty($candidate['kader_id'])) {
                        $kader = Kader::query()->find($candidate['kader_id']);
                    }
                    
                    if (!$kader) {
                        $kader = Kader::query()
                            ->where('nama', $candidate['nama'])
                            ->where('target_wilayah_id', $targetWilayahId)
                            ->where('nomor_rw', $this->upaRw)
                            ->first();
                    }

                    if ($kader) {
                        $kader->update([
                            'is_upa' => true,
                            'jabatan_upa' => $jabatan,
                        ]);
                    } else {
                        $kader = Kader::query()->create([
                            'nama' => $candidate['nama'],
                            'no_hp' => $candidate['no_hp'],
                            'no_wa' => $candidate['no_wa'],
                            'jenjang' => 'penggerak',
                            'tanggal_jenjang' => now(),
                            'dapil' => $this->selectedDapil,
                            'kecamatan' => $this->selectedKecamatan,
                            'desa' => $this->selectedDesa,
                            'nomor_rw' => $this->upaRw,
                            'target_wilayah_id' => $targetWilayahId,
                            'is_upa' => true,
                            'jabatan_upa' => $jabatan,
                            'status' => 'aktif',
                            'bisa_deploy' => true,
                            'created_by' => auth()->id(),
                        ]);
                    }

                    // 2. Create/Update UpaRwMember
                    UpaRwMember::query()->updateOrCreate(
                        [
                            'target_wilayah_id' => $targetWilayahId,
                            'nomor_rw' => $this->upaRw,
                            'nama' => $kader->nama,
                        ],
                        [
                            'dapil' => $this->selectedDapil,
                            'kecamatan' => $this->selectedKecamatan,
                            'desa' => $this->selectedDesa,
                            'no_hp' => $kader->no_wa ?: $kader->no_hp,
                            'jabatan' => $jabatan,
                            'asal' => 'kader_baru',
                            'status' => 'aktif',
                            'created_by' => auth()->id(),
                        ]
                    );

                    $selectedCount++;
                }
            }
        }

        if ($selectedCount === 0) {
            session()->flash('error', 'Silakan pilih setidaknya satu kader untuk dimasukkan ke UPA.');
            return;
        }

        $this->upaSelections = [];
        session()->flash('message', "Berhasil membentuk kelompok UPA baru dengan {$selectedCount} anggota di RW {$this->upaRw} Desa {$this->selectedDesa}.");
    }

}

