<div class="container py-8">
    <div class="mb-6 flex justify-between items-start">
        <div>
            <div class="text-xs text-zinc-500">Selamat datang kembali,</div>
            <h1 class="text-2xl font-medium text-zinc-900 mt-0.5">{{ auth()->user()->name }}</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 rounded-lg text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </button>
        </form>
    </div>

    <!-- 2 Columns Grid Layout -->
    <div class="dashboard-grid">
        <style>
            .dashboard-grid {
                display: flex;
                flex-direction: column;
                gap: 30px;
            }
            @media (min-width: 768px) {
                .dashboard-grid {
                    display: grid;
                    grid-template-columns: 5.5fr 6.5fr;
                    gap: 30px;
                    align-items: start;
                }
            }
        </style>

        <!-- Left Column: Kartu Anggota & Profil Saya -->
        <div class="space-y-6">
            {{-- KARTU ANGGOTA SECTION --}}
            <div>
                <div class="mb-2.5">
                    <div class="text-[10px] font-medium uppercase tracking-wider mb-0.5" style="color:#fe5000;">Kartu Anggota</div>
                    <div class="text-sm text-zinc-500">Tunjukkan saat check-in event</div>
                </div>

                {{-- KARTU UTAMA --}}
                <div style="width:100%;height:auto;aspect-ratio:560/340;border-radius:16px;position:relative;overflow:hidden;background:white;box-shadow:0 8px 32px rgba(0,0,0,.12);">

                    {{-- Diagonal stripe background --}}
                    <div style="position:absolute;inset:0;background:repeating-linear-gradient(135deg,transparent,transparent 8px,rgba(0,0,0,.015) 8px,rgba(0,0,0,.015) 16px);"></div>

                    {{-- Bendera merah putih pojok kanan atas --}}
                    <div style="position:absolute;top:0;right:0;width:110px;height:75px;overflow:hidden;z-index:5;">
                        <div style="position:absolute;top:-8px;right:-15px;width:130px;height:42px;background:#dc2626;transform:rotate(12deg);box-shadow:0 2px 8px rgba(220,38,38,.3);"></div>
                        <div style="position:absolute;top:26px;right:-15px;width:130px;height:42px;background:white;transform:rotate(12deg);border-top:1.5px solid #e4e4e7;"></div>
                    </div>

                    {{-- Header: Logo Bekasi Hebat --}}
                    <div style="position:absolute;top:0;left:0;right:100px;padding:12px 14px 8px;z-index:4;">
                        <img src="{{ Storage::url('logo-bekasi-hebat.png') }}"
                             style="height:46px;object-fit:contain;object-position:left;"
                             alt="Bekasi Hebat"
                             onerror="this.style.display='none';document.getElementById('logo-text-{{ $cardData['member_number'] }}').style.display='flex';">
                        {{-- Fallback teks jika logo tidak load --}}
                        <div id="logo-text-{{ $cardData['member_number'] }}" style="display:none;align-items:center;gap:8px;">
                            <div style="width:38px;height:38px;background:#ea580c;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 2C9 6 4 8 4 12c0 4.5 3.5 8 8 8s8-3.5 8-8c0-4-5-6-8-10z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:11px;font-weight:800;color:#18181b;">Kabupaten <span style="color:#ea580c;">Bekasi</span></div>
                                <div style="font-size:13px;font-weight:800;font-style:italic;color:#ea580c;">Hebat!</div>
                                <div style="font-size:7px;color:#71717a;">Komunitas Pelatihan & Pengembangan Potensi Masyarakat</div>
                            </div>
                        </div>
                    </div>

                    {{-- Garis pemisah header --}}
                    <div style="position:absolute;top:70px;left:14px;right:14px;height:0.5px;background:#e4e4e7;z-index:3;"></div>

                    {{-- Body Kartu --}}
                    <div style="position:absolute;top:78px;left:0;right:0;bottom:52px;display:flex;z-index:3;">

                        {{-- KIRI: Foto + Lokasi + QR --}}
                        <div style="width:115px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;padding:8px 8px 0;">

                            {{-- Foto member --}}
                            @if($cardData['foto_path'])
                                <img src="{{ Storage::url($cardData['foto_path']) }}"
                                     style="width:82px;height:95px;border-radius:6px;object-fit:cover;border:1.5px solid #e4e4e7;">
                            @else
                                <div style="width:82px;height:95px;border-radius:6px;background:#f4f4f5;border:1.5px solid #e4e4e7;display:flex;align-items:center;justify-content:center;">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="8" r="4" fill="#d4d4d8"/>
                                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="#d4d4d8" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Lokasi & tanggal --}}
                            <div style="margin-top:5px;text-align:center;">
                                <div style="font-size:9px;color:#71717a;font-weight:500;">Kab. Bekasi</div>
                                <div style="font-size:9px;color:#71717a;">{{ $cardData['joined_at'] }}</div>
                            </div>

                            {{-- QR Code --}}
                            <div style="margin-top:5px;background:#18181b;border-radius:5px;padding:4px;display:inline-flex;">
                                <div style="width:50px;height:50px;overflow:hidden;">
                                    {!! $cardData['qr_svg'] !!}
                                </div>
                            </div>
                        </div>

                        {{-- Garis vertikal pemisah --}}
                        <div style="width:0.5px;background:#e4e4e7;margin:4px 0;flex-shrink:0;"></div>

                        {{-- KANAN: Data member --}}
                        <div style="flex:1;padding:10px 14px;min-width:0;">

                            {{-- Nama besar --}}
                            <div style="font-size:22px;font-weight:800;color:#18181b;line-height:1.1;margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $cardData['name'] }}
                            </div>

                            {{-- Grid data --}}
                            <div style="display:grid;grid-template-columns:90px 1fr;gap:4px 6px;font-size:11.5px;">
                                <div style="color:#71717a;font-weight:500;">No. Anggota</div>
                                <div style="color:#ea580c;font-weight:700;">: {{ $cardData['member_number'] }}</div>

                                <div style="color:#71717a;font-weight:500;">Tanggal Lahir</div>
                                <div style="color:#18181b;font-weight:500;">: {{ $cardData['tanggal_lahir'] }}</div>

                                <div style="color:#71717a;font-weight:500;">Jenis Kelamin</div>
                                <div style="color:#18181b;font-weight:500;">: {{ $cardData['jenis_kelamin'] }}</div>

                                <div style="color:#71717a;font-weight:500;">Alamat</div>
                                <div style="color:#18181b;font-weight:500;line-height:1.4;font-size:10.5px;">: {{ Str::limit($cardData['alamat'], 60) }}</div>

                                <div style="color:#71717a;font-weight:500;">Wilayah</div>
                                <div style="color:#18181b;font-weight:500;">: {{ $cardData['wilayah'] }}{{ $cardData['dapil'] !== '-' ? ', '.$cardData['dapil'] : '' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom waves --}}
                    <div style="position:absolute;bottom:0;left:0;right:0;height:52px;overflow:hidden;z-index:3;">
                        <svg viewBox="0 0 560 52" preserveAspectRatio="none" style="width:100%;height:100%;">
                            <path d="M0 32 Q70 12 140 26 Q210 40 280 20 Q350 0 420 18 Q490 36 560 14 L560 52 L0 52 Z" fill="#ea580c" opacity=".12"/>
                            <path d="M0 38 Q84 20 168 32 Q252 44 336 26 Q420 8 504 28 Q532 36 560 24 L560 52 L0 52 Z" fill="#ea580c" opacity=".25"/>
                            <path d="M0 44 Q93 28 196 40 Q299 52 402 34 Q476 20 560 38 L560 52 L0 52 Z" fill="#ea580c" opacity=".5"/>
                            <path d="M0 47 Q112 35 224 44 Q336 53 448 40 Q504 34 560 44 L560 52 L0 52 Z" fill="#18181b"/>
                        </svg>
                    </div>

                </div>
            </div>

            {{-- PROFIL SAYA SECTION --}}
            <div>
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
                                {{ $cardData['wilayah_full'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Warnings & Events -->
        <div class="space-y-6">
            @unless($profile['is_complete'])
                <div class="rounded-xl border p-4 flex items-center justify-between gap-3"
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

            {{-- EVENT SAYA SECTION --}}
            <div>
                <div class="flex items-center justify-between mb-2.5">
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wider" style="color: #fe5000;">Event Saya</div>
                        <div class="text-sm text-zinc-500">Kegiatan yang Anda ikuti</div>
                    </div>
                    <a href="{{ route('public.events') }}" wire:navigate class="text-xs" style="color: #fe5000;">Lihat semua</a>
                </div>

                <div class="space-y-2">
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
                            <a href="{{ route('public.events') }}" wire:navigate class="text-xs font-medium" style="color: #fe5000;">Lihat kegiatan tersedia</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- EVENT MENDATANG SECTION --}}
            @if ($upcomingEvents->isNotEmpty() || $pendingApproval > 0)
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <div class="text-[10px] font-medium uppercase tracking-wider" style="color: #fe5000;">Event Mendatang</div>
                            <div class="text-sm text-zinc-500">Agenda yang sudah disetujui</div>
                        </div>
                        <a href="{{ route('public.events') }}" wire:navigate class="text-xs" style="color: #fe5000;">Lihat semua</a>
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
                            <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate class="bg-white border border-zinc-200 hover:border-zinc-300 rounded-xl p-3 flex items-center gap-3 transition">
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
        </div>
    </div>
</div>
