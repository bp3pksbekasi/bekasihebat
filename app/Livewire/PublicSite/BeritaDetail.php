<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Berita as BeritaModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class BeritaDetail extends Component
{
    public BeritaModel $berita;

    public function mount(string $slug): void
    {
        $this->berita = BeritaModel::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $this->berita->increment('views');
    }

    public function getRelatedBeritaProperty()
    {
        return BeritaModel::query()
            ->published()
            ->where('id', '!=', $this->berita->id)
            ->where('kategori', $this->berita->kategori)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();
    }

    public function getLatestBeritaProperty()
    {
        return BeritaModel::query()
            ->published()
            ->where('id', '!=', $this->berita->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.public-site.berita-detail')
            ->title($this->berita->judul . ' - Bekasi Hebat');
    }
}
