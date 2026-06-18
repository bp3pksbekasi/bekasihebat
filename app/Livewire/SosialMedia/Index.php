<?php

declare(strict_types=1);

namespace App\Livewire\SosialMedia;

use App\Models\AnggotaDewan;
use App\Models\DataRw;
use App\Models\DistribusiMateri;
use App\Models\KontenMedsos;
use App\Models\MateriDigital;
use App\Models\TargetWilayah;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $activeTab = 'video_materi';
    public string $selectedBulan = '';
    public string $selectedTahun = '';
    public string $selectedDewanId = '';
    public string $filterPlatform = '';
    public string $selectedDapil = '';
    public string $selectedKecamatan = '';
    public string $selectedDesa = '';
    public ?string $selectedTargetWilayahId = null;
    public string $selectedRw = '';

    public bool $showDewanForm = false;
    public ?string $dewanEditId = null;
    public string $dNama = '';
    public string $dJabatan = '';
    public string $dDapil = '';
    public int $dSuara2024 = 0;
    public bool $dStatusPetahana = false;
    public string $dJabatanFraksi = '';
    public string $dJabatanDprd = '';
    public string $dJabatanPartai = '';
    public string $dWilayahDapil = '';
    public string $dHp = '';
    public string $dInstagram = '';
    public int $dIgFollowers = 0;
    public string $dTiktok = '';
    public int $dTtFollowers = 0;
    public string $dYoutube = '';
    public int $dYtSubs = 0;
    public string $dTwitter = '';
    public int $dTwFollowers = 0;
    public string $dFacebook = '';
    public int $dFbFollowers = 0;
    public string $dTimNama = '';
    public string $dTimHp = '';

    public bool $showKontenForm = false;
    public ?string $kontenEditId = null;
    public string $kcDewanId = '';
    public string $kcPlatform = 'instagram';
    public string $kcJenis = 'post';
    public string $kcCaption = '';
    public string $kcUrl = '';
    public string $kcTanggal = '';
    public int $kcLikes = 0;
    public int $kcComments = 0;
    public int $kcShares = 0;
    public int $kcViews = 0;
    public string $kcTopik = 'pelayanan';
    public string $kcDapil = '';
    public string $kcRw = '';
    public string $kcDesa = '';
    public bool $kcIsVideo = false;

    public bool $showMateriForm = false;
    public string $matJudul = '';
    public string $matJenis = 'poster';
    public string $matDeskripsi = '';
    public $matFile = null;
    public string $matTopik = '';

    public bool $showDistribusiForm = false;
    public string $distMateriId = '';
    public string $distChannel = 'wa_blast';
    public string $distDapil = '';
    public int $distRwCount = 0;
    public string $distCatatan = '';

    public function mount(): void
    {
        $this->selectedBulan = now()->format('m');
        $this->selectedTahun = now()->format('Y');
        $this->kcTanggal = now()->format('Y-m-d');
    }

    public function updatingSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
    }

    public function updatingSelectedKecamatan(): void
    {
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
    }

    public function updatingSelectedDesa(): void
    {
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
    }

    public function updatedSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
    }

    public function updatedSelectedKecamatan(): void
    {
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
    }

    public function updatedSelectedDesa(string $value): void
    {
        if ($value === '') {
            $this->selectedTargetWilayahId = null;
        } else {
            $village = TargetWilayah::query()
                ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
                ->where('desa', $value)
                ->first();
            $this->selectedTargetWilayahId = $village ? (string) $village->id : null;
        }
        $this->selectedRw = '';
    }

    public function selectDesa(string $targetWilayahId): void
    {
        $this->selectedTargetWilayahId = $this->selectedTargetWilayahId === $targetWilayahId ? null : $targetWilayahId;
        if ($this->selectedTargetWilayahId) {
            $w = TargetWilayah::find($this->selectedTargetWilayahId);
            $this->selectedDesa = $w ? $w->desa : '';
        } else {
            $this->selectedDesa = '';
        }
        $this->selectedRw = '';
    }

    public function closeVillageDetail(): void
    {
        $this->selectedTargetWilayahId = null;
        $this->selectedDesa = '';
        $this->selectedRw = '';
    }

    public function selectRw(string $rw): void
    {
        $this->selectedRw = $this->selectedRw === $rw ? '' : $rw;
    }

    public function updatedSelectedBulan(): void
    {
        $this->resetPage(pageName: 'kontenPage');
    }

    public function updatedSelectedTahun(): void
    {
        $this->resetPage(pageName: 'kontenPage');
    }

    public function updatedSelectedDewanId(): void
    {
        $this->resetPage(pageName: 'kontenPage');
    }

    public function updatedFilterPlatform(): void
    {
        $this->resetPage(pageName: 'kontenPage');
    }

    public function getKpiProperty(): array
    {
        $dewanQuery = $this->filteredDewanQuery();
        $kontenQuery = $this->filteredKontenBaseQuery();

        return [
            'total_dewan_aktif' => (int) (clone $dewanQuery)->aktif()->count(),
            'avg_popularitas' => round((float) ((clone $dewanQuery)->aktif()->avg('skor_popularitas') ?? 0), 1),
            'konten_bulan_ini' => (int) (clone $kontenQuery)->count(),
            'video_pelayanan_bulan_ini' => (int) (clone $kontenQuery)->where('is_video_pelayanan', true)->count(),
            'materi_didistribusi_bulan_ini' => (int) $this->filteredDistribusiQuery()->count(),
        ];
    }

    public function getDewanListProperty(): Collection
    {
        return $this->filteredDewanQuery()
            ->withCount([
                'kontens as konten_bulan_ini_count' => fn (Builder $query) => $this->applyPeriodeFilter($query, 'tanggal_posting'),
                'kontens as video_bulan_ini_count' => fn (Builder $query) => $this->applyPeriodeFilter($query, 'tanggal_posting')->where('is_video_pelayanan', true),
            ])
            ->orderByDesc('skor_popularitas')
            ->orderBy('nama')
            ->get();
    }

    public function getKontenListProperty(): LengthAwarePaginator
    {
        return $this->filteredKontenBaseQuery()
            ->with('anggotaDewan')
            ->orderByDesc('tanggal_posting')
            ->orderByDesc('created_at')
            ->paginate(8, ['*'], 'kontenPage');
    }

    public function getVideoPelayananProperty(): Collection
    {
        $videoItems = $this->filteredKontenBaseQuery()
            ->where('is_video_pelayanan', true)
            ->with('anggotaDewan')
            ->orderByDesc('tanggal_posting')
            ->get();

        $videoGroups = $videoItems
            ->filter(fn (KontenMedsos $item) => trim((string) $item->desa_terkait) !== '' && trim((string) $item->rw_terkait) !== '')
            ->groupBy(function (KontenMedsos $item): string {
                $dapil = $item->dapil_terkait ?: ($item->anggotaDewan?->dapil ?: '-');

                return implode('|', [
                    $dapil,
                    trim((string) $item->desa_terkait),
                    trim((string) $item->rw_terkait),
                ]);
            });

        $coverageRows = $this->filteredRwCoverageQuery()
            ->get()
            ->unique(fn (DataRw $row) => implode('|', [$row->dapil, $row->desa, $row->nomor_rw]))
            ->values()
            ->map(function (DataRw $row) use ($videoGroups): array {
                $key = implode('|', [$row->dapil, $row->desa, $row->nomor_rw]);
                $videos = collect($videoGroups->get($key, []))->values();

                return [
                    'key' => $key,
                    'dapil' => (string) $row->dapil,
                    'desa' => (string) $row->desa,
                    'rw' => (string) $row->nomor_rw,
                    'has_video' => $videos->isNotEmpty(),
                    'video_count' => $videos->count(),
                    'videos' => $videos,
                ];
            });

        $knownKeys = $coverageRows->pluck('key');
        $extraRows = $videoGroups
            ->reject(fn (Collection $items, string $key) => $knownKeys->contains($key))
            ->map(function (Collection $items, string $key): array {
                [$dapil, $desa, $rw] = explode('|', $key);

                return [
                    'key' => $key,
                    'dapil' => $dapil,
                    'desa' => $desa,
                    'rw' => $rw,
                    'has_video' => true,
                    'video_count' => $items->count(),
                    'videos' => $items->values(),
                ];
            })
            ->values();

        return $coverageRows
            ->concat($extraRows)
            ->sortBy([
                ['dapil', 'asc'],
                ['desa', 'asc'],
                ['rw', 'asc'],
            ])
            ->values();
    }

    public function getVideoCoverageSummaryProperty(): array
    {
        $rows = $this->videoPelayanan;

        return [
            'total_rw' => $rows->count(),
            'rw_sudah_video' => $rows->where('has_video', true)->count(),
            'rw_belum_video' => $rows->where('has_video', false)->count(),
            'total_video' => $rows->sum('video_count'),
        ];
    }

    public function getVideoPerDapilProperty(): Collection
    {
        return $this->videoPelayanan
            ->groupBy('dapil')
            ->map(function (Collection $rows, string $dapil): array {
                return [
                    'dapil' => $dapil,
                    'covered' => $rows->where('has_video', true)->count(),
                    'missing' => $rows->where('has_video', false)->count(),
                    'rows' => $rows->values(),
                ];
            })
            ->values()
            ->sortBy('dapil')
            ->values();
    }

    public function getMateriListProperty(): Collection
    {
        return MateriDigital::query()
            ->withCount('distribusis')
            ->with(['distribusis' => fn (Builder $query) => $query->latest('tanggal_distribusi')->latest('created_at')])
            ->orderByDesc('created_at')
            ->get();
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

        $videoGroups = KontenMedsos::query()
            ->where('is_video_pelayanan', true)
            ->when($this->selectedBulan !== '', fn ($q) => $q->whereMonth('tanggal_posting', (int) $this->selectedBulan))
            ->when($this->selectedTahun !== '', fn ($q) => $q->whereYear('tanggal_posting', (int) $this->selectedTahun))
            ->select('desa_terkait', 'rw_terkait')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim((string) $item->desa_terkait)))
            ->map(fn (Collection $items) => $items->map(fn ($item) => str_pad(trim((string) $item->rw_terkait), 3, '0', STR_PAD_LEFT))->unique()->count());

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                $totalRw = (int) ($rwCounts[$w->id] ?? 0);
                $desaLower = strtolower(trim($w->desa));
                $rwSudahVideo = (int) ($videoGroups[$desaLower] ?? 0);

                if ($totalRw > 0) {
                    if ($rwSudahVideo >= $totalRw) {
                        $color = '#22c55e';
                    } elseif ($rwSudahVideo > 0) {
                        $color = '#eab308';
                    } else {
                        $color = '#ef4444';
                    }
                } else {
                    $color = '#22c55e';
                }

                $size = 12 + ($totalRw > 0 ? (int) round(($rwSudahVideo / $totalRw) * 12) : 12);

                $markers[] = [
                    'id' => $w->id,
                    'key' => $w->id,
                    'label' => "{$w->desa} · {$rwSudahVideo}/{$totalRw} RW Ada Video",
                    'x' => $config[$desaUpper]['x'],
                    'y' => $config[$desaUpper]['y'],
                    'size' => $size,
                    'color' => $color,
                    'count' => $rwSudahVideo,
                    'target' => $totalRw,
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
            ->get(['id', 'kecamatan', 'desa']);

        if ($wilayahs->isEmpty()) {
            return collect();
        }

        $targetIds = $wilayahs->pluck('id');

        $rwCounts = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->selectRaw('target_wilayah_id, COUNT(*) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        $videoGroups = KontenMedsos::query()
            ->where('is_video_pelayanan', true)
            ->when($this->selectedBulan !== '', fn ($q) => $q->whereMonth('tanggal_posting', (int) $this->selectedBulan))
            ->when($this->selectedTahun !== '', fn ($q) => $q->whereYear('tanggal_posting', (int) $this->selectedTahun))
            ->select('desa_terkait', 'rw_terkait')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim((string) $item->desa_terkait)))
            ->map(fn (Collection $items) => $items->map(fn ($item) => str_pad(trim((string) $item->rw_terkait), 3, '0', STR_PAD_LEFT))->unique()->count());

        return $wilayahs->map(function (TargetWilayah $w) use ($rwCounts, $videoGroups): array {
            $total = (int) ($rwCounts[$w->id] ?? 0);
            $desaLower = strtolower(trim($w->desa));
            $rwSudahVideo = (int) ($videoGroups[$desaLower] ?? 0);
            $pct = $total > 0 ? (int) round(($rwSudahVideo / $total) * 100) : 0;

            return [
                'id' => $w->id,
                'desa' => $w->desa,
                'kecamatan' => $w->kecamatan,
                'total_rw' => $total,
                'rw_terisi' => $rwSudahVideo,
                'pct_terisi' => $pct,
            ];
        });
    }

    public function getSelectedVillageDetailProperty(): ?array
    {
        if ($this->selectedTargetWilayahId === null) {
            return null;
        }

        $w = TargetWilayah::find($this->selectedTargetWilayahId);
        if (!$w) {
            return null;
        }

        $rwList = DataRw::query()
            ->where('target_wilayah_id', $w->id)
            ->orderBy('nomor_rw')
            ->get(['nomor_rw', 'dpt', 'estimasi_pks', 'status_wilayah']);

        $villageVideos = KontenMedsos::query()
            ->where('is_video_pelayanan', true)
            ->where(fn ($query) => $query->where('desa_terkait', $w->desa)->orWhere('desa_terkait', strtolower($w->desa)))
            ->when($this->selectedBulan !== '', fn ($q) => $q->whereMonth('tanggal_posting', (int) $this->selectedBulan))
            ->when($this->selectedTahun !== '', fn ($q) => $q->whereYear('tanggal_posting', (int) $this->selectedTahun))
            ->with('anggotaDewan')
            ->get();

        $videoCounts = $villageVideos
            ->groupBy(fn ($item) => str_pad(trim((string) $item->rw_terkait), 3, '0', STR_PAD_LEFT))
            ->map(fn (Collection $items) => $items->count());

        $totalRw = $rwList->count();
        $rwSudahVideo = $videoCounts->count();
        $pct = $totalRw > 0 ? (int) round(($rwSudahVideo / $totalRw) * 100) : 0;

        $mappedRws = $rwList->map(function (DataRw $rw) use ($videoCounts): array {
            $rwNum = str_pad(trim((string) $rw->nomor_rw), 3, '0', STR_PAD_LEFT);
            return [
                'nomor_rw' => $rw->nomor_rw,
                'dpt' => (int) $rw->dpt,
                'estimasi_pks' => (int) $rw->estimasi_pks,
                'status' => $rw->status_wilayah,
                'video_count' => (int) ($videoCounts[$rwNum] ?? 0),
            ];
        });

        return [
            'id' => $w->id,
            'desa' => $w->desa,
            'kecamatan' => $w->kecamatan,
            'dapil' => $w->dapil,
            'total_rw' => $totalRw,
            'rw_terisi' => $rwSudahVideo,
            'pct_terisi' => $pct,
            'rw_list' => $mappedRws,
            'videos' => $villageVideos,
        ];
    }

    public function getDewanOptionsProperty(): Collection
    {
        return AnggotaDewan::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'dapil']);
    }

    public function getPlatformOptionsProperty(): array
    {
        return KontenMedsos::PLATFORM_OPTIONS;
    }

    public function getBulanOptionsProperty(): array
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }

    public function getTahunOptionsProperty(): Collection
    {
        $years = collect(range((int) now()->year - 2, (int) now()->year + 1))
            ->map(fn (int $year): string => (string) $year);

        $contentYears = KontenMedsos::query()
            ->selectRaw('DISTINCT YEAR(tanggal_posting) as tahun')
            ->pluck('tahun')
            ->filter()
            ->map(fn ($year): string => (string) $year);

        return $years->concat($contentYears)->unique()->sort()->values();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage(pageName: 'kontenPage');
    }

    public function openDewanForm(): void
    {
        $this->closeDrawer();
        $this->resetDewanForm();
        $this->showDewanForm = true;
        $this->activeTab = 'profil';
    }

    public function editDewan(string $dewanId): void
    {
        $dewan = AnggotaDewan::query()->findOrFail($dewanId);

        $this->closeDrawer();
        $this->showDewanForm = true;
        $this->dewanEditId = $dewan->id;
        $this->dNama = (string) $dewan->nama;
        $this->dJabatan = (string) $dewan->jabatan;
        $this->dDapil = (string) ($dewan->dapil ?? '');
        $this->dSuara2024 = (int) ($dewan->suara_2024 ?? 0);
        $this->dStatusPetahana = (bool) $dewan->status_petahana;
        $this->dJabatanFraksi = (string) ($dewan->jabatan_fraksi ?? '');
        $this->dJabatanDprd = (string) ($dewan->jabatan_dprd ?? '');
        $this->dJabatanPartai = (string) ($dewan->jabatan_partai ?? '');
        $this->dWilayahDapil = (string) ($dewan->wilayah_dapil ?? '');
        $this->dHp = (string) ($dewan->no_hp ?? '');
        $this->dInstagram = (string) ($dewan->instagram ?? '');
        $this->dIgFollowers = (int) $dewan->ig_followers;
        $this->dTiktok = (string) ($dewan->tiktok ?? '');
        $this->dTtFollowers = (int) $dewan->tt_followers;
        $this->dYoutube = (string) ($dewan->youtube ?? '');
        $this->dYtSubs = (int) $dewan->yt_subscribers;
        $this->dTwitter = (string) ($dewan->twitter ?? '');
        $this->dTwFollowers = (int) $dewan->tw_followers;
        $this->dFacebook = (string) ($dewan->facebook ?? '');
        $this->dFbFollowers = (int) $dewan->fb_followers;
        $this->dTimNama = (string) ($dewan->tim_media_nama ?? '');
        $this->dTimHp = (string) ($dewan->tim_media_hp ?? '');
    }

    public function simpanDewan(): void
    {
        $data = $this->validate($this->dewanRules());

        $dewan = AnggotaDewan::query()->find($this->dewanEditId) ?? new AnggotaDewan();
        $dewan->fill([
            'nama' => $data['dNama'],
            'jabatan' => $data['dJabatan'],
            'dapil' => $data['dDapil'] !== '' ? $data['dDapil'] : null,
            'suara_2024' => (int) $data['dSuara2024'],
            'status_petahana' => (bool) $data['dStatusPetahana'],
            'jabatan_fraksi' => $data['dJabatanFraksi'] !== '' ? $data['dJabatanFraksi'] : null,
            'jabatan_dprd' => $data['dJabatanDprd'] !== '' ? $data['dJabatanDprd'] : null,
            'jabatan_partai' => $data['dJabatanPartai'] !== '' ? $data['dJabatanPartai'] : null,
            'wilayah_dapil' => $data['dWilayahDapil'] !== '' ? $data['dWilayahDapil'] : null,
            'no_hp' => $data['dHp'] !== '' ? $data['dHp'] : null,
            'instagram' => $data['dInstagram'] !== '' ? $data['dInstagram'] : null,
            'ig_followers' => (int) $data['dIgFollowers'],
            'tiktok' => $data['dTiktok'] !== '' ? $data['dTiktok'] : null,
            'tt_followers' => (int) $data['dTtFollowers'],
            'youtube' => $data['dYoutube'] !== '' ? $data['dYoutube'] : null,
            'yt_subscribers' => (int) $data['dYtSubs'],
            'twitter' => $data['dTwitter'] !== '' ? $data['dTwitter'] : null,
            'tw_followers' => (int) $data['dTwFollowers'],
            'facebook' => $data['dFacebook'] !== '' ? $data['dFacebook'] : null,
            'fb_followers' => (int) $data['dFbFollowers'],
            'tim_media_nama' => $data['dTimNama'] !== '' ? $data['dTimNama'] : null,
            'tim_media_hp' => $data['dTimHp'] !== '' ? $data['dTimHp'] : null,
            'status' => 'aktif',
            'created_by' => $dewan->exists ? $dewan->created_by : auth()->id(),
        ]);
        $dewan->save();

        $this->syncPopularitas($dewan->id);
        $this->closeDrawer();
        session()->flash('message', 'Profil dewan berhasil disimpan.');
    }

    public function openKontenForm(string $dewanId = ''): void
    {
        $this->closeDrawer();
        $this->resetKontenForm();
        $this->showKontenForm = true;
        $this->kcDewanId = $dewanId !== '' ? $dewanId : $this->selectedDewanId;
        $this->activeTab = 'konten';

        if ($this->kcDewanId !== '') {
            $dewan = AnggotaDewan::query()->find($this->kcDewanId);
            $this->kcDapil = (string) ($dewan?->dapil ?? '');
        }
    }

    public function editKonten(string $kontenId): void
    {
        $konten = KontenMedsos::query()->findOrFail($kontenId);

        $this->closeDrawer();
        $this->showKontenForm = true;
        $this->kontenEditId = $konten->id;
        $this->kcDewanId = (string) $konten->anggota_dewan_id;
        $this->kcPlatform = (string) $konten->platform;
        $this->kcJenis = (string) $konten->jenis_konten;
        $this->kcCaption = (string) ($konten->caption ?? '');
        $this->kcUrl = (string) ($konten->url ?? '');
        $this->kcTanggal = $konten->tanggal_posting?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->kcLikes = (int) $konten->likes;
        $this->kcComments = (int) $konten->comments;
        $this->kcShares = (int) $konten->shares;
        $this->kcViews = (int) $konten->views;
        $this->kcTopik = (string) ($konten->topik ?? 'pelayanan');
        $this->kcDapil = (string) ($konten->dapil_terkait ?? '');
        $this->kcRw = (string) ($konten->rw_terkait ?? '');
        $this->kcDesa = (string) ($konten->desa_terkait ?? '');
        $this->kcIsVideo = (bool) $konten->is_video_pelayanan;
        $this->activeTab = 'konten';
    }

    public function simpanKonten(): void
    {
        $data = $this->validate($this->kontenRules());

        $konten = KontenMedsos::query()->find($this->kontenEditId) ?? new KontenMedsos();
        $konten->fill([
            'anggota_dewan_id' => $data['kcDewanId'],
            'platform' => $data['kcPlatform'],
            'jenis_konten' => $data['kcJenis'],
            'caption' => $data['kcCaption'] !== '' ? $data['kcCaption'] : null,
            'url' => $data['kcUrl'] !== '' ? $data['kcUrl'] : null,
            'tanggal_posting' => $data['kcTanggal'],
            'likes' => (int) $data['kcLikes'],
            'comments' => (int) $data['kcComments'],
            'shares' => (int) $data['kcShares'],
            'views' => (int) $data['kcViews'],
            'topik' => $data['kcTopik'] !== '' ? $data['kcTopik'] : null,
            'dapil_terkait' => $data['kcDapil'] !== '' ? $data['kcDapil'] : null,
            'rw_terkait' => $data['kcRw'] !== '' ? $data['kcRw'] : null,
            'desa_terkait' => $data['kcDesa'] !== '' ? $data['kcDesa'] : null,
            'is_video_pelayanan' => (bool) $data['kcIsVideo'],
            'created_by' => $konten->exists ? $konten->created_by : auth()->id(),
        ]);
        $konten->save();

        $this->selectedDewanId = $konten->anggota_dewan_id;
        $this->syncPopularitas($konten->anggota_dewan_id);
        $this->closeDrawer();
        session()->flash('message', 'Log konten berhasil disimpan dan skor popularitas diperbarui.');
    }

    public function openMateriForm(): void
    {
        $this->closeDrawer();
        $this->resetMateriForm();
        $this->showMateriForm = true;
        $this->activeTab = 'materi';
    }

    public function simpanMateri(): void
    {
        $data = $this->validate($this->materiRules());
        $storedPath = $this->matFile->store('materi', 'public');
        $extension = strtolower((string) $this->matFile->getClientOriginalExtension());
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        MateriDigital::query()->create([
            'judul' => $data['matJudul'],
            'jenis' => $data['matJenis'],
            'deskripsi' => $data['matDeskripsi'] !== '' ? $data['matDeskripsi'] : null,
            'file_path' => $storedPath,
            'thumbnail' => in_array($extension, $imageExtensions, true) ? $storedPath : null,
            'topik' => $data['matTopik'] !== '' ? $data['matTopik'] : null,
            'status' => 'published',
            'created_by' => auth()->id(),
        ]);

        $this->closeDrawer();
        session()->flash('message', 'Materi digital berhasil di-upload.');
    }

    public function openDistribusiForm(string $materiId): void
    {
        $this->closeDrawer();
        $this->resetDistribusiForm();
        $this->showDistribusiForm = true;
        $this->distMateriId = $materiId;
        $this->activeTab = 'materi';
    }

    public function simpanDistribusi(): void
    {
        $data = $this->validate($this->distribusiRules());
        $materi = MateriDigital::query()->findOrFail($data['distMateriId']);

        DistribusiMateri::query()->create([
            'materi_digital_id' => $materi->id,
            'channel' => $data['distChannel'],
            'target_dapil' => $data['distDapil'] !== '' ? $data['distDapil'] : null,
            'target_rw_count' => (int) $data['distRwCount'],
            'terkirim' => (int) $data['distRwCount'],
            'terbaca' => 0,
            'tanggal_distribusi' => now()->toDateString(),
            'catatan' => $data['distCatatan'] !== '' ? $data['distCatatan'] : null,
            'created_by' => auth()->id(),
        ]);

        $materi->increment('distribusi_count');

        $this->closeDrawer();
        session()->flash('message', 'Distribusi materi berhasil dicatat.');
    }

    public function updateFollowers(string $dewanId): void
    {
        $dewan = AnggotaDewan::query()->findOrFail($dewanId);

        $dewan->update([
            'ig_followers' => max((int) $dewan->ig_followers, 0),
            'tt_followers' => max((int) $dewan->tt_followers, 0),
            'yt_subscribers' => max((int) $dewan->yt_subscribers, 0),
            'tw_followers' => max((int) $dewan->tw_followers, 0),
            'fb_followers' => max((int) $dewan->fb_followers, 0),
        ]);

        session()->flash('message', 'Follower tersimpan ulang dari angka manual terakhir yang ada di profil dewan.');
    }

    public function recalculatePopularitas(): void
    {
        AnggotaDewan::query()
            ->aktif()
            ->get()
            ->each(fn (AnggotaDewan $dewan) => $this->syncPopularitas($dewan->id));

        session()->flash('message', 'Skor popularitas seluruh dewan aktif berhasil dihitung ulang.');
    }

    public function closeDrawer(): void
    {
        $this->showDewanForm = false;
        $this->showKontenForm = false;
        $this->showMateriForm = false;
        $this->showDistribusiForm = false;

        $this->resetDewanForm();
        $this->resetKontenForm();
        $this->resetMateriForm();
        $this->resetDistribusiForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.sosial-media.index')
            ->layout('components.layouts.app.sidebar');
    }

    private function filteredDewanQuery(): Builder
    {
        return AnggotaDewan::query()
            ->when($this->selectedDewanId !== '', fn (Builder $query) => $query->whereKey($this->selectedDewanId))
            ->when($this->filterPlatform !== '', function (Builder $query): void {
                $platformColumn = match ($this->filterPlatform) {
                    'instagram' => 'instagram',
                    'tiktok' => 'tiktok',
                    'youtube' => 'youtube',
                    'twitter' => 'twitter',
                    'facebook' => 'facebook',
                    default => null,
                };

                if ($platformColumn !== null) {
                    $query->whereNotNull($platformColumn)->where($platformColumn, '!=', '');
                }
            });
    }

    private function filteredKontenBaseQuery(): Builder
    {
        $query = KontenMedsos::query()
            ->when($this->selectedDewanId !== '', fn (Builder $builder) => $builder->where('anggota_dewan_id', $this->selectedDewanId))
            ->when($this->filterPlatform !== '', fn (Builder $builder) => $builder->where('platform', $this->filterPlatform));

        return $this->applyPeriodeFilter($query, 'tanggal_posting');
    }

    private function filteredRwCoverageQuery(): Builder
    {
        $query = DataRw::query()
            ->select('dapil', 'desa', 'nomor_rw');

        if ($this->selectedDewanId !== '') {
            $dewan = AnggotaDewan::query()->find($this->selectedDewanId);

            if ($dewan !== null && $dewan->dapil) {
                $query->where('dapil', $dewan->dapil);
            }
        }

        return $query
            ->orderBy('dapil')
            ->orderBy('desa')
            ->orderBy('nomor_rw');
    }

    private function filteredDistribusiQuery(): Builder
    {
        return DistribusiMateri::query()
            ->when($this->selectedBulan !== '', fn (Builder $query) => $query->whereMonth('tanggal_distribusi', (int) $this->selectedBulan))
            ->when($this->selectedTahun !== '', fn (Builder $query) => $query->whereYear('tanggal_distribusi', (int) $this->selectedTahun));
    }

    private function applyPeriodeFilter(Builder $query, string $column): Builder
    {
        return $query
            ->when($this->selectedBulan !== '', fn (Builder $builder) => $builder->whereMonth($column, (int) $this->selectedBulan))
            ->when($this->selectedTahun !== '', fn (Builder $builder) => $builder->whereYear($column, (int) $this->selectedTahun));
    }

    private function syncPopularitas(string $dewanId): void
    {
        $dewan = AnggotaDewan::query()->find($dewanId);

        if ($dewan === null) {
            return;
        }

        $dewan->update([
            'skor_popularitas' => $dewan->hitungPopularitas(),
        ]);
    }

    private function dewanRules(): array
    {
        return [
            'dNama' => ['required', 'string', 'max:255'],
            'dJabatan' => ['required', 'string', 'max:255'],
            'dDapil' => ['nullable', 'string', 'max:255'],
            'dSuara2024' => ['nullable', 'integer', 'min:0'],
            'dStatusPetahana' => ['boolean'],
            'dJabatanFraksi' => ['nullable', 'string', 'max:255'],
            'dJabatanDprd' => ['nullable', 'string', 'max:255'],
            'dJabatanPartai' => ['nullable', 'string', 'max:255'],
            'dWilayahDapil' => ['nullable', 'string', 'max:255'],
            'dHp' => ['nullable', 'string', 'max:255'],
            'dInstagram' => ['nullable', 'string', 'max:255'],
            'dIgFollowers' => ['nullable', 'integer', 'min:0'],
            'dTiktok' => ['nullable', 'string', 'max:255'],
            'dTtFollowers' => ['nullable', 'integer', 'min:0'],
            'dYoutube' => ['nullable', 'string', 'max:255'],
            'dYtSubs' => ['nullable', 'integer', 'min:0'],
            'dTwitter' => ['nullable', 'string', 'max:255'],
            'dTwFollowers' => ['nullable', 'integer', 'min:0'],
            'dFacebook' => ['nullable', 'string', 'max:255'],
            'dFbFollowers' => ['nullable', 'integer', 'min:0'],
            'dTimNama' => ['nullable', 'string', 'max:255'],
            'dTimHp' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function kontenRules(): array
    {
        return [
            'kcDewanId' => ['required', 'exists:anggota_dewans,id'],
            'kcPlatform' => ['required', 'string', 'max:50'],
            'kcJenis' => ['required', 'string', 'max:50'],
            'kcCaption' => ['nullable', 'string'],
            'kcUrl' => ['nullable', 'url', 'max:255'],
            'kcTanggal' => ['required', 'date'],
            'kcLikes' => ['nullable', 'integer', 'min:0'],
            'kcComments' => ['nullable', 'integer', 'min:0'],
            'kcShares' => ['nullable', 'integer', 'min:0'],
            'kcViews' => ['nullable', 'integer', 'min:0'],
            'kcTopik' => ['nullable', 'string', 'max:50'],
            'kcDapil' => ['nullable', 'string', 'max:255'],
            'kcRw' => ['nullable', 'string', 'max:255'],
            'kcDesa' => ['nullable', 'string', 'max:255'],
            'kcIsVideo' => ['boolean'],
        ];
    }

    private function materiRules(): array
    {
        return [
            'matJudul' => ['required', 'string', 'max:255'],
            'matJenis' => ['required', 'string', 'max:50'],
            'matDeskripsi' => ['nullable', 'string'],
            'matFile' => ['required', 'file', 'max:10240'],
            'matTopik' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function distribusiRules(): array
    {
        return [
            'distMateriId' => ['required', 'exists:materi_digitals,id'],
            'distChannel' => ['required', 'string', 'max:50'],
            'distDapil' => ['nullable', 'string', 'max:255'],
            'distRwCount' => ['nullable', 'integer', 'min:0'],
            'distCatatan' => ['nullable', 'string'],
        ];
    }

    private function resetDewanForm(): void
    {
        $this->dewanEditId = null;
        $this->dNama = '';
        $this->dJabatan = '';
        $this->dDapil = '';
        $this->dSuara2024 = 0;
        $this->dStatusPetahana = false;
        $this->dJabatanFraksi = '';
        $this->dJabatanDprd = '';
        $this->dJabatanPartai = '';
        $this->dWilayahDapil = '';
        $this->dHp = '';
        $this->dInstagram = '';
        $this->dIgFollowers = 0;
        $this->dTiktok = '';
        $this->dTtFollowers = 0;
        $this->dYoutube = '';
        $this->dYtSubs = 0;
        $this->dTwitter = '';
        $this->dTwFollowers = 0;
        $this->dFacebook = '';
        $this->dFbFollowers = 0;
        $this->dTimNama = '';
        $this->dTimHp = '';
    }

    private function resetKontenForm(): void
    {
        $this->kontenEditId = null;
        $this->kcDewanId = '';
        $this->kcPlatform = 'instagram';
        $this->kcJenis = 'post';
        $this->kcCaption = '';
        $this->kcUrl = '';
        $this->kcTanggal = now()->format('Y-m-d');
        $this->kcLikes = 0;
        $this->kcComments = 0;
        $this->kcShares = 0;
        $this->kcViews = 0;
        $this->kcTopik = 'pelayanan';
        $this->kcDapil = '';
        $this->kcRw = '';
        $this->kcDesa = '';
        $this->kcIsVideo = false;
    }

    private function resetMateriForm(): void
    {
        $this->matJudul = '';
        $this->matJenis = 'poster';
        $this->matDeskripsi = '';
        $this->matFile = null;
        $this->matTopik = '';
    }

    private function resetDistribusiForm(): void
    {
        $this->distMateriId = '';
        $this->distChannel = 'wa_blast';
        $this->distDapil = '';
        $this->distRwCount = 0;
        $this->distCatatan = '';
    }
}
