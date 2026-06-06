<x-layouts.public
    title="Kegiatan"
    description="Jelajahi daftar kegiatan dan event publik Kabupaten Bekasi Hebat, lengkap dengan status, pencarian, dan akses pendaftaran.">

    @php
        $filter = request('filter', 'semua');
        $search = trim((string) request('search', ''));

        $eventsQuery = \App\Models\Event::query()
            ->where('is_public', true)
            ->where('status', \App\Models\Event::STATUS_DISETUJUI)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('judul', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%')
                        ->orWhere('lokasi', 'like', '%' . $search . '%');
                });
            });

        if ($filter === 'mendatang') {
            $eventsQuery->where('tanggal_mulai', '>=', now());
        } elseif ($filter === 'selesai') {
            $eventsQuery->where('tanggal_mulai', '<', now());
        }

        $events = $eventsQuery
            ->orderByDesc('tanggal_mulai')
            ->paginate(9)
            ->withQueryString();

        $tabs = [
            ['key' => 'semua', 'label' => 'Semua'],
            ['key' => 'mendatang', 'label' => 'Mendatang'],
            ['key' => 'selesai', 'label' => 'Selesai'],
        ];
    @endphp

    <section class="bg-zinc-50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <div class="mb-2 text-xs font-medium uppercase tracking-wider text-orange-600">Kegiatan</div>
                <h1 class="text-3xl font-medium text-zinc-900">Kegiatan & Event</h1>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Ikuti berbagai kegiatan positif bersama komunitas Bekasi Hebat, mulai dari agenda sosial, pengajian, pelatihan, hingga forum warga.
                </p>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <form method="GET" class="mb-6 flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-wrap gap-2">
                    @foreach ($tabs as $tab)
                        @php
                            $isActive = $filter === $tab['key'];
                            $query = ['filter' => $tab['key']];

                            if ($search !== '') {
                                $query['search'] = $search;
                            }
                        @endphp
                        <a
                            href="{{ route('public.events', $query) }}"
                            class="rounded-full px-4 py-2 text-xs font-medium {{ $isActive ? 'bg-orange-600 text-white' : 'border border-zinc-200 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex w-full gap-2 md:max-w-md">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari judul, deskripsi, atau lokasi"
                        class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-900 outline-none placeholder:text-zinc-400 focus:border-orange-300 focus:ring-2 focus:ring-orange-100">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">Cari</button>
                </div>
            </form>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($events as $event)
                    @php
                        $isUpcoming = $event->tanggal_mulai && $event->tanggal_mulai->isFuture();
                        $isRegistrationOpen = $isUpcoming;
                    @endphp
                    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white transition-all hover:border-zinc-300 hover:shadow-sm">
                        <div class="relative flex h-40 items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200">
                            @if ($event->cover_image)
                                <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" class="h-full w-full object-cover">
                            @else
                                <i class="ti ti-calendar-event text-4xl text-orange-300"></i>
                            @endif
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-white/90 px-2 py-1 text-[10px] font-medium text-orange-600">{{ $event->jenis_label }}</span>
                                @if ($isUpcoming)
                                    <span class="rounded-full bg-green-50 px-2 py-1 text-[10px] font-medium text-green-600">Pendaftaran Dibuka</span>
                                @else
                                    <span class="rounded-full bg-zinc-100 px-2 py-1 text-[10px] font-medium text-zinc-500">Selesai</span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="mb-2 flex items-center gap-1 text-xs text-zinc-400">
                                <i class="ti ti-calendar text-xs"></i>
                                {{ $event->tanggal_mulai?->translatedFormat('d F Y, H:i') }}
                            </div>
                            <div class="mb-2 text-sm font-medium text-zinc-900">{{ $event->judul }}</div>
                            <div class="mb-3 flex items-center gap-1 text-xs text-zinc-500">
                                <i class="ti ti-map-pin text-xs"></i>
                                {{ $event->lokasi }}
                            </div>
                            @if ($event->kapasitas > 0)
                                <div class="mb-4 text-xs text-zinc-400">{{ $event->registration_count }} / {{ $event->kapasitas }} peserta</div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('public.event-detail', $event) }}" class="flex-1 rounded-lg border border-zinc-200 px-3 py-2 text-center text-xs font-medium text-zinc-700 hover:border-zinc-300">Lihat Detail</a>
                                @if ($isRegistrationOpen)
                                    <a href="{{ route('public.event-detail', $event) }}" class="flex-1 rounded-lg bg-orange-600 px-3 py-2 text-center text-xs font-medium text-white hover:bg-orange-700">Daftar</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center text-sm text-zinc-500 md:col-span-2 xl:col-span-3">
                        Belum ada event publik yang sesuai dengan filter saat ini.
                    </div>
                @endforelse
            </div>

            @if ($events->hasPages())
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </section>

</x-layouts.public>
