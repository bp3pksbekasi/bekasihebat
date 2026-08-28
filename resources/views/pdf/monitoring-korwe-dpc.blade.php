<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Klasemen Progress Pengisian Infrastruktur</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 18px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dpc-row { background-color: #fff; }
        .grand-total { background-color: #4f46e5; color: white; font-weight: bold; font-size: 13px; }
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 4px; height: 12px; width: 100%; position: relative; }
        .progress-bar-fill { background-color: #4f46e5; height: 100%; border-radius: 4px; }
        .page-break { page-break-after: always; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: right; font-size: 9px; color: #777; }
        .page-number:before { content: "Halaman " counter(page); }
    </style>
</head>
<body>
    <div class="footer">
        <span class="page-number"></span> | Dicetak pada: {{ date('d-m-Y H:i') }}
    </div>

    <div class="header">
        <h1>Klasemen Progress Pengisian Infrastruktur DPC</h1>
        <p>Tingkat Kecamatan se-Kabupaten Bekasi</p>
        <p>Target Tahun: {{ $tahun }}</p>
    </div>

    @php
        // Sort array by progress descending for klasemen
        uasort($data, function($a, $b) {
            $pctA = $a['target_total'] > 0 ? ($a['terisi_total'] / $a['target_total']) : 0;
            $pctB = $b['target_total'] > 0 ? ($b['terisi_total'] / $b['target_total']) : 0;
            if ($pctA == $pctB) {
                return $b['terisi_total'] <=> $a['terisi_total'];
            }
            return $pctB <=> $pctA;
        });
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 5%">Rank</th>
                <th style="width: 20%">Kecamatan</th>
                <th style="width: 10%">Target</th>
                <th style="width: 10%">Korwe</th>
                <th style="width: 10%">Korte</th>
                <th style="width: 10%">P'galang</th>
                <th style="width: 10%">Total</th>
                <th style="width: 25%">Progress (%)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1;
                $grandTarget = 0;
                $grandTerisiKorwe = 0;
                $grandTerisiKorte = 0;
                $grandTerisiPenggalang = 0;
                $grandTerisiTotal = 0;
            @endphp
            @foreach($data as $dpc)
                @php 
                    $grandTarget += $dpc['target_total'];
                    $grandTerisiKorwe += $dpc['terisi_korwe_total'];
                    $grandTerisiKorte += $dpc['terisi_korte_total'];
                    $grandTerisiPenggalang += $dpc['terisi_penggalang_total'];
                    $grandTerisiTotal += $dpc['terisi_total'];

                    $dpcPct = $dpc['target_total'] > 0 ? round(($dpc['terisi_total'] / $dpc['target_total']) * 100, 1) : 0;
                @endphp
                <tr class="dpc-row">
                    <td class="text-center font-bold" style="font-size: 14px;">{{ $no++ }}</td>
                    <td style="font-weight: bold;">DPC KEC. {{ strtoupper($dpc['kecamatan']) }}</td>
                    <td class="text-center">{{ number_format($dpc['target_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_korwe_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_korte_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_penggalang_total']) }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($dpc['terisi_total']) }}</td>
                    <td>
                        <div style="float: left; width: 75%; margin-top: 2px;">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ min(100, $dpcPct) }}%"></div>
                            </div>
                        </div>
                        <div style="float: right; width: 20%; text-align: right; font-weight: bold;">{{ $dpcPct }}%</div>
                        <div style="clear: both;"></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $grandPct = $grandTarget > 0 ? round(($grandTerisiTotal / $grandTarget) * 100, 1) : 0;
            @endphp
            <tr class="grand-total">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($grandTarget) }}</td>
                <td class="text-center">{{ number_format($grandTerisiKorwe) }}</td>
                <td class="text-center">{{ number_format($grandTerisiKorte) }}</td>
                <td class="text-center">{{ number_format($grandTerisiPenggalang) }}</td>
                <td class="text-center">{{ number_format($grandTerisiTotal) }}</td>
                <td class="text-center">{{ $grandPct }}%</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
