# Prompt Bertahap Trae — Website Publik

Website publik kabupatenbekasihebat.id — halaman yang bisa diakses tanpa login. Menampilkan profil komunitas, event publik, galeri kegiatan, dan form registrasi anggota.

---

## PROMPT 1: Layout Publik + Route + Homepage

```
Buat website publik dengan layout terpisah dari admin. Langsung buat, JANGAN test, JANGAN tanya.

PENTING: Website publik TIDAK pakai sidebar admin. Pakai layout sendiri dengan navbar atas + footer.

== 1. LAYOUT PUBLIK ==

File: resources/views/components/layouts/public.blade.php

```blade
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Kabupaten Bekasi Hebat' }}</title>
    <meta name="description" content="{{ $description ?? 'Komunitas Kabupaten Bekasi Hebat — Bersama membangun Bekasi yang lebih hebat' }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-zinc-900 antialiased">

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-zinc-100" id="navbar">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('public.home') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                        <x-app-logo-icon class="size-4 fill-current text-white" />
                    </div>
                    <span class="text-sm font-medium text-zinc-900">Kabupaten Bekasi Hebat</span>
                </a>

                {{-- Menu desktop --}}
                <div class="hidden md:flex items-center gap-6 text-sm">
                    <a href="{{ route('public.home') }}" class="{{ request()->routeIs('public.home') ? 'text-orange-600 font-medium' : 'text-zinc-600 hover:text-zinc-900' }}">Beranda</a>
                    <a href="{{ route('public.tentang') }}" class="{{ request()->routeIs('public.tentang') ? 'text-orange-600 font-medium' : 'text-zinc-600 hover:text-zinc-900' }}">Tentang Kami</a>
                    <a href="{{ route('public.events') }}" class="{{ request()->routeIs('public.events*') ? 'text-orange-600 font-medium' : 'text-zinc-600 hover:text-zinc-900' }}">Kegiatan</a>
                    <a href="{{ route('public.galeri') }}" class="{{ request()->routeIs('public.galeri') ? 'text-orange-600 font-medium' : 'text-zinc-600 hover:text-zinc-900' }}">Galeri</a>
                </div>

                {{-- Auth buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-zinc-600 hover:text-zinc-900">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-zinc-600 hover:text-zinc-900">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">Daftar</a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button class="md:hidden" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                    <i class="ti ti-menu-2 text-xl text-zinc-600"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu" class="hidden md:hidden border-t border-zinc-100 bg-white px-4 py-3 space-y-2">
            <a href="{{ route('public.home') }}" class="block py-2 text-sm text-zinc-600">Beranda</a>
            <a href="{{ route('public.tentang') }}" class="block py-2 text-sm text-zinc-600">Tentang Kami</a>
            <a href="{{ route('public.events') }}" class="block py-2 text-sm text-zinc-600">Kegiatan</a>
            <a href="{{ route('public.galeri') }}" class="block py-2 text-sm text-zinc-600">Galeri</a>
        </div>
    </nav>

    {{-- CONTENT --}}
    <main>
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="bg-zinc-900 text-zinc-400 text-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-orange-600 rounded-md flex items-center justify-center">
                            <x-app-logo-icon class="size-3 fill-current text-white" />
                        </div>
                        <span class="text-sm font-medium text-white">Bekasi Hebat</span>
                    </div>
                    <p class="text-xs leading-relaxed text-zinc-500">Komunitas Kabupaten Bekasi Hebat. Bersama membangun Bekasi yang lebih baik untuk generasi mendatang.</p>
                </div>
                {{-- Menu --}}
                <div>
                    <div class="text-[10px] text-zinc-600 uppercase tracking-wider font-medium mb-3">Menu</div>
                    <div class="space-y-2 text-xs">
                        <a href="{{ route('public.home') }}" class="block text-zinc-500 hover:text-zinc-300">Beranda</a>
                        <a href="{{ route('public.tentang') }}" class="block text-zinc-500 hover:text-zinc-300">Tentang Kami</a>
                        <a href="{{ route('public.events') }}" class="block text-zinc-500 hover:text-zinc-300">Kegiatan</a>
                        <a href="{{ route('public.galeri') }}" class="block text-zinc-500 hover:text-zinc-300">Galeri</a>
                    </div>
                </div>
                {{-- Akun --}}
                <div>
                    <div class="text-[10px] text-zinc-600 uppercase tracking-wider font-medium mb-3">Akun</div>
                    <div class="space-y-2 text-xs">
                        <a href="{{ route('register') }}" class="block text-zinc-500 hover:text-zinc-300">Daftar</a>
                        <a href="{{ route('login') }}" class="block text-zinc-500 hover:text-zinc-300">Masuk</a>
                    </div>
                </div>
                {{-- Sosial --}}
                <div>
                    <div class="text-[10px] text-zinc-600 uppercase tracking-wider font-medium mb-3">Sosial Media</div>
                    <div class="space-y-2 text-xs">
                        <a href="#" class="flex items-center gap-2 text-zinc-500 hover:text-zinc-300"><i class="ti ti-brand-instagram text-sm"></i> Instagram</a>
                        <a href="#" class="flex items-center gap-2 text-zinc-500 hover:text-zinc-300"><i class="ti ti-brand-youtube text-sm"></i> YouTube</a>
                        <a href="#" class="flex items-center gap-2 text-zinc-500 hover:text-zinc-300"><i class="ti ti-brand-facebook text-sm"></i> Facebook</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-zinc-800 mt-8 pt-6 text-center text-xs text-zinc-600">
                © {{ date('Y') }} Kabupaten Bekasi Hebat. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

</body>
</html>
```

== 2. ROUTES PUBLIK ==

Di routes/web.php, SEBELUM middleware auth group (agar bisa diakses tanpa login):

```php
// Public pages
Route::get('/', function () { return view('public.home'); })->name('public.home');
Route::get('/tentang-kami', function () { return view('public.tentang'); })->name('public.tentang');
Route::get('/kegiatan', function () { return view('public.events'); })->name('public.events');
Route::get('/kegiatan/{event}', function (App\Models\Event $event) {
    abort_unless($event->is_public && $event->status === 'disetujui', 404);
    return view('public.event-detail', compact('event'));
})->name('public.event-detail');
Route::get('/galeri', function () { return view('public.galeri'); })->name('public.galeri');
```

PENTING: Ganti route '/' yang sudah ada (welcome page) dengan public.home.

== 3. HOMEPAGE ==

File: resources/views/public/home.blade.php

```blade
<x-layouts.public title="Kabupaten Bekasi Hebat">

{{-- HERO --}}
<section class="relative overflow-hidden" style="background:linear-gradient(135deg,#1a1a1a 0%,#2d1a0a 50%,#1a1a1a 100%);">
    <div class="absolute top-0 right-0 w-96 h-96" style="background:radial-gradient(circle,rgba(254,80,0,0.12) 0%,transparent 70%);"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center relative z-10">
        <div class="text-xs text-orange-500 uppercase tracking-widest font-medium mb-4">Komunitas Kabupaten Bekasi Hebat</div>
        <h1 class="text-3xl md:text-4xl font-medium text-white leading-tight max-w-xl mx-auto">
            Bersama membangun Bekasi yang lebih <span class="text-orange-500">hebat</span>
        </h1>
        <p class="text-sm text-zinc-400 mt-4 max-w-md mx-auto leading-relaxed">
            Bergabung bersama ribuan warga Kabupaten Bekasi dalam membangun komunitas, mengikuti kegiatan, dan berkontribusi untuk kemajuan daerah.
        </p>
        <div class="flex gap-3 justify-center mt-8">
            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">Daftar sekarang</a>
            <a href="{{ route('public.events') }}" class="px-6 py-2.5 border border-zinc-600 text-white rounded-lg text-sm hover:bg-white/5">Lihat kegiatan</a>
        </div>
        {{-- Stats --}}
        <div class="flex gap-10 justify-center mt-12">
            @php
                $totalAnggota = \App\Models\User::count();
                $totalEvent = \App\Models\Event::where('is_public', true)->where('status', 'disetujui')->count();
            @endphp
            <div class="text-center">
                <div class="text-2xl font-medium text-white">{{ number_format($totalAnggota) }}+</div>
                <div class="text-xs text-zinc-500">Anggota</div>
            </div>
            <div class="w-px bg-zinc-700"></div>
            <div class="text-center">
                <div class="text-2xl font-medium text-white">7</div>
                <div class="text-xs text-zinc-500">Dapil</div>
            </div>
            <div class="w-px bg-zinc-700"></div>
            <div class="text-center">
                <div class="text-2xl font-medium text-white">{{ $totalEvent }}+</div>
                <div class="text-xs text-zinc-500">Kegiatan</div>
            </div>
        </div>
    </div>
</section>

{{-- TENTANG SINGKAT --}}
<section class="py-16 bg-zinc-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="text-xs text-orange-600 uppercase tracking-wider font-medium mb-2">Tentang kami</div>
                <h2 class="text-xl font-medium mb-3">Komunitas yang bergerak untuk perubahan nyata</h2>
                <p class="text-sm text-zinc-600 leading-relaxed mb-4">
                    Kabupaten Bekasi Hebat adalah komunitas warga yang berdedikasi untuk memajukan Kabupaten Bekasi melalui kegiatan sosial, pendidikan, dan pemberdayaan masyarakat. Kami hadir di 23 kecamatan dengan struktur yang solid.
                </p>
                <a href="{{ route('public.tentang') }}" class="text-sm text-orange-600 font-medium inline-flex items-center gap-1 hover:gap-2 transition-all">
                    Selengkapnya <i class="ti ti-arrow-right text-sm"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach([['23', 'Kecamatan'], ['187', 'Desa/Kelurahan'], ['5', 'Bidang Program'], ['50+', 'Pengurus Aktif']] as [$num, $label])
                    <div class="bg-white rounded-xl border border-zinc-200 p-4 text-center">
                        <div class="text-xl font-medium text-orange-600">{{ $num }}</div>
                        <div class="text-xs text-zinc-500 mt-1">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- EVENT MENDATANG --}}
