<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\BidangDpd;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class KinerjaDpd extends Component
{
    public string $year;
    public string $month = '';

    public function mount()
    {
        $this->year = (string) date('Y');
    }

    public function render()
    {
        // 1. Ambil data bidang yang khusus DPD
        $bidangs = BidangDpd::where('is_dpd', true)->where('is_active', true)->orderBy('urutan')->get();

        // 2. Query Events
        $query = Event::where('org_level', 'dpd')->whereNotNull('bidang_dpd_id');

        if ($this->year) {
            $query->whereYear('tanggal_mulai', $this->year);
        }

        if ($this->month) {
            $query->whereMonth('tanggal_mulai', $this->month);
        }

        // Ambil data beserta relasinya untuk budget dan report
        $events = $query->with(['budgetItems', 'report'])->get();

        // 3. Proses Statistik Utama
        $totalProgram = $events->count();
        $totalSelesai = $events->where('status', Event::STATUS_SELESAI)->count();
        $persenSelesai = $totalProgram > 0 ? round(($totalSelesai / $totalProgram) * 100, 1) : 0;

        $totalRAB = 0;
        $totalRealisasi = 0;
        $totalPeserta = 0;

        $rekapPerBidang = [];
        // Inisialisasi struktur rekap per bidang
        foreach ($bidangs as $bidang) {
            $rekapPerBidang[$bidang->id] = [
                'nama' => $bidang->nama,
                'singkatan' => $bidang->singkatan,
                'color' => $bidang->color ?? '#fe5000',
                'program_total' => 0,
                'program_selesai' => 0,
                'rab' => 0,
                'realisasi' => 0,
                'peserta' => 0,
            ];
        }

        // 4. Kalkulasi Data
        foreach ($events as $event) {
            $bidangId = $event->bidang_dpd_id;
            
            // RAB & Realisasi
            $rab = (float) $event->budgetItems->sum('subtotal');
            $realisasi = (float) ($event->report['realisasi_anggaran'] ?? 0);
            $peserta = (int) ($event->report['peserta_hadir'] ?? 0);

            $totalRAB += $rab;
            $totalRealisasi += $realisasi;
            $totalPeserta += $peserta;

            if (isset($rekapPerBidang[$bidangId])) {
                $rekapPerBidang[$bidangId]['program_total']++;
                if ($event->status === Event::STATUS_SELESAI) {
                    $rekapPerBidang[$bidangId]['program_selesai']++;
                }
                $rekapPerBidang[$bidangId]['rab'] += $rab;
                $rekapPerBidang[$bidangId]['realisasi'] += $realisasi;
                $rekapPerBidang[$bidangId]['peserta'] += $peserta;
            }
        }

        $persenSerapan = $totalRAB > 0 ? round(($totalRealisasi / $totalRAB) * 100, 1) : 0;

        return view('livewire.reports.kinerja-dpd', [
            'metrics' => [
                'total_program' => $totalProgram,
                'total_selesai' => $totalSelesai,
                'persen_selesai' => $persenSelesai,
                'total_rab' => $totalRAB,
                'total_realisasi' => $totalRealisasi,
                'persen_serapan' => $persenSerapan,
                'total_peserta' => $totalPeserta,
            ],
            'rekapPerBidang' => $rekapPerBidang,
        ])->layout('layouts.app', ['title' => 'Laporan Kinerja DPD']);
    }
}
