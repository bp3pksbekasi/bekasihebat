<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Galeri as GaleriModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
#[Title('Galeri - Bekasi Hebat')]
class Galeri extends Component
{
    use WithPagination;

    public string $filterKategori = '';

    public ?string $selectedGaleriId = null;

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
        $this->selectedGaleriId = null;
    }

    public function selectItem(?string $id): void
    {
        $this->selectedGaleriId = $id;
    }

    public function closeLightbox(): void
    {
        $this->selectedGaleriId = null;
    }

    public function nextItem(): void
    {
        if (! $this->selectedGaleriId) {
            return;
        }

        $current = GaleriModel::find($this->selectedGaleriId);
        if (! $current) {
            return;
        }

        $next = GaleriModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->where(function ($query) use ($current) {
                $query->where('tanggal', '<', $current->tanggal)
                    ->orWhere(function ($query) use ($current) {
                        $query->where('tanggal', '=', $current->tanggal)
                            ->where('urutan', '>', $current->urutan);
                    });
            })
            ->orderByDesc('tanggal')
            ->orderBy('urutan')
            ->first();

        if ($next) {
            $this->selectedGaleriId = $next->id;
        } else {
            // Cycle back to first
            $first = GaleriModel::query()
                ->published()
                ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
                ->orderByDesc('tanggal')
                ->orderBy('urutan')
                ->first();
            if ($first) {
                $this->selectedGaleriId = $first->id;
            }
        }
    }

    public function prevItem(): void
    {
        if (! $this->selectedGaleriId) {
            return;
        }

        $current = GaleriModel::find($this->selectedGaleriId);
        if (! $current) {
            return;
        }

        $prev = GaleriModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->where(function ($query) use ($current) {
                $query->where('tanggal', '>', $current->tanggal)
                    ->orWhere(function ($query) use ($current) {
                        $query->where('tanggal', '=', $current->tanggal)
                            ->where('urutan', '<', $current->urutan);
                    });
            })
            ->orderBy('tanggal')
            ->orderByDesc('urutan')
            ->first();

        if ($prev) {
            $this->selectedGaleriId = $prev->id;
        } else {
            // Cycle to last
            $last = GaleriModel::query()
                ->published()
                ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
                ->orderBy('tanggal')
                ->orderByDesc('urutan')
                ->first();
            if ($last) {
                $this->selectedGaleriId = $last->id;
            }
        }
    }

    public function getSelectedItemProperty(): ?GaleriModel
    {
        if (! $this->selectedGaleriId) {
            return null;
        }

        return GaleriModel::query()->with('event')->find($this->selectedGaleriId);
    }

    public function getGaleriListProperty()
    {
        return GaleriModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderByDesc('tanggal')
            ->orderBy('urutan')
            ->paginate(20);
    }

    public function getKategoriOptionsProperty(): array
    {
        return GaleriModel::KATEGORI_OPTIONS;
    }

    public function render()
    {
        return view('livewire.public-site.galeri');
    }
}
