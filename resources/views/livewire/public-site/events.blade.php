@php
    $jenisColors = [
        'baksos' => ['bg' => '#fff7ed', 'text' => '#9a3412', 'bar' => '#ea580c', 'icon' => 'ti-heart-handshake'],
        'pengajian' => ['bg' => '#faf5ff', 'text' => '#6b21a8', 'bar' => '#9333ea', 'icon' => 'ti-book'],
        'senam' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'bar' => '#16a34a', 'icon' => 'ti-stretching'],
        'diskusi' => ['bg' => '#f0f9ff', 'text' => '#0369a1', 'bar' => '#0284c7', 'icon' => 'ti-message-circle'],
        'pelatihan' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'bar' => '#2563eb', 'icon' => 'ti-school'],
        'musyawarah' => ['bg' => '#fffbeb', 'text' => '#92400e', 'bar' => '#d97706', 'icon' => 'ti-users-group'],
        'bedah_rumah' => ['bg' => '#fff1f2', 'text' => '#9f1239', 'bar' => '#e11d48', 'icon' => 'ti-home-edit'],
        'kesehatan' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'bar' => '#059669', 'icon' => 'ti-heartbeat'],
        'pendidikan' => ['bg' => '#ecfeff', 'text' => '#155e75', 'bar' => '#0891b2', 'icon' => 'ti-school'],
        'lainnya' => ['bg' => '#f4f4f5', 'text' => '#52525b', 'bar' => '#a1a1aa', 'icon' => 'ti-calendar-event'],
    ];
@endphp

<style>
    @media (max-width: 768px) {
        .ev-grid { grid-template-columns: 1fr !important; }
        .ev-featured { grid-column: span 1 !important; }
        .ev-featured-inner { grid-template-columns: 1fr !important; }
    }
</style>

