@php
    $kpi = $this->kpi;
    $alerts = $this->alerts;
    $trend = $this->trend;
    $dapilMap = $this->dapilMap;
    $kecamatanMap = $this->kecamatanMap;
    $accessScope = $this->accessScope;
    $isFullscreenMode = ! $showSidebar;
    $mapCardHeight = $isFullscreenMode ? 320 : 226;
    $mapImageHeight = $isFullscreenMode ? 190 : 118;
    $mapGridGap = $isFullscreenMode ? 12 : 10;
    $chartCardMinHeight = 0;
    $chartPlotHeight = 98;
    $monthLabel = \Carbon\Carbon::create()->month((int) $selectedBulan)->translatedFormat('F');
    $infraTrends = $this->infraTrends;
    $operationalHighlights = $this->operationalHighlights;
    $kpiCards = [
        [
            'label' => 'KORWE',
            'value' => number_format($kpi['korweTerbentuk']),
            'suffix' => '/ ' . number_format($kpi['targetKorwe']),
            'meta_left' => 'Target 2026: ' . number_format($kpi['korwePct']) . '%',
            'meta_right' => '',
            'icon' => 'building-community',
            'border' => '#fdba74',
            'bg' => '#fff7ed',
            'title' => '#ea580c',
            'value_color' => '#18181b',
            'meta' => '#9a3412',
            'watermark' => 'rgba(249,115,22,.10)',
        ],
        [
            'label' => 'KORTE',
            'value' => number_format($kpi['korteTerbentuk']),
            'suffix' => '/ ' . number_format($kpi['targetKorte']),
            'meta_left' => 'Target 2026: ' . number_format($kpi['kortePct']) . '%',
            'meta_right' => '',
            'icon' => 'users-group',
            'border' => '#fdba74',
            'bg' => '#fff7ed',
            'title' => '#d97706',
            'value_color' => '#18181b',
            'meta' => '#9a3412',
            'watermark' => 'rgba(245,158,11,.10)',
        ],
        [
            'label' => 'Sisir RW',
            'value' => number_format($kpi['rwTersisir']),
            'suffix' => '/ ' . number_format($kpi['totalRw']),
            'meta_left' => '',
            'meta_right' => '',
            'icon' => 'walk',
            'border' => '#fb923c',
            'bg' => 'linear-gradient(135deg,#fe5000 0%,#ea580c 100%)',
            'title' => 'rgba(255,255,255,.88)',
            'value_color' => '#ffffff',
            'meta' => 'rgba(255,255,255,.82)',
            'watermark' => 'rgba(255,255,255,.10)',
        ],
        [
            'label' => 'Profil RW',
            'value' => number_format($kpi['profilTerisi']),
            'suffix' => '/ ' . number_format($kpi['totalRw']),
            'meta_left' => number_format($kpi['profilLengkap']) . ' lengkap',
            'meta_right' => '',
            'icon' => 'clipboard-text',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#18181b',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
        [
            'label' => 'Event',
            'value' => number_format($kpi['eventSelesai']),
            'suffix' => '',
            'meta_left' => 'Event done',
            'meta_right' => '',
            'icon' => 'calendar-event',
            'border' => '#fdba74',
            'bg' => '#fff7ed',
            'title' => '#ea580c',
            'value_color' => '#18181b',
            'meta' => '#9a3412',
            'watermark' => 'rgba(249,115,22,.10)',
        ],
        [
            'label' => 'Kader',
            'value' => number_format($kpi['totalKader']),
            'suffix' => '',
            'meta_left' => '+' . number_format($kpi['kaderBulanIni']) . ' bulan ini',
            'meta_right' => '',
            'icon' => 'user-star',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#2563eb',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
        [
            'label' => 'Avg Popularitas',
            'value' => number_format((float) $kpi['avgPopularitas'], 1),
            'suffix' => '',
            'meta_left' => 'Skor dewan aktif',
            'meta_right' => '',
            'icon' => 'sparkles',
            'border' => '#c4b5fd',
            'bg' => '#faf5ff',
            'title' => '#7c3aed',
            'value_color' => '#7c3aed',
            'meta' => '#6d28d9',
            'watermark' => 'rgba(124,58,237,.10)',
        ],
        [
            'label' => 'Konten Medsos',
            'value' => number_format($kpi['kontenBulanIni']),
            'suffix' => '',
            'meta_left' => $monthLabel . ' ' . $selectedTahun,
            'meta_right' => '',
            'icon' => 'brand-instagram',
            'border' => '#c4b5fd',
            'bg' => '#faf5ff',
            'title' => '#7c3aed',
            'value_color' => '#18181b',
            'meta' => '#6d28d9',
            'watermark' => 'rgba(124,58,237,.10)',
        ],
        [
            'label' => 'UPA RW',
            'value' => number_format($kpi['upaRwFormal']),
            'suffix' => '',
            'meta_left' => 'UPA formal',
            'meta_right' => number_format($kpi['upaRw']) . ' profil',
            'icon' => 'home-up',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#18181b',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
        [
            'label' => 'RKI Aktif',
            'value' => number_format($kpi['rki']),
            'suffix' => '',
            'meta_left' => 'Infrasturktur',
            'meta_right' => '',
            'icon' => 'activity-heartbeat',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#18181b',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
        [
            'label' => 'KSN / Senam',
            'value' => number_format($kpi['ksn']),
            'suffix' => '',
            'meta_left' => 'Titik kegiatan',
            'meta_right' => '',
            'icon' => 'barbell',
            'border' => '#fde68a',
            'bg' => '#fffbeb',
            'title' => '#d97706',
            'value_color' => '#18181b',
            'meta' => '#b45309',
            'watermark' => 'rgba(217,119,6,.10)',
        ],
        [
            'label' => 'Aspirasi',
            'value' => number_format($kpi['aspirasiTotal']),
            'suffix' => '',
            'meta_left' => number_format($kpi['aspirasiSipd']).' masuk SIPD',
            'meta_right' => '',
            'icon' => 'message-chatbot',
            'border' => '#67e8f9',
            'bg' => '#ecfeff',
            'title' => '#0891b2',
            'value_color' => '#0f172a',
            'meta' => '#0f766e',
            'watermark' => 'rgba(14,165,233,.10)',
        ],
        [
            'label' => 'Realisasi Aspirasi',
            'value' => number_format($kpi['aspirasiRealisasi']),
            'suffix' => '',
            'meta_left' => number_format($kpi['aspirasiStuck']).' stuck',
            'meta_right' => '',
            'icon' => 'rosette-discount-check',
            'border' => '#86efac',
            'bg' => '#f0fdf4',
            'title' => '#15803d',
            'value_color' => '#14532d',
            'meta' => '#166534',
            'watermark' => 'rgba(34,197,94,.10)',
        ],
        [
            'label' => 'Relawan',
            'value' => number_format($kpi['relawan']),
            'suffix' => '',
            'meta_left' => 'Relawan milenial',
            'meta_right' => '',
            'icon' => 'users',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#18181b',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
        [
            'label' => 'Penggalang',
            'value' => number_format($kpi['penggalang']),
            'suffix' => '/ ' . number_format($kpi['targetPenggalang']),
            'meta_left' => 'Target 35.000',
            'meta_right' => '',
            'icon' => 'speakerphone',
            'border' => '#fdba74',
            'bg' => '#fff7ed',
            'title' => '#ea580c',
            'value_color' => '#18181b',
            'meta' => '#9a3412',
            'watermark' => 'rgba(249,115,22,.10)',
        ],
        [
            'label' => 'Saksi TPS',
            'value' => number_format($kpi['saksiTps']),
            'suffix' => '/ ' . number_format($kpi['totalTps']),
            'meta_left' => 'Terkonfirmasi',
            'meta_right' => 'TPS',
            'icon' => 'eye-check',
            'border' => '#bfdbfe',
            'bg' => '#eff6ff',
            'title' => '#2563eb',
            'value_color' => '#18181b',
            'meta' => '#1d4ed8',
            'watermark' => 'rgba(37,99,235,.10)',
        ],
    ];
@endphp

<div style="min-height:100vh;background:#fafafa;padding:12px 14px 16px;">
    <div style="width:100%;margin:0;display:flex;flex-direction:column;gap:10px;">
        @if ($showTopPanel)
            <div style="position:relative;background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                <button
                    type="button"
                    wire:click="hideTopPanel"
                    title="Sembunyikan panel"
                    style="position:absolute;top:10px;right:10px;width:26px;height:26px;border:none;border-radius:999px;background:#f4f4f5;color:#52525b;font-size:14px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="ti ti-minus"></i>
                </button>

                <div style="width:100%;display:flex;align-items:flex-end;justify-content:flex-end;gap:8px;flex-wrap:wrap;padding-right:32px;">
                    @if (($accessScope['mode'] ?? 'global') !== 'dapil')
                        <div>
                            <label style="display:block;font-size:10px;color:#888;margin-bottom:4px;">Dapil</label>
                            <select wire:model.live="selectedDapil" style="height:34px;min-width:150px;padding:0 30px 0 10px;border:0.5px solid #d4d4d8;border-radius:8px;background:white;font-size:12px;">
                                <option value="">Semua dapil</option>
                                @foreach ($this->dapilOptions as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div style="height:34px;padding:0 12px;border:0.5px solid #fdba74;border-radius:8px;background:#fff7ed;color:#c2410c;font-size:12px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;white-space:nowrap;">
                            {{ $accessScope['locked_dapil'] !== '' ? $accessScope['locked_dapil'] : 'Scope Dapil' }}
                        </div>
                    @endif
                    <div>
                        <label style="display:block;font-size:10px;color:#888;margin-bottom:4px;">Bulan</label>
                        <select wire:model.live="selectedBulan" style="height:34px;min-width:110px;padding:0 30px 0 10px;border:0.5px solid #d4d4d8;border-radius:8px;background:white;font-size:12px;">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:10px;color:#888;margin-bottom:4px;">Tahun</label>
                        <select wire:model.live="selectedTahun" style="height:34px;min-width:96px;padding:0 30px 0 10px;border:0.5px solid #d4d4d8;border-radius:8px;background:white;font-size:12px;">
                            @foreach (range((int) now()->year - 1, (int) now()->year + 2) as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button
                        type="button"
                        onclick="if (!document.fullscreenElement) { document.documentElement.requestFullscreen?.(); } else { document.exitFullscreen?.(); }"
                        style="height:34px;padding:0 14px;border:none;border-radius:8px;background:#18181b;color:white;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">
                        Full Screen
                    </button>
                    <a
                        href="{{ route('dashboard', $showSidebar ? [] : ['sidebar' => 1]) }}"
                        style="height:34px;padding:0 14px;border:0.5px solid #d4d4d8;border-radius:8px;background:white;color:#18181b;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;white-space:nowrap;"
                        wire:navigate>
                        {{ $showSidebar ? 'Sembunyikan Sidebar' : 'Tampilkan Sidebar' }}
                    </a>
                </div>
            </div>
        @else
            <div style="position:fixed;top:12px;right:14px;z-index:40;">
                <button
                    type="button"
                    wire:click="showTopPanel"
                    title="Tampilkan panel"
                    style="width:30px;height:30px;border:none;border-radius:999px;background:#18181b;color:white;font-size:14px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(0,0,0,.12);cursor:pointer;">
                    <i class="ti ti-layout-navbar-expand"></i>
                </button>
            </div>
        @endif

        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-top:28px;padding:4px 2px 2px;">
            <div>
                <div style="font-size:24px;font-weight:700;color:#18181b;line-height:1.15;">Dashboard Pemenangan DPD PKS Kabupaten Bekasi</div>
                <div style="font-size:11px;color:#a1a1aa;margin-top:8px;">Periode aktif: {{ $monthLabel }} {{ $selectedTahun }}</div>
            </div>

            <div style="height:84px;display:flex;align-items:flex-start;justify-content:flex-end;flex:0 0 auto;margin-right:24px;margin-top:-12px;">
                <img
                    src="{{ asset('images/pks-logo.png') }}"
                    alt="Logo PKS"
                    style="max-height:84px;max-width:156px;object-fit:contain;display:block;"
                >
            </div>
        </div>

        @if (($accessScope['mode'] ?? 'global') !== 'dapil')
            <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:{{ $mapGridGap }}px;">
                @foreach ($dapilMap as $dapil)
                    <button
                        type="button"
                        wire:click="$set('selectedDapil', '{{ $dapil['active'] ? '' : $dapil['dapil'] }}')"
                        title="{{ $dapil['dapil'] }}"
                        style="min-width:0;border:none;background:transparent;padding:0;cursor:pointer;">
                        <div style="height:{{ $mapCardHeight }}px;border-radius:18px;padding:12px 12px 11px;background:{{ $dapil['active'] ? '#fff7ed' : 'white' }};border:1px solid {{ $dapil['active'] ? '#fdba74' : '#e5e7eb' }};box-shadow:{{ $dapil['active'] ? '0 0 0 2px rgba(251,146,60,.14), 0 16px 28px rgba(0,0,0,.08)' : '0 8px 18px rgba(0,0,0,.05)' }};opacity:{{ $selectedDapil !== '' && ! $dapil['active'] ? '.74' : '1' }};display:flex;flex-direction:column;gap:10px;text-align:left;transition:all .18s ease;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <div style="font-size:12px;font-weight:800;color:#18181b;letter-spacing:.05em;">{{ str_replace('BEKASI ', 'DAPIL ', $dapil['dapil']) }}</div>
                                @if ($dapil['active'])
                                    <span style="font-size:9px;font-weight:700;padding:4px 7px;border-radius:999px;background:#fed7aa;color:#c2410c;">AKTIF</span>
                                @endif
                            </div>

                            <div style="flex:1;min-height:0;display:flex;align-items:center;justify-content:center;border-radius:14px;background:#fafafa;padding:8px;">
                                @if ($dapil['image'])
                                    <img
                                        src="{{ $dapil['image'] }}"
                                        alt="Peta {{ $dapil['dapil'] }}"
                                        style="max-width:100%;max-height:{{ $mapImageHeight }}px;object-fit:contain;display:block;"
                                    >
                                @else
                                    <div style="font-size:11px;color:#a1a1aa;">Peta tidak tersedia</div>
                                @endif
                            </div>

                            <div>
                                <div style="font-size:10px;color:#71717a;">{{ number_format($dapil['desa_total']) }} desa · {{ number_format($dapil['total_rw']) }} RW</div>
                                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-top:8px;">
                                    <div style="padding:6px 4px;border-radius:9px;background:#fff7ed;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#ea580c;line-height:1;">{{ number_format($dapil['korwe_pct']) }}%</div>
                                        <div style="font-size:8px;color:#9a3412;margin-top:2px;">Korwe</div>
                                    </div>
                                    <div style="padding:6px 4px;border-radius:9px;background:#fff7ed;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#ea580c;line-height:1;">{{ number_format($dapil['sisir_pct']) }}%</div>
                                        <div style="font-size:8px;color:#9a3412;margin-top:2px;">Sisir</div>
                                    </div>
                                    <div style="padding:6px 4px;border-radius:9px;background:#eff6ff;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#2563eb;line-height:1;">{{ number_format($dapil['profil_pct']) }}%</div>
                                        <div style="font-size:8px;color:#1d4ed8;margin-top:2px;">Profil</div>
                                    </div>
                                </div>
                                <div style="height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;margin-top:8px;">
                                    <div style="height:100%;width:{{ min(100, $dapil['score']) }}%;background:#f97316;border-radius:999px;"></div>
                                </div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        @else
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#f97316;">Kecamatan {{ $accessScope['locked_dapil'] !== '' ? $accessScope['locked_dapil'] : 'Dapil' }}</div>
                <div style="font-size:11px;color:#a1a1aa;">Peta dan capaian per kecamatan</div>
            </div>

            <div style="display:grid;grid-template-columns:repeat({{ max($kecamatanMap->count(), 1) }},minmax(0,1fr));gap:{{ $mapGridGap }}px;margin-top:10px;">
                @foreach ($kecamatanMap as $kecamatan)
                    <button
                        type="button"
                        wire:click="selectKecamatan('{{ $kecamatan['kecamatan'] }}')"
                        title="{{ $kecamatan['active'] ? 'Tampilkan semua kecamatan di dapil ini' : 'Filter ke ' . $kecamatan['kecamatan'] }}"
                        style="min-width:0;border:none;background:transparent;padding:0;cursor:pointer;">
                        <div style="height:{{ $mapCardHeight }}px;border-radius:18px;padding:12px 12px 11px;background:{{ $kecamatan['active'] ? '#fff7ed' : 'white' }};border:1px solid {{ $kecamatan['active'] ? '#fdba74' : '#e5e7eb' }};box-shadow:{{ $kecamatan['active'] ? '0 0 0 2px rgba(251,146,60,.14), 0 16px 28px rgba(0,0,0,.08)' : '0 8px 18px rgba(0,0,0,.05)' }};display:flex;flex-direction:column;gap:10px;text-align:left;transition:all .18s ease;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <div style="font-size:12px;font-weight:800;color:#18181b;letter-spacing:.05em;">{{ $kecamatan['kecamatan'] }}</div>
                                @if ($kecamatan['active'])
                                    <span style="font-size:9px;font-weight:700;padding:4px 7px;border-radius:999px;background:#fed7aa;color:#c2410c;">AKTIF</span>
                                @endif
                            </div>

                            <div style="flex:1;min-height:0;display:flex;align-items:center;justify-content:center;border-radius:14px;background:#fafafa;padding:8px;">
                                <img
                                    src="{{ $kecamatan['image'] }}"
                                    alt="Peta {{ $kecamatan['kecamatan'] }}"
                                    style="max-width:100%;max-height:{{ $mapImageHeight }}px;object-fit:contain;display:block;"
                                >
                            </div>

                            <div>
                                <div style="font-size:10px;color:#71717a;">{{ number_format($kecamatan['desa_total']) }} desa · {{ number_format($kecamatan['total_rw']) }} RW</div>
                                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-top:8px;">
                                    <div style="padding:6px 4px;border-radius:9px;background:#fff7ed;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#ea580c;line-height:1;">{{ number_format($kecamatan['korwe_pct']) }}%</div>
                                        <div style="font-size:8px;color:#9a3412;margin-top:2px;">Korwe</div>
                                    </div>
                                    <div style="padding:6px 4px;border-radius:9px;background:#fff7ed;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#ea580c;line-height:1;">{{ number_format($kecamatan['sisir_pct']) }}%</div>
                                        <div style="font-size:8px;color:#9a3412;margin-top:2px;">Sisir</div>
                                    </div>
                                    <div style="padding:6px 4px;border-radius:9px;background:#eff6ff;text-align:center;">
                                        <div style="font-size:11px;font-weight:700;color:#2563eb;line-height:1;">{{ number_format($kecamatan['profil_pct']) }}%</div>
                                        <div style="font-size:8px;color:#1d4ed8;margin-top:2px;">Profil</div>
                                    </div>
                                </div>
                                <div style="height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;margin-top:8px;">
                                    <div style="height:100%;width:{{ min(100, $kecamatan['score']) }}%;background:#f97316;border-radius:999px;"></div>
                                </div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#f97316;">Progress Infrastruktur</div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:10px;margin-top:12px;">
                @foreach ($kpiCards as $card)
                    <div style="position:relative;overflow:hidden;background:{{ $card['bg'] }};border:1px solid {{ $card['border'] }};border-radius:14px;padding:10px 12px;min-height:92px;">
                        <div style="position:absolute;right:-6px;bottom:-10px;font-size:50px;line-height:1;color:{{ $card['watermark'] }};pointer-events:none;">
                            <i class="ti ti-{{ $card['icon'] }}"></i>
                        </div>
                        <div style="position:relative;z-index:1;display:flex;flex-direction:column;height:100%;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                <div style="font-size:10px;font-weight:700;color:{{ $card['title'] }};text-transform:uppercase;letter-spacing:.06em;line-height:1.1;">{{ $card['label'] }}</div>
                                <div style="display:flex;align-items:flex-end;gap:6px;justify-content:flex-end;flex-wrap:wrap;max-width:54%;text-align:right;font-size:10px;color:{{ $card['meta'] }};line-height:1.15;">
                                    <span>{{ $card['meta_left'] }}</span>
                                    @if ($card['meta_right'] !== '')
                                        <span>{{ $card['meta_right'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;align-items:flex-end;gap:8px;margin-top:10px;">
                                <div style="font-size:42px;font-weight:800;line-height:1;color:{{ $card['value_color'] }};">{{ $card['value'] }}</div>
                                @if ($card['suffix'] !== '')
                                    <div style="font-size:11px;color:{{ $card['meta'] }};opacity:.9;line-height:1.1;">{{ $card['suffix'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#f97316;">Trend Infrastruktur 6 Bulan</div>
                <div style="font-size:11px;color:#a1a1aa;">Periode: 6 bulan terakhir</div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-top:12px;">
                @foreach ($infraTrends as $series)
                    @php
                        $seriesIcon = match ($series['label']) {
                            'Korwe' => 'building-community',
                            'UPA RW' => 'home-up',
                            'RKI Aktif' => 'activity-heartbeat',
                            'KSN / Senam' => 'barbell',
                            'Relawan Milenial' => 'users-group',
                            default => 'chart-bar',
                        };
                        $chartTheme = match ($series['label']) {
                            'Korwe', 'KSN / Senam' => [
                                'border' => '#fdba74',
                                'bg' => '#ffffff',
                                'watermark' => 'rgba(249,115,22,.10)',
                                'accent' => '#f97316',
                                'muted' => '#fb923c',
                            ],
                            default => [
                                'border' => '#93c5fd',
                                'bg' => '#ffffff',
                                'watermark' => 'rgba(59,130,246,.10)',
                                'accent' => '#2563eb',
                                'muted' => '#60a5fa',
                            ],
                        };
                    @endphp
                    <div style="position:relative;overflow:hidden;min-width:0;min-height:{{ $chartCardMinHeight }}px;border:1px solid {{ $chartTheme['border'] }};border-radius:14px;padding:10px 10px 9px;background:{{ $chartTheme['bg'] }};">
                        <div style="position:absolute;left:-4px;bottom:-8px;font-size:42px;line-height:1;color:{{ $chartTheme['watermark'] }};pointer-events:none;">
                            <i class="ti ti-{{ $seriesIcon }}"></i>
                        </div>
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                            <div>
                                <div style="font-size:11px;font-weight:700;color:#18181b;">{{ $series['label'] }}</div>
                                <div style="font-size:10px;color:#71717a;margin-top:3px;">6 bulan terakhir</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:14px;font-weight:700;color:{{ $chartTheme['accent'] }};">{{ number_format($series['current']) }}</div>
                                <div style="font-size:10px;font-weight:600;color:{{ $series['change_pct'] >= 0 ? '#16a34a' : '#dc2626' }};">
                                    {{ $series['change_pct'] >= 0 ? '+' : '' }}{{ number_format($series['change_pct']) }}%
                                </div>
                            </div>
                        </div>

                    <div style="display:flex;align-items:flex-end;gap:5px;height:{{ $chartPlotHeight }}px;margin-top:10px;">
                            @foreach ($series['months'] as $month)
                                <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:5px;height:100%;">
                                <div style="width:100%;max-width:24px;height:{{ max((int) round($month['pct'] * 0.82), 8) }}px;border-radius:8px 8px 3px 3px;background:{{ $month['current'] ? $chartTheme['accent'] : '#d4d4d8' }};"></div>
                                <div style="font-size:9px;color:{{ $month['current'] ? $chartTheme['accent'] : '#a1a1aa' }};font-weight:{{ $month['current'] ? '700' : '500' }};">{{ $month['label'] }}</div>
                                    <div style="font-size:9px;color:#71717a;">{{ number_format($month['count']) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-top:8px;height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ max(6, min(100, $series['max'] > 0 ? (int) round(($series['current'] / $series['max']) * 100) : 0)) }}%;background:{{ $chartTheme['accent'] }};border-radius:999px;"></div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:6px;font-size:10px;color:#71717a;">
                            <span>Bulan ini: {{ number_format($series['current']) }}</span>
                            <span>Puncak: {{ number_format($series['max']) }}</span>
                        </div>
                    </div>
                @endforeach

                <div style="position:relative;overflow:hidden;min-width:0;min-height:{{ $chartCardMinHeight }}px;border:1px solid #fdba74;border-radius:14px;padding:10px 10px 9px;background:#ffffff;">
                    <div style="position:absolute;left:-4px;bottom:-8px;font-size:42px;line-height:1;color:rgba(249,115,22,.10);pointer-events:none;">
                        <i class="ti ti-walk"></i>
                    </div>
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#18181b;">Trend Sisir RW</div>
                            <div style="font-size:10px;color:#71717a;margin-top:3px;">6 bulan terakhir</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:14px;font-weight:700;color:#fe5000;">{{ number_format($trend['current']) }}</div>
                            <div style="font-size:10px;font-weight:600;color:{{ $trend['change_pct'] >= 0 ? '#16a34a' : '#dc2626' }};">
                                {{ $trend['change_pct'] >= 0 ? '+' : '' }}{{ number_format($trend['change_pct']) }}%
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-end;gap:5px;height:{{ $chartPlotHeight }}px;margin-top:10px;">
                        @foreach ($trend['months'] as $t)
                            <div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:5px;height:100%;">
                                <div style="width:100%;max-width:24px;height:{{ max((int) round($t['pct'] * 0.82), 8) }}px;border-radius:8px 8px 3px 3px;background:{{ $t['current'] ? '#fe5000' : '#d4d4d8' }};"></div>
                                <div style="font-size:9px;color:{{ $t['current'] ? '#ea580c' : '#a1a1aa' }};font-weight:{{ $t['current'] ? '700' : '500' }};">{{ $t['label'] }}</div>
                                <div style="font-size:9px;color:#71717a;">{{ number_format($t['count']) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top:8px;height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ max(6, min(100, (($trend['months']->max('count') ?? 0) > 0) ? (int) round(($trend['current'] / $trend['months']->max('count')) * 100) : 0)) }}%;background:#fe5000;border-radius:999px;"></div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:6px;font-size:10px;color:#71717a;">
                        <span>Bulan ini: {{ number_format($trend['current']) }}</span>
                        <span>Puncak: {{ number_format($trend['months']->max('count') ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:{{ count($alerts) > 0 ? 'minmax(0,1.35fr) minmax(340px,.65fr)' : 'minmax(0,1fr)' }};gap:10px;align-items:stretch;">
            <div style="height:100%;background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#f97316;">Prestasi/Capaian</div>
                    </div>
                    <div style="font-size:11px;color:#a1a1aa;">Update {{ now()->format('d M Y H:i') }}</div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:14px;">
                    @foreach ($operationalHighlights as $highlight)
                        @php
                            $highlightTheme = $highlight['theme'] === 'orange'
                                ? ['border' => '#fdba74', 'bg' => '#fff7ed', 'title' => '#ea580c', 'meta' => '#9a3412', 'watermark' => 'rgba(249,115,22,.10)']
                                : ['border' => '#bfdbfe', 'bg' => '#eff6ff', 'title' => '#2563eb', 'meta' => '#1d4ed8', 'watermark' => 'rgba(37,99,235,.10)'];
                        @endphp
                        <div style="position:relative;overflow:hidden;border:1px solid {{ $highlightTheme['border'] }};background:{{ $highlightTheme['bg'] }};border-radius:14px;padding:12px 14px;min-width:0;min-height:98px;">
                            <div style="position:absolute;right:-6px;bottom:-8px;font-size:52px;line-height:1;color:{{ $highlightTheme['watermark'] }};pointer-events:none;">
                                <i class="ti ti-{{ $highlight['icon'] }}"></i>
                            </div>
                            <div style="position:relative;z-index:1;display:flex;flex-direction:column;height:100%;">
                                <div style="font-size:10px;font-weight:700;color:{{ $highlightTheme['title'] }};text-transform:uppercase;letter-spacing:.06em;">{{ $highlight['label'] }}</div>
                                <div style="font-size:18px;font-weight:700;color:#18181b;line-height:1.2;margin-top:8px;max-width:86%;">{{ $highlight['value'] }}</div>
                                <div style="font-size:11px;font-weight:600;color:{{ $highlightTheme['meta'] }};margin-top:8px;">{{ $highlight['meta'] }}</div>
                                <div style="font-size:10px;color:#71717a;margin-top:4px;max-width:92%;">{{ $highlight['detail'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if (count($alerts) > 0)
                <div style="height:100%;background:white;border:0.5px solid #e5e5e5;border-radius:14px;padding:14px 16px;display:flex;flex-direction:column;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#f97316;">Perlu Perhatian</div>
                        </div>
                        <div style="font-size:11px;color:#a1a1aa;">{{ number_format(count($alerts)) }} alert</div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat({{ max(count($alerts), 1) }},minmax(0,1fr));gap:8px;margin-top:14px;flex:1;align-items:stretch;">
                        @foreach ($alerts as $alert)
                            @php
                                $c = [
                                    'warning' => ['bg' => '#fffbeb', 'bc' => '#fde68a', 'ic' => '#d97706', 'tc' => '#92400e'],
                                    'danger' => ['bg' => '#fef2f2', 'bc' => '#fecaca', 'ic' => '#dc2626', 'tc' => '#991b1b'],
                                    'info' => ['bg' => '#eff6ff', 'bc' => '#bfdbfe', 'ic' => '#2563eb', 'tc' => '#1e40af'],
                                ][$alert['type']] ?? ['bg' => '#eff6ff', 'bc' => '#bfdbfe', 'ic' => '#2563eb', 'tc' => '#1e40af'];
                            @endphp
                            <div style="position:relative;overflow:hidden;height:100%;border:1px solid {{ $c['bc'] }};background:{{ $c['bg'] }};border-radius:12px;padding:11px 12px;">
                                <div style="display:flex;align-items:flex-start;gap:10px;">
                                    <i class="ti ti-{{ $alert['icon'] }}" style="font-size:16px;color:{{ $c['ic'] }};margin-top:1px;" aria-hidden="true"></i>
                                    <div style="min-width:0;flex:1;">
                                        <div style="font-size:11px;line-height:1.45;color:{{ $c['tc'] }};">{!! $alert['text'] !!}</div>
                                        <a href="{{ $alert['link'] }}" style="display:inline-flex;margin-top:8px;font-size:11px;font-weight:600;color:#ea580c;text-decoration:none;" wire:navigate>{{ $alert['link_text'] }} →</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
