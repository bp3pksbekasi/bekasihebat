<?php

namespace App\Livewire\BukuIndukRw;

use App\Models\DataRw;
use App\Models\TargetWilayah;
use App\Traits\WithWilayahScope;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;
    use WithWilayahScope;

    #[Url]
    public string $selectedDapil = '';
    
    #[Url]
    public string $selectedKecamatan = '';
    
    #[Url]
    public string $selectedDesa = '';
    
    #[Url]
    public string $search = '';

    #[Url]
    public string $selectedStatus = '';

    #[Url]
    public int $selectedTahun = 2026;

    public function mount()
    {
        // Restore from session
        $this->selectedDapil = session('birw_dapil', '');
        $this->selectedKecamatan = session('birw_kecamatan', '');
        $this->selectedDesa = session('birw_desa', '');
        $this->selectedStatus = session('birw_status', '');
        $this->search = session('birw_search', '');
        $this->selectedTahun = (int) session('birw_tahun', date('Y') >= 2026 ? 2026 : 2026);

        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil') {
            $this->selectedDapil = (string) ($scope['locked_dapil'] ?? '');
            
            if (!session()->has('birw_kecamatan')) {
                $this->selectedKecamatan = (string) ($scope['kecamatan'] ?? '');
            }
            if (!empty($scope['desa']) && !session()->has('birw_desa')) {
                $this->selectedDesa = $scope['desa'];
            }
        }
    }

    public function updatedSelectedDapil()
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->resetPage();
        session(['birw_dapil' => $this->selectedDapil, 'birw_kecamatan' => '', 'birw_desa' => '']);
    }

    public function updatedSelectedKecamatan()
    {
        $this->selectedDesa = '';
        $this->resetPage();
        session(['birw_kecamatan' => $this->selectedKecamatan, 'birw_desa' => '']);
    }

    public function updatedSelectedDesa()
    {
        $this->resetPage();
        session(['birw_desa' => $this->selectedDesa]);
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
        session(['birw_status' => $this->selectedStatus]);
    }

    public function updatedSelectedTahun()
    {
        $this->resetPage();
        session(['birw_tahun' => $this->selectedTahun]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
        session(['birw_search' => $this->search]);
    }

    public function render()
    {
        $query = DataRw::query()
            ->with(['targetWilayah'])
            ->select('data_rws.*')
            ->addSelect([
                'korwe_count' => \App\Models\Korwe::selectRaw('count(*)')
                    ->whereColumn('target_wilayah_id', 'data_rws.target_wilayah_id')
                    ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)'),
                'korte_count' => \App\Models\Korte::selectRaw('count(*)')
                    ->whereColumn('target_wilayah_id', 'data_rws.target_wilayah_id')
                    ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)'),
                'penggalang_count' => \App\Models\PenggalangSuara::selectRaw('count(*)')
                    ->whereColumn('target_wilayah_id', 'data_rws.target_wilayah_id')
                    ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)'),
                'target_penggalang' => \App\Models\TargetWilayah::select('target_penggalang')
                    ->whereColumn('id', 'data_rws.target_wilayah_id')
                    ->limit(1),
            ]);

        // Apply Scope
        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil' && !empty($scope['locked_dapil'])) {
            $query->whereHas('targetWilayah', function (Builder $q) use ($scope) {
                $q->where('dapil', $scope['locked_dapil']);
                if (!empty($scope['kecamatan'])) {
                    $q->where('kecamatan', $scope['kecamatan']);
                }
                if (!empty($scope['desa'])) {
                    $q->where('desa', $scope['desa']);
                }
            });
        }

        // Apply Filters
        if ($this->selectedDapil !== '') {
            $query->whereHas('targetWilayah', fn(Builder $q) => $q->where('dapil', $this->selectedDapil));
        }
        if ($this->selectedKecamatan !== '') {
            $query->whereHas('targetWilayah', fn(Builder $q) => $q->where('kecamatan', $this->selectedKecamatan));
        }
        if ($this->selectedDesa !== '') {
            $query->whereHas('targetWilayah', fn(Builder $q) => $q->where('desa', $this->selectedDesa));
        }
        if ($this->selectedStatus !== '') {
            $query->where('data_rws.status_wilayah', $this->selectedStatus);
        }
        if ($this->search !== '') {
            $query->where(function(Builder $q) {
                $q->whereHas('targetWilayah', function(Builder $q2) {
                    $q2->where('desa', 'like', '%' . $this->search . '%')
                      ->orWhere('kecamatan', 'like', '%' . $this->search . '%');
                })->orWhere('nomor_rw', 'like', '%' . $this->search . '%');
            });
        }

        $rws = $query->join('target_wilayahs', 'data_rws.target_wilayah_id', '=', 'target_wilayahs.id')
            ->orderBy('target_wilayahs.dapil')
            ->orderBy('target_wilayahs.kecamatan')
            ->orderBy('target_wilayahs.desa')
            ->orderBy('data_rws.nomor_rw')
            ->paginate(20);

        // Calculate summary based on current filters (unpaginated)
        $summaryQuery = clone $query;
        $summaryQuery->selectRaw('COUNT(DISTINCT target_wilayahs.id) as total_desa')
            ->selectRaw('COUNT(data_rws.id) as total_rw')
            ->selectRaw('SUM(data_rws.jumlah_rt) as total_rt')
            ->selectRaw("COALESCE(SUM(target_wilayahs.target_korwe_{$this->selectedTahun}), 0) as total_target_korwe")
            ->selectRaw("COALESCE(SUM(target_wilayahs.target_korte_{$this->selectedTahun}), 0) as total_target_korte")
            ->selectRaw("COALESCE(SUM(target_wilayahs.target_penggalang_{$this->selectedTahun}), 0) as total_target_penggalang");
        
        $summaryData = $summaryQuery->first();

        // Calculate achieved
        $achievedQuery = clone $query;
        $achievedQuery->selectRaw('
            (SELECT COUNT(*) FROM korwes k WHERE k.target_wilayah_id = data_rws.target_wilayah_id AND TRIM(LEADING "0" FROM k.nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)) as total_korwe
        ')->selectRaw('
            (SELECT COUNT(*) FROM kortes k WHERE k.target_wilayah_id = data_rws.target_wilayah_id AND TRIM(LEADING "0" FROM k.nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)) as total_korte
        ')->selectRaw('
            (SELECT COUNT(*) FROM penggalang_suaras p WHERE p.target_wilayah_id = data_rws.target_wilayah_id AND TRIM(LEADING "0" FROM p.nomor_rw) = TRIM(LEADING "0" FROM data_rws.nomor_rw)) as total_penggalang
        ');
        $achievedTotals = $achievedQuery->get();
        $totalKorwe = $achievedTotals->sum('total_korwe');
        $totalKorte = $achievedTotals->sum('total_korte');
        $totalPenggalang = $achievedTotals->sum('total_penggalang');

        // Profil completion
        $profilRwsIds = $achievedTotals->pluck('target_wilayah_id')->unique();
        $profilCompleted = \App\Models\ProfilRw::whereIn('target_wilayah_id', $profilRwsIds)
            ->where('is_complete', true)
            ->count();
        $profilTerisi = \App\Models\ProfilRw::whereIn('target_wilayah_id', $profilRwsIds)
            ->count();

        $summary = [
            'total_desa' => $summaryData->total_desa ?? 0,
            'total_rw' => $summaryData->total_rw ?? 0,
            'total_rt' => $summaryData->total_rt ?? 0,
            
            'target_korwe' => $summaryData->total_target_korwe ?? 0,
            'tercapai_korwe' => $totalKorwe,
            
            'target_korte' => $summaryData->total_target_korte ?? 0,
            'tercapai_korte' => $totalKorte,
            
            'target_penggalang' => $summaryData->total_target_penggalang ?? 0,
            'tercapai_penggalang' => $totalPenggalang,
            
            'profil_terisi' => $profilTerisi,
            'profil_lengkap' => $profilCompleted,
        ];

        $profilRws = \App\Models\ProfilRw::whereIn('target_wilayah_id', $rws->pluck('target_wilayah_id'))
            ->get()
            ->keyBy(function($item) {
                return $item->target_wilayah_id . '_' . ltrim((string) $item->nomor_rw, '0');
            });

        return view('livewire.buku-induk-rw.index', [
            'rws' => $rws,
            'profilRws' => $profilRws,
            'dapils' => TargetWilayah::select('dapil')->distinct()->orderBy('dapil')->pluck('dapil'),
            'kecamatans' => TargetWilayah::when($this->selectedDapil, fn($q) => $q->where('dapil', $this->selectedDapil))->select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan'),
            'desas' => TargetWilayah::when($this->selectedKecamatan, fn($q) => $q->where('kecamatan', $this->selectedKecamatan))->select('desa')->distinct()->orderBy('desa')->pluck('desa'),
            'statuses' => TargetWilayah::STATUS_CONFIG,
            'summary' => $summary,
        ])->layout('components.layouts.app', ['title' => 'Peta Kekuatan RW']);
    }
}
