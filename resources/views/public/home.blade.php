<x-layouts.public
    title="Kabupaten Bekasi Hebat"
    description="Homepage Kabupaten Bekasi Hebat berisi ajakan bergabung, pilihan program kegiatan, event terdekat, berita kegiatan, dan testimoni warga.">

    @php
        $publicEvents = \App\Models\Event::query()
            ->where('is_public', true)
            ->where('status', \App\Models\Event::STATUS_DISETUJUI)
            ->orderBy('tanggal_mulai')
            ->limit(4)
            ->get();

        $featuredEvent = $publicEvents->first();
        $sideEvents = $publicEvents->skip(1)->take(4);
        $totalAnggota = \App\Models\User::count();

        $programs = [
            'Berita Dakwah',
            'Keluarga',
            'Kesehatan',
            'Kewirausahaan',
            'Ketenagakerjaan',
            'Lingkungan Hidup',
            'Pendidikan',
            'Pertanian/Perikanan',
            'Seni & Budaya',
        ];

        $testimonials = [
            [
                'quote' => 'Kegiatannya terasa dekat dengan kebutuhan warga, bukan sekadar seremonial.',
                'name' => 'Warga Tambun Utara',
            ],
            [
                'quote' => 'Program pelatihan dan silaturahminya membuat jaringan komunitas kami makin kuat.',
                'name' => 'Peserta Pelatihan',
            ],
            [
                'quote' => 'Informasi kegiatan jadi lebih rapi dan mudah diikuti lewat website publik ini.',
                'name' => 'Relawan Wilayah',
            ],
        ];
    @endphp

    <section
        class="relative overflow-hidden border-b border-zinc-200"
        style="background-image:linear-gradient(90deg,rgba(255,255,255,0.18),rgba(255,255,255,0.08)),url('{{ asset('images/hero.jpg') }}');background-size:cover;background-position:center;">
        <div class="bg-gradient-to-r from-white/10 via-white/10 to-slate-950/20">
            <div class="mx-auto grid min-h-[430px] max-w-6xl items-center gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
                <div class="self-end pb-4">
                    <div class="mx-auto max-w-[360px] rounded-[32px] bg-white/10 p-4 backdrop-blur-[1px] lg:mx-0">
                        <div class="rounded-[28px] border border-white/50 bg-white/20 p-4 shadow-xl shadow-black/10">
                            <div class="grid grid-cols-3 gap-3">
                                <div class="h-44 rounded-[26px] bg-gradient-to-b from-zinc-100 to-zinc-300"></div>
                                <div class="h-48 rounded-[26px] bg-gradient-to-b from-zinc-100 to-zinc-300"></div>
                                <div class="h-44 rounded-[26px] bg-gradient-to-b from-zinc-100 to-zinc-300"></div>
                            </div>
                            <div class="-mt-4 text-center">
                                <span class="inline-flex rounded-full bg-orange-500 px-4 py-2 text-[11px] font-semibold text-white shadow-md">
                                    Satu Kartu untuk Kabupaten Bekasi Hebat!
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:justify-self-end">
                    <div class="max-w-md rounded-[26px] border border-white/15 bg-slate-950/72 p-6 shadow-2xl shadow-slate-950/35 backdrop-blur-md">
                        <div class="inline-flex rounded-full border border-orange-300/30 bg-orange-500/18 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-orange-200">
                            Komunitas Kabupaten Bekasi
                        </div>
                        <div class="mt-4 text-4xl font-semibold leading-tight text-white drop-shadow-sm">
                            Ayo Bergabung!
                        </div>
                        <div class="mt-3 max-w-sm text-sm leading-relaxed text-white/85">
                            Bergabung sekarang untuk mendapatkan akses kegiatan, pelatihan, dan jaringan kolaborasi warga Kabupaten Bekasi Hebat.
                        </div>
                        <div class="mt-5 space-y-3 text-[13px] leading-relaxed text-white/90">
                            <div class="rounded-xl border border-white/10 bg-white/8 px-4 py-3">
                                <span class="font-semibold text-orange-200">1.</span>
                                Segera bergabung dan buat kartu anggota untuk menikmati banyak manfaat.
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/8 px-4 py-3">
                                <span class="font-semibold text-orange-200">2.</span>
                                Pilih program pelatihan atau kegiatan yang tersedia sesuai kebutuhan dan minat Anda.
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/8 px-4 py-3">
                                <span class="font-semibold text-orange-200">3.</span>
                                Ajak saudara, teman, tetangga, dan kerabat ikut serta untuk Kabupaten Bekasi Hebat.
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-950/20 transition hover:bg-orange-600">
                                Daftar Sekarang
                                <i class="ti ti-arrow-right text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="program-kegiatan" class="bg-white py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-semibold tracking-tight text-slate-800">Pilihan Program Kegiatan & Pelatihan</h2>
                <p class="mt-2 text-sm text-slate-500">Temukan program pelatihan sesuai dengan minat dan passion Anda.</p>
            </div>
            <div class="mx-auto mt-10 flex max-w-4xl flex-wrap justify-center gap-4">
                @foreach ($programs as $program)
                    <a
                        href="{{ route('public.events') }}"
                        class="rounded-xl bg-rose-50 px-5 py-3 text-[13px] font-semibold text-slate-600 shadow-sm ring-1 ring-rose-100 transition hover:bg-orange-500 hover:text-white hover:ring-orange-500">
                        {{ $program }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-[#f4f5f7] py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-800">Event Terdekat</h2>
                    <p class="mt-1 text-sm text-slate-500">Jangan lewatkan event-event menarik.</p>
                </div>
                <a href="{{ route('public.events') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">
                    Lihat Semua Event <i class="ti ti-chevron-right text-sm"></i>
                </a>
            </div>

            @if ($publicEvents->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($publicEvents->take(3) as $event)
                        <a href="{{ route('public.event-detail', $event) }}" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="relative h-44 bg-gradient-to-br from-orange-100 to-orange-200">
                                @if ($event->cover_image)
                                    <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <i class="ti ti-calendar-event text-5xl text-orange-300"></i>
                                    </div>
                                @endif
                                <div class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-[11px] font-semibold text-orange-600">
                                    {{ $event->jenis_label }}
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="text-xs font-medium text-slate-500">{{ $event->tanggal_mulai?->translatedFormat('d F Y, H:i') }}</div>
                                <div class="mt-2 line-clamp-2 text-lg font-semibold leading-snug text-slate-800">{{ $event->judul }}</div>
                                <div class="mt-2 text-sm text-slate-500">{{ $event->lokasi }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl bg-white p-10 text-center text-sm text-slate-500 shadow-sm ring-1 ring-zinc-200">
                    Event publik akan tampil di sini setelah data kegiatan tersedia.
                </div>
            @endif
        </div>
    </section>

    <section id="berita-kegiatan" class="bg-white py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-800">Berita Kegiatan</h2>
                    <p class="mt-1 text-sm text-slate-500">Update kegiatan dan informasi terbaru di Kabupaten Bekasi.</p>
                </div>
                <a href="{{ route('public.events') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">
                    Berita Lainnya <i class="ti ti-chevron-right text-sm"></i>
                </a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_0.8fr]">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200">
                    <div class="h-[290px] bg-zinc-100">
                        @if ($featuredEvent && $featuredEvent->cover_image)
                            <img src="{{ Storage::url($featuredEvent->cover_image) }}" alt="{{ $featuredEvent->judul }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset('images/hero.jpg') }}" alt="Berita kegiatan" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="text-2xl font-semibold leading-snug text-slate-800">
                            {{ $featuredEvent?->judul ?? 'PKS Kabupaten Bekasi Gelar Pelatihan Kader Dasar untuk Anggota Baru' }}
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">
                            {{ \Illuminate\Support\Str::limit(strip_tags((string) ($featuredEvent?->deskripsi ?? 'PKS Kabupaten Bekasi mengadakan pelatihan dan penguatan kapasitas anggota untuk mendorong partisipasi warga dan aktivitas sosial yang lebih luas di berbagai wilayah Kabupaten Bekasi.')), 180) }}
                        </p>
                        <div class="mt-5 flex items-center justify-between">
                            <div class="text-xs font-medium text-slate-400">
                                {{ $featuredEvent?->tanggal_mulai?->translatedFormat('l, d/m/Y') ?? now()->translatedFormat('l, d/m/Y') }}
                            </div>
                            @if ($featuredEvent)
                                <a href="{{ route('public.event-detail', $featuredEvent) }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">Baca Selengkapnya</a>
                            @else
                                <a href="{{ route('public.events') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">Baca Selengkapnya</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($sideEvents as $event)
                        <a href="{{ route('public.event-detail', $event) }}" class="flex gap-3 overflow-hidden rounded-xl bg-white p-3 shadow-sm ring-1 ring-zinc-200 transition hover:shadow-md">
                            <div class="h-20 w-24 shrink-0 overflow-hidden rounded-lg bg-orange-100">
                                @if ($event->cover_image)
                                    <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" class="h-full w-full object-cover">
                                @else
                                    <img src="{{ asset('images/hero.jpg') }}" alt="{{ $event->judul }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="line-clamp-2 text-[13px] font-semibold leading-snug text-slate-800">{{ $event->judul }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">{{ $event->tanggal_mulai?->translatedFormat('d M Y') }}</div>
                            </div>
                        </a>
                    @empty
                        @foreach (range(1, 5) as $i)
                            <a href="{{ route('public.events') }}" class="flex gap-3 overflow-hidden rounded-xl bg-white p-3 shadow-sm ring-1 ring-zinc-200 transition hover:shadow-md">
                                <div class="h-20 w-24 shrink-0 overflow-hidden rounded-lg bg-orange-100">
                                    <img src="{{ asset('images/hero.jpg') }}" alt="Berita kegiatan" class="h-full w-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <div class="line-clamp-2 text-[13px] font-semibold leading-snug text-slate-800">PKS Kabupaten Bekasi Gelar Kegiatan Sosial dan Penguatan Warga</div>
                                    <div class="mt-1 text-[11px] text-slate-500">{{ now()->subDays($i)->translatedFormat('d M Y') }}</div>
                                </div>
                            </a>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#f4f5f7] py-16">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-semibold tracking-tight text-slate-800">Apa Kata Mereka</h2>
            <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500">
                Dengarkan pengalaman dan cerita dari mereka yang telah berpartisipasi dalam program-program Kabupaten Bekasi Hebat.
            </p>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($testimonials as $item)
                    <div class="rounded-2xl bg-white p-6 text-left shadow-sm ring-1 ring-zinc-200">
                        <div class="mb-4 text-orange-500">
                            <i class="ti ti-quote text-3xl"></i>
                        </div>
                        <p class="text-sm leading-relaxed text-slate-600">{{ $item['quote'] }}</p>
                        <div class="mt-5 text-sm font-semibold text-slate-800">{{ $item['name'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-500">
                <div><span class="font-semibold text-slate-800">{{ number_format($totalAnggota) }}+</span> anggota aktif</div>
                <div><span class="font-semibold text-slate-800">23</span> kecamatan terjangkau</div>
                <div><span class="font-semibold text-slate-800">{{ number_format($publicEvents->count()) }}</span> event publik terbaru</div>
            </div>
        </div>
    </section>

</x-layouts.public>
