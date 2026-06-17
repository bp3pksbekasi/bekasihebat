@php
    $kpi = $this->kpi;
    $upaTab = $activeTab === 'upa';
    $pelatihanTab = $activeTab === 'pelatihan';
    $mapTab = $activeTab === 'map';
    $selectedPelatihan = $this->selectedPelatihan;
    $selectedVillageDetail = $this->selectedVillageDetail;
@endphp

<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-school" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div style="font-size:14px;font-weight:600;">Kaderisasi</div>
            </div>
            <div style="display:grid;gap:8px;flex:1 1 auto;min-width:0;">
                <div x-data="{
                    wilayahs: @js($this->allWilayahs),
                    compareNatural(a, b) {
                        return String(a).localeCompare(String(b), 'id-ID', { numeric: true, sensitivity: 'base' });
                    },
                    toTitleCase(val) {
                        return String(val ?? '').toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
                    },
                    get kecamatanOptions() {
                        let dapil = $wire.selectedDapil;
                        if (!dapil) {
                            return [...new Set(this.wilayahs.map(w => w.kecamatan))].sort(this.compareNatural);
                        }
                        return [...new Set(this.wilayahs.filter(w => w.dapil === dapil).map(w => w.kecamatan))].sort(this.compareNatural);
                    },
                    get desaOptions() {
                        let dapil = $wire.selectedDapil;
                        let kec = $wire.selectedKecamatan;
                        let filtered = this.wilayahs;
                        if (dapil) {
                            filtered = filtered.filter(w => w.dapil === dapil);
                        }
                        if (kec) {
                            filtered = filtered.filter(w => w.kecamatan === kec);
                        }
                        return [...new Set(filtered.map(w => w.desa))].sort(this.compareNatural);
                    }
                }" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select x-model="$wire.selectedDapil" id="selectedDapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#eff6ff;color:#1d4ed8;font-weight:500;">
                        <option value="">Semua dapil</option>
                        @foreach ($this->dapilOptions as $dapil)
                            <option value="{{ $dapil }}">{{ $dapil }}</option>
                        @endforeach
                    </select>
                    <select x-model="$wire.selectedKecamatan" id="selectedKecamatanSelect" wire:ignore style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua kecamatan</option>
                        <template x-for="kec in kecamatanOptions" :key="kec">
                            <option :value="kec" x-text="toTitleCase(kec)" :selected="$wire.selectedKecamatan === kec"></option>
                        </template>
                    </select>
                    <select x-model="$wire.selectedDesa" id="selectedDesaSelect" wire:ignore style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua desa/kelurahan</option>
                        <template x-for="desa in desaOptions" :key="desa">
                            <option :value="desa" x-text="toTitleCase(desa)" :selected="$wire.selectedDesa === desa"></option>
                        </template>
                    </select>
                    <select wire:model.live="filterJenjang" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua jenjang</option>
                        @foreach (\App\Models\Kader::JENJANG_OPTIONS as $key => $config)
                            <option value="{{ $key }}">{{ $config['label'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, HP, WA, desa..." style="min-width:220px;padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                </div>
            </div>
        </div>
        <div style="width:26px;height:26px;background:#2563eb;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">KD</div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
        @if (session()->has('message'))
            <div style="margin:14px 20px 0;padding:10px 12px;border-radius:10px;background:#eff6ff;color:#1d4ed8;font-size:12px;border:0.5px solid #bfdbfe;">
                {{ session('message') }}
            </div>
        @endif

        <div style="padding:18px 20px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Kaderisasi</h1>
                <div style="font-size:12px;color:#666;">Database kader, mutasi kader, dan pelatihan berjenjang untuk penguatan wilayah.</div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;">
                <button type="button" wire:click="importFromInfra" style="padding:6px 10px;border:0.5px solid #2563eb;border-radius:8px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:600;cursor:pointer;">
                    Sinkronisasi infra
                </button>
                <button type="button" wire:click="openKaderForm" style="padding:6px 10px;border:none;border-radius:8px;background:#2563eb;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                    + Tambah kader
                </button>
            </div>
        </div>

        <div style="padding:14px 20px 0;">
            <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:#f4f4f5;border:0.5px solid #e4e4e7;">
                <button type="button" wire:click="setActiveTab('map')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $mapTab ? '#dbeafe' : 'transparent' }};color:{{ $mapTab ? '#1d4ed8' : '#71717a' }};">Peta Sebaran</button>
                <button type="button" wire:click="setActiveTab('pembentukan_upa')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $activeTab === 'pembentukan_upa' ? '#dbeafe' : 'transparent' }};color:{{ $activeTab === 'pembentukan_upa' ? '#1d4ed8' : '#71717a' }};">Pembentukan UPA</button>
            </div>
        </div>

        @if ($mapTab)
            <div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;padding:18px 20px;" class="kaderisasi-kpi-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Total Kader</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['totalKader']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:14px;color:white;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Aktif</div>
                    <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($kpi['kaderAktif']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">RW Ada Kader</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['rwAdaKader']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">UPA Terbentuk</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['upaTerbentuk']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Pelatihan Bulan Ini</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['pelatihanBulanIni']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">RW Kosong</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['rwKosong']) }}</div>
                </div>
            </div>

            <!-- Row 1: 3-column grid for Map, Daftar Kelurahan, and Detail Desa -->
            <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;">
                <!-- Column 1: Map Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div>
                            <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Peta Sebaran Kader</div>
                            <div style="font-size:14px;color:#111827;font-weight:600;margin-top:2px;">
                                @if ($selectedDesa)
                                    Desa/Kelurahan: {{ $selectedDesa }} (Kec. {{ $selectedKecamatan }})
                                @elseif ($selectedKecamatan)
                                    Kecamatan {{ $selectedKecamatan }}
                                @elseif ($selectedDapil)
                                    {{ $selectedDapil }}
                                @else
                                    Kabupaten Bekasi
                                @endif
                            </div>
                        </div>
                        @if ($selectedDapil || $selectedKecamatan || $selectedDesa)
                            <button type="button" wire:click="resetFilters" style="font-size:11px;color:#dc2626;border:none;background:none;cursor:pointer;text-decoration:underline;">✕ Reset Filter</button>
                        @endif
                    </div>
                    
                    <div class="kaderisasi-map-wrapper" style="position:relative;width:100%;flex:1;border-radius:10px;overflow:hidden;border:0.5px solid #e5e5e5;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ $this->mapImage }}" style="max-width:100%;max-height:100%;object-fit:contain;display:block;" alt="Peta Sebaran">
                        
                        <!-- Markers -->
                        @foreach ($this->mapMarkers as $marker)
                            <button type="button" 
                                wire:click="selectVillage('{{ $marker['id'] }}')" 
                                title="{{ $marker['label'] }}" 
                                style="position:absolute;left:{{ $marker['x'] }}%;top:{{ $marker['y'] }}%;transform:translate(-50%,-50%);width:{{ $marker['size'] }}px;height:{{ $marker['size'] }}px;border-radius:50%;border:2px solid white;background:{{ $marker['color'] }};box-shadow:0 3px 8px rgba(0,0,0,0.25);cursor:pointer;transition:transform 0.15s ease-in-out;z-index:10;"
                                onmouseover="this.style.transform='translate(-50%,-50%) scale(1.3)'"
                                onmouseout="this.style.transform='translate(-50%,-50%) scale(1)'">
                            </button>
                        @endforeach
                        
                        <!-- Legend -->
                        <div style="position:absolute;bottom:10px;left:10px;background:rgba(255,255,255,0.95);padding:6px 10px;border-radius:8px;font-size:10px;display:flex;gap:10px;border:0.5px solid #e5e5e5;box-shadow:0 2px 6px rgba(0,0,0,0.05);flex-wrap:wrap;z-index:20;">
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                                <span>Mencapai Target</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#eab308;display:inline-block;"></span>
                                <span>Parsial</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                                <span>Belum Ada Kader</span>
                            </div>
                        </div>
                        
                        <div style="position:absolute;right:10px;bottom:10px;background:rgba(255,255,255,0.95);padding:6px 10px;border-radius:8px;font-size:9px;color:#666;border:0.5px solid #e5e5e5;box-shadow:0 2px 6px rgba(0,0,0,0.05);z-index:20;">
                            Ukuran = Densitas Kader
                        </div>
                    </div>
                    
                    @if (!$selectedDapil && !$selectedKecamatan)
                        <div style="margin-top:10px;font-size:11px;color:#666;background:#f4f4f5;padding:8px 10px;border-radius:8px;border:0.5px solid #e4e4e7;text-align:center;">
                            💡 <strong>Info:</strong> Pilih <strong>Dapil 1</strong>, <strong>Dapil 4</strong>, atau Kecamatan <strong>Setu</strong> pada filter di atas untuk memunculkan marker sebaran desa interaktif.
                        </div>
                    @endif
                    
                    <!-- Komposisi Jenjang (Excluding Madya, Dewasa, Purna) -->
                    <div style="border-top:0.5px solid #e5e5e5;padding-top:10px;margin-top:10px;">
                        <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Komposisi Jenjang</div>
                        <div style="display:grid;gap:6px;">
                            @php
                                $filteredJenjang = $this->jenjangChart->reject(fn ($item) => in_array($item['key'], ['madya', 'dewasa', 'purna']));
                                $maxCount = max($filteredJenjang->max('count'), 1);
                            @endphp
                            @foreach ($filteredJenjang as $item)
                                <div style="display:grid;grid-template-columns:80px 1fr 30px;align-items:center;gap:6px;">
                                    <div style="font-size:10px;color:#374151;">{{ $item['label'] }}</div>
                                    <div style="height:6px;background:#eff6ff;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ round(($item['count'] / $maxCount) * 100) }}%;background:{{ $item['color'] }};"></div>
                                    </div>
                                    <div style="font-size:10px;font-weight:700;color:#111827;text-align:right;">{{ $item['count'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Column 2: Detail Desa Panel or Placeholder -->
                @if ($selectedVillageDetail)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;position:relative;display:flex;flex-direction:column;box-sizing:border-box;">
                        <button type="button" wire:click="closeVillageDetail" style="position:absolute;top:14px;right:14px;border:none;background:none;font-size:16px;color:#71717a;cursor:pointer;line-height:1;">✕</button>
                        
                        <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Detail Desa/Kelurahan</div>
                        <h3 style="font-size:16px;font-weight:700;color:#111827;margin:4px 0 0 0;">{{ $selectedVillageDetail['desa'] }}</h3>
                        <div style="font-size:11px;color:#666;margin-top:2px;">Kec. {{ $selectedVillageDetail['kecamatan'] }} · {{ $selectedVillageDetail['dapil'] }}</div>
                        
                        <!-- Target status badge on a new line -->
                        <div style="margin-top:8px;">
                            <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;padding:4px 10px;border-radius:999px;background:{{ $selectedVillageDetail['statusBg'] }};color:{{ $selectedVillageDetail['statusText'] }};border:0.5px solid rgba(0,0,0,0.05);">
                                <span style="width:6px;height:6px;border-radius:50%;background:{{ $selectedVillageDetail['statusDot'] }};display:inline-block;"></span>
                                {{ $selectedVillageDetail['statusLabel'] }}
                            </span>
                        </div>
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px;padding-top:14px;border-top:0.5px solid #e5e5e5;">
                            <div>
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">Kader Aktif / Target</div>
                                <div style="font-size:15px;font-weight:700;color:#111827;margin-top:2px;">{{ $selectedVillageDetail['count'] }} / {{ $selectedVillageDetail['target'] }}</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">Jumlah RW</div>
                                <div style="font-size:15px;font-weight:700;color:#111827;margin-top:2px;">{{ $selectedVillageDetail['jumlah_rw'] ?: 0 }}</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">TPS</div>
                                <div style="font-size:15px;font-weight:700;color:#111827;margin-top:2px;">{{ $selectedVillageDetail['jumlah_tps'] ?: 0 }}</div>
                            </div>
                            <div>
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">DPT</div>
                                <div style="font-size:15px;font-weight:700;color:#111827;margin-top:2px;">{{ number_format($selectedVillageDetail['jumlah_dpt'] ?: 0) }}</div>
                            </div>
                        </div>
                        
                        <div style="margin-top:14px;padding-top:14px;border-top:0.5px solid #e5e5e5;display:flex;flex-direction:column;justify-content:center;align-items:center;flex:1;min-height:0;text-align:center;">
                            <div style="width:40px;height:40px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;margin-bottom:8px;">
                                <i class="ti ti-users" style="font-size:18px;"></i>
                            </div>
                            <div style="font-size:12px;font-weight:600;color:#111827;">{{ $selectedVillageDetail['count'] }} Kader Aktif</div>
                            <div style="font-size:10px;color:#6b7280;margin-top:2px;max-width:185px;line-height:1.4;">Daftar lengkap kader dapat dilihat pada tabel Database Kader di bawah.</div>
                            <button type="button" onclick="document.getElementById('database-kader-section').scrollIntoView({behavior: 'smooth'})" style="margin-top:10px;padding:6px 12px;border:0.5px solid #2563eb;border-radius:6px;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:4px;">
                                <span>🔍 Lihat di Tabel</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:20px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#71717a;box-sizing:border-box;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="ti ti-map-pin" style="font-size:24px;"></i>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah</div>
                        <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                            Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian target, jumlah RW, TPS, dan daftar kader aktif.
                        </div>
                    </div>
                @endif

                <!-- Column 3: Daftar Kelurahan/Desa -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                    <div style="flex:1;overflow-y:auto;display:grid;gap:6px;padding-right:4px;">
                        @php
                            $wilayahList = \App\Models\TargetWilayah::query()
                                ->when($selectedDapil !== '', fn ($q) => $q->byDapil($selectedDapil))
                                ->when($selectedKecamatan !== '', fn ($q) => $q->byKecamatan($selectedKecamatan))
                                ->orderBy('desa')
                                ->get();
                            
                             $kaderCounts = \App\Models\Kader::query()
                                 ->where('status', 'aktif')
                                 ->select('target_wilayah_id')
                                 ->selectRaw('COUNT(*) as total_kader')
                                 ->groupBy('target_wilayah_id')
                                 ->pluck('total_kader', 'target_wilayah_id');
                        @endphp
                        
                        @forelse ($wilayahList as $w)
                            @php
                                $count = $kaderCounts[$w->id] ?? 0;
                                $target = $w->target_korwe_2029 ?: $w->jumlah_rw ?: 1;
                                $isActive = $selectedVillageId === $w->id;
                                
                                if ($count >= $target) {
                                    $dotColor = '#22c55e';
                                    $badgeBg = '#dcfce7';
                                    $badgeText = '#166534';
                                } elseif ($count > 0) {
                                    $dotColor = '#eab308';
                                    $badgeBg = '#fef3c7';
                                    $badgeText = '#b45309';
                                } else {
                                    $dotColor = '#ef4444';
                                    $badgeBg = '#fee2e2';
                                    $badgeText = '#991b1b';
                                }
                            @endphp
                            <button type="button" 
                                wire:click="selectVillage('{{ $w->id }}')" 
                                style="width:100%;text-align:left;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border:0.5px solid {{ $isActive ? '#2563eb' : '#e5e5e5' }};border-radius:8px;background:{{ $isActive ? '#eff6ff' : 'white' }};cursor:pointer;transition:all 0.15s ease-in-out;"
                                onmouseover="this.style.borderColor='#2563eb'"
                                onmouseout="this.style.borderColor='{{ $isActive ? '#2563eb' : '#e5e5e5' }}'">
                                <div>
                                    <div style="font-size:12px;font-weight:600;color:#111827;">{{ $w->desa }}</div>
                                    <div style="font-size:10px;color:#666;margin-top:1px;">Kec. {{ $w->kecamatan }}</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:10px;font-weight:600;padding:2px 6px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeText }};">
                                        {{ $count }} / {{ $target }}
                                    </span>
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $dotColor }};display:inline-block;"></span>
                                </div>
                            </button>
                        @empty
                            <div style="padding:20px;text-align:center;color:#9ca3af;font-size:11px;border:1px dashed #d4d4d8;border-radius:8px;">
                                Tidak ada kelurahan/desa yang sesuai filter.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Row 2: 2-column main grid for Database Table and lists -->
            <div style="display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px;padding:0 20px 20px;" class="kaderisasi-main-grid">
                <div style="display:grid;gap:12px;align-content:start;">
                    <!-- Database Kader Table -->
                    <div id="database-kader-section" style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:14px;padding-bottom:10px;border-bottom:0.5px solid #f1f5f9;">
                            <div>
                                <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Database Kader</div>
                                <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Daftar kader PKS Kabupaten Bekasi</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="position:relative;">
                                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kader..." style="width:200px;padding:6px 10px 6px 30px;border:0.5px solid #d4d4d8;border-radius:8px;font-size:12px;background:white;color:#1f2937;">
                                    <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;color:#a1a1aa;"></i>
                                </div>
                                <div style="font-size:11px;color:#6b7280;font-weight:500;background:#f4f4f5;padding:4px 8px;border-radius:6px;border:0.5px solid #e4e4e7;">
                                    {{ number_format($this->kaderList->total()) }} kader
                                </div>
                            </div>
                        </div>

                        <div style="overflow:auto;">
                            <table style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:0.5px solid #e5e5e5;">
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Kader</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Wilayah</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Peran</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->kaderList as $kader)
                                        @php
                                            $waNumber = $kader->no_wa ?: $kader->no_hp;
                                            if ($waNumber) {
                                                $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
                                                if (str_starts_with($waNumber, '0')) {
                                                    $waNumber = '62' . substr($waNumber, 1);
                                                }
                                            }
                                        @endphp
                                        <tr style="border-bottom:0.5px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td style="padding:12px;">
                                                <div style="display:flex;align-items:center;gap:6px;">
                                                    <div style="font-size:12px;font-weight:600;color:#111827;">{{ $kader->nama }}</div>
                                                    @if ($waNumber)
                                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" style="color:#25d366;display:inline-flex;align-items:center;padding:2px;border-radius:4px;background:#e8fbe8;transition:transform 0.15s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Hubungi via WhatsApp ({{ $kader->no_wa ?: $kader->no_hp }})">
                                                            <i class="ti ti-brand-whatsapp" style="font-size:14px;font-weight:bold;"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ $kader->no_wa ?: $kader->no_hp ?: '-' }}</div>
                                            </td>
                                            <td style="padding:12px;font-size:11px;color:#4b5563;">
                                                <div><strong>{{ $kader->desa ?: '-' }}</strong></div>
                                                <div style="font-size:10px;color:#6b7280;margin-top:2px;">
                                                    Kec. {{ $kader->kecamatan ?: '-' }}
                                                    @if ($kader->nomor_rw) · RW {{ $kader->nomor_rw }} @endif
                                                    @if ($kader->nomor_rt) / RT {{ $kader->nomor_rt }} @endif
                                                </div>
                                            </td>
                                            <td style="padding:12px;font-size:11px;color:#4b5563;">
                                                @if($kader->roles)
                                                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                                        @foreach($kader->roles as $role)
                                                            <span style="font-size:9px;font-weight:600;background:#eff6ff;color:#1d4ed8;padding:2px 6px;border-radius:4px;border:0.5px solid #dbeafe;">{{ $role }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span style="color:#9ca3af;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding:12px;">
                                                <div style="display:flex;gap:10px;align-items:center;">
                                                    <button type="button" wire:click="editKader('{{ $kader->id }}')" style="font-size:11px;font-weight:600;color:#2563eb;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:2px;transition:opacity 0.15s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="ti ti-edit" style="font-size:12px;"></i> Edit
                                                    </button>
                                                    @if ($kader->bisa_deploy && $kader->status === 'aktif')
                                                        <button type="button" wire:click="openDeployForm('{{ $kader->id }}')" style="font-size:11px;font-weight:600;color:#16a34a;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:2px;transition:opacity 0.15s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                            <i class="ti ti-arrows-exchange" style="font-size:12px;"></i> Mutasi
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="padding:30px;text-align:center;color:#9ca3af;font-size:12px;">Belum ada data kader.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:12px;">
                            {{ $this->kaderList->links('livewire::simple-tailwind') }}
                        </div>
                    </div>
                </div>

                <div style="display:grid;gap:12px;align-content:start;">

                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:#d97706;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Rekomendasi Mutasi</div>
                        <div style="display:grid;gap:8px;max-height:220px;overflow-y:auto;padding-right:4px;">
                            @forelse ($this->deployRecommendations as $recommendation)
                                <button type="button" 
                                    wire:click="selectVillage('{{ $recommendation['source']->target_wilayah_id }}')" 
                                    style="width:100%;text-align:left;border:0.5px solid #e5e5e5;border-radius:10px;padding:10px 12px;background:#fafafa;cursor:pointer;transition:all 0.15s ease-in-out;"
                                    onmouseover="this.style.borderColor='#d97706';this.style.background='#fffbeb';"
                                    onmouseout="this.style.borderColor='#e5e5e5';this.style.background='#fafafa';">
                                    <div style="font-size:11px;font-weight:700;color:#b45309;display:flex;align-items:center;gap:4px;margin-bottom:4px;">
                                        <i class="ti ti-arrows-exchange" style="font-size:14px;"></i>
                                        <span>Rekomendasi Mutasi</span>
                                    </div>
                                    <div style="font-size:12px;font-weight:600;color:#111827;line-height:1.4;">
                                        {{ $recommendation['source']->desa }} (RW {{ $recommendation['source']->nomor_rw }}):
                                    </div>
                                    <div style="font-size:11px;color:#4b5563;margin-top:2px;line-height:1.4;">
                                        Surplus <strong>{{ $recommendation['source']->total_kader }} kader</strong>. Dapat dimutasi ke <strong>{{ $recommendation['empty_rws']->count() }} RW kosong</strong>.
                                    </div>
                                    <div style="font-size:10px;color:#2563eb;font-weight:600;margin-top:6px;display:flex;align-items:center;gap:2px;">
                                        <span>🔍 Pilih & Tampilkan Kader</span>
                                    </div>
                                </button>
                            @empty
                                <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                    Belum ada rekomendasi mutasi.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if (false)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Coverage UPA</div>
                        <div style="display:grid;gap:8px;">
                            @foreach ($this->upaPerDapil as $row)
                                <div style="border:0.5px solid #e5e5e5;border-radius:12px;padding:10px 12px;background:#fafafa;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                        <div style="font-size:12px;font-weight:700;color:#111827;">{{ $row['dapil'] }}</div>
                                        <div style="font-size:11px;font-weight:700;color:#16a34a;">{{ $row['pct'] }}%</div>
                                    </div>
                                    <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin-top:8px;">
                                        <div style="height:100%;width:{{ $row['pct'] }}%;background:#16a34a;"></div>
                                    </div>
                                    <div style="font-size:10px;color:#71717a;margin-top:6px;">{{ $row['rw_upa'] }} dari {{ $row['total_rw'] }} RW sudah ada UPA</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Pelatihan Mendatang</div>
                        <div style="display:grid;gap:8px;">
                            @forelse ($this->pelatihanMendatang as $pelatihan)
                                <div style="border-left:3px solid #2563eb;padding:10px 12px;background:#fafafa;border-radius:0 10px 10px 0;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;">{{ $pelatihan->nama_pelatihan }}</div>
                                    <div style="font-size:10px;color:#71717a;margin-top:3px;">{{ $pelatihan->tanggal_mulai?->format('d M Y') }} · {{ \App\Models\Pelatihan::JENIS_OPTIONS[$pelatihan->jenis] ?? ucfirst($pelatihan->jenis) }}</div>
                                </div>
                            @empty
                                <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                    Belum ada jadwal pelatihan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
           @elseif ($activeTab === 'pembentukan_upa')
            @if (session()->has('error'))
                <div style="margin:14px 20px 0;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#dc2626;font-size:12px;border:0.5px solid #fecaca;">
                    {{ session('error') }}
                </div>
            @endif

            @if (empty($selectedDapil) || empty($selectedKecamatan) || empty($selectedDesa))
                <div style="margin:20px;padding:40px 20px;background:white;border:0.5px solid #e5e5e5;border-radius:12px;text-align:center;color:#64748b;">
                    <div style="width:48px;height:48px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="ti ti-map-pin" style="font-size:24px;"></i>
                    </div>
                    <div style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:6px;">Wilayah Belum Lengkap</div>
                    <div style="font-size:12px;color:#64748b;max-width:360px;margin:0 auto;line-height:1.5;">
                        Silakan tentukan filter <strong>Dapil</strong>, <strong>Kecamatan</strong>, dan <strong>Desa/Kelurahan</strong> pada filter atas terlebih dahulu untuk memulai pembentukan UPA.
                    </div>
                </div>
            @else
                <div style="display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px;padding:18px 20px 20px;" class="kaderisasi-main-grid">
                    <!-- Kolom Kiri: Form & Tabel Kandidat -->
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:18px;display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Pembentukan UPA Baru</div>
                            <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Pilih lokasi RW dan pilih kader untuk dijadikan anggota kelompok UPA.</div>
                        </div>

                        <!-- Info Wilayah Statis & Select RW -->
                        <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:10px;background:#f8fafc;padding:12px;border-radius:10px;border:0.5px solid #e2e8f0;">
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#64748b;margin-bottom:2px;text-transform:uppercase;">Dapil</label>
                                <div style="font-size:12px;font-weight:700;color:#1e293b;padding:4px 0;">{{ $selectedDapil }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#64748b;margin-bottom:2px;text-transform:uppercase;">Kecamatan</label>
                                <div style="font-size:12px;font-weight:700;color:#1e293b;padding:4px 0;">{{ $selectedKecamatan }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#64748b;margin-bottom:2px;text-transform:uppercase;">Desa</label>
                                <div style="font-size:12px;font-weight:700;color:#1e293b;padding:4px 0;">{{ $selectedDesa }}</div>
                            </div>
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#4b5563;margin-bottom:4px;">Nomor RW *</label>
                                <select wire:model.live="upaRw" style="width:100%;padding:6px 10px;border:0.5px solid #cbd5e1;border-radius:6px;font-size:12px;background:white;font-weight:600;color:#2563eb;">
                                    <option value="">Pilih RW</option>
                                    @foreach ($this->upaRwOptions as $rw)
                                        <option value="{{ $rw }}">{{ $rw }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if (empty($upaRw))
                            <div style="padding:40px 20px;text-align:center;color:#64748b;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:12px;margin-top:10px;">
                                <div style="width:48px;height:48px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                    <i class="ti ti-map-pin" style="font-size:24px;"></i>
                                </div>
                                <div style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Pilih Nomor RW</div>
                                <div style="font-size:11px;color:#64748b;max-width:320px;margin:0 auto;line-height:1.5;">
                                    Silakan tentukan Nomor RW untuk menampilkan daftar kandidat kader yang aktif di wilayah tersebut.
                                </div>
                            </div>
                        @else
                            <!-- Candidates Table -->
                            <div style="margin-top:10px;">
                                <div style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;">
                                    <span>Kandidat Kader Aktif (Belum UPA & Bukan Pelopor)</span>
                                    <span style="font-size:10px;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:999px;">{{ $this->upaCandidates->count() }} Kandidat</span>
                                </div>
                                <div style="overflow:auto;max-height:380px;border:0.5px solid #e2e8f0;border-radius:10px;">
                                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                        <thead>
                                            <tr style="background:#f8fafc;border-bottom:0.5px solid #e2e8f0;position:sticky;top:0;z-index:5;">
                                                <th style="padding:10px;text-align:left;width:40px;">Pilih</th>
                                                <th style="padding:10px;text-align:left;">Nama / Kontak</th>
                                                <th style="padding:10px;text-align:left;">Jenjang</th>
                                                <th style="padding:10px;text-align:left;">Sumber Data</th>
                                                <th style="padding:10px;text-align:left;width:150px;">Tugas Jabatan UPA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($this->upaCandidates as $key => $c)
                                                @php
                                                    $waNumber = $c['no_wa'] ?: $c['no_hp'];
                                                    if ($waNumber) {
                                                        $waNumber = preg_replace('/[^0-9]/', '', (string)$waNumber);
                                                        if (str_starts_with($waNumber, '0')) {
                                                            $waNumber = '62' . substr($waNumber, 1);
                                                        }
                                                    }
                                                @endphp
                                                <tr style="border-bottom:0.5px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <td style="padding:10px;text-align:center;">
                                                        <input type="checkbox" wire:model.live="upaSelections.{{ $key }}.selected" style="width:15px;height:15px;cursor:pointer;">
                                                    </td>
                                                    <td style="padding:10px;">
                                                        <div style="display:flex;align-items:center;gap:6px;">
                                                            <div style="font-weight:600;color:#1e293b;">{{ $c['nama'] }}</div>
                                                            @if ($waNumber)
                                                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" style="color:#25d366;display:inline-flex;" title="Hubungi via WhatsApp">
                                                                    <i class="ti ti-brand-whatsapp" style="font-size:12px;font-weight:bold;"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div style="font-size:10px;color:#64748b;margin-top:2px;">{{ $c['no_wa'] ?: $c['no_hp'] ?: '-' }}</div>
                                                    </td>
                                                    <td style="padding:10px;">
                                                        <span style="font-size:10px;font-weight:600;background:#eff6ff;color:#1d4ed8;padding:2px 6px;border-radius:4px;border:0.5px solid #dbeafe;">
                                                            {{ $c['jenjang'] }}
                                                        </span>
                                                    </td>
                                                    <td style="padding:10px;">
                                                        <span style="font-size:10px;color:#475569;font-weight:500;background:#f1f5f9;padding:2px 6px;border-radius:4px;border:0.5px solid #e2e8f0;">
                                                            {{ $c['source_label'] }}
                                                        </span>
                                                    </td>
                                                    <td style="padding:10px;">
                                                        <select wire:model="upaSelections.{{ $key }}.jabatan" style="width:100%;padding:4px 8px;border:0.5px solid #cbd5e1;border-radius:6px;font-size:11px;background:white;" @disabled(empty($upaSelections[$key]['selected'] ?? false))>
                                                            @foreach (\App\Models\UpaRwMember::JABATAN_OPTIONS as $roleKey => $label)
                                                                <option value="{{ $roleKey }}">{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" style="padding:30px;text-align:center;color:#64748b;">
                                                        Tidak ada kandidat kader di RW ini. Semua kader aktif di RW ini mungkin sudah terdaftar UPA atau berjenjang Pelopor.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($this->upaCandidates->isNotEmpty())
                                <div style="display:flex;justify-content:flex-end;margin-top:14px;border-top:0.5px solid #e2e8f0;padding-top:14px;">
                                    <button type="button" wire:click="bentukUpa" style="padding:8px 16px;border:none;border-radius:8px;background:#2563eb;color:white;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 1px 3px rgba(0,0,0,0.1);" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                        <i class="ti ti-plus" style="font-size:14px;"></i> Bentuk UPA Baru
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Kolom Kanan: UPA Terdaftar Info -->
                    <div style="display:grid;gap:12px;align-content:start;">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:18px;min-height:280px;display:flex;flex-direction:column;">
                            <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Kader UPA Terdaftar di RW Ini</div>
                            <div style="font-size:12px;color:#64748b;margin-top:2px;margin-bottom:12px;">Daftar kader yang sudah tergabung dalam UPA di RW terpilih.</div>

                            @if (empty($upaRw))
                                <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#94a3b8;padding:20px;">
                                    <i class="ti ti-users" style="font-size:32px;color:#cbd5e1;margin-bottom:8px;"></i>
                                    <span style="font-size:11px;">Pilih RW di kolom kiri untuk melihat anggota UPA yang sudah terbentuk.</span>
                                </div>
                            @else
                                <div style="flex:1;display:grid;gap:8px;max-height:420px;overflow-y:auto;padding-right:4px;">
                                    @forelse ($this->existingUpaMembers as $member)
                                        @php
                                            $waNumber = $member->no_wa ?: $member->no_hp;
                                            if ($waNumber) {
                                                $waNumber = preg_replace('/[^0-9]/', '', (string)$waNumber);
                                                if (str_starts_with($waNumber, '0')) {
                                                    $waNumber = '62' . substr($waNumber, 1);
                                                }
                                            }
                                        @endphp
                                        <div style="border:0.5px solid #e2e8f0;border-radius:10px;padding:10px 12px;background:#f8fafc;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                            <div>
                                                <div style="display:flex;align-items:center;gap:6px;">
                                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">{{ $member->nama }}</div>
                                                    @if ($waNumber)
                                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" style="color:#25d366;display:inline-flex;" title="Hubungi via WhatsApp">
                                                            <i class="ti ti-brand-whatsapp" style="font-size:12px;font-weight:bold;"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div style="font-size:10px;color:#64748b;margin-top:2px;">Jenjang: {{ $member->jenjang_config['label'] }}</div>
                                            </div>
                                            <div style="text-align:right;">
                                                <span style="font-size:10px;font-weight:700;background:#dcfce7;color:#166534;padding:3px 8px;border-radius:999px;border:0.5px solid #bbf7d0;">
                                                    {{ ucfirst($member->jabatan_upa ?: 'anggota') }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#94a3b8;padding:40px 20px;border:1px dashed #cbd5e1;border-radius:10px;">
                                            <i class="ti ti-users-minus" style="font-size:28px;color:#cbd5e1;margin-bottom:6px;"></i>
                                            <span style="font-weight:600;color:#475569;font-size:12px;margin-bottom:2px;">Belum Ada UPA RW</span>
                                            <span style="font-size:10px;color:#64748b;max-width:200px;line-height:1.4;">Belum ada kader di RW ini yang ditandai sebagai anggota/pengurus UPA.</span>
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @if ($showKaderForm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60;" wire:click="resetKaderForm"></div>
        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100vh;background:white;z-index:61;box-shadow:-10px 0 30px rgba(0,0,0,0.12);overflow-y:auto;">
            <div style="padding:18px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">{{ $kaderEditId ? 'Edit Kader' : 'Tambah Kader' }}</div>
                    <div style="font-size:11px;color:#71717a;margin-top:2px;">Data pribadi, lokasi, peran, dan keahlian kader</div>
                </div>
                <button type="button" wire:click="resetKaderForm" style="border:none;background:none;color:#71717a;font-size:14px;cursor:pointer;">✕</button>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;">Data pribadi</div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Nama</label>
                    <input type="text" wire:model="kNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    @error('kNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">HP</label>
                        <input type="text" wire:model="kHp" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">WA</label>
                        <input type="text" wire:model="kWa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">NIK</label>
                        <input type="text" wire:model="kNik" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">No KTA</label>
                        <input type="text" wire:model="kKta" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>

                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;">Jenjang & Lokasi</div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenjang</label>
                    <select wire:model="kJenjang" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        @foreach (\App\Models\Kader::JENJANG_OPTIONS as $key => $config)
                            <option value="{{ $key }}">{{ $config['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Dapil</label>
                        <select wire:model.live="kDapil" wire:key="k-dapil" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih dapil</option>
                            @foreach ($this->dapilOptions as $dapil)
                                <option value="{{ $dapil }}">{{ $dapil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kecamatan</label>
                        <select wire:model.live="kKecamatan" wire:key="k-kec" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih kecamatan</option>
                            @foreach ($this->formKecamatanOptions as $kecamatan)
                                <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;">
                    <div style="grid-column:span 2;">
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Desa</label>
                        <select wire:model.live="kDesa" wire:key="k-desa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->formDesaOptions as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RW</label>
                        <select wire:model.live="kRw" wire:key="k-rw" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">RW</option>
                            @foreach ($this->formRwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RT</label>
                        <select wire:model="kRt" wire:key="k-rt" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">RT</option>
                            @foreach ($this->formRtOptions as $rt)
                                <option value="{{ $rt }}">{{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;">Peran</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;color:#374151;">
                    <label><input type="checkbox" wire:model="kIsKorwe"> KORWE</label>
                    <label><input type="checkbox" wire:model="kIsKorte"> KORTE</label>
                    <label><input type="checkbox" wire:model="kIsUpa"> UPA</label>
                    <label><input type="checkbox" wire:model="kIsPenggalang"> Penggalang</label>
                    <label><input type="checkbox" wire:model="kIsSaksi"> Saksi TPS</label>
                </div>
                @if ($kIsUpa)
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jabatan UPA</label>
                        <select wire:model="kJabatanUpa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @foreach (\App\Models\UpaRwMember::JABATAN_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;">Keahlian</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;color:#374151;">
                    @foreach (\App\Models\Kader::KEAHLIAN_OPTIONS as $key => $label)
                        <label><input type="checkbox" wire:model="kKeahlian" value="{{ $key }}"> {{ $label }}</label>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <label style="font-size:12px;color:#374151;"><input type="checkbox" wire:model="kBisaDeploy"> Bisa mutasi</label>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Status</label>
                        <select wire:model="kStatus" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="pindah">Pindah</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetKaderForm" style="padding:10px 14px;border:0.5px solid #d4d4d4;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="simpanKader" style="padding:10px 14px;border:none;border-radius:10px;background:#2563eb;color:white;font-size:12px;font-weight:700;cursor:pointer;">Simpan</button>
            </div>
        </div>
    @endif

    @if ($showPelatihanForm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60;" wire:click="resetPelatihanForm"></div>
        <div style="position:fixed;top:0;right:0;width:420px;max-width:100%;height:100vh;background:white;z-index:61;box-shadow:-10px 0 30px rgba(0,0,0,0.12);overflow-y:auto;">
            <div style="padding:18px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">{{ $pelEditId ? 'Edit Pelatihan' : 'Buat Pelatihan Baru' }}</div>
                    <div style="font-size:11px;color:#71717a;margin-top:2px;">Atur target jenjang dan kapasitas peserta</div>
                </div>
                <button type="button" wire:click="resetPelatihanForm" style="border:none;background:none;color:#71717a;font-size:14px;cursor:pointer;">✕</button>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Nama pelatihan</label>
                    <input type="text" wire:model="pelNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    @error('pelNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenjang target</label>
                        <select wire:model="pelJenjangTarget" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @foreach (\App\Models\Kader::JENJANG_OPTIONS as $key => $config)
                                <option value="{{ $key }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenis</label>
                        <select wire:model="pelJenis" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @foreach (\App\Models\Pelatihan::JENIS_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Tanggal</label>
                        <input type="date" wire:model="pelTanggal" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        @error('pelTanggal') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kapasitas</label>
                        <input type="number" min="0" wire:model="pelKapasitas" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Lokasi</label>
                    <input type="text" wire:model="pelLokasi" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Instruktur</label>
                    <input type="text" wire:model="pelInstruktur" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetPelatihanForm" style="padding:10px 14px;border:0.5px solid #d4d4d4;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="simpanPelatihan" style="padding:10px 14px;border:none;border-radius:10px;background:#d97706;color:white;font-size:12px;font-weight:700;cursor:pointer;">Simpan</button>
            </div>
        </div>
    @endif

    @if ($showDeployForm)
        @php $kaderDeploy = \App\Models\Kader::find($deployKaderId); @endphp
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:70;"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100vw - 32px);background:white;border-radius:14px;z-index:71;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <div style="padding:18px;border-bottom:0.5px solid #e5e5e5;">
                <div style="font-size:15px;font-weight:700;color:#111827;">Mutasi Kader</div>
                <div style="font-size:11px;color:#71717a;margin-top:2px;">Pindahkan penugasan kader (mutasi) ke wilayah baru</div>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                @if ($kaderDeploy)
                    <div style="padding:10px 12px;border-radius:10px;background:#eff6ff;border:0.5px solid #bfdbfe;">
                        <div style="font-size:12px;font-weight:700;color:#111827;">{{ $kaderDeploy->nama }}</div>
                        <div style="font-size:10px;color:#475569;margin-top:3px;">{{ $kaderDeploy->jenjang_config['label'] }} · {{ $kaderDeploy->desa ?: '-' }} RW {{ $kaderDeploy->nomor_rw ?: '-' }}</div>
                    </div>
                @endif
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Dapil tujuan</label>
                        <select wire:model.live="deployKeDapil" wire:key="d-dapil" style="width:100%;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih dapil</option>
                            @foreach ($this->dapilOptions as $dapil)
                                <option value="{{ $dapil }}">{{ $dapil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kecamatan</label>
                        <select wire:model.live="deployKeKecamatan" wire:key="d-kec" style="width:100%;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih kecamatan</option>
                            @foreach ($this->deployKecamatanOptions as $kecamatan)
                                <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Desa</label>
                        <select wire:model.live="deployKeDesa" wire:key="d-desa" style="width:100%;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->deployDesaOptions as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RW</label>
                        <select wire:model="deployKeRw" wire:key="d-rw" style="width:100%;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="">Pilih RW</option>
                            @foreach ($this->deployRwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Alasan</label>
                    <select wire:model="deployAlasan" style="width:100%;padding:10px;border:0.5px solid #d4d4d8;border-radius:10px;font-size:12px;">
                        <option value="kebutuhan_wilayah">Kebutuhan wilayah</option>
                        <option value="pemerataan">Pemerataan</option>
                        <option value="permintaan">Permintaan</option>
                    </select>
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetDeployForm" style="padding:10px 14px;border:0.5px solid #d4d4d8;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="deployKader" style="padding:10px 14px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:700;cursor:pointer;">Mutasi</button>
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
                height: 200px !important;
                flex: none !important;
            }
        }

        @media (max-width: 1280px) {
            .kaderisasi-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .kaderisasi-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
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
            .kaderisasi-main-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 720px) {
            .kaderisasi-kpi-grid,
            .kaderisasi-summary-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
