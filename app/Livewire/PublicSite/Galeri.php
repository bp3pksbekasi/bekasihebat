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

    public function render()
    {
        return view('livewire.public-site.galeri');
    }
}
