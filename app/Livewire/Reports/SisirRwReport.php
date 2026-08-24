<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SisirRwReport extends Component
{
    #[Computed]
    public function cakupanTeritorial()
    {
        // Ambil semua target wilayah (Dapil, Kecamatan, Desa) beserta jumlah RW-nya
        $wilayahs = TargetWilayah::orderBy('dapil')->orderBy('kecamatan')->orderBy('desa')->get();
        
        // Ambil data RW unik yang sudah tersisir
        $kegiatanUnik = KegiatanRw::select('target_wilayah_id', 'nomor_rw')
            ->distinct()
            ->get();
            
        // Kelompokkan RW tersisir by target_wilayah_id
        $tersisirByWilayah = [];
        foreach ($kegiatanUnik as $kg) {
            if (!isset($tersisirByWilayah[$kg->target_wilayah_id])) {
                $tersisirByWilayah[$kg->target_wilayah_id] = 0;
            }
            $tersisirByWilayah[$kg->target_wilayah_id]++;
        }

        $dapils = [];
        $blankSpots = [];

        foreach ($wilayahs as $w) {
            $tersisir = $tersisirByWilayah[$w->id] ?? 0;
            
            if (!isset($dapils[$w->dapil])) {
                $dapils[$w->dapil] = ['total_rw' => 0, 'tersisir' => 0];
            }
            
            $dapils[$w->dapil]['total_rw'] += $w->jumlah_rw;
            $dapils[$w->dapil]['tersisir'] += $tersisir;
            
            if ($tersisir == 0) {
                $blankSpots[] = $w;
            }
        }

        return [
            'dapils' => $dapils,
            'blankSpots' => $blankSpots,
        ];
    }

    #[Computed]
    public function kehadiranDewan()
    {
        $kegiatans = KegiatanRw::select('dpr_ri_hadir', 'dprd_prov_hadir', 'dprd_kab_hadir')->get();

        $dpr = [];
        $prov = [];
        $kab = [];

        foreach ($kegiatans as $k) {
            $r = strtoupper(trim((string)$k->dpr_ri_hadir));
            if ($r !== '' && $r !== 'TIDAK ADA') {
                $dpr[$r] = ($dpr[$r] ?? 0) + 1;
            }

            $p = strtoupper(trim((string)$k->dprd_prov_hadir));
            if ($p !== '' && $p !== 'TIDAK ADA') {
                $prov[$p] = ($prov[$p] ?? 0) + 1;
            }

            $b = strtoupper(trim((string)$k->dprd_kab_hadir));
            if ($b !== '' && $b !== 'TIDAK ADA') {
                $kab[$b] = ($kab[$b] ?? 0) + 1;
            }
        }

        arsort($dpr);
        arsort($prov);
        arsort($kab);

        return [
            'dpr' => $dpr,
            'prov' => $prov,
            'kab' => $kab,
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.sisir-rw-report')
            ->title('Laporan Sisir RW');
    }
}
