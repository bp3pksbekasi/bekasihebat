<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TargetWilayah;
use App\Models\Korwe;
use App\Traits\WithWilayahScope;

class ReportController extends Controller
{
    use WithWilayahScope;

    public function downloadKorwePdf(Request $request)
    {
        $scope = $this->accessScope();
        
        // Ensure only DPD role has access
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        $tahun = $request->query('tahun', 2026);

        $desas = TargetWilayah::select('id', 'dapil', 'kecamatan', 'desa', "target_korwe_{$tahun} as target_korwe")
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $desaIds = $desas->pluck('id');
        
        $korweCounts = Korwe::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $data = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($data[$kecamatan])) {
                $data[$kecamatan] = [
                    'kecamatan' => $kecamatan,
                    'target_korwe_total' => 0,
                    'terisi_korwe_total' => 0,
                    'dpras' => []
                ];
            }
            $terisi = $korweCounts[$desa->id] ?? 0;
            $data[$kecamatan]['dpras'][] = [
                'desa' => $desa->desa,
                'target' => $desa->target_korwe,
                'terisi' => $terisi,
            ];
            $data[$kecamatan]['target_korwe_total'] += $desa->target_korwe;
            $data[$kecamatan]['terisi_korwe_total'] += $terisi;
        }

        $pdf = Pdf::loadView('pdf.monitoring-korwe', [
            'data' => $data,
            'tahun' => $tahun,
        ]);
        
        return $pdf->download('laporan-progress-korwe.pdf');
    }

    public function downloadKorweDpcPdf(Request $request)
    {
        $scope = $this->accessScope();
        
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        $tahun = $request->query('tahun', 2026);

        $desas = TargetWilayah::select('id', 'dapil', 'kecamatan', 'desa', "target_korwe_{$tahun} as target_korwe")
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $desaIds = $desas->pluck('id');
        
        $korweCounts = Korwe::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $data = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($data[$kecamatan])) {
                $data[$kecamatan] = [
                    'kecamatan' => $kecamatan,
                    'target_korwe_total' => 0,
                    'terisi_korwe_total' => 0,
                ];
            }
            $terisi = $korweCounts[$desa->id] ?? 0;
            $data[$kecamatan]['target_korwe_total'] += $desa->target_korwe;
            $data[$kecamatan]['terisi_korwe_total'] += $terisi;
        }

        $pdf = Pdf::loadView('pdf.monitoring-korwe-dpc', [
            'data' => $data,
            'tahun' => $tahun,
        ]);
        
        return $pdf->download('laporan-peringkat-korwe-dpc.pdf');
    }
}
