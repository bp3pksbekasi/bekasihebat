@php
    $stories = $this->successStories;
@endphp

<div>
    <section class="section section-white">
        <div class="container">
            @if (session('message'))
                <div style="margin-bottom:18px;padding:12px 14px;border-radius:12px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:13px;">
                    {{ session('message') }}
                </div>
            @endif

            <div class="section-header center">
                <span class="section-tag blue">Aspirasi Warga</span>
                <h1 class="section-title">Aspirasi yang diperjuangkan untuk Kabupaten Bekasi</h1>
                <p class="section-subtitle center">Warga dapat menyampaikan usulan kebutuhan wilayah. Aspirasi yang sudah terealisasi kami tampilkan sebagai bentuk transparansi tindak lanjut.</p>
            </div>

            <div style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(320px,.8fr);gap:20px;" class="aspirasi-public-hero-grid">
                <div style="padding:26px;border-radius:18px;background:linear-gradient(135deg,#1a3a6b,#0f2748);color:white;">
                    <div style="font-size:13px;color:rgba(255,255,255,.75);">Total Aspirasi</div>
                    <div style="font-size:42px;font-weight:800;line-height:1.1;margin-top:6px;">{{ number_format($this->totalAspirasi) }}</div>
                    <div style="font-size:15px;color:rgba(255,255,255,.82);margin-top:10px;">Aspirasi telah diperjuangkan oleh Dewan PKS Kabupaten Bekasi</div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:18px;">
                        <div style="padding:14px;border-radius:14px;background:rgba(255,255,255,.08);">
                            <div style="font-size:11px;color:rgba(255,255,255,.72);">Masuk SIPD</div>
                            <div style="font-size:26px;font-weight:800;margin-top:4px;">{{ number_format($this->inputSipd) }}</div>
                        </div>
                        <div style="padding:14px;border-radius:14px;background:rgba(255,255,255,.08);">
                            <div style="font-size:11px;color:rgba(255,255,255,.72);">Dianggarkan</div>
                            <div style="font-size:26px;font-weight:800;margin-top:4px;">{{ number_format($this->dianggarkan) }}</div>
                        </div>
                        <div style="padding:14px;border-radius:14px;background:rgba(255,255,255,.08);">
                            <div style="font-size:11px;color:rgba(255,255,255,.72);">Terealisasi</div>
                            <div style="font-size:26px;font-weight:800;margin-top:4px;">{{ number_format($this->terealisasi) }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:22px;border-radius:18px;border:1px solid #ececea;background:#fff;">
                    <div style="font-size:12px;font-weight:700;color:#1a3a6b;text-transform:uppercase;">Breakdown Kategori</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
                        @foreach ($this->perKategori as $item)
                            <span style="padding:8px 12px;border-radius:999px;background:#f5f5f5;color:#3d3d3a;font-size:12px;font-weight:600;">
                                {{ $item['label'] }} · {{ number_format($item['count']) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="margin-top:28px;display:grid;grid-template-columns:minmax(0,1fr) minmax(360px,.95fr);gap:20px;" class="aspirasi-public-content-grid">
                <div>
                    <div class="section-header">
                        <span class="section-tag orange">Success Stories</span>
                        <h2 class="section-title" style="font-size:24px;">Aspirasi yang sudah terealisasi</h2>
                    </div>
                    <div style="display:grid;gap:12px;">
                        @forelse ($stories as $story)
                            <article style="padding:18px;border-radius:16px;border:1px solid #ececea;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                    <div>
                                        <h3 style="margin:0;font-size:18px;font-weight:700;color:#1a1a18;">{{ $story->judul }}</h3>
                                        <div style="font-size:12px;color:#9a9890;margin-top:6px;">{{ $story->desa ?: '-' }}, {{ $story->kecamatan ?: '-' }}</div>
                                    </div>
                                    <div style="padding:6px 10px;border-radius:999px;background:#ecfdf3;color:#166534;font-size:11px;font-weight:700;">Terealisasi</div>
                                </div>
                                <div style="font-size:13px;color:#3d3d3a;line-height:1.7;margin-top:10px;">{{ \Illuminate\Support\Str::limit($story->deskripsi, 160) }}</div>
                                <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:12px;font-size:12px;color:#5f5f5c;">
                                    <span><i class="ti ti-user-star"></i> {{ $story->assignedDewan?->nama ?? 'Dewan PKS Kabupaten Bekasi' }}</span>
                                    <span><i class="ti ti-coin"></i> {{ $story->anggaran_nominal ? 'Rp '.number_format((float) $story->anggaran_nominal, 0, ',', '.') : 'Nominal belum dicatat' }}</span>
                                </div>
                            </article>
                        @empty
                            <div style="padding:24px;border-radius:16px;border:1px dashed #dddddb;background:#fff;text-align:center;font-size:13px;color:#9a9890;">
                                Belum ada success story aspirasi yang ditampilkan saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="section-header">
                        <span class="section-tag blue">Sampaikan Aspirasi</span>
                        <h2 class="section-title" style="font-size:24px;">Kirim aspirasi Anda</h2>
                        <p class="section-subtitle">Isi form berikut agar tim kami dapat menindaklanjuti kebutuhan wilayah Anda.</p>
                    </div>

                    <div style="padding:20px;border-radius:18px;border:1px solid #ececea;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.06);display:grid;gap:12px;">
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <input wire:model="publicNama" type="text" placeholder="Nama lengkap" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                            <input wire:model="publicHp" type="text" placeholder="No HP / WhatsApp" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <select wire:model.live="publicDapil" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                                <option value="">Pilih dapil</option>
                                @foreach ($this->dapilOptions as $dapil)
                                    <option value="{{ $dapil }}">{{ $dapil }}</option>
                                @endforeach
                            </select>
                            <select wire:model="publicDesaId" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                                <option value="">Pilih desa</option>
                                @foreach ($this->desaOptions as $desa)
                                    <option value="{{ $desa->id }}">{{ $desa->desa }} - {{ $desa->kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:grid;grid-template-columns:100px minmax(0,1fr);gap:10px;">
                            <input wire:model="publicRw" type="text" placeholder="RW" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                            <select wire:model="publicKategori" style="height:44px;border-radius:10px;border:1px solid #dddddb;padding:0 12px;background:#fff;">
                                @foreach (\App\Models\Aspirasi::KATEGORI_OPTIONS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <textarea wire:model="publicDeskripsi" rows="6" placeholder="Ceritakan kebutuhan atau permasalahan yang ingin disampaikan..." style="width:100%;border-radius:10px;border:1px solid #dddddb;padding:12px;background:#fff;resize:vertical;"></textarea>
                        <button wire:click="submitAspirasi" type="button" class="nav-cta" style="justify-content:center;height:46px;border:none;cursor:pointer;">
                            Sampaikan Aspirasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @media (max-width: 900px) {
            .aspirasi-public-hero-grid,
            .aspirasi-public-content-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>
