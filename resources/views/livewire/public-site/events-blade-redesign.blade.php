{{-- ============================================================
     REPLACE: resources/views/livewire/public-site/events.blade.php
     
     Redesign: compact cards, 2-col grid, category colors,
     date badges, registration bars, featured event.
     ============================================================ --}}

@php
    // Color mapping per jenis event
    $jenisColors = [
        'baksos'      => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'date_bg' => 'bg-orange-50', 'date_text' => 'text-orange-700', 'bar' => 'bg-orange-500', 'icon' => 'ti-heart-handshake'],
        'pengajian'   => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'date_bg' => 'bg-purple-50', 'date_text' => 'text-purple-700', 'bar' => 'bg-purple-500', 'icon' => 'ti-book'],
        'senam'       => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'date_bg' => 'bg-green-50',  'date_text' => 'text-green-700',  'bar' => 'bg-green-500',  'icon' => 'ti-stretching'],
        'diskusi'     => ['bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'date_bg' => 'bg-sky-50',    'date_text' => 'text-sky-700',    'bar' => 'bg-sky-500',    'icon' => 'ti-message-circle'],
        'pelatihan'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'date_bg' => 'bg-blue-50',   'date_text' => 'text-blue-700',   'bar' => 'bg-blue-500',   'icon' => 'ti-school'],
        'musyawarah'  => ['bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'date_bg' => 'bg-amber-50',  'date_text' => 'text-amber-700',  'bar' => 'bg-amber-500',  'icon' => 'ti-users-group'],
        'bedah_rumah' => ['bg' => 'bg-rose-50',   'text' => 'text-rose-700',   'date_bg' => 'bg-rose-50',   'date_text' => 'text-rose-700',   'bar' => 'bg-rose-500',   'icon' => 'ti-home-edit'],
        'kesehatan'   => ['bg' => 'bg-emerald-50','text' => 'text-emerald-700','date_bg' => 'bg-emerald-50','date_text' => 'text-emerald-700','bar' => 'bg-emerald-500','icon' => 'ti-heartbeat'],
        'pendidikan'  => ['bg' => 'bg-cyan-50',   'text' => 'text-cyan-700',   'date_bg' => 'bg-cyan-50',   'date_text' => 'text-cyan-700',   'bar' => 'bg-cyan-500',   'icon' => 'ti-school'],
        'lainnya'     => ['bg' => 'bg-zinc-100',  'text' => 'text-zinc-600',   'date_bg' => 'bg-zinc-100',  'date_text' => 'text-zinc-600',   'bar' => 'bg-zinc-400',   'icon' => 'ti-calendar-event'],
    ];

    $isSelesai = fn($event) => in_array($event->status, ['completed', 'selesai']);
@endphp

