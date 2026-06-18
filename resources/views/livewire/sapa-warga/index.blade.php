@php
    $summary = $this->summary;
    $villageList = $this->villageList;
    $selectedVillage = $this->selectedVillageDetail;
    $selectedTarget = $this->selectedTargetWilayah;
    $rwRows = $this->rwRows;
    $rwSummary = $this->rwSummary;
    $rtBadges = $this->rtBadges;
    $rwPenggalang = $this->rwPenggalang;
    $kontakRows = $this->kontakRows;
    $bulkPreview = $this->bulkPreview;
    $isKader = auth()->user()?->isKader() ?? false;
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti ti-address-book" style="font-size:16px;color:white;" aria-hidden="true"></i>
                    </div>
                    <div style="font-weight:500;font-size:14px;">Sapa Warga</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">{{ $isKader ? 'Scope :' : 'Filter :' }}</div>
                    @if ($isKader)
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:600;">{{ auth()->user()->dapil ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#27272a;color:#f4f4f5;">{{ auth()->user()->kecamatan ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#27272a;color:#f4f4f5;">{{ auth()->user()->desa ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#fef3c7;color:#92400e;font-weight:700;">RW {{ auth()->user()->nomor_rw ?: '-' }}</span>
                        </div>
                    @else
                        <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
                            <option value="">Semua dapil</option>
                            @foreach ($this->dapilOptions as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedKecamatan" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                            <option value="">Semua kecamatan</option>
                            @foreach ($this->kecamatanOptions as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedDesa" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                            <option value="">Semua desa</option>
                            @foreach ($this->desaOptions as $desaOption)
                                <option value="{{ $desaOption }}">{{ $desaOption }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="$set('selectedDapil', ''); $set('selectedKecamatan', ''); $set('selectedDesa', '')" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
                    @endif
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">SW</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Sapa Warga</h1>
                    <div style="font-size:12px;color:#666;">Database kontak warga per RW untuk distribusi dan tindak lanjut lapangan</div>
                </div>
                <div style="font-size:11px;color:#888;">Target standar {{ number_format(\App\Models\KontakWarga::TARGET_PER_RW) }} kontak per RW</div>
            </div>

            @if (session()->has('success'))
                <div style="padding:14px 20px 0;">
                    <div style="border:0.5px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:10px;padding:10px 12px;font-size:12px;">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- KPI Cards Summary Grid -->
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="sapa-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Total Kontak</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['total_kontak']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Kontak aktif tersimpan</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target Kontak</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_kontak']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">Akumulasi target semua RW</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Desa Tersedia</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['desa_count']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Sesuai filter dapil/kecamatan/desa</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RW Terisi</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['rw_terisi']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($summary['progress_pct']) }}% progress target</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Penggalang Aktif</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['penggalang_aktif']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Terkoneksi ke wilayah terfilter</div>
                </div>
            </div>

            <!-- Row 1: 3-column layout (Map, Selected Village Detail, List of Villages) -->
            <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
                <!-- Column 1: Map Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Peta Sebaran Sapa Warga</div>
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
                @if ($selectedVillage)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;">
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">{{ $selectedVillage['desa'] }}</h2>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Kec. {{ $selectedVillage['kecamatan'] }} · {{ $selectedVillage['dapil'] }}</div>
                            </div>
                            <button wire:click="closeVillageDetail" type="button" style="width:24px;height:24px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#666;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;">✕</button>
                        </div>

                        <!-- Progress Bar & Stats -->
                        <div style="background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:12px;display:grid;gap:6px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;">
                                <span style="font-weight:600;color:#1e293b;">Total Kontak (Desa)</span>
                                <span style="font-weight:700;color:#fe5000;">{{ number_format($selectedVillage['total_kontak']) }} / {{ number_format($selectedVillage['target_kontak']) }}</span>
                            </div>
                            <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $selectedVillage['pct_progress'] }}%;background:#fe5000;"></div>
                            </div>
                            <div style="font-size:10px;color:#6b7280;">
                                {{ number_format($selectedVillage['total_rw']) }} RW · {{ number_format($selectedVillage['total_rt']) }} RT
                            </div>
                        </div>

                        <!-- RW Heatmap Grid -->
                        <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Peta Sebaran RW</div>
                        <div style="flex:1;overflow-y:auto;max-height:260px;padding-right:4px;">
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach ($rwRows as $row)
                                    @php
                                        $count = (int) $row['kontak_count'];
                                        $bgColor = $count >= 200 ? '#dcfce7' : ($count >= 50 ? '#fff7f1' : ($count > 0 ? '#fef3c7' : 'transparent'));
                                        $borderColor = $selectedRw === $row['nomor_rw'] ? '#fe5000' : ($count >= 200 ? '#bbf7d0' : ($count >= 50 ? '#fed7aa' : ($count > 0 ? '#fde68a' : '#e5e7eb')));
                                    @endphp
                                    <button
                                        type="button"
                                        wire:key="rw-{{ $row['nomor_rw'] }}"
                                        wire:click="selectRw('{{ $row['nomor_rw'] }}')"
                                        title="RW {{ $row['nomor_rw'] }} · {{ $count }} kontak · {{ $row['jumlah_rt'] }} RT"
                                        style="width:36px;height:36px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s ease;background:{{ $bgColor }};border:{{ $selectedRw === $row['nomor_rw'] ? '2px' : '0.5px' }} solid {{ $borderColor }};color:#1a1a1a;"
                                    >
                                        {{ ltrim($row['nomor_rw'], '0') ?: '0' }}
                                    </button>
                                @endforeach
                            </div>
                            
                            <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:12px;font-size:9px;color:#888;">
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#dcfce7;border:0.5px solid #bbf7d0;"></span>&ge;200</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#fff7f1;border:0.5px solid #fed7aa;"></span>50-199</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#fef3c7;border:0.5px solid #fde68a;"></span>1-49</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;border:0.5px solid #e5e7eb;background:white;"></span>0</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#71717a;box-sizing:border-box;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#fff7f1;color:#fe5000;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="ti ti-map-pin" style="font-size:24px;"></i>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah</div>
                        <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                            Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian sebaran kontak per RW.
                        </div>
                    </div>
                @endif

                <!-- Column 3: Daftar Kelurahan/Desa -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                    <div style="flex:1;overflow-y:auto;display:grid;gap:8px;padding-right:4px;">
                        @forelse ($villageList as $v)
                            @php
                                $isActive = $selectedTargetWilayahId === $v['id'];
                                $rowStyle = $isActive 
                                    ? 'border:0.5px solid #fed7aa;background:#fff7f1;box-shadow:inset 3px 0 0 #fe5000;' 
                                    : 'border:0.5px solid #e5e7eb;background:#f9fafb;';
                            @endphp
                            <div 
                                wire:click="selectDesa('{{ $v['id'] }}')" 
                                style="border-radius:10px;padding:10px;cursor:pointer;transition:all 0.15s;{{ $rowStyle }}"
                                onmouseover="this.style.borderColor='#fed7aa'" 
                                onmouseout="this.style.borderColor='{{ $isActive ? '#fed7aa' : '#e5e7eb' }}'"
                            >
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                    <div style="min-width:0;flex:1;">
                                        <div style="font-size:12px;font-weight:600;color:#111827;">{{ $v['desa'] }}</div>
                                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">{{ $v['kecamatan'] }}</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:11px;font-weight:600;color:#fe5000;">{{ $v['rw_terisi'] }} / {{ $v['total_rw'] }} RW</div>
                                        <div style="font-size:9px;color:#888;margin-top:2px;">{{ $v['pct_terisi'] }}% terisi</div>
                                    </div>
                                </div>
                                <div style="height:4px;background:#e5e7eb;border-radius:999px;margin-top:6px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $v['pct_terisi'] }}%;background:#fe5000;border-radius:999px;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:11px;">Tidak ada data desa/kelurahan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Row 2: 2-column layout (Contact list on Left, Bulk Input & Penggalang list on Right) -->
            <div style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(320px,0.8fr);gap:14px;padding:0 20px 20px;align-items:start;" class="top-grid">
                <!-- Left Column: Daftar Kontak -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;box-sizing:border-box;">
                    @if ($selectedTarget && $selectedRw)
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
                            <div>
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Daftar Kontak RW {{ $selectedRw }}</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:600;margin-top:2px;">{{ $selectedTarget->desa }} · Kec. {{ $selectedTarget->kecamatan }}</div>
                                <div style="display:flex;align-items:baseline;gap:6px;margin-top:4px;">
                                    <span style="font-size:22px;font-weight:700;color:#1a1a1a;line-height:1;">{{ number_format($rwSummary['total_kontak']) }}</span>
                                    <span style="font-size:12px;color:#888;">/ {{ number_format($rwSummary['target_kontak']) }} target kontak</span>
                                    <span style="font-size:12px;font-weight:700;color:{{ $rwSummary['progress_pct'] >= 80 ? '#16a34a' : ($rwSummary['progress_pct'] >= 20 ? '#d97706' : '#fe5000') }}; margin-left:8px;">
                                        {{ number_format($rwSummary['progress_pct']) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div style="height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;margin-bottom:14px;">
                            <div style="height:100%;width:{{ min($rwSummary['progress_pct'], 100) }}%;background:{{ $rwSummary['progress_pct'] >= 80 ? '#16a34a' : ($rwSummary['progress_pct'] >= 20 ? '#d97706' : '#fe5000') }};border-radius:999px;"></div>
                        </div>

                        <!-- RT Sebaran Badges -->
                        <div style="margin-bottom:14px;">
                            <div style="font-size:10px;color:#888;margin-bottom:6px;font-weight:500;">Sebaran per RT:</div>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                @forelse ($rtBadges as $badge)
                                    <span style="font-size:9px;padding:3px 6px;border-radius:999px;font-weight:700;background:{{ $badge['bg'] }};color:{{ $badge['text'] }};">
                                        RT {{ $badge['rt'] }}: {{ number_format($badge['total']) }}
                                    </span>
                                @empty
                                    <span style="font-size:9px;color:#a1a1aa;">Belum ada data sebaran RT</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Search Box -->
                        <div style="padding:10px;background:#fafafa;border:0.5px solid #e5e5e5;border-radius:8px;margin-bottom:14px;">
                            <div style="display:flex;align-items:center;gap:8px;padding:4px 8px;background:white;border:0.5px solid #d1d5db;border-radius:6px;">
                                <i class="ti ti-search" style="font-size:12px;color:#a1a1aa;" aria-hidden="true"></i>
                                <input wire:model.live.debounce.300ms="detailSearch" type="text" placeholder="Cari kontak warga..." style="border:none;outline:none;font-size:12px;width:100%;background:transparent;color:#18181b;">
                                <span style="font-size:9px;color:#a1a1aa;white-space:nowrap;">Total: {{ $kontakRows->total() }}</span>
                            </div>
                        </div>

                        <!-- Contact List Table -->
                        <div style="overflow-x:auto; border:0.5px solid #e5e5e5; border-radius:8px; max-height:420px; overflow-y:auto;">
                            <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left;">
                                <thead style="background:#fafafa; position:sticky; top:0; z-index:5;">
                                    <tr style="border-bottom:0.5px solid #e5e5e5;">
                                        <th style="padding:8px 12px; font-weight:600; color:#525252; font-size:10px; text-transform:uppercase;">Nama</th>
                                        <th style="padding:8px 12px; font-weight:600; color:#525252; font-size:10px; text-transform:uppercase;">WhatsApp</th>
                                        <th style="padding:8px 12px; font-weight:600; color:#525252; font-size:10px; text-transform:uppercase; text-align:center;">RT</th>
                                        <th style="padding:8px 12px; font-weight:600; color:#525252; font-size:10px; text-transform:uppercase; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kontakRows as $kontak)
                                        <tr style="border-bottom:0.5px solid #f1f5f9; background:{{ $loop->even ? '#fafafa' : 'white' }};">
                                            <td style="padding:8px 12px; font-weight:500; color:#1a1a1a;">
                                                <div style="display:flex; align-items:center; gap:6px;">
                                                    <i class="ti ti-user" style="font-size:12px; color:#6b7280;"></i>
                                                    {{ $kontak->nama }}
                                                </div>
                                            </td>
                                            <td style="padding:8px 12px; color:#525252;">
                                                @if ($kontak->no_wa)
                                                    <a href="https://wa.me/{{ $kontak->no_wa }}" target="_blank" style="color:#25d366; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                                        <i class="ti ti-brand-whatsapp" style="font-size:13px;"></i>
                                                        {{ $kontak->no_wa }}
                                                    </a>
                                                @else
                                                    <span style="color:#a1a1aa;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding:8px 12px; text-align:center;">
                                                @if ($kontak->rt)
                                                    <span style="font-size:9px; padding:2px 6px; border-radius:999px; background:#f4f4f5; color:#71717a; font-weight:600;">RT {{ $kontak->rt }}</span>
                                                @else
                                                    <span style="color:#a1a1aa;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding:8px 12px; text-align:center;">
                                                <button
                                                    type="button"
                                                    wire:click="deactivateContact('{{ $kontak->id }}')"
                                                    onclick="return confirm('Apakah Anda yakin ingin menonaktifkan kontak warga ini?')"
                                                    style="border:none; background:none; color:#dc2626; cursor:pointer; padding:4px;"
                                                    title="Hapus / Nonaktifkan Kontak"
                                                >
                                                    <i class="ti ti-trash" style="font-size:13px;" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="padding:24px; text-align:center; color:#9ca3af;">Belum ada data kontak untuk RW ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($kontakRows->hasPages())
                            <div style="margin-top:10px;">
                                {{ $kontakRows->links('livewire::simple-tailwind') }}
                            </div>
                        @endif
                    @else
                        <div style="padding:40px; text-align:center; color:#9ca3af;">
                            <i class="ti ti-address-book" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            Pilih kelurahan/desa dan RW di atas terlebih dahulu untuk menampilkan daftar kontak warga.
                        </div>
                    @endif
                </div>

                <!-- Right Column: Bulk Input & Penggalang -->
                <div style="display:grid; gap:14px;">
                    <!-- Input Section -->
                    @if ($selectedTarget && $selectedRw)
                        <div style="background:white; border:0.5px solid #e5e5e5; border-radius:12px; padding:14px; box-sizing:border-box;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                                <div style="font-size:11px; color:#fe5000; font-weight:600; letter-spacing:0.8px; text-transform:uppercase;">Bulk Input Kontak</div>
                                <span style="font-size:9px; color:#888;">RW {{ $selectedRw }} · {{ $selectedTarget->desa }}</span>
                            </div>

                            <div style="background:#fff7f1; border:0.5px solid #fed7aa; border-radius:8px; padding:10px; margin-bottom:12px;">
                                <div style="font-size:10px; color:#9a3412; font-weight:500; line-height:1.4;">
                                    Format: <strong>Nama, No WhatsApp</strong> per baris.<br>
                                    Contoh:<br>
                                    <em>Ahmad Fauzi, 08123456789</em><br>
                                    <em>Siti Nurhaliza, 08567891234</em>
                                </div>
                            </div>

                            <textarea
                                wire:model.live.debounce.500ms="bulkText"
                                rows="5"
                                placeholder="Ahmad Fauzi, 08123456789&#10;Siti Nurhaliza, 08567891234"
                                style="width:100%; padding:10px; border:0.5px solid #d1d5db; border-radius:8px; background:white; font-size:12px; color:#111827; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace; resize:vertical; outline:none; box-sizing:border-box;"
                                onfocus="this.style.borderColor='#fe5000'"
                                onblur="this.style.borderColor='#d1d5db'"
                            ></textarea>
                            @error('bulkText')
                                <div style="font-size:11px; color:#b91c1c; margin-top:4px;">{{ $message }}</div>
                            @enderror

                            <div style="margin-top:10px; display:grid; gap:8px;">
                                <textarea
                                    wire:model.live="bulkCatatan"
                                    rows="2"
                                    placeholder="Catatan tambahan (sumber, agenda, dll) - Opsional"
                                    style="width:100%; padding:8px 10px; border:0.5px solid #d1d5db; border-radius:8px; background:white; font-size:12px; color:#111827; resize:vertical; outline:none; box-sizing:border-box;"
                                    onfocus="this.style.borderColor='#fe5000'"
                                    onblur="this.style.borderColor='#d1d5db'"
                                ></textarea>
                            </div>

                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; margin-top:10px;">
                                <span style="font-size:10px; color:#666;">
                                    <strong>{{ number_format($bulkPreview['ready_to_save']) }}</strong> kontak valid siap disimpan.
                                </span>
                                <button
                                    type="button"
                                    wire:click="saveBulk"
                                    @if ($bulkPreview['ready_to_save'] === 0) disabled style="opacity:0.5; background:#cbd5e1; color:#94a3b8; cursor:not-allowed;" @endif
                                    style="padding:6px 12px; border:none; border-radius:6px; background:#fe5000; color:white; font-size:11px; font-weight:700; cursor:pointer; transition:all 0.15s;"
                                    onmouseover="this.style.background='#d94400'"
                                    onmouseout="this.style.background='#fe5000'"
                                >
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <!-- Active Penggalang in RW -->
                        <div style="background:white; border:0.5px solid #e5e5e5; border-radius:12px; padding:14px; box-sizing:border-box;">
                            <div style="font-size:11px; color:#fe5000; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; margin-bottom:12px;">
                                Penggalang Suara Aktif
                            </div>
                            
                            <div style="display:grid; gap:8px; max-height:220px; overflow-y:auto; padding-right:2px;">
                                @forelse ($rwPenggalang as $pg)
                                    <div style="padding:10px; background:#f8fafc; border:0.5px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                        <div style="min-width:0; flex:1;">
                                            <div style="font-size:12px; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $pg->nama }}</div>
                                            <div style="font-size:10px; color:#64748b; margin-top:2px;">Target: {{ number_format($pg->target_suara) }} suara</div>
                                        </div>
                                        @if ($pg->no_wa)
                                            <a href="https://wa.me/{{ $pg->no_wa }}" target="_blank" style="width:26px; height:26px; border-radius:50%; background:#25d366; color:white; display:flex; align-items:center; justify-content:center; text-decoration:none;" title="Chat WhatsApp">
                                                <i class="ti ti-brand-whatsapp" style="font-size:14px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <div style="text-align:center; padding:20px; color:#9ca3af; font-size:11px; border:1px dashed #e2e8f0; border-radius:8px;">
                                        Belum ada penggalang terdaftar di RW ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                height: 390px !important;
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

        @media (max-width: 1200px) {
            .sapa-summary-grid,
            .top-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 768px) {
            .sapa-summary-grid,
            .top-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
