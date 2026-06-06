<div class="max-w-[720px] mx-auto px-4 py-6">
    <div class="mb-5">
        <div class="text-xs text-zinc-500">Selamat datang kembali,</div>
        <h1 class="text-2xl font-medium text-zinc-900 mt-0.5">{{ auth()->user()->name }}</h1>
    </div>

    @unless($profile['is_complete'])
        <div class="rounded-xl border p-4 mb-5 flex items-center justify-between gap-3"
             style="background: linear-gradient(95deg, #fff7f1 0%, #fce4d3 100%); border-color: #fce4ce;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #fe5000;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-900">Profil Anda {{ $profile['percentage'] }}% lengkap</div>
                    <div class="text-xs text-zinc-600">Lengkapi data lahir dan alamat untuk mulai daftar event.</div>
                </div>
            </div>
            <a href="{{ route('profile.complete') }}" class="px-3 py-1.5 text-white text-xs font-medium rounded-md whitespace-nowrap" style="background: #fe5000;">
                Lengkapi
            </a>
        </div>
    @endunless

    <div class="mb-2.5">
        <div class="text-[10px] font-medium uppercase tracking-wider mb-0.5" style="color: #fe5000;">Kartu Anggota</div>
        <div class="text-sm text-zinc-500">Tunjukkan saat check-in event</div>
    </div>

    <div class="rounded-2xl p-5 text-white mb-6 relative overflow-hidden"
         style="background: linear-gradient(135deg, #fe5000 0%, #d94400 100%); box-shadow: 0 4px 16px rgba(254, 80, 0, 0.18);">
        <div class="absolute -right-5 -top-5 w-28 h-28 rounded-full" style="background: rgba(255,255,255,0.08);"></div>

        <div class="relative flex justify-between items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-3">
                    <div class="w-5 h-5 rounded-md bg-white/25 flex items-center justify-center">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a4 4 0 014 4v1h2a1 1 0 011 1v3a7 7 0 11-14 0V8a1 1 0 011-1h2V6a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div class="text-[11px] font-medium">Bekasi Hebat!</div>
                </div>

                <div class="text-[9px] opacity-85">No. Anggota</div>
                <div class="text-base font-medium font-mono tracking-wide mb-2.5">{{ $cardData['member_number'] }}</div>

                <div class="text-[9px] opacity-85">Nama</div>
                <div class="text-sm font-medium mb-2.5">{{ $cardData['name'] }}</div>

                <div class="flex gap-4">
                    <div>
                        <div class="text-[8px] opacity-85">Wilayah</div>
                        <div class="text-[10px] font-medium">{{ $cardData['wilayah'] }}</div>
                    </div>
                    <div>
                        <div class="text-[8px] opacity-85">Sejak</div>
                        <div class="text-[10px] font-medium">{{ $cardData['joined_at'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-1.5 rounded-lg flex-shrink-0">
                <div class="w-20 h-20 overflow-hidden">
                    {!! $cardData['qr_svg'] !!}
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-2.5">
        <div>
            <div class="text-[10px] font-medium uppercase tracking-wider" style="color: #fe5000;">Event Saya</div>
            <div class="text-sm text-zinc-500">Kegiatan yang Anda ikuti</div>
        </div>
        <a href="{{ route('events.index') }}" wire:navigate class="text-xs" style="color: #fe5000;">Lihat semua</a>
    </div>

    <div class="space-y-2 mb-6">
        @forelse($myEvents as $registration)
            <div class="bg-white border border-zinc-200 rounded-xl p-3 flex items-center gap-3">
                <div class="w-11 h-11 rounded-lg flex flex-col items-center justify-center flex-shrink-0"
                     style="background: #ffe4d3;">
                    <div class="text-[8px] font-medium uppercase leading-none" style="color: #993c1d;">
                        {{ $registration->event->starts_at->isoFormat('MMM') }}
                    </div>
                    <div class="text-base font-medium leading-tight" style="color: #fe5000;">
                        {{ $registration->event->starts_at->format('d') }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-zinc-900 truncate">{{ $registration->event->title }}</div>
                    <div class="text-[10px] text-zinc-500">
                        {{ $registration->event->starts_at->format('H:i') }} · {{ $registration->event->location_name }}
                    </div>
                </div>
                @php
                    $status = $registration->status?->value ?? 'registered';
                    $statusBadge = match($status) {
                        'confirmed', 'attended' => ['bg' => '#dcfce7', 'text' => '#14532d', 'label' => 'Terdaftar'],
                        'registered' => ['bg' => '#fce4ce', 'text' => '#993c1d', 'label' => 'Menunggu'],
                        'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Dibatalkan'],
                        default => ['bg' => '#f5f5f5', 'text' => '#666', 'label' => $status],
                    };
                @endphp
                <span class="text-[9px] font-medium px-2 py-0.5 rounded-full whitespace-nowrap"
                      style="background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['text'] }};">
                    {{ $statusBadge['label'] }}
                </span>
            </div>
        @empty
            <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-6 text-center">
                <div class="text-sm text-zinc-600 mb-2">Belum ada event yang diikuti</div>
                <a href="{{ route('events.index') }}" wire:navigate class="text-xs font-medium" style="color: #fe5000;">Lihat kegiatan tersedia</a>
            </div>
        @endforelse
    </div>

    @if ($upcomingEvents->isNotEmpty() || $pendingApproval > 0)
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2.5">
                <div>
                    <div class="text-[10px] font-medium uppercase tracking-wider" style="color: #fe5000;">Event Mendatang</div>
                    <div class="text-sm text-zinc-500">Agenda yang sudah disetujui</div>
                </div>
                <a href="{{ route('events.index') }}" wire:navigate class="text-xs" style="color: #fe5000;">Lihat semua</a>
            </div>

            @if ($pendingApproval > 0)
                <div class="rounded-xl border p-3 mb-3 flex items-center gap-2" style="background:#fff7ed;border-color:#fed7aa;">
                    <i class="ti ti-alert-circle" style="font-size:16px;color:#d97706;" aria-hidden="true"></i>
                    <span class="text-xs" style="color:#b45309;">{{ $pendingApproval }} event menunggu approval</span>
                    <a href="{{ route('events.index', ['status' => 'menunggu_approval']) }}" wire:navigate class="ml-auto text-xs font-medium" style="color:#fe5000;">Review</a>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-2.5">
                @foreach ($upcomingEvents as $event)
                    <a href="{{ route('events.detail', $event) }}" wire:navigate class="bg-white border border-zinc-200 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg flex flex-col items-center justify-center flex-shrink-0" style="background:#ffe4d3;">
                            <div class="text-[8px] font-medium uppercase leading-none" style="color:#993c1d;">
                                {{ $event->tanggal_mulai?->isoFormat('MMM') }}
                            </div>
                            <div class="text-base font-medium leading-tight" style="color:#fe5000;">
                                {{ $event->tanggal_mulai?->format('d') }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-zinc-900 truncate">{{ $event->judul }}</div>
                            <div class="text-[10px] text-zinc-500">
                                {{ $event->tanggal_mulai?->format('H:i') }} · {{ $event->lokasi_desa ?? $event->lokasi }}
                            </div>
                        </div>
                        <span class="text-[9px] font-medium px-2 py-0.5 rounded-full whitespace-nowrap" style="background:{{ $event->status_config['bg'] }};color:{{ $event->status_config['color'] }};">
                            {{ $event->status_config['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between mb-2.5">
        <div>
            <div class="text-[10px] font-medium uppercase tracking-wider" style="color: #fe5000;">Profil Saya</div>
            <div class="text-sm text-zinc-500">Data diri Anda</div>
        </div>
        <a href="{{ route('profile.complete') }}" class="text-xs" style="color: #fe5000;">
            {{ $profile['is_complete'] ? 'Edit' : 'Lengkapi' }}
        </a>
    </div>

    <div class="bg-white border border-zinc-200 rounded-xl p-4">
        <div class="space-y-2.5">
            <div class="flex justify-between items-center">
                <span class="text-xs text-zinc-500">Email</span>
                <span class="text-sm text-zinc-900">{{ auth()->user()->email }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-zinc-500">No. WhatsApp</span>
                <span class="text-sm text-zinc-900">{{ auth()->user()->phone }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-zinc-500">Wilayah</span>
                <span class="text-sm {{ auth()->user()->kelurahan_code ? 'text-zinc-900' : 'text-zinc-400 italic' }}">
                    {{ $cardData['wilayah'] }}
                </span>
            </div>
        </div>
    </div>
</div>
