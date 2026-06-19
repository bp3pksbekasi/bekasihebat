# Prompt Antigravity — Redesign Dashboard: Tab System

Redesign view dashboard (`resources/views/livewire/dashboard.blade.php`) agar lebih fokus dan terorganisir dengan sistem tab. JANGAN ubah `app/Livewire/Dashboard.php` — semua computed properties sudah ada dan sudah benar. Hanya ganti VIEW-nya. Langsung kerjakan, JANGAN test, JANGAN tanya.

---

## DATA YANG TERSEDIA (dari computed properties yang sudah ada)

```
$this->kpi                  → array semua KPI (korweTerbentuk, targetKorwe, korwePct, korteTerbentuk,
                              targetKorte, kortePct, totalRw, rwTersisir, sisirPct, kegiatanBulanIni,
                              wargaTerjangkau, profilTerisi, profilPct, upaRw, upaRwFormal, rki, ksn,
                              relawan, penggalang, targetPenggalang, saksiTps, totalTps,
                              eventAktif, eventMenunggu, eventSelesai, totalKader, kaderBulanIni,
                              avgPopularitas, kontenBulanIni)

$this->alerts               → array of alerts (type, icon, text, link, link_text)
$this->dapilMap             → collection dapil (dapil, dapil_number, desa_total, total_rw,
                              korwe_pct, sisir_pct, profil_pct, score, active, image)
$this->dapilProgress        → collection per dapil dengan score
$this->kecamatanMap         → collection kecamatan (aktif saat selectedDapil ada)
$this->trend                → array trend sisir RW (months[], change_pct, current, previous)
$this->infraTrends          → collection 5 trend (Korwe, UPA RW, RKI, KSN, Relawan)
$this->operationalHighlights → array 4 highlight (DPC aktif, Kader produktif, Kegiatan, KORWE baru)
$this->statusWilayah        → collection 5 status (JAGA KUAT, AMANKAN, REBUT, GARAP, ZONA BERAT)
$this->leaderboard          → dapilProgress sorted by score
$this->timeline             → collection 8 aktivitas terbaru
$this->quickAccess          → array 5 shortcut menu
$this->periodLabel          → string "Juni 2026"
$this->selectedDapil        → string dapil terpilih
$this->selectedKecamatan    → string kecamatan terpilih
$this->accessScope          → array scope akses user
```

---

## STRUKTUR BARU DASHBOARD

Ganti seluruh isi `resources/views/livewire/dashboard.blade.php` dengan struktur berikut.

### ZONE 1 — Header (selalu tampil)

