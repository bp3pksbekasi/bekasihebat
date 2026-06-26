<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#0f0f10] text-zinc-100">
        <style>
            [data-flux-sidebar] {
                background: #111113 !important;
                border-color: #27272a !important;
            }
            [data-flux-sidebar] .flux-separator,
            [data-flux-sidebar] hr {
                border-color: #27272a !important;
            }
            [data-flux-main] {
                background: #f5f5f5 !important;
            }
            .sidebar-menu-scroll {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .sidebar-menu-scroll::-webkit-scrollbar {
                display: none;
            }
        </style>

        @php
            $user = auth()->user();
            $pendingCount = class_exists(\App\Models\Event::class)
                ? \App\Models\Event::query()
                    ->where('status', 'pending_approval')
                    ->where('org_level', 'dpd')  // hanya DPD yang butuh approval
                    ->count()
                : 0;
            $totalKontak = class_exists(\App\Models\KontakWarga::class)
                ? \App\Models\KontakWarga::query()->where('status', 'aktif')->count()
                : 0;
            $totalKader = class_exists(\App\Models\Kader::class)
                ? \App\Models\Kader::query()->where('status', 'aktif')->count()
                : 0;
            $dashboardRoute = $user && $user->isKader() ? 'member.dashboard' : 'dashboard';
            $aspirasiTablesReady = \Illuminate\Support\Facades\Schema::hasTable('aspirasis')
                && \Illuminate\Support\Facades\Schema::hasTable('aspirasi_reminders');
            $belumAssignAspirasi = $aspirasiTablesReady && class_exists(\App\Models\Aspirasi::class)
                ? \App\Models\Aspirasi::query()->whereNull('assigned_dewan_id')->count()
                : 0;
            $unreadReminders = $aspirasiTablesReady && auth()->check() && class_exists(\App\Models\AspirasiReminder::class)
                ? \App\Models\AspirasiReminder::query()
                    ->where('target_user_id', auth()->id())
                    ->where('is_read', false)
                    ->count()
                : 0;
            $reminderItems = $aspirasiTablesReady && auth()->check() && class_exists(\App\Models\AspirasiReminder::class)
                ? \App\Models\AspirasiReminder::query()
                    ->with('aspirasi')
                    ->where('target_user_id', auth()->id())
                    ->latest()
                    ->limit(8)
                    ->get()
                : collect();

            $menus = [
                ['route' => $dashboardRoute, 'slug' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'active' => ['dashboard', 'member.dashboard']],

                ['section' => 'PROGRAM'],
                ['route' => 'kaderisasi.index', 'slug' => 'kaderisasi', 'label' => 'Kaderisasi', 'icon' => 'school', 'active' => ['kaderisasi.*']],
                ['route' => 'infra-rtrw.index', 'slug' => 'infra-rtrw', 'label' => 'Infrastruktur', 'icon' => 'building-community', 'active' => ['infra-rtrw.*']],
                ['route' => 'buku-induk-rw.index', 'slug' => 'peta-kekuatan-rw', 'label' => 'Peta Kekuatan RW', 'icon' => 'book', 'active' => ['buku-induk-rw.*']],
                ['route' => 'sisir-rw.index', 'slug' => 'sisir-rw', 'label' => 'Sisir RW', 'icon' => 'walk', 'active' => ['sisir-rw.*']],
                ['route' => 'sapa-warga.index', 'slug' => 'sapa-warga', 'label' => 'Sapa Warga', 'icon' => 'address-book', 'active' => ['sapa-warga.*']],
                ['route' => 'sosial-media.index', 'slug' => 'sosial-media', 'label' => 'Sosial Media', 'icon' => 'brand-instagram', 'active' => ['sosial-media.*']],
                ['route' => 'rki.index', 'slug' => 'rki', 'label' => 'Bipeka', 'icon' => 'heart-handshake', 'active' => ['rki.*']],
                ['route' => 'ksn.index', 'slug' => 'ksn', 'label' => 'Binapora', 'icon' => 'barbell', 'active' => ['ksn.*']],

                ['section' => 'STRATEGI & ANALISA'],
                [
                    'slug' => 'bedah-dapil-group',
                    'label' => 'Bedah Dapil',
                    'icon' => 'chart-pie',
                    'active' => ['bedah-dapil.*'],
                    'children' => [
                        ['route' => 'bedah-dapil.pemilu-dprd', 'slug' => 'pemilu-dprd', 'label' => 'Pemilu DPRD', 'active' => ['bedah-dapil.pemilu-dprd']],
                        ['route' => 'bedah-dapil.analisa-caleg', 'slug' => 'analisa-caleg', 'label' => 'Analisa Caleg', 'active' => ['bedah-dapil.analisa-caleg']],
                    ],
                ],
                ['route' => 'aspirasi.index', 'slug' => 'aspirasi', 'label' => 'Aspirasi & POKIR', 'icon' => 'message-chatbot', 'active' => ['aspirasi.*']],

                ['section' => 'ORGANISASI'],
                ['route' => 'events.index', 'slug' => 'event', 'access_slugs' => ['event', 'event-view'], 'label' => 'Program', 'icon' => 'calendar-event', 'active' => ['events.*']],

                ['section' => 'MANAJEMEN'],
                ['route' => 'settings.profile', 'slug' => 'profil', 'label' => 'Profil', 'icon' => 'user-circle', 'active' => ['settings.profile', 'settings.password', 'settings.appearance']],
                ['route' => 'approval-rw.index', 'slug' => 'approval-rw', 'label' => 'Antrean Profil RW', 'icon' => 'checklist', 'active' => ['approval-rw.*']],
                ['route' => 'pengaturan.users', 'slug' => 'pengaturan-users', 'label' => 'Kelola User', 'icon' => 'users-cog', 'active' => ['pengaturan.users']],
                ['route' => 'pengaturan.whatsapp', 'slug' => 'pengaturan-whatsapp', 'label' => 'Setting WhatsApp', 'icon' => 'brand-whatsapp', 'active' => ['pengaturan.whatsapp']],
                ['route' => 'pengaturan.rule', 'slug' => 'pengaturan-rule', 'label' => 'Pengaturan Rule', 'icon' => 'shield-lock', 'active' => ['pengaturan.rule']],
                ['route' => 'kartu-anggota.admin', 'slug' => 'kartu-anggota', 'label' => 'Kartu Anggota', 'icon' => 'id-badge-2', 'active' => ['kartu-anggota.*']],
            ];

            $menuVisible = function (array $menu) use ($user): bool {
                if (isset($menu['children'])) {
                    foreach ($menu['children'] as $child) {
                        if ($user && $user->canAccessMenuFromConfig($child)) {
                            return true;
                        }
                    }

                    return false;
                }

                return isset($menu['route']) && $user && $user->canAccessMenuFromConfig($menu);
            };

            $hasVisibleMenu = function (array $items, int $fromIndex) use ($menuVisible): bool {
                for ($idx = $fromIndex + 1; $idx < count($items); $idx++) {
                    $child = $items[$idx];

                    if (isset($child['section'])) {
                        break;
                    }

                    if ($menuVisible($child)) {
                        return true;
                    }
                }

                return false;
            };

            $routeUrl = function (array $menu): string {
                return Route::has($menu['route']) ? route($menu['route']) : '#';
            };

            $isActiveMenu = function (array $menu): bool {
                foreach ($menu['active'] ?? [$menu['route']] as $pattern) {
                    if (request()->routeIs($pattern)) {
                        return true;
                    }
                }

                return false;
            };

            $childLinkStyle = static function (bool $isActive): string {
                return $isActive
                    ? 'display:block;padding:6px 10px;border-radius:8px;background:rgba(254,80,0,0.12);color:#fe5000;font-size:13px;'
                    : 'display:block;padding:6px 10px;border-radius:8px;color:#a1a1aa;font-size:13px;';
            };
        @endphp

        <flux:sidebar sticky stashable class="border-r border-zinc-800 !bg-[#111113] !text-zinc-300">
            <flux:sidebar.toggle class="lg:hidden !text-zinc-300" icon="x-mark" />

            <a href="{{ route($dashboardRoute) }}" class="flex items-center px-5 py-4" style="border-bottom:0.5px solid #27272a;" wire:navigate>
                <img src="{{ asset('images/logoputih.png') }}" alt="Bekasi Hebat" style="height:36px;width:auto;object-fit:contain;">
            </a>

            <div class="sidebar-menu-scroll flex-1 overflow-y-auto py-2">
                @foreach ($menus as $index => $menu)
                    @if (isset($menu['section']))
                        @if ($hasVisibleMenu($menus, $index))
                            <div style="padding:12px 12px 4px;font-size:10px;color:#52525b;text-transform:uppercase;letter-spacing:1px;font-weight:500;">{{ $menu['section'] }}</div>
                        @endif
                    @elseif ($menuVisible($menu))
                        <div class="px-2 @if (($menus[$index - 1]['section'] ?? null) === null) space-y-0.5 @endif">
                            @if (isset($menu['children']))
                                <div class="rounded-lg px-2.5 py-2" style="{{ $isActiveMenu($menu) ? 'background:rgba(254,80,0,0.08);color:#fe5000;' : 'color:#a1a1aa;' }}">
                                    <div class="flex items-center gap-2.5 text-sm">
                                        <i class="ti ti-{{ $menu['icon'] }}" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
                                        <span class="flex-1">{{ $menu['label'] }}</span>
                                    </div>
                                    <div style="margin-left:30px;margin-top:8px;display:grid;gap:4px;">
                                        @foreach ($menu['children'] as $child)
                                            @if ($user && $user->canAccessMenuFromConfig($child))
                                                <a href="{{ $routeUrl($child) }}" wire:navigate style="{{ $childLinkStyle($isActiveMenu($child)) }}">
                                                    {{ $child['label'] }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $routeUrl($menu) }}" wire:navigate class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm transition-colors" style="{{ $isActiveMenu($menu) ? 'background:rgba(254,80,0,0.1);color:#fe5000;' : 'color:#a1a1aa;' }}">
                                    <i class="ti ti-{{ $menu['icon'] }}" style="font-size:16px;width:20px;text-align:center;" aria-hidden="true"></i>
                                    <span class="flex-1">{{ $menu['label'] }}</span>
                                    @if ($menu['slug'] === 'kaderisasi' && $totalKader > 0)
                                        <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:rgba(255,255,255,0.15);font-weight:500;">{{ number_format($totalKader) }}</span>
                                    @elseif ($menu['slug'] === 'sapa-warga' && $totalKontak > 0)
                                        <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:rgba(255,255,255,0.15);font-weight:500;">{{ number_format($totalKontak) }}</span>
                                    @elseif (in_array($menu['slug'], ['event', 'event-view'], true) && $pendingCount > 0 && ! $user->isKader())
                                        <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#d9770630;color:#d97706;font-weight:500;">{{ $pendingCount }}</span>
                                    @elseif ($menu['slug'] === 'aspirasi' && $belumAssignAspirasi > 0)
                                        <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#0ea5e920;color:#38bdf8;font-weight:500;">{{ $belumAssignAspirasi }}</span>
                                    @endif
                                </a>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <div style="border-top:0.5px solid #27272a;padding:8px;">
                <div style="padding:0 0 8px;">
                    <flux:dropdown position="top" align="start">
                        <button type="button" class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm hover:bg-white/5" style="color:#a1a1aa;">
                            <div style="position:relative;width:34px;height:34px;border-radius:10px;background:#18181b;display:flex;align-items:center;justify-content:center;color:#f4f4f5;">
                                <i class="ti ti-bell" style="font-size:16px;" aria-hidden="true"></i>
                                @if ($unreadReminders > 0)
                                    <span style="position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;padding:0 4px;border-radius:999px;background:#ef4444;color:white;font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center;">{{ $unreadReminders }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <div style="font-size:12px;color:#e4e4e7;font-weight:500;">Reminder Aspirasi</div>
                                <div style="font-size:10px;color:#52525b;">{{ $unreadReminders > 0 ? $unreadReminders.' belum dibaca' : 'Tidak ada reminder baru' }}</div>
                            </div>
                        </button>

                        <flux:menu class="w-[320px] !rounded-xl !border-zinc-800 !bg-[#1c1c1f] !text-zinc-200">
                            @forelse ($reminderItems as $reminder)
                                <flux:menu.item href="{{ route('aspirasi.reminders.read', $reminder) }}" wire:navigate class="!items-start !py-3">
                                    <div>
                                        <div style="font-size:12px;line-height:1.45;color:#f4f4f5;">{{ $reminder->pesan }}</div>
                                        <div style="font-size:10px;color:#71717a;margin-top:4px;">{{ $reminder->created_at?->diffForHumans() }}</div>
                                    </div>
                                </flux:menu.item>
                            @empty
                                <div style="padding:14px 16px;font-size:12px;color:#71717a;">Belum ada reminder aspirasi.</div>
                            @endforelse
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <flux:dropdown position="top" align="start">
                    <button type="button" class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm hover:bg-white/5" style="color:#a1a1aa;">
                        <div class="flex items-center justify-center rounded-lg" style="width:34px;height:34px;background:#fe5000;color:white;font-size:11px;font-weight:500;flex-shrink:0;">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <div style="font-size:12px;color:#e4e4e7;font-weight:500;" class="truncate">{{ auth()->user()->name }}</div>
                            <div style="font-size:10px;color:#52525b;" class="truncate">{{ auth()->user()->email }}</div>
                        </div>
                        <i class="ti ti-selector" style="font-size:14px;color:#52525b;" aria-hidden="true"></i>
                    </button>

                    <flux:menu class="w-[220px] !rounded-xl !border-zinc-800 !bg-[#1c1c1f] !text-zinc-200">
                        <flux:menu.item href="{{ route('settings.profile') }}" icon="user-circle" wire:navigate>Profil</flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:sidebar>

        <flux:header class="lg:hidden !bg-[#111113] !text-white" style="border-bottom:0.5px solid #27272a;">
            <flux:sidebar.toggle class="lg:hidden !text-zinc-300" icon="bars-2" inset="left" />
            <flux:spacer />
            <div style="font-size:13px;font-weight:500;color:white;">Bekasi Hebat</div>
            <flux:spacer />
            <div class="flex items-center justify-center rounded-lg" style="width:28px;height:28px;background:#fe5000;color:white;font-size:10px;font-weight:500;">
                {{ auth()->user()->initials() }}
            </div>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
