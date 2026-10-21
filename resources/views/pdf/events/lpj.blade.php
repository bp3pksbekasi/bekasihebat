<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $event->judul }}</title>
    @php
        $fmtDate = fn ($date, string $format = 'd F Y') => $date instanceof \DateTimeInterface
            ? \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat($format)
            : '-';
        $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');

        $logoPath = public_path('images/pks-logo.png');
        $logoData = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $report = $event->report;
        $totalAnggaran = (float) ($event->budgetItems->isNotEmpty() ? $event->total_budget : $event->total_anggaran);
        $realisasi = (float) ($report?->realisasi_anggaran ?? 0);
        $sisa = $totalAnggaran - $realisasi;
        $serapan = $totalAnggaran > 0 ? round(($realisasi / $totalAnggaran) * 100, 1) : 0;
        $pesertaHadir = (int) ($report?->peserta_hadir ?? 0);

        $statusLabel = $event->status_config['label'] ?? ucfirst((string) $event->status);

        $lokasi = collect([$event->lokasi ?: $event->location_name, $event->lokasi_desa, $event->lokasi_kecamatan])
            ->filter()->unique()->implode(', ');

        $penyelenggara = $event->penyelenggara
            ?: ($event->bidang ? trim($event->bidang->nama . ($event->bidang->singkatan ? ' (' . $event->bidang->singkatan . ')' : '')) : null);

        $isBidangDpd = $event->org_level === 'dpd' && ! empty($event->bidang_dpd_id);
        $approvalLevels = $isBidangDpd
            ? ['kesekretariatan' => 'Kesekretariatan', 'sekum' => 'Sekretaris Umum', 'bendum' => 'Bendahara Umum', 'ketua_dpd' => 'Ketua DPD']
            : ['dpra' => 'Ketua DPRa', 'dpc' => 'Ketua DPC', 'dpd' => 'Ketua DPD'];
        $approvalStatusLabels = ['approved' => 'Disetujui', 'rejected' => 'Ditolak', 'pending' => 'Menunggu'];
        $approvalNotes = $event->approvals->filter(fn ($approval) => filled($approval->catatan));

        $fotoPaths = collect($report?->foto ?? [])
            ->map(fn ($path) => storage_path('app/public/'.$path))
            ->filter(fn ($fullPath) => is_file($fullPath))
            ->take(8)
            ->values();
        $dokumenList = collect($report?->dokumen ?? []);
        $dokumenJenis = \App\Models\EventReport::DOKUMEN_JENIS;
    @endphp
    <style>
        @page { margin: 28mm 18mm 22mm 18mm; }

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
        }

        /* Kop & footer berulang di setiap halaman */
        .page-header {
            position: fixed;
            top: -21mm;
            left: 0;
            right: 0;
            height: 17mm;
            border-bottom: 2px solid #fe5000;
        }
        .page-header table { width: 100%; border-collapse: collapse; }
        .page-header td { vertical-align: middle; padding: 0; }
        .page-header .logo { height: 13mm; }
        .page-header .org { font-size: 8.5px; color: #6b7280; letter-spacing: .4px; text-transform: uppercase; }
        .page-header .doc-title { font-size: 13px; font-weight: bold; color: #111827; letter-spacing: .3px; }

        .page-footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 0.5px solid #d1d5db;
            padding-top: 4px;
            font-size: 8px;
            color: #9ca3af;
        }
        .page-footer table { width: 100%; border-collapse: collapse; }

        /* Judul dokumen */
        .doc-head { text-align: center; margin-bottom: 14px; }
        .doc-head h1 { font-size: 16px; margin: 4px 0 2px; color: #111827; }
        .doc-head .meta { font-size: 9px; color: #6b7280; }

        /* Kartu ringkasan */
        .summary { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 16px; }
        .summary td { width: 25%; background: #fff7f1; border: 0.5px solid #fed7aa; border-radius: 6px; padding: 8px 10px; vertical-align: top; }
        .summary .label { font-size: 8px; color: #9a3412; text-transform: uppercase; letter-spacing: .4px; font-weight: bold; }
        .summary .value { font-size: 12.5px; font-weight: bold; color: #111827; margin-top: 3px; }
        .summary .hint { font-size: 8px; color: #6b7280; margin-top: 1px; }

        /* Judul bagian */
        .section { margin-bottom: 14px; }
        .section-title { font-size: 10.5px; font-weight: bold; color: #111827; text-transform: uppercase; letter-spacing: .4px; padding: 0 0 4px; margin-bottom: 6px; border-bottom: 1.5px solid #fe5000; }
        .section-title .num { color: #fe5000; margin-right: 4px; }

        /* Tabel informasi */
        table.info { width: 100%; border-collapse: collapse; }
        table.info td { padding: 4px 6px; vertical-align: top; border-bottom: 0.5px solid #f3f4f6; }
        table.info td.label { width: 30%; color: #6b7280; }
        table.info td.sep { width: 2%; color: #9ca3af; }
        table.info td.value { color: #111827; }

        .text-block { padding: 6px 8px; background: #f9fafb; border-left: 2px solid #e5e7eb; margin-bottom: 8px; }
        .text-block .label { font-size: 8.5px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }

        /* Tabel RAB */
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #1f2937; color: #fff; font-size: 8.5px; text-transform: uppercase; letter-spacing: .3px; padding: 6px; text-align: left; }
        table.data td { padding: 5px 6px; border-bottom: 0.5px solid #e5e7eb; vertical-align: top; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        table.data tfoot td { padding: 5px 6px; font-weight: bold; border-top: 1px solid #1f2937; }
        table.data tfoot tr.sub td { border-top: none; font-weight: normal; color: #4b5563; }
        table.data tr { page-break-inside: avoid; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #9ca3af; font-style: italic; }

        .badge { display: inline-block; padding: 1px 7px; border-radius: 9px; font-size: 8.5px; font-weight: bold; }

        /* Lembar persetujuan: tidak boleh terpotong antar halaman */
        .approval-sheet { page-break-inside: avoid; margin-top: 18px; }

        table.photos { width: 100%; border-collapse: separate; border-spacing: 4px; margin: 0 -4px; }
        table.photos tr { page-break-inside: avoid; }
        table.photos td { width: 25%; height: 92px; padding: 0; text-align: center; vertical-align: middle; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
        table.photos img { max-width: 100%; max-height: 92px; }
        table.sign { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.sign td { border: 0.5px solid #d1d5db; padding: 8px 6px; text-align: center; vertical-align: top; }
        table.sign .role { font-size: 8.5px; font-weight: bold; color: #374151; text-transform: uppercase; letter-spacing: .3px; }
        table.sign .stamp { height: 52px; padding-top: 8px; }
        table.sign .stamp-text { display: inline-block; border: 1.5px solid; border-radius: 4px; padding: 3px 8px; font-size: 9px; font-weight: bold; letter-spacing: 1px; }
        table.sign .name { font-weight: bold; color: #111827; border-top: 0.5px solid #9ca3af; padding-top: 4px; margin: 0 6px; }
        table.sign .date { font-size: 8px; color: #6b7280; margin-top: 2px; }
        .notes { margin-top: 8px; font-size: 9px; }
        .notes td { padding: 2px 4px; vertical-align: top; }
    </style>
</head>
<body>

    <div class="page-header">
        <table>
            <tr>
                <td style="width: 18mm;">
                    @if ($logoData)
                        <img src="{{ $logoData }}" class="logo" alt="Logo">
                    @endif
                </td>
                <td>
                    <div class="org">Kabupaten Bekasi Hebat</div>
                    <div class="doc-title">LAPORAN PERTANGGUNGJAWABAN PROGRAM</div>
                </td>
                <td style="text-align: right; font-size: 8.5px; color: #6b7280;">
                    Dicetak {{ $fmtDate(now(), 'd F Y, H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer">
        <table>
            <tr>
                <td>LPJ &middot; {{ \Illuminate\Support\Str::limit($event->judul, 70) }}</td>
            </tr>
        </table>
    </div>

    <div class="doc-head">
        <h1>{{ $event->judul ?: '-' }}</h1>
        <div class="meta">
            {{ $penyelenggara ?: '-' }}
            &nbsp;&middot;&nbsp; {{ $fmtDate($event->tanggal_mulai) }}
            &nbsp;&middot;&nbsp;
            <span class="badge" style="background: {{ $event->status_config['bg'] ?? '#f3f4f6' }}; color: {{ $event->status_config['color'] ?? '#374151' }};">{{ $statusLabel }}</span>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Anggaran</div>
                <div class="value">{{ $rupiah($totalAnggaran) }}</div>
                <div class="hint">Rencana (RAB)</div>
            </td>
            <td>
                <div class="label">Realisasi</div>
                <div class="value">{{ $rupiah($realisasi) }}</div>
                <div class="hint">Serapan {{ str_replace(".", ",", (string) $serapan) }}%</div>
            </td>
            <td>
                <div class="label">Sisa Anggaran</div>
                <div class="value">{{ $rupiah($sisa) }}</div>
                <div class="hint">{{ $sisa < 0 ? 'Melebihi anggaran' : 'Belum terserap' }}</div>
            </td>
            <td>
                <div class="label">Peserta Hadir</div>
                <div class="value">{{ number_format($pesertaHadir, 0, ',', '.') }} orang</div>
                <div class="hint">{{ $event->kapasitas ? 'Kapasitas ' . number_format((int) $event->kapasitas, 0, ',', '.') : 'Sesuai laporan' }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title"><span class="num">A.</span>Informasi Program</div>
        <table class="info">
            <tr><td class="label">Nama Program</td><td class="sep">:</td><td class="value">{{ $event->judul ?: '-' }}</td></tr>
            <tr><td class="label">Penyelenggara</td><td class="sep">:</td><td class="value">{{ $penyelenggara ?: '-' }}</td></tr>
            <tr>
                <td class="label">Penanggung Jawab (PIC)</td><td class="sep">:</td>
                <td class="value">{{ $event->pic_nama ?: '-' }}@if ($event->pic_hp) &nbsp;&middot;&nbsp; {{ $event->pic_hp }}@endif</td>
            </tr>
            <tr>
                <td class="label">Waktu Pelaksanaan</td><td class="sep">:</td>
                <td class="value">
                    {{ $fmtDate($event->tanggal_mulai, 'l, d F Y H:i') }}
                    @if ($event->tanggal_selesai)
                        <br>s.d. {{ $fmtDate($event->tanggal_selesai, 'l, d F Y H:i') }}
                    @endif
                </td>
            </tr>
            <tr><td class="label">Lokasi</td><td class="sep">:</td><td class="value">{{ $lokasi ?: '-' }}</td></tr>
            <tr>
                <td class="label">Target Program</td><td class="sep">:</td>
                <td class="value">{{ $event->target_program ? \Illuminate\Support\Str::headline($event->target_program) : '-' }}</td>
            </tr>
            @if ($event->requirements)
                <tr><td class="label">Sasaran Peserta</td><td class="sep">:</td><td class="value">{{ $event->requirements }}</td></tr>
            @endif
            <tr><td class="label">Sumber Pendanaan</td><td class="sep">:</td><td class="value">{{ $event->funding_source ?: '-' }}</td></tr>
            @if ($event->speakers)
                <tr><td class="label">Narasumber / Pihak Terlibat</td><td class="sep">:</td><td class="value">{{ is_array($event->speakers) ? implode(', ', $event->speakers) : $event->speakers }}</td></tr>
            @endif
            <tr><td class="label">Deskripsi</td><td class="sep">:</td><td class="value">{{ $event->deskripsi ?: '-' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title"><span class="num">B.</span>Laporan Pelaksanaan &amp; Evaluasi</div>
        @if ($report)
            <div class="text-block">
                <div class="label">Ringkasan Kegiatan</div>
                {!! nl2br(e($report->ringkasan ?: '-')) !!}
            </div>
            <div class="text-block">
                <div class="label">Evaluasi</div>
                {!! nl2br(e($report->evaluasi ?: '-')) !!}
            </div>
            <div class="text-block">
                <div class="label">Tindak Lanjut</div>
                {!! nl2br(e($report->tindak_lanjut ?: '-')) !!}
            </div>
        @else
            <p class="muted">Laporan pelaksanaan belum diisi.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title"><span class="num">C.</span>Rencana Anggaran &amp; Realisasi</div>
        @if ($event->budgetItems->isNotEmpty())
            <table class="data">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">No</th>
                        <th style="width: 37%;">Uraian</th>
                        <th style="width: 16%;">Kategori</th>
                        <th style="width: 12%;" class="text-center">Volume</th>
                        <th style="width: 15%;" class="text-right">Harga Satuan</th>
                        <th style="width: 15%;" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event->budgetItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $item->item }}
                                @if ($item->keterangan)
                                    <div style="font-size: 8px; color: #6b7280;">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td>{{ $item->kategori ? \Illuminate\Support\Str::headline($item->kategori) : '-' }}</td>
                            <td class="text-center">{{ rtrim(rtrim(number_format((float) $item->qty, 2, ',', '.'), '0'), ',') }} {{ $item->satuan }}</td>
                            <td class="text-right">{{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total Anggaran</td>
                        <td class="text-right">{{ $rupiah($totalAnggaran) }}</td>
                    </tr>
                    <tr class="sub">
                        <td colspan="5" class="text-right">Realisasi Anggaran</td>
                        <td class="text-right">{{ $rupiah($realisasi) }}</td>
                    </tr>
                    <tr class="sub">
                        <td colspan="5" class="text-right">Sisa Anggaran</td>
                        <td class="text-right">{{ $rupiah($sisa) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p class="muted">Tidak ada rincian item RAB.</p>
            <table class="info">
                <tr><td class="label">Total Anggaran</td><td class="sep">:</td><td class="value">{{ $rupiah($totalAnggaran) }}</td></tr>
                <tr><td class="label">Realisasi Anggaran</td><td class="sep">:</td><td class="value">{{ $rupiah($realisasi) }}</td></tr>
                <tr><td class="label">Sisa Anggaran</td><td class="sep">:</td><td class="value">{{ $rupiah($sisa) }}</td></tr>
            </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title"><span class="num">D.</span>Dokumentasi &amp; Lampiran</div>
        @if ($fotoPaths->isNotEmpty())
            <table class="photos">
                @foreach ($fotoPaths->chunk(4) as $row)
                    <tr>
                        @foreach ($row as $fotoPath)
                            <td><img src="{{ $fotoPath }}" alt="Foto dokumentasi"></td>
                        @endforeach
                        @for ($i = $row->count(); $i < 4; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        @else
            <p class="muted">Belum ada foto dokumentasi.</p>
        @endif

        @if ($dokumenList->isNotEmpty())
            <table class="data" style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">No</th>
                        <th style="width: 30%;">Jenis Dokumen</th>
                        <th style="width: 45%;">Nama File</th>
                        <th style="width: 20%;">Diunggah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dokumenList as $index => $doc)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $dokumenJenis[$doc['jenis'] ?? ''] ?? \Illuminate\Support\Str::headline($doc['jenis'] ?? '-') }}</td>
                            <td>{{ $doc['nama'] ?? basename($doc['path'] ?? '-') }}</td>
                            <td>{{ ! empty($doc['uploaded_at']) ? $fmtDate(\Illuminate\Support\Carbon::parse($doc['uploaded_at']), 'd M Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="font-size: 8px; color: #6b7280; margin-top: 4px;">File asli dokumen tersimpan di sistem dan dapat diunduh dari halaman detail program.</div>
        @else
            <p class="muted">Belum ada dokumen pendukung (kwitansi, absensi, dll.) yang diunggah.</p>
        @endif
    </div>

    <div class="approval-sheet">
        <div class="section-title"><span class="num">E.</span>Lembar Persetujuan</div>
        <table class="sign">
            <tr>
                @foreach ($approvalLevels as $level => $label)
                    @php
                        $approval = $event->approvals->firstWhere('level', $level);
                        $approvalStatus = $approval?->status;
                        $stampColor = match ($approvalStatus) {
                            'approved' => '#16a34a',
                            'rejected' => '#dc2626',
                            default => '#9ca3af',
                        };
                    @endphp
                    <td>
                        <div class="role">{{ $label }}</div>
                        <div class="stamp">
                            @if ($approvalStatus === 'approved' || $approvalStatus === 'rejected')
                                <span class="stamp-text" style="color: {{ $stampColor }}; border-color: {{ $stampColor }};">
                                    {{ strtoupper($approvalStatusLabels[$approvalStatus]) }}
                                </span>
                            @endif
                        </div>
                        <div class="name">{{ $approval?->approver?->name ?? '(.................................)' }}</div>
                        <div class="date">{{ $approval?->decided_at ? $fmtDate($approval->decided_at, 'd M Y, H:i') : 'Belum diproses' }}</div>
                    </td>
                @endforeach
            </tr>
        </table>

        @if ($approvalNotes->isNotEmpty())
            <table class="notes">
                <tr><td colspan="2" style="font-weight: bold; color: #6b7280;">Catatan persetujuan:</td></tr>
                @foreach ($approvalNotes as $approval)
                    <tr>
                        <td style="width: 28%; color: #6b7280;">{{ $approvalLevels[$approval->level] ?? \Illuminate\Support\Str::headline($approval->level) }}</td>
                        <td>{{ $approval->catatan }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

</body>
</html>
