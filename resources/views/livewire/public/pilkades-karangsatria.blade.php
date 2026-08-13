<div style="display:flex; flex-direction:column; height:100%; background:#fff;">

    {{-- ===== HEADER ===== --}}
    <div style="flex:none; background:linear-gradient(135deg,#065f46,#047857); color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.25);">
        <div>
            <h1 style="margin:0; font-size:1.05rem; font-weight:700; display:flex; align-items:center; gap:8px; letter-spacing:.02em;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PETA STRATEGI PILKADES — DESA KARANGSATRIA
            </h1>
            <p style="margin:3px 0 0; font-size:0.7rem; color:#a7f3d0; letter-spacing:.04em;">
                DAPIL 4 &bull; KEC. TAMBUN UTARA &bull; 32 RW &bull; Data Pemilu 2019/2024
            </p>
        </div>
        <div style="display:flex; gap:10px; align-items:center; flex-shrink:0;">
            <span style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.25); padding:4px 12px; border-radius:6px; font-size:0.68rem; font-weight:700;">
                {{ count($rwData) }} RW
            </span>
            <a href="/" style="background:#059669; border:1px solid #34d399; padding:4px 12px; border-radius:6px; font-size:0.68rem; font-weight:600; color:#fff; text-decoration:none;">
                &larr; Beranda
            </a>
        </div>
    </div>

    @if(!$targetWilayah)
        <div style="flex:1; display:flex; align-items:center; justify-content:center; background:#f8fafc;">
            <div style="text-align:center; color:#6b7280; padding:40px;">
                <p style="font-size:1.2rem; font-weight:700; color:#374151;">Data Wilayah Tidak Ditemukan</p>
                <p style="font-size:0.85rem; margin-top:6px;">Pastikan master data Desa Karangsatria sudah tersedia di sistem.</p>
            </div>
        </div>
    @else

        {{-- ===== DEBUG INFO (hapus setelah selesai diagnosa) ===== --}}
        @if(!empty($debugInfo))
        <div style="background:#1e1b4b; color:#c7d2fe; font-family:monospace; font-size:0.68rem; padding:8px 16px; border-bottom:2px solid #4f46e5; flex:none; overflow:auto; max-height:120px;">
            <strong style="color:#818cf8;">🔍 DEBUG PERIOD INFO:</strong>
            <span style="margin-left:10px; color:#a5b4fc;">Period terpilih: <strong style="color:#f0abfc;">{{ $debugInfo['selected_period'] ?? '-' }}</strong></span>
            <span style="margin-left:16px; color:#a5b4fc;">Desa dicari: <strong style="color:#fde68a;">{{ $debugInfo['desa_query'] ?? '-' }}</strong></span>
            <span style="margin-left:16px; color:#a5b4fc;">Summary ditemukan: <strong style="color:{{ ($debugInfo['summary_found'] ?? false) ? '#4ade80' : '#f87171' }}">{{ ($debugInfo['summary_found'] ?? false) ? 'YA' : 'TIDAK' }}</strong></span>
            <span style="margin-left:16px; color:#a5b4fc;">rw_rows count: <strong style="color:#fb923c;">{{ $debugInfo['rw_rows_count'] ?? 0 }}</strong></span>
            <span style="margin-left:16px; color:#a5b4fc;">pks_votes desa: <strong style="color:#4ade80;">{{ number_format($debugInfo['desa_pks_votes'] ?? 0, 0, ',', '.') }}</strong></span>
            @if(!empty($debugInfo['any_summary_period']))
            <br><strong style="color:#f87171;">⚠ Summary TIDAK ditemukan utk period tsb.</strong>
            <span style="margin-left:8px;">Ada summary di: {{ $debugInfo['any_summary_period'] }}</span>
            @endif
            <br><strong style="color:#818cf8;">Semua period DPRD:</strong>
            @foreach($debugInfo['all_periods'] ?? [] as $p)
            <span style="margin-left:8px; color:#6ee7b7;">{{ $p }}</span>
            @endforeach
        </div>
        @endif

        {{-- ===== TABEL AREA ===== --}}
        <div class="sheet-scroll" style="flex:1; overflow:auto; background:#f1f5f9;">
            <table style="border-collapse:collapse; white-space:nowrap; font-size:0.72rem; font-family:system-ui,-apple-system,sans-serif;">

                {{-- ===== THEAD ===== --}}
                <thead>
                    <tr>
                        {{-- Kelompok: Identitas --}}
                        <th colspan="2" style="background:#1e3a5f; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">IDENTITAS WILAYAH</th>
                        <th colspan="4" style="background:#1e3a5f; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">DATA DASAR</th>
                        <th colspan="3" style="background:#14532d; color:#bbf7d0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">SUARA PEMILU</th>
                        <th colspan="1" style="background:#1e3a5f; color:#bfdbfe; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">3 PARTAI TERKUAT</th>
                        <th colspan="1" style="background:#1e3a5f; color:#bfdbfe; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em;">3 CALEG PEMENANG</th>
                    </tr>
                    <tr>
                        {{-- Identitas --}}
                        <th style="position:sticky; left:0; z-index:20; width:52px; background:#1e293b; color:#94a3b8; padding:8px 6px; text-align:center; font-size:0.62rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #0f172a; text-transform:uppercase;">RW</th>
                        <th style="position:sticky; left:52px; z-index:20; min-width:190px; background:#1e293b; color:#94a3b8; padding:8px 10px; font-size:0.62rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Nama Perumahan / Kampung</th>
                        {{-- Data Dasar --}}
                        <th style="min-width:60px; background:#1e293b; color:#94a3b8; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Jml RT</th>
                        <th style="min-width:80px; background:#1e293b; color:#94a3b8; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Est. DPT</th>
                        <th style="min-width:65px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Korwe</th>
                        <th style="min-width:65px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Korte</th>
                        {{-- Suara --}}
                        <th style="min-width:80px; background:#1c1917; color:#fbbf24; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">Suara PKS</th>
                        <th style="min-width:80px; background:#1c1917; color:#60a5fa; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">Suara PAN</th>
                        <th style="min-width:80px; background:#1c1917; color:#34d399; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">PKS+PAN</th>
                        {{-- 3 Partai Terkuat --}}
                        <th style="min-width:220px; background:#1e293b; color:#94a3b8; padding:8px 10px; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">3 Partai Terkuat di RW</th>
                        {{-- 3 Caleg --}}
                        <th style="min-width:260px; background:#1e293b; color:#94a3b8; padding:8px 10px; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; text-transform:uppercase;">3 Caleg Pemenang di RW</th>
                    </tr>
                </thead>

                {{-- ===== TBODY ===== --}}
                <tbody>
                    @foreach($rwData as $index => $row)
                        @php
                            $bg     = $index % 2 === 0 ? '#ffffff' : '#f8fafc';
                            $pksSum = $row['pks_pan'];
                            $pksBg  = $row['suara_pks'] > 0 ? '#fefce8' : $bg;
                        @endphp
                        <tr style="background:{{ $bg }};">

                            {{-- Nomor RW (sticky) --}}
                            <td style="position:sticky; left:0; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 6px; text-align:center; font-weight:800; color:#0f172a; font-size:0.8rem;">
                                {{ $row['nomor_rw'] }}
                            </td>

                            {{-- Nama Wilayah (sticky) --}}
                            <td style="position:sticky; left:52px; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 10px; font-weight:600; color:#1e293b; max-width:190px; overflow:hidden; text-overflow:ellipsis;" title="{{ $row['nama_wilayah'] }}">
                                {{ $row['nama_wilayah'] }}
                            </td>

                            {{-- Jml RT --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center; color:#475569; font-weight:500;">
                                {{ $row['jumlah_rt'] > 0 ? $row['jumlah_rt'] : '—' }}
                            </td>

                            {{-- Est. DPT --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:right; font-weight:600; color:#1e293b;">
                                {{ $row['estimasi_dpt'] > 0 ? number_format($row['estimasi_dpt'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- Korwe --}}
                            <td style="background:{{ $row['korwe_count'] > 0 ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['korwe_count'] > 0)
                                    <span style="background:#bbf7d0; color:#14532d; font-weight:700; font-size:0.7rem; padding:2px 7px; border-radius:10px;">{{ $row['korwe_count'] }}</span>
                                @else
                                    <span style="color:#cbd5e1; font-size:1rem;">—</span>
                                @endif
                            </td>

                            {{-- Korte --}}
                            <td style="background:{{ $row['korte_count'] > 0 ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 8px; text-align:center;">
                                @if($row['korte_count'] > 0)
                                    <span style="background:#dcfce7; border:1px solid #86efac; color:#14532d; font-weight:700; font-size:0.68rem; padding:2px 8px; border-radius:4px;">{{ $row['korte_count'] }} RT</span>
                                @else
                                    <span style="color:#cbd5e1; font-size:1rem;">—</span>
                                @endif
                            </td>

                            {{-- Suara PKS --}}
                            <td style="background:{{ $row['suara_pks'] > 0 ? '#fefce8' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:700; color:{{ $row['suara_pks'] > 0 ? '#92400e' : '#94a3b8' }}; font-size:0.78rem;">
                                {{ $row['suara_pks'] > 0 ? number_format($row['suara_pks'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- Suara PAN --}}
                            <td style="background:{{ $row['suara_pan'] > 0 ? '#eff6ff' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:700; color:{{ $row['suara_pan'] > 0 ? '#1e40af' : '#94a3b8' }}; font-size:0.78rem;">
                                {{ $row['suara_pan'] > 0 ? number_format($row['suara_pan'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- PKS + PAN --}}
                            <td style="background:{{ $pksSum > 0 ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 10px; text-align:right; font-weight:800; color:{{ $pksSum > 0 ? '#14532d' : '#94a3b8' }}; font-size:0.82rem;">
                                {{ $pksSum > 0 ? number_format($pksSum, 0, ',', '.') : '—' }}
                            </td>

                            {{-- 3 Partai Terkuat --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:6px 10px; vertical-align:top;">
                                @if(count($row['top3_partai']) > 0)
                                    @foreach(array_slice($row['top3_partai'], 0, 3) as $pi => $p)
                                        <div style="display:flex; align-items:center; gap:5px; {{ $pi > 0 ? 'margin-top:3px;' : '' }}">
                                            <span style="background:{{ $pi === 0 ? '#fef3c7' : ($pi === 1 ? '#f1f5f9' : '#f8fafc') }}; border:1px solid {{ $pi === 0 ? '#fbbf24' : '#e2e8f0' }}; color:{{ $pi === 0 ? '#92400e' : '#475569' }}; font-size:0.6rem; font-weight:800; width:14px; height:14px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $pi+1 }}</span>
                                            <span style="font-size:0.68rem; color:#1e293b; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;" title="{{ $p['party_name'] ?? '' }}">{{ $p['party_name'] ?? '-' }}</span>
                                            <span style="margin-left:auto; font-size:0.65rem; font-weight:700; color:#64748b; flex-shrink:0;">{{ number_format($p['votes'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="color:#cbd5e1; font-size:0.7rem;">Belum ada data</span>
                                @endif
                            </td>

                            {{-- 3 Caleg Pemenang --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; padding:6px 10px; vertical-align:top;">
                                @if(count($row['top3_caleg']) > 0)
                                    @foreach(array_slice($row['top3_caleg'], 0, 3) as $ci => $c)
                                        <div style="display:flex; align-items:center; gap:5px; {{ $ci > 0 ? 'margin-top:3px;' : '' }}">
                                            <span style="background:{{ $ci === 0 ? '#dbeafe' : '#f1f5f9' }}; border:1px solid {{ $ci === 0 ? '#93c5fd' : '#e2e8f0' }}; color:{{ $ci === 0 ? '#1e40af' : '#64748b' }}; font-size:0.6rem; font-weight:800; width:14px; height:14px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $ci+1 }}</span>
                                            <span style="font-size:0.68rem; color:#1e293b; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:145px;" title="{{ $c['name'] }} ({{ $c['party'] }})">{{ $c['name'] }}</span>
                                            <span style="margin-left:auto; font-size:0.6rem; color:#94a3b8; flex-shrink:0; white-space:nowrap;">{{ number_format($c['votes'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="color:#cbd5e1; font-size:0.7rem;">Belum ada data</span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

                {{-- ===== TOTAL ROW ===== --}}
                @php
                    $totalDpt   = array_sum(array_column($rwData, 'estimasi_dpt'));
                    $totalKorwe = array_sum(array_column($rwData, 'korwe_count'));
                    $totalKorte = array_sum(array_column($rwData, 'korte_count'));
                    $totalPks   = array_sum(array_column($rwData, 'suara_pks'));
                    $totalPan   = array_sum(array_column($rwData, 'suara_pan'));
                    $totalSum   = array_sum(array_column($rwData, 'pks_pan'));
                @endphp
                <tfoot>
                    <tr style="background:#f1f5f9; font-weight:700;">
                        <td style="position:sticky; left:0; z-index:5; background:#e2e8f0; border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px 6px; text-align:center; font-size:0.7rem; color:#475569;">TOTAL</td>
                        <td style="position:sticky; left:52px; z-index:5; background:#e2e8f0; border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px 10px; font-size:0.68rem; color:#64748b;">32 RW</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#374151;">—</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#1e293b; font-size:0.78rem;">{{ number_format($totalDpt, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#14532d;">{{ $totalKorwe }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px; text-align:center; color:#14532d;">{{ $totalKorte }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#92400e; font-size:0.8rem;">{{ number_format($totalPks, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#1e40af; font-size:0.8rem;">{{ number_format($totalPan, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px; text-align:right; color:#14532d; font-size:0.85rem;">{{ number_format($totalSum, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; padding:8px;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- STATUS BAR --}}
        <div style="flex:none; background:#1e293b; border-top:1px solid #334155; padding:5px 16px; display:flex; justify-content:space-between; align-items:center; font-size:0.65rem; color:#64748b;">
            <span>Scroll kiri-kanan &amp; atas-bawah untuk melihat semua data &bull; Kolom RW &amp; Nama dibekukan</span>
            <div style="display:flex; gap:14px; align-items:center;">
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#fef3c7;border:1px solid #fbbf24;border-radius:2px;display:inline-block;"></span> Suara PKS</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#eff6ff;border:1px solid #93c5fd;border-radius:2px;display:inline-block;"></span> Suara PAN</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:2px;display:inline-block;"></span> PKS+PAN / Infrastruktur</span>
            </div>
        </div>

    @endif
</div>
