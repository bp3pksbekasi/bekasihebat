<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterStatus = '';

    public string $filterJenis = '';

    public string $filterDapil = '';

    public string $filterKecamatan = '';

    public string $filterDesa = '';

    public string $filterLevel = '';

    public string $filterBidang = '';

    public string $search = '';

    public bool $showDeleteConfirm = false;

    public ?string $deleteId = null;

    public string $viewMode = 'table';

    protected $queryString = [
        'filterStatus' => ['as' => 'status', 'except' => ''],
        'filterJenis' => ['as' => 'jenis', 'except' => ''],
        'filterDapil' => ['as' => 'dapil', 'except' => ''],
        'filterKecamatan' => ['as' => 'kecamatan', 'except' => ''],
        'filterDesa' => ['as' => 'desa', 'except' => ''],
        'filterLevel' => ['as' => 'level', 'except' => ''],
        'filterBidang' => ['as' => 'bidang', 'except' => ''],
        'search' => ['except' => ''],
        'viewMode' => ['as' => 'view', 'except' => 'table'],
    ];

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDapil(): void
    {
        $this->filterKecamatan = '';
        $this->filterDesa = '';
        $this->resetPage();
    }

    public function updatedFilterKecamatan(): void
    {
        $this->filterDesa = '';
        $this->resetPage();
    }

    public function updatedFilterDesa(): void
    {
        $this->resetPage();
    }

    public function updatedFilterLevel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterBidang(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status = ''): void
    {
        $this->filterStatus = $this->filterStatus === $status ? '' : $status;
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['table', 'cards'], true)) {
            $this->viewMode = $mode;
        }
    }

    public function clearFilters(): void
    {
        $this->filterStatus = '';
        $this->filterJenis = '';
        $this->filterDapil = '';
        $this->filterKecamatan = '';
        $this->filterDesa = '';
        $this->filterLevel = '';
        $this->filterBidang = '';
        $this->search = '';
        $this->resetPage();
    }

    public function confirmDelete(string $eventUuid): void
    {
        $this->deleteId = $eventUuid;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->showDeleteConfirm = false;
    }

    public function deleteEvent(): void
    {
        if ($this->deleteId === null) {
            return;
        }

        Event::query()->where('uuid', $this->deleteId)->delete();

        $this->cancelDelete();
        session()->flash('message', 'Event berhasil dihapus.');
    }

    public function togglePublic(string $eventUuid): void
    {
        $event = Event::query()->where('uuid', $eventUuid)->firstOrFail();

        if ($event->status !== Event::STATUS_DISETUJUI) {
            session()->flash('message', 'Event hanya bisa dipublikasikan setelah disetujui.');

            return;
        }

        $event->update(['is_public' => ! $event->is_public]);
        session()->flash('message', 'Visibilitas event berhasil diperbarui.');
    }

    #[Computed]
    public function summary(): array
    {
        $base = $this->filteredQuery();

        return [
            'draft' => (clone $base)->where('status', Event::STATUS_DRAFT)->count(),
            'menunggu' => (clone $base)->where('status', Event::STATUS_MENUNGGU)->count(),
            'disetujui' => (clone $base)->where('status', Event::STATUS_DISETUJUI)->count(),
            'berlangsung' => (clone $base)->where('status', Event::STATUS_BERLANGSUNG)->count(),
            'selesai' => (clone $base)->where('status', Event::STATUS_SELESAI)->count(),
        ];
    }

    #[Computed]
    public function events(): LengthAwarePaginator
    {
        return $this->filteredQuery()
            ->with(['creator', 'approvals'])
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('created_at')
            ->paginate(5);
    }

    #[Computed]
    public function dapilOptions(): Collection
    {
        return \App\Models\TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    #[Computed]
    public function kecamatanOptions(): Collection
    {
        return \App\Models\TargetWilayah::query()
            ->when($this->filterDapil !== '', fn (Builder $query) => $query->where('dapil', $this->filterDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        return \App\Models\TargetWilayah::query()
            ->when($this->filterDapil !== '', fn (Builder $query) => $query->where('dapil', $this->filterDapil))
            ->when($this->filterKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->filterKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    #[Computed]
    public function mapImage(): string
    {
        if ($this->filterKecamatan !== '') {
            $slug = str_replace(' ', '-', strtolower($this->filterKecamatan));
            return "/images/peta/kecamatan/{$slug}.png";
        }

        if ($this->filterDapil !== '') {
            $num = str_replace('BEKASI ', '', strtoupper($this->filterDapil));
            return "/images/peta/dapil{$num}.png";
        }

        return "/images/peta/kabupaten-bekasi.png";
    }

    #[Computed]
    public function mapMarkers(): array
    {
        $configs = (new \App\Livewire\Kaderisasi\Index())->getMapConfigs();
        $config = null;

        if ($this->filterKecamatan !== '') {
            $config = $configs[strtoupper($this->filterKecamatan)] ?? null;
        } elseif ($this->filterDapil !== '') {
            $config = $configs[strtoupper($this->filterDapil)] ?? null;
        }

        if (!$config) {
            return [];
        }

        // Aggregate actual event counts per desa
        $actualCounts = $this->filteredQuery()
            ->whereNotNull('lokasi_desa')
            ->select('lokasi_desa', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('lokasi_desa')
            ->pluck('total', 'lokasi_desa')
            ->mapWithKeys(function ($item, $key) {
                return [strtoupper($key) => $item];
            });

        $wilayahs = \App\Models\TargetWilayah::query()
            ->when($this->filterDapil !== '', fn ($q) => $q->where('dapil', $this->filterDapil))
            ->when($this->filterKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->filterKecamatan))
            ->get();

        $maxCount = max(1, $actualCounts->max() ?? 1);

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                $count = $actualCounts[$desaUpper] ?? 0;

                // Color coding based on count (e.g. gray if 0, light green, dark green)
                $color = '#d1d5db'; // gray-300
                if ($count > 0) {
                    $color = '#22c55e'; // green-500
                    if ($count > $maxCount / 2) {
                        $color = '#15803d'; // green-700
                    }
                }

                $size = 14;
                if ($count > 0) {
                    $size = 14 + min(12, round(($count / $maxCount) * 12));
                }

                $markers[] = [
                    'id' => $w->id,
                    'desa' => $w->desa,
                    'x' => $config[$desaUpper]['x'],
                    'y' => $config[$desaUpper]['y'],
                    'color' => $color,
                    'size' => $size,
                    'label' => "{$w->desa} ({$count} Program)",
                ];
            }
        }

        return $markers;
    }

    #[Computed]
    public function bidangOptions(): \Illuminate\Support\Collection
    {
        return \App\Models\BidangDpd::query()->orderBy('urutan')->get();
    }

    public function render()
    {
        return view('livewire.events.index')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Program']);
    }

    private function filteredQuery(): Builder
    {
        $user = auth()->user();

        return Event::query()
            ->forUser($user)
            ->when($this->filterStatus !== '', fn(Builder $q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis !== '', fn(Builder $q) => $q->where('jenis', $this->filterJenis))
            ->when($this->filterLevel !== '', fn(Builder $q) => $q->where('org_level', $this->filterLevel))
            ->when($this->filterBidang !== '', fn(Builder $q) => $q->where('bidang_dpd_id', $this->filterBidang))
            ->when($this->filterDapil !== '', fn(Builder $q) => $q->where('lokasi_dapil', $this->filterDapil))
            ->when($this->filterKecamatan !== '', fn(Builder $q) => $q->where('lokasi_kecamatan', $this->filterKecamatan))
            ->when($this->filterDesa !== '', fn(Builder $q) => $q->where('lokasi_desa', $this->filterDesa))
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $sub): void {
                    $sub->where('judul', 'like', '%'.$this->search.'%')
                        ->orWhere('lokasi', 'like', '%'.$this->search.'%')
                        ->orWhere('lokasi_desa', 'like', '%'.$this->search.'%')
                        ->orWhere('pic_nama', 'like', '%'.$this->search.'%');
                });
            });
    }
}
