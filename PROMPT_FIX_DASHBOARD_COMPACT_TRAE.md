# Prompt Trae — Fix Dashboard Admin (Compact, 1 Screen)

```
Fix dashboard admin agar SEMUA data terlihat dalam 1 screen tanpa scroll. Compact, padat, langsung actionable. Langsung edit, JANGAN test, JANGAN tanya.

File: resources/views/livewire/dashboard.blade.php

MASALAH SAAT INI:
1. Header bar gelap di atas buang ruang — hapus
2. Card KPI terlalu besar, padding terlalu banyak
3. Section "Snapshot Operasional" dan "Trend Bulanan" makan banyak ruang
4. Leaderboard terlalu luas — kolom BAR tidak perlu
5. Status wilayah terlalu besar — bisa lebih compact
6. Terlalu banyak whitespace antar section
7. Tidak ada tabel Progress per Dapil (matrix KORWE/Sisir/Profil/UPA/RKI/KSN)

TARGET: Semua konten muat di viewport 1080px height (1920x1080 monitor standar). User buka dashboard → langsung lihat semua tanpa scroll.

=== LAYOUT BARU (COMPACT) ===

HAPUS: header bar gelap di atas (yang ada logo + monitoring text + login). Tidak perlu — sidebar sudah ada info user.

BARIS 1: Title + Filter (1 baris, compact)
```html
<div class="flex items-center justify-between mb-2">
    <div>
        <h1 class="text-lg font-medium leading-tight">Dashboard kontrol</h1>
        <p class="text-xs text-zinc-400">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="flex gap-2 items-center">
        <select wire:model.live="selectedDapil" class="h-8 rounded-md border border-zinc-300 dark:border-zinc-600 text-xs px-2 bg-white dark:bg-zinc-800">
            <option value="">Semua dapil</option>
            @foreach($this->dapilOptions as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
        </select>
        <select wire:model.live="selectedBulan" class="h-8 rounded-md border border-zinc-300 dark:border-zinc-600 text-xs px-2 bg-white dark:bg-zinc-800">
            @for($m=1;$m<=12;$m++)<option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('M') }}</option>@endfor
        </select>
        <select wire:model.live="selectedTahun" class="h-8 rounded-md border border-zinc-300 dark:border-zinc-600 text-xs px-2 bg-white dark:bg-zinc-800">
            <option>2026</option><option>2027</option><option>2028</option><option>2029</option>
        </select>
    </div>
