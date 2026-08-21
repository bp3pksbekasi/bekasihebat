<?php

declare(strict_types=1);

namespace App\Livewire\Pengaturan;

use App\Models\BidangDpd;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MappingBidang extends Component
{
    #[Layout('components.layouts.app.sidebar')]
    public function render(): View
    {
        $bidangs = BidangDpd::orderBy('urutan')->get();

        return view('livewire.pengaturan.mapping-bidang', [
            'bidangs' => $bidangs,
        ])
        ->title('Mapping Level Bidang');
    }

    public function toggleLevel(string $bidangId, string $level): void
    {
        $bidang = BidangDpd::find($bidangId);
        if ($bidang) {
            $field = 'is_' . $level;
            if (in_array($field, ['is_dpd', 'is_dpc', 'is_dpra'])) {
                $bidang->$field = !$bidang->$field;
                $bidang->save();
            }
        }
    }
}
