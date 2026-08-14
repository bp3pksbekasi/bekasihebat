<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progress Pengisian Korwe</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #fe5000; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 18px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dpc-row { background-color: #ffe8cc; font-weight: bold; }
        .dpra-row { background-color: #fff; }
        .grand-total { background-color: #fe5000; color: white; font-weight: bold; font-size: 12px; }
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 4px; height: 10px; width: 100%; position: relative; }
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
        <h1>Laporan Monitoring Progress Pengisian Korwe</h1>
        <p>Tingkat DPC dan DPRa se-Kabupaten Bekasi</p>
        <p>Target Tahun: {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Kecamatan / Desa</th>
                <th class="text-center" style="width: 15%">Target Korwe</th>
                <th class="text-center" style="width: 15%">Terisi</th>
                <th class="text-center" style="width: 15%">Kekurangan</th>
                <th class="text-center" style="width: 25%">Progress (%)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1;
                $grandTarget = 0;
                $grandTerisi = 0;
            @endphp
            @foreach($data as $dpc)
                @php 
                    $grandTarget += $dpc['target_korwe_total'];
                    $grandTerisi += $dpc['terisi_korwe_total'];
                    $dpcPct = $dpc['target_korwe_total'] > 0 ? round(($dpc['terisi_korwe_total'] / $dpc['target_korwe_total']) * 100, 1) : 0;
                    $dpcKekurangan = max(0, $dpc['target_korwe_total'] - $dpc['terisi_korwe_total']);
                @endphp
                <tr class="dpc-row">
                    <td class="text-center">{{ $no++ }}</td>
                    <td>DPC KEC. {{ strtoupper($dpc['kecamatan']) }}</td>
                    <td class="text-center">{{ number_format($dpc['target_korwe_total']) }}</td>
                    <td class="text-center">{{ number_format($dpc['terisi_korwe_total']) }}</td>
                    <td class="text-center">{{ number_format($dpcKekurangan) }}</td>
                    <td>
                        <div style="float: left; width: 65%; margin-top: 2px;">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ min(100, $dpcPct) }}%"></div>
                            </div>
                        </div>
                        <div style="float: right; width: 30%; text-align: right; font-size: 10px;">{{ $dpcPct }}%</div>
                        <div style="clear: both;"></div>
                    </td>
                </tr>
                
                @foreach($dpc['dpras'] as $dpra)
                    @php
                        $dpraPct = $dpra['target'] > 0 ? round(($dpra['terisi'] / $dpra['target']) * 100, 1) : 0;
                        $dpraKekurangan = max(0, $dpra['target'] - $dpra['terisi']);
                    @endphp
                    <tr class="dpra-row">
                        <td></td>
                        <td style="padding-left: 20px;">DPRa {{ $dpra['desa'] }}</td>
                        <td class="text-center">{{ number_format($dpra['target']) }}</td>
                        <td class="text-center">{{ number_format($dpra['terisi']) }}</td>
                        <td class="text-center">{{ number_format($dpraKekurangan) }}</td>
                        <td>
                            <div style="float: left; width: 65%; margin-top: 2px;">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="background-color: #64748b; width: {{ min(100, $dpraPct) }}%"></div>
                                </div>
                            </div>
                            <div style="float: right; width: 30%; text-align: right; font-size: 9px; color: #64748b;">{{ $dpraPct }}%</div>
                            <div style="clear: both;"></div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            @php
                $grandPct = $grandTarget > 0 ? round(($grandTerisi / $grandTarget) * 100, 1) : 0;
                $grandKekurangan = max(0, $grandTarget - $grandTerisi);
            @endphp
            <tr class="grand-total">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($grandTarget) }}</td>
                <td class="text-center">{{ number_format($grandTerisi) }}</td>
                <td class="text-center">{{ number_format($grandKekurangan) }}</td>
                <td class="text-center">{{ $grandPct }}%</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