```blade
<div class="flex items-start justify-between mb-4">
  <div>
    <h1 class="text-xl font-semibold text-zinc-900">Dashboard pemenangan</h1>
    <p class="text-xs text-zinc-500 mt-0.5">DPD PKS Kabupaten Bekasi · Periode: {{ $this->periodLabel }}</p>
  </div>
  {{-- Filter dapil + periode (pertahankan dari view lama) --}}
  <div class="flex items-center gap-2">
    <select wire:model.live="selectedDapil" class="text-xs border border-zinc-200 rounded-lg px-2 py-1.5">
      <option value="">Semua dapil</option>
      @foreach ($this->dapilOptions as $dapil)
        <option value="{{ $dapil }}">{{ $dapil }}</option>
      @endforeach
    </select>
    <select wire:model.live="selectedBulan" class="text-xs border border-zinc-200 rounded-lg px-2 py-1.5">
      @foreach (range(1, 12) as $bulan)
        <option value="{{ $bulan }}">{{ Carbon\Carbon::create(null, $bulan)->translatedFormat('MMMM') }}</option>
      @endforeach
    </select>
  </div>
</div>

{{-- Progress target 350.000 suara --}}
<div class="bg-white border border-zinc-100 rounded-xl p-4 mb-4">
  @php $totalKontak = $this->kpi['totalKader'] ?? 0; $targetSuara = 350000; $pct = min(100, round($totalKontak / $targetSuara * 100)); @endphp
  <div class="flex justify-between items-center mb-2">
    <span class="text-xs font-medium text-zinc-500">Target 350.000 suara — Pemilu 2029</span>
    <span class="text-sm font-semibold" style="color:#ea580c;">{{ $pct }}%</span>
  </div>
  <div class="h-1.5 rounded-full bg-zinc-100 overflow-hidden">
    <div class="h-full rounded-full" style="width:{{ $pct }}%; background:#ea580c;"></div>
  </div>
  {{-- 4 KPI terpenting --}}
  <div class="grid grid-cols-4 gap-3 mt-4">
    <div>
      <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">Kader aktif</div>
      <div class="text-2xl font-semibold text-zinc-900">{{ number_format($this->kpi['totalKader']) }}</div>
      <div class="text-[10px] text-zinc-400">+{{ number_format($this->kpi['kaderBulanIni']) }} bulan ini</div>
    </div>
    <div>
      <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">KORWE terbentuk</div>
      <div class="text-2xl font-semibold text-zinc-900">{{ number_format($this->kpi['korweTerbentuk']) }}</div>
      <div class="text-[10px] text-zinc-400">{{ $this->kpi['korwePct'] }}% dari {{ number_format($this->kpi['targetKorwe']) }} target</div>
    </div>
    <div>
      <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">Sisir RW</div>
      <div class="text-2xl font-semibold text-zinc-900">{{ number_format($this->kpi['rwTersisir']) }}</div>
      <div class="text-[10px] text-zinc-400">{{ $this->kpi['sisirPct'] }}% dari {{ number_format($this->kpi['totalRw']) }} RW</div>
    </div>
    <div>
      <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">Aspirasi POKIR</div>
      <div class="text-2xl font-semibold text-zinc-900">50</div>
      <div class="text-[10px] text-zinc-400">28 masuk SIPD</div>
    </div>
  </div>
</div>
```

### ZONE 2 — Peta 7 Dapil (selalu tampil, compact)

```blade
<div class="bg-white border border-zinc-100 rounded-xl p-4 mb-4">
  <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium mb-3">Peta 7 dapil</div>
  <div class="grid grid-cols-7 gap-2">
    @foreach ($this->dapilMap as $dapil)
    <a href="#" wire:click.prevent="$set('selectedDapil', '{{ $dapil['dapil'] }}')"
       class="rounded-lg border p-2 text-center cursor-pointer transition
       {{ $dapil['active'] ? 'border-orange-400 bg-orange-50' : 'border-zinc-100 bg-zinc-50 hover:border-zinc-300' }}">
      {{-- Peta gambar --}}
      <div class="h-9 flex items-center justify-center mb-1 overflow-hidden rounded">
        @if($dapil['image'])
          <img src="{{ $dapil['image'] }}" class="h-full w-full object-contain">
        @else
          <div class="h-9 w-full bg-zinc-100 rounded"></div>
        @endif
      </div>
      <div class="text-[9px] font-semibold {{ $dapil['active'] ? 'text-orange-600' : 'text-zinc-500' }}">
        DAPIL {{ $dapil['dapil_number'] }}
      </div>
      <div class="text-[8px] text-zinc-400">{{ $dapil['desa_total'] }} desa</div>
      <div class="flex justify-between mt-1 text-[8px]">
        <span style="color:#ea580c;">{{ $dapil['korwe_pct'] }}%K</span>
        <span class="text-zinc-400">{{ $dapil['sisir_pct'] }}%S</span>
        <span style="color:#2563eb;">{{ $dapil['profil_pct'] }}%P</span>
      </div>
    </a>
    @endforeach
  </div>
  <div class="flex gap-4 mt-2 text-[9px] text-zinc-400">
    <span style="color:#ea580c;">K = KORWE</span>
    <span>S = Sisir RW</span>
    <span style="color:#2563eb;">P = Profil RW</span>
  </div>
</div>
```

### ZONE 3 — Tab System (konten utama)