<section class="min-h-screen bg-zinc-50 pb-16">
    <div class="container">
        <div style="background:white; border-radius:16px; padding:24px; margin-top:24px; border:1px solid #f4f4f5;">
            <h1 style="font-size:30px; font-weight:700; color:#18181b; line-height:1.2;">Event & kegiatan</h1>
            <p style="max-width:680px; font-size:17px; line-height:1.8; color:#71717a; margin-top:8px;">Kegiatan DPD PKS Kabupaten Bekasi yang terbuka untuk seluruh warga. Daftar langsung dari halaman ini.</p>

            <div style="display:flex; gap:8px; margin-top:16px; flex-wrap:wrap;">
                @foreach ([
                    'mendatang' => 'Mendatang',
                    'semua' => 'Semua',
                    'selesai' => 'Selesai',
                ] as $key => $label)
                    <button
                        wire:click="$set('filter', '{{ $key }}')"
                        type="button"
                        style="{{ $filter === $key
                            ? 'background:#ea580c; color:white; border:1px solid #ea580c;'
                            : 'background:white; color:#52525b; border:1px solid #e4e4e7;' }} border-radius:999px; padding:6px 16px; font-size:12px; font-weight:600; transition:.2s;"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="ev-grid" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-top:20px;">
            @forelse($this->events as $index => $event)
                @php
                    $c = $jenisColors[$event->jenis] ?? $jenisColors['lainnya'];
                    $selesai = in_array($event->status, ['completed', 'selesai']);
                    $isFeatured = $index === 0 && $filter === 'mendatang' && ! $selesai;
                    $regCount = (int) ($event->registrations_count ?? 0);
                    $regTarget = (int) ($event->peserta_target ?: 100);
                    $regPct = min(100, round($regCount / max($regTarget, 1) * 100));
                @endphp

                @if($isFeatured)
                    <article class="ev-featured" style="grid-column:span 2; background:white; border-radius:12px; border:1px solid #e4e4e7; overflow:hidden;">
                        <div class="ev-featured-inner" style="display:grid; grid-template-columns:5fr 7fr;">
                            <div style="background:{{ $c['bg'] }}; display:flex; align-items:center; justify-content:center; padding:24px; position:relative; min-height:160px;">
                                @if($event->cover_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="text-align:center;">
                                        <i class="ti {{ $c['icon'] }}" style="font-size:40px; color:{{ $c['text'] }}; opacity:.3;"></i>
                                        <div style="font-size:10px; color:{{ $c['text'] }}; margin-top:6px; font-weight:500; text-transform:uppercase; letter-spacing:1px;">{{ $event->jenis_label }}</div>
                                    </div>
                                @endif

                                <div style="position:absolute; top:10px; right:10px; background:#dcfce7; color:#14532d; padding:2px 8px; border-radius:6px; font-size:10px; font-weight:500;">
                                    Pendaftaran dibuka
                                </div>
                            </div>

                            <div style="padding:20px;">
                                <span style="display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:500; background:{{ $c['bg'] }}; color:{{ $c['text'] }};">
                                    <i class="ti {{ $c['icon'] }}" style="font-size:11px;"></i>
                                    {{ $event->jenis_label }}
                                </span>

                                <h2 style="font-size:24px; font-weight:600; color:#18181b; margin-top:8px; line-height:1.3;">{{ $event->judul }}</h2>

                                @if($event->deskripsi)
                                    <p style="max-width:680px; font-size:17px; color:#71717a; margin-top:8px; line-height:1.8;">{{ \Illuminate\Support\Str::limit(strip_tags((string) $event->deskripsi), 160) }}</p>
                                @endif

                                <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:10px; font-size:11px; color:#a1a1aa;">
                                    <span><i class="ti ti-calendar" style="font-size:12px;"></i> {{ $event->tanggal_mulai?->translatedFormat('d M Y') }}</span>
                                    <span><i class="ti ti-clock" style="font-size:12px;"></i> {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB</span>
                                    @if($event->lokasi)
                                        <span><i class="ti ti-map-pin" style="font-size:12px;"></i> {{ \Illuminate\Support\Str::limit($event->lokasi, 35) }}</span>
                                    @endif
                                </div>

                                <div style="display:flex; align-items:center; gap:8px; margin-top:14px; flex-wrap:wrap;">
                                    <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate style="display:inline-flex; align-items:center; gap:4px; padding:7px 16px; background:#ea580c; color:white; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;">
                                        <i class="ti ti-external-link" style="font-size:13px;"></i> Daftar sekarang
                                    </a>
                                    <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate style="display:inline-flex; align-items:center; gap:4px; padding:7px 12px; border:1px solid #e4e4e7; border-radius:8px; font-size:12px; font-weight:500; color:#52525b; text-decoration:none;">
                                        <i class="ti ti-info-circle" style="font-size:13px;"></i> Detail
                                    </a>
                                    <div style="margin-left:auto; display:flex; align-items:center; gap:6px; font-size:11px; color:#a1a1aa;">
                                        <i class="ti ti-users" style="font-size:13px;"></i>
                                        {{ number_format($regCount) }} / {{ number_format($regTarget) }}
                                        <div style="width:56px; height:4px; border-radius:2px; background:#e4e4e7; overflow:hidden;">
                                            <div style="width:{{ $regPct }}%; height:100%; border-radius:2px; background:{{ $c['bar'] }};"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @else
                    <article style="background:white; border-radius:12px; border:1px solid #e4e4e7; overflow:hidden; {{ $selesai ? 'opacity:.6;' : '' }}">
                        <div style="display:flex; gap:12px; padding:14px; position:relative; min-height:92px;">
                            <div style="width:48px; height:52px; flex-shrink:0; display:flex; flex-direction:column; align-items:center; justify-content:center; border-radius:10px; background:{{ $selesai ? '#f4f4f5' : $c['bg'] }};">
                                <div style="font-size:20px; font-weight:600; line-height:1; color:{{ $selesai ? '#a1a1aa' : $c['text'] }};">
                                    {{ $event->tanggal_mulai?->format('d') }}
                                </div>
                                <div style="font-size:9px; text-transform:uppercase; letter-spacing:.5px; margin-top:2px; color:{{ $selesai ? '#a1a1aa' : $c['text'] }};">
                                    {{ $event->tanggal_mulai?->translatedFormat('M') }}
                                </div>
                            </div>

                            <div style="flex:1; min-width:0;">
                                <span style="display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:10px; font-size:10px; font-weight:500; background:{{ $selesai ? '#f4f4f5' : $c['bg'] }}; color:{{ $selesai ? '#a1a1aa' : $c['text'] }};">
                                    <i class="ti {{ $c['icon'] }}" style="font-size:10px;"></i>
                                    {{ $event->jenis_label }}
                                </span>
                                <h3 style="font-size:16px; font-weight:600; line-height:1.35; margin-top:4px; color:{{ $selesai ? '#a1a1aa' : '#18181b' }}; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                    {{ $event->judul }}
                                </h3>
                                <div style="margin-top:4px; font-size:11px; color:#a1a1aa; display:flex; flex-direction:column; gap:1px;">
                                    <span><i class="ti ti-clock" style="font-size:11px;"></i> {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB</span>
                                    @if($event->lokasi)
                                        <span><i class="ti ti-map-pin" style="font-size:11px;"></i> {{ \Illuminate\Support\Str::limit($event->lokasi, 40) }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($selesai)
                                <div style="position:absolute; top:10px; right:10px; background:#f4f4f5; color:#a1a1aa; padding:2px 7px; border-radius:6px; font-size:9px; font-weight:500;">
                                    Selesai
                                </div>
                            @endif
                        </div>

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 14px; border-top:1px solid #f4f4f5; gap:10px;">
                            @if($selesai)
                                <div style="display:flex; align-items:center; gap:4px; font-size:11px; color:#a1a1aa;">
                                    <i class="ti ti-users" style="font-size:12px;"></i>
                                    {{ number_format($regCount) }} hadir
                                </div>
                                <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate style="display:inline-flex; align-items:center; gap:3px; padding:4px 10px; border:1px solid #e4e4e7; border-radius:6px; font-size:11px; font-weight:500; color:#71717a; text-decoration:none;">
                                    <i class="ti ti-photo" style="font-size:11px;"></i> Lihat galeri
                                </a>
                            @else
                                <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#a1a1aa;">
                                    <i class="ti ti-users" style="font-size:12px;"></i>
                                    {{ number_format($regCount) }} pendaftar
                                    <div style="width:56px; height:4px; border-radius:2px; background:#e4e4e7; overflow:hidden;">
                                        <div style="width:{{ $regPct }}%; height:100%; border-radius:2px; background:{{ $c['bar'] }};"></div>
                                    </div>
                                </div>
                                <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate style="display:inline-flex; align-items:center; gap:3px; padding:4px 12px; background:#ea580c; color:white; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none;">
                                    <i class="ti ti-external-link" style="font-size:11px;"></i> Daftar
                                </a>
                            @endif
                        </div>
                    </article>
                @endif
            @empty
                <div style="grid-column:span 2; text-align:center; padding:40px; background:white; border-radius:12px; border:1px dashed #d4d4d8; color:#a1a1aa;">
                    <i class="ti ti-calendar-off" style="font-size:32px; color:#d4d4d8;"></i>
                    <p style="margin-top:8px; font-size:13px;">Belum ada event untuk filter ini.</p>
                </div>
            @endforelse
        </div>

        @if ($this->events->hasPages())
            <div style="margin-top:24px;">
                {{ $this->events->links() }}
            </div>
        @endif
    </div>
</section>
