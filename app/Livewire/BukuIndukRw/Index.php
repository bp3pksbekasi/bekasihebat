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



        // Fetch all filtered RWs for summary (unpaginated)
        $allFilteredRws = (clone $query)->get();
        $uniqueWilayahs = $allFilteredRws->pluck('targetWilayah')->unique('id');

        // Calculate targets from unique target_wilayahs to avoid duplication
        $targetKorwe = $uniqueWilayahs->sum("target_korwe_{$this->selectedTahun}");
        $targetKorte = $uniqueWilayahs->sum("target_korte_{$this->selectedTahun}");
        $targetPenggalang = $uniqueWilayahs->sum("target_penggalang_{$this->selectedTahun}");

        // Calculate achieved by summing the counts already selected in the main query
        $tercapaiKorwe = $allFilteredRws->sum('korwe_count');
        $tercapaiKorte = $allFilteredRws->sum('korte_count');
        $tercapaiPenggalang = $allFilteredRws->sum('penggalang_count');

        // Profil completion
        $profilRwsIds = $allFilteredRws->pluck('target_wilayah_id')->unique();
        $profilCompleted = \App\Models\ProfilRw::whereIn('target_wilayah_id', $profilRwsIds)
            ->where('is_complete', true)
            ->count();
        $profilTerisi = \App\Models\ProfilRw::whereIn('target_wilayah_id', $profilRwsIds)
            ->count();

        $summary = [
            'total_desa' => $uniqueWilayahs->count(),
            'total_rw' => $allFilteredRws->count(),
            'total_rt' => $allFilteredRws->sum('jumlah_rt'),
            
            'target_korwe' => $targetKorwe,
            'tercapai_korwe' => $tercapaiKorwe,
            
            'target_korte' => $targetKorte,
            'tercapai_korte' => $tercapaiKorte,
            
            'target_penggalang' => $targetPenggalang,
            'tercapai_penggalang' => $tercapaiPenggalang,
            
            'profil_terisi' => $profilTerisi,
            'profil_lengkap' => $profilCompleted,
        ];

        $rws = $query->join('target_wilayahs', 'data_rws.target_wilayah_id', '=', 'target_wilayahs.id')
            ->orderBy('target_wilayahs.dapil')
            ->orderBy('target_wilayahs.kecamatan')
            ->orderBy('target_wilayahs.desa')
            ->orderBy('data_rws.nomor_rw')
            ->paginate(20);

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
