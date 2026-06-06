@php
    $jenisColors = [
        'baksos' => ['bg' => '#fff7ed', 'text' => '#9a3412', 'icon' => 'ti-heart-handshake'],
        'pengajian' => ['bg' => '#faf5ff', 'text' => '#6b21a8', 'icon' => 'ti-book'],
        'senam' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'icon' => 'ti-stretching'],
        'diskusi' => ['bg' => '#f0f9ff', 'text' => '#0369a1', 'icon' => 'ti-message-circle'],
        'pelatihan' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'icon' => 'ti-school'],
        'musyawarah' => ['bg' => '#fffbeb', 'text' => '#92400e', 'icon' => 'ti-users-group'],
        'bedah_rumah' => ['bg' => '#fff1f2', 'text' => '#9f1239', 'icon' => 'ti-home-edit'],
        'kesehatan' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'icon' => 'ti-heartbeat'],
        'pendidikan' => ['bg' => '#ecfeff', 'text' => '#155e75', 'icon' => 'ti-school'],
        'lainnya' => ['bg' => '#f4f4f5', 'text' => '#52525b', 'icon' => 'ti-calendar-event'],
    ];
    $c = $jenisColors[$event->jenis] ?? $jenisColors['lainnya'];
    $shareUrl = route('public.events.show', $event->slug);
    $regCount = $event->registrations_count ?? 0;
    $regTarget = $event->peserta_target ?: 100;
    $regPct = min(100, round($regCount / max($regTarget, 1) * 100));
@endphp

<style>
    .ed-grid { display:grid; grid-template-columns:minmax(0,7fr) minmax(280px,5fr); gap:16px; align-items:start; }
    .ed-sticky { position:sticky; top:80px; }
    @media(max-width:768px) {
        .ed-grid { grid-template-columns:1fr; }
        .ed-sticky { position:static; }
    }
</style>

