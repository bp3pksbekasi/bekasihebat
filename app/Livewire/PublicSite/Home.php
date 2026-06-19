<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\AnggotaDewan;
use App\Models\Aspirasi;
use App\Models\Berita;
use App\Models\DataRw;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Kader;
use App\Models\KontakWarga;
use App\Models\TargetWilayah;
use App\Models\TitikRki;
use App\Models\TitikSenam;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Kabupaten Bekasi Hebat')]
class Home extends Component
{
    public function getEventMendatangProperty(): Collection
    {
        return Event::query()
            ->where('is_public', true)
            ->where('status', Event::STATUS_DISETUJUI)
            ->where('tanggal_mulai', '>=', now())
            ->withCount('registrations')
            ->orderBy('tanggal_mulai')
            ->limit(3)
            ->get();
    }

    public function getStatsProperty(): array
    {
        return [
            'dapil' => 7,
            'kecamatan' => (int) TargetWilayah::query()->distinct('kecamatan')->count('kecamatan'),
            'desa' => (int) TargetWilayah::query()->count(),
            'rw' => (int) DataRw::query()->count(),
            'event_total' => (int) Event::query()->where('status', Event::STATUS_SELESAI)->count(),
            'warga_terjangkau' => (int) KontakWarga::query()->aktif()->count(),
            'kader_aktif' => (int) Kader::query()->aktif()->count(),
            'titik_senam' => (int) TitikSenam::query()->aktif()->count(),
            'member' => (int) User::query()->whereNotNull('member_number')->count(),
        ];
    }

    public function getProgramProperty(): array
    {
        return [
            [
                'key' => 'rki',
                'label' => 'RKI',
                'count' => (int) TitikRki::query()->aktif()->count(),
                'icon' => 'ti ti-heart-handshake',
                'color' => '#ec4899',
                'description' => 'Ruang kegiatan ibu dan keluarga berbasis wilayah yang aktif di tingkat RW.',
                'target_label' => 'titik aktif',
            ],
            [
                'key' => 'ksn',
                'label' => 'KSN',
                'count' => (int) TitikSenam::query()->aktif()->count(),
                'icon' => 'ti ti-barbell',
                'color' => '#16a34a',
                'description' => 'Pembinaan titik olahraga warga untuk penguatan interaksi dan kesehatan komunitas.',
                'target_label' => 'titik senam',
            ],
            [
                'key' => 'sapa-warga',
                'label' => 'Sapa Warga',
                'count' => (int) KontakWarga::query()->aktif()->count(),
                'icon' => 'ti ti-address-book',
                'color' => '#f97316',
                'description' => 'Jaringan kontak warga yang terus diperluas untuk pelayanan dan pengorganisasian.',
                'target_label' => 'warga terjangkau',
            ],
            [
                'key' => 'kaderisasi',
                'label' => 'Kaderisasi',
                'count' => (int) Kader::query()->aktif()->count(),
                'icon' => 'ti ti-school',
                'color' => '#2563eb',
                'description' => 'Penguatan kader aktif melalui pembinaan, pelatihan, dan deployment wilayah.',
                'target_label' => 'kader aktif',
            ],
        ];
    }

    public function getDprdProperty(): Collection
    {
        return AnggotaDewan::query()
            ->aktif()
            ->orderBy('dapil')
            ->limit(4)
            ->get();
    }

    public function getBeritaFeaturedProperty(): ?Berita
    {
        return Berita::query()
            ->published()
            ->featured()
            ->orderByDesc('published_at')
            ->first();
    }

    public function getBeritaListProperty(): Collection
    {
        return Berita::query()
            ->published()
            ->where('is_featured', false)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    public function getGaleriHighlightsProperty(): Collection
    {
        return Galeri::query()
            ->published()
            ->orderByDesc('tanggal')
            ->orderBy('urutan')
            ->limit(6)
            ->get();
    }

    public function getAspirasiStatsProperty(): array
    {
        if (! Schema::hasTable('aspirasis')) {
            return [
                'total' => 0,
                'sipd' => 0,
                'dianggarkan' => 0,
                'terealisasi' => 0,
            ];
        }

        return [
            'total' => (int) Aspirasi::query()->count(),
            'sipd' => (int) Aspirasi::query()->whereIn('status', ['input_sipd', 'verifikasi_bappeda', 'dianggarkan', 'terealisasi'])->count(),
            'dianggarkan' => (int) Aspirasi::query()->whereIn('status', ['dianggarkan', 'terealisasi'])->count(),
            'terealisasi' => (int) Aspirasi::query()->where('status', 'terealisasi')->count(),
        ];
    }

    public function getAspirasiStoriesProperty(): Collection
    {
        if (! Schema::hasTable('aspirasis')) {
            return collect();
        }

        return Aspirasi::query()
            ->with('assignedDewan')
            ->where('status', 'terealisasi')
            ->latest('realisasi_at')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.public-site.home');
    }
}
