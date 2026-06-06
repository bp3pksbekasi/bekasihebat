<x-layouts.public
    title="Galeri"
    description="Galeri kegiatan Kabupaten Bekasi Hebat berisi dokumentasi lapangan dan aktivitas komunitas dari berbagai wilayah.">

    @php
        $jenis = trim((string) request('jenis', ''));

        $galeriItems = \App\Models\KegiatanRw::query()
            ->where('tampil_galeri', true)
            ->whereNotNull('foto')
            ->when($jenis !== '', fn ($query) => $query->where('jenis_kegiatan', $jenis))
            ->orderByDesc('tanggal_kegiatan')
            ->paginate(20)
            ->withQueryString();

        $fotoItems = $galeriItems->getCollection()
            ->flatMap(function ($kegiatan) {
                return collect($kegiatan->foto)->map(function ($foto) use ($kegiatan) {
                    return [
                        'src' => Storage::url($foto),
                        'caption' => trim($kegiatan->jenis_config['label'] . ' - ' . $kegiatan->desa . ', ' . $kegiatan->tanggal_kegiatan?->translatedFormat('d F Y')),
                        'kegiatan' => $kegiatan,
                    ];
                });
            });

        $jenisOptions = \App\Models\KegiatanRw::query()
            ->where('tampil_galeri', true)
            ->whereNotNull('foto')
            ->select('jenis_kegiatan')
            ->distinct()
            ->orderBy('jenis_kegiatan')
            ->pluck('jenis_kegiatan');
    @endphp

    <section class="bg-zinc-50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <div class="mb-2 text-xs font-medium uppercase tracking-wider text-orange-600">Galeri</div>
                <h1 class="text-3xl font-medium text-zinc-900">Galeri Kegiatan</h1>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Dokumentasi kegiatan lapangan, kunjungan warga, dan agenda komunitas yang dibagikan ke galeri publik.
                </p>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap gap-2">
                <a
                    href="{{ route('public.galeri') }}"
                    class="rounded-full px-4 py-2 text-xs font-medium {{ $jenis === '' ? 'bg-orange-600 text-white' : 'border border-zinc-200 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900' }}">
                    Semua
                </a>
                @foreach ($jenisOptions as $option)
                    <a
                        href="{{ route('public.galeri', ['jenis' => $option]) }}"
                        class="rounded-full px-4 py-2 text-xs font-medium {{ $jenis === $option ? 'bg-orange-600 text-white' : 'border border-zinc-200 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900' }}">
                        {{ \App\Models\KegiatanRw::JENIS_KEGIATAN[$option]['label'] ?? ucfirst($option) }}
                    </a>
                @endforeach
            </div>

            @if ($fotoItems->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($fotoItems as $item)
                        <button
                            type="button"
                            onclick='openLightbox(@js($item["src"]), @js($item["caption"]))'
                            class="overflow-hidden rounded-xl border border-zinc-200 bg-white text-left transition-all hover:border-zinc-300 hover:shadow-sm">
                            <div class="aspect-square bg-zinc-100">
                                <img src="{{ $item['src'] }}" alt="{{ $item['caption'] }}" class="h-full w-full object-cover">
                            </div>
                            <div class="p-3">
                                <div class="text-xs font-medium text-zinc-900">{{ $item['kegiatan']->jenis_config['label'] }}</div>
                                <div class="mt-1 text-[11px] text-zinc-500">{{ $item['kegiatan']->desa }}, {{ $item['kegiatan']->kecamatan }}</div>
                                <div class="mt-1 text-[11px] text-zinc-400">{{ $item['kegiatan']->tanggal_kegiatan?->translatedFormat('d F Y') }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center text-sm text-zinc-500">
                    Belum ada dokumentasi galeri untuk filter yang dipilih.
                </div>
            @endif

            @if ($galeriItems->hasPages())
                <div class="mt-8">
                    {{ $galeriItems->links() }}
                </div>
            @endif
        </div>
    </section>

    <div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4" onclick="closeLightbox()">
        <div class="relative max-w-5xl">
            <img id="lightbox-img" src="" class="max-h-[80vh] max-w-full rounded-lg" alt="">
            <div id="lightbox-caption" class="mt-3 text-center text-sm text-white"></div>
        </div>
    </div>

    <script>
        function openLightbox(src, caption) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-caption').textContent = caption;
            document.getElementById('lightbox').classList.remove('hidden');
            document.getElementById('lightbox').classList.add('flex');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('flex');
            document.getElementById('lightbox').classList.add('hidden');
        }
    </script>

</x-layouts.public>