```blade
{{-- Tab navigation --}}
<div class="flex gap-2 mb-3 overflow-x-auto" x-data="{ tab: 'infra' }">
  <button @click="tab='infra'"
    :class="tab==='infra' ? 'bg-orange-600 text-white border-orange-600' : 'border-zinc-200 text-zinc-500 hover:border-zinc-300'"
    class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-medium border whitespace-nowrap transition">
    <i class="ti ti-building text-sm"></i> Infrastruktur
  </button>
  <button @click="tab='program'"
    :class="tab==='program' ? 'bg-orange-600 text-white border-orange-600' : 'border-zinc-200 text-zinc-500 hover:border-zinc-300'"
    class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-medium border whitespace-nowrap transition">
    <i class="ti ti-calendar-event text-sm"></i> Program
  </button>
  <button @click="tab='kinerja'"
    :class="tab==='kinerja' ? 'bg-orange-600 text-white border-orange-600' : 'border-zinc-200 text-zinc-500 hover:border-zinc-300'"
    class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-medium border whitespace-nowrap transition">
    <i class="ti ti-chart-line text-sm"></i> Kinerja & trend
  </button>
  <button @click="tab='alert'"
    :class="tab==='alert' ? 'bg-orange-600 text-white border-orange-600' : 'border-zinc-200 text-zinc-500 hover:border-zinc-300'"
    class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-medium border whitespace-nowrap transition">
    <i class="ti ti-bell text-sm"></i> Perlu perhatian
    @if(count($this->alerts) > 0)
      <span class="bg-red-500 text-white rounded-full px-1.5 py-0 text-[9px] leading-4">{{ count($this->alerts) }}</span>
    @endif
  </button>

  {{-- ===== TAB INFRASTRUKTUR ===== --}}
  <div x-show="tab==='infra'" class="w-full mt-0">

    {{-- 3 Metric utama dengan progress bar --}}
    <div class="grid grid-cols-3 gap-3 mb-3">

      {{-- KORWE --}}
      <div class="bg-white border border-zinc-100 rounded-xl p-4">
        <div class="flex items-start justify-between mb-3">
          <div>
            <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">KORWE terbentuk</div>
            <div class="text-3xl font-semibold text-zinc-900 mt-1">
              {{ number_format($this->kpi['korweTerbentuk']) }}
              <span class="text-sm font-normal text-zinc-400">/ {{ number_format($this->kpi['targetKorwe']) }}</span>
            </div>
          </div>
          <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $this->kpi['korwePct'] >= 50 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
            {{ $this->kpi['korwePct'] }}%
          </span>
        </div>
        <div class="h-1.5 rounded-full bg-zinc-100 overflow-hidden">
          <div class="h-full rounded-full bg-orange-500" style="width:{{ min(100, $this->kpi['korwePct']) }}%"></div>
        </div>
        <div class="text-[10px] text-zinc-400 mt-1.5">{{ number_format($this->kpi['targetKorwe'] - $this->kpi['korweTerbentuk']) }} RW tersisa</div>
      </div>

      {{-- SISIR RW --}}
      <div class="bg-white border border-zinc-100 rounded-xl p-4">
        <div class="flex items-start justify-between mb-3">
          <div>
            <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">Sisir RW bulan ini</div>
            <div class="text-3xl font-semibold text-zinc-900 mt-1">
              {{ number_format($this->kpi['rwTersisir']) }}
              <span class="text-sm font-normal text-zinc-400">/ {{ number_format($this->kpi['totalRw']) }}</span>
            </div>
          </div>
          <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $this->kpi['sisirPct'] >= 20 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
            {{ $this->kpi['sisirPct'] }}%
          </span>
        </div>
        <div class="h-1.5 rounded-full bg-zinc-100 overflow-hidden">
          <div class="h-full rounded-full bg-blue-500" style="width:{{ min(100, $this->kpi['sisirPct']) }}%"></div>
        </div>
        <div class="text-[10px] text-zinc-400 mt-1.5">{{ number_format($this->kpi['kegiatanBulanIni']) }} kegiatan · {{ number_format($this->kpi['wargaTerjangkau']) }} warga</div>
      </div>

      {{-- PROFIL RW --}}
      <div class="bg-white border border-zinc-100 rounded-xl p-4">
        <div class="flex items-start justify-between mb-3">
          <div>
            <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium">Profil RW terisi</div>
            <div class="text-3xl font-semibold text-zinc-900 mt-1">
              {{ number_format($this->kpi['profilTerisi']) }}
              <span class="text-sm font-normal text-zinc-400">/ {{ number_format($this->kpi['totalRw']) }}</span>
            </div>
          </div>
          <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $this->kpi['profilPct'] >= 30 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
            {{ $this->kpi['profilPct'] }}%
          </span>
        </div>
        <div class="h-1.5 rounded-full bg-zinc-100 overflow-hidden">
          <div class="h-full rounded-full bg-amber-500" style="width:{{ min(100, $this->kpi['profilPct']) }}%"></div>
        </div>
        <div class="text-[10px] text-zinc-400 mt-1.5">{{ number_format($this->kpi['profilLengkap']) }} lengkap 100%</div>
      </div>
    </div>

    {{-- 6 Mini cards pendukung --}}
    <div class="grid grid-cols-6 gap-2">
      @foreach ([
        ['label' => 'KORTE', 'value' => $this->kpi['korteTerbentuk'], 'sub' => '/'.$this->kpi['targetKorte'], 'icon' => 'ti-map-2', 'color' => 'text-zinc-400'],
        ['label' => 'UPA RW', 'value' => $this->kpi['upaRw'], 'sub' => $this->kpi['upaRwFormal'].' formal', 'icon' => 'ti-building-community', 'color' => 'text-green-500'],
        ['label' => 'Penggalang', 'value' => $this->kpi['penggalang'], 'sub' => '/'.$this->kpi['targetPenggalang'], 'icon' => 'ti-users', 'color' => 'text-zinc-400'],
        ['label' => 'Relawan', 'value' => $this->kpi['relawan'], 'sub' => 'milenial', 'icon' => 'ti-heart-handshake', 'color' => 'text-purple-500'],
        ['label' => 'Saksi TPS', 'value' => $this->kpi['saksiTps'], 'sub' => '/'.$this->kpi['totalTps'].' TPS', 'icon' => 'ti-checkup-list', 'color' => 'text-zinc-400'],
        ['label' => 'Event aktif', 'value' => $this->kpi['eventAktif'], 'sub' => $this->kpi['eventMenunggu'].' pending', 'icon' => 'ti-calendar-event', 'color' => $this->kpi['eventMenunggu'] > 0 ? 'text-amber-500' : 'text-zinc-400'],
      ] as $m)
      <div class="bg-white border border-zinc-100 rounded-lg p-3 flex items-center gap-2.5">
        <i class="ti {{ $m['icon'] }} text-lg {{ $m['color'] }}"></i>
        <div>
          <div class="text-[9px] text-zinc-400">{{ $m['label'] }}</div>
          <div class="text-base font-semibold text-zinc-900">{{ number_format($m['value']) }}</div>
          <div class="text-[9px] text-zinc-400">{{ $m['sub'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ===== TAB PROGRAM ===== --}}
  <div x-show="tab==='program'" class="w-full mt-0">
    <div class="grid grid-cols-3 gap-3">
      @foreach ([
        ['label' => 'Event', 'icon' => 'ti-calendar-event', 'color' => '#2563eb', 'value' => $this->kpi['eventSelesai'].' selesai', 'sub' => $this->kpi['eventMenunggu'].' menunggu approval', 'route' => route('events.index')],
        ['label' => 'RKI (Bipeka)', 'icon' => 'ti-home-heart', 'color' => '#db2777', 'value' => $this->kpi['rki'].' aktif', 'sub' => 'Titik RKI terbentuk', 'route' => route('rki.index')],
        ['label' => 'KSN / Senam', 'icon' => 'ti-stretching', 'color' => '#16a34a', 'value' => $this->kpi['ksn'].' aktif', 'sub' => 'Titik senam terbentuk', 'route' => route('ksn.index')],
        ['label' => 'Kaderisasi', 'icon' => 'ti-users-group', 'color' => '#7c3aed', 'value' => number_format($this->kpi['totalKader']).' kader', 'sub' => '+'.$this->kpi['kaderBulanIni'].' bulan ini', 'route' => route('kaderisasi.index')],
        ['label' => 'Sosial Media', 'icon' => 'ti-brand-instagram', 'color' => '#ea580c', 'value' => $this->kpi['kontenBulanIni'].' konten', 'sub' => 'Avg popularitas '.$this->kpi['avgPopularitas'], 'route' => route('sosial-media.index')],
        ['label' => 'Aspirasi & POKIR', 'icon' => 'ti-message-chatbot', 'color' => '#0891b2', 'value' => '50 total', 'sub' => '28 masuk SIPD · 7 realisasi', 'route' => route('aspirasi.index')],
      ] as $p)
      <a href="{{ $p['route'] }}" wire:navigate
         class="bg-white border border-zinc-100 rounded-xl p-4 hover:border-zinc-300 transition block">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $p['color'] }}18;">
            <i class="ti {{ $p['icon'] }}" style="font-size:16px;color:{{ $p['color'] }};"></i>
          </div>
          <div class="text-xs font-medium text-zinc-700">{{ $p['label'] }}</div>
        </div>
        <div class="text-xl font-semibold text-zinc-900">{{ $p['value'] }}</div>
        <div class="text-[10px] text-zinc-400 mt-0.5">{{ $p['sub'] }}</div>
      </a>
      @endforeach
    </div>
  </div>

  {{-- ===== TAB KINERJA & TREND ===== --}}
  <div x-show="tab==='kinerja'" class="w-full mt-0">

    {{-- Trend infrastruktur 6 bulan --}}
    <div class="grid grid-cols-3 gap-3 mb-3">
      @foreach ($this->infraTrends->take(3) as $trend)
      <div class="bg-white border border-zinc-100 rounded-xl p-4">
        <div class="flex justify-between items-start mb-3">
          <div class="text-xs font-medium text-zinc-600">{{ $trend['label'] }}</div>
          <span class="text-xs font-semibold {{ $trend['change_pct'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $trend['change_pct'] >= 0 ? '+' : '' }}{{ $trend['change_pct'] }}%
          </span>
        </div>
        <div class="text-xl font-semibold mb-2" style="color:{{ $trend['color'] }}">{{ $trend['current'] }}</div>
        {{-- Mini bar chart --}}
        <div class="flex items-end gap-0.5 h-10">
          @foreach ($trend['months'] as $m)
          <div class="flex-1 rounded-sm {{ $m['current'] ? '' : 'opacity-40' }}"
               style="height:{{ max(8, $m['pct']) }}%; background:{{ $trend['color'] }};"></div>
          @endforeach
        </div>
        <div class="flex justify-between text-[8px] text-zinc-300 mt-1">
          @foreach ($trend['months'] as $m)
          <span>{{ $m['label'] }}</span>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>

    {{-- 4 Prestasi/Capaian --}}
    <div class="grid grid-cols-4 gap-3 mb-3">
      @foreach ($this->operationalHighlights as $h)
      <div class="bg-white border-l-2 border border-zinc-100 rounded-xl p-3"
           style="border-left-color:{{ $h['theme'] === 'orange' ? '#ea580c' : '#2563eb' }};">
        <div class="text-[9px] font-semibold uppercase tracking-wide mb-1"
             style="color:{{ $h['theme'] === 'orange' ? '#ea580c' : '#2563eb' }};">{{ $h['label'] }}</div>
        <div class="text-sm font-semibold text-zinc-900 truncate">{{ $h['value'] }}</div>
        <div class="text-[10px] text-zinc-400 mt-0.5">{{ $h['meta'] }}</div>
        <div class="text-[9px] text-zinc-300">{{ $h['detail'] }}</div>
      </div>
      @endforeach
    </div>

    {{-- Timeline aktivitas terbaru --}}
    <div class="bg-white border border-zinc-100 rounded-xl p-4">
      <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-medium mb-3">Aktivitas terbaru</div>
      <div class="space-y-3">
        @foreach ($this->timeline->take(6) as $item)
        <div class="flex items-start gap-3">
          <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
               style="background:{{ $item['color'] }}18;">
            <i class="ti ti-{{ $item['icon'] }}" style="font-size:11px;color:{{ $item['color'] }};"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs text-zinc-700 truncate">{{ $item['title'] }}</div>
            <div class="text-[10px] text-zinc-400">{{ $item['desc'] }}</div>
          </div>
          <div class="text-[9px] text-zinc-300 whitespace-nowrap flex-shrink-0">
            {{ $item['time'] ? \Carbon\Carbon::parse($item['time'])->diffForHumans() : '' }}
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ===== TAB PERLU PERHATIAN ===== --}}
  <div x-show="tab==='alert'" class="w-full mt-0">
    @if(count($this->alerts) > 0)
    <div class="flex flex-col gap-3">
      @foreach ($this->alerts as $alert)
      <div class="rounded-xl border p-4 flex items-start gap-3
        {{ $alert['type'] === 'danger' ? 'bg-red-50 border-red-200' :
           ($alert['type'] === 'warning' ? 'bg-amber-50 border-amber-200' : 'bg-blue-50 border-blue-200') }}">
        <i class="ti ti-{{ $alert['icon'] }} text-lg flex-shrink-0 mt-0.5
          {{ $alert['type'] === 'danger' ? 'text-red-500' :
             ($alert['type'] === 'warning' ? 'text-amber-500' : 'text-blue-500') }}"></i>
        <div class="flex-1">
          <div class="text-sm font-medium
            {{ $alert['type'] === 'danger' ? 'text-red-700' :
               ($alert['type'] === 'warning' ? 'text-amber-700' : 'text-blue-700') }}">
            {!! $alert['text'] !!}
          </div>
          @if(isset($alert['link']))
          <a href="{{ $alert['link'] }}" wire:navigate class="inline-block mt-2 text-xs font-medium px-3 py-1 rounded-md
            {{ $alert['type'] === 'danger' ? 'bg-red-100 text-red-600 hover:bg-red-200' :
               ($alert['type'] === 'warning' ? 'bg-amber-100 text-amber-600 hover:bg-amber-200' : 'bg-blue-100 text-blue-600 hover:bg-blue-200') }}">
            {{ $alert['link_text'] }} →
          </a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="bg-white border border-zinc-100 rounded-xl p-8 text-center">
      <i class="ti ti-circle-check text-4xl text-green-400"></i>
      <div class="text-sm font-medium text-zinc-600 mt-2">Tidak ada alert saat ini</div>
      <div class="text-xs text-zinc-400 mt-1">Semua program berjalan normal</div>
    </div>
    @endif

    {{-- Status Wilayah summary --}}
    <div class="grid grid-cols-5 gap-2 mt-3">
      @foreach ($this->statusWilayah as $s)
      <div class="rounded-lg p-3 text-center" style="background:{{ $s['bg'] }};">
        <div class="text-lg font-semibold" style="color:{{ $s['text'] }};">{{ number_format($s['total']) }}</div>
        <div class="text-[9px] font-medium" style="color:{{ $s['text'] }};">{{ $s['label'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

</div>
```

