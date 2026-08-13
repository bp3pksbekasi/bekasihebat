<?php

namespace App\Livewire\Public;

use App\Models\TargetWilayah;
use App\Models\DataRw;
use App\Models\ProfilRw;
use App\Models\Korwe;
use App\Models\Korte;
use Livewire\Component;

class PilkadesKarangsatria extends Component
{
    public $targetWilayah;
    public $rwData = [];

    public function mount()
    {
        // Temukan wilayah Karangsatria, Tambun Utara, Dapil 4
        $this->targetWilayah = TargetWilayah::where('desa', 'KARANGSATRIA')
            ->where('kecamatan', 'TAMBUN UTARA')
            ->first();

        if ($this->targetWilayah) {
            $this->loadData();
        }
    }

    private function loadData()
    {
        // Ambil Data RW, Profil RW, dan Infra
        $dataRws = DataRw::where('target_wilayah_id', $this->targetWilayah->id)
            ->orderBy('nomor_rw')
            ->get();
            
        $profilRws = ProfilRw::where('target_wilayah_id', $this->targetWilayah->id)
            ->get()
            ->keyBy(function($item) {
                return ltrim((string) $item->nomor_rw, '0');
            });

        $korwes = Korwe::where('target_wilayah_id', $this->targetWilayah->id)->get();
        $kortes = Korte::where('target_wilayah_id', $this->targetWilayah->id)->get();
            
        // Group infras by RW
        $korweByRw = [];
        foreach ($korwes as $korwe) {
            $rwKey = ltrim((string) $korwe->nomor_rw, '0');
            if (!isset($korweByRw[$rwKey])) {
                $korweByRw[$rwKey] = collect();
            }
            $korweByRw[$rwKey]->push($korwe);
        }

        $korteByRw = [];
        foreach ($kortes as $korte) {
            $rwKey = ltrim((string) $korte->nomor_rw, '0');
            if (!isset($korteByRw[$rwKey])) {
                $korteByRw[$rwKey] = collect();
            }
            $korteByRw[$rwKey]->push($korte);
        }

        $formattedData = [];
        
        // Loop from 1 to 35 (or based on available data)
        // Karangsatria might have up to ~32 or more RWs
        $maxRw = max(32, $dataRws->max(function($r) { return (int) $r->nomor_rw; }));

        for ($i = 1; $i <= $maxRw; $i++) {
            $rwKey = (string)$i;
            $paddedRw = str_pad($rwKey, 3, '0', STR_PAD_LEFT);
            
            $dataRw = $dataRws->firstWhere('nomor_rw', $paddedRw);
            $profilRw = $profilRws->get($rwKey);
            $korwesInRw = $korweByRw[$rwKey] ?? collect();
            $kortesInRw = $korteByRw[$rwKey] ?? collect();
            
            $korweCount = $korwesInRw->count();
            $korteCount = $kortesInRw->count();

            $formattedData[] = [
                'nomor_rw' => $paddedRw,
                'nama_wilayah' => $profilRw?->nama_wilayah ?: '-',
                'jumlah_rt' => $dataRw?->jumlah_rt ?? 0,
                'estimasi_dpt' => $dataRw?->dpt ?? 0,
                'suara_pks_2019' => $profilRw?->suara_pks_2019 ?? 0,
                'tipologi' => $profilRw?->tipologi ? (\App\Models\ProfilRw::TIPOLOGI_OPTIONS[$profilRw->tipologi] ?? $profilRw->tipologi) : '-',
                'ekonomi_dominan' => $profilRw?->ekonomi_dominan ?: '-',
                'profil_warga' => $profilRw?->profil_warga ?: '-',
                'faktor_penyebab' => $profilRw?->faktor_penyebab ?: '-',
                'afiliasi_ketua_rw' => $profilRw?->afiliasi_ketua_rw ?: '-',
                'afiliasi_tomas' => $profilRw?->afiliasi_tomas ?: '-',
                'korwe_count' => $korweCount,
                'korte_count' => $korteCount,
                'is_complete' => $profilRw?->is_complete ?? false,
            ];
        }
        
        $this->rwData = $formattedData;
    }

    public function render()
    {
        return view('livewire.public.pilkades-karangsatria')
            ->layout('components.layouts.public-fullscreen', ['title' => 'Pemetaan Strategi Pilkades - Karangsatria']);
    }
}
