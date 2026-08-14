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
                DAPIL 4 &bull; KEC. TAMBUN UTARA &bull; 32 RW &bull; Data: {{ $electionLabel ?: 'Pemilu 2019' }}
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

        {{-- ===== TABEL AREA ===== --}}
        <div class="sheet-scroll" style="flex:1; overflow:auto; background:#f1f5f9;">
            <table style="border-collapse:collapse; white-space:nowrap; font-size:0.72rem; font-family:system-ui,-apple-system,sans-serif;">

                {{-- ===== THEAD ===== --}}
                <thead>
                    <tr>
                        {{-- Kelompok: Identitas --}}
                        <th colspan="2" style="background:#1e3a5f; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">IDENTITAS WILAYAH</th>
                        <th colspan="9" style="background:#1e3a5f; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">DATA DASAR</th>
                        <th colspan="4" style="background:#14532d; color:#bbf7d0; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">SUARA PEMILU</th>
                        <th colspan="2" style="background:#1e3a5f; color:#bfdbfe; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">PETA KEKUATAN</th>
                        <th colspan="1" style="background:#1e3a5f; color:#bfdbfe; padding:7px 12px; text-align:center; font-size:0.6rem; font-weight:700; letter-spacing:.06em;">3 CALEG PEMENANG</th>
                    </tr>
                    <tr>
                        {{-- Identitas --}}
                        <th style="position:sticky; left:0; z-index:20; width:52px; background:#1e293b; color:#94a3b8; padding:8px 6px; text-align:center; font-size:0.62rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #0f172a; text-transform:uppercase;">RW</th>
                        <th style="position:sticky; left:52px; z-index:20; min-width:190px; background:#1e293b; color:#94a3b8; padding:8px 10px; font-size:0.62rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Nama Perumahan / Kampung</th>
                        {{-- Data Dasar --}}
                        <th style="min-width:60px; background:#1e293b; color:#94a3b8; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Jml RT</th>
                        <th style="min-width:80px; background:#1e293b; color:#94a3b8; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Est. DPT</th>
                        <th style="min-width:140px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Korwe</th>
                        <th style="min-width:85px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Afiliasi RW</th>
                        <th style="min-width:100px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Afil. PKK</th>
                        <th style="min-width:110px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Afil. K.Taruna</th>
                        <th style="min-width:100px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Afil. DKM</th>
                        <th style="min-width:100px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Afil. Tokoh</th>
                        <th style="min-width:100px; background:#14532d; color:#86efac; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Sosmed</th>
                        {{-- Suara --}}
                        <th style="min-width:80px; background:#1c1917; color:#fbbf24; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">Suara PKS</th>
                        <th style="min-width:80px; background:#1c1917; color:#60a5fa; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">Suara PAN</th>
                        <th style="min-width:80px; background:#1c1917; color:#34d399; padding:8px 8px; text-align:right; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">PKS+PAN</th>
                        <th style="min-width:65px; background:#1c1917; color:#f8fafc; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Juara 1</th>
                        {{-- Peta Kekuatan --}}
                        <th style="min-width:65px; background:#1e293b; color:#22c55e; padding:8px 8px; text-align:center; font-size:0.62rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">PILKADES<br>(UNO)</th>
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
                            <td style="background:{{ $row['korwe_nama'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    @if($row['korwe_nama'])
                                        <span style="color:#14532d; font-weight:700; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;" title="{{ $row['korwe_nama'] }}">{{ $row['korwe_nama'] }}</span>
                                    @else
                                        <span style="color:#cbd5e1; font-size:0.7rem; font-style:italic;">-</span>
                                    @endif
                                    
                                    {{-- Edit Icon --}}
                                    <button wire:click="openKorweModal({{ $row['nomor_rw'] }})" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Data Korwe">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Afiliasi RW --}}
                            <td style="background:{{ $row['afiliasi'] === 'UNO' ? '#dcfce7' : ($row['afiliasi'] === 'Ke calon lain' ? '#fee2e2' : $bg) }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    @if($row['afiliasi'] === 'UNO')
                                        <span style="color:#166534; font-weight:800; font-size:0.65rem;">UNO</span>
                                    @elseif($row['afiliasi'] === 'Ke calon lain')
                                        <span style="color:#991b1b; font-weight:800; font-size:0.65rem;" title="{{ $row['calon_lain'] }}">LAINNYA</span>
                                    @else
                                        <span style="color:#94a3b8; font-style:italic; font-size:0.6rem;">-</span>
                                    @endif
                                    
                                    {{-- Edit Icon --}}
                                    <button wire:click="openAfiliasiModal({{ $row['nomor_rw'] }})" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Afiliasi">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Afiliasi PKK --}}
                            <td style="background:{{ $row['afiliasi_pkk'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <span style="color:{{ $row['afiliasi_pkk'] ? '#14532d' : '#cbd5e1' }}; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70px;" title="{{ $row['afiliasi_pkk'] }}">{{ $row['afiliasi_pkk'] ?: '-' }}</span>
                                    <button wire:click="openEditFieldModal({{ $row['nomor_rw'] }}, 'afiliasi_pkk', 'Afiliasi PKK')" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Afiliasi PKK">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Afiliasi Karang Taruna --}}
                            <td style="background:{{ $row['afiliasi_karang_taruna'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <span style="color:{{ $row['afiliasi_karang_taruna'] ? '#14532d' : '#cbd5e1' }}; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:80px;" title="{{ $row['afiliasi_karang_taruna'] }}">{{ $row['afiliasi_karang_taruna'] ?: '-' }}</span>
                                    <button wire:click="openEditFieldModal({{ $row['nomor_rw'] }}, 'afiliasi_karang_taruna', 'Afiliasi Karang Taruna')" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Afiliasi Karang Taruna">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Afiliasi DKM --}}
                            <td style="background:{{ $row['afiliasi_dkm'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <span style="color:{{ $row['afiliasi_dkm'] ? '#14532d' : '#cbd5e1' }}; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70px;" title="{{ $row['afiliasi_dkm'] }}">{{ $row['afiliasi_dkm'] ?: '-' }}</span>
                                    <button wire:click="openEditFieldModal({{ $row['nomor_rw'] }}, 'afiliasi_dkm', 'Afiliasi DKM')" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Afiliasi DKM">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Afiliasi Tokoh --}}
                            <td style="background:{{ $row['afiliasi_tokoh'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <span style="color:{{ $row['afiliasi_tokoh'] ? '#14532d' : '#cbd5e1' }}; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70px;" title="{{ $row['afiliasi_tokoh'] }}">{{ $row['afiliasi_tokoh'] ?: '-' }}</span>
                                    <button wire:click="openEditFieldModal({{ $row['nomor_rw'] }}, 'afiliasi_tokoh', 'Afiliasi Tokoh')" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Afiliasi Tokoh">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Sosial Media --}}
                            <td style="background:{{ $row['sosial_media'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <span style="color:{{ $row['sosial_media'] ? '#14532d' : '#cbd5e1' }}; font-size:0.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70px;" title="{{ $row['sosial_media'] }}">{{ $row['sosial_media'] ?: '-' }}</span>
                                    <button wire:click="openEditFieldModal({{ $row['nomor_rw'] }}, 'sosial_media', 'Sosial Media')" style="background:transparent; border:none; padding:2px; cursor:pointer; color:#94a3b8; outline:none; transition:color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'" title="Ubah Sosial Media">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:12px; height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
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
                            <td style="background:{{ $pksSum > 0 ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:800; color:{{ $pksSum > 0 ? '#14532d' : '#94a3b8' }}; font-size:0.82rem;">
                                {{ $pksSum > 0 ? number_format($pksSum, 0, ',', '.') : '—' }}
                            </td>

                            {{-- Juara 1 --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 6px; text-align:center;">
                                @if($row['juara_1'] === 'PKS')
                                    <span style="background:#fef3c7; border:1px solid #fbbf24; color:#92400e; font-size:0.62rem; font-weight:800; padding:2px 6px; border-radius:4px;">PKS #1</span>
                                @elseif($row['juara_1'] === 'PAN')
                                    <span style="background:#eff6ff; border:1px solid #93c5fd; color:#1e40af; font-size:0.62rem; font-weight:800; padding:2px 6px; border-radius:4px;">PAN #1</span>
                                @elseif($row['juara_1'] !== '')
                                    <span style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; font-size:0.58rem; font-weight:700; padding:2px 4px; border-radius:4px; text-transform:uppercase;">{{ $row['juara_1'] }} #1</span>
                                @else
                                    <span style="color:#cbd5e1; font-size:0.7rem;">-</span>
                                @endif
                            </td>

                            {{-- Pilkades UNO --}}
                            <td style="background:{{ $row['dukungan_uno'] ? '#22c55e' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 6px; text-align:center;">
                                @if($row['dukungan_uno'])
                                    <span style="color:#ffffff; font-size:0.75rem; font-weight:800;">✓</span>
                                @else
                                    <span style="color:#cbd5e1; font-size:0.7rem;">-</span>
                                @endif
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
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#14532d;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#475569;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#92400e; font-size:0.8rem;">{{ number_format($totalPks, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#1e40af; font-size:0.8rem;">{{ number_format($totalPan, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#14532d; font-size:0.85rem;">{{ number_format($totalSum, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px; text-align:center; color:#475569; font-size:0.7rem;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#22c55e; font-size:0.85rem; font-weight:800;">✓ {{ array_sum(array_column($rwData, 'dukungan_uno')) }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; padding:8px;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- STATUS BAR --}}
        <div style="flex:none; background:#1e293b; border-top:1px solid #334155; padding:5px 16px; display:flex; justify-content:space-between; align-items:center; font-size:0.65rem; color:#64748b;">
            <span>
                Data level RW ini telah disesuaikan secara manual dengan data lapangan yang tervalidasi.
            </span>
            <div style="display:flex; gap:14px; align-items:center;">
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#fef3c7;border:1px solid #fbbf24;border-radius:2px;display:inline-block;"></span> Suara PKS</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#eff6ff;border:1px solid #93c5fd;border-radius:2px;display:inline-block;"></span> Suara PAN</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:2px;display:inline-block;"></span> PKS+PAN / Infrastruktur</span>
            </div>
        </div>

    @endif

    {{-- Modal Afiliasi & Korwe Terpisah --}}

    {{-- Modal Korwe Khusus UNO --}}
    @if($showKorweModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#14532d; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Profil RW {{ $editRwId }}</h3>
                    <button wire:click="$set('showKorweModal', false)" style="background:none; border:none; color:#bbf7d0; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#14532d; margin-bottom:6px;">Nama Korwe</label>
                        <input type="text" wire:model="formKorweNama" placeholder="Ketik nama koordinator..." style="width:100%; padding:8px; border:1px solid #86efac; border-radius:4px; font-size:0.8rem; color:#14532d; outline:none; box-sizing:border-box;">
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showKorweModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveKorwe" style="padding:6px 12px; background:#059669; border:none; color:#fff; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Afiliasi --}}
    @if($showAfiliasiModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#1e293b; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Status Afiliasi RW {{ $editRwId }}</h3>
                    <button wire:click="$set('showAfiliasiModal', false)" style="background:none; border:none; color:#cbd5e1; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#475569; margin-bottom:6px;">Status Dukungan (Afiliasi)</label>
                        <select wire:model.live="formAfiliasi" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:4px; font-size:0.8rem; color:#1e293b; outline:none;">
                            <option value="">Belum Jelas</option>
                            <option value="UNO">UNO</option>
                            <option value="Ke calon lain">Ke calon lain</option>
                        </select>
                    </div>
                    
                    @if($formAfiliasi === 'Ke calon lain')
                        <div style="margin-bottom:12px; background:#fef2f2; border:1px solid #fecaca; padding:10px; border-radius:6px;">
                            <label style="display:block; font-size:0.75rem; font-weight:600; color:#991b1b; margin-bottom:6px;">Nama Calon Lain yang Didukung</label>
                            <input type="text" wire:model="formCalonLain" placeholder="Masukkan nama calon..." style="width:100%; padding:8px; border:1px solid #fca5a5; border-radius:4px; font-size:0.8rem; color:#991b1b; outline:none; box-sizing:border-box;">
                        </div>
                    @endif
                    
                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showAfiliasiModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveAfiliasi" style="padding:6px 12px; background:#059669; border:none; color:#fff; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generic Edit Modal --}}
    @if($showEditModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#14532d; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Ubah {{ $modalTitle }} (RW {{ ltrim($editRwId, '0') }})</h3>
                    <button wire:click="$set('showEditModal', false)" style="background:none; border:none; color:#bbf7d0; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#14532d; margin-bottom:6px;">{{ $modalTitle }}</label>
                        @if($editField === 'sosial_media')
                            <input type="text" wire:model="formFieldValue" placeholder="Ketik {{ strtolower($modalTitle) }}..." style="width:100%; padding:8px; border:1px solid #86efac; border-radius:4px; font-size:0.8rem; color:#14532d; outline:none; box-sizing:border-box;">
                        @else
                            <select wire:model="formFieldValue" style="width:100%; padding:8px; border:1px solid #86efac; border-radius:4px; font-size:0.8rem; color:#14532d; outline:none; box-sizing:border-box;">
                                <option value="">Belum Jelas</option>
                                <option value="UNO">UNO</option>
                                <option value="Ke calon lain">Ke calon lain</option>
                            </select>
                        @endif
                    </div>
                    
                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showEditModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveEditField" style="padding:6px 12px; background:#059669; border:none; color:#fff; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
