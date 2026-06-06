# Prompt Trae — Redesign Sidebar & Dashboard KBH Management

---

## PROMPT: Redesign Sidebar Menu + Dashboard

```
Redesign sidebar dan dashboard halaman utama KBH Management. Langsung ganti file, JANGAN test, JANGAN tanya.

=== FILE 1: resources/views/components/layouts/app/sidebar.blade.php ===

GANTI SELURUH isi file dengan kode berikut. Sidebar baru menggunakan dark theme (#111113) dengan accent orange #fe5000 sebagai identitas Bekasi Hebat.

PERHATIKAN: Tetap pakai Flux components (flux:sidebar, flux:navlist, dll) karena itu yang dipakai project ini. Tapi OVERRIDE styling-nya agar match desain baru via inline style dan tambahan CSS.

STRUKTUR SIDEBAR BARU:

1. LOGO & BRAND
   - Icon: kotak rounded 34px background #fe5000 dengan SVG logo (pakai <x-app-logo-icon> yang sudah ada, set fill="white")
   - Text: "Bekasi Hebat" (13px, font-weight 500, white)
   - Sub: "Management System" (10px, #71717a)

2. GROUP: "Menu Utama"
   - Dashboard (icon: home) → route('dashboard')

3. DIVIDER

4. GROUP: "Program"
   - Kaderisasi (icon: users-group) → '#' (belum ada route)
   - Infra RT/RW (icon: building-community) → '#'
   - Sosial Media (icon: brand-instagram) → '#'
   - RKI / KSN (icon: clipboard-list) → '#'
   
   Untuk menu yang belum aktif (route '#'), tambahkan badge "Segera" kecil di kanan:
   ```html
   <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#27272a;color:#71717a;">Segera</span>
   ```

5. DIVIDER

6. GROUP: "Strategi & Analisa"
   - Bedah Dapil (icon: map-search) → route('bedah-dapil.index')
     - Badge "Aktif" (background: #fe500030, color: #fe5000) jika route sudah ada
     - SUB-MENU (tampil saat current route match 'bedah-dapil.*'):
       - Dashboard Pemilu (icon: chart-bar) → route('bedah-dapil.pemilu-dprd')
       - Analisa Caleg (icon: user-search) → route('bedah-dapil.analisa-caleg')
       - Rencana Aksi (icon: target-arrow) → route('bedah-dapil.rencana-aksi')
       - Sisir RW (icon: walk) → route('bedah-dapil.sisir-rw')
     
     Sub-menu hanya tampil jika user sedang di halaman bedah-dapil:
     ```blade
     @if(request()->routeIs('bedah-dapil.*'))
       <!-- sub-menu items -->
     @endif
     ```

7. DIVIDER

8. GROUP: "Manajemen"
   - Kegiatan / Event (icon: calendar-event) → '#'
   - Kartu Anggota (icon: id-badge-2) → '#'
   - Affiliate (icon: link) → '#'
   
   Semua pakai badge "Segera".

9. SPACER (flex:1)

10. BOTTOM SECTION (border-top)
    - Pengaturan (icon: settings) → route('settings.profile')
    - User profile card: avatar initials (34px, bg #fe5000, white text), nama, email
    - Dropdown menu: Settings, Log Out (pakai yang sudah ada, jangan ubah logic-nya)

STYLING OVERRIDE:

Tambahkan <style> di dalam file untuk override Flux default:

```html
<style>
  /* Override Flux sidebar untuk dark theme Bekasi Hebat */
  [data-flux-sidebar] {
    background: #111113 !important;
    border-color: #27272a !important;
  }
  [data-flux-sidebar] [data-flux-navlist-item] {
    color: #a1a1aa !important;
    border-radius: 8px !important;
  }
  [data-flux-sidebar] [data-flux-navlist-item]:hover {
    background: #1c1c1f !important;
    color: #e4e4e7 !important;
  }
  [data-flux-sidebar] [data-flux-navlist-item][aria-current="true"],
  [data-flux-sidebar] [data-flux-navlist-item][data-current] {
    background: rgba(254, 80, 0, 0.1) !important;
    color: #fe5000 !important;
  }
  [data-flux-sidebar] [data-flux-navlist-group-heading] {
    color: #52525b !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
  }
  [data-flux-sidebar] .flux-separator,
  [data-flux-sidebar] hr {
    border-color: #27272a !important;
  }