<section class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="text-xs text-orange-600 uppercase tracking-wider font-medium mb-1">Kegiatan</div>
                <h2 class="text-xl font-medium">Event & kegiatan mendatang</h2>
            </div>
            <a href="{{ route('public.events') }}" class="text-sm text-orange-600 font-medium">Lihat semua →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @php
                $publicEvents = \App\Models\Event::where('is_public', true)
                    ->where('status', 'disetujui')
                    ->where('tanggal_mulai', '>=', now())
                    ->orderBy('tanggal_mulai')
                    ->limit(3)->get();
            @endphp
            @forelse($publicEvents as $event)
                <a href="{{ route('public.event-detail', $event) }}" class="block rounded-xl border border-zinc-200 overflow-hidden hover:border-zinc-300 hover:shadow-sm transition-all">
                    {{-- Cover --}}
                    <div class="h-36 flex items-center justify-center" style="background:linear-gradient(135deg,#fe500020,#fe500050);">
                        @if($event->cover_image)
                            <img src="{{ Storage::url($event->cover_image) }}" class="w-full h-full object-cover" alt="{{ $event->judul }}">
                        @else
                            <i class="ti ti-calendar-event text-3xl text-orange-300"></i>
                        @endif
                        <div class="absolute top-3 left-3">
                            <span class="text-[10px] px-2 py-1 rounded-full bg-white/90 text-orange-600 font-medium">{{ \App\Models\Event::JENIS_EVENT[$event->jenis] ?? $event->jenis }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-zinc-400 flex items-center gap-1 mb-1">
                            <i class="ti ti-calendar text-xs"></i>
                            {{ $event->tanggal_mulai->translatedFormat('d F Y, H:i') }}
                        </div>
                        <div class="text-sm font-medium line-clamp-2 mb-2">{{ $event->judul }}</div>
                        <div class="text-xs text-zinc-500 flex items-center gap-1">
                            <i class="ti ti-map-pin text-xs"></i>
                            {{ $event->lokasi }}
                        </div>
                        @if($event->kapasitas > 0)
                            @php $regCount = $event->registrations()->count(); @endphp
                            <div class="mt-2 text-xs text-zinc-400">{{ $regCount }} / {{ $event->kapasitas }} peserta</div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center py-12 text-sm text-zinc-400">
                    Belum ada event yang dijadwalkan. Nantikan kegiatan kami selanjutnya!
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- GALERI PREVIEW --}}
<section class="py-16 bg-zinc-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="text-xs text-orange-600 uppercase tracking-wider font-medium mb-1">Galeri</div>
                <h2 class="text-xl font-medium">Dokumentasi kegiatan</h2>
            </div>
            <a href="{{ route('public.galeri') }}" class="text-sm text-orange-600 font-medium">Lihat semua →</a>
        </div>
        @php
            $fotos = \App\Models\KegiatanRw::where('tampil_galeri', true)
                ->whereNotNull('foto')
                ->orderByDesc('tanggal_kegiatan')
                ->limit(8)->get()
                ->flatMap(fn($k) => collect($k->foto)->map(fn($f) => ['path' => $f, 'kegiatan' => $k]))
                ->take(8);
        @endphp
        @if($fotos->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($fotos as $foto)
                    <div class="aspect-square rounded-lg overflow-hidden bg-zinc-200">
                        <img src="{{ Storage::url($foto['path']) }}" alt="{{ $foto['kegiatan']->jenis_config['label'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-zinc-400">Galeri foto akan segera tersedia</div>
        @endif
    </div>
</section>

{{-- CTA BERGABUNG --}}
<section class="py-16 text-center" style="background:linear-gradient(135deg,#1a1a1a,#2d1a0a);">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-xs text-orange-500 uppercase tracking-widest font-medium mb-3">Bergabung sekarang</div>
        <h2 class="text-2xl font-medium text-white max-w-md mx-auto leading-tight">Jadilah bagian dari pergerakan Bekasi Hebat</h2>
        <p class="text-sm text-zinc-500 mt-3 max-w-sm mx-auto leading-relaxed">
            Daftarkan diri anda, dapatkan kartu anggota digital, dan ikuti berbagai kegiatan positif bersama komunitas.
        </p>
        <div class="flex gap-3 justify-center mt-6">
            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">Daftar gratis</a>
            <a href="{{ route('login') }}" class="px-6 py-2.5 border border-zinc-600 text-white rounded-lg text-sm hover:bg-white/5">Masuk</a>
        </div>
    </div>
</section>

</x-layouts.public>
```

Langsung buat semua. Jangan test.
```

---

## PROMPT 2: Halaman Tentang Kami + Tokoh + Event Listing + Galeri

```
Buat 4 halaman publik. Langsung buat, JANGAN test.

== 1. TENTANG KAMI ==

File: resources/views/public/tentang.blade.php
Layout: <x-layouts.public title="Tentang Kami">

Section 1: Header
- Heading "Tentang Kabupaten Bekasi Hebat"
- Deskripsi panjang tentang visi, misi, sejarah komunitas (teks lorem sementara, nanti diganti)

Section 2: Visi Misi (grid 2 kolom)
- Card Visi + Card Misi

Section 3: Struktur Organisasi
- DPD → 7 DPC (Dapil) → Kecamatan
- Visual sederhana: card per level

Section 4: Tokoh Bekasi
- Heading "Tokoh-tokoh Penggerak"
- Grid 4 kolom: foto (placeholder circle), nama, jabatan
- Data sementara hardcode 8 tokoh (nanti bisa dari database)

```blade
@php
    $tokoh = [
        ['nama' => 'Nama Tokoh 1', 'jabatan' => 'Ketua DPD', 'foto' => null],
        ['nama' => 'Nama Tokoh 2', 'jabatan' => 'Sekretaris DPD', 'foto' => null],
        ['nama' => 'Nama Tokoh 3', 'jabatan' => 'Bendahara DPD', 'foto' => null],
        ['nama' => 'Nama Tokoh 4', 'jabatan' => 'Ketua Dapil 1', 'foto' => null],
        ['nama' => 'Nama Tokoh 5', 'jabatan' => 'Ketua Dapil 2', 'foto' => null],
        ['nama' => 'Nama Tokoh 6', 'jabatan' => 'Ketua Dapil 3', 'foto' => null],
        ['nama' => 'Nama Tokoh 7', 'jabatan' => 'Ketua Dapil 4', 'foto' => null],
        ['nama' => 'Nama Tokoh 8', 'jabatan' => 'Ketua Dapil 5', 'foto' => null],
    ];
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    @foreach($tokoh as $t)
        <div class="text-center">
            <div class="w-20 h-20 rounded-full bg-zinc-100 mx-auto mb-3 flex items-center justify-center border-2 border-orange-100">
                @if($t['foto'])
                    <img src="{{ $t['foto'] }}" class="w-full h-full rounded-full object-cover" alt="">
                @else
                    <i class="ti ti-user text-2xl text-zinc-300"></i>
                @endif
            </div>
            <div class="text-sm font-medium">{{ $t['nama'] }}</div>
            <div class="text-xs text-zinc-500">{{ $t['jabatan'] }}</div>
        </div>
    @endforeach
</div>
```

Section 5: Wilayah Kerja
- Info: 7 Dapil, 23 Kecamatan, 187 Desa
- Daftar kecamatan per dapil (collapsible atau grid)

== 2. EVENT LISTING ==

File: resources/views/public/events.blade.php
Layout: <x-layouts.public title="Kegiatan">

```blade
@php
    $events = \App\Models\Event::where('is_public', true)
        ->where('status', 'disetujui')
        ->orderBy('tanggal_mulai', 'desc')
        ->paginate(9);
@endphp
```

Section 1: Header
- Heading "Kegiatan & Event"
- Subheading "Ikuti berbagai kegiatan positif bersama komunitas Bekasi Hebat"

Section 2: Filter (horizontal)
- Tab: Semua, Mendatang, Selesai
- Search input

Section 3: Grid event cards (3 kolom)
- Sama seperti di homepage tapi paginated
- Jika event sudah lewat: badge "Selesai" abu
- Jika pendaftaran masih buka: badge "Pendaftaran Dibuka" hijau + tombol "Daftar"
- Pagination links

== 3. EVENT DETAIL ==

File: resources/views/public/event-detail.blade.php
Layout: <x-layouts.public :title="$event->judul">

Section 1: Cover image (full width, max-h-64)

Section 2: Event info (max-w-3xl, centered)
- Badge jenis + badge status
- Heading: judul
- Info grid: tanggal, lokasi, penyelenggara, kapasitas
- Deskripsi (prose)
- Tombol "Daftar Event" (jika belum login → redirect ke register. Jika sudah login → create EventRegistration)

```blade
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex gap-2 mb-4">
        <span class="text-xs px-3 py-1 rounded-full bg-orange-50 text-orange-600 font-medium">
            {{ \App\Models\Event::JENIS_EVENT[$event->jenis] ?? $event->jenis }}
        </span>
        @if($event->tanggal_mulai > now())
            <span class="text-xs px-3 py-1 rounded-full bg-green-50 text-green-600 font-medium">Pendaftaran Dibuka</span>
        @else
            <span class="text-xs px-3 py-1 rounded-full bg-zinc-100 text-zinc-500 font-medium">Selesai</span>
        @endif
    </div>

    <h1 class="text-2xl font-medium mb-4">{{ $event->judul }}</h1>

    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="flex items-center gap-2 text-sm text-zinc-600">
            <i class="ti ti-calendar text-orange-500"></i>
            {{ $event->tanggal_mulai->translatedFormat('l, d F Y') }}
        </div>
        <div class="flex items-center gap-2 text-sm text-zinc-600">
            <i class="ti ti-clock text-orange-500"></i>
            {{ $event->tanggal_mulai->format('H:i') }} WIB
        </div>
        <div class="flex items-center gap-2 text-sm text-zinc-600">
            <i class="ti ti-map-pin text-orange-500"></i>
            {{ $event->lokasi }}
        </div>
        <div class="flex items-center gap-2 text-sm text-zinc-600">
            <i class="ti ti-users text-orange-500"></i>
            @if($event->kapasitas > 0)
                {{ $event->registrations()->count() }} / {{ $event->kapasitas }} peserta
            @else
                Tidak terbatas
            @endif
        </div>
    </div>

    <div class="prose prose-sm max-w-none text-zinc-600 mb-8">
        {!! nl2br(e($event->deskripsi)) !!}
    </div>

    {{-- CTA Daftar --}}
    @if($event->tanggal_mulai > now())
        @auth
            @php $sudahDaftar = $event->registrations()->where('user_id', auth()->id())->exists(); @endphp
            @if($sudahDaftar)
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-center">
                    <i class="ti ti-check text-green-600 text-xl"></i>
                    <div class="text-sm font-medium text-green-700 mt-1">Anda sudah terdaftar</div>
                </div>
            @else
                <form method="POST" action="{{ route('public.event-register', $event) }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-orange-600 text-white rounded-xl text-sm font-medium hover:bg-orange-700">
                        Daftar Event Ini
                    </button>
                </form>
            @endif
        @else
            <a href="{{ route('register', ['redirect' => route('public.event-detail', $event)]) }}" class="block w-full py-3 bg-orange-600 text-white rounded-xl text-sm font-medium hover:bg-orange-700 text-center">
                Daftar & Ikuti Event
            </a>
            <p class="text-xs text-zinc-400 text-center mt-2">Belum punya akun? Daftar gratis untuk mengikuti event</p>
        @endauth
    @endif
</div>
```

Tambahkan route untuk registrasi event:
```php
Route::post('/kegiatan/{event}/register', function (App\Models\Event $event) {
    abort_unless(auth()->check(), 403);
    abort_unless($event->is_public && $event->status === 'disetujui', 404);

    $event->registrations()->firstOrCreate(
        ['user_id' => auth()->id()],
        ['status' => 'registered']
    );

    return back()->with('message', 'Berhasil mendaftar!');
})->middleware('auth')->name('public.event-register');
```

== 4. GALERI ==

File: resources/views/public/galeri.blade.php
Layout: <x-layouts.public title="Galeri">

```blade
@php
    $galeriItems = \App\Models\KegiatanRw::where('tampil_galeri', true)
        ->whereNotNull('foto')
        ->orderByDesc('tanggal_kegiatan')
        ->paginate(20);
@endphp
```

Section: Header + Grid foto
- Heading "Galeri Kegiatan"
- Filter: Semua, Baksos, Pengajian, Senam, dll (by jenis_kegiatan)
- Grid masonry-style (4 kolom): setiap foto klik → lightbox (pakai JS sederhana)
- Caption: jenis kegiatan, lokasi, tanggal
- Pagination

Lightbox sederhana (JS inline):
```html
<div id="lightbox" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center" onclick="this.classList.add('hidden')">
    <img id="lightbox-img" src="" class="max-w-4xl max-h-[80vh] rounded-lg" alt="">
    <div id="lightbox-caption" class="absolute bottom-8 text-white text-sm text-center"></div>
</div>

<script>
function openLightbox(src, caption) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-caption').textContent = caption;
    document.getElementById('lightbox').classList.remove('hidden');
}
</script>
```

Langsung buat semua 4 file. Jangan test.
```

