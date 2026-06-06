<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\TargetWilayah;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Tentang - Bekasi Hebat')]
class Tentang extends Component
{
    public function getCoverageProperty(): array
    {
        return [
            'dapil' => 7,
            'kecamatan' => 23,
            'desa' => 187,
            'db_kecamatan' => (int) TargetWilayah::query()->distinct('kecamatan')->count('kecamatan'),
            'db_desa' => (int) TargetWilayah::query()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.public-site.tentang');
    }
}