</style>
```

JIKA Flux components tidak support override styling dengan cara di atas, GANTI dengan HTML biasa + Tailwind:

```blade
<aside class="fixed inset-y-0 left-0 z-30 w-[250px] flex flex-col" style="background:#111113;border-right:0.5px solid #27272a;">
  
  {{-- Logo --}}
  <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-4" style="border-bottom:0.5px solid #27272a;" wire:navigate>
    <div class="flex items-center justify-center rounded-lg" style="width:34px;height:34px;background:#fe5000;">
      <x-app-logo-icon class="size-[18px] fill-current text-white" />
    </div>
    <div>
      <div style="font-size:13px;font-weight:500;color:white;line-height:1.2;">Bekasi Hebat</div>
      <div style="font-size:10px;color:#71717a;margin-top:1px;">Management System</div>
    </div>
  </a>

  {{-- Scrollable nav --}}
  <nav class="flex-1 overflow-y-auto py-2">
    
    {{-- Menu Utama --}}
    <div style="padding:12px 12px 4px;font-size:10px;color:#52525b;text-transform:uppercase;letter-spacing:1px;font-weight:500;">Menu Utama</div>
    <div class="px-2">
      <a href="{{ route('dashboard') }}" wire:navigate
         class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition-colors"
         style="{{ request()->routeIs('dashboard') ? 'background:rgba(254,80,0,0.1);color:#fe5000;' : 'color:#a1a1aa;' }}"
         onmouseover="if(!this.style.background.includes('254'))this.style.background='#1c1c1f';this.style.color=this.style.color==='rgb(161, 161, 170)'?'#e4e4e7':this.style.color"
         onmouseout="if(!this.getAttribute('href').includes(window.location.pathname)){this.style.background='';this.style.color='#a1a1aa';}">
        <i class="ti ti-home" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
        Dashboard
      </a>
    </div>

    <hr style="border-color:#27272a;margin:8px 12px;">

    {{-- Program --}}
    <div style="padding:12px 12px 4px;font-size:10px;color:#52525b;text-transform:uppercase;letter-spacing:1px;font-weight:500;">Program</div>
    <div class="px-2 space-y-0.5">
      @php
        $programMenus = [
          ['icon' => 'users-group', 'label' => 'Kaderisasi', 'route' => '#'],
          ['icon' => 'building-community', 'label' => 'Infra RT/RW', 'route' => '#'],
          ['icon' => 'brand-instagram', 'label' => 'Sosial Media', 'route' => '#'],
          ['icon' => 'clipboard-list', 'label' => 'RKI / KSN', 'route' => '#'],
        ];
      @endphp
      @foreach($programMenus as $menu)
        <a href="{{ $menu['route'] }}" 
           class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm"
           style="color:#a1a1aa;">
          <i class="ti ti-{{ $menu['icon'] }}" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
          <span class="flex-1">{{ $menu['label'] }}</span>
          <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#27272a;color:#71717a;">Segera</span>
        </a>
      @endforeach
    </div>

    <hr style="border-color:#27272a;margin:8px 12px;">

    {{-- Strategi & Analisa --}}
    <div style="padding:12px 12px 4px;font-size:10px;color:#52525b;text-transform:uppercase;letter-spacing:1px;font-weight:500;">Strategi & Analisa</div>
    <div class="px-2 space-y-0.5">
      <a href="{{ route('bedah-dapil.index') }}"
         class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition-colors"
         style="{{ request()->routeIs('bedah-dapil.*') ? 'background:rgba(254,80,0,0.1);color:#fe5000;' : 'color:#a1a1aa;' }}">
        <i class="ti ti-map-search" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
        <span class="flex-1">Bedah Dapil</span>
        @if(Route::has('bedah-dapil.index'))
          <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:rgba(254,80,0,0.18);color:#fe5000;font-weight:500;">Aktif</span>
        @endif
      </a>
      
      @if(request()->routeIs('bedah-dapil.*'))
        <div style="padding-left:30px;" class="space-y-0.5">
          @php
            $subMenus = [
              ['icon' => 'chart-bar', 'label' => 'Dashboard Pemilu', 'route' => 'bedah-dapil.pemilu-dprd'],
              ['icon' => 'user-search', 'label' => 'Analisa Caleg', 'route' => 'bedah-dapil.analisa-caleg'],
              ['icon' => 'target-arrow', 'label' => 'Rencana Aksi', 'route' => 'bedah-dapil.rencana-aksi'],
              ['icon' => 'walk', 'label' => 'Sisir RW', 'route' => 'bedah-dapil.sisir-rw'],
            ];
          @endphp
          @foreach($subMenus as $sub)
            @if(Route::has($sub['route']))
              <a href="{{ route($sub['route']) }}"
                 class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-xs"
                 style="{{ request()->routeIs($sub['route']) ? 'color:#fe5000;' : 'color:#71717a;' }}">
                <i class="ti ti-{{ $sub['icon'] }}" style="font-size:13px;" aria-hidden="true"></i>
                {{ $sub['label'] }}
              </a>
            @endif
          @endforeach
        </div>
      @endif
    </div>

    <hr style="border-color:#27272a;margin:8px 12px;">

    {{-- Manajemen --}}
    <div style="padding:12px 12px 4px;font-size:10px;color:#52525b;text-transform:uppercase;letter-spacing:1px;font-weight:500;">Manajemen</div>
    <div class="px-2 space-y-0.5">
      @php
        $manajemenMenus = [
          ['icon' => 'calendar-event', 'label' => 'Kegiatan / Event', 'route' => '#'],
          ['icon' => 'id-badge-2', 'label' => 'Kartu Anggota', 'route' => '#'],
          ['icon' => 'link', 'label' => 'Affiliate', 'route' => '#'],
        ];
      @endphp
      @foreach($manajemenMenus as $menu)
        <a href="{{ $menu['route'] }}"
           class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm"
           style="color:#a1a1aa;">
          <i class="ti ti-{{ $menu['icon'] }}" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
          <span class="flex-1">{{ $menu['label'] }}</span>
          <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#27272a;color:#71717a;">Segera</span>
        </a>
      @endforeach
    </div>
  </nav>

  {{-- Bottom section --}}
  <div style="border-top:0.5px solid #27272a;padding:8px;">
    <a href="{{ route('settings.profile') }}" wire:navigate
       class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm"
       style="color:#a1a1aa;margin-bottom:4px;">
      <i class="ti ti-settings" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
      Pengaturan
    </a>
    
    {{-- User dropdown --}}
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm hover:bg-white/5" style="color:#a1a1aa;">
        <div class="flex items-center justify-center rounded-lg" style="width:32px;height:32px;background:#fe5000;color:white;font-size:11px;font-weight:500;flex-shrink:0;">
          {{ auth()->user()->initials() }}
        </div>
        <div class="flex-1 text-left min-w-0">
          <div style="font-size:12px;color:#e4e4e7;font-weight:500;" class="truncate">{{ auth()->user()->name }}</div>
          <div style="font-size:10px;color:#52525b;" class="truncate">{{ auth()->user()->email }}</div>
        </div>
        <i class="ti ti-selector" style="font-size:14px;color:#52525b;" aria-hidden="true"></i>
      </button>
      
      {{-- Dropdown menu --}}
      <div x-show="open" @click.away="open = false" x-transition
           style="position:absolute;bottom:100%;left:0;right:0;margin-bottom:4px;background:#1c1c1f;border:0.5px solid #27272a;border-radius:8px;padding:4px;z-index:50;">
        <a href="{{ route('settings.profile') }}" wire:navigate
           class="flex items-center gap-2 rounded-md px-3 py-2 text-xs hover:bg-white/5"
           style="color:#a1a1aa;">
          <i class="ti ti-settings" style="font-size:14px;" aria-hidden="true"></i>
          Settings
        </a>
        <hr style="border-color:#27272a;margin:4px 0;">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-xs hover:bg-white/5"
                  style="color:#a1a1aa;">
            <i class="ti ti-logout" style="font-size:14px;" aria-hidden="true"></i>
            Log Out
          </button>
        </form>
      </div>
    </div>
  </div>
