<div style="display:flex; flex-direction:column; height:100%; background:#fff;">
    @if($isAuthenticated)
    {{-- ===== HEADER ===== --}}
    <div style="flex:none; background:linear-gradient(135deg,#0D1B3D,#122B5A); color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.25);">
        <div>
            <h1 style="margin:0; font-size:1.05rem; font-weight:700; display:flex; align-items:center; gap:8px; letter-spacing:.02em;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PETA STRATEGI PILKADES — DESA KARANGSATRIA
            </h1>
            <p style="margin:3px 0 0; font-size:0.85rem; color:#FFC107; letter-spacing:.04em;">
                DAPIL 4 &bull; KEC. TAMBUN UTARA &bull; 32 RW &bull; Data: {{ $electionLabel ?: 'Pemilu 2019' }}
            </p>
        </div>
        <div style="display:flex; gap:10px; align-items:center; flex-shrink:0;">
            <span style="background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.25); padding:4px 12px; border-radius:6px; font-size:0.85rem; font-weight:700;">
                {{ count($rwData) }} RW
            </span>
            <a href="/" style="background:#FF6A00; border:1px solid #FFC107; padding:4px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; color:#fff; text-decoration:none;">
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
        <style>
            .kbh-table td button[wire\:click^="open"] {
                opacity: 0;
                transition: opacity 0.2s, color 0.2s !important;
            }
            .kbh-table td:hover button[wire\:click^="open"] {
                opacity: 1;
            }
        </style>
        <div class="sheet-scroll" style="flex:1; overflow:auto; background:#f1f5f9;">
            <table class="kbh-table" style="border-collapse:collapse; white-space:nowrap; font-size:0.85rem; font-family:system-ui,-apple-system,sans-serif;">

                {{-- ===== THEAD ===== --}}
                <thead>
                    <tr>
                        {{-- Kelompok: Identitas --}}
                        <th colspan="2" style="background:#0D1B3D; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">IDENTITAS WILAYAH</th>
                        <th colspan="8" style="background:#0D1B3D; color:#e2e8f0; padding:7px 12px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">DATA DASAR</th>
                        <th colspan="4" style="background:#122B5A; color:#bbf7d0; padding:7px 12px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">SUARA PEMILU</th>
                        <th colspan="2" style="background:#0D1B3D; color:#FFC107; padding:7px 12px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.06em; border-right:2px solid #334155;">PETA KEKUATAN</th>
                        <th colspan="1" style="background:#0D1B3D; color:#FFC107; padding:7px 12px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.06em;">3 CALEG PEMENANG</th>
                    </tr>
                    <tr>
                        {{-- Identitas --}}
                        <th style="position:sticky; left:0; z-index:20; width:52px; background:#0D1B3D; color:#FFFFFF; padding:8px 6px; text-align:center; font-size:1rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #0f172a; text-transform:uppercase;">RW</th>
                        <th style="position:sticky; left:52px; z-index:20; min-width:190px; background:#0D1B3D; color:#FFFFFF; padding:8px 10px; font-size:1rem; font-weight:700; letter-spacing:.05em; border-bottom:2px solid #0f172a; border-right:2px solid #334155; box-shadow: inset -2px 0 0 #334155; text-transform:uppercase;">Wilayah</th>
                        {{-- Data Dasar --}}
                        <th style="min-width:60px; background:#0D1B3D; color:#FFFFFF; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Jml RT</th>
                        <th style="min-width:80px; background:#0D1B3D; color:#FFFFFF; padding:8px 8px; text-align:right; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">Est. DPT</th>
                        <th style="min-width:100px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">Korwe</th>
                        <th style="min-width:70px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">RW</th>
                        <th style="min-width:70px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">RT</th>
                        <th style="min-width:70px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">PKK</th>
                        <th style="min-width:75px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">K. Taruna</th>
                        <th style="min-width:70px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #166534; text-transform:uppercase;">DKM</th>
                        <th style="min-width:70px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Tokoh</th>
                        {{-- Suara --}}
                        <th style="min-width:65px; background:#122B5A; color:#fbbf24; padding:8px 8px; text-align:right; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">PKS</th>
                        <th style="min-width:65px; background:#122B5A; color:#60a5fa; padding:8px 8px; text-align:right; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">PAN</th>
                        <th style="min-width:80px; background:#122B5A; color:#FFC107; padding:8px 8px; text-align:right; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #292524; text-transform:uppercase;">PKS+PAN</th>
                        <th style="min-width:65px; background:#122B5A; color:#f8fafc; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">Juara 1</th>
                        {{-- Peta Kekuatan --}}
                        <th style="min-width:110px; background:#0D1B3D; color:#22c55e; padding:8px 8px; text-align:center; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:1px solid #334155; text-transform:uppercase;">SCORE (%)</th>
                        <th style="min-width:170px; background:#0D1B3D; color:#FFFFFF; padding:8px 10px; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; border-right:2px solid #334155; text-transform:uppercase;">3 Partai Terkuat di RW</th>
                        {{-- 3 Caleg --}}
                        <th style="min-width:210px; background:#0D1B3D; color:#FFFFFF; padding:8px 10px; font-size:1rem; font-weight:700; border-bottom:2px solid #0f172a; text-transform:uppercase;">3 Caleg Pemenang di RW</th>
                    </tr>
                </thead>

                {{-- ===== TBODY ===== --}}
                <tbody>
                    @foreach($rwData as $index => $row)
                        @php
                            $bg     = '#ffffff';
                            $pksSum = $row['pks_pan'];
                            $pksBg  = $row['suara_pks'] > 0 ? '#fefce8' : $bg;
                        @endphp
                        <tr style="background:{{ $bg }};">

                            {{-- Nomor RW (sticky) --}}
                            <td style="position:sticky; left:0; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 6px; text-align:center; font-weight:800; color:#0f172a; font-size:1rem;">
                                {{ $row['nomor_rw'] }}
                            </td>

                            {{-- Nama Wilayah (sticky) --}}
                            <td style="position:sticky; left:52px; z-index:5; background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; box-shadow: inset -2px 0 0 #cbd5e1; padding:7px 10px; font-weight:600; color:#0D1B3D; max-width:190px; overflow:hidden; text-overflow:ellipsis;" title="{{ $row['nama_wilayah'] }}">
                                {{ $row['nama_wilayah'] }}
                            </td>

                            {{-- Jml RT --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center; color:#475569; font-weight:500;">
                                {{ $row['jumlah_rt'] > 0 ? $row['jumlah_rt'] : '—' }}
                            </td>

                            {{-- Est. DPT --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:right; font-weight:600; color:#0D1B3D;">
                                {{ $row['estimasi_dpt'] > 0 ? number_format($row['estimasi_dpt'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- Korwe --}}
                            <td style="position:relative; background:{{ $row['korwe_nama'] ? '#f0fdf4' : $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                                    @if($row['korwe_nama'])
                                        <span style="color:#122B5A; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:75px;" title="{{ $row['korwe_nama'] }}">{{ $row['korwe_nama'] }}</span>
                                    @else
                                        <span style="color:#475569; font-size:0.85rem; font-style:italic;">-</span>
                                    @endif
                                    
                                    {{-- Edit Icon --}}
                                    <button wire:click="openKorweModal('{{ $row['nomor_rw'] }}')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Data Korwe">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                            {{-- Afiliasi RW --}}
                            <td style="position:relative; background:{{ match($row['afiliasi']) { 'UNO' => '#dcfce7', 'CALON LAIN' => '#fee2e2', 'NETRAL' => '#f1f5f9', default => $bg } }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['afiliasi'] === 'UNO')
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        <span style="color:#166534; font-weight:800; font-size:0.85rem;">UNO</span>
                                        @if($row['afiliasi_pilkades_bukti'])
                                            <a href="{{ Storage::url($row['afiliasi_pilkades_bukti']) }}" target="_blank" style="color:#15803d; text-decoration:none; position:relative; z-index:10;" title="Lihat Bukti">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($row['afiliasi'] === 'CALON LAIN')
                                    <span style="display:inline-block; color:#b91c1c; font-size:0.9rem; font-weight:700;" title="Dukungan: {{ $row['calon_lain'] }}">{{ $row['calon_lain'] ?: 'LAINNYA' }}</span>
                                @elseif($row['afiliasi'] === 'NETRAL')
                                    <span style="display:inline-block; color:#475569; font-weight:700; font-size:0.85rem;">NETRAL</span>
                                @else
                                    <span style="display:inline-block; color:#475569; font-style:italic; font-size:1rem;">-</span>
                                @endif
                                
                                {{-- Edit Icon --}}
                                <button wire:click="openAfiliasiModal('{{ $row['nomor_rw'] }}')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </td>

                            {{-- Afiliasi RT --}}
                            <td style="position:relative; background:{{ $row['jumlah_rt'] > 0 && $row['afiliasi_rt_terisi'] === $row['jumlah_rt'] ? '#dcfce7' : ($row['afiliasi_rt_terisi'] > 0 ? $bg : $bg) }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['jumlah_rt'] > 0)
                                    <span style="font-size:0.85rem; font-weight:600; color:{{ $row['afiliasi_rt_terisi'] === $row['jumlah_rt'] ? '#166534' : '#64748b' }};">{{ $row['afiliasi_rt_terisi'] }} / {{ $row['jumlah_rt'] }}</span>
                                    <button wire:click="openRtModal('{{ $row['nomor_rw'] }}', {{ $row['jumlah_rt'] }})" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi RT">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                @else
                                    <span style="font-size:1rem; color:#475569; font-style:italic;">-</span>
                                @endif
                            </td>

                            {{-- Afiliasi PKK --}}
                            <td style="position:relative; background:{{ match($row['afiliasi_pkk']) { 'UNO' => '#dcfce7', 'CALON LAIN' => '#fee2e2', 'NETRAL' => '#f1f5f9', default => $bg } }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['afiliasi_pkk'] === 'CALON LAIN')
                                    <span style="display:inline-block; color:#b91c1c; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:60px;" title="{{ $row['afiliasi_pkk_calon_lain'] }}">{{ $row['afiliasi_pkk_calon_lain'] ?: 'LAINNYA' }}</span>
                                @elseif($row['afiliasi_pkk'] === 'UNO')
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        <span style="color:#166534; font-weight:800; font-size:0.85rem;">UNO</span>
                                        @if($row['afiliasi_pkk_bukti'])
                                            <a href="{{ Storage::url($row['afiliasi_pkk_bukti']) }}" target="_blank" style="color:#15803d; text-decoration:none; position:relative; z-index:10;" title="Lihat Bukti">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($row['afiliasi_pkk'] === 'NETRAL')
                                    <span style="display:inline-block; color:#475569; font-weight:700; font-size:0.85rem;">NETRAL</span>
                                @else
                                    <span style="display:inline-block; color:#475569; font-size:0.85rem;">-</span>
                                @endif
                                <button wire:click="openEditFieldModal('{{ $row['nomor_rw'] }}', 'afiliasi_pkk', 'Afiliasi PKK')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi PKK">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </td>

                            {{-- Afiliasi Karang Taruna --}}
                            <td style="position:relative; background:{{ match($row['afiliasi_karang_taruna']) { 'UNO' => '#dcfce7', 'CALON LAIN' => '#fee2e2', 'NETRAL' => '#f1f5f9', default => $bg } }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['afiliasi_karang_taruna'] === 'CALON LAIN')
                                    <span style="display:inline-block; color:#b91c1c; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70px;" title="{{ $row['afiliasi_karang_taruna_calon_lain'] }}">{{ $row['afiliasi_karang_taruna_calon_lain'] ?: 'LAINNYA' }}</span>
                                @elseif($row['afiliasi_karang_taruna'] === 'UNO')
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        <span style="color:#166534; font-weight:800; font-size:0.85rem;">UNO</span>
                                        @if($row['afiliasi_karang_taruna_bukti'])
                                            <a href="{{ Storage::url($row['afiliasi_karang_taruna_bukti']) }}" target="_blank" style="color:#15803d; text-decoration:none; position:relative; z-index:10;" title="Lihat Bukti">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($row['afiliasi_karang_taruna'] === 'NETRAL')
                                    <span style="display:inline-block; color:#475569; font-weight:700; font-size:0.85rem;">NETRAL</span>
                                @else
                                    <span style="display:inline-block; color:#475569; font-size:0.85rem;">-</span>
                                @endif
                                <button wire:click="openEditFieldModal('{{ $row['nomor_rw'] }}', 'afiliasi_karang_taruna', 'Afiliasi Karang Taruna')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi Karang Taruna">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </td>

                            {{-- Afiliasi DKM --}}
                            <td style="position:relative; background:{{ match($row['afiliasi_dkm']) { 'UNO' => '#dcfce7', 'CALON LAIN' => '#fee2e2', 'NETRAL' => '#f1f5f9', default => $bg } }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 8px; text-align:center;">
                                @if($row['afiliasi_dkm'] === 'CALON LAIN')
                                    <span style="display:inline-block; color:#b91c1c; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:60px;" title="{{ $row['afiliasi_dkm_calon_lain'] }}">{{ $row['afiliasi_dkm_calon_lain'] ?: 'LAINNYA' }}</span>
                                @elseif($row['afiliasi_dkm'] === 'UNO')
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        <span style="color:#166534; font-weight:800; font-size:0.85rem;">UNO</span>
                                        @if($row['afiliasi_dkm_bukti'])
                                            <a href="{{ Storage::url($row['afiliasi_dkm_bukti']) }}" target="_blank" style="color:#15803d; text-decoration:none; position:relative; z-index:10;" title="Lihat Bukti">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($row['afiliasi_dkm'] === 'NETRAL')
                                    <span style="display:inline-block; color:#475569; font-weight:700; font-size:0.85rem;">NETRAL</span>
                                @else
                                    <span style="display:inline-block; color:#475569; font-size:0.85rem;">-</span>
                                @endif
                                <button wire:click="openEditFieldModal('{{ $row['nomor_rw'] }}', 'afiliasi_dkm', 'Afiliasi DKM')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi DKM">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </td>

                            {{-- Afiliasi Tokoh --}}
                            <td style="position:relative; background:{{ match($row['afiliasi_tokoh']) { 'UNO' => '#dcfce7', 'CALON LAIN' => '#fee2e2', 'NETRAL' => '#f1f5f9', default => $bg } }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 8px; text-align:center;">
                                @if($row['afiliasi_tokoh'] === 'CALON LAIN')
                                    <span style="display:inline-block; color:#b91c1c; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:60px;" title="{{ $row['afiliasi_tokoh_calon_lain'] }}">{{ $row['afiliasi_tokoh_calon_lain'] ?: 'LAINNYA' }}</span>
                                @elseif($row['afiliasi_tokoh'] === 'UNO')
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        <span style="color:#166534; font-weight:800; font-size:0.85rem;">UNO</span>
                                        @if($row['afiliasi_tokoh_bukti'])
                                            <a href="{{ Storage::url($row['afiliasi_tokoh_bukti']) }}" target="_blank" style="color:#15803d; text-decoration:none; position:relative; z-index:10;" title="Lihat Bukti">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($row['afiliasi_tokoh'] === 'NETRAL')
                                    <span style="display:inline-block; color:#475569; font-weight:700; font-size:0.85rem;">NETRAL</span>
                                @else
                                    <span style="display:inline-block; color:#475569; font-size:0.85rem;">-</span>
                                @endif
                                <button wire:click="openEditFieldModal('{{ $row['nomor_rw'] }}', 'afiliasi_tokoh', 'Afiliasi Tokoh')" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:rgba(255,255,255,0.95); border:1px solid #cbd5e1; border-radius:50%; padding:6px; cursor:pointer; color:#FF6A00; box-shadow:0 2px 5px rgba(0,0,0,0.15); outline:none; transition:all 0.2s;" onmouseover="this.style.background='#fff'; this.style.transform='translate(-50%, -50%) scale(1.15)'" onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.transform='translate(-50%, -50%) scale(1)'" title="Ubah Afiliasi Tokoh">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </td>

                            {{-- Suara PKS --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:700; color:{{ $row['suara_pks'] > 0 ? '#92400e' : '#94a3b8' }}; font-size:0.95rem;">
                                {{ $row['suara_pks'] > 0 ? number_format($row['suara_pks'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- Suara PAN --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:700; color:{{ $row['suara_pan'] > 0 ? '#1e40af' : '#94a3b8' }}; font-size:0.95rem;">
                                {{ $row['suara_pan'] > 0 ? number_format($row['suara_pan'], 0, ',', '.') : '—' }}
                            </td>

                            {{-- PKS + PAN --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:7px 10px; text-align:right; font-weight:800; color:{{ $pksSum > 0 ? '#122B5A' : '#94a3b8' }}; font-size:0.82rem;">
                                {{ $pksSum > 0 ? number_format($pksSum, 0, ',', '.') : '—' }}
                            </td>

                            {{-- Juara 1 --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:7px 6px; text-align:center;">
                                @if($row['juara_1'] === 'PKS')
                                    <span style="background:#fef3c7; border:1px solid #fbbf24; color:#92400e; font-size:1rem; font-weight:800; padding:2px 6px; border-radius:4px;">PKS #1</span>
                                @elseif($row['juara_1'] === 'PAN')
                                    <span style="background:#eff6ff; border:1px solid #93c5fd; color:#1e40af; font-size:1rem; font-weight:800; padding:2px 6px; border-radius:4px;">PAN #1</span>
                                @elseif($row['juara_1'] !== '')
                                    <span style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; font-size:0.58rem; font-weight:700; padding:2px 4px; border-radius:4px; text-transform:uppercase;">{{ $row['juara_1'] }} #1</span>
                                @else
                                    <span style="color:#475569; font-size:0.85rem;">-</span>
                                @endif
                            </td>

                            {{-- UNO Score --}}
                            <td style="background:{{ $row['uno_score']['color'] }}; border-bottom:1px solid #e2e8f0; border-right:1px solid #e2e8f0; padding:6px; text-align:center;">
                                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%;">
                                    <span style="color:{{ $row['uno_score']['text_color'] }}; font-size:1rem; font-weight:800;">
                                        {{ $row['uno_score']['score'] }}%
                                    </span>
                                    <span style="color:{{ $row['uno_score']['text_color'] }}; font-size:0.55rem; font-weight:600; text-transform:uppercase; margin-top:2px; text-align:center; line-height:1.1;">
                                        {{ $row['uno_score']['badge'] }}<br>
                                        <span style="opacity:0.8;">({{ $row['uno_score']['filled'] }}/5)</span>
                                    </span>
                                </div>
                            </td>

                            {{-- 3 Partai Terkuat --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; border-right:2px solid #cbd5e1; padding:6px 10px; vertical-align:top;">
                                @if(count($row['top3_partai']) > 0)
                                    @foreach(array_slice($row['top3_partai'], 0, 3) as $pi => $p)
                                        <div style="display:flex; align-items:center; gap:5px; {{ $pi > 0 ? 'margin-top:3px;' : '' }}">
                                            <span style="background:{{ $pi === 0 ? '#fef3c7' : ($pi === 1 ? '#f1f5f9' : '#f8fafc') }}; border:1px solid {{ $pi === 0 ? '#fbbf24' : '#e2e8f0' }}; color:{{ $pi === 0 ? '#92400e' : '#475569' }}; font-size:1rem; font-weight:800; width:14px; height:14px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $pi+1 }}</span>
                                            <span style="font-size:0.85rem; color:#0D1B3D; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;" title="{{ $p['party_name'] ?? '' }}">{{ $p['party_name'] ?? '-' }}</span>
                                            <span style="margin-left:auto; font-size:0.85rem; font-weight:700; color:#64748b; flex-shrink:0;">{{ number_format($p['votes'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="color:#475569; font-size:0.85rem;">Belum ada data</span>
                                @endif
                            </td>

                            {{-- 3 Caleg Pemenang --}}
                            <td style="background:{{ $bg }}; border-bottom:1px solid #e2e8f0; padding:6px 10px; vertical-align:top;">
                                @if(count($row['top3_caleg']) > 0)
                                    @foreach(array_slice($row['top3_caleg'], 0, 3) as $ci => $c)
                                        <div style="display:flex; align-items:center; gap:5px; {{ $ci > 0 ? 'margin-top:3px;' : '' }}">
                                            <span style="background:{{ $ci === 0 ? '#dbeafe' : '#f1f5f9' }}; border:1px solid {{ $ci === 0 ? '#93c5fd' : '#e2e8f0' }}; color:{{ $ci === 0 ? '#1e40af' : '#64748b' }}; font-size:1rem; font-weight:800; width:14px; height:14px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $ci+1 }}</span>
                                            <span style="font-size:0.85rem; color:#0D1B3D; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:145px;" title="{{ $c['name'] }} ({{ $c['party'] }})">{{ $c['name'] }}</span>
                                            <span style="margin-left:auto; font-size:1rem; color:#475569; flex-shrink:0; white-space:nowrap;">{{ number_format($c['votes'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span style="color:#475569; font-size:0.85rem;">Belum ada data</span>
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
                        <td style="position:sticky; left:0; z-index:5; background:#e2e8f0; border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px 6px; text-align:center; font-size:0.85rem; color:#475569;">TOTAL</td>
                        <td style="position:sticky; left:52px; z-index:5; background:#e2e8f0; border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px 10px; font-size:0.85rem; color:#64748b;">32 RW</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#374151;">—</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#0D1B3D; font-size:0.95rem;">{{ number_format($totalDpt, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#122B5A;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#475569;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#92400e; font-size:1rem;">{{ number_format($totalPks, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#1e40af; font-size:1rem;">{{ number_format($totalPan, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:right; color:#122B5A; font-size:0.85rem;">{{ number_format($totalSum, 0, ',', '.') }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px; text-align:center; color:#475569; font-size:0.85rem;">-</td>
                        <td style="border-top:2px solid #94a3b8; border-right:1px solid #cbd5e1; padding:8px; text-align:center; color:#22c55e; font-size:0.85rem; font-weight:800;">✓ {{ array_sum(array_column($rwData, 'dukungan_uno')) }}</td>
                        <td style="border-top:2px solid #94a3b8; border-right:2px solid #94a3b8; padding:8px;"></td>
                        <td style="border-top:2px solid #94a3b8; padding:8px;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- STATUS BAR --}}
        <div style="flex:none; background:#0D1B3D; border-top:1px solid #334155; padding:5px 16px; display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; color:#64748b;">
            <span>
                Data level RW ini telah disesuaikan secara manual dengan data lapangan yang tervalidasi.
            </span>
            <div style="display:flex; gap:14px; align-items:center;">
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#fef3c7;border:1px solid #fbbf24;border-radius:2px;display:inline-block;"></span> Suara PKS</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#eff6ff;border:1px solid #93c5fd;border-radius:2px;display:inline-block;"></span> Suara PAN</span>
                <span style="display:flex; align-items:center; gap:4px;"><span style="width:10px;height:10px;background:#f0fdf4;border:1px solid #FFC107;border-radius:2px;display:inline-block;"></span> PKS+PAN / Infrastruktur</span>
            </div>
        </div>

    @endif

    {{-- Modal Afiliasi & Korwe Terpisah --}}

    {{-- Modal Korwe Khusus UNO --}}
    @if($showKorweModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#122B5A; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Profil RW {{ $editRwId }}</h3>
                    <button wire:click="$set('showKorweModal', false)" style="background:none; border:none; color:#bbf7d0; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:0.9rem; font-weight:600; color:#122B5A; margin-bottom:6px;">Nama Korwe</label>
                        <input type="text" wire:model="formKorweNama" placeholder="Ketik nama koordinator..." style="width:100%; padding:8px; border:1px solid #FFC107; border-radius:4px; font-size:1rem; color:#122B5A; outline:none; box-sizing:border-box;">
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showKorweModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveKorwe" style="padding:6px 12px; background:#FF6A00; border:none; color:#fff; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Afiliasi --}}
    @if($showAfiliasiModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#0D1B3D; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Status Afiliasi RW {{ $editRwId }}</h3>
                    <button wire:click="$set('showAfiliasiModal', false)" style="background:none; border:none; color:#475569; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:0.9rem; font-weight:600; color:#475569; margin-bottom:6px;">Status Dukungan (Afiliasi)</label>
                        <select wire:model.live="formAfiliasi" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:4px; font-size:1rem; color:#0D1B3D; outline:none;">
                            <option value="BELUM DIKETAHUI">BELUM DIKETAHUI</option>
                            <option value="NETRAL">NETRAL</option>
                            <option value="UNO">UNO</option>
                            <option value="CALON LAIN">CALON LAIN</option>
                        </select>
                    </div>
                    
                    @if($formAfiliasi === 'CALON LAIN')
                        <div style="margin-bottom:12px; background:#fef2f2; border:1px solid #fecaca; padding:10px; border-radius:6px;">
                            <label style="display:block; font-size:0.9rem; font-weight:600; color:#991b1b; margin-bottom:6px;">Nama Calon Lain yang Didukung</label>
                            <select wire:model="formCalonLain" style="width:100%; padding:8px; border:1px solid #fca5a5; border-radius:4px; font-size:1rem; color:#991b1b; outline:none; box-sizing:border-box;">
                                <option value="">Pilih Calon...</option>
                                <option value="UNO">UNO</option>
                                <option value="EMON">EMON</option>
                                <option value="RAHMAT">RAHMAT</option>
                                <option value="EKO">EKO</option>
                                <option value="MASPRI">MASPRI</option>
                                <option value="TEGUH">TEGUH</option>
                            </select>
                        </div>
                    @elseif($formAfiliasi === 'UNO')
                        <div style="margin-bottom:12px; background:#f0fdf4; border:1px solid #bbf7d0; padding:10px; border-radius:6px;">
                            <label style="display:block; font-size:0.9rem; font-weight:600; color:#166534; margin-bottom:6px;">Upload Bukti Dokumen</label>
                            <input type="file" wire:model="formBukti" accept=".pdf,.doc,.docx,image/*" style="width:100%; font-size:1rem; color:#166534;">
                            <div wire:loading wire:target="formBukti" style="font-size:0.85rem; color:#15803d; margin-top:4px;">Mengunggah...</div>
                        </div>
                    @endif
                    
                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showAfiliasiModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveAfiliasi" style="padding:6px 12px; background:#FF6A00; border:none; color:#fff; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Generic Edit Modal --}}
    @if($showEditModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:340px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif;">
                <div style="background:#122B5A; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Ubah {{ $modalTitle }} (RW {{ ltrim($editRwId, '0') }})</h3>
                    <button wire:click="$set('showEditModal', false)" style="background:none; border:none; color:#bbf7d0; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                <div style="padding:16px;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:0.9rem; font-weight:600; color:#122B5A; margin-bottom:6px;">{{ $modalTitle }}</label>
                        @if($editField === 'sosial_media')
                            <input type="text" wire:model="formFieldValue" placeholder="Ketik {{ strtolower($modalTitle) }}..." style="width:100%; padding:8px; border:1px solid #FFC107; border-radius:4px; font-size:1rem; color:#122B5A; outline:none; box-sizing:border-box;">
                        @else
                            <select wire:model.live="formFieldValue" style="width:100%; padding:8px; border:1px solid #FFC107; border-radius:4px; font-size:1rem; color:#122B5A; outline:none; box-sizing:border-box;">
                                <option value="BELUM DIKETAHUI">BELUM DIKETAHUI</option>
                                <option value="NETRAL">NETRAL</option>
                                <option value="UNO">UNO</option>
                                <option value="CALON LAIN">CALON LAIN</option>
                            </select>

                            @if($formFieldValue === 'CALON LAIN')
                                <div style="margin-top:12px; background:#fef2f2; border:1px solid #fecaca; padding:10px; border-radius:6px;">
                                    <label style="display:block; font-size:0.9rem; font-weight:600; color:#991b1b; margin-bottom:6px;">Nama Calon Lain</label>
                                    <select wire:model="formFieldValueCalonLain" style="width:100%; padding:8px; border:1px solid #fca5a5; border-radius:4px; font-size:1rem; color:#991b1b; outline:none; box-sizing:border-box;">
                                        <option value="">Pilih Calon...</option>
                                        <option value="EMON">EMON</option>
                                        <option value="RAHMAT">RAHMAT</option>
                                        <option value="EKO">EKO</option>
                                        <option value="MASPRI">MASPRI</option>
                                        <option value="TEGUH">TEGUH</option>
                                    </select>
                                </div>
                            @elseif($formFieldValue === 'UNO')
                                <div style="margin-top:12px; background:#f0fdf4; border:1px solid #bbf7d0; padding:10px; border-radius:6px;">
                                    <label style="display:block; font-size:0.9rem; font-weight:600; color:#166534; margin-bottom:6px;">Upload Bukti Dokumen</label>
                                    <input type="file" wire:model="formBukti" accept=".pdf,.doc,.docx,image/*" style="width:100%; font-size:1rem; color:#166534;">
                                    <div wire:loading wire:target="formBukti" style="font-size:0.85rem; color:#15803d; margin-top:4px;">Mengunggah...</div>
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
                        <button wire:click="$set('showEditModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Batal</button>
                        <button wire:click="saveEditField" style="padding:6px 12px; background:#FF6A00; border:none; color:#fff; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Pemetaan RT --}}
    @if($showRtModal)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
            <div style="background:#fff; width:480px; max-height:85vh; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden; font-family:system-ui,-apple-system,sans-serif; display:flex; flex-direction:column;">
                <div style="background:#122B5A; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
                    <h3 style="margin:0; color:#fff; font-size:0.9rem; font-weight:600;">Pemetaan RT (RW {{ ltrim($editRwId, '0') }})</h3>
                    <button wire:click="$set('showRtModal', false)" style="background:none; border:none; color:#bbf7d0; cursor:pointer; font-size:1.2rem; line-height:1;">&times;</button>
                </div>
                
                <div style="padding:16px; overflow-y:auto; flex-grow:1;">
                    @if($rtCount > 0)
                        <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                            <thead>
                                <tr>
                                    <th style="padding:6px; border-bottom:1px solid #cbd5e1; text-align:left; color:#475569;">RT</th>
                                    <th style="padding:6px; border-bottom:1px solid #cbd5e1; text-align:left; color:#475569;">Afiliasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formAfiliasiRtData as $index => $rtItem)
                                    <tr>
                                        <td style="padding:8px 6px; border-bottom:1px solid #e2e8f0; font-weight:600; color:#0f172a; width:50px;">
                                            {{ $rtItem['rt'] }}
                                        </td>
                                        <td style="padding:8px 6px; border-bottom:1px solid #e2e8f0;">
                                            <select wire:model.live="formAfiliasiRtData.{{ $index }}.afiliasi" style="width:100%; padding:6px; border:1px solid #FFC107; border-radius:4px; font-size:0.9rem; color:#122B5A; outline:none; box-sizing:border-box;">
                                                <option value="BELUM DIKETAHUI">BELUM DIKETAHUI</option>
                                                <option value="NETRAL">NETRAL</option>
                                                <option value="UNO">UNO</option>
                                                <option value="CALON LAIN">CALON LAIN</option>
                                            </select>
                                            
                                            @if(($formAfiliasiRtData[$index]['afiliasi'] ?? '') === 'CALON LAIN')
                                                <div style="margin-top:6px;">
                                                    <select wire:model="formAfiliasiRtData.{{ $index }}.calon_lain" style="width:100%; padding:6px; border:1px solid #fca5a5; border-radius:4px; font-size:0.9rem; color:#991b1b; outline:none; box-sizing:border-box;">
                                                        <option value="">Pilih Calon...</option>
                                                        <option value="EMON">EMON</option>
                                                        <option value="RAHMAT">RAHMAT</option>
                                                        <option value="EKO">EKO</option>
                                                        <option value="MASPRI">MASPRI</option>
                                                        <option value="TEGUH">TEGUH</option>
                                                    </select>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="text-align:center; padding:20px; color:#64748b; font-size:1rem; font-style:italic;">
                            Data jumlah RT belum diatur untuk RW ini.
                        </div>
                    @endif
                </div>
                
                <div style="padding:16px; display:flex; justify-content:flex-end; gap:8px; border-top:1px solid #e2e8f0; flex-shrink:0;">
                    <button wire:click="$set('showRtModal', false)" style="padding:6px 12px; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Batal</button>
                    @if($rtCount > 0)
                        <button wire:click="saveRtData" style="padding:6px 12px; background:#FF6A00; border:none; color:#fff; border-radius:4px; font-size:0.9rem; font-weight:600; cursor:pointer;">Simpan RT</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
    @else
        <div style="display:flex; align-items:center; justify-content:center; min-height:100vh; background:linear-gradient(135deg, #0D1B3D, #122B5A); width:100%;">
            <div style="background:#122B5A; padding:32px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.5); width:100%; max-width:400px; border:1px solid rgba(255,193,7,0.3);">
                <div style="text-align:center; margin-bottom:24px;">
                    <div style="background:#FF6A00; width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; box-shadow:0 4px 10px rgba(255,106,0,0.4);">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:32px; height:32px; color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 style="margin:0 0 8px; font-size:1.5rem; color:#FFFFFF; font-weight:700;">Login Khusus</h2>
                    <p style="margin:0; color:#FFC107; font-size:0.875rem;">Pemetaan Pilkades Karangsatria</p>
                </div>
                
                <form wire:submit.prevent="loginPilkades">
                    @if($loginError)
                        <div style="background:rgba(239,68,68,0.1); border-left:4px solid #ef4444; color:#fca5a5; padding:12px; border-radius:4px; font-size:0.85rem; margin-bottom:16px;">
                            {{ $loginError }}
                        </div>
                    @endif
                    
                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:0.875rem; font-weight:600; color:#e2e8f0; margin-bottom:6px;">Username</label>
                        <input type="text" wire:model="loginUsername" style="width:100%; background:rgba(255,255,255,0.1); color:#fff; padding:10px 12px; border:1px solid rgba(255,255,255,0.2); border-radius:6px; font-size:0.95rem; outline:none; box-sizing:border-box; transition:border-color 0.2s;" onfocus="this.style.borderColor='#FFC107'" onblur="this.style.borderColor='rgba(255,255,255,0.2)'" required>
                    </div>
                    
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:0.875rem; font-weight:600; color:#e2e8f0; margin-bottom:6px;">Password</label>
                        <input type="password" wire:model="loginPassword" style="width:100%; background:rgba(255,255,255,0.1); color:#fff; padding:10px 12px; border:1px solid rgba(255,255,255,0.2); border-radius:6px; font-size:0.95rem; outline:none; box-sizing:border-box; transition:border-color 0.2s;" onfocus="this.style.borderColor='#FFC107'" onblur="this.style.borderColor='rgba(255,255,255,0.2)'" required>
                    </div>
                    
                    <button type="submit" style="width:100%; background:#FF6A00; color:#fff; border:none; padding:12px; border-radius:6px; font-size:1rem; font-weight:700; cursor:pointer; transition:background 0.2s; box-shadow:0 4px 6px rgba(255,106,0,0.3);" onmouseover="this.style.background='#CC5500'" onmouseout="this.style.background='#FF6A00'">
                        Masuk
                    </button>
                    <div wire:loading wire:target="loginPilkades" style="text-align:center; width:100%; margin-top:12px; font-size:0.85rem; color:#FFC107; font-weight:600;">
                        Memeriksa kredensial...
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