<section class="min-h-screen bg-zinc-50 pb-16">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">

        {{-- ======= HERO ======= --}}
        <div class="rounded-2xl bg-white px-6 py-8 mt-6 border border-zinc-100">
            <h1 class="text-2xl font-semibold text-zinc-900">Event & kegiatan</h1>
            <p class="mt-1 text-sm text-zinc-500 max-w-lg">
                Kegiatan DPD PKS Kabupaten Bekasi yang terbuka untuk seluruh warga. Daftar langsung dari halaman ini.
            </p>

            {{-- Filter tabs --}}
            <div class="mt-4 flex gap-2">
                @foreach ([
                    'mendatang' => 'Mendatang',
                    'semua' => 'Semua',
                    'selesai' => 'Selesai',
                ] as $key => $label)
                    <button
                        wire:click="$set('filter', '{{ $key }}')"
                        type="button"
                        class="rounded-full px-4 py-1.5 text-xs font-medium transition
                        {{ $filter === $key
                            ? 'bg-orange-600 text-white'
                            : 'border border-zinc-200 bg-white text-zinc-500 hover:border-zinc-300 hover:text-zinc-900' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ======= EVENT GRID ======= --}}
        <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2">
            @forelse ($this->events as $index => $event)
                @php
                    $c = $jenisColors[$event->jenis] ?? $jenisColors['lainnya'];
                    $selesai = $isSelesai($event);
                    $isFeatured = $index === 0 && $filter === 'mendatang' && !$selesai;
                    $regCount = $event->registrations_count ?? 0;
                    $regTarget = $event->peserta_target ?: 100;
                    $regPct = min(100, round($regCount / $regTarget * 100));
                @endphp

                {{-- FEATURED (event pertama di tab mendatang) --}}
                @if ($isFeatured)
                    <article class="md:col-span-2 overflow-hidden rounded-xl border border-zinc-200 bg-white transition hover:shadow-md">
                        <div class="grid md:grid-cols-12">
                            {{-- Visual area --}}
                            <div class="md:col-span-5 relative flex items-center justify-center p-6 {{ $c['bg'] }}">
                                @if ($event->cover_image)
                                    <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" class="absolute inset-0 h-full w-full object-cover">
                                @else
                                    <div class="text-center">
                                        <i class="ti {{ $c['icon'] }} text-5xl {{ $c['text'] }} opacity-30"></i>
                                        <div class="mt-2 text-xs font-medium {{ $c['text'] }} uppercase tracking-wider">{{ $event->jenis_label }}</div>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 rounded-md bg-green-50 px-2 py-0.5 text-[10px] font-medium text-green-700">
                                    Pendaftaran dibuka
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="md:col-span-7 p-5">
                                <div class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium {{ $c['bg'] }} {{ $c['text'] }}">
                                    <i class="ti {{ $c['icon'] }} text-xs"></i>
                                    {{ $event->jenis_label }}
                                </div>
                                <h2 class="mt-2 text-lg font-semibold text-zinc-900 leading-snug">{{ $event->judul }}</h2>
                                @if ($event->deskripsi)
                                    <p class="mt-2 text-xs text-zinc-500 leading-relaxed line-clamp-3">{{ Str::limit(strip_tags($event->deskripsi), 180) }}</p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-calendar text-xs"></i>
                                        {{ $event->tanggal_mulai?->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-clock text-xs"></i>
                                        {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB
                                    </span>
                                    @if ($event->lokasi)
                                        <span class="flex items-center gap-1">
                                            <i class="ti ti-map-pin text-xs"></i>
                                            {{ Str::limit($event->lokasi, 40) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Actions + registration --}}
                                <div class="mt-4 flex items-center gap-3">
                                    <a href="{{ route('public.events.show', $event->slug) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-orange-700">
                                        <i class="ti ti-external-link text-sm"></i> Daftar sekarang
                                    </a>
                                    <a href="{{ route('public.events.show', $event->slug) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-2 text-xs font-medium text-zinc-600 transition hover:border-zinc-300">
                                        <i class="ti ti-info-circle text-sm"></i> Detail
                                    </a>
                                    <div class="ml-auto flex items-center gap-2 text-xs text-zinc-400">
                                        <i class="ti ti-users text-sm"></i>
                                        <span>{{ number_format($regCount) }} / {{ number_format($regTarget) }}</span>
                                        <div class="w-14 h-1 rounded-full bg-zinc-200 overflow-hidden">
                                            <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $regPct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                {{-- REGULAR CARD --}}
                @else
                    <article class="overflow-hidden rounded-xl border border-zinc-200 bg-white transition hover:shadow-md {{ $selesai ? 'opacity-60' : '' }}">
                        {{-- Top: date + content --}}
                        <div class="relative flex gap-3 p-4">
                            {{-- Date badge --}}
                            <div class="w-12 h-[52px] flex-shrink-0 flex flex-col items-center justify-center rounded-lg {{ $selesai ? 'bg-zinc-100' : $c['date_bg'] }}">
                                <div class="text-lg font-semibold leading-none {{ $selesai ? 'text-zinc-400' : $c['date_text'] }}">
                                    {{ $event->tanggal_mulai?->format('d') }}
                                </div>
                                <div class="text-[9px] uppercase tracking-wider mt-0.5 {{ $selesai ? 'text-zinc-400' : $c['date_text'] }}">
                                    {{ $event->tanggal_mulai?->translatedFormat('M') }}
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium {{ $selesai ? 'bg-zinc-100 text-zinc-400' : $c['bg'] . ' ' . $c['text'] }}">
                                    <i class="ti {{ $c['icon'] }} text-[10px]"></i>
                                    {{ $event->jenis_label }}
                                </div>
                                <h3 class="mt-1 text-sm font-semibold leading-snug line-clamp-2 {{ $selesai ? 'text-zinc-400' : 'text-zinc-900' }}">
                                    {{ $event->judul }}
                                </h3>
                                <div class="mt-1.5 flex flex-col gap-0.5 text-[11px] text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-clock text-[11px]"></i>
                                        {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB
                                    </span>
                                    @if ($event->lokasi)
                                        <span class="flex items-center gap-1">
                                            <i class="ti ti-map-pin text-[11px]"></i>
                                            {{ Str::limit($event->lokasi, 45) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status badge (selesai) --}}
                            @if ($selesai)
                                <div class="absolute top-3 right-3 rounded-md bg-zinc-100 px-2 py-0.5 text-[9px] font-medium text-zinc-400">
                                    Selesai
                                </div>
                            @endif
                        </div>

                        {{-- Bottom bar --}}
                        <div class="flex items-center justify-between border-t border-zinc-100 px-4 py-2.5">
                            @if ($selesai)
                                <div class="flex items-center gap-1.5 text-[11px] text-zinc-400">
                                    <i class="ti ti-users text-xs"></i>
                                    <span>{{ number_format($event->peserta_count ?? $regCount) }} hadir</span>
                                </div>
                                <a href="{{ route('public.events.show', $event->slug) }}" class="inline-flex items-center gap-1 rounded-md border border-zinc-200 px-2.5 py-1 text-[11px] font-medium text-zinc-500 transition hover:border-zinc-300">
                                    <i class="ti ti-photo text-xs"></i> Lihat galeri
                                </a>
                            @else
                                <div class="flex items-center gap-2 text-[11px] text-zinc-400">
                                    <i class="ti ti-users text-xs"></i>
                                    <span>{{ number_format($regCount) }} pendaftar</span>
                                    <div class="w-14 h-1 rounded-full bg-zinc-200 overflow-hidden">
                                        <div class="h-full rounded-full {{ $c['bar'] }}" style="width: {{ $regPct }}%"></div>
                                    </div>
                                </div>
                                <a href="{{ route('public.events.show', $event->slug) }}" class="inline-flex items-center gap-1 rounded-md bg-orange-600 px-3 py-1 text-[11px] font-semibold text-white transition hover:bg-orange-700">
                                    <i class="ti ti-external-link text-xs"></i> Daftar
                                </a>
                            @endif
                        </div>
                    </article>
                @endif

            @empty
                <div class="md:col-span-2 rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center text-sm text-zinc-400">
                    <i class="ti ti-calendar-off text-3xl text-zinc-300"></i>
                    <p class="mt-2">Belum ada event untuk filter ini.</p>
                </div>
            @endforelse
        </div>

        {{-- ======= PAGINATION ======= --}}
        @if ($this->events->hasPages())
            <div class="mt-6">
                {{ $this->events->links() }}
            </div>
        @endif

    </div>
</section>
