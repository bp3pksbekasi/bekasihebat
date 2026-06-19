@php
    $kpi = $this->kpi;
    $videoMateriTab = $activeTab === 'video_materi';
    $profilTab = $activeTab === 'profil';
    $kontenTab = $activeTab === 'konten';
    $drawerOpen = $showDewanForm || $showKontenForm || $showMateriForm || $showDistribusiForm;
    $videoSummary = $this->videoCoverageSummary;
    $selectedVillageDetail = $this->selectedVillageDetail;
@endphp

<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#7c3aed;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-brand-instagram" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div style="font-size:14px;font-weight:600;">Sosial Media & Dewan</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                <select wire:model.live="selectedDapil" style="padding:6px 28px 6px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;" @if ($this->accessScope['is_dapil'] ?? false) disabled @endif>
                    <option value="">Semua dapil</option>
                    @foreach ($this->dapilOptions as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedKecamatan" style="padding:6px 28px 6px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;" @if (($this->accessScope['kecamatan'] ?? '') !== '') disabled @endif>
                    <option value="">Semua kecamatan</option>
                    @foreach ($this->kecamatanOptions as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedDesa" style="padding:6px 28px 6px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;" @if (auth()->user()?->isDpra()) disabled @endif>
                    <option value="">Semua desa</option>
                    @foreach ($this->desaOptions as $desaOption)
                        <option value="{{ $desaOption }}">{{ $desaOption }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedDewanId" style="padding:6px 28px 6px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:220px;">
                    <option value="">Semua dewan</option>
                    @foreach ($this->dewanOptions as $dewanOption)
                        <option value="{{ $dewanOption->id }}">{{ $dewanOption->nama }}{{ $dewanOption->dapil ? ' · '.$dewanOption->dapil : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="width:26px;height:26px;background:#7c3aed;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">SM</div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
        @if (session()->has('message'))
            <div style="margin:14px 20px 0;padding:10px 12px;border-radius:10px;background:#faf5ff;color:#6d28d9;font-size:12px;border:0.5px solid #ddd6fe;">
                {{ session('message') }}
            </div>
        @endif

        <div style="padding:18px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Sosial Media & Dewan</h1>
                <div style="font-size:12px;color:#666;">Monitoring performa profil dewan, log konten, video pelayanan, dan distribusi materi.</div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                <select wire:model.live="selectedBulan" style="padding:6px 28px 6px 10px;border:0.5px solid #c4b5fd;border-radius:8px;font-size:12px;background:#faf5ff;color:#6d28d9;font-weight:600;">
                    @foreach ($this->bulanOptions as $bulan => $label)
                        <option value="{{ $bulan }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedTahun" style="padding:6px 28px 6px 10px;border:0.5px solid #d4d4d8;border-radius:8px;font-size:12px;background:white;color:#111827;font-weight:600;">
                    @foreach ($this->tahunOptions as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
                <button type="button" wire:click="recalculatePopularitas" style="padding:8px 12px;border:0.5px solid #c4b5fd;border-radius:8px;background:#faf5ff;color:#6d28d9;font-size:12px;font-weight:600;cursor:pointer;">
                    Hitung ulang skor
                </button>
            </div>
        </div>

        <div style="padding:14px 20px 0;">
            <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:#f4f4f5;border:0.5px solid #e4e4e7;flex-wrap:wrap;">
                <button type="button" wire:click="setActiveTab('video_materi')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $videoMateriTab ? '#ede9fe' : 'transparent' }};color:{{ $videoMateriTab ? '#6d28d9' : '#71717a' }};">Video Pelayanan & Materi</button>
                <button type="button" wire:click="setActiveTab('profil')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $profilTab ? '#ede9fe' : 'transparent' }};color:{{ $profilTab ? '#6d28d9' : '#71717a' }};">Profil Dewan</button>
                <button type="button" wire:click="setActiveTab('konten')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $kontenTab ? '#ede9fe' : 'transparent' }};color:{{ $kontenTab ? '#6d28d9' : '#71717a' }};">Log Konten</button>
            </div>
        </div>

        @if ($profilTab)
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;padding:18px 20px;" class="sosmed-kpi-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Total Dewan Aktif</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['total_dewan_aktif']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:12px;padding:14px;color:white;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Avg Popularitas</div>
                    <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($kpi['avg_popularitas'], 1) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Konten Bulan Ini</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['konten_bulan_ini']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Video Pelayanan</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['video_pelayanan_bulan_ini']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Materi Didistribusi</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['materi_didistribusi_bulan_ini']) }}</div>
                </div>
            </div>

            <div style="padding:0 20px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Profil Anggota Dewan</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Grid 2 kolom untuk skor popularitas, akun platform, dan capaian konten bulan berjalan</div>
                    </div>
                    <button type="button" wire:click="openDewanForm" style="padding:7px 12px;border:none;border-radius:8px;background:#7c3aed;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                        + Tambah profil dewan
                    </button>
                </div>

                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;" class="sosmed-two-col-grid">
                    @forelse ($this->dewanList as $dewan)
                        @php
                            $score = (int) $dewan->skor_popularitas;
                            $isTargetMet = $score >= 70;
                            $isMedium = $score >= 30 && $score < 70;
                            $cardBorder = $isTargetMet ? '#22c55e' : ($isMedium ? '#f59e0b' : '#ef4444');
                            $cardBg = $isTargetMet ? '#f0fdf4' : ($isMedium ? '#fffbeb' : '#fef2f2');
                            $progressBg = $isTargetMet ? '#dcfce7' : ($isMedium ? '#fef3c7' : '#fee2e2');
                            $progressColor = $isTargetMet ? '#16a34a' : ($isMedium ? '#d97706' : '#dc2626');
                            $initials = collect(explode(' ', trim($dewan->nama)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($item) => strtoupper(substr($item, 0, 1)))
                                ->implode('');
                        @endphp
                        <div style="border:1px solid {{ $cardBorder }};border-radius:14px;padding:14px;background:{{ $cardBg }};">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div style="display:flex;gap:12px;min-width:0;">
                                    <div style="width:48px;height:48px;border-radius:14px;background:#7c3aed;color:white;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;flex-shrink:0;">
                                        {{ $initials !== '' ? $initials : 'AD' }}
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="font-size:14px;font-weight:700;color:#111827;">{{ $dewan->nama }}</div>
                                        <div style="font-size:11px;color:#71717a;margin-top:3px;">{{ $dewan->jabatan }}</div>
                                        <div style="font-size:11px;color:#71717a;margin-top:4px;line-height:1.5;">
                                            @if($dewan->jabatan_dprd)
                                                <span style="font-weight:600;color:#ea580c;">{{ $dewan->jabatan_dprd }}</span> ·
                                            @endif
                                            @if($dewan->jabatan_fraksi)
                                                <span style="font-weight:600;color:#2563eb;">{{ $dewan->jabatan_fraksi }}</span> ·
                                            @endif
                                            {{ $dewan->dapil ?: 'Tanpa dapil' }}
                                            @if($dewan->wilayah_dapil)
                                                <span style="color:#6b7280;">({{ $dewan->wilayah_dapil }})</span>
                                            @endif
                                        </div>
                                        <div style="font-size:11px;color:#71717a;margin-top:2px;">
                                            Suara 2024: {{ number_format((int) $dewan->suara_2024) }}
                                            @if($dewan->status_petahana)
                                                · <span style="color:#16a34a;">Petahana</span>
                                            @else
                                                · <span style="color:#2563eb;">Baru</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:10px;color:#71717a;text-transform:uppercase;">Skor</div>
                                    <div style="font-size:30px;font-weight:800;color:{{ $progressColor }};line-height:1;">{{ number_format($score) }}</div>
                                </div>
                            </div>

                            <div style="height:8px;background:{{ $progressBg }};border-radius:999px;overflow:hidden;margin-top:12px;">
                                <div style="height:100%;width:{{ min($score, 100) }}%;background:{{ $progressColor }};"></div>
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                <div style="font-size:11px;color:#374151;">
                                    Target {{ number_format($dewan->target_popularitas) }}
                                </div>
                                @if ($isTargetMet)
                                    <div style="font-size:10px;color:#166534;font-weight:700;">Target tercapai</div>
                                @elseif ($isMedium)
                                    <div style="font-size:10px;color:#92400e;font-weight:700;">Perlu dorongan konten</div>
                                @else
                                    <div style="font-size:10px;color:#b91c1c;font-weight:700;">Warning: popularitas rendah</div>
                                @endif
                            </div>

                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
                                @forelse ($dewan->platforms as $platform)
                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:{{ $platform['color'] }}15;color:{{ $platform['color'] }};font-weight:600;">
                                        {{ strtoupper($platform['name'] === 'youtube' ? 'YT' : ($platform['name'] === 'instagram' ? 'IG' : ($platform['name'] === 'tiktok' ? 'TikTok' : ($platform['name'] === 'twitter' ? 'X' : 'FB')))) }}
                                        · {{ number_format((int) $platform['followers']) }}
                                    </span>
                                @empty
                                    <span style="font-size:10px;color:#9ca3af;">Belum ada akun media sosial</span>
                                @endforelse
                            </div>

                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px;">
                                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:10px;background:white;">
                                    <div style="font-size:10px;color:#888;text-transform:uppercase;">Konten bulan ini</div>
                                    <div style="font-size:22px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format((int) $dewan->konten_bulan_ini_count) }}</div>
                                </div>
                                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:10px;background:white;">
                                    <div style="font-size:10px;color:#888;text-transform:uppercase;">Video pelayanan</div>
                                    <div style="font-size:22px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format((int) $dewan->video_bulan_ini_count) }}</div>
                                </div>
                            </div>

                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                                <button type="button" wire:click="editDewan('{{ $dewan->id }}')" style="padding:6px 10px;border:0.5px solid #c4b5fd;border-radius:8px;background:white;color:#6d28d9;font-size:11px;font-weight:600;cursor:pointer;">
                                    Lihat detail
                                </button>
                                <button type="button" wire:click="openKontenForm('{{ $dewan->id }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#7c3aed;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                    Catat konten
                                </button>
                                <button type="button" wire:click="updateFollowers('{{ $dewan->id }}')" style="padding:6px 10px;border:0.5px solid #d4d4d8;border-radius:8px;background:white;color:#52525b;font-size:11px;font-weight:600;cursor:pointer;">
                                    Update followers
                                </button>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;padding:28px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                            Belum ada profil anggota dewan.
                        </div>
                    @endforelse
                </div>
            </div>
        @elseif ($kontenTab)
            <div style="padding:18px 20px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Log Konten</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Daftar konten terurut dari posting terbaru dengan badge platform, jenis, topik, dan engagement</div>
                    </div>
                    <button type="button" wire:click="openKontenForm" style="padding:7px 12px;border:none;border-radius:8px;background:#7c3aed;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                        + Catat konten baru
                    </button>
                </div>

                <div style="display:grid;gap:12px;">
                    @forelse ($this->kontenList as $konten)
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px;">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div style="width:54px;height:54px;border-radius:14px;background:{{ $konten->platform_config['color'] }}15;color:{{ $konten->platform_config['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-{{ $konten->platform_config['icon'] }}" style="font-size:24px;" aria-hidden="true"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                        <div>
                                            <div style="font-size:13px;font-weight:700;color:#111827;">{{ $konten->anggotaDewan?->nama ?: '-' }}</div>
                                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:5px;">
                                                <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:{{ $konten->platform_config['color'] }}15;color:{{ $konten->platform_config['color'] }};">
                                                    {{ $konten->platform_config['label'] }}
                                                </span>
                                                <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#ede9fe;color:#6d28d9;">
                                                    {{ \App\Models\KontenMedsos::JENIS_KONTEN_OPTIONS[$konten->jenis_konten] ?? $konten->jenis_konten }}
                                                </span>
                                                @if ($konten->topik)
                                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#f5f3ff;color:#7c3aed;">
                                                        {{ \App\Models\KontenMedsos::TOPIK_OPTIONS[$konten->topik] ?? ucfirst($konten->topik) }}
                                                    </span>
                                                @endif
                                                @if ($konten->is_video_pelayanan)
                                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-weight:700;">
                                                        Video Pelayanan{{ $konten->rw_terkait ? ' · RW '.$konten->rw_terkait : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="font-size:11px;color:#71717a;">{{ $konten->tanggal_posting?->format('d M Y') }}</div>
                                    </div>

                                    <div style="font-size:12px;color:#111827;margin-top:10px;line-height:1.55;">
                                        {{ $konten->caption ?: 'Konten tanpa caption.' }}
                                    </div>

                                    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:12px;" class="sosmed-engagement-grid">
                                        <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                            <div style="font-size:10px;color:#888;">Likes</div>
                                            <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $konten->likes) }}</div>
                                        </div>
                                        <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                            <div style="font-size:10px;color:#888;">Comments</div>
                                            <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $konten->comments) }}</div>
                                        </div>
                                        <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                            <div style="font-size:10px;color:#888;">Shares</div>
                                            <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $konten->shares) }}</div>
                                        </div>
                                        <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                            <div style="font-size:10px;color:#888;">Views</div>
                                            <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $konten->views) }}</div>
                                        </div>
                                    </div>

                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:12px;">
                                        <div style="font-size:11px;color:#71717a;">
                                            {{ $konten->dapil_terkait ?: ($konten->anggotaDewan?->dapil ?: '-') }}
                                            @if ($konten->desa_terkait)
                                                · {{ $konten->desa_terkait }}
                                            @endif
                                            @if ($konten->rw_terkait)
                                                · RW {{ $konten->rw_terkait }}
                                            @endif
                                        </div>
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                            @if ($konten->url)
                                                <a href="{{ $konten->url }}" target="_blank" rel="noreferrer" style="font-size:11px;color:#7c3aed;text-decoration:underline;">Link post</a>
                                            @endif
                                            <button type="button" wire:click="editKonten('{{ $konten->id }}')" style="font-size:11px;color:#7c3aed;text-decoration:underline;background:none;border:none;cursor:pointer;">
                                                Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding:28px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                            Belum ada log konten pada periode ini.
                        </div>
                    @endforelse
                </div>

                <div style="margin-top:12px;">
                    {{ $this->kontenList->links('livewire::simple-tailwind') }}
                </div>
            </div>
        @elseif ($videoMateriTab)
            <!-- KPI Summary Cards for Videos & Materi -->
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;padding:18px 20px 0;" class="sosmed-kpi-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Total RW</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($videoSummary['total_rw']) }}</div>
                </div>
                <div style="background:#f0fdf4;border:0.5px solid #bbf7d0;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#166534;text-transform:uppercase;letter-spacing:0.8px;">RW Sudah Video</div>
                    <div style="font-size:28px;font-weight:700;color:#166534;margin-top:6px;">{{ number_format($videoSummary['rw_sudah_video']) }}</div>
                </div>
                <div style="background:#fef2f2;border:0.5px solid #fecaca;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#b91c1c;text-transform:uppercase;letter-spacing:0.8px;">RW Belum Video</div>
                    <div style="font-size:28px;font-weight:700;color:#b91c1c;margin-top:6px;">{{ number_format($videoSummary['rw_belum_video']) }}</div>
                </div>
                <div style="background:#faf5ff;border:0.5px solid #ddd6fe;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#6d28d9;text-transform:uppercase;letter-spacing:0.8px;">Total Video</div>
                    <div style="font-size:28px;font-weight:700;color:#6d28d9;margin-top:6px;">{{ number_format($videoSummary['total_video']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Materi Digital</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($this->materiList->count()) }}</div>
                </div>
            </div>

            <!-- Row 1: 3-column layout (Map, Selected Village Detail, List of Villages) -->
            <div style="padding:20px 20px 10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Peta Sebaran & Coverage Video Pelayanan RW</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Analisis spasial sebaran video pelayanan PKS per RW di tingkat kelurahan/desa</div>
                    </div>
                </div>
            </div>

            <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
                <!-- Column 1: Map Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Peta Sebaran Video Pelayanan</div>
                    <div class="kaderisasi-map-wrapper" style="flex:1;min-height:0;background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ $this->mapImage }}" style="max-width:100%;max-height:100%;object-fit:contain;display:block;" alt="Peta Sebaran">
                        @foreach ($this->mapMarkers as $marker)
                            <button type="button" 
                                wire:click="selectDesa('{{ $marker['id'] }}')" 
                                title="{{ $marker['label'] }}"
                                style="position:absolute;left:{{ $marker['x'] }}%;top:{{ $marker['y'] }}%;transform:translate(-50%,-50%);width:{{ $marker['size'] }}px;height:{{ $marker['size'] }}px;border-radius:50%;border:2px solid white;background:{{ $marker['color'] }};box-shadow:0 3px 8px rgba(0,0,0,0.25);cursor:pointer;transition:transform 0.15s ease-in-out;z-index:10;"
                                onmouseover="this.style.transform='translate(-50%,-50%) scale(1.25)'"
                                onmouseout="this.style.transform='translate(-50%,-50%) scale(1)'">
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Column 2: Selected Village Detail & RW Heatmap -->
                @if ($selectedVillageDetail)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;">
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">{{ $selectedVillageDetail['desa'] }}</h2>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Kec. {{ $selectedVillageDetail['kecamatan'] }} · Dapil {{ $selectedVillageDetail['dapil'] }}</div>
                            </div>
                            <button wire:click="closeVillageDetail" type="button" style="width:24px;height:24px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#666;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;">✕</button>
                        </div>

                        <!-- Progress Bar & Stats -->
                        <div style="background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:12px;display:grid;gap:6px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;">
                                <span style="font-weight:600;color:#1e293b;">RW Sudah Ada Video</span>
                                <span style="font-weight:700;color:#7c3aed;">{{ number_format($selectedVillageDetail['rw_terisi']) }} / {{ number_format($selectedVillageDetail['total_rw']) }} RW</span>
                            </div>
                            <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $selectedVillageDetail['pct_terisi'] }}%;background:#7c3aed;"></div>
                            </div>
                        </div>

                        <!-- RW Heatmap Grid -->
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Peta Sebaran RW</div>
                        <div style="overflow-y:auto;max-height:140px;padding-right:4px;">
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach ($selectedVillageDetail['rw_list'] as $row)
                                    @php
                                        $count = (int) $row['video_count'];
                                        $bgColor = $count >= 3 ? '#dcfce7' : ($count >= 1 ? '#faf5ff' : 'transparent');
                                        $borderColor = $selectedRw === $row['nomor_rw'] ? '#7c3aed' : ($count >= 3 ? '#bbf7d0' : ($count >= 1 ? '#ddd6fe' : '#e5e7eb'));
                                    @endphp
                                    <button
                                        type="button"
                                        wire:key="rw-{{ $row['nomor_rw'] }}"
                                        wire:click="selectRw('{{ $row['nomor_rw'] }}')"
                                        title="RW {{ $row['nomor_rw'] }} · {{ $count }} video"
                                        style="width:34px;height:34px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s ease;background:{{ $bgColor }};border:{{ $selectedRw === $row['nomor_rw'] ? '2px' : '0.5px' }} solid {{ $borderColor }};color:#1a1a1a;"
                                    >
                                        {{ ltrim((string)$row['nomor_rw'], '0') ?: '0' }}
                                    </button>
                                @endforeach
                            </div>
                            
                            <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:10px;font-size:9px;color:#888;">
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#dcfce7;border:0.5px solid #bbf7d0;"></span>&ge;3 Video</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#faf5ff;border:0.5px solid #ddd6fe;"></span>1-2 Video</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;border:0.5px solid #e5e7eb;background:white;"></span>0 Video</span>
                            </div>
                        </div>

                        <!-- RW Video List -->
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-top:14px;margin-bottom:8px;">
                            {{ $selectedRw ? 'Video RW ' . ltrim($selectedRw, '0') : 'Semua Video Desa' }}
                        </div>
                        <div style="flex:1;overflow-y:auto;max-height:160px;display:grid;gap:8px;padding-right:4px;">
                            @php
                                $filteredVideos = $selectedVillageDetail['videos'];
                                if ($selectedRw) {
                                    $paddedRw = str_pad(trim((string) $selectedRw), 3, '0', STR_PAD_LEFT);
                                    $filteredVideos = $filteredVideos->filter(fn($v) => str_pad(trim((string) $v->rw_terkait), 3, '0', STR_PAD_LEFT) === $paddedRw);
                                }
                            @endphp
                            @forelse ($filteredVideos as $video)
                                <div style="border:0.5px solid #ede9fe;border-radius:10px;padding:8px 10px;background:#faf5ff;">
                                    <div style="font-size:11px;font-weight:600;color:#111827;display:flex;justify-content:space-between;align-items:center;">
                                        <span>{{ $video->anggotaDewan?->nama ?: '-' }}</span>
                                        <span style="font-size:9px;color:#71717a;font-weight:normal;">{{ $video->tanggal_posting?->format('d M Y') }}</span>
                                    </div>
                                    <div style="font-size:10px;color:#71717a;margin-top:3px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
                                        <span>{{ $video->platform_config['label'] }} · RW {{ ltrim($video->rw_terkait, '0') }}</span>
                                        @if ($video->url)
                                            <a href="{{ $video->url }}" target="_blank" rel="noreferrer" style="color:#6d28d9;text-decoration:underline;font-weight:600;">
                                                Buka video
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div style="padding:16px;text-align:center;color:#9ca3af;font-size:11px;border:0.5px dashed #d4d4d8;border-radius:10px;">
                                    Belum ada video pelayanan {{ $selectedRw ? 'untuk RW ini' : 'untuk desa ini' }}.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#71717a;box-sizing:border-box;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#faf5ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="ti ti-map-pin" style="font-size:24px;"></i>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah</div>
                        <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                            Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian sebaran video per RW.
                        </div>
                    </div>
                @endif

                <!-- Column 3: Daftar Kelurahan/Desa -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                    <div style="flex:1;overflow-y:auto;max-height:380px;display:grid;gap:8px;padding-right:4px;">
                        @forelse ($this->villageList as $v)
                            @php
                                $isActive = $selectedTargetWilayahId === $v['id'];
                                $rowStyle = $isActive 
                                    ? 'border:0.5px solid #ddd6fe;background:#faf5ff;box-shadow:inset 3px 0 0 #7c3aed;' 
                                    : 'border:0.5px solid #e5e7eb;background:#f9fafb;';
                            @endphp
                            <div 
                                wire:click="selectDesa('{{ $v['id'] }}')" 
                                style="border-radius:10px;padding:10px;cursor:pointer;transition:all 0.15s;{{ $rowStyle }}"
                                onmouseover="this.style.borderColor='#ddd6fe'" 
                                onmouseout="this.style.borderColor='{{ $isActive ? '#ddd6fe' : '#e5e7eb' }}'"
                            >
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                    <div style="min-width:0;flex:1;">
                                        <div style="font-size:12px;font-weight:600;color:#111827;">{{ $v['desa'] }}</div>
                                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">{{ $v['kecamatan'] }}</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:11px;font-weight:600;color:#7c3aed;">{{ $v['rw_terisi'] }} / {{ $v['total_rw'] }} RW</div>
                                        <div style="font-size:9px;color:#888;margin-top:2px;">{{ $v['pct_terisi'] }}% terisi</div>
                                    </div>
                                </div>
                                <div style="height:4px;background:#e5e7eb;border-radius:999px;margin-top:6px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $v['pct_terisi'] }}%;background:#7c3aed;border-radius:999px;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:11px;">Tidak ada data desa/kelurahan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Separator Line -->
            <div style="border-top: 1px solid #e5e7eb; margin: 10px 20px 20px 20px;"></div>

            <!-- Row 2: Library & Distribusi Materi Digital -->
            <div style="padding:0 20px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Library & Distribusi Materi</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Library materi digital dengan log distribusi per channel</div>
                    </div>
                    <button type="button" wire:click="openMateriForm" style="padding:7px 12px;border:none;border-radius:8px;background:#7c3aed;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                        + Upload materi baru
                    </button>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;" class="sosmed-materi-grid">
                    @forelse ($this->materiList as $materi)
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px;display:grid;gap:12px;align-content:start;">
                            <div style="height:148px;border-radius:12px;overflow:hidden;background:#f5f3ff;border:0.5px solid #ede9fe;display:flex;align-items:center;justify-content:center;">
                                @if ($materi->thumbnail)
                                    <img src="{{ asset('storage/' . ltrim($materi->thumbnail, '/')) }}" alt="{{ $materi->judul }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="display:grid;justify-items:center;gap:6px;color:#7c3aed;">
                                        <i class="ti ti-photo" style="font-size:28px;" aria-hidden="true"></i>
                                        <div style="font-size:11px;font-weight:700;">{{ \App\Models\MateriDigital::JENIS_OPTIONS[$materi->jenis] ?? ucfirst($materi->jenis) }}</div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                                    <div style="font-size:13px;font-weight:700;color:#111827;">{{ $materi->judul }}</div>
                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#ede9fe;color:#6d28d9;">{{ ucfirst($materi->status) }}</span>
                                </div>
                                <div style="font-size:10px;color:#71717a;margin-top:4px;">
                                    {{ \App\Models\MateriDigital::JENIS_OPTIONS[$materi->jenis] ?? ucfirst($materi->jenis) }}
                                    @if ($materi->topik)
                                        · {{ ucfirst($materi->topik) }}
                                    @endif
                                </div>
                                @if ($materi->deskripsi)
                                    <div style="font-size:11px;color:#525252;line-height:1.55;margin-top:8px;">
                                        {{ $materi->deskripsi }}
                                    </div>
                                @endif
                            </div>

                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                                <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                    <div style="font-size:10px;color:#888;">Distribusi</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $materi->distribusi_count) }}</div>
                                </div>
                                <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                    <div style="font-size:10px;color:#888;">Log</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;">{{ number_format((int) $materi->distribusis_count) }}</div>
                                </div>
                            </div>

                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <button type="button" wire:click="openDistribusiForm('{{ $materi->id }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#7c3aed;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                    Distribusi
                                </button>
                                <a href="{{ asset('storage/' . ltrim($materi->file_path, '/')) }}" target="_blank" rel="noreferrer" style="padding:6px 10px;border:0.5px solid #c4b5fd;border-radius:8px;background:white;color:#6d28d9;font-size:11px;font-weight:600;text-decoration:none;">
                                    Lihat file
                                </a>
                            </div>

                            <div>
                                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Log distribusi</div>
                                <div style="display:grid;gap:8px;">
                                    @forelse ($materi->distribusis->take(3) as $log)
                                        <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:9px 10px;background:#fafafa;">
                                            <div style="font-size:11px;font-weight:600;color:#111827;">
                                                {{ \App\Models\DistribusiMateri::CHANNEL_OPTIONS[$log->channel] ?? ucfirst($log->channel) }}
                                            </div>
                                            <div style="font-size:10px;color:#71717a;margin-top:3px;">
                                                {{ $log->tanggal_distribusi?->format('d M Y') }} · {{ $log->target_dapil ?: 'Semua dapil' }}
                                            </div>
                                            <div style="font-size:10px;color:#71717a;margin-top:3px;">
                                                {{ number_format((int) $log->terkirim) }} terkirim · {{ number_format((int) $log->target_rw_count) }} target RW
                                            </div>
                                        </div>
                                    @empty
                                        <div style="padding:16px;text-align:center;color:#9ca3af;font-size:11px;border:0.5px dashed #d4d4d8;border-radius:10px;">
                                            Belum ada log distribusi.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;padding:28px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                            Belum ada materi digital.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    @if ($drawerOpen)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,0.35);z-index:40;" wire:click="closeDrawer"></div>

        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100vh;background:white;border-left:0.5px solid #e5e7eb;z-index:50;display:flex;flex-direction:column;">
            <div style="padding:16px 18px;border-bottom:0.5px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:12px;font-weight:700;color:#111827;">
                        @if ($showDewanForm)
                            {{ $dewanEditId ? 'Detail / Edit Profil Dewan' : 'Tambah Profil Dewan' }}
                        @elseif ($showKontenForm)
                            {{ $kontenEditId ? 'Edit Konten' : 'Catat Konten Baru' }}
                        @elseif ($showMateriForm)
                            Upload Materi Baru
                        @else
                            Distribusi Materi
                        @endif
                    </div>
                    <div style="font-size:10px;color:#71717a;margin-top:3px;">
                        @if ($showKontenForm)
                            Drawer kanan 440px untuk input konten dan engagement
                        @elseif ($showDistribusiForm)
                            Catat channel distribusi, target dapil, dan estimasi RW
                        @else
                            Simpan perubahan langsung ke modul Sosial Media & Dewan
                        @endif
                    </div>
                </div>
                <button type="button" wire:click="closeDrawer" style="border:none;background:none;color:#71717a;cursor:pointer;">
                    <i class="ti ti-x" style="font-size:20px;" aria-hidden="true"></i>
                </button>
            </div>

            <div style="flex:1;overflow:auto;padding:16px 18px;">
                @if ($showDewanForm)
                    <form wire:submit.prevent="simpanDewan" style="display:grid;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Nama</label>
                            <input type="text" wire:model.defer="dNama" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @error('dNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Jabatan</label>
                            <input type="text" wire:model.defer="dJabatan" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @error('dJabatan') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Dapil</label>
                                <input type="text" wire:model.defer="dDapil" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Suara 2024</label>
                                <input type="number" min="0" wire:model.defer="dSuara2024" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Jabatan fraksi</label>
                                <input type="text" wire:model.defer="dJabatanFraksi" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Jabatan DPRD</label>
                                <input type="text" wire:model.defer="dJabatanDprd" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Jabatan partai</label>
                                <input type="text" wire:model.defer="dJabatanPartai" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">No. HP</label>
                                <input type="text" wire:model.defer="dHp" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Wilayah dapil</label>
                            <input type="text" wire:model.defer="dWilayahDapil" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                        <label style="display:flex;align-items:center;gap:10px;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;background:#fafafa;cursor:pointer;">
                            <input type="checkbox" wire:model.defer="dStatusPetahana">
                            <span style="font-size:11px;color:#374151;">Status petahana</span>
                        </label>

                        <div style="display:grid;gap:10px;">
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) 120px;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Instagram</label>
                                    <input type="text" wire:model.defer="dInstagram" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Followers</label>
                                    <input type="number" wire:model.defer="dIgFollowers" min="0" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) 120px;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">TikTok</label>
                                    <input type="text" wire:model.defer="dTiktok" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Followers</label>
                                    <input type="number" wire:model.defer="dTtFollowers" min="0" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) 120px;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">YouTube</label>
                                    <input type="text" wire:model.defer="dYoutube" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Subscribers</label>
                                    <input type="number" wire:model.defer="dYtSubs" min="0" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) 120px;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Twitter / X</label>
                                    <input type="text" wire:model.defer="dTwitter" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Followers</label>
                                    <input type="number" wire:model.defer="dTwFollowers" min="0" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) 120px;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Facebook</label>
                                    <input type="text" wire:model.defer="dFacebook" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#374151;">Followers</label>
                                    <input type="number" wire:model.defer="dFbFollowers" min="0" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                </div>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Nama tim media</label>
                                <input type="text" wire:model.defer="dTimNama" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">HP tim media</label>
                                <input type="text" wire:model.defer="dTimHp" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>

                        <button type="submit" style="padding:10px 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                            Simpan profil dewan
                        </button>
                    </form>
                @elseif ($showKontenForm)
                    <form wire:submit.prevent="simpanKonten" style="display:grid;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Anggota dewan</label>
                            <select wire:model.defer="kcDewanId" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                <option value="">Pilih anggota dewan</option>
                                @foreach ($this->dewanOptions as $dewanOption)
                                    <option value="{{ $dewanOption->id }}">{{ $dewanOption->nama }}{{ $dewanOption->dapil ? ' · '.$dewanOption->dapil : '' }}</option>
                                @endforeach
                            </select>
                            @error('kcDewanId') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Platform</label>
                                <select wire:model.defer="kcPlatform" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                    @foreach ($this->platformOptions as $platform => $config)
                                        <option value="{{ $platform }}">{{ $config['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Jenis konten</label>
                                <select wire:model.defer="kcJenis" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                    @foreach (\App\Models\KontenMedsos::JENIS_KONTEN_OPTIONS as $jenis => $label)
                                        <option value="{{ $jenis }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Caption</label>
                            <textarea wire:model.defer="kcCaption" rows="4" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;resize:vertical;color:#111827;background:white;"></textarea>
                        </div>

                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">URL konten</label>
                            <input type="url" wire:model.defer="kcUrl" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @error('kcUrl') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Tanggal posting</label>
                            <input type="date" wire:model.defer="kcTanggal" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>

                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Engagement</label>
                            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:6px;">
                                <input type="number" min="0" wire:model.defer="kcLikes" placeholder="Likes" style="padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                <input type="number" min="0" wire:model.defer="kcComments" placeholder="Comments" style="padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                <input type="number" min="0" wire:model.defer="kcShares" placeholder="Shares" style="padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                <input type="number" min="0" wire:model.defer="kcViews" placeholder="Views" style="padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Topik</label>
                                <select wire:model.defer="kcTopik" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                    @foreach (\App\Models\KontenMedsos::TOPIK_OPTIONS as $topik => $label)
                                        <option value="{{ $topik }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Dapil terkait</label>
                                <input type="text" wire:model.defer="kcDapil" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Desa terkait</label>
                                <input type="text" wire:model.defer="kcDesa" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">RW terkait</label>
                                <input type="text" wire:model.defer="kcRw" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>

                        <label style="display:flex;align-items:flex-start;gap:10px;padding:10px;border:0.5px solid #ddd6fe;border-radius:10px;background:#faf5ff;cursor:pointer;">
                            <input type="checkbox" wire:model.defer="kcIsVideo" style="margin-top:2px;">
                            <span style="font-size:11px;color:#4c1d95;">
                                Ini adalah video pelayanan PKS berbasis RW
                            </span>
                        </label>

                        <button type="submit" style="padding:10px 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                            Simpan konten
                        </button>
                    </form>
                @elseif ($showMateriForm)
                    <form wire:submit.prevent="simpanMateri" style="display:grid;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Judul materi</label>
                            <input type="text" wire:model.defer="matJudul" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @error('matJudul') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Jenis</label>
                                <select wire:model.defer="matJenis" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                    @foreach (\App\Models\MateriDigital::JENIS_OPTIONS as $jenis => $label)
                                        <option value="{{ $jenis }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:600;color:#374151;">Topik</label>
                                <input type="text" wire:model.defer="matTopik" placeholder="program / pemenangan / edukasi" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Deskripsi</label>
                            <textarea wire:model.defer="matDeskripsi" rows="4" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;resize:vertical;color:#111827;background:white;"></textarea>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">File materi</label>
                            <input type="file" wire:model="matFile" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;background:white;color:#111827;">
                            @error('matFile') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" style="padding:10px 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                            Upload materi
                        </button>
                    </form>
                @elseif ($showDistribusiForm)
                    <form wire:submit.prevent="simpanDistribusi" style="display:grid;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Channel distribusi</label>
                            <select wire:model.defer="distChannel" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                @foreach (\App\Models\DistribusiMateri::CHANNEL_OPTIONS as $channel => $label)
                                    <option value="{{ $channel }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Target dapil</label>
                            <input type="text" wire:model.defer="distDapil" placeholder="Semua / Dapil 1 / Dapil 2" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Estimasi target RW</label>
                            <input type="number" min="0" wire:model.defer="distRwCount" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#374151;">Catatan</label>
                            <textarea wire:model.defer="distCatatan" rows="4" style="width:100%;margin-top:6px;padding:9px 10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;resize:vertical;color:#111827;background:white;"></textarea>
                        </div>
                        <button type="submit" style="padding:10px 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                            Simpan distribusi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <style>
        @media (min-width: 1025px) {
            .kaderisasi-3col-grid {
                height: 490px;
            }
            .kaderisasi-3col-grid > div {
                height: 100%;
                overflow: hidden;
            }
            .kaderisasi-map-wrapper {
                height: 330px !important;
                flex: none !important;
            }
        }

        @media (max-width: 1024px) {
            .kaderisasi-3col-grid {
                grid-template-columns: minmax(0, 1fr) !important;
                height: auto !important;
            }
            .kaderisasi-3col-grid > div {
                height: auto !important;
            }
            .kaderisasi-map-wrapper {
                height: 320px !important;
                flex: none !important;
            }
        }

        @media (max-width: 1280px) {
            .sosmed-kpi-grid,
            .sosmed-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .sosmed-materi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 980px) {
            .sosmed-two-col-grid,
            .sosmed-materi-grid,
            .sosmed-engagement-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 720px) {
            .sosmed-kpi-grid,
            .sosmed-summary-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