</aside>

{{-- Content offset for fixed sidebar --}}
<div class="lg:pl-[250px]">
  {{-- Mobile header --}}
  <header class="lg:hidden flex items-center justify-between px-4 py-3" style="background:#111113;border-bottom:0.5px solid #27272a;">
    <button @click="$dispatch('toggle-sidebar')" style="color:#a1a1aa;">
      <i class="ti ti-menu-2" style="font-size:20px;" aria-hidden="true"></i>
    </button>
    <div style="font-size:13px;font-weight:500;color:white;">Bekasi Hebat</div>
    <div class="flex items-center justify-center rounded-lg" style="width:28px;height:28px;background:#fe5000;color:white;font-size:10px;font-weight:500;">
      {{ auth()->user()->initials() }}
    </div>
  </header>
  
  {{ $slot }}
</div>
```

PENTING:
- Pastikan Tabler Icons CSS sudah di-load di head. Jika belum, tambahkan di resources/views/partials/head.blade.php:
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
- Jika pendekatan custom HTML (bukan Flux components) menyebabkan layout rusak, kemungkinan karena <flux:main> di app.blade.php bergantung pada <flux:sidebar>. Dalam kasus itu, update juga app.blade.php:

```blade
{{-- resources/views/components/layouts/app.blade.php --}}
<x-layouts.app.sidebar :title="$title ?? null">
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>
</x-layouts.app.sidebar>
```

- Test buka halaman, jika sidebar tidak muncul atau overlap, adjust padding-left/margin di content wrapper.
- Pertahankan semua logic auth (logout form, CSRF, user menu) — jangan hapus.

=== FILE 2: resources/views/components/app-logo.blade.php ===

Ganti agar match branding baru:

```blade
<div class="flex items-center justify-center rounded-lg" style="width:34px;height:34px;background:#fe5000;">
    <x-app-logo-icon class="size-[18px] fill-current text-white" />
