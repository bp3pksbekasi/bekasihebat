<div class="section">
    <div class="container">
        <div style="background:white; border-radius:16px; padding:24px; margin-top:24px; border:1px solid #f4f4f5;">
            <h1 style="font-size:30px; font-weight:700; color:#18181b; line-height:1.2;">Ruang Berita Publik</h1>
            <p style="max-width:680px; font-size:17px; line-height:1.8; color:#71717a; margin-top:8px;">Artikel program, kegiatan, dakwah, opini, dan pengumuman terbaru dari website Bekasi Hebat.</p>

            <div style="display:flex; gap:8px; margin-top:16px; flex-wrap:wrap;">
                <button
                    wire:click="$set('filterKategori', '')"
                    type="button"
                    style="{{ $filterKategori === ''
                        ? 'background:#ea580c; color:white; border:1px solid #ea580c;'
                        : 'background:white; color:#52525b; border:1px solid #e4e4e7;' }} border-radius:999px; padding:6px 16px; font-size:12px; font-weight:600; cursor:pointer; transition:.2s;"
                >
                    Semua
                </button>
                @foreach ($this->kategoriOptions as $key => $label)
                    <button
                        wire:click="$set('filterKategori', '{{ $key }}')"
                        type="button"
                        style="{{ $filterKategori === $key
                            ? 'background:#ea580c; color:white; border:1px solid #ea580c;'
                            : 'background:white; color:#52525b; border:1px solid #e4e4e7;' }} border-radius:999px; padding:6px 16px; font-size:12px; font-weight:600; cursor:pointer; transition:.2s;"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @if ($this->beritaFeatured)
            @php($featuredImageUrl = $this->beritaFeatured->thumbnail ? '/storage/' . ltrim($this->beritaFeatured->thumbnail, '/') : null)
            <div class="fade-up" style="display:grid;grid-template-columns:7fr 5fr;gap:20px;margin-bottom:28px;">
                <div style="overflow:hidden;border-radius:14px;background:#fff;border:1px solid #ececea;box-shadow:0 2px 12px rgba(0,0,0,.08); display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <a href="{{ route('public.berita.show', $this->beritaFeatured->slug) }}" wire:navigate style="display:block; position:relative;height:260px;display:flex;align-items:flex-end;padding:20px;background:linear-gradient(135deg,#1a3a6b,#2155a0);overflow:hidden; text-decoration:none;">
                            @if ($featuredImageUrl)
                                <img
                                    src="{{ $featuredImageUrl }}"
                                    alt="{{ $this->beritaFeatured->judul }}"
                                    style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;"
                                    onerror="this.style.display='none';">
                            @endif
                            <div style="position:absolute;inset:0;background:linear-gradient(transparent 40%,rgba(0,0,0,.72));"></div>
                            <h3 style="position:relative;z-index:2;margin:0;font-size:20px;font-weight:700;line-height:1.3;color:#fff; transition:.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#fff'">{{ $this->beritaFeatured->judul }}</h3>
                        </a>
                        <div style="padding:16px;">
                            <p style="margin:0;max-width:680px;font-size:16px;line-height:1.8;color:#71717a;">{{ $this->beritaFeatured->ringkasan }}</p>
                        </div>
                    </div>
                    <div style="padding:0 16px 16px 16px; display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-size:11px;font-weight:600;color:#fe5000;">
                            <i class="ti ti-calendar"></i>
                            {{ $this->beritaFeatured->published_at?->translatedFormat('d F Y') }} · {{ number_format($this->beritaFeatured->views) }} views
                        </div>
                        <a href="{{ route('public.berita.show', $this->beritaFeatured->slug) }}" wire:navigate style="font-size:13px; font-weight:700; color:#ea580c; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#ea580c'">
                            Selengkapnya <i class="ti ti-arrow-right" style="font-size:14px;"></i>
                        </a>
                    </div>
                </div>
                <div style="display:grid;gap:16px;">
                    @foreach ($this->beritaList->take(4) as $item)
                        <a href="{{ route('public.berita.show', $item->slug) }}" wire:navigate style="text-decoration:none; display:block;">
                            <div style="border:1px solid #ececea;background:#fff;border-radius:24px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.08); transition:0.2s; height:100%; display:flex; flex-direction:column; justify-content:space-between;" onmouseover="this.style.borderColor='#ea580c'" onmouseout="this.style.borderColor='#ececea'">
                                <div>
                                    <div style="font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#9a9890;">{{ $item->published_at?->translatedFormat('d M Y') }}</div>
                                    <div style="margin-top:8px;font-size:16px;font-weight:600;color:#1a1a18;line-height:1.4; transition:0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#1a1a18'">{{ $item->judul }}</div>
                                    <p style="margin:8px 0 0;font-size:14px;line-height:1.8;color:#71717a;">{{ Str::limit($item->ringkasan, 100) }}</p>
                                </div>
                                <div style="margin-top:12px; font-size:13px; font-weight:700; color:#ea580c; display:inline-flex; align-items:center; gap:4px;">
                                    Baca Selengkapnya <i class="ti ti-arrow-right" style="font-size:14px;"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($this->beritaList->count() > 0)
            <div class="fade-up berita-grid-public" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
                @foreach ($this->beritaList as $item)
                    @php($thumbUrl = $item->thumbnail ? '/storage/' . ltrim($item->thumbnail, '/') : null)
                    <article style="overflow:hidden;border-radius:14px;border:1px solid #ececea;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.08); display:flex; flex-direction:column; justify-content:space-between; height:100%;">
                        <div>
                            <a href="{{ route('public.berita.show', $item->slug) }}" wire:navigate style="display:block; text-decoration:none;">
                                @if($thumbUrl)
                                    <img
                                        src="{{ $thumbUrl }}"
                                        alt="{{ $item->judul }}"
                                        style="width:100%; height:180px; object-fit:cover;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none;height:180px;background:linear-gradient(135deg,#e8f0fb,#ececea);align-items:center;justify-content:center;color:#b5b5b0;font-size:42px;">
                                        <i class="ti ti-newspaper" style="font-size:24px; color:#d4d4d8;"></i>
                                    </div>
                                @else
                                    <div style="height:180px;background:linear-gradient(135deg,#e8f0fb,#ececea);display:flex;align-items:center;justify-content:center;color:#b5b5b0;font-size:42px;">
                                        <i class="ti ti-newspaper" style="font-size:24px; color:#d4d4d8;"></i>
                                    </div>
                                @endif
                            </a>
                            <div style="padding:18px 18px 0 18px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                    <span style="display:inline-flex;padding:4px 12px;border-radius:999px;background:#fff3ee;color:#fe5000;font-size:11px;font-weight:700;">{{ \App\Models\Berita::KATEGORI_OPTIONS[$item->kategori] ?? ucfirst($item->kategori) }}</span>
                                    <span style="font-size:12px;color:#9a9890;">{{ number_format($item->views) }} views</span>
                                </div>
                                <a href="{{ route('public.berita.show', $item->slug) }}" wire:navigate style="text-decoration:none;">
                                    <h3 style="margin:12px 0 0;font-size:16px;font-weight:600;line-height:1.35;color:#1a1a18; transition:0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#1a1a18'">{{ $item->judul }}</h3>
                                </a>
                                <p style="margin:10px 0 0;font-size:14px;line-height:1.8;color:#71717a;">{{ Str::limit($item->ringkasan, 100) }}</p>
                            </div>
                        </div>
                        <div style="padding:0 18px 18px 18px; margin-top:16px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f4f5f7; padding-top:12px;">
                            <span style="font-size:12px;color:#9a9890;">{{ $item->published_at?->translatedFormat('d F Y') }}</span>
                            <a href="{{ route('public.berita.show', $item->slug) }}" wire:navigate style="font-size:13px; font-weight:700; color:#ea580c; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:0.2s;" onmouseover="this.style.color='#fe5000'" onmouseout="this.style.color='#ea580c'">
                                Baca <i class="ti ti-arrow-right" style="font-size:14px;"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div style="margin-top:24px;">
                {{ $this->beritaList->links() }}
            </div>
        @else
            <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Belum ada artikel untuk kategori ini.</div>
        @endif
    </div>
</div>

<style>
    @media (max-width: 1100px) {
        .berita-grid-public {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .berita-grid-public {
            grid-template-columns: 1fr !important;
        }
    }
</style>
