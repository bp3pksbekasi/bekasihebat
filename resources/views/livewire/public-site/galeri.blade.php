<div class="section">
    <div class="container">
        <div style="background:white; border-radius:16px; padding:24px; margin-top:24px; border:1px solid #f4f4f5;">
            <h1 style="font-size:30px; font-weight:700; color:#18181b; line-height:1.2;">Galeri Bekasi Hebat</h1>
            <p style="max-width:680px; font-size:17px; line-height:1.8; color:#71717a; margin-top:8px;">Dokumentasi kegiatan publik, event, baksos, senam, RKI, dan dakwah yang sudah dipublikasikan.</p>

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

        @if ($this->galeriList->count() > 0)
            <div class="fade-up galeri-grid-public" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;">
                @foreach ($this->galeriList as $index => $item)
                    @php($isFeaturedTile = $index === 0)
                    @php($imagePath = $item->file_path ?: $item->thumbnail)
                    @php($imageUrl = $imagePath ? '/storage/' . ltrim($imagePath, '/') : null)
                    <div 
                        class="{{ $isFeaturedTile ? 'galeri-featured-public' : '' }} galeri-card-interactive" 
                        wire:click="selectItem('{{ $item->id }}')"
                        style="position:relative;overflow:hidden;border-radius:24px;background:#f4f4f5;border:1px solid #ececea;box-shadow:0 2px 12px rgba(0,0,0,.08);{{ $isFeaturedTile ? 'grid-column:span 2;grid-row:span 2;min-height:378px;' : 'min-height:180px;' }} cursor:pointer; transition:0.2s;"
                    >
                        @if ($imageUrl)
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $item->judul }}"
                                style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;background:linear-gradient(135deg,#ececea,#f7f7f5);"><i class="ti ti-photo" style="font-size:32px; color:#d4d4d8;"></i></div>
                        @else
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#ececea,#f7f7f5);"><i class="ti ti-photo" style="font-size:32px; color:#d4d4d8;"></i></div>
                        @endif
                        <div class="galeri-overlay-public" style="position:absolute;inset:auto 0 0 0;display:flex;flex-direction:column;gap:6px;padding:16px;text-align:center;background:linear-gradient(transparent,rgba(0,0,0,.75));color:#fff; transition:0.2s;">
                            <strong style="font-size:16px;font-weight:600;line-height:1.4;">{{ $item->judul }}</strong>
                            <span style="font-size:11px;color:rgba(255,255,255,.8);">{{ $item->lokasi ?: 'Kabupaten Bekasi' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:24px;">
                {{ $this->galeriList->links() }}
            </div>
        @else
            <div class="event-card-public fade-up" style="padding:32px;text-align:center;">Belum ada dokumentasi untuk kategori ini.</div>
        @endif

        <!-- Lightbox Modal -->
        @if ($this->selectedItem)
            @php($modalImagePath = $this->selectedItem->file_path ?: $this->selectedItem->thumbnail)
            @php($modalImageUrl = $modalImagePath ? '/storage/' . ltrim($modalImagePath, '/') : null)
            <div 
                class="lightbox-overlay" 
                style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; transition: 0.3s;"
                wire:click.self="closeLightbox"
            >
                <div class="lightbox-card" style="position: relative; background: white; border-radius: 24px; width: 100%; max-width: 1000px; display: grid; grid-template-columns: 3fr 2fr; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); max-height: 85vh; border: 1px solid rgba(255, 255, 255, 0.1);">
                    
                    <!-- Close button -->
                    <button 
                        type="button" 
                        wire:click="closeLightbox" 
                        style="position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%; background: rgba(0, 0, 0, 0.5); color: white; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.2s; z-index: 10;"
                        onmouseover="this.style.background='rgba(234, 88, 12, 0.8)'"
                        onmouseout="this.style.background='rgba(0, 0, 0, 0.5)'"
                    >
                        <i class="ti ti-x" style="font-size: 20px;"></i>
                    </button>

                    <!-- Left Column: Large Image & Nav -->
                    <div style="position: relative; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 350px; height: 100%; overflow: hidden;">
                        @if ($modalImageUrl)
                            <img 
                                src="{{ $modalImageUrl }}" 
                                alt="{{ $this->selectedItem->judul }}" 
                                style="max-width: 100%; max-height: 85vh; object-fit: contain; display: block;"
                            >
                        @endif

                        <!-- Navigation controls overlay -->
                        <button 
                            type="button" 
                            wire:click="prevItem" 
                            style="position: absolute; left: 16px; width: 44px; height: 44px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.2s; backdrop-filter: blur(4px);"
                            onmouseover="this.style.background='rgba(234, 88, 12, 0.8)'"
                            onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'"
                        >
                            <i class="ti ti-chevron-left" style="font-size: 24px;"></i>
                        </button>
                        <button 
                            type="button" 
                            wire:click="nextItem" 
                            style="position: absolute; right: 16px; width: 44px; height: 44px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.2s; backdrop-filter: blur(4px);"
                            onmouseover="this.style.background='rgba(234, 88, 12, 0.8)'"
                            onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'"
                        >
                            <i class="ti ti-chevron-right" style="font-size: 24px;"></i>
                        </button>
                    </div>

                    <!-- Right Column: Details -->
                    <div style="padding: 32px; display: flex; flex-direction: column; justify-content: space-between; overflow-y: auto; height: 100%; background: #ffffff;">
                        <div>
                            <!-- Category Badge -->
                            <span style="display: inline-flex; padding: 4px 12px; border-radius: 999px; background: #fff3ee; color: #fe5000; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">
                                {{ \App\Models\Galeri::KATEGORI_OPTIONS[$this->selectedItem->kategori] ?? ucfirst($this->selectedItem->kategori) }}
                            </span>

                            <!-- Title -->
                            <h2 style="font-size: 20px; font-weight: 800; color: #18181b; line-height: 1.3; margin: 0 0 12px 0;">
                                {{ $this->selectedItem->judul }}
                            </h2>

                            <!-- Metadata -->
                            <div style="display: grid; gap: 8px; font-size: 13px; color: #71717a; margin-bottom: 20px; border-bottom: 1px solid #f4f4f5; padding-bottom: 16px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="ti ti-map-pin" style="color: #ea580c; font-size: 16px;"></i>
                                    <span>Lokasi: <strong>{{ $this->selectedItem->lokasi ?: 'Kabupaten Bekasi' }}</strong></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="ti ti-calendar" style="font-size: 16px;"></i>
                                    <span>Tanggal: <strong>{{ $this->selectedItem->tanggal?->translatedFormat('d F Y') }}</strong></span>
                                </div>
                            </div>

                            <!-- Description -->
                            <p style="font-size: 14px; line-height: 1.8; color: #52525b; margin: 0 0 20px 0;">
                                {{ $this->selectedItem->deskripsi }}
                            </p>
                        </div>

                        <!-- Action Link to event if available -->
                        @if ($this->selectedItem->event)
                            <div style="border-top: 1px solid #f4f4f5; padding-top: 20px;">
                                <a 
                                    href="{{ route('public.events.show', $this->selectedItem->event->slug) }}" 
                                    wire:navigate
                                    class="btn" 
                                    style="display: block; text-align: center; background: #fe5000; color: white; padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s;"
                                    onmouseover="this.style.background='#ea580c'"
                                    onmouseout="this.style.background='#fe5000'"
                                >
                                    <i class="ti ti-calendar-event"></i> Lihat Detail Kegiatan
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .galeri-card-interactive:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(234, 88, 12, 0.15) !important;
        border-color: #ea580c !important;
    }
    
    .galeri-card-interactive:hover .galeri-overlay-public {
        background: linear-gradient(transparent, rgba(234, 88, 12, 0.85)) !important;
    }

    @media (max-width: 1100px) {
        .galeri-grid-public {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 768px) {
        .galeri-grid-public {
            grid-template-columns: 1fr !important;
        }

        .galeri-featured-public {
            grid-column: span 1 !important;
            grid-row: span 1 !important;
            min-height: 220px !important;
        }

        .lightbox-card {
            grid-template-columns: 1fr !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
        }
    }
</style>
