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

    public function selectItem(string $id): void
    {
        $this->selectedGaleriId = $id;
    }

    public function closeLightbox(): void
    {
        $this->selectedGaleriId = null;
    }

    public function getSelectedItemProperty()
    {
        if (! $this->selectedGaleriId) {
            return null;
        }

        return GaleriModel::with('event')->find($this->selectedGaleriId);
    }

    public function nextItem(): void
    {
        if (! $this->selectedGaleriId) {
            return;
        }

        $items = GaleriModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderByDesc('tanggal')
            ->orderBy('urutan')
            ->pluck('id')
            ->toArray();

        $currentIndex = array_search($this->selectedGaleriId, $items);
        if ($currentIndex !== false && isset($items[$currentIndex + 1])) {
            $this->selectedGaleriId = $items[$currentIndex + 1];
        } elseif ($currentIndex !== false && count($items) > 0) {
            $this->selectedGaleriId = $items[0];
        }
    }

    public function prevItem(): void
    {
        if (! $this->selectedGaleriId) {
            return;
        }

        $items = GaleriModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderByDesc('tanggal')
            ->orderBy('urutan')
            ->pluck('id')
            ->toArray();

        $currentIndex = array_search($this->selectedGaleriId, $items);
        if ($currentIndex !== false && isset($items[$currentIndex - 1])) {
            $this->selectedGaleriId = $items[$currentIndex - 1];
        } elseif ($currentIndex !== false && count($items) > 0) {
            $this->selectedGaleriId = $items[count($items) - 1];
        }
    }

    public function render()
    {
        return view('livewire.public-site.galeri');
    }
}
