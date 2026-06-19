@props([
    'title' => 'Kabupaten Bekasi Hebat',
    'description' => 'Kabupaten Bekasi Hebat : Komunitas pelatihan dan pengembangan potensi masyarakat, program sosial, dan aspirasi warga untuk membangun Kabupaten Bekasi yang lebih baik.',
    'image' => null,
    'ogType' => 'website',
    'noindex' => false,
])

@php
    $dashboardRoute = route('member.dashboard');
    $canonicalUrl = url()->current();
    $ogImage = $image ?? asset('images/logo-kabupaten-bekasi-hebat-sm.jpg');
    $siteName = 'DPD PKS Kabupaten Bekasi';
    $fullTitle = $title !== 'Kabupaten Bekasi Hebat' ? $title . ' — ' . $siteName : $title;

    $navItems = [
        ['route' => 'public.home', 'label' => 'Beranda'],
        ['route' => 'public.events', 'label' => 'Event'],
        ['route' => 'public.aspirasi', 'label' => 'Aspirasi'],
        ['route' => 'public.berita', 'label' => 'Berita'],
        ['route' => 'public.galeri', 'label' => 'Galeri'],
        ['route' => 'public.tentang', 'label' => 'Tentang'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fe5000">
    <meta name="description" content="{{ $description }}">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <title>{{ $fullTitle }}</title>

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- JSON-LD Organization --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "PoliticalParty",
        "name": "{{ $siteName }}",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo-kabupaten-bekasi-hebat-sm.jpg') }}",
        "description": "Kabupaten Bekasi Hebat : Komunitas pelatihan dan pengembangan potensi masyarakat, program sosial, dan aspirasi warga untuk membangun Kabupaten Bekasi yang lebih baik.",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "Ruko Sentra Niaga Kalimas, Jl. Kiyai H. Noer Ali No.16 Blok A, Setiadarma",
            "addressLocality": "Tambun Selatan",
            "addressRegion": "Kabupaten Bekasi",
            "addressCountry": "ID",
            "postalCode": "17510"
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --orange: #fe5000;
            --orange-dark: #d94000;
            --orange-light: #fff3ee;
            --blue: #1a3a6b;
            --blue-mid: #2155a0;
            --blue-light: #e8f0fb;
            --gray-50: #fafaf8;
            --gray-100: #f7f7f5;
            --gray-200: #ececea;
            --gray-300: #dddddb;
            --gray-400: #9a9890;
            --gray-700: #3d3d3a;
            --gray-900: #1a1a18;
            --white: #ffffff;
            --shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 6px 32px rgba(0, 0, 0, 0.13);
            --radius: 8px;
            --radius-lg: 14px;
        }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-50);
            color: var(--gray-700);
            font-size: 15px;
            line-height: 1.65;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        button, input, textarea, select { font: inherit; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 3px solid var(--orange);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }
        .nav-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: var(--orange);
            color: var(--white);
        }
        .brand-icon svg { width: 20px; height: 20px; fill: currentColor; }
        .brand-text span {
            display: block;
            font-size: 9px;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--gray-400);
        }
        .brand-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            color: var(--blue);
        }
        .nav-links { display: flex; align-items: center; gap: 2px; }
        .nav-links a {
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            border-radius: var(--radius);
            transition: .2s;
        }
        .nav-links a:hover,
        .nav-links a.active {
            background: var(--orange-light);
            color: var(--orange);
        }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .nav-dashboard {
            padding: 8px 14px;
            border: 1px solid var(--gray-200);
            border-radius: 18px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
        }
        .nav-dashboard:hover { border-color: var(--gray-300); color: var(--gray-900); }
        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: var(--orange);
            color: var(--white);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            transition: .2s;
        }
        .nav-cta:hover { background: var(--orange-dark); }
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--gray-700);
            font-size: 22px;
            cursor: pointer;
        }
        .mobile-nav {
            display: none;
            background: var(--white);
            padding: 12px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .mobile-nav.show { display: block; }
        .mobile-nav a {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
        }
        .mobile-nav a:last-child { border-bottom: none; }
        .flash-message {
            border-bottom: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-size: 13px;
        }
        .flash-message .container { padding-top: 10px; padding-bottom: 10px; }
        .section { padding: 56px 0; }
        .section-white { background: var(--white); }
        .section-header { margin-bottom: 36px; }
        .section-header.center { text-align: center; }
        .section-tag {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .section-tag.orange { background: var(--orange-light); color: var(--orange); }
        .section-tag.blue { background: var(--blue-light); color: var(--blue); }
        .section-title {
            margin: 8px 0 0;
            font-size: 24px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -.3px;
            color: var(--gray-900);
        }
        .section-subtitle {
            margin-top: 6px;
            max-width: 680px;
            font-size: 18px;
            line-height: 1.8;
            color: var(--gray-400);
        }
        .section-subtitle.center { margin-left: auto; margin-right: auto; }
        .fade-up { opacity: 0; transform: translateY(20px); transition: .6s ease; }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .site-footer {
            background: var(--gray-900);
            color: rgba(255, 255, 255, 0.7);
        }
        .footer-main {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 32px;
            padding: 40px 0;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .footer-brand .icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--orange);
            color: var(--white);
        }
        .footer-brand .icon svg { width: 16px; height: 16px; fill: currentColor; }
        .footer-brand strong { color: var(--white); font-size: 14px; }
        .site-footer p { margin: 0; font-size: 12px; line-height: 1.6; }
        .site-footer h4 {
            margin: 0 0 12px;
            color: var(--white);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .site-footer ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .site-footer li { margin-bottom: 6px; }
        .site-footer li a {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            transition: .2s;
        }
        .site-footer li a:hover { color: var(--orange); }
        .footer-socials {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .footer-socials a {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.65);
            transition: .2s;
        }
        .footer-socials a:hover { background: var(--orange); color: var(--white); }
        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 11px;
        }
        .orange-strip {
            width: 60px;
            height: 3px;
            border-radius: 2px;
            background: var(--orange);
        }
        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none; }
            .mobile-toggle { display: block; }
            .footer-main { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .footer-main, .footer-bottom { grid-template-columns: 1fr; display: grid; }
            .footer-bottom { justify-content: start; }
        }

        /* Dynamic Backlight Glow Accents */
        .bg-glow-container {
            position: relative;
            overflow: hidden;
        }
        .bg-glow-accent {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
        }
        .bg-glow-orange {
            background: radial-gradient(circle, rgba(254, 80, 0, 0.08) 0%, rgba(254, 80, 0, 0.01) 70%);
        }
        .bg-glow-blue {
            background: radial-gradient(circle, rgba(33, 85, 160, 0.08) 0%, rgba(33, 85, 160, 0.01) 70%);
        }

        /* Pulsing green live indicator badge */
        .live-pulse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px !important;
            background: rgba(34, 197, 94, 0.12) !important;
            border: 1px solid rgba(34, 197, 94, 0.24) !important;
            color: #16a34a !important;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .live-pulse::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 1.5s infinite;
        }
        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        /* Micro-Animations & premium hover effects */
        .premium-hover-card {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease, border-color 0.4s ease !important;
        }
        .premium-hover-card:hover {
            transform: translateY(-6px) scale(1.005) !important;
            box-shadow: 0 16px 36px rgba(254, 80, 0, 0.09) !important;
            border-color: rgba(254, 80, 0, 0.2) !important;
        }
        .premium-hover-card img {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .premium-hover-card:hover img {
            transform: scale(1.04) !important;
        }

        /* Interactive tab switcher styling */
        .dynamic-tab-btn {
            background: #fff;
            border: 1px solid var(--gray-200);
            color: var(--gray-700);
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dynamic-tab-btn:hover {
            border-color: var(--orange);
            color: var(--orange);
            background: var(--orange-light);
        }
        .dynamic-tab-btn.active {
            background: var(--orange);
            border-color: var(--orange);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(254, 80, 0, 0.2);
        }
        
        .dynamic-tab-content {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            display: none;
        }
        .dynamic-tab-content.active {
            opacity: 1;
            transform: translateY(0);
            display: block;
        }

        /* Sliding button arrow on hover */
        .btn-sliding-arrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-sliding-arrow i {
            transition: transform 0.2s ease;
        }
        .btn-sliding-arrow:hover i {
            transform: translateX(4px);
        }

        /* Modern News Ticker Styling */
        .news-ticker-wrap {
            background: #0f172a;
            color: #fff;
            border-bottom: 2px solid var(--orange);
            font-size: 13px;
            font-weight: 500;
            height: 38px;
            display: flex;
            align-items: center;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }
        .news-ticker-label {
            background: var(--orange);
            color: #fff;
            padding: 0 16px;
            height: 100%;
            display: flex;
            align-items: center;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .5px;
            z-index: 2;
            box-shadow: 6px 0 15px rgba(0, 0, 0, 0.3);
            position: relative;
            flex-shrink: 0;
        }
        .news-ticker-content {
            flex-grow: 1;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            display: flex;
            align-items: center;
        }
        .news-ticker-list {
            display: inline-block;
            padding-left: 100%;
            animation: ticker 40s linear infinite;
        }
        .news-ticker-item {
            display: inline-block;
            padding-right: 54px;
            color: rgba(255, 255, 255, 0.85);
        }
        .news-ticker-item span {
            color: var(--orange);
            font-weight: 700;
        }
        .news-ticker-item::after {
            content: '•';
            color: rgba(255, 255, 255, 0.25);
            margin-left: 27px;
        }
        @keyframes ticker {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }
        .news-ticker-wrap:hover .news-ticker-list {
            animation-play-state: paused;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="nav-wrap">
                <a href="{{ route('public.home') }}" wire:navigate class="brand">
                    <img src="{{ asset('images/logo-hebat.png') }}" alt="Kabupaten Bekasi Hebat" style="height: 52px; width: auto; object-fit: contain;">
                </a>

                <nav class="nav-links">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" wire:navigate class="{{ request()->routeIs($item['route']) || ($item['route'] === 'public.events' && request()->routeIs('public.events.show')) ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="nav-actions">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="nav-dashboard">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="nav-dashboard">Masuk</a>
                    @endauth
                    <a href="{{ route('register') }}" wire:navigate class="nav-cta">
                        <i class="ti ti-user-plus"></i>
                        <span>Daftar</span>
                    </a>
                </div>

                <button type="button" class="mobile-toggle" onclick="document.getElementById('mobileNav').classList.toggle('show')">
                    <i class="ti ti-menu-2"></i>
                </button>
            </div>

            <div id="mobileNav" class="mobile-nav">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate>{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ route('register') }}" wire:navigate>Daftar Anggota</a>
                <a href="{{ auth()->check() ? $dashboardRoute : route('login') }}" wire:navigate>
                    {{ auth()->check() ? 'Dashboard' : 'Login Dashboard' }}
                </a>
            </div>
        </div>
    </header>

    @if (request()->routeIs('public.home'))
        @php
            $aspirasiCount = class_exists(\App\Models\Aspirasi::class) && \Illuminate\Support\Facades\Schema::hasTable('aspirasis') 
                ? \App\Models\Aspirasi::count() 
                : 184;
        @endphp
        <div class="news-ticker-wrap">
            <div class="news-ticker-label">
                <i class="ti ti-speakerphone" style="margin-right: 6px; font-size: 14px;"></i> Update Terkini
            </div>
            <div class="news-ticker-content">
                <div class="news-ticker-list">
                    <div class="news-ticker-item">PKS Kabupaten Bekasi berkomitmen hadir melayani sedekat mungkin hingga ke tingkat RW.</div>
                    <div class="news-ticker-item">Event Terdekat: Ikuti Senam PKS Bersama dan pelayanan kesehatan gratis di daerah Anda.</div>
                    <div class="news-ticker-item">Segara daftarkan diri Anda sebagai member Kabupaten Bekasi Hebat.</div>
                </div>
            </div>
        </div>
    @endif

    @if (session('message'))
        <div class="flash-message">
            <div class="container">{{ session('message') }}</div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-main">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset('images/logoputih.png') }}" alt="Kabupaten Bekasi Hebat" style="height: 48px; width: auto; object-fit: contain">
                    </div>
                    <p>
                        DPD PKS Kabupaten Bekasi<br>
                        Ruko Sentra Niaga Kalimas<br>
                        Jl. kiyai H. Noer Ali No.16 blok A, Setiadarma, Kec. Tambun Sel., Kabupaten Bekasi, Jawa Barat 17510
                    </p>
                    <div class="footer-socials">
                        @foreach ([
                            ['icon' => 'brand-instagram', 'label' => 'Instagram'],
                            ['icon' => 'brand-tiktok', 'label' => 'TikTok'],
                            ['icon' => 'brand-youtube', 'label' => 'YouTube'],
                            ['icon' => 'brand-facebook', 'label' => 'Facebook'],
                            ['icon' => 'brand-whatsapp', 'label' => 'WhatsApp'],
                        ] as $social)
                            <a href="#" aria-label="{{ $social['label'] }}">
                                <i class="ti ti-{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4>Menu</h4>
                    <ul>
                        @foreach ($navItems as $item)
                            <li><a href="{{ route($item['route']) }}" wire:navigate>{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4>Program</h4>
                    <ul>
                        <li><a href="{{ route('public.tentang') }}" wire:navigate>RKI</a></li>
                        <li><a href="{{ route('public.tentang') }}" wire:navigate>KSN / Senam</a></li>
                        <li><a href="{{ route('public.tentang') }}" wire:navigate>Sapa Warga</a></li>
                        <li><a href="{{ route('public.tentang') }}" wire:navigate>Kaderisasi</a></li>
                        <li><a href="{{ route('register') }}" wire:navigate>Daftar Anggota</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Lainnya</h4>
                    <ul>
                        <li><a href="{{ route('public.tentang') }}" wire:navigate>Tentang Kami</a></li>
                        <li><a href="{{ route('public.events') }}" wire:navigate>DPRD Kami</a></li>
                        <li><a href="{{ route('register') }}" wire:navigate>Daftar Anggota</a></li>
                        <li><a href="{{ auth()->check() ? $dashboardRoute : route('login') }}" wire:navigate>Login Dashboard</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© {{ date('Y') }} DPD PKS Kabupaten Bekasi. Hak cipta dilindungi.</span>
                <div class="orange-strip"></div>
            </div>
        </div>
    </footer>
    <script>
        (function () {
            const revealFadeUp = () => {
                document.querySelectorAll('.fade-up').forEach((element) => {
                    element.classList.add('visible');
                });
            };

            document.addEventListener('DOMContentLoaded', revealFadeUp);
            document.addEventListener('livewire:navigated', revealFadeUp);
            revealFadeUp();
        })();
    </script>
</body>
</html>
