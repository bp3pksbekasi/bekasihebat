<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TargetWilayah;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
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

        $desas = TargetWilayah::select('id', 'dapil', 'kecamatan', 'desa', "target_korwe_{$tahun} as target_korwe", "target_korte_{$tahun} as target_korte", "target_penggalang_{$tahun} as target_penggalang")
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $desaIds = $desas->pluck('id');
        
        $korweCounts = Korwe::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $korteCounts = Korte::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $penggalangCounts = PenggalangSuara::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $data = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($data[$kecamatan])) {
                $data[$kecamatan] = [
                    'kecamatan' => $kecamatan,
                    'target_total' => 0,
                    'terisi_korwe_total' => 0,
                    'terisi_korte_total' => 0,
                    'terisi_penggalang_total' => 0,
                    'terisi_total' => 0,
                    'dpras' => []
                ];
            }
            $terisiKorwe = $korweCounts[$desa->id] ?? 0;
            $terisiKorte = $korteCounts[$desa->id] ?? 0;
            $terisiPenggalang = $penggalangCounts[$desa->id] ?? 0;
            
            $targetTotal = ($desa->target_korwe ?? 0) + ($desa->target_korte ?? 0) + ($desa->target_penggalang ?? 0);
            $terisiTotal = $terisiKorwe + $terisiKorte + $terisiPenggalang;

            $data[$kecamatan]['dpras'][] = [
                'desa' => $desa->desa,
                'target' => $targetTotal,
                'terisi_korwe' => $terisiKorwe,
                'terisi_korte' => $terisiKorte,
                'terisi_penggalang' => $terisiPenggalang,
                'terisi_total' => $terisiTotal,
            ];
            
            $data[$kecamatan]['target_total'] += $targetTotal;
            $data[$kecamatan]['terisi_korwe_total'] += $terisiKorwe;
            $data[$kecamatan]['terisi_korte_total'] += $terisiKorte;
            $data[$kecamatan]['terisi_penggalang_total'] += $terisiPenggalang;
            $data[$kecamatan]['terisi_total'] += $terisiTotal;
        }

        $pdf = Pdf::loadView('pdf.monitoring-korwe', [
            'data' => $data,
            'tahun' => $tahun,
        ]);

        return $pdf->stream('detail_infrastruktur.pdf');
    }

    public function downloadKorweDpcPdf(Request $request)
    {
        $scope = $this->accessScope();
        
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        $tahun = $request->query('tahun', 2026);

        $desas = TargetWilayah::select('id', 'dapil', 'kecamatan', 'desa', "target_korwe_{$tahun} as target_korwe", "target_korte_{$tahun} as target_korte", "target_penggalang_{$tahun} as target_penggalang")
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $desaIds = $desas->pluck('id');
        
        $korweCounts = Korwe::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $korteCounts = Korte::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $penggalangCounts = PenggalangSuara::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $data = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($data[$kecamatan])) {
                $data[$kecamatan] = [
                    'kecamatan' => $kecamatan,
                    'target_total' => 0,
                    'terisi_korwe_total' => 0,
                    'terisi_korte_total' => 0,
                    'terisi_penggalang_total' => 0,
                    'terisi_total' => 0,
                ];
            }
            $terisiKorwe = $korweCounts[$desa->id] ?? 0;
            $terisiKorte = $korteCounts[$desa->id] ?? 0;
            $terisiPenggalang = $penggalangCounts[$desa->id] ?? 0;
            
            $targetTotal = ($desa->target_korwe ?? 0) + ($desa->target_korte ?? 0) + ($desa->target_penggalang ?? 0);
            $terisiTotal = $terisiKorwe + $terisiKorte + $terisiPenggalang;

            $data[$kecamatan]['target_total'] += $targetTotal;
            $data[$kecamatan]['terisi_korwe_total'] += $terisiKorwe;
            $data[$kecamatan]['terisi_korte_total'] += $terisiKorte;
            $data[$kecamatan]['terisi_penggalang_total'] += $terisiPenggalang;
            $data[$kecamatan]['terisi_total'] += $terisiTotal;
        }

        $pdf = Pdf::loadView('pdf.monitoring-korwe-dpc', [
            'data' => $data,
            'tahun' => $tahun,
        ]);

        return $pdf->stream('klasemen_infrastruktur.pdf');
    }

    public function downloadSisirRwPdf(Request $request)
    {
        $scope = $this->accessScope();
        
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        $tahun = $request->query('tahun', date('Y'));

        $desas = TargetWilayah::select('id', 'kecamatan', 'desa')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        $desaIds = $desas->pluck('id');
        
        $kegiatanCounts = \App\Models\KegiatanRw::whereIn('target_wilayah_id', $desaIds)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $data = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($data[$kecamatan])) {
                $data[$kecamatan] = [
                    'kecamatan' => $kecamatan,
                    'total_kegiatan' => 0,
                ];
            }
            $terisi = $kegiatanCounts[$desa->id] ?? 0;
            $data[$kecamatan]['total_kegiatan'] += $terisi;
        }

        $pdf = Pdf::loadView('pdf.monitoring-sisir-rw', [
            'data' => $data,
            'tahun' => $tahun,
        ]);
        
        return $pdf->download('laporan-peringkat-sisir-rw-dpc.pdf');
    }

    public function downloadPilkadesKarangsatriaPrioritasPdf()
    {
        $targetWilayah = TargetWilayah::where('desa', 'KARANGSATRIA')
            ->where('kecamatan', 'TAMBUN UTARA')
            ->first();

        if (!$targetWilayah) {
            abort(404, 'Data wilayah tidak ditemukan.');
        }

        $profilRws = \App\Models\ProfilRw::where('target_wilayah_id', $targetWilayah->id)
            ->get()
            ->keyBy(function ($item) {
                return ltrim((string) $item->nomor_rw, '0');
            });

        $validatedData = [
            1  => ['pks' => 601, 'pan' => 76,  'dpt' => 1870],
            2  => ['pks' => 210, 'pan' => 69,  'dpt' => 1358],
            3  => ['pks' => 169, 'pan' => 68,  'dpt' => 1574],
            4  => ['pks' => 236, 'pan' => 286, 'dpt' => 2707],
            5  => ['pks' => 329, 'pan' => 181, 'dpt' => 3445],
            6  => ['pks' => 324, 'pan' => 92,  'dpt' => 2167],
            7  => ['pks' => 732, 'pan' => 99,  'dpt' => 3473],
            8  => ['pks' => 629, 'pan' => 276, 'dpt' => 2123],
            9  => ['pks' => 305, 'pan' => 113, 'dpt' => 1869],
            10 => ['pks' => 324, 'pan' => 35,  'dpt' => 966],
            11 => ['pks' => 201, 'pan' => 344, 'dpt' => 1182],
            12 => ['pks' => 227, 'pan' => 444, 'dpt' => 1090],
            13 => ['pks' => 306, 'pan' => 269, 'dpt' => 959],
            14 => ['pks' => 410, 'pan' => 1323,'dpt' => 3457],
            15 => ['pks' => 164, 'pan' => 423, 'dpt' => 1168],
            16 => ['pks' => 401, 'pan' => 77,  'dpt' => 1385],
            17 => ['pks' => 439, 'pan' => 64,  'dpt' => 1663],
            18 => ['pks' => 352, 'pan' => 134, 'dpt' => 1845],
            19 => ['pks' => 191, 'pan' => 145, 'dpt' => 871],
            20 => ['pks' => 94,  'pan' => 126, 'dpt' => 852],
            21 => ['pks' => 567, 'pan' => 594, 'dpt' => 2842],
            22 => ['pks' => 206, 'pan' => 169, 'dpt' => 1076],
            23 => ['pks' => 123, 'pan' => 69,  'dpt' => 806],
            24 => ['pks' => 146, 'pan' => 46,  'dpt' => 520],
            25 => ['pks' => 58,  'pan' => 40,  'dpt' => 273],
            26 => ['pks' => 132, 'pan' => 34,  'dpt' => 819],
            27 => ['pks' => 220, 'pan' => 258, 'dpt' => 1027],
            28 => ['pks' => 179, 'pan' => 403, 'dpt' => 1214],
            29 => ['pks' => 115, 'pan' => 100, 'dpt' => 495],
            30 => ['pks' => 95,  'pan' => 98,  'dpt' => 515],
            31 => ['pks' => 76,  'pan' => 81,  'dpt' => 535],
            32 => ['pks' => 60,  'pan' => 43,  'dpt' => 487],
        ];

        $rings = [1 => [], 2 => [], 3 => [], 4 => []];

        for ($i = 1; $i <= 32; $i++) {
            $dpt = $validatedData[$i]['dpt'] ?? 0;
            $pksPan = ($validatedData[$i]['pks'] ?? 0) + ($validatedData[$i]['pan'] ?? 0);
            $rwKey = (string) $i;
            $profilRw = $profilRws->get($rwKey);
            $afiliasi = $profilRw?->afiliasi_pilkades ?? '';
            $calonLain = $profilRw?->afiliasi_calon_lain ?? '';
            $namaWilayah = $profilRw?->nama_wilayah ?? '-';

            if ($afiliasi === 'UNO') {
                $ring = 2;
            } elseif ($afiliasi === 'CALON LAIN') {
                $ring = 3;
            } else {
                if ($dpt >= 1500) {
                    $ring = 1;
                } else {
                    $ring = 4;
                }
            }
            
            $rings[$ring][] = [
                'rw' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'wilayah' => $namaWilayah,
                'dpt' => $dpt,
                'pks_pan' => $pksPan,
                'afiliasi' => $afiliasi ?: 'BELUM DIKETAHUI',
                'calon_lain' => $calonLain
            ];
        }

        // Sort descending by DPT within each ring
        foreach ($rings as $r => $data) {
            usort($rings[$r], function($a, $b) {
                return $b['dpt'] <=> $a['dpt'];
            });
        }

        $summary = [];
        foreach ($rings as $ringIndex => $ringData) {
            foreach ($ringData as $row) {
                $afiliasi = $row['afiliasi'];
                $calonLain = $row['calon_lain'];
                
                $namaCalon = $afiliasi;
                if ($afiliasi === 'CALON LAIN') {
                    $namaCalon = $calonLain ?: 'CALON LAIN (Belum Spesifik)';
                }

                if (!isset($summary[$namaCalon])) {
                    $summary[$namaCalon] = [
                        'nama_calon' => $namaCalon,
                        'total_rw' => 0,
                        'total_dpt' => 0
                    ];
                }
                
                $summary[$namaCalon]['total_rw'] += 1;
                $summary[$namaCalon]['total_dpt'] += $row['dpt'];
            }
        }
        
        usort($summary, function($a, $b) {
            return $b['total_dpt'] <=> $a['total_dpt'];
        });

        $pdf = Pdf::loadView('pdf.pilkades-karangsatria-prioritas', [
            'rings' => $rings,
            'summary' => $summary,
        ]);
        
        return $pdf->download('peta-prioritas-pilkades-karangsatria.pdf');
    }
    public function getLeaderboardData($tahun)
    {
        // 1. Top 5 Sisir RW
        $desas = TargetWilayah::select('id', 'kecamatan')->get();
        $desaIds = $desas->pluck('id');
        $kegiatanCounts = \App\Models\KegiatanRw::whereIn('target_wilayah_id', $desaIds)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->selectRaw('target_wilayah_id, count(*) as count')
            ->groupBy('target_wilayah_id')
            ->pluck('count', 'target_wilayah_id');

        $sisirByKecamatan = [];
        foreach ($desas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($sisirByKecamatan[$kecamatan])) {
                $sisirByKecamatan[$kecamatan] = 0;
            }
            $sisirByKecamatan[$kecamatan] += $kegiatanCounts[$desa->id] ?? 0;
        }

        arsort($sisirByKecamatan);
        $topSisirData = array_slice($sisirByKecamatan, 0, 5, true);
        
        $dataSisir = [];
        foreach ($topSisirData as $kecamatan => $total) {
            $dataSisir[] = [
                'kecamatan' => $kecamatan,
                'total_kegiatan' => $total,
            ];
        }

        // 2. Top 5 Infrastruktur
        $infraDesas = TargetWilayah::select('id', 'kecamatan', "target_korwe_{$tahun}", "target_korte_{$tahun}", "target_penggalang_{$tahun}")->get();
        
        $korweCounts = Korwe::whereIn('target_wilayah_id', $desaIds)->selectRaw('target_wilayah_id, count(*) as count')->groupBy('target_wilayah_id')->pluck('count', 'target_wilayah_id');
        $korteCounts = Korte::whereIn('target_wilayah_id', $desaIds)->selectRaw('target_wilayah_id, count(*) as count')->groupBy('target_wilayah_id')->pluck('count', 'target_wilayah_id');
        $penggalangCounts = PenggalangSuara::whereIn('target_wilayah_id', $desaIds)->selectRaw('target_wilayah_id, count(*) as count')->groupBy('target_wilayah_id')->pluck('count', 'target_wilayah_id');

        $infraByKecamatan = [];
        foreach ($infraDesas as $desa) {
            $kecamatan = $desa->kecamatan;
            if (!isset($infraByKecamatan[$kecamatan])) {
                $infraByKecamatan[$kecamatan] = ['target_total' => 0, 'terisi_total' => 0];
            }
            $target = ($desa->{"target_korwe_{$tahun}"} ?? 0) + ($desa->{"target_korte_{$tahun}"} ?? 0) + ($desa->{"target_penggalang_{$tahun}"} ?? 0);
            $terisi = ($korweCounts[$desa->id] ?? 0) + ($korteCounts[$desa->id] ?? 0) + ($penggalangCounts[$desa->id] ?? 0);
            
            $infraByKecamatan[$kecamatan]['target_total'] += $target;
            $infraByKecamatan[$kecamatan]['terisi_total'] += $terisi;
        }

        uasort($infraByKecamatan, function($a, $b) {
            $pctA = $a['target_total'] > 0 ? ($a['terisi_total'] / $a['target_total']) : 0;
            $pctB = $b['target_total'] > 0 ? ($b['terisi_total'] / $b['target_total']) : 0;
            if ($pctA == $pctB) {
                return $b['terisi_total'] <=> $a['terisi_total'];
            }
            return $pctB <=> $pctA;
        });
        
        $topInfraData = array_slice($infraByKecamatan, 0, 5, true);
        
        $dataInfra = [];
        foreach ($topInfraData as $kecamatan => $data) {
            $data['kecamatan'] = $kecamatan;
            $dataInfra[] = $data;
        }
        
        $bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan = $bulanList[date('n') - 1];
        
        $firstDayOfMonth = strtotime(date('Y-m-01'));
        $currentDay = time();
        $diffDays = ($currentDay - $firstDayOfMonth) / (60 * 60 * 24);
        $pekan = ceil(($diffDays + date('N', $firstDayOfMonth)) / 7);

        $periode = "PEKAN KE-{$pekan} | " . strtoupper($bulan) . " {$tahun}";

        return compact('dataSisir', 'dataInfra', 'periode');
    }

    public function leaderboard(Request $request)
    {
        $scope = $this->accessScope();
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        return view('reports.leaderboard');
    }

    public function leaderboardImage(Request $request)
    {
        $scope = $this->accessScope();
        if (($scope['mode'] ?? 'global') !== 'dapil' && auth()->user()?->role !== 'admin_dpd' && ($scope['mode'] ?? 'global') !== 'dpd') {
            abort(403, 'Akses ditolak. Fitur ini khusus untuk DPD.');
        }

        $tahun = $request->query('tahun', date('Y'));
        $data = $this->getLeaderboardData($tahun);

        return view('reports.leaderboard-image', $data);
    }
}

