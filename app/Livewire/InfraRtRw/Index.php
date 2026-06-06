<?php

declare(strict_types=1);

namespace App\Livewire\InfraRtRw;

use App\Models\ProfilRw;
use App\Models\TargetWilayah;
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

    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public int $selectedTahun = 2026;

    public string $search = '';

    public string $activeTab = 'korwe';

    public function updatedSelectedDapil(): void
    {
        // #region debug-point D:index-filter-dapil
        $this->reportDebug('D', 'Index@updatedSelectedDapil', '[DEBUG] Selected dapil updated', ['selectedDapil' => $this->selectedDapil]);
        // #endregion
        $this->selectedKecamatan = '';
        $this->resetPage();
    }

    public function updatedSelectedKecamatan(): void
    {
        // #region debug-point D:index-filter-kecamatan
        $this->reportDebug('D', 'Index@updatedSelectedKecamatan', '[DEBUG] Selected kecamatan updated', ['selectedKecamatan' => $this->selectedKecamatan]);
        // #endregion
        $this->resetPage();
    }

    public function updatedSelectedTahun(): void
    {
        // #region debug-point D:index-filter-tahun
        $this->reportDebug('D', 'Index@updatedSelectedTahun', '[DEBUG] Selected tahun updated', ['selectedTahun' => $this->selectedTahun]);
        // #endregion
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        // #region debug-point D:index-filter-search
        $this->reportDebug('D', 'Index@updatedSearch', '[DEBUG] Search updated', ['search' => $this->search]);
        // #endregion
        $this->resetPage();
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['korwe', 'korte'], true)) {
            $this->activeTab = $tab;
            // #region debug-point D:index-tab
            $this->reportDebug('D', 'Index@setActiveTab', '[DEBUG] Active tab updated', ['activeTab' => $this->activeTab]);
            // #endregion
        }
    }

    public function export(): StreamedResponse
    {
        $rows = $this->desaBaseQuery()
            ->withCount([
                'korwes as korwes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
                'kortes as kortes_terbentuk' => fn (Builder $query) => $query->where('status', 'terbentuk'),
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

        return response()->streamDownload(function () use ($rows, $tahun, $korweColumn, $korteColumn): void {
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

        $filteredTotals = $this->applyFilters(TargetWilayah::query())
            ->leftJoinSub($korweCounts, 'korwe_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'korwe_counts.target_wilayah_id'))
            ->leftJoinSub($korteCounts, 'korte_counts', fn ($join) => $join->on('target_wilayahs.id', '=', 'korte_counts.target_wilayah_id'))
            ->selectRaw('COUNT(*) as total_desa')
            ->selectRaw('COALESCE(SUM(target_wilayahs.jumlah_rw), 0) as total_rw')
            ->selectRaw('COALESCE(SUM(target_wilayahs.jumlah_rt), 0) as total_rt')
            ->selectRaw('COALESCE(SUM(target_wilayahs.suara_pks_2024), 0) as total_suara_pks_2024')
            ->selectRaw('COALESCE(SUM(target_wilayahs.target_suara_2029), 0) as total_target_suara_2029')
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as total_target_korwe', $this->targetKorweColumn()))
            ->selectRaw(sprintf('COALESCE(SUM(target_wilayahs.%s), 0) as total_target_korte', $this->targetKorteColumn()))
            ->selectRaw('COALESCE(SUM(korwe_counts.formed_korwe), 0) as total_formed_korwe')
            ->selectRaw('COALESCE(SUM(korte_counts.formed_korte), 0) as total_formed_korte')
            ->first();

        $totalRw = (int) ($filteredTotals->total_rw ?? 0);
        $totalRt = (int) ($filteredTotals->total_rt ?? 0);
        $targetKorwe = (int) ($filteredTotals->total_target_korwe ?? 0);
        $targetKorte = (int) ($filteredTotals->total_target_korte ?? 0);
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
            'formed_korwe' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'formed_korte' => (int) ($filteredTotals->total_formed_korte ?? 0),
        ]);
        // #endregion

        return [
            'total_desa' => (int) ($filteredTotals->total_desa ?? 0),
            'total_rw' => $totalRw,
            'total_rt' => $totalRt,
            'target_korwe' => $targetKorwe,
            'target_korte' => $targetKorte,
            'terbentuk_korwe' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'terbentuk_korte' => (int) ($filteredTotals->total_formed_korte ?? 0),
            'korwe_terbentuk' => (int) ($filteredTotals->total_formed_korwe ?? 0),
            'korte_terbentuk' => (int) ($filteredTotals->total_formed_korte ?? 0),
            'persen_korwe' => $totalRw > 0 ? min(100, round(($targetKorwe / $totalRw) * 100, 1)) : 0,
            'persen_korte' => $totalRt > 0 ? min(100, round(($targetKorte / $totalRt) * 100, 1)) : 0,
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
            ->first();

        $totalRw = (int) ($totals->total_rw ?? 0);
        $totalRt = (int) ($totals->total_rt ?? 0);
        $years = [2026, 2027, 2028, 2029];

        $data = [];

        foreach ($years as $year) {
            $korweTarget = (int) ($totals->{"korwe_{$year}"} ?? 0);
            $korteTarget = (int) ($totals->{"korte_{$year}"} ?? 0);

            $data["korwe_{$year}"] = $korweTarget;
            $data["korwe_pct_{$year}"] = $totalRw > 0 ? min(100, round(($korweTarget / $totalRw) * 100, 1)) : 0;
            $data["korte_{$year}"] = $korteTarget;
            $data["korte_pct_{$year}"] = $totalRt > 0 ? min(100, round(($korteTarget / $totalRt) * 100, 1)) : 0;
        }

        return $data;
    }

    #[Computed]
    public function dapilProgressData(): Collection
    {
        $targetColumn = $this->activeTab === 'korte' ? $this->targetKorteColumn() : $this->targetKorweColumn();
        $countsTable = $this->activeTab === 'korte' ? 'kortes' : 'korwes';
        $formedAlias = $this->activeTab === 'korte' ? 'formed_korte' : 'formed_korwe';

        $formedSubquery = DB::table($countsTable)
            ->selectRaw(sprintf('target_wilayah_id, COUNT(*) as %s', $formedAlias))
            ->where('status', 'terbentuk')
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
            ->layout('components.layouts.app-fullwidth', ['title' => 'Infra RT/RW']);
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
                $this->targetKorweColumn(),
                $this->targetKorteColumn(),
            ]);
    }

    private function filteredTargetQuery(): Builder
    {
        return $this->applyFilters(TargetWilayah::query());
    }

    private function applyFilters(Builder $query): Builder
    {
        return $query
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
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
}
