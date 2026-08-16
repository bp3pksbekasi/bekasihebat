<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Prioritas Pergerakan Pilkades Karangsatria</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0D1B3D; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 18px; color: #000; text-transform: uppercase; }
        .header p { margin: 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #0D1B3D; color: #fff; font-size: 11px; text-transform: uppercase; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .ring-header { background-color: #f1f5f9; font-weight: bold; color: #0D1B3D; font-size: 13px; text-transform: uppercase; }
        
        .ring-1-title { background-color: #fef2f2; color: #991b1b; padding: 8px; font-weight: bold; font-size: 12px; border: 1px solid #fecaca; margin-bottom: 5px; }
        .ring-2-title { background-color: #f0fdf4; color: #166534; padding: 8px; font-weight: bold; font-size: 12px; border: 1px solid #bbf7d0; margin-bottom: 5px; }
        .ring-3-title { background-color: #fffbeb; color: #b45309; padding: 8px; font-weight: bold; font-size: 12px; border: 1px solid #fde68a; margin-bottom: 5px; }
        .ring-4-title { background-color: #f8fafc; color: #475569; padding: 8px; font-weight: bold; font-size: 12px; border: 1px solid #e2e8f0; margin-bottom: 5px; }
        
        .desc { font-size: 10px; font-weight: normal; display: block; margin-top: 2px; color: #666; }
        
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: right; font-size: 9px; color: #777; }
        .page-number:before { content: "Halaman " counter(page); }
    </style>
</head>
<body>
    <div class="footer">
        <span class="page-number"></span> | Dicetak pada: {{ date('d-m-Y H:i') }}
    </div>

    <div class="header">
        <h1>Peta Prioritas Pergerakan Pilkades Karangsatria</h1>
        <p>Berdasarkan Kuantitas DPT & Modal Afiliasi (Suara PKS+PAN)</p>
    </div>

    @foreach([1, 2, 3, 4] as $ringIndex)
        @php
            $ringData = $rings[$ringIndex] ?? [];
            if (count($ringData) == 0) continue;
            
            $totalDpt = array_sum(array_column($ringData, 'dpt'));
            $totalPksPan = array_sum(array_column($ringData, 'pks_pan'));
            
            $title = '';
            $desc = '';
            $titleClass = '';
            if ($ringIndex == 1) {
                $title = "RING 1: WILAYAH TEMPUR UTAMA (PRIORITAS TERTINGGI)";
                $desc = "DPT Besar (> 1.500) & Afiliasi RW Netral/Belum Diketahui. Kandidat (Pak Uno) wajib turun lobi VIP tertutup.";
                $titleClass = 'ring-1-title';
            } elseif ($ringIndex == 2) {
                $title = "RING 2: LUMBUNG SUARA / MARKAS BESAR (PERTAHANAN)";
                $desc = "RW yang sudah berafiliasi ke UNO. Prioritas: Pemeliharaan massal & pembentukan Korte.";
                $titleClass = 'ring-2-title';
            } elseif ($ringIndex == 3) {
                $title = "RING 3: OPERASI GERILYA (PENETRASI TAKTIS)";
                $desc = "RW yang berafiliasi ke CALON LAIN. Hindari lobi formal, gunakan taktik gerilya via tokoh akar rumput PKS/PAN.";
                $titleClass = 'ring-3-title';
            } elseif ($ringIndex == 4) {
                $title = "RING 4: PRIORITAS RENDAH (EFISIENSI LOGISTIK)";
                $desc = "DPT Kecil (< 1.500) & Afiliasi Netral. Datangi hanya jika ada momentum (efisiensi).";
                $titleClass = 'ring-4-title';
            }
            
            $desc .= " | Total Estimasi DPT: " . number_format($totalDpt, 0, ',', '.') . " | Total Suara PKS+PAN: " . number_format($totalPksPan, 0, ',', '.');
        @endphp

        <div style="page-break-inside: avoid; margin-bottom: 20px;">
            <div class="{{ $titleClass }}">
                {{ $title }}
                <span class="desc">{{ $desc }}</span>
            </div>
            
            <table style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width:10%">RW</th>
                        <th style="width:20%">Wilayah</th>
                        <th style="width:20%">Afiliasi Saat Ini</th>
                        <th style="width:15%">Est. DPT</th>
                        <th style="width:15%">Suara PKS+PAN (2024)</th>
                        <th style="width:20%">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ringData as $row)
                        <tr>
                            <td class="text-center" style="font-weight:bold; font-size:12px;">{{ ltrim($row['rw'], '0') }}</td>
                            <td class="text-center" style="font-size:11px;">{{ $row['wilayah'] }}</td>
                            <td class="text-center" style="font-weight:bold; color:{{ $row['afiliasi'] == 'UNO' ? '#166534' : ($row['afiliasi'] == 'CALON LAIN' ? '#991b1b' : '#475569') }}">
                                @if($row['afiliasi'] === 'CALON LAIN')
                                    {{ $row['calon_lain'] ?: 'CALON LAIN' }}
                                @else
                                    {{ $row['afiliasi'] }}
                                @endif
                            </td>
                            <td class="text-center" style="font-weight:bold;">{{ number_format($row['dpt'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($row['pks_pan'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f1f5f9; font-weight:bold;">
                        <td colspan="3" class="text-right" style="padding-right: 15px; color:#475569;">TOTAL RING {{ $ringIndex }}</td>
                        <td class="text-center" style="color:#0D1B3D;">{{ number_format($totalDpt, 0, ',', '.') }}</td>
                        <td class="text-center" style="color:#0D1B3D;">{{ number_format($totalPksPan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

</body>
</html>