</div>
<div class="ms-2 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold text-white">Bekasi Hebat</span>
    <span class="truncate text-xs" style="color:#71717a;">Management System</span>
</div>
```

=== FILE 3: resources/views/dashboard.blade.php ===

Ganti SELURUH isi dengan dashboard baru:

```blade
<x-layouts.app :title="__('Dashboard')">
<div class="mx-auto max-w-6xl">
  
  {{-- Header --}}
  <div class="mb-6">
    <h1 class="text-xl font-medium text-zinc-900 dark:text-zinc-100">Dashboard</h1>
    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Selamat datang di Bekasi Hebat Management System</p>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    {{-- Card: Total Anggota --}}
    <div class="rounded-xl p-4" style="background:var(--color-background-secondary, #f4f4f5);">
      <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400 mb-1">
        <i class="ti ti-users" style="font-size:13px;" aria-hidden="true"></i> Total anggota
      </div>
      <div class="text-2xl font-medium text-zinc-900 dark:text-zinc-100">—</div>
      <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Segera tersedia</div>
    </div>
    {{-- Card: Event Aktif --}}
    <div class="rounded-xl p-4" style="background:var(--color-background-secondary, #f4f4f5);">
      <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400 mb-1">
        <i class="ti ti-calendar-event" style="font-size:13px;" aria-hidden="true"></i> Event aktif
      </div>
      <div class="text-2xl font-medium text-zinc-900 dark:text-zinc-100">—</div>
      <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Segera tersedia</div>
    </div>
    {{-- Card: Wilayah --}}
    <div class="rounded-xl p-4" style="background:var(--color-background-secondary, #f4f4f5);">
      <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400 mb-1">
        <i class="ti ti-map-pin" style="font-size:13px;" aria-hidden="true"></i> Wilayah tercover
      </div>
      <div class="text-2xl font-medium text-zinc-900 dark:text-zinc-100">7 Dapil</div>
      <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">23 kecamatan · 187 desa</div>
    </div>
    {{-- Card: Bedah Dapil --}}
    <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#fe5000,#d94400);color:white;">
      <div class="flex items-center gap-1.5 text-xs mb-1" style="opacity:0.9;">
        <i class="ti ti-target-arrow" style="font-size:13px;" aria-hidden="true"></i> Bedah Dapil
      </div>
      <div class="text-2xl font-medium">Aktif</div>
      <div class="text-xs mt-0.5" style="opacity:0.85;">Data Pemilu 2024 tersedia</div>
    </div>
  </div>

  {{-- Quick Access --}}
  <div class="mb-6">
    <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-3">Akses cepat</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
      
      @php
        $quickAccess = [
          [
            'icon' => 'map-search',
            'color' => '#fe5000',
            'label' => 'Bedah Dapil',
            'desc' => 'Analisa pemilu & strategi pemenangan 2029',
            'route' => Route::has('bedah-dapil.index') ? route('bedah-dapil.index') : '#',
            'active' => true,
          ],
          [
            'icon' => 'users-group',
            'color' => '#2563eb',
            'label' => 'Kaderisasi',
            'desc' => 'Kelola dan tracking kader per wilayah',
            'route' => '#',
            'active' => false,
          ],
          [
            'icon' => 'building-community',
            'color' => '#16a34a',
            'label' => 'Infra RT/RW',
            'desc' => 'Infrastruktur dan data RT/RW',
            'route' => '#',
            'active' => false,
          ],
          [
            'icon' => 'brand-instagram',
            'color' => '#d946ef',
            'label' => 'Sosial Media',
            'desc' => 'Monitoring dan strategi sosmed',
            'route' => '#',
            'active' => false,
          ],
          [
            'icon' => 'clipboard-list',
            'color' => '#d97706',
            'label' => 'RKI / KSN',
            'desc' => 'Rencana Kerja Internal',
            'route' => '#',
            'active' => false,
          ],
        ];
      @endphp

      @foreach($quickAccess as $qa)
        <a href="{{ $qa['route'] }}" 
           class="block rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 transition-colors hover:border-zinc-300 dark:hover:border-zinc-600"
           style="background:var(--color-background-primary, white);"
           @if($qa['active'] && $qa['route'] !== '#') wire:navigate @endif>
          <div class="flex items-center gap-2 mb-2">
            <div class="flex items-center justify-center rounded-lg" style="width:32px;height:32px;background:{{ $qa['color'] }}15;">
              <i class="ti ti-{{ $qa['icon'] }}" style="font-size:16px;color:{{ $qa['color'] }};" aria-hidden="true"></i>
            </div>
            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $qa['label'] }}</div>
          </div>
          <div class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $qa['desc'] }}</div>
          @if(!$qa['active'])
            <div class="mt-2">
              <span class="text-[10px] px-2 py-0.5 rounded-full" style="background:#27272a;color:#71717a;">Segera hadir</span>
            </div>
          @endif
        </a>
      @endforeach
    </div>
  </div>

  {{-- Info Section --}}
  <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-5" style="background:var(--color-background-primary, white);">
    <div class="flex items-start gap-3">
      <div class="flex items-center justify-center rounded-lg flex-shrink-0" style="width:40px;height:40px;background:#fe500015;">
        <i class="ti ti-info-circle" style="font-size:20px;color:#fe5000;" aria-hidden="true"></i>
      </div>
      <div>
        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-1">Platform sedang dalam pengembangan</div>
        <div class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
          Modul <strong>Bedah Dapil</strong> sudah aktif dengan data Pemilu DPRD 2024. 
          Modul lainnya (Kaderisasi, Infra RT/RW, Sosial Media, RKI/KSN, Kegiatan, Kartu Anggota, Affiliate) 
          akan diluncurkan secara bertahap. Hubungi admin untuk informasi lebih lanjut.
        </div>
      </div>
    </div>
  </div>

</div>
</x-layouts.app>
```

=== FILE 4: resources/views/partials/head.blade.php ===

Tambahkan Tabler Icons CSS jika BELUM ada. Cek dulu isi file, lalu tambahkan sebelum </head> atau sebelum @vite:

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
```

=== CATATAN ===

- Jika sidebar custom HTML menyebabkan Flux main content tidak render, kemungkinan karena Flux expect <flux:sidebar>. Solusi: bungkus sidebar custom HTML di dalam <flux:sidebar> sebagai slot, ATAU ubah app.blade.php agar tidak pakai <flux:main>.
- Jika Tabler Icons tidak muncul (kotak kosong), pastikan CDN link sudah di-load di head.
- Jika sidebar overlap content, pastikan content area punya padding-left: 250px (atau lg:pl-[250px] di Tailwind).
- JANGAN hapus route yang sudah ada untuk bedah-dapil.
- Semua menu '#' (belum aktif) tetap tampil tapi tidak navigasi — ini agar user tahu fitur yang akan datang.
- Setelah selesai, buka /dashboard dan pastikan sidebar + dashboard baru tampil benar.

Langsung kerjakan semua file. Jangan test.
```
