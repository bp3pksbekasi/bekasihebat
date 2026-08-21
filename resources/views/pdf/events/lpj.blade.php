<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ Program - {{ $event->judul }}</title>
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
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.info-table td {
            padding: 6px;
            vertical-align: top;
        }
        table.info-table td.label {
            width: 25%;
            font-weight: bold;
            color: #555;
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
        .approval-box {
            margin-top: 40px;
            width: 100%;
        }
        .approval-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .approval-box td {
            width: 25%;
            text-align: center;
            vertical-align: bottom;
            padding: 10px;
        }
        .sign-area {
            height: 70px;
            margin-bottom: 10px;
        }
        .approver-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .approver-role {
            font-size: 11px;
            color: #666;
        }
        .approver-date {
            font-size: 10px;
            color: #999;
            margin-top: 4px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: white;
            background-color: #22c55e;
            font-weight: bold;
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
                    <h2>Laporan Pertanggungjawaban</h2>
                    <p>Program / Kegiatan</p>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>
    </div>

    <div class="section-title">A. INFORMASI PROGRAM</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Program</td>
            <td>: {{ $event->judul }}</td>
        </tr>
        <tr>
            <td class="label">Penyelenggara</td>
            <td>: {{ $event->penyelenggara ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">PIC / Penanggung Jawab</td>
            <td>: {{ $event->pic_nama ?: '-' }} ({{ $event->pic_kontak ?: '-' }})</td>
        </tr>
        <tr>
            <td class="label">Waktu Pelaksanaan</td>
            <td>: {{ $event->tanggal_mulai ? $event->tanggal_mulai->format('d M Y, H:i') : '-' }} s/d {{ $event->tanggal_selesai ? $event->tanggal_selesai->format('d M Y, H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Target Sasaran</td>
            <td>: {{ str_replace('_', ' ', strtoupper($event->target_sasaran)) }}</td>
        </tr>
        <tr>
            <td class="label">Sumber Pendanaan</td>
            <td>: {{ $event->sumber_pendanaan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Deskripsi / Latar Belakang</td>
            <td>: {{ $event->deskripsi ?: '-' }}</td>
        </tr>
    </table>

    <div class="section-title">B. LAPORAN PELAKSANAAN & EVALUASI</div>
    <table class="info-table">
        <tr>
            <td class="label">Ringkasan Kegiatan</td>
            <td>: {{ $event->report['ringkasan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Evaluasi</td>
            <td>: {{ $event->report['evaluasi'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tindak Lanjut</td>
            <td>: {{ $event->report['tindak_lanjut'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Peserta Hadir</td>
            <td>: {{ number_format((float) ($event->report['peserta_hadir'] ?? 0)) }} Orang</td>
        </tr>
        <tr>
            <td class="label">Status Program</td>
            <td>: <span class="badge">{{ strtoupper($event->status) }}</span></td>
        </tr>
    </table>

    <div class="section-title">C. RENCANA ANGGARAN & REALISASI (RAB)</div>
    @if($event->budgetItems->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama Item</th>
                    <th width="20%">Qty</th>
                    <th width="20%" class="text-right">Harga Satuan (Rp)</th>
                    <th width="20%" class="text-right">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->budgetItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->item }}</td>
                        <td>{{ $item->qty }} {{ $item->satuan }}</td>
                        <td class="text-right">{{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total Anggaran Direncanakan :</th>
                    <th class="text-right">{{ number_format((float) $event->total_budget, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">Total Realisasi Anggaran :</th>
                    <th class="text-right">{{ number_format((float) ($event->report['realisasi_anggaran'] ?? 0), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <p><i>Tidak ada rincian RAB yang diajukan.</i></p>
    @endif

    <div class="section-title">D. LEMBAR PERSETUJUAN (APPROVAL TRACKER)</div>
    <div class="approval-box">
        <table>
            <tr>
                @php
                    $isBidangDpd = $event->org_level === 'dpd' && !empty($event->bidang_dpd_id);
                    $approvalLevels = $isBidangDpd
                        ? ['kesekretariatan' => 'Kesekretariatan', 'sekum' => 'Sekretaris Umum', 'bendum' => 'Bendahara Umum', 'ketua_dpd' => 'Ketua DPD']
                        : ['dpra' => 'Ketua DPRa', 'dpc' => 'Ketua DPC', 'dpd' => 'Ketua DPD'];
                @endphp

                @foreach ($approvalLevels as $level => $label)
                    @php
                        $approval = $event->approvals->firstWhere('level', $level);
                        $isApproved = $approval && $approval->status === 'approved';
                    @endphp
                    <td>
                        <div class="approver-role">{{ strtoupper($label) }}</div>
                        <div class="sign-area">
                            @if($isApproved)
                                <div style="color: #22c55e; font-size: 24px; font-weight: bold; margin-top: 20px;">✓</div>
                                <div style="color: #22c55e; font-size: 10px; font-weight: bold;">DISETUJUI</div>
                            @endif
                        </div>
                        <div class="approver-name">{{ $approval?->approver?->name ?? '.......................................' }}</div>
                        <div class="approver-date">{{ $approval?->decided_at ? $approval->decided_at->format('d M Y H:i') : '-' }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

</body>
</html>
