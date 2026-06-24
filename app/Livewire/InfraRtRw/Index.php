<?php

declare(strict_types=1);

namespace App\Livewire\InfraRtRw;

use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use App\Models\DataRw;
use App\Traits\WithWilayahScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;
    use WithWilayahScope;

    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public string $filterDesa = '';

    public int $selectedTahun = 2026;

    public string $search = '';

    public string $activeTab = 'korwe';

    public ?string $selectedVillageId = null;

    public string $selectedDesa = '';

    // Form KORWE / KORTE
    public bool $showForm = false;
    public string $formNomorRw = '';
    public string $formParentRw = '';
    public string $formNamaKoordinator = '';
    public string $formNoHp = '';
    public string $formStatus = 'belum';
    public string $formCatatan = '';
    public ?string $formTanggal = null;
    public ?string $editId = null;

    // Profil RW Drawer
    public bool $showProfilDrawer = false;
    public ?string $profilRwId = null;
    public string $rwStatusFilter = '';
    public array $profilData = [];
    public array $autoFillData = [];

    public function mount(): void
    {
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil') {
            $this->selectedDapil = (string) ($scope['locked_dapil'] ?? '');
            $this->selectedKecamatan = (string) ($scope['kecamatan'] ?? '');
            if (!empty($scope['desa'])) {
                $this->selectedDesa = $scope['desa'];
            }
        }
    }
    public bool $isEditingProfil = false;
    public int $profilCompletion = 0;
    public string $upa_rw_wa = '';
    public string $rki_wa = '';
    public string $senam_wa = '';
    public string $relawan_milenial_wa = '';
    public string $kompetitor_wa = '';
    public string $tim_sukses_wa = '';
    public string $faktorSelect = '';
    public string $faktorDesc = '';
    public string $strategiSelect = '';
    public string $strategiDesc = '';

    // Form Penggalang
    public bool $showPenggalangForm = false;
    public string $pgNama = '';
    public string $pgHp = '';
    public string $pgWa = '';
    public string $pgRw = '';
    public string $pgRt = '';
    public string $pgSumber = 'warga';
    public int $pgTarget = 10;
    public ?string $pgEditId = null;

    public function updatedSelectedDapil(): void
    {
        // #region debug-point D:index-filter-dapil
        $this->reportDebug('D', 'Index@updatedSelectedDapil', '[DEBUG] Selected dapil updated', ['selectedDapil' => $this->selectedDapil]);
        // #endregion
        $this->selectedKecamatan = '';
        $this->filterDesa = '';
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedSelectedKecamatan(): void
    {
        // #region debug-point D:index-filter-kecamatan
        $this->reportDebug('D', 'Index@updatedSelectedKecamatan', '[DEBUG] Selected kecamatan updated', ['selectedKecamatan' => $this->selectedKecamatan]);
        // #endregion
        $this->filterDesa = '';
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedFilterDesa(): void
    {
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedSelectedTahun(): void
    {
        // #region debug-point D:index-filter-tahun
        $this->reportDebug('D', 'Index@updatedSelectedTahun', '[DEBUG] Selected tahun updated', ['selectedTahun' => $this->selectedTahun]);
        // #endregion
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        // #region debug-point D:index-filter-search
        $this->reportDebug('D', 'Index@updatedSearch', '[DEBUG] Search updated', ['search' => $this->search]);
        // #endregion
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
        $this->resetPage();
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['korwe', 'korte', 'penggalang'], true)) {
            $this->activeTab = $tab;
            // #region debug-point D:index-tab
            $this->reportDebug('D', 'Index@setActiveTab', '[DEBUG] Active tab updated', ['activeTab' => $this->activeTab]);
            // #endregion
        }
    }

    public function resetFilters(): void
    {
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') !== 'dapil') {
            $this->selectedDapil = '';
            $this->selectedKecamatan = '';
        }
        $this->filterDesa = '';
        $this->selectedTahun = 2026;
        $this->search = '';
        $this->activeTab = 'korwe';
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
    }

    public function export(): StreamedResponse
    {
        $rows = $this->desaBaseQuery()
            ->withCount([
                'korwes as korwes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
                'kortes as kortes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
                'penggalangSuaras as penggalangs_terbentuk' => fn (Builder $query) => $query->where('status', 'aktif'),
                'dataRws as rw_prioritas_count' => fn (Builder $query) => $query->where('prioritas_urutan', '<=', 2),
                'dataRws as rw_total' => fn (Builder $query) => $query,
                'profilRws as profil_terisi_count' => fn (Builder $query) => $query->where('completion_percent', '>', 0),
                'profilRws as profil_lengkap_count' => fn (Builder $query) => $query->where('is_complete', true),
            ])
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $tahun = $this->selectedTahun;
        $korweColumn = $this->targetKorweColumn();
        $korteColumn = $this->targetKorteColumn();
        $penggalangColumn = $this->targetPenggalangColumn();

        return response()->streamDownload(function () use ($rows, $tahun, $korweColumn, $korteColumn, $penggalangColumn): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'Dapil',
                'Kecamatan',
                'Desa',
                'RW',
                'RT',
                "KORWE Terbentuk {$tahun}",
                "KORWE Target {$tahun}",
                "KORTE Terbentuk {$tahun}",
                "KORTE Target {$tahun}",
                "Penggalang Terbentuk",
                "Penggalang Target {$tahun}",
                'Suara PKS 2024',
                'Target Suara 2029',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->dapil,
                    $row->kecamatan,
                    $row->desa,
                    $row->jumlah_rw,
                    $row->jumlah_rt,
                    $row->korwes_terbentuk,
                    $row->{$korweColumn},
                    $row->kortes_terbentuk,
                    $row->{$korteColumn},
                    $row->penggalangs_terbentuk,
                    $row->{$penggalangColumn},
                    $row->suara_pks_2024,
                    $row->target_suara_2029,
                ]);
            }

            fclose($handle);
        }, sprintf('infra-rtrw-%s.csv', now()->format('Ymd-His')));
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

    public function getDapilOptions(): Collection
    {
        return $this->dapilOptions();
    }

    #[Computed]
    public function kecamatanOptions(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getKecamatanOptions(): Collection
    {
        return $this->kecamatanOptions();
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    public function getDesaOptions(): Collection
    {
        return $this->desaOptions();
    }

    #[Computed]
    public function summaryData(): array
    {
        $korweCounts = DB::table('korwes')
            ->selectRaw('target_wilayah_id, COUNT(*) as formed_korwe')
            ->where('status', 'terbentuk')
            ->groupBy('target_wilayah_id');

        $korteCounts = DB::table('kortes')
            ->selectRaw('target_wilayah_id, COUNT(*) as formed_korte')
            ->where('status', 'terbentuk')
            ->groupBy('target_wilayah_id');

        $penggalangCounts = DB::table('penggalang_suaras')
            ->selectRaw('target_wilayah_id, COUNT(*) as formed_penggalang')
            ->where('status', 'aktif')
            ->groupBy('target_wilayah_id');

        $filteredTotals = $this->applyFilters(TargetWilayah::query())
            ->leftJoinSub($korweCounts, 'korwe_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'korwe_counts.target_wilayah_id'))
            ->leftJoinSub($korteCounts, 'korte_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'korte_counts.target_wilayah_id'))
            ->leftJoinSub($penggalangCounts, 'penggalang_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'penggalang_counts.target_wilayah_id'))
            ->selectRaw('COUNT(*) as total_desa')
            ->selectRaw('COALESCE(SUM(target_wilayahs.jumlah_rw), 0) as total_rw')
            ->selectRaw('COALESCE(SUM(target_wilayahs.jumlah_rt), 0) as total_rt')
            ->selectRaw('COALESCE(SUM(target_wilayahs.suara_pks_2024), 0) as total_suara_pks_2024')
            ->selectRaw('COALESCE(SUM(target_wilayahs.target_suara_2029), 0) as total_target_suara_2029')
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as total_target_korwe', $this->targetKorweColumn()))
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as total_target_korte', $this->targetKorteColumn()))
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as total_target_penggalang', $this->targetPenggalangColumn()))
            ->selectRaw('COALESCE(SUM(korwe_counts.formed_korwe), 0) as total_formed_korwe')
            ->selectRaw('COALESCE(SUM(korte_counts.formed_korte), 0) as total_formed_korte')
            ->selectRaw('COALESCE(SUM(penggalang_counts.formed_penggalang), 0) as total_formed_penggalang')
            ->first();

        $totalRw = (int) ($filteredTotals->total_rw ?? 0);
        $totalRt = (int) ($filteredTotals->total_rt ?? 0);
        $targetKorwe = (int) ($filteredTotals->total_target_korwe ?? 0);
        $targetKorte = (int) ($filteredTotals->total_target_korte ?? 0);
        $targetPenggalang = (int) ($filteredTotals->total_target_penggalang ?? 0);
        $formedPenggalang = (int) ($filteredTotals->total_formed_penggalang ?? 0);
        $suara2024 = (int) ($filteredTotals->total_suara_pks_2024 ?? 0);
        $target2029 = (int) ($filteredTotals->total_target_suara_2029 ?? 0);
        $growth = $suara2024 > 0 ? round((($target2029 - $suara2024) / $suara2024) * 100, 1) : 0;
        $targetIds = (clone $this->filteredTargetQuery())->pluck('id');
        $profilBase = ProfilRw::query()->whereIn('target_wilayah_id', $targetIds);
        $dataRwBase = DB::table('data_rws')->whereIn('target_wilayah_id', $targetIds);
        $profilTerisi = (clone $profilBase)->where('completion_percent', '>', 0)->count();
        $profilLengkap = (clone $profilBase)->where('is_complete', true)->count();
        $totalRwAll = (clone $dataRwBase)->count();

        // #region debug-point B:index-summary
        $this->reportDebug('B', 'Index@summaryData', '[DEBUG] Summary data calculated', [
            'selectedDapil' => $this->selectedDapil,
            'selectedKecamatan' => $this->selectedKecamatan,
            'selectedTahun' => $this->selectedTahun,
            'search' => $this->search,
            'total_desa' => (int) ($filteredTotals->total_desa ?? 0),
            'total_rw' => $totalRw,
            'total_rt' => $totalRt,
            'target_korwe' => $targetKorwe,
            'target_korte' => $targetKorte,
            'target_penggalang' => $targetPenggalang,
            'formed_korwe' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'formed_korte' => (int) ($filteredTotals->total_formed_korte ?? 0),
            'formed_penggalang' => $formedPenggalang,
        ]);
        // #endregion

        return [
            'total_desa' => (int) ($filteredTotals->total_desa ?? 0),
            'total_rw' => $totalRw,
            'total_rt' => $totalRt,
            'target_korwe' => $targetKorwe,
            'target_korte' => $targetKorte,
            'target_penggalang' => $targetPenggalang,
            'terbentuk_korwe' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'terbentuk_korte' => (int) ($filteredTotals->total_formed_korte ?? 0),
            'terbentuk_penggalang' => $formedPenggalang,
            'korwe_terbentuk' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'korte_terbentuk' => (int) ($filteredTotals->total_formed_korte ?? 0),
            'penggalang_terbentuk' => $formedPenggalang,
            'persen_korwe' => $totalRw > 0 ? min(100, round(($targetKorwe / $totalRw) * 100, 1)) : 0,
            'persen_korte' => $totalRt > 0 ? min(100, round(($targetKorte / $totalRt) * 100, 1)) : 0,
            'persen_penggalang' => $targetPenggalang > 0 ? min(100, round(($formedPenggalang / $targetPenggalang) * 100, 1)) : 0,
            'total_suara_pks_2024' => $suara2024,
            'target_suara_2029' => $target2029,
            'suara_2024' => $suara2024,
            'target_suara' => $target2029,
            'growth_percent' => $growth,
            'growth_percent_abs' => abs($growth),
            'growth_direction' => $growth >= 0 ? 'naik' : 'turun',
            'profil_terisi' => $profilTerisi,
            'profil_lengkap' => $profilLengkap,
            'total_rw_all' => $totalRwAll,
        ];
    }

    #[Computed]
    public function milestoneData(): array
    {
        $totals = (clone $this->filteredTargetQuery())
            ->selectRaw('COALESCE(SUM(jumlah_rw), 0) as total_rw')
            ->selectRaw('COALESCE(SUM(jumlah_rt), 0) as total_rt')
            ->selectRaw('COALESCE(SUM(target_korwe_2026), 0) as korwe_2026')
            ->selectRaw('COALESCE(SUM(target_korwe_2027), 0) as korwe_2027')
            ->selectRaw('COALESCE(SUM(target_korwe_2028), 0) as korwe_2028')
            ->selectRaw('COALESCE(SUM(target_korwe_2029), 0) as korwe_2029')
            ->selectRaw('COALESCE(SUM(target_korte_2026), 0) as korte_2026')
            ->selectRaw('COALESCE(SUM(target_korte_2027), 0) as korte_2027')
            ->selectRaw('COALESCE(SUM(target_korte_2028), 0) as korte_2028')
            ->selectRaw('COALESCE(SUM(target_korte_2029), 0) as korte_2029')
            ->selectRaw('COALESCE(SUM(target_penggalang_2026), 0) as penggalang_2026')
            ->selectRaw('COALESCE(SUM(target_penggalang_2027), 0) as penggalang_2027')
            ->selectRaw('COALESCE(SUM(target_penggalang_2028), 0) as penggalang_2028')
            ->selectRaw('COALESCE(SUM(target_penggalang_2029), 0) as penggalang_2029')
            ->first();

        $totalRw = (int) ($totals->total_rw ?? 0);
        $totalRt = (int) ($totals->total_rt ?? 0);
        $totalPenggalangFinal = (int) ($totals->penggalang_2029 ?? 0);
        $years = [2026, 2027, 2028, 2029];

        $data = [];

        foreach ($years as $year) {
            $korweTarget = (int) ($totals->{"korwe_{$year}"} ?? 0);
            $korteTarget = (int) ($totals->{"korte_{$year}"} ?? 0);
            $penggalangTarget = (int) ($totals->{"penggalang_{$year}"} ?? 0);

            $data["korwe_{$year}"] = $korweTarget;
            $data["korwe_pct_{$year}"] = $totalRw > 0 ? min(100, round(($korweTarget / $totalRw) * 100, 1)) : 0;
            $data["korte_{$year}"] = $korteTarget;
            $data["korte_pct_{$year}"] = $totalRt > 0 ? min(100, round(($korteTarget / $totalRt) * 100, 1)) : 0;
            $data["penggalang_{$year}"] = $penggalangTarget;
            $data["penggalang_pct_{$year}"] = $totalPenggalangFinal > 0 ? min(100, round(($penggalangTarget / $totalPenggalangFinal) * 100, 1)) : 0;
        }

        return $data;
    }

    #[Computed]
    public function dapilProgressData(): Collection
    {
        if ($this->activeTab === 'penggalang') {
            $targetColumn = $this->targetPenggalangColumn();
            $countsTable = 'penggalang_suaras';
            $statusField = 'status';
            $statusVal = 'aktif';
            $formedAlias = 'formed_penggalang';
        } elseif ($this->activeTab === 'korte') {
            $targetColumn = $this->targetKorteColumn();
            $countsTable = 'kortes';
            $statusField = 'status';
            $statusVal = 'terbentuk';
            $formedAlias = 'formed_korte';
        } else {
            $targetColumn = $this->targetKorweColumn();
            $countsTable = 'korwes';
            $statusField = 'status';
            $statusVal = 'terbentuk';
            $formedAlias = 'formed_korwe';
        }

        $formedSubquery = DB::table($countsTable)
            ->selectRaw(sprintf('target_wilayah_id, COUNT(*) as %s', $formedAlias))
            ->where($statusField, $statusVal)
            ->groupBy('target_wilayah_id');
        $profilSubquery = DB::table('profil_rws')
            ->selectRaw('target_wilayah_id, COUNT(*) as profil_terisi_total')
            ->where('completion_percent', '>', 0)
            ->groupBy('target_wilayah_id');
        $dataRwSubquery = DB::table('data_rws')
            ->selectRaw('target_wilayah_id, COUNT(*) as total_rw_count')
            ->groupBy('target_wilayah_id');

        return $this->applyFilters(TargetWilayah::query())
            ->leftJoinSub($formedSubquery, 'formed_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'formed_counts.target_wilayah_id'))
            ->leftJoinSub($profilSubquery, 'profil_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'profil_counts.target_wilayah_id'))
            ->leftJoinSub($dataRwSubquery, 'rw_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'rw_counts.target_wilayah_id'))
            ->select('target_wilayahs.dapil')
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as target_total', $targetColumn))
            ->selectRaw(sprintf('COALESCE(SUM(formed_counts.%s), 0) as formed_total', $formedAlias))
            ->selectRaw('COALESCE(SUM(profil_counts.profil_terisi_total), 0) as profil_terisi_total')
            ->selectRaw('COALESCE(SUM(rw_counts.total_rw_count), 0) as rw_total')
            ->groupBy('target_wilayahs.dapil')
            ->get()
            ->map(function (TargetWilayah $item) {
                $formed = (int) $item->formed_total;
                $target = (int) $item->target_total;
                $profilTerisi = (int) $item->profil_terisi_total;
                $totalRw = (int) $item->rw_total;

                return [
                    'dapil' => $item->dapil,
                    'terbentuk' => $formed,
                    'target' => $target,
                    'persen' => $target > 0 ? min(100, round(($formed / $target) * 100, 1)) : 0,
                    'percent' => $target > 0 ? min(100, round(($formed / $target) * 100, 1)) : 0,
                    'profil_terisi' => $profilTerisi,
                    'total_rw' => $totalRw,
                    'profil_persen' => $totalRw > 0 ? min(100, round(($profilTerisi / $totalRw) * 100, 1)) : 0,
                ];
            })
            ->sortBy('dapil')
            ->values()
            ->tap(function (Collection $rows): void {
                // #region debug-point B:index-dapil-progress
                $this->reportDebug('B', 'Index@dapilProgressData', '[DEBUG] Dapil progress calculated', [
                    'activeTab' => $this->activeTab,
                    'selectedTahun' => $this->selectedTahun,
                    'rows' => $rows->all(),
                ]);
                // #endregion
            });
    }

    #[Computed]
    public function desaData(): LengthAwarePaginator
    {
        $paginator = $this->desaBaseQuery()
            ->withCount([
                'korwes as korwes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
                'kortes as kortes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
                'penggalangSuaras as penggalangs_terbentuk' => fn (Builder $query) => $query->where('status', 'aktif'),
                'dataRws as rw_prioritas_count' => fn (Builder $query) => $query->where('prioritas_urutan', '<=', 2),
                'dataRws as rw_total' => fn (Builder $query) => $query,
                'profilRws as profil_terisi_count' => fn (Builder $query) => $query->where('completion_percent', '>', 0),
                'profilRws as profil_lengkap_count' => fn (Builder $query) => $query->where('is_complete', true),
            ])
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->paginate(25);

        // #region debug-point E:index-desa-data
        $this->reportDebug('E', 'Index@desaData', '[DEBUG] Desa data paginated', [
            'count' => $paginator->count(),
            'total' => $paginator->total(),
            'currentPage' => $paginator->currentPage(),
            'selectedDapil' => $this->selectedDapil,
            'selectedKecamatan' => $this->selectedKecamatan,
            'search' => $this->search,
        ]);
        // #endregion

        return $paginator;
    }

    public function render()
    {
        // #region debug-point A:index-render
        $this->reportDebug('A', 'Index@render', '[DEBUG] Infra RT/RW index render', [
            'selectedDapil' => $this->selectedDapil,
            'selectedKecamatan' => $this->selectedKecamatan,
            'selectedTahun' => $this->selectedTahun,
            'search' => $this->search,
            'activeTab' => $this->activeTab,
        ]);
        // #endregion

        return view('livewire.infra-rtrw.index')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Infrastruktur']);
    }

    // #region debug-point A:index-debug-helper
    private function reportDebug(string $hypothesisId, string $location, string $msg, array $data = []): void
    {
        $envPath = base_path('.dbg/infra-rtrw-fixes.env');
        $url = 'http://127.0.0.1:7777/event';
        $sessionId = 'infra-rtrw-fixes';

        if (is_file($envPath)) {
            $env = (string) file_get_contents($envPath);
            if (preg_match('/^DEBUG_SERVER_URL=(.+)$/m', $env, $matches) === 1) {
                $url = trim($matches[1]);
            }
            if (preg_match('/^DEBUG_SESSION_ID=(.+)$/m', $env, $matches) === 1) {
                $sessionId = trim($matches[1]);
            }
        }

        $payload = json_encode([
            'sessionId' => $sessionId,
            'runId' => 'post-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'msg' => $msg,
            'data' => $data,
            'ts' => (int) round(microtime(true) * 1000),
        ]);

        if ($payload === false) {
            return;
        }

        @file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 1,
            ],
        ]));
    }
    // #endregion

    private function desaBaseQuery(): Builder
    {
        return $this->filteredTargetQuery()
            ->select([
                'id',
                'dapil',
                'kecamatan',
                'desa',
                'jumlah_rw',
                'jumlah_rt',
                'suara_pks_2024',
                'target_suara_2029',
                'target_penggalang',
                $this->targetKorweColumn(),
                $this->targetKorteColumn(),
                $this->targetPenggalangColumn(),
            ]);
    }

    private function filteredTargetQuery(): Builder
    {
        return $this->applyFilters(TargetWilayah::query());
    }

    private function applyFilters(Builder $query): Builder
    {
        return $this->applyUserScope($query)
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->filterDesa !== '', fn (Builder $query) => $query->where('desa', $this->filterDesa))
            ->when($this->search !== '', function (Builder $query): void {
                $search = trim($this->search);
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('desa', 'like', '%' . $search . '%')
                        ->orWhere('kecamatan', 'like', '%' . $search . '%')
                        ->orWhere('dapil', 'like', '%' . $search . '%');
                });
            });
    }

    private function targetKorweColumn(): string
    {
        return 'target_korwe_' . $this->selectedTahun;
    }

    private function targetKorteColumn(): string
    {
        return 'target_korte_' . $this->selectedTahun;
    }

    private function targetPenggalangColumn(): string
    {
        return 'target_penggalang_' . $this->selectedTahun;
    }

    public function selectVillage(string $id): void
    {
        $this->selectedVillageId = $this->selectedVillageId === $id ? null : $id;
        if ($this->selectedVillageId) {
            $w = TargetWilayah::find($this->selectedVillageId);
            $this->selectedDesa = $w ? $w->desa : '';
        } else {
            $this->selectedDesa = '';
        }
    }

    public function closeVillageDetail(): void
    {
        $this->selectedVillageId = null;
        $this->selectedDesa = '';
    }

    #[Computed]
    public function mapImage(): string
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

    #[Computed]
    public function mapMarkers(): array
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

        if ($this->activeTab === 'penggalang') {
            $countsTable = 'penggalang_suaras';
            $statusField = 'status';
            $statusVal = 'aktif';
            $targetColumn = $this->targetPenggalangColumn();
            $labelSuffix = 'PENGGALANG';
        } else {
            $countsTable = $this->activeTab === 'korte' ? 'kortes' : 'korwes';
            $statusField = 'status';
            $statusVal = 'terbentuk';
            $targetColumn = $this->activeTab === 'korte' ? $this->targetKorteColumn() : $this->targetKorweColumn();
            $labelSuffix = $this->activeTab === 'korte' ? 'KORTE' : 'KORWE';
        }

        $actualCounts = DB::table($countsTable)
            ->where($statusField, $statusVal)
            ->select('target_wilayah_id')
            ->selectRaw('COUNT(*) as total_formed')
            ->groupBy('target_wilayah_id')
            ->pluck('total_formed', 'target_wilayah_id');

        $wilayahs = TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->get();

        $maxFormed = max(1, $actualCounts->max());

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                $count = $actualCounts[$w->id] ?? 0;
                $target = $w->{$targetColumn} ?? 0;

                if ($target > 0) {
                    if ($count >= $target) {
                        $color = '#22c55e'; // Green
                    } elseif ($count > 0) {
                        $color = '#eab308'; // Yellow
                    } else {
                        $color = '#ef4444'; // Red
                    }
                } else {
                    $color = '#22c55e'; // Green if target is 0
                }

                $size = 12 + round(($count / $maxFormed) * 16);

                $markers[] = [
                    'id' => $w->id,
                    'key' => $w->id,
                    'label' => "{$w->desa} · {$count}/{$target} {$labelSuffix}",
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

    #[Computed]
    public function allWilayahs(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil', 'kecamatan', 'desa')
            ->orderBy('desa')
            ->get();
    }

    #[Computed]
    public function selectedVillageDetail(): ?array
    {
        if (!$this->selectedVillageId) {
            return null;
        }

        $w = TargetWilayah::find($this->selectedVillageId);
        if (!$w) {
            return null;
        }

        $korweTarget = (int) $w->{$this->targetKorweColumn()};
        $korteTarget = (int) $w->{$this->targetKorteColumn()};
        $penggalangTarget = (int) $w->target_penggalang;
        $korweFormed = $w->korwes()->where('status', 'terbentuk')->count();
        $korteFormed = $w->kortes()->where('status', 'terbentuk')->count();
        $penggalangFormed = $w->penggalangSuaras()->where('status', 'aktif')->count();

        $totalRw = $w->jumlah_rw;
        $profilTerisi = $w->profilRws()->where('completion_percent', '>', 0)->count();
        $profilLengkap = $w->profilRws()->where('is_complete', true)->count();
        $profilPct = $totalRw > 0 ? round(($profilTerisi / $totalRw) * 100) : 0;

        return [
            'id' => $w->id,
            'dapil' => $w->dapil,
            'kecamatan' => $w->kecamatan,
            'desa' => $w->desa,
            'jumlah_rw' => $w->jumlah_rw,
            'jumlah_rt' => $w->jumlah_rt,
            'jumlah_tps' => $w->jumlah_tps,
            'jumlah_dpt' => $w->jumlah_dpt,
            'suara_pks_2024' => $w->suara_pks_2024,
            'target_suara_2029' => $w->target_suara_2029,
            'korwe_target' => $korweTarget,
            'korte_target' => $korteTarget,
            'penggalang_target' => $penggalangTarget,
            'korwe_formed' => $korweFormed,
            'korte_formed' => $korteFormed,
            'penggalang_formed' => $penggalangFormed,
            'profil_terisi' => $profilTerisi,
            'profil_lengkap' => $profilLengkap,
            'profil_pct' => $profilPct,
        ];
    }

    // --- MAPPING & FORM IMPLEMENTATIONS FOR HOMEPAGE SPA WORKFLOW ---

    public function getRwListProperty(): Collection
    {
        if (!$this->selectedVillageId) {
            return collect();
        }

        $korweMap = Korwe::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->get()
            ->keyBy(fn (Korwe $item): string => $this->normalizeNumber($item->nomor_rw));

        return DataRw::query()
            ->byDesa($this->selectedVillageId)
            ->orderByPrioritas()
            ->get()
            ->map(function (DataRw $rw) use ($korweMap): DataRw {
                $rw->setRelation('korwe', $korweMap->get($this->normalizeNumber($rw->nomor_rw)));

                return $rw;
            });
    }

    public function getProfilRwMapProperty(): Collection
    {
        if (!$this->selectedVillageId) {
            return collect();
        }
        return ProfilRw::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->get()
            ->keyBy(fn (ProfilRw $item): string => $this->normalizeNumber($item->nomor_rw));
    }

    public function getFilteredRwListProperty(): Collection
    {
        if ($this->rwStatusFilter === '') {
            return $this->rwList;
        }

        return $this->rwList
            ->filter(fn (DataRw $rw): bool => $rw->status_wilayah === $this->rwStatusFilter)
            ->values();
    }

    public function getRwStatusFiltersProperty(): array
    {
        $filters = [[
            'key' => '',
            'label' => 'Semua',
            'count' => $this->rwList->count(),
            'active' => $this->rwStatusFilter === '',
            'bg' => '#f5f5f5',
            'text' => '#525252',
            'border' => '#d4d4d8',
        ]];

        foreach (TargetWilayah::STATUS_CONFIG as $key => $config) {
            $filters[] = [
                'key' => $key,
                'label' => $config['label'],
                'count' => $this->rwList->where('status_wilayah', $key)->count(),
                'active' => $this->rwStatusFilter === $key,
                'bg' => $config['bg'],
                'text' => $config['text'],
                'border' => $config['text'],
            ];
        }

        return $filters;
    }

    public function getKorteGroupsProperty(): array
    {
        if (!$this->selectedVillageId) {
            return [];
        }

        $village = TargetWilayah::find($this->selectedVillageId);
        if (!$village) {
            return [];
        }

        $kortes = Korte::query()->where('target_wilayah_id', $this->selectedVillageId)->get();
        $existingByRw = $kortes->groupBy(fn (Korte $item) => $this->normalizeNumber($item->nomor_rw));
        
        $totalRw = max(1, (int) $village->jumlah_rw);
        $totalRt = max(0, (int) $village->jumlah_rt);
        $base = $totalRt > 0 ? intdiv($totalRt, $totalRw) : 0;
        $remainder = $totalRt > 0 ? $totalRt % $totalRw : 0;
        $groups = [];

        for ($rwIndex = 1; $rwIndex <= $totalRw; $rwIndex++) {
            $nomorRw = $this->normalizeNumber((string) $rwIndex);
            /** @var Collection<int, Korte> $existingRows */
            $existingRows = $existingByRw->get($nomorRw, collect())->keyBy(fn (Korte $item) => $this->normalizeNumber($item->nomor_rt));
            $targetCount = $base + ($rwIndex <= $remainder ? 1 : 0);
            $targetCount = max($targetCount, $existingRows->count(), $existingRows->keys()->map(fn (string $value) => (int) $value)->max() ?? 0);
            $rows = [];

            if ($targetCount === 0 && $existingRows->isEmpty()) {
                $rows[] = $this->buildRowState('001', 'belum', null, null, null, null, null);
            } else {
                for ($rtIndex = 1; $rtIndex <= max(1, $targetCount); $rtIndex++) {
                    $nomorRt = $this->normalizeNumber((string) $rtIndex);
                    $record = $existingRows->get($nomorRt);

                    $rows[] = $this->buildRowState(
                        $nomorRt,
                        $record?->status ?? 'belum',
                        $record?->nama_koordinator,
                        $record?->no_hp,
                        $record?->catatan,
                        $record?->tanggal_terbentuk?->format('d M Y'),
                        $record?->id
                    );
                }
            }

            $groups[] = [
                'rw' => $nomorRw,
                'rows' => $rows,
            ];
        }

        return $groups;
    }

    public function getPenggalangListProperty(): Collection
    {
        if (!$this->selectedVillageId) {
            return collect();
        }

        return PenggalangSuara::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->orderBy('nomor_rw')
            ->orderBy('nama')
            ->get();
    }

    private function normalizeNumber(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        $number = $digits !== '' ? (int) $digits : 0;

        return str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    private function buildRowState(
        string $nomor,
        string $status,
        ?string $nama,
        ?string $noHp,
        ?string $catatan,
        ?string $tanggal,
        ?string $id
    ): array {
        return [
            'nomor' => $nomor,
            'status' => strtolower($status),
            'nama' => $nama,
            'no_hp' => $noHp,
            'catatan' => $catatan,
            'tanggal' => $tanggal,
            'id' => $id,
        ];
    }

    private function rules(): array
    {
        $rules = [
            'formNomorRw' => ['required', 'string', 'max:10'],
            'formNamaKoordinator' => ['nullable', 'string', 'max:255'],
            'formNoHp' => ['nullable', 'string', 'max:50'],
            'formStatus' => ['required', 'in:belum,proses,terbentuk'],
            'formCatatan' => ['nullable', 'string'],
            'formTanggal' => ['nullable', 'date'],
        ];

        if ($this->activeTab === 'korte') {
            $rules['formParentRw'] = ['required', 'string', 'max:10'];
        }

        if ($this->formStatus === 'terbentuk') {
            $rules['formTanggal'] = ['required', 'date'];
        }

        return $rules;
    }

    private function validationAttributes(): array
    {
        return [
            'formNomorRw' => $this->activeTab === 'korwe' ? 'nomor RW' : 'nomor RT',
            'formParentRw' => 'RW induk',
            'formNamaKoordinator' => 'nama koordinator',
            'formNoHp' => 'no HP',
            'formStatus' => 'status',
            'formCatatan' => 'catatan',
            'formTanggal' => 'tanggal terbentuk',
        ];
    }

    public function openCreateForm(?string $nomor = null, ?string $parentRw = null): void
    {
        if (!$this->selectedVillageId) {
            return;
        }
        $this->resetForm();
        $this->showForm = true;
        $this->formNomorRw = $nomor ?? '';
        $this->formParentRw = $parentRw ?? '';
    }

    public function assignKorwe(string $nomorRw): void
    {
        $this->activeTab = 'korwe';
        $this->resetForm();
        $this->formNomorRw = $this->normalizeNumber($nomorRw);
        $this->formStatus = 'proses';
        $this->showForm = true;
    }

    public function editKorwe(string $id): void
    {
        $this->activeTab = 'korwe';
        $this->openEditForm($id);
    }

    public function openEditForm(string $id): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $record = $this->activeTab === 'korwe'
            ? Korwe::query()->where('target_wilayah_id', $this->selectedVillageId)->find($id)
            : Korte::query()->where('target_wilayah_id', $this->selectedVillageId)->find($id);

        if (!$record) {
            return;
        }

        $this->resetForm();
        $this->showForm = true;
        $this->editId = $record->id;
        $this->formNamaKoordinator = (string) ($record->nama_koordinator ?? '');
        $this->formNoHp = (string) ($record->no_hp ?? '');
        $this->formStatus = (string) $record->status;
        $this->formCatatan = (string) ($record->catatan ?? '');
        $this->formTanggal = $record->tanggal_terbentuk?->format('Y-m-d');

        if ($this->activeTab === 'korwe') {
            $this->formNomorRw = (string) $record->nomor_rw;
            $this->formParentRw = '';
        } else {
            $this->formNomorRw = (string) $record->nomor_rt;
            $this->formParentRw = (string) $record->nomor_rw;
        }
    }

    public function closeForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function simpan(): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $validated = $this->validate($this->rules(), [], $this->validationAttributes());
        $normalizedNomor = $this->normalizeNumber($validated['formNomorRw']);
        $normalizedParentRw = $this->activeTab === 'korte'
            ? $this->normalizeNumber($validated['formParentRw'])
            : null;

        $payload = [
            'nama_koordinator' => $validated['formNamaKoordinator'] !== '' ? $validated['formNamaKoordinator'] : null,
            'no_hp' => $validated['formNoHp'] !== '' ? $validated['formNoHp'] : null,
            'status' => strtolower($validated['formStatus']),
            'catatan' => $validated['formCatatan'] !== '' ? $validated['formCatatan'] : null,
            'tanggal_terbentuk' => strtolower($validated['formStatus']) === 'terbentuk' ? $validated['formTanggal'] : null,
            'created_by' => auth()->id(),
        ];

        if ($this->activeTab === 'korwe') {
            $duplicate = Korwe::query()
                ->where('target_wilayah_id', $this->selectedVillageId)
                ->where('nomor_rw', $normalizedNomor)
                ->when($this->editId, fn ($query) => $query->where('id', '!=', $this->editId))
                ->exists();

            if ($duplicate) {
                $this->addError('formNomorRw', 'Nomor RW sudah digunakan.');
                return;
            }

            if ($this->editId) {
                Korwe::query()
                    ->where('target_wilayah_id', $this->selectedVillageId)
                    ->findOrFail($this->editId)
                    ->update([
                        'nomor_rw' => $normalizedNomor,
                        ...$payload,
                    ]);
            } else {
                Korwe::query()->create([
                    'target_wilayah_id' => $this->selectedVillageId,
                    'nomor_rw' => $normalizedNomor,
                    ...$payload,
                ]);
            }

            session()->flash('success', 'Data KORWE berhasil disimpan.');
        } else {
            $duplicate = Korte::query()
                ->where('target_wilayah_id', $this->selectedVillageId)
                ->where('nomor_rw', $normalizedParentRw)
                ->where('nomor_rt', $normalizedNomor)
                ->when($this->editId, fn ($query) => $query->where('id', '!=', $this->editId))
                ->exists();

            if ($duplicate) {
                $this->addError('formNomorRw', 'Nomor RT pada RW tersebut sudah digunakan.');
                return;
            }

            if ($this->editId) {
                Korte::query()
                    ->where('target_wilayah_id', $this->selectedVillageId)
                    ->findOrFail($this->editId)
                    ->update([
                        'nomor_rw' => $normalizedParentRw,
                        'nomor_rt' => $normalizedNomor,
                        ...$payload,
                    ]);
            } else {
                Korte::query()->create([
                    'target_wilayah_id' => $this->selectedVillageId,
                    'nomor_rw' => $normalizedParentRw,
                    'nomor_rt' => $normalizedNomor,
                    ...$payload,
                ]);
            }

            session()->flash('success', 'Data KORTE berhasil disimpan.');
        }

        $this->closeForm();
    }

    public function hapus(string $id): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $model = $this->activeTab === 'korwe' ? Korwe::class : Korte::class;

        $record = $model::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->find($id);

        if (!$record) {
            return;
        }

        $record->delete();

        session()->flash('success', $this->activeTab === 'korwe'
            ? 'Data KORWE berhasil dihapus.'
            : 'Data KORTE berhasil dihapus.');

        $this->closeForm();
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->formNomorRw = '';
        $this->formParentRw = '';
        $this->formNamaKoordinator = '';
        $this->formNoHp = '';
        $this->formStatus = 'belum';
        $this->formCatatan = '';
        $this->formTanggal = null;
        $this->resetErrorBag();
    }

    public function openPenggalangForm(): void
    {
        if (!$this->selectedVillageId) {
            return;
        }
        $this->resetPenggalangForm();
        $this->showPenggalangForm = true;
    }

    public function editPenggalang(string $id): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $record = PenggalangSuara::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->findOrFail($id);

        $this->resetPenggalangForm();
        $this->showPenggalangForm = true;
        $this->pgEditId = $record->id;
        $this->pgNama = (string) $record->nama;
        $this->pgRw = (string) $record->nomor_rw;
        $this->pgHp = (string) ($record->no_hp ?? '');
        $this->pgWa = (string) ($record->no_wa ?? '');
        $this->pgRt = (string) ($record->rt ?? '');
        $this->pgSumber = (string) $record->sumber;
        $this->pgTarget = (int) $record->target_jangkauan;
    }

    public function simpanPenggalang(): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $village = TargetWilayah::find($this->selectedVillageId);
        if (!$village) {
            return;
        }

        $validated = $this->validate([
            'pgNama' => ['required', 'string', 'max:255'],
            'pgRw' => ['required', 'string', 'max:10'],
            'pgHp' => ['nullable', 'string', 'max:50'],
            'pgWa' => ['nullable', 'string', 'max:50'],
            'pgRt' => ['nullable', 'string', 'max:10'],
            'pgSumber' => ['required', 'string', 'max:50'],
            'pgTarget' => ['required', 'integer', 'min:0'],
        ], [], [
            'pgNama' => 'nama penggalang',
            'pgRw' => 'nomor RW',
        ]);

        $data = [
            'target_wilayah_id' => $this->selectedVillageId,
            'dapil' => $village->dapil,
            'kecamatan' => $village->kecamatan,
            'desa' => $village->desa,
            'nomor_rw' => $this->normalizeNumber($validated['pgRw']),
            'nama' => $validated['pgNama'],
            'no_hp' => $validated['pgHp'] !== '' ? $validated['pgHp'] : null,
            'no_wa' => $validated['pgWa'] !== '' ? $validated['pgWa'] : null,
            'rt' => $validated['pgRt'] !== '' ? $this->normalizeNumber($validated['pgRt']) : null,
            'sumber' => $validated['pgSumber'],
            'target_jangkauan' => (int) $validated['pgTarget'],
            'created_by' => auth()->id(),
        ];

        if ($this->pgEditId !== null) {
            PenggalangSuara::query()
                ->where('target_wilayah_id', $this->selectedVillageId)
                ->findOrFail($this->pgEditId)
                ->update($data);
        } else {
            PenggalangSuara::query()->create($data);
        }

        $this->resetPenggalangForm();
        session()->flash('success', 'Penggalang suara berhasil disimpan.');
    }

    public function hapusPenggalang(string $id): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $record = PenggalangSuara::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->find($id);

        if (!$record) {
            return;
        }

        $record->delete();

        session()->flash('success', 'Penggalang suara berhasil dihapus.');
        $this->resetPenggalangForm();
    }

    public function resetPenggalangForm(): void
    {
        $this->resetErrorBag();
        $this->showPenggalangForm = false;
        $this->pgEditId = null;
        $this->pgNama = '';
        $this->pgHp = '';
        $this->pgWa = '';
        $this->pgRw = '';
        $this->pgRt = '';
        $this->pgSumber = 'warga';
        $this->pgTarget = 10;
    }

    public function openProfil(string $nomorRw): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $village = TargetWilayah::find($this->selectedVillageId);
        if (!$village) {
            return;
        }

        $this->profilRwId = $this->normalizeNumber($nomorRw);
        $this->showProfilDrawer = true;
        $this->isEditingProfil = false;

        $profil = ProfilRw::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->where('nomor_rw', $this->profilRwId)
            ->first();

        if ($profil instanceof ProfilRw) {
            $this->profilCompletion = (int) $profil->completion_percent;
            $this->profilData = $profil->only([
                'tipologi',
                'ekonomi_dominan',
                'profil_warga',
                'suara_pks_2019',
                'faktor_penyebab',
                'anggota_pks',
                'jumlah_kta',
                'upa_rw_status',
                'upa_rw_nama',
                'rki_status',
                'rki_nama',
                'senam_status',
                'senam_nama',
                'relawan_milenial_status',
                'relawan_milenial_nama',
                'caleg_terpilih_ada',
                'caleg_terpilih_nama',
                'afiliasi_rw_rt',
                'afiliasi_posyandu_dkm',
                'partai_dominan',
                'afiliasi_ketua_rw',
                'afiliasi_mayoritas_rt',
                'afiliasi_tomas',
                'afiliasi_toga',
                'afiliasi_pemuda',
                'kompetitor_status',
                'kompetitor_detail',
                'tim_sukses_status',
                'tim_sukses_detail',
                'strategi',
                'penanggung_jawab',
                'keterangan_lain',
            ]);

            // Parse UPA
            $upaParsed = $this->parseNameAndWa($this->profilData['upa_rw_nama'] ?? '');
            $this->profilData['upa_rw_nama'] = $upaParsed['name'];
            $this->upa_rw_wa = $upaParsed['wa'];

            // Parse RKI
            $rkiParsed = $this->parseNameAndWa($this->profilData['rki_nama'] ?? '');
            $this->profilData['rki_nama'] = $rkiParsed['name'];
            $this->rki_wa = $rkiParsed['wa'];

            // Parse Senam
            $senamParsed = $this->parseNameAndWa($this->profilData['senam_nama'] ?? '');
            $this->profilData['senam_nama'] = $senamParsed['name'];
            $this->senam_wa = $senamParsed['wa'];

            // Parse Relawan Milenial
            $relawanParsed = $this->parseNameAndWa($this->profilData['relawan_milenial_nama'] ?? '');
            $this->profilData['relawan_milenial_nama'] = $relawanParsed['name'];
            $this->relawan_milenial_wa = $relawanParsed['wa'];

            // Parse Kompetitor
            $kompetitorParsed = $this->parseNameAndWa($this->profilData['kompetitor_detail'] ?? '');
            $this->profilData['kompetitor_detail'] = $kompetitorParsed['name'];
            $this->kompetitor_wa = $kompetitorParsed['wa'];

            // Parse Tim Sukses
            $tsParsed = $this->parseNameAndWa($this->profilData['tim_sukses_detail'] ?? '');
            $this->profilData['tim_sukses_detail'] = $tsParsed['name'];
            $this->tim_sukses_wa = $tsParsed['wa'];

            // Parse Faktor Penyebab
            $faktorRaw = $this->profilData['faktor_penyebab'] ?? '';
            $this->faktorSelect = '';
            $this->faktorDesc = $faktorRaw;
            if (str_starts_with($faktorRaw, '[')) {
                $closeBracketIdx = strpos($faktorRaw, ']');
                if ($closeBracketIdx !== false) {
                    $this->faktorSelect = substr($faktorRaw, 1, $closeBracketIdx - 1);
                    $this->faktorDesc = trim(substr($faktorRaw, $closeBracketIdx + 1));
                }
            } else {
                $presets = [
                    'Kekuatan Caleg Lokal',
                    'Ketokohan Tokoh Agama/Masyarakat',
                    'Program Kerja & Bantuan Nyata',
                    'Pragmatisme Politik Uang',
                    'Keaktifan Kader & Relawan',
                    'Kurangnya Sosialisasi/Kehadiran',
                    'Dominasi Partai Lain'
                ];
                if (in_array(trim($faktorRaw), $presets, true)) {
                    $this->faktorSelect = trim($faktorRaw);
                    $this->faktorDesc = '';
                }
            }

            // Parse Strategi
            $strategiRaw = $this->profilData['strategi'] ?? '';
            $this->strategiSelect = '';
            $this->strategiDesc = $strategiRaw;
            if (str_starts_with($strategiRaw, '[')) {
                $closeBracketIdx = strpos($strategiRaw, ']');
                if ($closeBracketIdx !== false) {
                    $this->strategiSelect = substr($strategiRaw, 1, $closeBracketIdx - 1);
                    $this->strategiDesc = trim(substr($strategiRaw, $closeBracketIdx + 1));
                }
            } else {
                $presets = [
                    'Sapa Warga & Door to Door',
                    'Penyediaan Layanan Sosial',
                    'Penguatan Tokoh Kunci',
                    'Event / Kegiatan Komunitas',
                    'Kampanye Digital & Media Sosial',
                    'Penguatan Saksi & Pengawalan Suara'
                ];
                if (in_array(trim($strategiRaw), $presets, true)) {
                    $this->strategiSelect = trim($strategiRaw);
                    $this->strategiDesc = '';
                }
            }

        } else {
            $this->profilCompletion = 0;
            $this->profilData = $this->emptyProfilData();
            $this->upa_rw_wa = '';
            $this->rki_wa = '';
            $this->senam_wa = '';
            $this->relawan_milenial_wa = '';
            $this->kompetitor_wa = '';
            $this->tim_sukses_wa = '';
            $this->faktorSelect = '';
            $this->faktorDesc = '';
            $this->strategiSelect = '';
            $this->strategiDesc = '';
        }

        $this->loadAutoFillData($this->profilRwId);
    }

    public function simpanProfil(): void
    {
        if (!$this->selectedVillageId || $this->profilRwId === null) {
            return;
        }

        $village = TargetWilayah::find($this->selectedVillageId);
        if (!$village) {
            return;
        }

        // Serialize fields
        $this->profilData['upa_rw_nama'] = $this->serializeNameAndWa($this->profilData['upa_rw_nama'] ?? '', $this->upa_rw_wa);
        $this->profilData['rki_nama'] = $this->serializeNameAndWa($this->profilData['rki_nama'] ?? '', $this->rki_wa);
        $this->profilData['senam_nama'] = $this->serializeNameAndWa($this->profilData['senam_nama'] ?? '', $this->senam_wa);
        $this->profilData['relawan_milenial_nama'] = $this->serializeNameAndWa($this->profilData['relawan_milenial_nama'] ?? '', $this->relawan_milenial_wa);
        $this->profilData['kompetitor_detail'] = $this->serializeNameAndWa($this->profilData['kompetitor_detail'] ?? '', $this->kompetitor_wa);
        $this->profilData['tim_sukses_detail'] = $this->serializeNameAndWa($this->profilData['tim_sukses_detail'] ?? '', $this->tim_sukses_wa);

        $this->profilData['faktor_penyebab'] = $this->faktorSelect ? '[' . $this->faktorSelect . '] ' . trim($this->faktorDesc) : trim($this->faktorDesc);
        $this->profilData['strategi'] = $this->strategiSelect ? '[' . $this->strategiSelect . '] ' . trim($this->strategiDesc) : trim($this->strategiDesc);

        $payload = array_merge($this->emptyProfilData(), $this->profilData, [
            'target_wilayah_id' => $this->selectedVillageId,
            'nomor_rw' => $this->profilRwId,
            'dapil' => $village->dapil,
            'kecamatan' => $village->kecamatan,
            'desa' => $village->desa,
            'filled_by' => auth()->id(),
            'filled_at' => now(),
            'suara_pks_2019' => (int) ($this->profilData['suara_pks_2019'] ?? 0),
            'jumlah_kta' => (int) ($this->profilData['jumlah_kta'] ?? 0),
            'caleg_terpilih_ada' => filter_var($this->profilData['caleg_terpilih_ada'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);

        $profil = ProfilRw::query()->updateOrCreate(
            [
                'target_wilayah_id' => $this->selectedVillageId,
                'nomor_rw' => $this->profilRwId,
            ],
            $payload
        );

        $completion = $profil->calculateCompletion();

        $profil->update([
            'completion_percent' => $completion,
            'is_complete' => $completion >= 80,
        ]);

        $this->showProfilDrawer = false;
        session()->flash('success', 'Profil RW ' . $this->profilRwId . ' berhasil disimpan.');
        $this->closeProfilDrawer();
    }

    public function closeProfilDrawer(): void
    {
        $this->showProfilDrawer = false;
        $this->profilRwId = null;
        $this->profilData = [];
        $this->autoFillData = [];
    }

    private function emptyProfilData(): array
    {
        return [
            'tipologi' => '',
            'ekonomi_dominan' => '',
            'profil_warga' => '',
            'suara_pks_2019' => 0,
            'faktor_penyebab' => '',
            'anggota_pks' => '',
            'jumlah_kta' => 0,
            'upa_rw_status' => 'belum',
            'upa_rw_nama' => '',
            'rki_status' => 'belum',
            'rki_nama' => '',
            'senam_status' => 'belum',
            'senam_nama' => '',
            'relawan_milenial_status' => 'belum',
            'relawan_milenial_nama' => '',
            'caleg_terpilih_ada' => 0,
            'caleg_terpilih_nama' => '',
            'afiliasi_rw_rt' => '',
            'afiliasi_posyandu_dkm' => '',
            'partai_dominan' => '',
            'afiliasi_ketua_rw' => '',
            'afiliasi_mayoritas_rt' => '',
            'afiliasi_tomas' => '',
            'afiliasi_toga' => '',
            'afiliasi_pemuda' => '',
            'kompetitor_status' => 'tidak_tahu',
            'kompetitor_detail' => '',
            'tim_sukses_status' => 'tidak_tahu',
            'tim_sukses_detail' => '',
            'strategi' => '',
            'penanggung_jawab' => '',
            'keterangan_lain' => '',
        ];
    }

    private function loadAutoFillData(string $nomorRw): void
    {
        if (!$this->selectedVillageId) {
            return;
        }

        $tw = TargetWilayah::find($this->selectedVillageId);
        if (!$tw) {
            return;
        }

        $dataRw = DataRw::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->where('nomor_rw', $nomorRw)
            ->first();

        $korwe = Korwe::query()
            ->where('target_wilayah_id', $this->selectedVillageId)
            ->where('nomor_rw', $nomorRw)
            ->first();

        $period = \App\Models\PemiluPeriod::query()
            ->forJenis('dprd')
            ->ordered()
            ->first();

        $summary = $period 
            ? \App\Models\PemiluDesaSummary::query()
                ->where('pemilu_period_id', $period->id)
                ->where('kecamatan', $tw->kecamatan)
                ->where('desa', $tw->desa)
                ->first()
            : null;
        
        $rwRows = $summary?->rw_rows ?? [];
        $targetRwRow = null;
        foreach ($rwRows as $row) {
            if (isset($row['rw']) && $this->normalizeNumber($row['rw']) === $this->normalizeNumber($nomorRw)) {
                $targetRwRow = $row;
                break;
            }
        }

        $partaiPemenang = '-';
        $top3Partai = '-';
        $calegPksTertinggi = '-';
        if ($targetRwRow) {
            $parties = $targetRwRow['party_rows'] ?? [];
            if (!empty($parties)) {
                $names = [];
                foreach (array_slice($parties, 0, 3) as $p) {
                    $names[] = $p['party_name'] ?? $p['partai'] ?? '';
                }
                $top3Partai = implode(', ', array_filter($names));
                $partaiPemenang = $parties[0]['party_name'] ?? $parties[0]['partai'] ?? '-';
            }
            $calegPksTertinggi = $targetRwRow['top_candidate']['name'] ?? '-';
        }

        $this->autoFillData = [
            'jumlah_rt' => $dataRw?->jumlah_rt ?? 0,
            'dpt' => $dataRw?->dpt ?? ($targetRwRow['total_dpt'] ?? 0),
            'dpt_laki' => $dataRw?->dpt_laki ?? ($targetRwRow['male'] ?? 0),
            'dpt_perempuan' => $dataRw?->dpt_perempuan ?? ($targetRwRow['female'] ?? 0),
            'gen_z' => $dataRw?->gen_z ?? ($targetRwRow['gen_z'] ?? 0),
            'millennial' => $dataRw?->millennial ?? ($targetRwRow['millennial'] ?? 0),
            'gen_x' => $dataRw?->gen_x ?? ($targetRwRow['gen_x'] ?? 0),
            'boomer' => $dataRw?->boomer ?? ($targetRwRow['boomer'] ?? 0),
            'estimasi_pks' => $dataRw?->estimasi_pks ?? ($targetRwRow['pks_votes'] ?? 0),
            'estimasi_share' => $dataRw?->estimasi_share ?? ($targetRwRow['share'] ?? 0),
            'estimasi_ranking' => $dataRw?->estimasi_ranking ?? ($targetRwRow['rank'] ?? 0),
            'partai_pemenang' => $partaiPemenang,
            'top_3_partai' => $top3Partai,
            'caleg_pks_tertinggi' => $calegPksTertinggi,
            'target_suara' => (int) round((float) $tw->target_avg_per_rw),
            'korwe_nama' => $korwe?->nama_koordinator,
            'korwe_status' => $korwe?->status ?? 'belum',
            'status_wilayah' => $dataRw?->status_wilayah ?? 'ZONA BERAT',
            'tps_count' => $targetRwRow['tps_count'] ?? 0,
            'party_rows' => $targetRwRow['party_rows'] ?? [],
        ];
    }

    private function parseNameAndWa(?string $value): array
    {
        $val = trim($value ?? '');
        $marker = ' | WA: ';
        $idx = strpos($val, $marker);
        if ($idx !== false) {
            return [
                'name' => trim(substr($val, 0, $idx)),
                'wa' => trim(substr($val, $idx + strlen($marker))),
            ];
        }
        return [
            'name' => $val,
            'wa' => '',
        ];
    }

    private function serializeNameAndWa(string $name, string $wa): string
    {
        $name = trim($name);
        $wa = trim($wa);
        if ($name === '') return '';
        if ($wa === '') return $name;
        return $name . ' | WA: ' . $wa;
    }
}