---

## PROMPT 3: Fix & Polish Website Publik

```
Cek dan fix website publik. Langsung fix, JANGAN test.

CHECKLIST:

1. Route / (homepage) load tanpa error — layout publik, bukan welcome.blade.php lama
2. Navbar: logo, menu (Beranda, Tentang Kami, Kegiatan, Galeri), tombol Masuk/Daftar
3. Navbar responsive: mobile menu toggle berfungsi
4. Jika sudah login: tombol "Dashboard" muncul menggantikan Masuk/Daftar
5. Hero section: stats angka dari database (jumlah user, jumlah event publik)
6. Section event mendatang: menampilkan max 3 event publik yang disetujui
7. Section galeri: menampilkan foto dari Sisir RW yang ditandai tampil_galeri
8. Footer: 4 kolom, semua link berfungsi
9. /tentang-kami: load dengan tokoh dan info wilayah
10. /kegiatan: listing event publik, paginated
11. /kegiatan/{id}: detail event, info lengkap, tombol daftar
12. Daftar event: jika belum login → redirect register. Jika sudah → create registration
13. /galeri: grid foto, lightbox berfungsi
14. Semua halaman responsive (mobile friendly)
15. Dark mode: TIDAK perlu dark mode di website publik (selalu light)
16. SEO: title dan meta description per halaman
17. Pastikan route lama '/' (welcome) sudah diganti
18. Pastikan link register dan login masih berfungsi (dari Fortify)

STYLING:
- Konsisten: accent orange-600 (#fe5000 diganti orange-600 tailwind untuk public)
- Typography: heading text-xl/2xl font-medium, body text-sm, label text-xs
- Cards: rounded-xl border border-zinc-200, hover border-zinc-300
- Spacing: section py-16, max-w-6xl mx-auto
- Tidak pakai Flux components di halaman publik (hanya HTML + Tailwind)

Langsung fix. Jangan test.
```
