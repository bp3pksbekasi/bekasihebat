<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Berita as BeritaModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
#[Title('Berita - Bekasi Hebat')]
class Berita extends Component
{
    use WithPagination;

    public string $filterKategori = '';

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function getBeritaFeaturedProperty(): ?BeritaModel
    {
        return BeritaModel::query()
            ->published()
            ->featured()
            ->orderByDesc('published_at')
            ->first();
    }

    public function getBeritaListProperty()
    {
        return BeritaModel::query()
            ->published()
            ->when($this->filterKategori !== '', fn ($query) => $query->where('kategori', $this->filterKategori))
            ->orderByDesc('published_at')
            ->paginate(12);
    }

    public function getKategoriOptionsProperty(): array
    {
        return BeritaModel::KATEGORI_OPTIONS;
    }

    public function render()
    {
        return view('livewire.public-site.berita');
    }
}