<section style="background:#fafafa; min-height:100vh; padding-bottom:40px;">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" style="padding-top:24px;">

        <div style="display:flex; align-items:center; gap:6px; font-size:12px; color:#a1a1aa; margin-bottom:16px;">
            <a href="{{ route('public.events') }}" wire:navigate style="color:#ea580c; text-decoration:none;">Event</a>
            <i class="ti ti-chevron-right" style="font-size:11px;"></i>
            <span>{{ \Illuminate\Support\Str::limit($event->judul, 50) }}</span>
        </div>

        <div class="ed-grid">

            <div>
                <div style="background:white; border-radius:12px; border:1px solid #e4e4e7; overflow:hidden;">
                    @if($event->cover_image)
                        <div style="height:260px; background:#f4f4f5;">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    @endif

                    <div style="display:flex; align-items:center; gap:16px; padding:20px; border-bottom:1px solid #f4f4f5;">
                        <div style="width:60px; height:64px; border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; background:{{ $c['bg'] }}; flex-shrink:0;">
                            <div style="font-size:24px; font-weight:600; line-height:1; color:{{ $c['text'] }};">{{ $event->tanggal_mulai?->format('d') }}</div>
                            <div style="font-size:10px; text-transform:uppercase; letter-spacing:.5px; margin-top:2px; color:{{ $c['text'] }};">{{ $event->tanggal_mulai?->translatedFormat('M Y') }}</div>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <span style="display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:500; background:{{ $c['bg'] }}; color:{{ $c['text'] }};">
                                <i class="ti {{ $c['icon'] }}" style="font-size:11px;"></i>
                                {{ $event->jenis_label }}
                            </span>
                            <h1 style="font-size:28px; font-weight:700; color:#18181b; margin-top:4px; line-height:1.3;">{{ $event->judul }}</h1>
                        </div>
                    </div>

                    <div style="display:flex; flex-wrap:wrap; gap:16px; padding:14px 20px; background:#fafafa; font-size:14px; color:#71717a;">
                        <span style="display:flex; align-items:center; gap:4px;">
                            <i class="ti ti-calendar" style="font-size:14px; color:#ea580c;"></i>
                            {{ $event->tanggal_mulai?->translatedFormat('l, d F Y') }}
                        </span>
                        <span style="display:flex; align-items:center; gap:4px;">
                            <i class="ti ti-clock" style="font-size:14px; color:#ea580c;"></i>
                            {{ $event->tanggal_mulai?->translatedFormat('H:i') }} WIB
                        </span>
                        @if($event->lokasi)
                            <span style="display:flex; align-items:center; gap:4px;">
                                <i class="ti ti-map-pin" style="font-size:14px; color:#ea580c;"></i>
                                {{ $event->lokasi }}
                            </span>
                        @endif
                    </div>

                    <div style="padding:20px;">
                        <div style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#ea580c; margin-bottom:8px;">Tentang event</div>
                        <div style="max-width:680px; font-size:17px; color:#3f3f46; line-height:1.8;">
                            {!! nl2br(e((string) $event->deskripsi)) !!}
                        </div>
                    </div>

                    <div style="padding:0 20px 20px;">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div style="background:#fafafa; border-radius:8px; padding:12px;">
                                <div style="font-size:11px; color:#a1a1aa; margin-bottom:2px;">Penyelenggara</div>
                                <div style="font-size:13px; font-weight:500; color:#18181b;">{{ $event->penyelenggara ?: 'DPD PKS Kabupaten Bekasi' }}</div>
                            </div>
                            <div style="background:#fafafa; border-radius:8px; padding:12px;">
                                <div style="font-size:11px; color:#a1a1aa; margin-bottom:2px;">Kapasitas</div>
                                <div style="font-size:13px; font-weight:500; color:#18181b;">{{ $regTarget }} peserta</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                    <a href="https://wa.me/?text={{ urlencode('Yuk ikut event: ' . $event->judul . ' - ' . $shareUrl) }}" target="_blank" rel="noopener"
                       style="display:inline-flex; align-items:center; gap:4px; padding:6px 14px; background:#dcfce7; color:#15803d; border-radius:8px; font-size:12px; font-weight:500; text-decoration:none;">
                        <i class="ti ti-brand-whatsapp" style="font-size:14px;"></i> Share via WA
                    </a>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $shareUrl }}'); this.textContent='✓ Copied!'; setTimeout(()=>this.innerHTML='<i class=\'ti ti-copy\' style=\'font-size:14px;\'></i> Copy link', 1500);"
                       style="display:inline-flex; align-items:center; gap:4px; padding:6px 14px; border:1px solid #e4e4e7; background:white; border-radius:8px; font-size:12px; font-weight:500; color:#71717a; cursor:pointer;">
                        <i class="ti ti-copy" style="font-size:14px;"></i> Copy link
                    </button>
                    <a href="{{ route('public.events') }}" wire:navigate style="display:inline-flex; align-items:center; gap:4px; padding:6px 14px; border:1px solid #e4e4e7; background:white; border-radius:8px; font-size:12px; font-weight:500; color:#71717a; text-decoration:none; margin-left:auto;">
                        <i class="ti ti-arrow-left" style="font-size:14px;"></i> Semua event
                    </a>
                </div>
            </div>

            <aside class="ed-sticky">
                <div style="background:white; border-radius:12px; border:1px solid #e4e4e7; overflow:hidden;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #f4f4f5;">
                        <div>
                            <div style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#ea580c;">Registrasi</div>
                            <div style="font-size:15px; font-weight:600; color:#18181b; margin-top:2px;">Daftar event ini</div>
                        </div>
                        <div style="text-align:center; background:#fff7ed; border-radius:8px; padding:6px 10px;">
                            <div style="font-size:18px; font-weight:600; color:#ea580c;">{{ number_format($regCount) }}</div>
                            <div style="font-size:8px; text-transform:uppercase; letter-spacing:.5px; color:#ea580c;">pendaftar</div>
                        </div>
                    </div>

                    <div style="padding:0 16px; margin-top:10px;">
                        <div style="display:flex; justify-content:space-between; font-size:10px; color:#a1a1aa; margin-bottom:3px;">
                            <span>{{ $regCount }} terdaftar</span>
                            <span>{{ $regTarget }} target</span>
                        </div>
                        <div style="height:5px; border-radius:3px; background:#f4f4f5; overflow:hidden;">
                            <div style="height:100%; border-radius:3px; background:#ea580c; width:{{ $regPct }}%;"></div>
                        </div>
                    </div>

                    @if ($registered)
                        <div style="padding:20px 16px; text-align:center;">
                            <div style="width:48px; height:48px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                <i class="ti ti-check" style="font-size:22px; color:#16a34a;"></i>
                            </div>
                            <div style="font-size:15px; font-weight:600; color:#15803d; margin-top:10px;">Anda sudah terdaftar!</div>
                            <p style="font-size:12px; color:#4ade80; margin-top:4px; line-height:1.5;">Data pendaftaran sudah masuk. Sampai jumpa di event!</p>
                        </div>
                    @else
                        <form wire:submit="register" style="padding:12px 16px 16px;">
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <div>
                                    <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">Nama lengkap *</label>
                                    <input wire:model.defer="regNama" type="text" placeholder="Nama anda"
                                        style="width:100%; padding:8px 12px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                    @error('regNama') <div style="font-size:10px; color:#dc2626; margin-top:2px;">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">No HP / WhatsApp *</label>
                                    <input wire:model.defer="regHp" type="text" placeholder="08xxxxxxxxxx"
                                        style="width:100%; padding:8px 12px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                    @error('regHp') <div style="font-size:10px; color:#dc2626; margin-top:2px;">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">Email <span style="color:#a1a1aa;">(opsional)</span></label>
                                    <input wire:model.defer="regEmail" type="email" placeholder="email@contoh.com"
                                        style="width:100%; padding:8px 12px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                                    <div>
                                        <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">Dapil</label>
                                        <input wire:model.defer="regDapil" type="text" placeholder="Dapil"
                                            style="width:100%; padding:8px 10px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                    </div>
                                    <div>
                                        <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">RW</label>
                                        <input wire:model.defer="regRw" type="text" placeholder="RW"
                                            style="width:100%; padding:8px 10px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size:13px; font-weight:500; color:#52525b; display:block; margin-bottom:3px;">Desa / Kelurahan</label>
                                    <input wire:model.defer="regDesa" type="text" list="desa-options" placeholder="Pilih atau ketik desa"
                                        style="width:100%; padding:8px 12px; border:1px solid #e4e4e7; border-radius:8px; font-size:14px; outline:none; color:#18181b; background:white;">
                                    <datalist id="desa-options">
                                        @foreach ($this->desaOptions as $desa)
                                            <option value="{{ $desa->desa }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <button type="submit"
                                style="width:100%; margin-top:12px; padding:10px; background:#ea580c; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;">
                                <i class="ti ti-send" style="font-size:14px;"></i> Daftar sekarang
                            </button>

                            <p style="font-size:10px; color:#a1a1aa; text-align:center; margin-top:8px; line-height:1.4;">
                                Dengan mendaftar, data anda akan masuk ke sistem Bekasi Hebat. Gratis, tanpa biaya.
                            </p>
                        </form>
                    @endif
                </div>
            </aside>

        </div>
    </div>
</section>
