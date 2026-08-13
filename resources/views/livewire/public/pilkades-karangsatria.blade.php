<div style="display:flex; flex-direction:column; height:100%; background:#fff;">

    {{-- ===== HEADER ===== --}}
    <div id="page-header" style="flex:none; background:#047857; color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 6px rgba(0,0,0,0.2); z-index:20;">
        <div>
            <h1 style="margin:0; font-size:1.1rem; font-weight:700; display:flex; align-items:center; gap:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#6ee7b7;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Peta Strategi Pilkades — Desa Karangsatria
            </h1>
            <p style="margin:4px 0 0; font-size:0.75rem; color:#a7f3d0;">Dapil 4 &bull; Kecamatan Tambun Utara &bull; Data update terkini</p>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <span style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.3); padding:4px 12px; border-radius:6px; font-size:0.7rem; font-weight:600;">
                Total RW: {{ count($rwData) }}
            </span>
            <a href="/" style="background:#059669; border:1px solid #34d399; padding:4px 12px; border-radius:6px; font-size:0.7rem; font-weight:600; color:#fff; text-decoration:none;">
                &larr; Beranda
            </a>
        </div>
    </div>

    @if(!$targetWilayah)
        <div style="flex:1; display:flex; align-items:center; justify-content:center; background:#f8fafc;">
            <div style="text-align:center; color:#6b7280;">
                <p style="font-size:1.2rem; font-weight:700;">Data Wilayah Tidak Ditemukan</p>
                <p style="font-size:0.9rem;">Pastikan master data Desa Karangsatria sudah tersedia.</p>
            </div>
        </div>
    @else

        {{-- ===== TABEL AREA (calc agar pasti scroll) ===== --}}
        <div class="sheet-scroll" style="flex:1; overflow:auto; background:#f8fafc;">
            <table style="border-collapse:collapse; width:max-content; min-width:2000px; font-size:0.75rem;">

                {{-- HEADER --}}
                <thead style="position:sticky; top:0; z-index:10;">
                    <tr>
                        <th style="position:sticky; left:0; z-index:20; width:56px; background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:10px 8px; text-align:center; font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; white-space:nowrap;">RW</th>
                        <th style="position:sticky; left:56px; z-index:20; width:220px; background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; white-space:nowrap;">Nama Perumahan / Kampung</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; text-align:center; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:70px;">Jml RT</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; text-align:right; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:90px;">Est. DPT</th>
                        <th style="background:#dcfce7; border-bottom:2px solid #86efac; border-right:1px solid #86efac; padding:10px 8px; text-align:center; font-size:0.65rem; font-weight:700; color:#166534; white-space:nowrap; width:80px;">Korwe</th>
                        <th style="background:#dcfce7; border-bottom:2px solid #86efac; border-right:1px solid #86efac; padding:10px 8px; text-align:center; font-size:0.65rem; font-weight:700; color:#166534; white-space:nowrap; width:80px;">Korte</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:180px;">Tipologi Wilayah</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:220px;">Ekonomi Dominan</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:260px;">Karakteristik Warga</th>
                        <th style="background:#dbeafe; border-bottom:2px solid #93c5fd; border-right:1px solid #93c5fd; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#1e40af; white-space:nowrap; width:200px;">Ketokohan (Ketua RW)</th>
                        <th style="background:#dbeafe; border-bottom:2px solid #93c5fd; border-right:1px solid #93c5fd; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#1e40af; white-space:nowrap; width:200px;">Ketokohan (Tomas/Toga)</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:260px;">Faktor Pendorong/Penghambat</th>
                        <th style="background:#ffedd5; border-bottom:2px solid #fdba74; border-right:1px solid #fdba74; padding:10px 8px; text-align:right; font-size:0.65rem; font-weight:700; color:#9a3412; white-space:nowrap; width:110px;">Suara PKS '19</th>
                        <th style="background:#e2e8f0; border-bottom:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:10px 8px; text-align:center; font-size:0.65rem; font-weight:700; color:#475569; white-space:nowrap; width:120px;">Status Profil</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody>
                    @foreach($rwData as $index => $row)
                        @php $bg = $index % 2 === 0 ? '#ffffff' : '#f8fafc'; @endphp
                        <tr style="background:{{ $bg }};">
                            {{-- Sticky: Nomor RW --}}
                            <td style="position:sticky; left:0; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #94a3b8; padding:7px 8px; text-align:center; font-weight:700; color:#1e293b; font-size:0.8rem; white-space:nowrap;">
                                {{ $row['nomor_rw'] }}
                            </td>
                            {{-- Sticky: Nama Wilayah --}}
                            <td style="position:sticky; left:56px; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; font-weight:600; color:#334155; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['nama_wilayah'] }}">
                                {{ $row['nama_wilayah'] }}
                            </td>
                            {{-- Jml RT --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center; color:#475569;">
                                {{ $row['jumlah_rt'] > 0 ? $row['jumlah_rt'] : '-' }}
                            </td>
                            {{-- Est DPT --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:right; font-weight:600; color:#1e293b;">
                                {{ $row['estimasi_dpt'] > 0 ? number_format($row['estimasi_dpt'], 0, ',', '.') : '-' }}
                            </td>
                            {{-- Korwe --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center; background:{{ $row['korwe_count'] > 0 ? '#f0fdf4' : 'inherit' }};">
                                @if($row['korwe_count'] > 0)
                                    <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; background:#bbf7d0; color:#166534; font-weight:700; font-size:0.75rem;">{{ $row['korwe_count'] }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            {{-- Korte --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center; background:{{ $row['korte_count'] > 0 ? '#f0fdf4' : 'inherit' }};">
                                @if($row['korte_count'] > 0)
                                    <span style="background:#dcfce7; border:1px solid #86efac; padding:2px 8px; border-radius:4px; color:#166534; font-weight:700; font-size:0.7rem;">{{ $row['korte_count'] }} RT</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            {{-- Tipologi --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#475569; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['tipologi'] }}">
                                {{ $row['tipologi'] }}
                            </td>
                            {{-- Ekonomi --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#475569; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['ekonomi_dominan'] }}">
                                {{ $row['ekonomi_dominan'] }}
                            </td>
                            {{-- Profil Warga --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#475569; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['profil_warga'] }}">
                                {{ $row['profil_warga'] }}
                            </td>
                            {{-- Ketua RW --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#1e40af; font-weight:500; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['afiliasi_ketua_rw'] }}">
                                {{ $row['afiliasi_ketua_rw'] }}
                            </td>
                            {{-- Tomas --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#1e40af; font-weight:500; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['afiliasi_tomas'] }}">
                                {{ $row['afiliasi_tomas'] }}
                            </td>
                            {{-- Faktor --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; color:#475569; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $row['faktor_penyebab'] }}">
                                {{ $row['faktor_penyebab'] }}
                            </td>
                            {{-- Suara 2019 --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:right; font-weight:700; color:#c2410c; background:#fff7ed;">
                                {{ $row['suara_pks_2019'] > 0 ? number_format($row['suara_pks_2019'], 0, ',', '.') : '-' }}
                            </td>
                            {{-- Status --}}
                            <td style="border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['is_complete'])
                                    <span style="background:#dcfce7; border:1px solid #86efac; color:#166534; font-size:0.65rem; font-weight:600; padding:2px 8px; border-radius:4px;">Lengkap</span>
                                @else
                                    <span style="background:#f1f5f9; border:1px solid #cbd5e1; color:#64748b; font-size:0.65rem; font-weight:600; padding:2px 8px; border-radius:4px;">Belum Lengkap</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- STATUS BAR --}}
        <div style="flex:none; background:#fff; border-top:1px solid #e2e8f0; padding:6px 16px; display:flex; justify-content:space-between; align-items:center; font-size:0.7rem; color:#94a3b8; z-index:20;">
            <span>Scroll atas-bawah &amp; kiri-kanan untuk melihat semua data.</span>
            <div style="display:flex; gap:16px; align-items:center;">
                <span style="display:flex; align-items:center; gap:5px;"><span style="width:12px;height:12px;background:#dcfce7;border:1px solid #86efac;border-radius:2px; display:inline-block;"></span> Infrastruktur</span>
                <span style="display:flex; align-items:center; gap:5px;"><span style="width:12px;height:12px;background:#dbeafe;border:1px solid #93c5fd;border-radius:2px; display:inline-block;"></span> Ketokohan</span>
                <span style="display:flex; align-items:center; gap:5px;"><span style="width:12px;height:12px;background:#ffedd5;border:1px solid #fdba74;border-radius:2px; display:inline-block;"></span> Suara Historis</span>
            </div>
        </div>

    @endif
</div>
