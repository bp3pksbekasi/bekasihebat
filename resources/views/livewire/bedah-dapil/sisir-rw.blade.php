@php
    $summary = $this->summary;
    $monthLabel = !empty($selectedBulan) ? \Carbon\Carbon::create()->month((int) $selectedBulan)->translatedFormat('F') : 'Semua Bulan';
    $yearLabel = !empty($selectedTahun) ? $selectedTahun : 'Semua Tahun';
    $heatmap = $this->heatmapData;
    $timeline = $this->timeline;
    $timelineTabs = $this->timelineTabs;
    $timelineMeta = $this->timelineMeta;
    $rwBelumPage = $this->rwBelumTersisirPage;
    $rwBelum = $this->rwBelumTersisirFiltered;
    $rwBelumFilters = $this->rwBelumStatusFilters;
    $rwBelumTotal = $this->rwBelumTersisirAll->count();
    $user = auth()->user();
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div class="sisir-topbar" style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:10px;flex-wrap:wrap;">
            <div class="sisir-topbar-left" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti ti-walk" style="font-size:15px;color:white;" aria-hidden="true"></i>
                    </div>
                    <div style="font-weight:500;font-size:14px;line-height:1.2;">Sisir RW</div>
                </div>
                <div class="sisir-topbar-filters" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;flex:1 1 auto;min-width:0;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select class="sisir-filter-select sisir-filter-dapil" wire:model.live="selectedDapil" style="padding:7px 24px 7px 8px;border:0.5px solid #3f3f46;border-radius:6px;font-size:11px;background:#fff7f1;color:#993c1d;font-weight:500;min-width:108px;" @if(count($this->dapilOptions) === 1) disabled @endif>
                        @if (count($this->dapilOptions) !== 1)
                            <option value="">Semua dapil</option>
                        @endif
                        @foreach ($this->dapilOptions as $dapil)
                            <option value="{{ $dapil }}">{{ $dapil }}</option>
                        @endforeach
                    </select>
                    <select class="sisir-filter-select sisir-filter-kecamatan" wire:model.live="selectedKecamatan" style="padding:7px 24px 7px 8px;border:0.5px solid #3f3f46;border-radius:6px;font-size:11px;background:#27272a;color:#f4f4f5;min-width:132px;" @if(count($this->kecamatanOptions) === 1 && $user && !$user->isAdmin()) disabled @endif>
                        @if (count($this->kecamatanOptions) !== 1)
                            <option value="">Semua kecamatan</option>
                        @endif
                        @foreach ($this->kecamatanOptions as $kecamatan)
                            <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                        @endforeach
                    </select>
                    <select class="sisir-filter-select sisir-filter-desa" wire:model.live="selectedDesa" style="padding:7px 24px 7px 8px;border:0.5px solid #3f3f46;border-radius:6px;font-size:11px;background:#27272a;color:#f4f4f5;min-width:120px;" @if($user && $user->isDpra()) disabled @endif>
                        <option value="">Semua desa</option>
                        @foreach ($this->filterDesaOptions as $desa)
                            <option value="{{ $desa }}">{{ $desa }}</option>
                        @endforeach
                    </select>
                    <select class="sisir-filter-select sisir-filter-bulan" wire:model.live="selectedBulan" style="padding:7px 24px 7px 8px;border:0.5px solid #3f3f46;border-radius:6px;font-size:11px;background:#27272a;color:#f4f4f5;min-width:94px;">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $bulan)
                            <option value="{{ $bulan }}">{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select class="sisir-filter-select sisir-filter-tahun" wire:model.live="selectedTahun" style="padding:7px 24px 7px 8px;border:0.5px solid #3f3f46;border-radius:6px;font-size:11px;background:#27272a;color:#f4f4f5;min-width:74px;">
                        <option value="">Semua Tahun</option>
                        @foreach (range((int) now()->year - 1, (int) now()->year + 2) as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">
                {{ $user?->initials() }}
            </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
            @if (session('message'))
                <div style="margin:14px 20px 0;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Sisir RW</h1>
                    <div style="font-size:12px;color:#666;">Monitoring intensitas kegiatan lapangan per RW dan prioritas kunjungan berikutnya</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
                    <div style="font-size:11px;color:#888;">Periode aktif {{ $monthLabel }} {{ $yearLabel }} · fokus RW belum tersisir</div>
                    <button wire:click="openForm" type="button" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;background:#fe5000;color:white;font-weight:500;cursor:pointer;">
                        + Catat Kegiatan
                    </button>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Total RW</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['total_rw']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">RW aktif pada scope filter saat ini</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;color:rgba(255,255,255,0.82);font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RW Tersisir</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['rw_tersisir']) }}</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.84);margin-top:4px;">{{ number_format($summary['pct_tersisir']) }}% dari total RW</div>
                    <div style="margin-top:8px;height:6px;background:rgba(255,255,255,0.18);border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ $summary['pct_tersisir'] }}%;background:white;border-radius:999px;"></div>
                    </div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Kegiatan Bulan Ini</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['kegiatan_bulan_ini']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ $monthLabel }} {{ $yearLabel }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Warga Terjangkau</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['warga_terjangkau']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Akumulasi peserta atau warga hadir</div>
                </div>
                <div style="background:white;border:0.5px solid #fecaca;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RW Belum Tersisir</div>
                    <div style="font-size:26px;font-weight:500;color:#dc2626;margin-top:6px;">{{ number_format($summary['rw_belum']) }}</div>
                    <div style="font-size:11px;color:#ef4444;margin-top:4px;">Prioritas kunjungan berikutnya</div>
                </div>
            </div>

            <!-- DATA TABLE SISIR RW -->
            <div style="padding:0 20px 20px;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;overflow:hidden;">
                    <div style="padding:14px 16px;border-bottom:0.5px solid #e5e5e5;display:flex;justify-content:space-between;align-items:center;">
                        <h2 style="font-size:14px;font-weight:600;color:#1a1a1a;margin:0;">Data Sisir RW</h2>
                        <div style="font-size:11px;color:#888;">Menampilkan data sesuai filter</div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;text-align:left;font-size:12px;">
                            <thead>
                                <tr style="background:#f9fafb;border-bottom:0.5px solid #e5e5e5;">
                                    <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;">Waktu Kegiatan</th>
                                    <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;">Wilayah</th>
                                    <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;">Jenis Kegiatan</th>
                                    <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;">Pelaksana</th>
                                    <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;">Jumlah Warga</th>
                                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isDpd()))
                                        <th style="padding:10px 16px;font-weight:600;color:#4b5563;white-space:nowrap;text-align:right;">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->tableKegiatan as $kg)
                                    <tr style="border-bottom:0.5px solid #e5e5e5;">
                                        <td style="padding:10px 16px;white-space:nowrap;">
                                            <div style="color:#111827;font-weight:500;">{{ \Carbon\Carbon::parse($kg->tanggal_kegiatan)->translatedFormat('d F Y') }}</div>
                                        </td>
                                        <td style="padding:10px 16px;white-space:nowrap;">
                                            <div style="color:#111827;font-weight:500;">RW {{ $kg->nomor_rw }}</div>
                                            <div style="color:#6b7280;font-size:11px;">{{ $kg->desa ?? '-' }}, {{ $kg->kecamatan ?? '-' }}</div>
                                        </td>
                                        <td style="padding:10px 16px;color:#111827;max-width:250px;">
                                            <div style="font-weight:500;">{{ $kg->jenis_config['label'] }}</div>
                                            @if($kg->catatan)
                                                <div style="color:#6b7280;font-size:11px;margin-top:2px;white-space:normal;">{{ $kg->catatan }}</div>
                                            @endif
                                        </td>
                                        <td style="padding:10px 16px;color:#111827;max-width:250px;">
                                            <div style="font-weight:500;">{{ $kg->pelaksana }}</div>
                                            @php
                                                $dewanHadir = collect([$kg->dpr_ri_hadir, $kg->dprd_prov_hadir, $kg->dprd_kab_hadir])
                                                    ->filter(fn($d) => !empty($d) && strtoupper(trim($d)) !== 'TIDAK ADA')
                                                    ->implode(', ');
                                            @endphp
                                            @if($dewanHadir)
                                                <div style="color:#6b7280;font-size:11px;margin-top:2px;white-space:normal;">Dewan Hadir: {{ $dewanHadir }}</div>
                                            @endif
                                        </td>
                                        <td style="padding:10px 16px;white-space:nowrap;color:#111827;">{{ number_format($kg->jumlah_warga) }}</td>
                                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isDpd()))
                                            <td style="padding:10px 16px;white-space:nowrap;text-align:right;">
                                                <button wire:click="editKegiatan('{{ $kg->id }}')" style="padding:4px 8px;font-size:11px;color:#2563eb;background:#eff6ff;border:none;border-radius:4px;cursor:pointer;margin-right:4px;">Edit</button>
                                                <button wire:click="hapusKegiatan('{{ $kg->id }}')" wire:confirm="Yakin ingin menghapus data ini?" style="padding:4px 8px;font-size:11px;color:#dc2626;background:#fef2f2;border:none;border-radius:4px;cursor:pointer;">Hapus</button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding:24px 16px;text-align:center;color:#6b7280;">Tidak ada data kegiatan yang sesuai filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($this->tableKegiatan->hasPages())
                        <div style="padding:12px 16px;border-top:0.5px solid #e5e5e5;">
                            {{ $this->tableKegiatan->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 1: 3-column layout (Map, Selected Village Detail, List of Villages) -->
            <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
                <!-- Column 1: Map Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Peta Sebaran Kunjungan</div>
                    <div class="kaderisasi-map-wrapper" style="flex:1;min-height:0;background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ $this->mapImage }}" style="max-width:100%;max-height:100%;object-fit:contain;display:block;" alt="Peta Sebaran">
                        @foreach ($this->mapMarkers as $marker)
                            <button type="button" 
                                wire:click="selectVillage('{{ $marker['id'] }}')" 
                                title="{{ $marker['label'] }}"
                                style="position:absolute;left:{{ $marker['x'] }}%;top:{{ $marker['y'] }}%;transform:translate(-50%,-50%);width:{{ $marker['size'] }}px;height:{{ $marker['size'] }}px;border-radius:50%;border:2px solid white;background:{{ $marker['color'] }};box-shadow:0 3px 8px rgba(0,0,0,0.25);cursor:pointer;transition:transform 0.15s ease-in-out;z-index:10;"
                                onmouseover="this.style.transform='translate(-50%,-50%) scale(1.25)'"
                                onmouseout="this.style.transform='translate(-50%,-50%) scale(1)'">
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Column 2: Selected Village Detail -->
                @php $selectedVillage = $this->selectedVillageDetail; @endphp
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
                        <div style="background:#f8fafc;border:0.5px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:12px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;margin-bottom:4px;">
                                <span style="font-weight:600;color:#1e293b;">RW Tersisir (Bulan Ini)</span>
                                <span style="font-weight:700;color:#fe5000;">{{ $selectedVillage['rw_tersisir'] }} / {{ $selectedVillage['total_rw'] }} RW</span>
                            </div>
                            <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $selectedVillage['pct_tersisir'] }}%;background:#fe5000;"></div>
                            </div>
                        </div>

                        <!-- RW Heatmap Grid -->
                        <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">Peta Kunjungan RW</div>
                        <div style="flex:1;overflow-y:auto;max-height:260px;padding-right:4px;">
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach ($selectedVillage['rw_list'] as $rw)
                                    @php
                                        $count = $rw['kegiatan_count'];
                                        $boxStyle = match (true) {
                                            $count >= 3 => 'background:#16a34a;color:white;border:0.5px solid #15803d;',
                                            $count === 2 => 'background:#dcfce7;color:#166534;border:0.5px solid #bbf7d0;',
                                            $count === 1 => 'background:#fff7ed;color:#c2410c;border:0.5px solid #fed7aa;',
                                            default => 'background:#fef2f2;color:#991b1b;border:0.5px solid #fecaca;',
                                        };
                                    @endphp
                                    <button
                                        wire:click="openFormForRw('{{ $selectedVillage['id'] }}', '{{ $rw['nomor_rw'] }}')"
                                        type="button"
                                        title="RW {{ $rw['nomor_rw'] }} · {{ $count }}x kegiatan · DPT {{ number_format($rw['dpt']) }} · Est. PKS ~{{ number_format($rw['estimasi_pks']) }} · Status: {{ $rw['status'] }}"
                                        style="width:36px;height:36px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;{{ $boxStyle }}"
                                    >
                                        {{ ltrim($rw['nomor_rw'], '0') ?: '0' }}
                                    </button>
                                @endforeach
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
                            Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian intensitas kunjungan per RW.
                        </div>
                    </div>
                @endif

                <!-- Column 3: Daftar Kelurahan/Desa -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                    <div style="flex:1;overflow-y:auto;max-height:400px;display:grid;gap:8px;padding-right:4px;">
                        @forelse ($this->villageList as $v)
                            @php
                                $isActive = $selectedVillageId === $v['id'];
                                $rowStyle = $isActive 
                                    ? 'border:0.5px solid #fed7aa;background:#fff7f1;box-shadow:inset 3px 0 0 #fe5000;' 
                                    : 'border:0.5px solid #e5e7eb;background:#f9fafb;';
                            @endphp
                            <div 
                                wire:click="selectVillage('{{ $v['id'] }}')" 
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
                                        <div style="font-size:11px;font-weight:600;color:#fe5000;">{{ $v['rw_tersisir'] }} / {{ $v['total_rw'] }} RW</div>
                                        <div style="font-size:9px;color:#888;margin-top:2px;">{{ $v['pct_tersisir'] }}%</div>
                                    </div>
                                </div>
                                <div style="height:4px;background:#e5e7eb;border-radius:999px;margin-top:6px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $v['pct_tersisir'] }}%;background:#fe5000;border-radius:999px;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:11px;">Tidak ada data desa/kelurahan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Row 2: 2-column layout (RW Belum Tersisir, Kegiatan Terbaru) -->
            <div style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(340px,0.9fr);gap:14px;padding:0 20px 20px;align-items:start;" class="top-grid">
                <!-- RW Belum Tersisir Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div style="font-size:11px;color:#ef4444;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RW Belum Tersisir</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Daftar prioritas kunjungan lapangan</div>
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                @foreach ($rwBelumFilters as $filter)
                                    <button
                                        wire:click="setRwStatusFilter('{{ $filter['key'] }}')"
                                        type="button"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:0.5px solid {{ $filter['active'] ? $filter['border'] : '#e5e5e5' }};background:{{ $filter['active'] ? $filter['bg'] : 'white' }};color:{{ $filter['active'] ? $filter['text'] : '#666' }};font-size:11px;font-weight:500;cursor:pointer;"
                                    >
                                        @if ($filter['key'] !== '')
                                            <span style="width:7px;height:7px;border-radius:999px;background:{{ $filter['text'] }};display:inline-block;"></span>
                                        @endif
                                        <span>{{ $filter['label'] }}</span>
                                        <span style="font-size:10px;opacity:.8;">{{ number_format($filter['count']) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div style="font-size:11px;color:#888;">{{ number_format($rwBelumPage['total']) }} dari {{ number_format($rwBelumTotal) }} RW belum dikunjungi pada periode ini</div>
                    </div>

                    <div style="max-height:480px;overflow-y:auto;padding-right:4px;display:grid;gap:8px;">
                        @forelse ($rwBelumPage['items'] as $rw)
                            @php
                                $cfg = $rw['status_config'];
                                $lastVisit = $rw['last_visit_at'];
                                $isPriority = $rw['prioritas_urutan'] <= 2;
                            @endphp
                            <div style="display:grid;grid-template-columns:10px minmax(0,1fr) auto;gap:12px;align-items:center;border:0.5px solid {{ $isPriority ? '#bfdbfe' : '#e5e5e5' }};background:{{ $isPriority ? '#eff6ff' : 'white' }};border-radius:10px;padding:12px;margin-bottom:8px;">
                                <div style="width:10px;height:10px;border-radius:50%;background:{{ $cfg['warna'] }};"></div>
                                <div style="min-width:0;">
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                        <span style="font-size:12px;color:#1a1a1a;font-weight:500;">RW {{ $rw['nomor_rw'] }}</span>
                                        <span style="font-size:11px;color:#888;">{{ $rw['desa'] }}, {{ $rw['kecamatan'] }}</span>
                                        <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:500;background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">
                                            {{ $cfg['label'] }}
                                        </span>
                                        @if ($isPriority)
                                            <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:600;background:#ffedd5;color:#c2410c;">PRIORITAS</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:#666;margin-top:4px;line-height:1.5;">
                                        DPT {{ number_format($rw['dpt']) }} · Est. PKS ~{{ number_format($rw['estimasi_pks']) }} ·
                                        Terakhir:
                                        <span style="font-weight:500;color:{{ $lastVisit ? '#444' : '#dc2626' }};">
                                            {{ $lastVisit ? \Carbon\Carbon::parse($lastVisit)->diffForHumans() : 'belum pernah' }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    wire:click="openFormForRw('{{ $rw['target_wilayah_id'] }}', '{{ $rw['nomor_rw'] }}')"
                                    type="button"
                                    style="padding:7px 12px;border-radius:7px;border:{{ $isPriority ? 'none' : '0.5px solid #d4d4d8' }};background:{{ $isPriority ? '#ea580c' : 'white' }};color:{{ $isPriority ? 'white' : '#444' }};font-size:11px;font-weight:500;cursor:pointer;"
                                >
                                    Catat
                                </button>
                            </div>
                        @empty
                            <div style="border:0.5px dashed #d4d4d8;border-radius:10px;padding:28px 14px;text-align:center;font-size:12px;color:#888;background:#fafafa;">
                                Semua RW pada scope ini sudah tersisir bulan ini.
                            </div>
                        @endforelse
                    </div>

                    @if ($rwBelumPage['total'] > 0)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding-top:8px;border-top:0.5px solid #f0f0f0;margin-top:10px;">
                            <div style="font-size:11px;color:#888;">
                                Menampilkan {{ number_format($rwBelumPage['from']) }}-{{ number_format($rwBelumPage['to']) }} dari {{ number_format($rwBelumPage['total']) }} RW
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <button
                                    wire:click="prevRwBelumPage"
                                    type="button"
                                    @disabled(! $rwBelumPage['has_prev'])
                                    style="padding:6px 10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;font-weight:500;cursor:pointer;opacity:{{ $rwBelumPage['has_prev'] ? '1' : '.45' }};"
                                >
                                    Sebelumnya
                                </button>
                                <div style="font-size:11px;color:#666;">Halaman {{ $rwBelumPage['current_page'] }} / {{ $rwBelumPage['last_page'] }}</div>
                                <button
                                    wire:click="nextRwBelumPage"
                                    type="button"
                                    @disabled(! $rwBelumPage['has_next'])
                                    style="padding:6px 10px;border-radius:8px;border:none;background:#ea580c;color:white;font-size:11px;font-weight:500;cursor:pointer;opacity:{{ $rwBelumPage['has_next'] ? '1' : '.45' }};"
                                >
                                    Berikutnya
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Kegiatan Terbaru Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Kegiatan Terbaru</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">{{ $timelineMeta['title'] }}</div>
                            <div style="font-size:11px;color:#888;margin-top:4px;">{{ $timelineMeta['hint'] }}</div>
                        </div>
                        <div style="font-size:10px;color:#888;">Maksimal 6 item</div>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        @foreach ($timelineTabs as $tab)
                            <button
                                wire:click="setTimelineTab('{{ $tab['key'] }}')"
                                type="button"
                                style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;border:0.5px solid {{ $tab['active'] ? '#fdba74' : '#e5e7eb' }};background:{{ $tab['active'] ? '#fff7ed' : 'white' }};color:{{ $tab['active'] ? '#c2410c' : '#666' }};font-size:11px;font-weight:500;cursor:pointer;"
                            >
                                <span>{{ $tab['label'] }}</span>
                                <span style="font-size:10px;opacity:.8;">{{ number_format($tab['count']) }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div style="max-height:480px;overflow-y:auto;padding-right:4px;display:grid;gap:8px;">
                        @forelse ($timeline as $kegiatan)
                            @php
                                $cfg = $kegiatan->jenis_config;
                            @endphp
                            <div style="border:0.5px solid #ededed;border-radius:10px;background:#fcfcfc;padding:10px 12px;">
                                <div style="display:grid;grid-template-columns:minmax(92px,auto) minmax(0,1fr) auto;gap:12px;align-items:start;">
                                    <div style="min-width:0;">
                                        <div style="font-size:10px;color:#888;">{{ $kegiatan->tanggal_kegiatan->translatedFormat('d M Y') }}</div>
                                        <div style="font-size:11px;color:#444;font-weight:500;margin-top:2px;">{{ $kegiatan->tanggal_kegiatan->format('H:i') }}</div>
                                        <div style="font-size:10px;color:#9ca3af;margin-top:4px;">{{ $kegiatan->tanggal_kegiatan->diffForHumans() }}</div>
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                            <span style="width:8px;height:8px;border-radius:999px;background:{{ $cfg['color'] }};display:inline-block;"></span>
                                            <span style="font-size:12px;color:#1a1a1a;font-weight:600;">RW {{ $kegiatan->nomor_rw }}</span>
                                            <span style="padding:3px 7px;border-radius:999px;background:{{ $cfg['bg'] ?? '#fff7ed' }};color:{{ $cfg['color'] }};font-size:10px;font-weight:600;">{{ $cfg['label'] }}</span>
                                            @if ($kegiatan->event_id_linked)
                                                <span style="padding:3px 7px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:600;">Sudah Jadi Event</span>
                                            @endif
                                        </div>
                                        <div style="font-size:11px;color:#666;margin-top:4px;">{{ $kegiatan->desa }} · {{ $kegiatan->kecamatan }}</div>
                                        <div style="font-size:11px;color:#444;margin-top:6px;line-height:1.45;">{{ \Illuminate\Support\Str::limit($kegiatan->catatan ?: 'Catatan belum diisi.', 110) }}</div>
                                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:10px;color:#888;margin-top:7px;">
                                            <span style="display:flex;align-items:center;gap:4px;"><i class="ti ti-user" style="font-size:11px;" aria-hidden="true"></i>{{ $kegiatan->pelaksana }}</span>
                                            <span style="display:flex;align-items:center;gap:4px;"><i class="ti ti-users" style="font-size:11px;" aria-hidden="true"></i>{{ number_format($kegiatan->jumlah_warga) }} warga</span>
                                            @if ($kegiatan->foto)
                                                <span style="display:flex;align-items:center;gap:4px;"><i class="ti ti-photo" style="font-size:11px;" aria-hidden="true"></i>{{ count($kegiatan->foto) }} foto</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end;align-items:center;">
                                        <button wire:click="editKegiatan('{{ $kegiatan->id }}')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:10px;cursor:pointer;">
                                            Edit
                                        </button>
                                        @if (! $kegiatan->event_id_linked)
                                            <a href="{{ route('events.create', ['from_kegiatan' => $kegiatan->id]) }}" wire:navigate style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;border:0.5px solid #fed7aa;background:#fff7ed;color:#c2410c;font-size:10px;font-weight:500;text-decoration:none;">
                                                <i class="ti ti-calendar-plus" style="font-size:11px;" aria-hidden="true"></i>
                                                Jadikan Event
                                            </a>
                                        @else
                                            <a href="{{ route('events.detail', $kegiatan->event_id_linked) }}" wire:navigate style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;border:0.5px solid #bfdbfe;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:500;text-decoration:none;">
                                                <i class="ti ti-external-link" style="font-size:11px;" aria-hidden="true"></i>
                                                Lihat Event
                                            </a>
                                        @endif
                                        @if (filled($kegiatan->catatan) || filled($kegiatan->tindak_lanjut) || filled($kegiatan->tokoh_ditemui))
                                            <a
                                                href="{{ route('aspirasi.index', [
                                                    'source' => 'sisir_rw',
                                                    'source_id' => $kegiatan->id,
                                                    'dapil' => $kegiatan->dapil,
                                                    'kecamatan' => $kegiatan->kecamatan,
                                                    'desa' => $kegiatan->desa,
                                                    'rw' => $kegiatan->nomor_rw,
                                                    'pelapor' => \Illuminate\Support\Str::before((string) ($kegiatan->tokoh_ditemui ?? ''), ','),
                                                    'judul' => 'Aspirasi warga RW '.$kegiatan->nomor_rw.' '.$kegiatan->desa,
                                                    'deskripsi' => trim(collect([$kegiatan->catatan, $kegiatan->tindak_lanjut])->filter()->implode(' ')),
                                                ]) }}"
                                                wire:navigate
                                                style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;border:0.5px solid #bae6fd;background:#ecfeff;color:#0369a1;font-size:10px;font-weight:600;text-decoration:none;">
                                                <i class="ti ti-message-chatbot" style="font-size:11px;" aria-hidden="true"></i>
                                                Aspirasi
                                            </a>
                                        @endif
                                        <button wire:click="hapusKegiatan('{{ $kegiatan->id }}')" onclick="return confirm('Yakin ingin menghapus kegiatan ini?')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:10px;cursor:pointer;">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="border:0.5px dashed #d4d4d8;border-radius:10px;padding:28px 14px;text-align:center;font-size:12px;color:#888;background:#fafafa;">
                                {{ $timelineMeta['empty'] }}
                            </div>
                        @endforelse
                    </div>
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
            .sisir-topbar,
            .sisir-topbar-left,
            .sisir-topbar-filters {
                flex-wrap: nowrap !important;
            }
            .sisir-topbar-filters {
                gap: 6px !important;
            }
            .sisir-filter-select {
                min-width: 0 !important;
            }
            .sisir-filter-dapil {
                width: 104px;
            }
            .sisir-filter-kecamatan {
                width: 128px;
            }
            .sisir-filter-desa {
                width: 116px;
            }
            .sisir-filter-bulan {
                width: 92px;
            }
            .sisir-filter-tahun {
                width: 72px;
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
            .summary-grid,
            .top-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .summary-grid,
            .top-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>

    <livewire:kegiatan-rw.catat-form />
</div>
