<div class="section" style="background: #fafafa; padding-bottom: 60px;">
    <div class="container">
        <!-- Breadcrumb & Back Link -->
        <div style="margin-top: 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 14px; color: #71717a;">
            <a href="{{ route('public.home') }}" wire:navigate style="color: #ea580c; text-decoration: none; font-weight: 500;">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.berita') }}" wire:navigate style="color: #ea580c; text-decoration: none; font-weight: 500;">Berita</a>
            <span>/</span>
            <span style="color: #fe5000; font-weight: 600;">{{ \App\Models\Berita::KATEGORI_OPTIONS[$berita->kategori] ?? ucfirst($berita->kategori) }}</span>
        </div>

        <div class="berita-detail-layout" style="display: grid; grid-template-columns: 8fr 4fr; gap: 32px; align-items: start;">
            <!-- Main Content -->
            <article style="background: white; border-radius: 20px; border: 1px solid #e4e4e7; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); overflow: hidden; padding: 32px;">
                <!-- Category Badge -->
                <span style="display: inline-flex; padding: 6px 14px; border-radius: 999px; background: #fff3ee; color: #fe5000; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">
                    {{ \App\Models\Berita::KATEGORI_OPTIONS[$berita->kategori] ?? ucfirst($berita->kategori) }}
                </span>

                <!-- Title -->
                <h1 style="font-size: 32px; font-weight: 800; color: #18181b; line-height: 1.3; margin: 0 0 16px 0;">
                    {{ $berita->judul }}
                </h1>

                <!-- Meta Info -->
                <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center; font-size: 13px; color: #71717a; border-bottom: 1px solid #f4f4f5; padding-bottom: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ti ti-user" style="font-size: 16px; color: #ea580c;"></i>
                        <span>Oleh: <strong style="color: #18181b;">{{ $berita->penulis ?? 'Redaksi' }}</strong></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ti ti-calendar" style="font-size: 16px;"></i>
                        <span>{{ $berita->published_at?->translatedFormat('d F Y H:i') }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i class="ti ti-eye" style="font-size: 16px;"></i>
                        <span>{{ number_format($berita->views) }} Kali Dibaca</span>
                    </div>
                </div>

                <!-- Featured Image -->
                @if ($berita->thumbnail)
                    <div style="border-radius: 16px; overflow: hidden; margin-bottom: 28px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05); border: 1px solid #ececea;">
                        <img 
                            src="/storage/{{ ltrim($berita->thumbnail, '/') }}" 
                            alt="{{ $berita->judul }}" 
                            style="width: 100%; height: auto; max-height: 480px; object-fit: cover; display: block;"
                            onerror="this.style.display='none';"
                        >
                    </div>
                @endif

                <!-- Article Content -->
                <div class="article-body" style="font-size: 17px; line-height: 1.9; color: #3f3f46;">
                    {!! $berita->konten !!}
                </div>

                <!-- Associated Event Link -->
                @if ($berita->event)
                    <div style="margin-top: 40px; padding: 24px; background: linear-gradient(135deg, #fff3ee, #fff8f5); border-radius: 16px; border: 1px dashed #fe5000; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
                        <div>
                            <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #fe5000;">Berita Ini Terkait dengan Event:</h4>
                            <p style="margin: 4px 0 0 0; font-size: 14px; color: #52525b; font-weight: 500;">{{ $berita->event->judul }}</p>
                        </div>
                        <a href="{{ route('public.events.show', $berita->event->slug) }}" wire:navigate class="btn" style="background: #fe5000; color: white; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s;">
                            Lihat Detail Event
                        </a>
                    </div>
                @endif

                <!-- Back Button -->
                <div style="margin-top: 40px; border-top: 1px solid #f4f4f5; padding-top: 24px; display: flex; justify-content: space-between;">
                    <a href="{{ route('public.berita') }}" wire:navigate style="display: inline-flex; align-items: center; gap: 8px; color: #ea580c; text-decoration: none; font-size: 15px; font-weight: 600; transition: 0.2s;">
                        <i class="ti ti-arrow-left"></i> Kembali ke Berita
                    </a>
                </div>
            </article>

            <!-- Sidebar -->
            <aside style="display: grid; gap: 32px;">
                <!-- Latest News widget -->
                <div style="background: white; border-radius: 20px; border: 1px solid #e4e4e7; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); padding: 24px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #18181b; margin: 0 0 16px 0; border-bottom: 2px solid #fe5000; padding-bottom: 8px; display: inline-block;">
                        Berita Terbaru
                    </h3>
                    <div style="display: grid; gap: 16px;">
                        @foreach ($this->latestBerita as $latest)
                            <a href="{{ route('public.berita.show', $latest->slug) }}" wire:navigate style="display: flex; gap: 12px; text-decoration: none; group; transition: 0.2s;">
                                @if ($latest->thumbnail)
                                    <img 
                                        src="/storage/{{ ltrim($latest->thumbnail, '/') }}" 
                                        alt="{{ $latest->judul }}" 
                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ececea; flex-shrink: 0;"
                                    >
                                @else
                                    <div style="width: 70px; height: 70px; background: #f4f4f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #a1a1aa;">
                                        <i class="ti ti-newspaper" style="font-size: 20px;"></i>
                                    </div>
                                @endif
                                <div style="display: flex; flex-direction: column; justify-content: center;">
                                    <span style="font-size: 11px; font-weight: 600; color: #fe5000; text-transform: uppercase;">
                                        {{ \App\Models\Berita::KATEGORI_OPTIONS[$latest->kategori] ?? $latest->kategori }}
                                    </span>
                                    <h4 style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600; color: #27272a; line-height: 1.4; transition: 0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#27272a'">
                                        {{ Str::limit($latest->judul, 55) }}
                                    </h4>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Related News widget -->
                @if ($this->relatedBerita->count() > 0)
                    <div style="background: white; border-radius: 20px; border: 1px solid #e4e4e7; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); padding: 24px;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #18181b; margin: 0 0 16px 0; border-bottom: 2px solid #fe5000; padding-bottom: 8px; display: inline-block;">
                            Berita Sejenis
                        </h3>
                        <div style="display: grid; gap: 16px;">
                            @foreach ($this->relatedBerita as $related)
                                <a href="{{ route('public.berita.show', $related->slug) }}" wire:navigate style="display: flex; gap: 12px; text-decoration: none; transition: 0.2s;">
                                    @if ($related->thumbnail)
                                        <img 
                                            src="/storage/{{ ltrim($related->thumbnail, '/') }}" 
                                            alt="{{ $related->judul }}" 
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ececea; flex-shrink: 0;"
                                        >
                                    @else
                                        <div style="width: 70px; height: 70px; background: #f4f4f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #a1a1aa;">
                                            <i class="ti ti-newspaper" style="font-size: 20px;"></i>
                                        </div>
                                    @endif
                                    <div style="display: flex; flex-direction: column; justify-content: center;">
                                        <span style="font-size: 12px; color: #71717a;">
                                            {{ $related->published_at?->translatedFormat('d M Y') }}
                                        </span>
                                        <h4 style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600; color: #27272a; line-height: 1.4; transition: 0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#27272a'">
                                            {{ Str::limit($related->judul, 55) }}
                                        </h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>

<style>
    .article-body p {
        margin-top: 0;
        margin-bottom: 1.25rem;
    }
    .article-body p:last-child {
        margin-bottom: 0;
    }
    
    @media (max-width: 992px) {
        .berita-detail-layout {
            grid-template-columns: 1fr !important;
            gap: 24px !important;
        }
    }
</style>
