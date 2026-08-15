<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengisian Sisir RW (DPC)</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #ea580c; padding-bottom: 15px; }
        .header h1 { margin: 0 0 8px; font-size: 20px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 12px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: #1e293b; font-size: 13px; text-transform: uppercase; }
        .text-center { text-align: center; }
        .dpc-row { background-color: #fff; }
        .dpc-row:nth-child(even) { background-color: #f8fafc; }
        .rank-1 { background-color: #dcfce7 !important; font-weight: bold; }
        .rank-2 { background-color: #fef3c7 !important; font-weight: bold; }
        .rank-3 { background-color: #ffedd5 !important; font-weight: bold; }
        .grand-total { background-color: #1e293b; color: white; font-weight: bold; font-size: 13px; }
        .page-break { page-break-after: always; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: right; font-size: 10px; color: #777; }
        .page-number:before { content: "Halaman " counter(page); }
        .rank-badge { display: inline-block; width: 24px; height: 24px; line-height: 24px; text-align: center; border-radius: 50%; background: #ea580c; color: white; font-weight: bold; }
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
        <h1>Peringkat Pengisian Sisir RW</h1>
        <p>Klasemen Tingkat Kecamatan (DPC) se-Kabupaten Bekasi</p>
        <p>Tahun: {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 15%">Peringkat</th>
                <th style="width: 55%">DPC (Kecamatan)</th>
                <th class="text-center" style="width: 30%">Total Kegiatan Diinput</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $rank = 1;
                $grandTotal = 0;
                
                // Sort data by total kegiatan descending
                $sortedData = collect($data)->sortByDesc('total_kegiatan')->values()->all();
            @endphp
            
            @foreach($sortedData as $dpc)
                @php 
                    $grandTotal += $dpc['total_kegiatan'];
                    
                    $rowClass = 'dpc-row';
                    if ($rank === 1) $rowClass .= ' rank-1';
                    elseif ($rank === 2) $rowClass .= ' rank-2';
                    elseif ($rank === 3) $rowClass .= ' rank-3';
                    
                    $badgeClass = 'rank-badge';
                    if ($rank === 1) $badgeClass .= ' rank-badge-1';
                    elseif ($rank === 2) $badgeClass .= ' rank-badge-2';
                    elseif ($rank === 3) $badgeClass .= ' rank-badge-3';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">
                        <span class="{{ $badgeClass }}">{{ $rank++ }}</span>
                    </td>
                    <td style="font-size: 14px; font-weight: bold;">DPC KEC. {{ strtoupper($dpc['kecamatan']) }}</td>
                    <td class="text-center" style="font-size: 16px; font-weight: bold; color: {{ $dpc['total_kegiatan'] > 0 ? '#16a34a' : '#94a3b8' }};">
                        {{ number_format($dpc['total_kegiatan']) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="2" class="text-center">TOTAL KEGIATAN SE-KABUPATEN</td>
                <td class="text-center" style="font-size: 16px;">{{ number_format($grandTotal) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
