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

    public string $search = '';

    public bool $showDeleteConfirm = false;

    public ?string $deleteId = null;

    public string $viewMode = 'table';

    protected $queryString = [
        'filterStatus' => ['as' => 'status', 'except' => ''],
        'filterJenis' => ['as' => 'jenis', 'except' => ''],
        'filterDapil' => ['as' => 'dapil', 'except' => ''],
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
            ->paginate($this->viewMode === 'cards' ? 12 : 15);
    }

    #[Computed]
    public function dapilOptions(): Collection
    {
        return Event::query()
            ->whereNotNull('lokasi_dapil')
            ->where('lokasi_dapil', '!=', '')
            ->distinct()
            ->orderBy('lokasi_dapil')
            ->pluck('lokasi_dapil');
    }

    public function render()
    {
        return view('livewire.events.index')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Kegiatan / Event']);
    }

    private function filteredQuery(): Builder
    {
        return Event::query()
            ->when($this->filterStatus !== '', fn (Builder $query) => $query->where('status', $this->filterStatus))
            ->when($this->filterJenis !== '', fn (Builder $query) => $query->where('jenis', $this->filterJenis))
            ->when($this->filterDapil !== '', fn (Builder $query) => $query->where('lokasi_dapil', $this->filterDapil))
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $sub): void {
                    $sub->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                        ->orWhere('lokasi', 'like', '%' . $this->search . '%')
                        ->orWhere('lokasi_desa', 'like', '%' . $this->search . '%')
                        ->orWhere('pic_nama', 'like', '%' . $this->search . '%');
                });
            });
    }
}
