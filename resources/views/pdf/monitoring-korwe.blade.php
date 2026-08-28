<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progress Pengisian Infrastruktur</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #fe5000; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 16px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dpc-row { background-color: #ffe8cc; font-weight: bold; }
        .dpra-row { background-color: #fff; }
        .grand-total { background-color: #fe5000; color: white; font-weight: bold; font-size: 11px; }
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 4px; height: 8px; width: 100%; position: relative; }
        .progress-bar-fill { background-color: #3b82f6; height: 100%; border-radius: 4px; }
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
        <h1>Laporan Monitoring Progress Pengisian Infrastruktur</h1>
        <p>Tingkat DPC dan DPRa se-Kabupaten Bekasi</p>
        <p>Target Tahun: {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 17%">Kecamatan / Desa</th>
                <th style="width: 10%">Target</th>
                <th style="width: 9%">Korwe</th>
                <th style="width: 9%">Korte</th>
                <th style="width: 10%">P'galang</th>
                <th style="width: 10%">Total</th>
                <th style="width: 10%">Kekurangan</th>
                <th style="width: 22%">Progress (%)</th>
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
                    $dpcKekurangan = max(0, $dpc['target_total'] - $dpc['terisi_total']);
                @endphp
                <tr class="dpc-row">
                    <td class="text-center">{{ $no++ }}</td>
                    <td>DPC KEC. {{ strtoupper($dpc['kecamatan']) }}</td>
                    <td class="text-center">{{ number_format($dpc['target_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_korwe_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_korte_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_penggalang_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_total']) }}</td>
                    <td class="text-center">{{ number_format($dpcKekurangan) }}</td>
                    <td>
                        <div style="float: left; width: 70%; margin-top: 2px;">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ min(100, $dpcPct) }}%"></div>
                            </div>
                        </div>
                        <div style="float: right; width: 25%; text-align: right; font-size: 9px;">{{ $dpcPct }}%</div>
                        <div style="clear: both;"></div>
                    </td>
                </tr>
                
                @foreach($dpc['dpras'] as $dpra)
                    @php
                        $dpraPct = $dpra['target'] > 0 ? round(($dpra['terisi_total'] / $dpra['target']) * 100, 1) : 0;
                        $dpraKekurangan = max(0, $dpra['target'] - $dpra['terisi_total']);
                    @endphp
                    <tr class="dpra-row">
                        <td></td>
                        <td style="padding-left: 15px;">DPRa {{ $dpra['desa'] }}</td>
                        <td class="text-center">{{ number_format($dpra['target']) }}</td>
                        <td class="text-center">{{ number_format($dpra['terisi_korwe']) }}</td>
                        <td class="text-center">{{ number_format($dpra['terisi_korte']) }}</td>
                        <td class="text-center">{{ number_format($dpra['terisi_penggalang']) }}</td>
                        <td class="text-center">{{ number_format($dpra['terisi_total']) }}</td>
                        <td class="text-center">{{ number_format($dpraKekurangan) }}</td>
                        <td>
                            <div style="float: left; width: 70%; margin-top: 2px;">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="background-color: #64748b; width: {{ min(100, $dpraPct) }}%"></div>
                                </div>
                            </div>
                            <div style="float: right; width: 25%; text-align: right; font-size: 8px; color: #64748b;">{{ $dpraPct }}%</div>
                            <div style="clear: both;"></div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            @php
                $grandPct = $grandTarget > 0 ? round(($grandTerisiTotal / $grandTarget) * 100, 1) : 0;
                $grandKekurangan = max(0, $grandTarget - $grandTerisiTotal);
            @endphp
            <tr class="grand-total">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($grandTarget) }}</td>
                <td class="text-center">{{ number_format($grandTerisiKorwe) }}</td>
                <td class="text-center">{{ number_format($grandTerisiKorte) }}</td>
                <td class="text-center">{{ number_format($grandTerisiPenggalang) }}</td>
                <td class="text-center">{{ number_format($grandTerisiTotal) }}</td>
                <td class="text-center">{{ number_format($grandKekurangan) }}</td>
                <td class="text-center">{{ $grandPct }}%</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
