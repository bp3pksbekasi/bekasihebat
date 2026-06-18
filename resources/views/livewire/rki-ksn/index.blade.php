@php
    $isRki = $activeTab === 'rki';
    $pageTitle = $isRki ? 'Bipeka' : 'Binapora';
    $pageAlias = $isRki ? 'RKI' : 'KSN';
    $accent = $isRki ? '#ec4899' : '#16a34a';
    $accentSoft = $isRki ? '#fdf2f8' : '#f0fdf4';
    $accentBorder = $isRki ? '#f9a8d4' : '#86efac';
    $accentText = $isRki ? '#9d174d' : '#166534';
    $selectedDesa = $this->selectedDesa;
    $rkiKpi = $this->rkiKpi;
    $ksnKpi = $this->ksnKpi;
@endphp

<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:{{ $accent }};display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-heart-handshake" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:600;">{{ $pageTitle }}</div>
                    <div style="font-size:10px;color:#a1a1aa;margin-top:1px;">{{ $pageAlias }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:{{ $isRki ? '#fdf2f8' : '#f0fdf4' }};color:{{ $isRki ? '#9d174d' : '#166534' }};font-weight:500;">
                    <option value="">Semua dapil</option>
                    @foreach ($this->dapilOptions as $dapil)
                        <option value="{{ $dapil }}">{{ $dapil }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedKecamatan" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua kecamatan</option>
                    @foreach ($this->kecamatanOptions as $kecamatan)
                        <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                    @endforeach
                </select>
                <select wire:model.live="selectedFilterDesaId" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua desa</option>
                    @foreach ($this->filterDesaOptions as $desa)
                        <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                    @endforeach
                </select>
                <button type="button" wire:click="resetFilters" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
            </div>
        </div>
        <div style="width:26px;height:26px;background:{{ $accent }};color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">{{ $isRki ? 'RK' : 'KS' }}</div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
        @if (session()->has('message'))
            <div style="padding:14px 20px 0;">
                <div style="border:0.5px solid {{ $accentBorder }};background:{{ $accentSoft }};color:{{ $accentText }};border-radius:10px;padding:10px 12px;font-size:12px;">
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <div style="padding:18px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">{{ $pageTitle }}</h1>
                <div style="font-size:12px;color:#666;">
                    {{ $isRki ? 'Pembentukan titik RKI per RW, penggerak aktif, dan catatan sesi lapangan.' : 'Pembinaan titik KSN per desa, instruktur aktif, dan catatan sesi kegiatan.' }}
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                <div style="display:inline-flex;gap:6px;padding:4px 10px;border-radius:10px;background:{{ $accentSoft }};border:0.5px solid {{ $accentBorder }};font-size:12px;font-weight:600;color:{{ $accentText }};">
                    {{ $pageTitle }} · {{ $pageAlias }}
                </div>
                <button type="button" wire:click="{{ $isRki ? 'openRkiForm' : 'openKsnForm' }}" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;background:{{ $accent }};color:white;cursor:pointer;">
                    + Tambah {{ $isRki ? 'Titik RKI' : 'Titik KSN' }}
                </button>
            </div>
        </div>

        @if ($isRki)
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;padding:18px 20px;" class="rkiksn-kpi-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">RKI Aktif</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($rkiKpi['aktif']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Titik aktif</div>
                </div>
                <div style="background:linear-gradient(135deg,#ec4899,#db2777);border-radius:12px;padding:14px;color:white;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Pembentukan</div>
                    <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($rkiKpi['pembentukan']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">Proses berjalan</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Target RW</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($rkiKpi['totalRw']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">1 titik per RW</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Penggerak</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($rkiKpi['penggerak']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Aktif + pembentukan</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Bulan Ini</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($rkiKpi['kegiatanBulanIni']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($rkiKpi['pesertaBulanIni']) }} peserta</div>
                </div>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;padding:18px 20px;" class="rkiksn-kpi-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">KSN Aktif</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($ksnKpi['aktif']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Titik aktif</div>
                </div>
                <div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:12px;padding:14px;color:white;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Pembentukan</div>
                    <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($ksnKpi['pembentukan']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">Proses berjalan</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Target Desa</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($ksnKpi['totalDesa']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Minimal 1 titik</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Instruktur</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($ksnKpi['instruktur']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Aktif + pembentukan</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Bulan Ini</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($ksnKpi['sesiBulanIni']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($ksnKpi['pesertaBulanIni']) }} peserta</div>
                </div>
            </div>
        @endif

        <!-- Row 1: Cockpit Grid (3-Columns) -->
        <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
            <!-- Column 1: Map Card -->
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Peta Sebaran {{ $pageAlias }}</div>
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

            <!-- Column 2: Selected Village Detail -->
            @php $selectedVillageDetail = $this->selectedVillageDetail; @endphp
            @if ($selectedVillageDetail)
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;">
                        <div>
                            <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">{{ $selectedVillageDetail['desa'] }}</h2>
                            <div style="font-size:11px;color:#6b7280;margin-top:2px;">Kec. {{ $selectedVillageDetail['kecamatan'] }} · {{ $selectedVillageDetail['dapil'] }}</div>
                        </div>
                        <button wire:click="closeVillageDetail" type="button" style="width:24px;height:24px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#666;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;">✕</button>
                    </div>

                    <!-- Progress Bar & Stats -->
                    @php
                        if ($isRki) {
                            $targetVal = $selectedVillageDetail['jumlah_rw'];
                            $actualVal = $selectedVillageDetail['rki_aktif'];
                            $pctVal = $targetVal > 0 ? min(100, round(($actualVal / $targetVal) * 100)) : 0;
                            $statsLabel = "RW dengan RKI Aktif";
                        } else {
                            $targetVal = 1;
                            $actualVal = $selectedVillageDetail['ksn_aktif'];
                            $pctVal = $actualVal >= 1 ? 100 : 0;
                            $statsLabel = "KSN Aktif Desa";
                        }
                    @endphp
                    <div style="background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:12px;display:grid;gap:6px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;">
                            <span style="font-weight:600;color:#1e293b;">{{ $statsLabel }}</span>
                            <span style="font-weight:700;color:{{ $accent }};">{{ $actualVal }} / {{ $targetVal }} {{ $isRki ? 'RW' : 'KSN' }}</span>
                        </div>
                        <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pctVal }}%;background:{{ $accent }};"></div>
                        </div>
                    </div>

                    <!-- Extra section for RKI: RW Belum Ada RKI -->
                    @if ($isRki)
                        <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">RW Belum Ada RKI</div>
                        <div style="flex:1;overflow-y:auto;padding-right:4px;">
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @forelse ($this->rwBelumRki as $rw)
                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;border:0.5px solid {{ $accentBorder }};background:{{ $accentSoft }};color:{{ $accentText }};font-weight:600;">RW {{ $rw }}</span>
                                @empty
                                    <span style="font-size:11px;color:#6b7280;font-style:italic;">Semua RW sudah memiliki RKI</span>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#6b7280;padding:10px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:{{ $accentSoft }};color:{{ $accent }};display:flex;align-items:center;justify-content:center;margin-bottom:8px;">
                                <i class="ti ti-activity" style="font-size:18px;"></i>
                            </div>
                            <div style="font-size:12px;font-weight:600;color:#1f2937;">Sistem KSN Desa</div>
                            <div style="font-size:11px;margin-top:2px;">Minimal terdapat 1 titik senam KSN aktif di kelurahan/desa ini.</div>
                        </div>
                    @endif
                </div>
            @else
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#71717a;box-sizing:border-box;">
                    <div style="width:48px;height:48px;border-radius:50%;background:{{ $accentSoft }};color:{{ $accent }};display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                        <i class="ti ti-map-pin" style="font-size:24px;"></i>
                    </div>
                    <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah</div>
                    <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                        Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian progress.
                    </div>
                </div>
            @endif

            <!-- Column 3: Daftar Kelurahan/Desa -->
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                <div style="flex:1;overflow-y:auto;display:grid;gap:8px;padding-right:4px;">
                    @forelse ($this->villageList as $v)
                        @php
                            $isActive = $selectedDesaId === $v['id'];
                            $rowStyle = $isActive 
                                ? "border:0.5px solid {$accentBorder};background:{$accentSoft};box-shadow:inset 3px 0 0 {$accent};" 
                                : 'border:0.5px solid #e5e7eb;background:#f9fafb;';
                        @endphp
                        <div 
                            wire:click="selectDesa('{{ $v['id'] }}')" 
                            style="border-radius:10px;padding:10px;cursor:pointer;transition:all 0.15s;{{ $rowStyle }}"
                            onmouseover="this.style.borderColor='{{ $accent }}'" 
                            onmouseout="this.style.borderColor='{{ $isActive ? $accentBorder : '#e5e7eb' }}'"
                        >
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <div style="min-width:0;flex:1;">
                                    <div style="font-size:12px;font-weight:600;color:#111827;">{{ $v['desa'] }}</div>
                                    <div style="font-size:10px;color:#6b7280;margin-top:2px;">{{ $v['kecamatan'] }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:11px;font-weight:600;color:{{ $accentText }};">{{ $v['actual'] }} / {{ $v['target'] }} {{ $isRki ? 'RW' : 'KSN' }}</div>
                                    <div style="font-size:9px;color:#888;margin-top:2px;">{{ $v['pct'] }}%</div>
                                </div>
                            </div>
                            <div style="height:4px;background:#e5e7eb;border-radius:999px;margin-top:6px;overflow:hidden;">
                                <div style="height:100%;width:{{ $v['pct'] }}%;background:{{ $accent }};border-radius:999px;"></div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:11px;">Tidak ada data desa/kelurahan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Titik Cards & Logs -->
    @if ($selectedDesa)
        <div style="padding:20px 0 0;">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">
                            {{ $isRki ? 'Detail Titik RKI Desa' : 'Detail Titik Senam KSN Desa' }}
                        </div>
                        <h3 style="font-size:18px;font-weight:700;color:#111827;margin:4px 0 0;">
                            {{ $selectedDesa->desa }} (Kec. {{ $selectedDesa->kecamatan }} · {{ $selectedDesa->dapil }})
                        </h3>
                    </div>
                    <button type="button" wire:click="{{ $isRki ? 'openRkiForm' : 'openKsnForm' }}" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;background:{{ $accent }};color:white;cursor:pointer;">
                        + Tambah {{ $isRki ? 'Titik RKI' : 'Titik KSN' }}
                    </button>
                </div>

                @if ($isRki)
                    <!-- Titik RKI Cards Grid -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(360px, 1fr));gap:14px;">
                        @forelse ($this->rkiDetail as $titik)
                            <div wire:key="rki-card-{{ $titik->id }}" style="border:0.5px solid {{ $accentBorder }};border-radius:12px;background:{{ $accentSoft }};padding:14px;display:flex;flex-direction:column;gap:10px;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div>
                                        <div style="font-size:15px;font-weight:700;color:{{ $accentText }};">RW {{ $titik->nomor_rw }}</div>
                                        <div style="font-size:12px;color:#4b5563;margin-top:4px;font-weight:600;">{{ $titik->nama_penggerak }}</div>
                                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ $titik->lokasi ?: 'Lokasi belum diisi' }}</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                                        <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $titik->status_config['bg'] }};color:{{ $titik->status_config['text'] }};font-weight:700;">
                                            {{ $titik->status_config['label'] }}
                                        </span>
                                        <button type="button" wire:click="openRkiForm('{{ $titik->id }}')" style="padding:5px 9px;border:0.5px solid {{ $accentBorder }};border-radius:8px;background:white;color:{{ $accentText }};font-size:11px;font-weight:600;cursor:pointer;">
                                            Edit
                                        </button>
                                        <button type="button" wire:click="openLogForm('{{ $titik->id }}', '{{ \App\Models\TitikRki::class }}')" style="padding:5px 9px;border:none;border-radius:8px;background:{{ $accent }};color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                            Catat sesi
                                        </button>
                                    </div>
                                </div>

                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid {{ $accentBorder }};">{{ $titik->hari_kegiatan ?: 'Hari belum diisi' }}</span>
                                    <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid {{ $accentBorder }};">{{ $titik->jam_kegiatan ?: 'Jam belum diisi' }}</span>
                                    <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid {{ $accentBorder }};">Avg {{ number_format($titik->avg_peserta) }} peserta</span>
                                </div>

                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @forelse (($titik->jenis_kegiatan ?? []) as $jenis)
                                        <span style="font-size:10px;padding:2px 6px;border-radius:6px;background:#fff;border:0.5px solid {{ $accentBorder }};color:{{ $accentText }};">
                                            {{ \App\Models\TitikRki::JENIS_KEGIATAN_OPTIONS[$jenis] ?? $jenis }}
                                        </span>
                                    @empty
                                        <span style="font-size:10px;color:#9ca3af;">Jenis kegiatan belum dipilih</span>
                                    @endforelse
                                </div>

                                <div style="margin-top:6px;border-top:1px solid #f1f5f9;padding-top:8px;">
                                    <div style="font-size:10px;color:{{ $accentText }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Log sesi terbaru</div>
                                    <div style="display:grid;gap:6px;">
                                        @forelse ($titik->logSesis as $log)
                                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;background:white;border:0.5px solid {{ $accentBorder }};border-radius:8px;padding:8px 10px;">
                                                <div>
                                                    <div style="font-size:11px;font-weight:600;color:#111827;">{{ $log->tanggal_sesi?->format('d M Y H:i') }}</div>
                                                    <div style="font-size:10px;color:#666;margin-top:2px;">{{ number_format($log->jumlah_peserta) }} peserta · {{ $log->pelaksana ?: 'Pelaksana belum diisi' }}</div>
                                                    @if ($log->catatan)
                                                        <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ $log->catatan }}</div>
                                                    @endif
                                                </div>
                                                @if (!empty($log->foto))
                                                    <span style="font-size:10px;color:{{ $accentText }};">{{ count($log->foto) }} foto</span>
                                                @endif
                                            </div>
                                        @empty
                                            <div style="font-size:11px;color:#9ca3af;font-style:italic;">Belum ada log sesi.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Log Form for specific RKI Titik -->
                                @if ($showLogForm && $expandedLogKey === \App\Models\TitikRki::class.'|'.$titik->id)
                                    <form wire:submit.prevent="simpanLog" style="margin-top:6px;border-top:0.5px solid {{ $accentBorder }};padding-top:12px;display:grid;gap:10px;">
                                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;" class="rkiksn-form-grid">
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Tanggal</div>
                                                <input type="datetime-local" wire:model="logTanggal" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Peserta</div>
                                                <input type="number" min="1" wire:model="logPeserta" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Pelaksana</div>
                                                <input type="text" wire:model="logPelaksana" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Catatan</div>
                                            <textarea wire:model="logCatatan" rows="2" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;resize:vertical;color:#111827;"></textarea>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Foto</div>
                                            <input type="file" wire:model="logFoto" multiple style="font-size:12px;">
                                            @if (!empty($logFoto))
                                                <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ count($logFoto) }} file dipilih</div>
                                            @endif
                                        </div>
                                        <div style="display:flex;justify-content:flex-end;gap:8px;">
                                            <button type="button" wire:click="closeLogForm" style="padding:7px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;background:white;color:{{ $accentText }};font-size:11px;font-weight:600;cursor:pointer;">Batal</button>
                                            <button type="submit" style="padding:7px 12px;border:none;border-radius:8px;background:{{ $accent }};color:white;font-size:11px;font-weight:600;cursor:pointer;">Simpan sesi</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; padding:38px; border:1px dashed {{ $accentBorder }}; border-radius:12px; background:{{ $accentSoft }}; text-align:center; color:#9ca3af; font-size:12px;">
                                Belum ada titik RKI di desa ini.
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Titik KSN Cards Grid -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(360px, 1fr));gap:14px;">
                        @forelse ($this->ksnDetail as $titik)
                            <div wire:key="ksn-card-{{ $titik->id }}" style="border:0.5px solid {{ $accentBorder }};border-radius:12px;background:{{ $accentSoft }};padding:14px;display:flex;flex-direction:column;gap:10px;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div>
                                        <div style="font-size:15px;font-weight:700;color:{{ $accentText }};">{{ $titik->nama_titik }}</div>
                                        <div style="font-size:12px;color:#4b5563;margin-top:4px;font-weight:600;">Instruktur: {{ $titik->instruktur }}</div>
                                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">RW {{ $titik->lokasi_rw ?: '-' }} · {{ $titik->hari_senam ?: 'Hari belum diisi' }} {{ $titik->jam_senam ?: '' }}</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                                        <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $titik->status_config['bg'] }};color:{{ $titik->status_config['text'] }};font-weight:700;">
                                            {{ $titik->status_config['label'] }}
                                        </span>
                                        <button type="button" wire:click="openKsnForm('{{ $titik->id }}')" style="padding:5px 9px;border:0.5px solid {{ $accentBorder }};border-radius:8px;background:white;color:{{ $accentText }};font-size:11px;font-weight:600;cursor:pointer;">
                                            Edit
                                        </button>
                                        <button type="button" wire:click="openLogForm('{{ $titik->id }}', '{{ \App\Models\TitikSenam::class }}')" style="padding:5px 9px;border:none;border-radius:8px;background:{{ $accent }};color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                            Catat sesi
                                        </button>
                                    </div>
                                </div>

                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:{{ $accentText }};border:0.5px solid {{ $accentBorder }}; font-weight: 500;">Avg {{ number_format($titik->avg_peserta) }} peserta</span>
                                    @if ($titik->instruktur_2)
                                        <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:{{ $accentText }};border:0.5px solid {{ $accentBorder }};">Cadangan: {{ $titik->instruktur_2 }}</span>
                                    @endif
                                    @if ($titik->no_hp_instruktur)
                                        <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:{{ $accentText }};border:0.5px solid {{ $accentBorder }};">{{ $titik->no_hp_instruktur }}</span>
                                    @endif
                                </div>

                                <div style="margin-top:6px;border-top:1px solid #f1f5f9;padding-top:8px;">
                                    <div style="font-size:10px;color:{{ $accentText }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Log sesi senam</div>
                                    <div style="display:grid;gap:6px;">
                                        @forelse ($titik->logSesis as $log)
                                            <div style="background:white;border:0.5px solid {{ $accentBorder }};border-radius:8px;padding:8px 10px;">
                                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                                    <div>
                                                        <div style="font-size:11px;font-weight:600;color:#111827;">{{ $log->tanggal_sesi?->format('d M Y H:i') }}</div>
                                                        <div style="font-size:10px;color:#666;margin-top:2px;">{{ number_format($log->jumlah_peserta) }} peserta · {{ $log->pelaksana ?: 'Pelaksana belum diisi' }}</div>
                                                    </div>
                                                    @if (!empty($log->foto))
                                                        <span style="font-size:10px;color:{{ $accentText }};">{{ count($log->foto) }} foto</span>
                                                    @endif
                                                </div>
                                                @if ($log->catatan)
                                                    <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ $log->catatan }}</div>
                                                @endif
                                            </div>
                                        @empty
                                            <div style="font-size:11px;color:#9ca3af;font-style:italic;">Belum ada log sesi senam.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Log Form for specific KSN Titik -->
                                @if ($showLogForm && $expandedLogKey === \App\Models\TitikSenam::class.'|'.$titik->id)
                                    <form wire:submit.prevent="simpanLog" style="margin-top:6px;border-top:0.5px solid {{ $accentBorder }};padding-top:12px;display:grid;gap:10px;">
                                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;" class="rkiksn-form-grid">
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Tanggal</div>
                                                <input type="datetime-local" wire:model="logTanggal" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Peserta</div>
                                                <input type="number" min="1" wire:model="logPeserta" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                            <div>
                                                <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Pelaksana</div>
                                                <input type="text" wire:model="logPelaksana" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;color:#111827;">
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Catatan</div>
                                            <textarea wire:model="logCatatan" rows="2" style="width:100%;padding:8px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;font-size:12px;background:white;resize:vertical;color:#111827;"></textarea>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:{{ $accentText }};font-weight:700;margin-bottom:4px;">Foto</div>
                                            <input type="file" wire:model="logFoto" multiple style="font-size:12px;">
                                            @if (!empty($logFoto))
                                                <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ count($logFoto) }} file dipilih</div>
                                            @endif
                                        </div>
                                        <div style="display:flex;justify-content:flex-end;gap:8px;">
                                            <button type="button" wire:click="closeLogForm" style="padding:7px 10px;border:0.5px solid {{ $accentBorder }};border-radius:8px;background:white;color:{{ $accentText }};font-size:11px;font-weight:600;cursor:pointer;">Batal</button>
                                            <button type="submit" style="padding:7px 12px;border:none;border-radius:8px;background:{{ $accent }};color:white;font-size:11px;font-weight:600;cursor:pointer;">Simpan sesi</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; padding:38px; border:1px dashed {{ $accentBorder }}; border-radius:12px; background:{{ $accentSoft }}; text-align:center; color:#9ca3af; font-size:12px;">
                                Belum ada titik KSN di desa ini.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($showRkiForm)
        <div style="position:fixed;inset:0;background:rgba(17,24,39,.38);z-index:50;display:flex;justify-content:flex-end;" wire:click.self="resetRkiForm">
            <div style="width:min(460px,100%);height:100%;background:white;box-shadow:-12px 0 32px rgba(15,23,42,.12);padding:18px;overflow:auto;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:14px;">
                    <div>
                        <div style="font-size:10px;color:#ec4899;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Form RKI</div>
                        <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ $rkiEditId ? 'Edit titik RKI' : 'Tambah titik RKI' }}</div>
                    </div>
                    <button type="button" wire:click="resetRkiForm" style="border:none;background:none;color:#9ca3af;font-size:18px;cursor:pointer;">x</button>
                </div>

                <form wire:submit.prevent="simpanRki" style="display:grid;gap:12px;">
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">RW</div>
                        <select wire:model="rkiRw" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih RW</option>
                            @foreach ($this->rwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Nama Penggerak</div>
                        <input type="text" wire:model="rkiPenggerak" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">HP</div>
                        <input type="text" wire:model="rkiHp" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Lokasi</div>
                        <input type="text" wire:model="rkiLokasi" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Hari</div>
                            <input type="text" wire:model="rkiHari" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Jam</div>
                            <input type="text" wire:model="rkiJam" placeholder="09:00" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:6px;">Jenis kegiatan</div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                            @foreach (\App\Models\TitikRki::JENIS_KEGIATAN_OPTIONS as $value => $label)
                                <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:0.5px solid #fbcfe8;border-radius:10px;font-size:12px;background:#fdf2f8;color:#111827;">
                                    <input type="checkbox" wire:model="rkiJenis" value="{{ $value }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Status</div>
                        <select wire:model="rkiStatus" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="pembentukan">Pembentukan</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" style="padding:10px 14px;border:none;border-radius:10px;background:#ec4899;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if ($showKsnForm)
        <div style="position:fixed;inset:0;background:rgba(17,24,39,.38);z-index:50;display:flex;justify-content:flex-end;" wire:click.self="resetKsnForm">
            <div style="width:min(460px,100%);height:100%;background:white;box-shadow:-12px 0 32px rgba(15,23,42,.12);padding:18px;overflow:auto;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:14px;">
                    <div>
                        <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Form KSN</div>
                        <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ $ksnEditId ? 'Edit titik senam' : 'Tambah titik senam' }}</div>
                    </div>
                    <button type="button" wire:click="resetKsnForm" style="border:none;background:none;color:#9ca3af;font-size:18px;cursor:pointer;">x</button>
                </div>

                <form wire:submit.prevent="simpanKsn" style="display:grid;gap:12px;">
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Desa</div>
                        <select wire:model="ksnDesaId" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->desaOptions as $desa)
                                <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Nama titik</div>
                        <input type="text" wire:model="ksnNamaTitik" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Instruktur</div>
                        <input type="text" wire:model="ksnInstruktur" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">HP instruktur</div>
                        <input type="text" wire:model="ksnHpInstruktur" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Instruktur cadangan</div>
                        <input type="text" wire:model="ksnInstruktur2" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Hari senam</div>
                            <select wire:model="ksnHari" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                                <option value="">Pilih hari</option>
                                @foreach (\App\Models\TitikSenam::HARI_OPTIONS as $hari)
                                    <option value="{{ $hari }}">{{ ucfirst($hari) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Jam</div>
                            <input type="text" wire:model="ksnJam" placeholder="06:30" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Lokasi RW</div>
                        <input type="text" wire:model="ksnLokasiRw" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Status</div>
                        <select wire:model="ksnStatus" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="pembentukan">Pembentukan</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" style="padding:10px 14px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                        Simpan
                    </button>
                </form>
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
            .rkiksn-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 1024px) {
            .rkiksn-form-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 720px) {
            .rkiksn-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
