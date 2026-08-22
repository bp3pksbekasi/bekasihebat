<?php

namespace App\Http\Controllers;

use App\Models\BidangDpd;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPrintController extends Controller
{
    public function printKinerjaDpd(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', '');

        // 1. Ambil data bidang
        $bidangs = BidangDpd::where('is_dpd', true)->where('is_active', true)->orderBy('urutan')->get();

        // 2. Query Events
        $query = Event::where('is_active', true)->where('org_level', 'dpd')->whereNotNull('bidang_dpd_id');

        if ($year) {
            $query->whereYear('tanggal_mulai', $year);
        }

        if ($month) {
            $query->whereMonth('tanggal_mulai', $month);
        }

        $events = $query->with(['budgetItems', 'report'])->get();

        // 3. Proses Statistik Utama
        $totalProgram = $events->count();
        $totalSelesai = $events->where('status', Event::STATUS_SELESAI)->count();
        $persenSelesai = $totalProgram > 0 ? round(($totalSelesai / $totalProgram) * 100, 1) : 0;

        $totalRAB = 0;
        $totalRealisasi = 0;
        $totalPeserta = 0;

        $rekapPerBidang = [];
        foreach ($bidangs as $bidang) {
            $rekapPerBidang[$bidang->id] = [
                'nama' => $bidang->nama,
                'singkatan' => $bidang->singkatan,
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

        // Data array to pass to view
        $data = [
            'year' => $year,
            'month' => $month,
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
        ];

        $pdf = Pdf::loadView('pdf.reports.kinerja-dpd', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan-Kinerja-DPD-' . $year . ($month ? '-' . $month : '') . '.pdf';
        return $pdf->stream($filename);
    }
}
