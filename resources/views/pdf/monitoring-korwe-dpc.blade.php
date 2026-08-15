<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peringkat Progress Korwe DPC</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #fe5000; padding-bottom: 15px; }
        .header h1 { margin: 0 0 8px; font-size: 20px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 12px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: #1e293b; font-size: 13px; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dpc-row { background-color: #fff; }
        .dpc-row:nth-child(even) { background-color: #f8fafc; }
        .rank-1 { background-color: #dcfce7 !important; font-weight: bold; }
        .rank-2 { background-color: #fef3c7 !important; font-weight: bold; }
        .rank-3 { background-color: #ffedd5 !important; font-weight: bold; }
        .grand-total { background-color: #1e293b; color: white; font-weight: bold; font-size: 13px; }
        .progress-bar-bg { background-color: #e2e8f0; border-radius: 6px; height: 14px; width: 100%; position: relative; }
        .progress-bar-fill { background-color: #3b82f6; height: 100%; border-radius: 6px; }
        .page-break { page-break-after: always; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: right; font-size: 10px; color: #777; }
        .page-number:before { content: "Halaman " counter(page); }
        .rank-badge { display: inline-block; width: 24px; height: 24px; line-height: 24px; text-align: center; border-radius: 50%; background: #3b82f6; color: white; font-weight: bold; }
        .rank-badge-1 { background: #eab308; color: #fff; } /* Gold */
        .rank-badge-2 { background: #94a3b8; color: #fff; } /* Silver */
        .rank-badge-3 { background: #b45309; color: #fff; } /* Bronze */
    </style>
</head>
<body>
    <div class="footer">
        <span class="page-number"></span> | Dicetak pada: {{ date('d-m-Y H:i') }}
    </div>

    <div class="header">
        <h1>Peringkat Progress Pengisian Korwe DPC</h1>
        <p>Klasemen Tingkat Kecamatan se-Kabupaten Bekasi</p>
        <p>Target Tahun: {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 8%">Peringkat</th>
                <th style="width: 25%">DPC (Kecamatan)</th>
                <th class="text-center" style="width: 14%">Target Korwe</th>
                <th class="text-center" style="width: 14%">Terisi</th>
                <th class="text-center" style="width: 14%">Kekurangan</th>
                <th class="text-center" style="width: 25%">Progress (%)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $rank = 1;
                $grandTarget = 0;
                $grandTerisi = 0;
                
                // Sort data by percentage descending
                $sortedData = collect($data)->sortByDesc(function ($dpc) {
                    return $dpc['target_korwe_total'] > 0 ? ($dpc['terisi_korwe_total'] / $dpc['target_korwe_total']) * 100 : 0;
                })->values()->all();
            @endphp
            
            @foreach($sortedData as $dpc)
                @php 
                    $grandTarget += $dpc['target_korwe_total'];
                    $grandTerisi += $dpc['terisi_korwe_total'];
                    $dpcPct = $dpc['target_korwe_total'] > 0 ? round(($dpc['terisi_korwe_total'] / $dpc['target_korwe_total']) * 100, 1) : 0;
                    $dpcKekurangan = max(0, $dpc['target_korwe_total'] - $dpc['terisi_korwe_total']);
                    
                    $rowClass = 'dpc-row';
                    if ($rank === 1) $rowClass .= ' rank-1';
                    elseif ($rank === 2) $rowClass .= ' rank-2';
                    elseif ($rank === 3) $rowClass .= ' rank-3';
                    
                    $badgeClass = 'rank-badge';
                    if ($rank === 1) $badgeClass .= ' rank-badge-1';
                    elseif ($rank === 2) $badgeClass .= ' rank-badge-2';
                    elseif ($rank === 3) $badgeClass .= ' rank-badge-3';
                    
                    $fillColor = '#3b82f6';
                    if ($dpcPct >= 80) $fillColor = '#16a34a';
                    elseif ($dpcPct >= 50) $fillColor = '#eab308';
                    elseif ($dpcPct < 20) $fillColor = '#dc2626';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">
                        <span class="{{ $badgeClass }}">{{ $rank++ }}</span>
                    </td>
                    <td style="font-size: 14px; font-weight: bold;">DPC KEC. {{ strtoupper($dpc['kecamatan']) }}</td>
                    <td class="text-center">{{ number_format($dpc['target_korwe_total']) }}</td>
                    <td class="text-center" style="font-weight: bold; color: {{ $dpcPct >= 80 ? '#16a34a' : '#1e293b' }};">{{ number_format($dpc['terisi_korwe_total']) }}</td>
                    <td class="text-center">{{ number_format($dpcKekurangan) }}</td>
                    <td>
                        <div style="float: left; width: 65%; margin-top: 5px;">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ min(100, $dpcPct) }}%; background-color: {{ $fillColor }};"></div>
                            </div>
                        </div>
                        <div style="float: right; width: 30%; text-align: right; font-size: 13px; font-weight: bold; color: {{ $fillColor }};">{{ $dpcPct }}%</div>
                        <div style="clear: both;"></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $grandPct = $grandTarget > 0 ? round(($grandTerisi / $grandTarget) * 100, 1) : 0;
                $grandKekurangan = max(0, $grandTarget - $grandTerisi);
            @endphp
            <tr class="grand-total">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN (SE-KABUPATEN)</td>
                <td class="text-center">{{ number_format($grandTarget) }}</td>
                <td class="text-center">{{ number_format($grandTerisi) }}</td>
                <td class="text-center">{{ number_format($grandKekurangan) }}</td>
                <td class="text-center">{{ $grandPct }}%</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
