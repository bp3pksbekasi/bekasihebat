@php
    $stats = $this->stats;
    $programs = $this->program;
    $dprd = $this->dprd;
    $events = $this->eventMendatang;
    $newsFeatured = $this->beritaFeatured;
    $newsList = $this->beritaList;
    $galleryItems = $this->galeriHighlights;
    $aspirasiStats = $this->aspirasiStats;
    $aspirasiStories = $this->aspirasiStories;
    $programCardPalette = [
        'rki' => ['bg' => '#ec489915', 'color' => '#ec4899'],
        'ksn' => ['bg' => '#16a34a15', 'color' => '#16a34a'],
        'sapa-warga' => ['bg' => '#fe500015', 'color' => '#fe5000'],
        'kaderisasi' => ['bg' => '#2563eb15', 'color' => '#2563eb'],
    ];
@endphp

<div>
<style>
    .hero-public {
        position: relative;
        overflow: hidden;
        min-height: 400px;
        display: flex;
        align-items: stretch;
        color: #fff;
        background: #091425;
    }
    .hero-media {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(90deg, rgba(8, 15, 30, .82) 0%, rgba(8, 15, 30, .68) 30%, rgba(8, 15, 30, .28) 62%, rgba(8, 15, 30, .12) 100%),
            linear-gradient(180deg, rgba(8, 15, 30, .10) 0%, rgba(8, 15, 30, .28) 76%, rgba(8, 15, 30, .52) 100%),
            url('{{ asset('images/hero.jpg') }}');
        background-size: cover;
        background-position: center center;
        transform: scale(1.02);
    }
    .hero-public::after {
        content: '';
        position: absolute;
        inset: auto 0 0;
        height: 60px;
        background: linear-gradient(180deg, rgba(9, 20, 37, 0) 0%, rgba(9, 20, 37, .72) 100%);
    }
    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        width: 100%;
        padding: 32px 0 28px;
    }
    .hero-panel {
        width: min(100%, 580px);
        padding: 20px 24px;
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 26px;
        background: linear-gradient(180deg, rgba(10, 20, 36, .52) 0%, rgba(10, 20, 36, .36) 100%);
        backdrop-filter: blur(8px);
        box-shadow: 0 18px 48px rgba(0, 0, 0, .24);
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        padding: 7px 16px;
        background: rgba(254, 80, 0, .18);
        border: 1px solid rgba(254, 80, 0, .34);
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #ffd2bf;
    }
    .hero-title {
        margin: 0 0 10px;
        font-size: clamp(24px, 3.5vw, 28px);
        font-weight: 700;
        line-height: 1.14;
        letter-spacing: -.5px;
        text-shadow: 0 3px 18px rgba(0, 0, 0, .2);
    }
    .hero-title span { color: #fe5000; }
    .hero-text {
        max-width: 540px;
        margin: 0 0 16px;
        font-size: 15px;
        line-height: 1.8;
        color: rgba(255, 255, 255, .92);
    }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        transition: .2s;
    }
    .hero-btn.primary { background: #fe5000; color: #fff; }
    .hero-btn.primary:hover { background: #d94000; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(254, 80, 0, .3); }
    .hero-btn.outline {
        border: 1px solid rgba(255, 255, 255, .24);
        background: rgba(255, 255, 255, .08);
        color: #fff;
    }
    .hero-btn.outline:hover { background: rgba(255, 255, 255, .14); border-color: rgba(255, 255, 255, .42); }
    .hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 20px;
    }
    .hero-stat {
        min-width: 90px;
        padding: 8px 12px;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 12px;
        background: rgba(255, 255, 255, .08);
        text-align: center;
    }
    .hero-stat .num { font-size: 20px; font-weight: 800; color: #fff; }
    .hero-stat .label { margin-top: 2px; font-size: 10px; color: rgba(255, 255, 255, .72); }
    .event-grid-public,
    .program-grid-public,
    .dprd-grid-public,
    .stats-grid-public,
    .gallery-grid-public { display: grid; gap: 20px; }
    .event-grid-public { grid-template-columns: repeat(3, 1fr); }
    .event-card-public,
    .news-featured-public,
    .dprd-card-public { background: #fff; border: 1px solid #ececea; box-shadow: 0 2px 12px rgba(0, 0, 0, .08); }
    .event-card-public {
        overflow: hidden;
        border-radius: 14px;
        transition: .3s;
    }
    .event-card-public:hover,
    .program-card-public:hover,
    .dprd-card-public:hover { transform: translateY(-4px); box-shadow: 0 6px 32px rgba(0, 0, 0, .13); }
    .event-card-public:hover { border-color: #dddddb; }
    .event-image-public {
        position: relative;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff3ee, #e8f0fb);
        color: rgba(254, 80, 0, .35);
        font-size: 48px;
    }
    .date-badge-public {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #fe5000;
        color: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        text-align: center;
        line-height: 1.1;
    }
    .date-badge-public .day { display: block; font-size: 20px; font-weight: 800; }
    .date-badge-public .month { font-size: 9px; text-transform: uppercase; letter-spacing: .5px; }
    .event-type-public {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 3px 10px;
        background: #fff;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        color: #1a3a6b;
    }
    .event-body-public { padding: 16px; }
    .event-body-public h3 {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.3;
        color: #1a1a18;
    }
    .event-meta-public {
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin-bottom: 12px;
        font-size: 12px;
        color: #9a9890;
    }
    .event-btn-public {
        display: block;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        background: #fe5000;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }
    .event-btn-public:hover { background: #d94000; }
    .program-grid-public { grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .program-card-public {
        padding: 24px 20px;
        border-radius: 14px;
        border: 1px solid #ececea;
        background: #fff;
        text-align: center;
        transition: .3s;
    }
    .program-icon-public {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border-radius: 14px;
        font-size: 24px;
    }
    .program-card-public h3 {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 600;
        color: #1a1a18;
    }
    .program-card-public p { margin: 0; font-size: 17px; line-height: 1.8; color: #71717a; }
    .program-stat-public { margin-top: 10px; font-size: 20px; font-weight: 800; }
    .program-stat-public span { font-size: 12px; color: #9a9890; }
    .news-layout-public { display: grid; grid-template-columns: 7fr 5fr; gap: 20px; }
    .news-featured-public { overflow: hidden; border-radius: 14px; }
    .news-featured-image-public {
        position: relative;
        height: 260px;
        display: flex;
        align-items: flex-end;
        padding: 20px;
        background: linear-gradient(135deg, #1a3a6b, #2155a0);
    }
    .news-featured-image-public::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(transparent 40%, rgba(0, 0, 0, .7));
    }
    .news-featured-image-public h3 {
        position: relative;
        z-index: 2;
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.3;
        color: #fff;
    }
    .news-featured-body-public { padding: 16px; }
    .news-featured-body-public p { margin: 0; max-width: 680px; font-size: 18px; line-height: 1.8; color: #71717a; }
    .news-featured-body-public .date { margin-top: 8px; font-size: 11px; font-weight: 600; color: #fe5000; }
    .news-list-public { display: flex; flex-direction: column; }
    .news-item-public {
        display: flex;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #ececea;
    }
    .news-item-public:last-child { border-bottom: none; }
    .news-thumb-public {
        flex-shrink: 0;
        width: 90px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #ececea;
        color: #9a9890;
        font-size: 20px;
    }
    .news-item-public h4 {
        margin: 0 0 4px;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.4;
        color: #1a1a18;
    }
    .news-item-public .date { font-size: 11px; color: #9a9890; }
    .dprd-grid-public { grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .dprd-card-public {
        overflow: hidden;
        border-radius: 14px;
        text-align: center;
        transition: .3s;
    }
    .dprd-photo-public {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e8f0fb, #ececea);
        color: #dddddb;
        font-size: 48px;
    }
    .dprd-info-public { padding: 14px; }
    .dprd-info-public h4 { margin: 0; font-size: 14px; font-weight: 700; color: #1a1a18; }
    .dprd-info-public .jabatan { margin-top: 2px; font-size: 11px; color: #9a9890; }
    .dprd-info-public .dapil {
        display: inline-block;
        margin-top: 6px;
        padding: 3px 10px;
        border-radius: 12px;
        background: #fff3ee;
        color: #fe5000;
        font-size: 10px;
        font-weight: 600;
    }
    .dprd-socials-public {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #ececea;
    }
    .dprd-socials-public a {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f7f7f5;
        color: #9a9890;
        font-size: 12px;
    }
    .dprd-socials-public a:hover { background: #fe5000; color: #fff; }
    .stats-public {
        position: relative;
        overflow: hidden;
        padding: 48px 0;
        background: linear-gradient(135deg, #1a3a6b 0%, #0d1f3c 100%);
        color: #fff;
    }
    .stats-public::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: .04;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 0L40 20L20 40L0 20z' fill='%23fff' fill-opacity='1'/%3E%3C/svg%3E");
    }
    .stats-grid-public {
        position: relative;
        z-index: 2;
        grid-template-columns: repeat(5, 1fr);
        text-align: center;
    }
    .stats-item-public .num { font-size: 36px; font-weight: 800; color: #fe5000; }
    .stats-item-public .label { margin-top: 4px; font-size: 12px; color: rgba(255, 255, 255, .7); }
    .gallery-grid-public { grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .gallery-item-public {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        border-radius: 8px;
        background: #ececea;
    }
    .gallery-item-public.featured {
        grid-column: span 2;
        grid-row: span 2;
        aspect-ratio: auto;
    }
    .gallery-placeholder-public {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f7f7f5, #ececea);
        color: #dddddb;
        font-size: 32px;
    }
    .gallery-overlay-public {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0);
        transition: .3s;
        color: #fff;
        font-size: 24px;
        opacity: 0;
    }
    .gallery-item-public:hover .gallery-overlay-public { background: rgba(254, 80, 0, .4); opacity: 1; }
    .cta-public {
        position: relative;
        overflow: hidden;
        padding: 56px 0;
        background: #fe5000;
        text-align: center;
        color: #fff;
    }
    .cta-public::before,
    .cta-public::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }
    .cta-public::before { top: -60px; right: -60px; width: 200px; height: 200px; }
    .cta-public::after { bottom: -40px; left: -40px; width: 150px; height: 150px; background: rgba(255, 255, 255, .06); }
    .cta-public h2 {
        position: relative;
        z-index: 2;
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 800;
    }
    .cta-public p {
        position: relative;
        z-index: 2;
        max-width: 680px;
        margin: 0 auto 24px;
        font-size: 18px;
        line-height: 1.8;
        color: rgba(255, 255, 255, .8);
    }
    .cta-public .btn {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 36px;
        border-radius: 30px;
        background: #fff;
        color: #fe5000;
        font-size: 15px;
        font-weight: 700;
    }
    .cta-public .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, .2); }
    @media (max-width: 768px) {
        .hero-public {
            min-height: auto;
        }
        .hero-media {
            background-position: 62% center;
        }
        .hero-content { padding: 36px 0 32px; }
        .hero-panel {
            padding: 22px 18px;
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(10, 20, 36, .72) 0%, rgba(10, 20, 36, .54) 100%);
        }
        .hero-title {
            font-size: 28px;
        }
        .hero-text {
            font-size: 17px;
        }
        .hero-stats { gap: 10px; }
        .hero-stat {
            min-width: calc(50% - 5px);
        }
        .event-grid-public,
        .news-layout-public,
        .program-grid-public,
        .dprd-grid-public { grid-template-columns: 1fr; }
        .program-grid-public { grid-template-columns: 1fr 1fr; }
        .stats-grid-public { grid-template-columns: repeat(3, 1fr); }
        .gallery-grid-public { grid-template-columns: 1fr 1fr; }
        #aspirasi-home-grid { grid-template-columns: 1fr !important; }
    }
    
    .aspirasi-stat-box {
        transition: transform 0.2s, background-color 0.2s, border-color 0.2s;
    }
    .aspirasi-stat-box:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        transform: translateY(-2px);
    }
</style>


<section class="hero-public">
    <div class="hero-media"></div>
    <div class="container">
        <div class="hero-content fade-up">
            <div class="hero-panel">
                <div class="hero-badge"><i class="ti ti-bullhorn"></i> Menuju Pemilu 2029</div>
                <h1 class="hero-title">Kabupaten <span>Bekasi Hebat</span> dimulai dari pelayanan yang dekat dengan warga</h1>
                <p class="hero-text">Gerakan kolaboratif untuk menghadirkan pelayanan sosial, penguatan komunitas, dan pemberdayaan masyarakat sampai ke tingkat RW di seluruh Kabupaten Bekasi.</p>
                <div class="hero-actions">
                    <a href="{{ route('register') }}" wire:navigate class="hero-btn primary"><i class="ti ti-user-plus"></i> Bergabung Sekarang</a>
                    <a href="#program" class="hero-btn outline"><i class="ti ti-player-play"></i> Lihat Program</a>
                </div>
                <div class="hero-stats">
                    @foreach ([
                        ['label' => 'Dapil', 'value' => $stats['dapil']],
                        ['label' => 'Kecamatan', 'value' => $stats['kecamatan']],
                        ['label' => 'Desa', 'value' => $stats['desa']],
                        ['label' => 'RW Terjangkau', 'value' => $stats['rw']],
                    ] as $item)
                        <div class="hero-stat">
                            <div class="num" data-counter="{{ $item['value'] }}">0</div>
                            <div class="label">{{ $item['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="event">
    <div class="container">
        <div class="section-header center fade-up">
            <div class="section-tag orange">Segera Hadir</div>
            <h2 class="section-title">Event Mendatang</h2>
            <p class="section-subtitle center">Ikuti kegiatan kami dan jadilah bagian dari perubahan</p>
        </div>
        @if ($events->isEmpty())
            <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Belum ada event mendatang.</div>
        @else
            <div class="event-grid-public">
                @foreach ($events as $event)
                    @php $eventImageUrl = $event->cover_image ? '/storage/' . ltrim($event->cover_image, '/') : null; @endphp
                    <article class="event-card-public premium-hover-card fade-up">
                        <div class="event-image-public">
                            @if ($eventImageUrl)
                                <img src="{{ $eventImageUrl }}" alt="{{ $event->judul }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';">
                            @else
                                <i class="ti ti-calendar-event"></i>
                            @endif
                            <div class="date-badge-public">
                                <span class="day">{{ $event->tanggal_mulai?->translatedFormat('d') }}</span>
                                <span class="month">{{ $event->tanggal_mulai?->translatedFormat('M') }}</span>
                            </div>
                            <div class="live-pulse" style="position:absolute; top:12px; right:12px;">{{ $event->jenis_label }}</div>
                        </div>
                        <div class="event-body-public">
                            <h3>{{ $event->judul }}</h3>
                            <div class="event-meta-public">
                                <span><i class="ti ti-clock" style="width:14px;color:#fe5000;"></i> {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB</span>
                                <span><i class="ti ti-map-pin" style="width:14px;color:#fe5000;"></i> {{ $event->lokasi ?: 'Lokasi menyusul' }}</span>
                                <span><i class="ti ti-users" style="width:14px;color:#fe5000;"></i> {{ number_format($event->registrations_count) }} pendaftar</span>
                            </div>
                            <a href="{{ route('public.events.show', $event->slug) }}" wire:navigate class="event-btn-public btn-sliding-arrow">Daftar Sekarang <i class="ti ti-arrow-right"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section section-white" id="program">
    <div class="container">
        <div class="section-header center fade-up">
            <div class="section-tag blue">Program Kami</div>
            <h2 class="section-title">Program Unggulan</h2>
            <p class="section-subtitle center">Pelayanan nyata untuk warga Kabupaten Bekasi</p>
        </div>
        <div class="program-grid-public">
            @foreach ($programs as $program)
                @php $palette = $programCardPalette[$program['key']] ?? ['bg' => '#fff3ee', 'color' => '#fe5000']; @endphp
                <div class="program-card-public premium-hover-card fade-up">
                    <div class="program-icon-public" style="background:{{ $palette['bg'] }};color:{{ $palette['color'] }};">
                        <i class="{{ $program['icon'] }}"></i>
                    </div>
                    <h3>{{ $program['label'] }}</h3>
                    <p>{{ $program['description'] }}</p>
                    <div class="program-stat-public" style="color:{{ $palette['color'] }};">
                        <span data-counter="{{ $program['count'] }}">0</span> <span>{{ $program['target_label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-glow-container" id="berita">
    <div class="bg-glow-accent bg-glow-orange" style="top: -50px; left: -50px;"></div>
    <div class="bg-glow-accent bg-glow-blue" style="bottom: -50px; right: -50px;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="section-header center fade-up">
            <div class="section-tag orange">Multimedia & Terkini</div>
            <h2 class="section-title">Kabar & Dokumentasi</h2>
            <p class="section-subtitle center">Update berita, foto kegiatan, dan suara realisasi Bekasi Hebat</p>
        </div>

        <!-- Dynamic Tab Switcher Buttons -->
        <div class="fade-up" style="display: flex; justify-content: center; gap: 12px; margin-bottom: 36px; flex-wrap: wrap;">
            <button class="dynamic-tab-btn active" onclick="switchTab('tab-berita', this)">
                <i class="ti ti-news" style="font-size: 15px; vertical-align: middle; margin-right: 4px;"></i> Berita Terbaru
            </button>
            <button class="dynamic-tab-btn" onclick="switchTab('tab-galeri', this)">
                <i class="ti ti-photo" style="font-size: 15px; vertical-align: middle; margin-right: 4px;"></i> Galeri Kegiatan
            </button>
            <button class="dynamic-tab-btn" onclick="switchTab('tab-aspirasi-realisasi', this)">
                <i class="ti ti-checkbox" style="font-size: 15px; vertical-align: middle; margin-right: 4px;"></i> Realisasi Aspirasi
            </button>
        </div>

        <!-- Tab 1: Berita Terbaru -->
        <div id="tab-berita" class="dynamic-tab-content active">
            @if ($newsFeatured)
                @php $featuredNewsImageUrl = $newsFeatured->thumbnail ? '/storage/' . ltrim($newsFeatured->thumbnail, '/') : null; @endphp
                <div class="news-layout-public fade-up">
                    <div class="news-featured-public premium-hover-card" style="display:flex; flex-direction:column; justify-content:space-between; height:100%;">
                        <div>
                            <a href="{{ route('public.berita.show', $newsFeatured->slug) }}" wire:navigate style="display:block; text-decoration:none;">
                                <div class="news-featured-image-public">
                                    @if ($featuredNewsImageUrl)
                                        <img src="{{ $featuredNewsImageUrl }}" alt="{{ $newsFeatured->judul }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';">
                                    @endif
                                    <h3 style="transition:.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#fff'">{{ $newsFeatured->judul }}</h3>
                                </div>
                            </a>
                            <div class="news-featured-body-public">
                                <p>{{ $newsFeatured->ringkasan }}</p>
                            </div>
                        </div>
                        <div style="padding:0 24px 24px 24px; display:flex; justify-content:space-between; align-items:center;">
                            <div class="date" style="font-size:12px; color:#9a9890;">
                                <i class="ti ti-calendar"></i>
                                {{ $newsFeatured->published_at?->translatedFormat('d F Y') }} · {{ number_format($newsFeatured->views) }} views
                            </div>
                            <a href="{{ route('public.berita.show', $newsFeatured->slug) }}" wire:navigate class="btn-sliding-arrow" style="font-size:13px; font-weight:700; color:#ea580c; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#ea580c'">
                                Selengkapnya <i class="ti ti-arrow-right" style="font-size:14px;"></i>
                            </a>
                        </div>
                    </div>
                    <div class="news-list-public">
                        @foreach ($newsList as $item)
                            @php $newsThumbUrl = $item->thumbnail ? '/storage/' . ltrim($item->thumbnail, '/') : null; @endphp
                            <a href="{{ route('public.berita.show', $item->slug) }}" wire:navigate style="text-decoration:none; color:inherit; display:block;">
                                <div class="news-item-public" style="transition:.2s; border-radius:12px; padding:8px;" onmouseover="this.style.background='#fff3ee'" onmouseout="this.style.background='transparent'">
                                    <div class="news-thumb-public">
                                        @if ($newsThumbUrl)
                                            <img src="{{ $newsThumbUrl }}" alt="{{ $item->judul }}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div style="display:none;width:100%;height:100%;align-items:center;justify-content:center;"><i class="ti ti-news"></i></div>
                                        @else
                                            <i class="ti ti-news"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 style="transition:.2s; font-size:15px;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#1a1a18'">{{ $item->judul }}</h4>
                                        <div class="date">{{ $item->published_at?->translatedFormat('d M Y') }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Berita publik belum tersedia.</div>
            @endif
        </div>

        <!-- Tab 2: Galeri Dokumentasi -->
        <div id="tab-galeri" class="dynamic-tab-content">
            @if ($galleryItems->isEmpty())
                <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Galeri publik belum tersedia.</div>
            @else
                <div class="gallery-grid-public fade-up">
                    @foreach ($galleryItems as $index => $item)
                        @php
                            $galleryImagePath = $item->file_path ?: $item->thumbnail;
                            $galleryImageUrl = $galleryImagePath ? '/storage/' . ltrim($galleryImagePath, '/') : null;
                        @endphp
                        <a href="{{ route('public.galeri') }}" wire:navigate class="gallery-item-public premium-hover-card {{ $index === 0 ? 'featured' : '' }}" style="display: block; position: relative; overflow: hidden; border-radius: 8px;">
                            @if ($galleryImageUrl)
                                <img src="{{ $galleryImageUrl }}" alt="{{ $item->judul }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="gallery-placeholder-public" style="display:none;"><i class="ti ti-photo"></i></div>
                            @else
                                <div class="gallery-placeholder-public"><i class="ti ti-photo"></i></div>
                            @endif
                            <div class="gallery-overlay-public" style="flex-direction:column;gap:6px;padding:16px;text-align:center;background:linear-gradient(transparent,rgba(0,0,0,.75));opacity:1;align-items:center;justify-content:flex-end;">
                                <strong style="font-size:17px;font-weight:600;line-height:1.3;color:#fff;">{{ $item->judul }}</strong>
                                <span style="font-size:11px;color:rgba(255,255,255,.84);"><i class="ti ti-map-pin"></i> {{ $item->lokasi ?: 'Kabupaten Bekasi' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab 3: Realisasi Aspirasi -->
        <div id="tab-aspirasi-realisasi" class="dynamic-tab-content">
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                @forelse ($aspirasiStories as $story)
                    <div class="premium-hover-card" style="padding:20px; border-radius:14px; border:1px solid #ececea; background:#fff; box-shadow:0 4px 14px rgba(0,0,0,.04); display:flex; flex-direction:column; justify-content:space-between; min-height:160px; transition: 0.3s;">
                        <div>
                            <div style="display:inline-flex; align-items:center; gap:6px; background:#fff3ee; color:#fe5000; font-size:10px; font-weight:700; padding:4px 10px; border-radius:12px; margin-bottom:10px; text-transform:uppercase;">
                                <i class="ti ti-circle-check"></i> Terealisasi
                            </div>
                            <h4 style="font-size:16px; font-weight:700; color:#1a1a18; margin:0 0 6px 0; line-height:1.4;">{{ $story->judul }}</h4>
                            <p style="font-size:12px; color:#9a9890; margin:0;">
                                <i class="ti ti-map-pin"></i> Desa {{ $story->desa ?: '-' }} &bull; <i class="ti ti-user"></i> {{ $story->assignedDewan?->nama ?? 'Dewan PKS' }}
                            </p>
                        </div>
                        <div style="font-size:13px; color:#166534; font-weight:800; margin-top:12px; border-top:1px solid #f4f4f5; padding-top:10px;">
                            {{ $story->anggaran_nominal ? 'Anggaran: Rp '.number_format((float) $story->anggaran_nominal, 0, ',', '.') : 'Realisasi Pembangunan Terlaksana' }}
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1/-1; padding:32px; border-radius:14px; border:1px dashed #dddddb; background:#fff; text-align:center; font-size:14px; color:#9a9890;">
                        Belum ada data realisasi pembangunan yang dipublikasikan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</section>

<section class="section section-white bg-glow-container" id="dprd">
    <div class="bg-glow-accent bg-glow-blue" style="top: -120px; left: -120px;"></div>
    <div class="bg-glow-accent bg-glow-orange" style="bottom: -120px; right: -120px;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="section-header center fade-up">
            <div class="section-tag blue">Wakil Rakyat</div>
            <h2 class="section-title">Anggota DPRD PKS</h2>
            <p class="section-subtitle center">Melayani dengan amanah di Kabupaten Bekasi</p>
        </div>
        <div class="dprd-grid-public fade-up">
            @forelse ($dprd as $anggota)
                <div class="dprd-card-public premium-hover-card">
                    <div class="dprd-photo-public">
                        @if ($anggota->foto)
                            <img src="{{ Storage::url($anggota->foto) }}" alt="{{ $anggota->nama }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="ti ti-user"></i>
                        @endif
                    </div>
                    <div class="dprd-info-public">
                        <h4>{{ $anggota->nama }}</h4>
                        <div class="jabatan">{{ $anggota->jabatan }}</div>
                        <div class="dapil">{{ $anggota->dapil }}</div>
                    </div>
                    <div class="dprd-socials-public">
                        @foreach ($anggota->platforms as $platform)
                            <a href="#"><i class="ti ti-{{ $platform['name'] === 'twitter' ? 'brand-x' : 'brand-'.$platform['name'] }}"></i></a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="event-card-public" style="padding:32px;text-align:center;grid-column:1/-1;">Data anggota DPRD belum tersedia.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="stats-public">
    <div class="container">
        <div class="stats-grid-public fade-up">
            @foreach ([
                ['label' => 'Event Terselenggara', 'value' => $stats['event_total']],
                ['label' => 'Warga Terjangkau', 'value' => $stats['warga_terjangkau']],
                ['label' => 'Kader Aktif', 'value' => $stats['kader_aktif']],
                ['label' => 'Titik Senam', 'value' => $stats['titik_senam']],
                ['label' => 'Anggota Terdaftar', 'value' => $stats['member']],
            ] as $item)
                <div class="stats-item-public">
                    <div class="num" data-counter="{{ $item['value'] }}">0</div>
                    <div class="label">{{ $item['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-white bg-glow-container" id="galeri">
    <div class="bg-glow-accent bg-glow-orange" style="top: -120px; right: -120px;"></div>
    <div class="bg-glow-accent bg-glow-blue" style="bottom: -120px; left: -120px;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="section-header center fade-up">
            <div class="section-tag orange">Dokumentasi</div>
            <h2 class="section-title">Galeri Kegiatan</h2>
        </div>
        @if ($galleryItems->isEmpty())
            <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Galeri publik belum tersedia.</div>
        @else
            <div class="gallery-grid-public fade-up">
                @foreach ($galleryItems as $index => $item)
                    @php
                        $galleryImagePath = $item->file_path ?: $item->thumbnail;
                        $galleryImageUrl = $galleryImagePath ? '/storage/' . ltrim($galleryImagePath, '/') : null;
                    @endphp
                    <a href="{{ route('public.galeri') }}" wire:navigate class="gallery-item-public premium-hover-card {{ $index === 0 ? 'featured' : '' }}" style="display: block;">
                        @if ($galleryImageUrl)
                            <img src="{{ $galleryImageUrl }}" alt="{{ $item->judul }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="gallery-placeholder-public" style="display:none;"><i class="ti ti-photo"></i></div>
                        @else
                            <div class="gallery-placeholder-public"><i class="ti ti-photo"></i></div>
                        @endif
                        <div class="gallery-overlay-public" style="flex-direction:column;gap:6px;padding:16px;text-align:center;background:linear-gradient(transparent,rgba(0,0,0,.75));opacity:1;align-items:center;justify-content:flex-end;">
                            <strong style="font-size:17px;font-weight:600;line-height:1.3;">{{ $item->judul }}</strong>
                            <span style="font-size:11px;color:rgba(255,255,255,.84);">{{ $item->lokasi ?: 'Kabupaten Bekasi' }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section section-white" id="aspirasi">
    <div class="container">
        <div class="section-header center fade-up">
            <div class="section-tag blue">Aspirasi Warga</div>
            <h2 class="section-title">Aspirasi yang terus diperjuangkan</h2>
            <p class="section-subtitle center">Transparansi progres aspirasi warga dari pengusulan hingga realisasi.</p>
        </div>
        <div class="fade-up" style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(320px,.8fr);gap:20px;" id="aspirasi-home-grid">
            <div style="padding:28px;border-radius:24px;background:linear-gradient(135deg,#1a3a6b,#0d1f3c);color:white;box-shadow:0 10px 30px rgba(13,31,60,0.15);display:flex;flex-direction:column;justify-content:space-between;">
                @php
                    $totalAspirasi = (int) ($aspirasiStats['total'] ?? 0);
                    $pctSipd = $totalAspirasi > 0 ? round((($aspirasiStats['sipd'] ?? 0) / $totalAspirasi) * 100) : 0;
                    $pctDianggarkan = $totalAspirasi > 0 ? round((($aspirasiStats['dianggarkan'] ?? 0) / $totalAspirasi) * 100) : 0;
                    $pctTerealisasi = $totalAspirasi > 0 ? round((($aspirasiStats['terealisasi'] ?? 0) / $totalAspirasi) * 100) : 0;
                @endphp
                <div>
                    <!-- Header with icon -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="font-size:13px;color:rgba(255,255,255,.7);font-weight:500;letter-spacing:0.02em;">Aspirasi diperjuangkan</div>
                            <div style="font-size:48px;font-weight:900;line-height:1.1;margin-top:6px;letter-spacing:-0.03em;">{{ number_format($totalAspirasi) }}</div>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;">
                            <i class="ti ti-speakerphone" style="font-size:24px;color:rgba(255,255,255,0.85);"></i>
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:24px;">
                        <div class="aspirasi-stat-box" style="padding:14px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,0.04);transition:0.2s;">
                            <i class="ti ti-file-text" style="font-size:18px;color:rgba(255,255,255,0.6);margin-bottom:8px;display:block;"></i>
                            <div style="font-size:11px;color:rgba(255,255,255,.72);font-weight:500;">Masuk SIPD</div>
                            <div style="font-size:24px;font-weight:800;margin-top:4px;">{{ number_format($aspirasiStats['sipd']) }}</div>
                        </div>
                        <div class="aspirasi-stat-box" style="padding:14px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,0.04);transition:0.2s;">
                            <i class="ti ti-coins" style="font-size:18px;color:rgba(255,255,255,0.6);margin-bottom:8px;display:block;"></i>
                            <div style="font-size:11px;color:rgba(255,255,255,.72);font-weight:500;">Dianggarkan</div>
                            <div style="font-size:24px;font-weight:800;margin-top:4px;">{{ number_format($aspirasiStats['dianggarkan']) }}</div>
                        </div>
                        <div class="aspirasi-stat-box" style="padding:14px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,0.04);transition:0.2s;">
                            <i class="ti ti-circle-check" style="font-size:18px;color:rgba(255,255,255,0.6);margin-bottom:8px;display:block;"></i>
                            <div style="font-size:11px;color:rgba(255,255,255,.72);font-weight:500;">Terealisasi</div>
                            <div style="font-size:24px;font-weight:800;margin-top:4px;">{{ number_format($aspirasiStats['terealisasi']) }}</div>
                        </div>
                    </div>

                    <!-- Pipeline Progress Bar -->
                    <div style="margin-top:28px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:rgba(255,255,255,0.7);margin-bottom:8px;font-weight:500;">
                            <span>Progress Pipeline Realisasi</span>
                            <span style="color:#22c55e;font-weight:700;">{{ $pctTerealisasi }}% Terealisasi</span>
                        </div>
                        <div style="height:10px;border-radius:999px;background:rgba(255,255,255,0.1);overflow:hidden;display:flex;gap:2px;">
                            <div style="width:{{ $pctTerealisasi }}%;background:#22c55e;" title="Terealisasi: {{ $pctTerealisasi }}%"></div>
                            <div style="width:{{ $pctDianggarkan }}%;background:#ea580c;" title="Dianggarkan: {{ $pctDianggarkan }}%"></div>
                            <div style="width:{{ $pctSipd }}%;background:#3b82f6;" title="Masuk SIPD: {{ $pctSipd }}%"></div>
                        </div>
                        <div style="display:flex;gap:12px;margin-top:12px;font-size:11px;flex-wrap:wrap;color:rgba(255,255,255,0.8);">
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>
                                <span>Terealisasi ({{ $pctTerealisasi }}%)</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ea580c;"></span>
                                <span>Dianggarkan ({{ $pctDianggarkan }}%)</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#3b82f6;"></span>
                                <span>Masuk SIPD ({{ $pctSipd }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kirim Aspirasi Footer Call-To-Action -->
                <div style="margin-top:32px;border-top:1px solid rgba(255,255,255,0.1);padding-top:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div style="font-size:12px;color:rgba(255,255,255,0.7);max-width:240px;line-height:1.5;font-weight:500;">
                        Punya usulan pembangunan untuk daerah Anda? Salurkan aspirasi Anda secara online.
                    </div>
                    <a href="{{ route('public.aspirasi') }}" wire:navigate style="background:white;color:#1a3a6b;font-weight:700;padding:10px 20px;border-radius:12px;font-size:13px;text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,0.15);transition:0.2s;display:inline-flex;align-items:center;gap:6px;" onmouseover="this.style.background='#fff3ee'; this.style.color='#fe5000'" onmouseout="this.style.background='white'; this.style.color='#1a3a6b'">
                        Kirim Aspirasi <i class="ti ti-arrow-right" style="font-size:14px;"></i>
                    </a>
                </div>
            </div>
            <div style="display:grid;gap:10px;">
                @forelse ($aspirasiStories as $story)
                    <div class="premium-hover-card" style="padding:14px;border-radius:14px;border:1px solid #ececea;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.06); transition: 0.3s;">
                        <div style="font-size:14px;font-weight:700;color:#1a1a18;">{{ $story->judul }}</div>
                        <div style="font-size:11px;color:#9a9890;margin-top:4px;">{{ $story->desa ?: '-' }} · {{ $story->assignedDewan?->nama ?? 'Dewan PKS' }}</div>
                        <div style="font-size:12px;color:#166534;font-weight:700;margin-top:8px;">{{ $story->anggaran_nominal ? 'Rp '.number_format((float) $story->anggaran_nominal, 0, ',', '.') : 'Progres realisasi tercatat' }}</div>
                    </div>
                @empty
                    <div style="padding:20px;border-radius:14px;border:1px dashed #dddddb;background:#fff;text-align:center;font-size:13px;color:#9a9890;">
                        Success story aspirasi akan tampil setelah ada realisasi.
                    </div>
                @endforelse
                <a href="{{ route('public.aspirasi') }}" wire:navigate class="hero-btn primary" style="justify-content:center;">
                    <i class="ti ti-message-plus"></i> Sampaikan Aspirasi
                </a>
            </div>
        </div>
    </div>
</section>

<section class="cta-public" id="daftar">
    <div class="container">
        <div class="fade-up">
            <h2>Bergabung Bersama Kami</h2>
            <p>Daftar sekarang dan dapatkan kartu anggota digital. Ikuti event, jadi bagian dari perubahan.</p>
            <a href="{{ route('register') }}" wire:navigate class="btn"><i class="ti ti-id-badge-2"></i> Daftar & Dapatkan Kartu Anggota</a>
        </div>
    </div>
</section>

<script>
    window.switchTab = (tabId, btn) => {
        document.querySelectorAll('.dynamic-tab-content').forEach(el => {
            el.classList.remove('active');
        });
        document.querySelectorAll('.dynamic-tab-btn').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    };

    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-up').forEach((item) => observer.observe(item));

        const animateCounters = (root) => {
            root.querySelectorAll('[data-counter]').forEach((el) => {
                if (el.dataset.done === '1') return;
                el.dataset.done = '1';
                const target = parseInt(el.dataset.counter || '0', 10);
                const duration = 1500;
                const start = Date.now();

                const update = () => {
                    const elapsed = Date.now() - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
                    if (progress < 1) requestAnimationFrame(update);
                };

                update();
            });
        };

        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                animateCounters(entry.target);
                statsObserver.unobserve(entry.target);
            });
        }, { threshold: 0.3 });

        document.querySelectorAll('.hero-stats, .stats-public, .program-grid-public').forEach((item) => statsObserver.observe(item));
    });
</script>
</div>
