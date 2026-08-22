<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja DPD</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #fe5000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo {
            width: 70px;
        }
        .title-box {
            text-align: center;
        }
        .title-box h2 {
            margin: 0;
            font-size: 18px;
            color: #fe5000;
            text-transform: uppercase;
        }
        .title-box p {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: bold;
        }
        .section-title {
            background-color: #f4f4f5;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #fe5000;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-box td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            width: 33.33%;
        }
        .summary-box .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-box .value {
            font-size: 18px;
            color: #fe5000;
            font-weight: bold;
            margin-top: 5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #fafafa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .signature-box {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 20px;
        }
        .sign-area {
            height: 80px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                @php
                    $logoPath = public_path('images/pks-logo.png');
                    $logoData = '';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp
                <td style="width: 20%;">
                    @if($logoData)
                        <img src="{{ $logoData }}" class="logo" alt="Logo PKS">
                    @endif
                </td>
                <td class="title-box" style="width: 60%;">
                    <h2>Laporan Kinerja DPD</h2>
                    <p>Periode: {{ $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' : '' }}{{ $year }}</p>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>
    </div>

    <div class="section-title">A. RINGKASAN EKSEKUTIF</div>
    <table class="summary-box" style="border-collapse: collapse;">
        <tr>
            <td>
                <div class="label">Penyelesaian Program</div>
                <div class="value">{{ $metrics['persen_selesai'] }}%</div>
                <div style="font-size:10px;color:#888;">{{ $metrics['total_selesai'] }} dr {{ $metrics['total_program'] }} selesai</div>
            </td>
            <td>
                <div class="label">Serapan Anggaran</div>
                <div class="value">{{ $metrics['persen_serapan'] }}%</div>
                <div style="font-size:10px;color:#888;">Terserap Rp{{ number_format($metrics['total_realisasi'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Keterlibatan Peserta</div>
                <div class="value">{{ number_format($metrics['total_peserta'], 0, ',', '.') }}</div>
                <div style="font-size:10px;color:#888;">Orang Terlibat</div>
            </td>
        </tr>
    </table>

    <div class="section-title">B. RINCIAN PERFORMANSI BIDANG</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="25%">Bidang</th>
                <th width="15%" class="text-center">Program Selesai</th>
                <th width="20%" class="text-right">Total RAB (Rp)</th>
                <th width="20%" class="text-right">Realisasi (Rp)</th>
                <th width="15%" class="text-center">Serapan (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rekapPerBidang as $rekap)
                @if($rekap['program_total'] > 0)
                    @php
                        $persen = $rekap['rab'] > 0 ? round(($rekap['realisasi'] / $rekap['rab']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            <strong>{{ $rekap['nama'] }}</strong><br>
                            <span style="font-size:10px;color:#666;">Peserta: {{ number_format($rekap['peserta'], 0, ',', '.') }}</span>
                        </td>
                        <td class="text-center">{{ $rekap['program_selesai'] }} / {{ $rekap['program_total'] }}</td>
                        <td class="text-right">{{ number_format($rekap['rab'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($rekap['realisasi'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $persen }}%</td>
                    </tr>
                @endif
            @endforeach
            @if($no === 1)
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data program pada periode ini.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL :</th>
                <th class="text-center">{{ $metrics['total_selesai'] }} / {{ $metrics['total_program'] }}</th>
                <th class="text-right">{{ number_format($metrics['total_rab'], 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($metrics['total_realisasi'], 0, ',', '.') }}</th>
                <th class="text-center">{{ $metrics['persen_serapan'] }}%</th>
            </tr>
        </tfoot>
    </table>

    <table class="signature-box">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div>Ketua DPD</div>
                <div class="sign-area"></div>
                <div class="sign-name">.......................................</div>
            </td>
            <td>
                <div>Dibuat oleh,</div>
                <div>Kesekretariatan DPD</div>
                <div class="sign-area"></div>
                <div class="sign-name">.......................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