---

## CATATAN PENTING

1. **Tab sistem pakai Alpine.js** (`x-data`, `x-show`, `@click`) — sudah tersedia di Flux UI, tidak perlu install.

2. **JANGAN ubah Dashboard.php** — semua computed properties sudah sempurna, hanya view yang diganti.

3. **`$this->kpi['profilLengkap']`** — tambahkan ke return array di `kpi()` jika belum ada:
   ```php
   'profilLengkap' => (int) $this->filteredProfilQuery()->where('is_complete', true)->count(),
   ```

4. **Route yang dipakai** (pastikan semua exist):
   - `route('events.index')` ✅
   - `route('kaderisasi.index')` ✅
   - `route('sisir-rw.index')` ✅
   - `route('infra-rtrw.index')` ✅
   - `route('rki.index')` — cek nama route RKI
   - `route('ksn.index')` — cek nama route KSN
   - `route('sosial-media.index')` — cek nama route sosmed
   - `route('aspirasi.index')` ✅

   Jika ada route yang tidak ada, ganti dengan `#` sementara.

5. **Carbon import di blade** — pastikan ada di atas file:
   ```blade
   @use('Carbon\Carbon')
   ```
   Atau gunakan `\Carbon\Carbon::` di inline.

6. **Layout** — pertahankan layout yang sudah ada di `render()`:
   ```php
   ->layout($layout, ['title' => 'Dashboard Admin'])
   ```

Langsung ganti seluruh isi view. Jangan test.