</div>
```

BARIS 2: KPI Cards — grid 7 kolom, SUPER compact (semua KPI utama + infra dalam 1 baris)
```html
<div class="grid grid-cols-7 gap-2 mb-2">
    {{-- KORWE --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-0.5">KORWE</div>
        <div class="flex items-baseline gap-1">
            <span class="text-lg font-medium">{{ $kpi['korweTerbentuk'] }}</span>
            <span class="text-[10px] text-zinc-400">/ {{ number_format($kpi['targetKorwe']) }}</span>
        </div>
        <div class="h-1 bg-zinc-100 dark:bg-zinc-700 rounded-full mt-1 overflow-hidden">
            <div class="h-full bg-orange-500 rounded-full" style="width:{{ $kpi['targetKorwe'] > 0 ? round($kpi['korweTerbentuk']/$kpi['targetKorwe']*100) : 0 }}%"></div>
        </div>
    </div>
    {{-- KORTE --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-0.5">KORTE</div>
        <div class="flex items-baseline gap-1">
            <span class="text-lg font-medium">{{ $kpi['korteTerbentuk'] }}</span>
            <span class="text-[10px] text-zinc-400">/ {{ number_format($kpi['targetKorte']) }}</span>
        </div>
        <div class="h-1 bg-zinc-100 dark:bg-zinc-700 rounded-full mt-1 overflow-hidden">
            <div class="h-full bg-orange-500 rounded-full" style="width:{{ $kpi['targetKorte'] > 0 ? round($kpi['korteTerbentuk']/$kpi['targetKorte']*100) : 0 }}%"></div>
        </div>
    </div>
    {{-- Sisir RW — accent --}}
    <div class="rounded-lg p-2.5" style="background:linear-gradient(135deg,#fe5000,#d94400);color:white;">
        <div class="text-[10px] mb-0.5" style="opacity:.8;">Sisir RW</div>
        <div class="flex items-baseline gap-1">
            <span class="text-lg font-medium">{{ $kpi['rwTersisir'] }}</span>
            <span class="text-[10px]" style="opacity:.7;">/ {{ number_format($kpi['totalRw']) }}</span>
        </div>
        <div class="text-[10px] mt-0.5" style="opacity:.75;">{{ $kpi['kegiatanBulanIni'] }} keg · {{ number_format($kpi['wargaTerjangkau']) }} warga</div>
    </div>
    {{-- Profil --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-0.5">Profil RW</div>
        <div class="flex items-baseline gap-1">
            <span class="text-lg font-medium">{{ $kpi['profilTerisi'] }}</span>
            <span class="text-[10px] text-zinc-400">/ {{ number_format($kpi['totalRw']) }}</span>
        </div>
        <div class="h-1 bg-zinc-100 dark:bg-zinc-700 rounded-full mt-1 overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width:{{ $kpi['totalRw'] > 0 ? round($kpi['profilTerisi']/$kpi['totalRw']*100) : 0 }}%"></div>
        </div>
    </div>
    {{-- UPA + RKI + KSN (gabung 1 card) --}}
    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-1">Infrastruktur</div>
        <div class="grid grid-cols-3 gap-1 text-center">
            <div>
                <div class="text-sm font-medium">{{ $kpi['upaRw'] }}</div>
                <div class="text-[8px] text-zinc-400">UPA</div>
            </div>
            <div>
                <div class="text-sm font-medium">{{ $kpi['rki'] }}</div>
                <div class="text-[8px] text-zinc-400">RKI</div>
            </div>
            <div>
                <div class="text-sm font-medium">{{ $kpi['ksn'] }}</div>
                <div class="text-[8px] text-zinc-400">KSN</div>
            </div>
        </div>
    </div>
    {{-- Event --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-0.5">Event</div>
        <div class="flex items-baseline gap-1">
            <span class="text-lg font-medium">{{ $kpi['eventAktif'] }}</span>
            <span class="text-[10px] text-zinc-400">aktif</span>
        </div>
        <div class="text-[10px] text-amber-600 mt-0.5">{{ $kpi['eventMenunggu'] }} menunggu</div>
    </div>
    {{-- Member --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-2.5">
        <div class="text-[10px] text-zinc-400 mb-0.5">Member</div>
        <div class="text-lg font-medium" style="color:#7c3aed;">{{ number_format($kpi['totalMember']) }}</div>
        <div class="text-[10px] text-zinc-400 mt-0.5">+{{ $kpi['memberBulanIni'] }} bulan ini</div>
    </div>
</div>
```

BARIS 3: Alerts (inline, 1 baris per alert, sangat compact)
```html
@if(count($this->alerts) > 0)
<div class="space-y-1 mb-2">
    @foreach($this->alerts as $alert)
        @php
            $c = ['warning'=>['bg'=>'#fffbeb','bc'=>'#fde68a','ic'=>'#d97706','tc'=>'#92400e'],
                  'danger'=>['bg'=>'#fef2f2','bc'=>'#fecaca','ic'=>'#dc2626','tc'=>'#991b1b'],
                  'info'=>['bg'=>'#eff6ff','bc'=>'#bfdbfe','ic'=>'#2563eb','tc'=>'#1e40af']][$alert['type']];
        @endphp
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[11px]" style="background:{{ $c['bg'] }};border:0.5px solid {{ $c['bc'] }};color:{{ $c['tc'] }};">
            <i class="ti ti-{{ $alert['icon'] }}" style="font-size:13px;color:{{ $c['ic'] }};" aria-hidden="true"></i>
            <span class="flex-1">{!! $alert['text'] !!}</span>
            <a href="{{ $alert['link'] }}" class="text-[10px] font-medium" style="color:#fe5000;" wire:navigate>{{ $alert['link_text'] }} →</a>
        </div>
    @endforeach
</div>
@endif
```

BARIS 4: AREA UTAMA — grid 3 kolom (progress dapil BESAR di kiri, status+trend di tengah, activity+event di kanan)

```html
<div class="grid grid-cols-12 gap-2">

    {{-- KOLOM KIRI: Progress Dapil (span 5) --}}
    <div class="col-span-5 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
        <div class="text-[10px] text-orange-600 uppercase tracking-wider font-medium mb-1">Progress per dapil</div>

        <table class="w-full text-[11px]">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="text-left py-1 pr-1 text-[9px] text-zinc-400 uppercase font-medium">Dapil</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">KORWE</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">Sisir</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">Profil</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">UPA</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">RKI</th>
                    <th class="text-center py-1 px-1 text-[9px] text-zinc-400 uppercase font-medium">KSN</th>
                    <th class="text-right py-1 pl-1 text-[9px] text-zinc-400 uppercase font-medium">Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->leaderboard as $i => $dp)
                    <tr class="border-b border-zinc-100 dark:border-zinc-700/50 {{ $i === 0 ? 'bg-orange-50/50 dark:bg-orange-950/10' : '' }}">
                        <td class="py-1.5 pr-1 font-medium flex items-center gap-1">
                            @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @else <span class="text-zinc-300 text-[10px] w-4 inline-block">{{ $i+1 }}</span> @endif
                            <span class="text-[11px]">{{ str_replace('BEKASI ', '', $dp['dapil']) }}</span>
                        </td>
                        <td class="text-center py-1.5 px-1">
                            <span class="text-[10px] {{ $dp['korwe_pct'] > 0 ? 'text-orange-600 font-medium' : 'text-zinc-300' }}">{{ $dp['korwe_pct'] }}%</span>
                        </td>
                        <td class="text-center py-1.5 px-1">
                            <span class="text-[10px] {{ $dp['sisir_pct'] > 0 ? 'text-amber-600 font-medium' : 'text-zinc-300' }}">{{ $dp['sisir_pct'] }}%</span>
                        </td>
                        <td class="text-center py-1.5 px-1">
                            <span class="text-[10px] {{ $dp['profil_pct'] > 0 ? 'text-blue-600 font-medium' : 'text-zinc-300' }}">{{ $dp['profil_pct'] }}%</span>
                        </td>
                        <td class="text-center py-1.5 px-1 text-[10px]">{{ $dp['upa_rw'] }}</td>
                        <td class="text-center py-1.5 px-1 text-[10px]">{{ $dp['rki'] }}</td>
                        <td class="text-center py-1.5 px-1 text-[10px]">{{ $dp['ksn'] }}</td>
                        <td class="text-right py-1.5 pl-1">
                            <span class="text-[11px] font-medium {{ $i === 0 ? 'text-orange-600' : '' }}">
                                {{ number_format(($dp['korwe_pct']*0.4)+($dp['sisir_pct']*0.3)+($dp['profil_pct']*0.2)+(($dp['upa_rw']+$dp['rki']+$dp['ksn'])*0.1), 1) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- KOLOM TENGAH: Status Wilayah + Trend (span 3) --}}
    <div class="col-span-3 flex flex-col gap-2">
        {{-- Status Wilayah --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
            <div class="text-[10px] text-orange-600 uppercase tracking-wider font-medium mb-2">Status wilayah</div>
            <div class="space-y-1.5">
                @php
                    $statusItems = [
                        ['key'=>'JAGA KUAT','bg'=>'#dcfce7','text'=>'#14532d','bar'=>'#16a34a'],
                        ['key'=>'AMANKAN','bg'=>'#ecfccb','text'=>'#3f6212','bar'=>'#65a30d'],
                        ['key'=>'REBUT REALISTIS','bg'=>'#dbeafe','text'=>'#1e3a5f','bar'=>'#2563eb'],
                        ['key'=>'GARAP INTENSIF','bg'=>'#fff7f1','text'=>'#993c1d','bar'=>'#d97706'],
                        ['key'=>'ZONA BERAT','bg'=>'#fee2e2','text'=>'#991b1b','bar'=>'#b91c1c'],
                    ];
                    $totalDesa = max(array_sum($this->statusWilayah->toArray()), 1);
                @endphp
                @foreach($statusItems as $s)
                    @php $count = $this->statusWilayah[$s['key']] ?? 0; $pct = round($count / $totalDesa * 100); @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-[52px] text-[10px] font-medium truncate" style="color:{{ $s['text'] }};">{{ explode(' ', $s['key'])[0] }}</div>
                        <div class="flex-1 h-3 rounded-sm overflow-hidden" style="background:{{ $s['bg'] }};">
                            <div class="h-full rounded-sm" style="width:{{ $pct }}%;background:{{ $s['bar'] }};"></div>
                        </div>
                        <div class="w-6 text-[10px] text-right font-medium" style="color:{{ $s['text'] }};">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Trend --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 flex-1">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[10px] text-zinc-400 uppercase tracking-wider font-medium">Trend sisir RW</div>
                @php
                    $trendArr = $this->trend->toArray();
                    $prev = $trendArr[count($trendArr)-2]['count'] ?? 0;
                    $curr = $trendArr[count($trendArr)-1]['count'] ?? 0;
                    $change = $prev > 0 ? round(($curr - $prev) / $prev * 100) : ($curr > 0 ? 100 : 0);
                @endphp
                <span class="text-[10px] {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                    {{ $change >= 0 ? '↑' : '↓' }} {{ abs($change) }}%
                </span>
            </div>
            <div class="flex items-end gap-1" style="height:40px;">
                @foreach($this->trend as $t)
                    <div class="flex-1 flex flex-col items-center gap-1 justify-end h-full">
                        <div class="w-full rounded-sm" style="height:{{ max($t['pct'] * 0.35, 2) }}px;background:{{ $t['current'] ? '#fe5000' : '#fe500030' }};"></div>
                        <span class="text-[8px] {{ $t['current'] ? 'text-orange-600 font-medium' : 'text-zinc-400' }}">{{ $t['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Activity Feed + Event (span 4) --}}
    <div class="col-span-4 flex flex-col gap-2">
        {{-- Activity --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 flex-1">
            <div class="text-[10px] text-green-600 uppercase tracking-wider font-medium mb-2">Aktivitas terbaru</div>
            <div class="space-y-0">
                @foreach($this->timeline->take(5) as $item)
                    <div class="flex gap-2 py-1.5 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-700/50' : '' }}">
                        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" style="background:{{ $item['color'] }};"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[11px] font-medium truncate">{{ $item['title'] }}</div>
                            <div class="text-[10px] text-zinc-400 flex items-center gap-2">
                                <span>{{ $item['time']->diffForHumans() }}</span>
                                <span class="truncate">{{ $item['desc'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="#" class="block text-center text-[10px] text-orange-600 font-medium mt-1" wire:navigate>Semua aktivitas →</a>
        </div>

        {{-- Event Pipeline --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
            <div class="text-[10px] text-zinc-400 uppercase tracking-wider font-medium mb-2">Event</div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-center py-1.5 bg-zinc-50 dark:bg-zinc-700/30 rounded-md">
                    <div class="text-sm font-medium text-amber-600">{{ $kpi['eventMenunggu'] }}</div>
                    <div class="text-[9px] text-zinc-400">Menunggu</div>
                </div>
                <div class="text-center py-1.5 bg-zinc-50 dark:bg-zinc-700/30 rounded-md">
                    <div class="text-sm font-medium text-green-600">{{ $kpi['eventAktif'] }}</div>
                    <div class="text-[9px] text-zinc-400">Aktif</div>
                </div>
                <div class="text-center py-1.5 bg-zinc-50 dark:bg-zinc-700/30 rounded-md">
                    <div class="text-sm font-medium">{{ $kpi['eventSelesai'] }}</div>
                    <div class="text-[9px] text-zinc-400">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>
```

=== RINGKASAN PERUBAHAN ===

HAPUS:
- Header bar gelap (logo + monitoring text + login)
- Heading "Dashboard Admin" yang besar
- Section "Snapshot Operasional" terpisah (UPA/RKI/KSN digabung ke 1 card)
- Leaderboard sebagai section terpisah (digabung ke tabel progress dapil)

GABUNG:
- UPA + RKI + KSN → 1 card "Infrastruktur" (grid 3 kolom dalam 1 card)
- Progress Dapil + Leaderboard → 1 tabel (kolom: Dapil, KORWE%, Sisir%, Profil%, UPA, RKI, KSN, Skor)
- Semua KPI utama + infra dalam 1 baris (grid 7 kolom)

COMPACT:
- Card padding: p-2.5 (bukan p-4)
- Font: text-lg untuk angka besar (bukan text-2xl)
- Gap: gap-2 (bukan gap-3 atau gap-4)
- Section margin: mb-2 (bukan mb-4)
- Label: text-[10px] (bukan text-xs)
- Alert: py-1.5 (bukan py-2)
- Tabel row: py-1.5 (bukan py-2)
- Trend chart: height 40px (bukan 60px)
- Status bar: h-3 (bukan h-5)

LAYOUT:
- Baris 1: Title + filter (1 baris)
- Baris 2: 7 KPI cards
- Baris 3: Alerts (conditional, compact)
- Baris 4: Grid 12 kolom → Progress Dapil (span-5) + Status/Trend (span-3) + Activity/Event (span-4)

Total tinggi: ~600-650px → muat di monitor 1080p tanpa scroll.

Langsung ganti view. Jangan test.
```
