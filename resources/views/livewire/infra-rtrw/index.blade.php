<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    @php
        $summary = $this->summaryData;
        $milestones = $this->milestoneData;
        $dapilProgress = $this->dapilProgressData;
        $desaData = $this->desaData;
        $desaGroups = $desaData->getCollection()->groupBy('kecamatan');
    @endphp

    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20 7V12C20 17 16.5 20.74 12 22C7.5 20.74 4 17 4 12V7L12 3Z" stroke="white" stroke-width="1.5"/>
                            <path d="M12 7V17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M7 12H17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div style="font-weight:500;font-size:14px;">Infrastruktur</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;" @if ($this->accessScope['is_dapil'] ?? false) disabled @endif>
                        <option value="">Semua dapil</option>
                        @foreach ($this->getDapilOptions() as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedKecamatan" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;" @if (($this->accessScope['kecamatan'] ?? '') !== '') disabled @endif>
                        <option value="">Semua kecamatan</option>
                        @foreach ($this->getKecamatanOptions() as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterDesa" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;" @if (auth()->user()?->isDpra()) disabled @endif>
                        <option value="">Semua desa/kelurahan</option>
                        @foreach ($this->getDesaOptions() as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedTahun" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        @foreach ([2026, 2027, 2028, 2029] as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari desa..." style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;width:160px;background:#27272a;color:#f4f4f5;">
                    <button type="button" wire:click="resetFilters" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
                    <button wire:click="export" type="button" style="padding:5px 12px;border-radius:6px;font-size:12px;background:#fe5000;color:white;border:none;cursor:pointer;">Export</button>
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">RW</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:clip;">
            
            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Dashboard Infrastruktur</h1>
                    <div style="font-size:12px;color:#666;">Tracking pembentukan Koordinator RW (KORWE), Koordinator RT (KORTE) &amp; Penggalang Suara</div>
                </div>
                <div>
                    @if ($selectedVillageId)
                        <button type="button" 
                            @if ($activeTab === 'penggalang')
                                wire:click="openPenggalangForm"
                            @else
                                wire:click="openCreateForm"
                            @endif
                            style="padding:8px 16px;border-radius:8px;background:linear-gradient(135deg,#fe5000,#d94400);color:white;border:none;cursor:pointer;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 4px rgba(254,80,0,0.25);transition:all 0.15s ease-in-out;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            @if ($activeTab === 'korwe')
                                <i class="ti ti-user-shield" style="font-size:14px;"></i> Input KORWE
                            @elseif ($activeTab === 'korte')
                                <i class="ti ti-users" style="font-size:14px;"></i> Input KORTE
                            @elseif ($activeTab === 'penggalang')
                                <i class="ti ti-speakerphone" style="font-size:14px;"></i> Input Penggalang
                            @endif
                        </button>
                    @else
                        <button type="button" onclick="alert('Silakan pilih kelurahan/desa terlebih dahulu di peta atau daftar di bawah.')" style="padding:8px 16px;border-radius:8px;background:#e5e7eb;color:#9ca3af;border:0.5px solid #d1d5db;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                            @if ($activeTab === 'korwe')
                                <i class="ti ti-user-shield" style="font-size:14px;"></i> Input KORWE
                            @elseif ($activeTab === 'korte')
                                <i class="ti ti-users" style="font-size:14px;"></i> Input KORTE
                            @elseif ($activeTab === 'penggalang')
                                <i class="ti ti-speakerphone" style="font-size:14px;"></i> Input Penggalang
                            @endif
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tab Switcher (KORWE vs KORTE vs PENGGALANG) -->
            <div style="padding:14px 20px 0;position:sticky;top:0;z-index:30;background:white;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:#f4f4f5;border:0.5px solid #e4e4e7;">
                    <button type="button" wire:click="setActiveTab('korwe')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $activeTab === 'korwe' ? '#fed7aa' : 'transparent' }};color:{{ $activeTab === 'korwe' ? '#c2410c' : '#71717a' }};">Peta Sebaran KORWE</button>
                    <button type="button" wire:click="setActiveTab('korte')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $activeTab === 'korte' ? '#fed7aa' : 'transparent' }};color:{{ $activeTab === 'korte' ? '#c2410c' : '#71717a' }};">Peta Sebaran KORTE</button>
                    <button type="button" wire:click="setActiveTab('penggalang')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $activeTab === 'penggalang' ? '#fed7aa' : 'transparent' }};color:{{ $activeTab === 'penggalang' ? '#c2410c' : '#71717a' }};">Peta Sebaran Penggalang</button>
                </div>
                <div style="font-size:11px;color:#888;">
                    Fokus: <span style="color:#fe5000;font-weight:600;">{{ $selectedDapil !== '' ? $selectedDapil : 'Semua Dapil' }}</span>
                </div>
            </div>

            <!-- Summary KPI Grid -->
            <div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="infra-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Total RW</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['total_rw']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($summary['total_rt']) }} RT · {{ number_format($summary['total_desa']) }} desa</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target KORWE {{ $selectedTahun }}</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_korwe']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">{{ number_format($summary['persen_korwe'], 1) }}% dari RW · terbentuk {{ number_format($summary['korwe_terbentuk']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target KORTE {{ $selectedTahun }}</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_korte']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">{{ number_format($summary['persen_korte'], 1) }}% dari RT · terbentuk {{ number_format($summary['korte_terbentuk']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target Penggalang</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_penggalang']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">{{ number_format($summary['persen_penggalang'], 1) }}% dari target · aktif {{ number_format($summary['penggalang_terbentuk']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Target Suara 2029</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['target_suara']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ ucfirst($summary['growth_direction']) }} {{ number_format($summary['growth_percent_abs'], 1) }}% dari {{ number_format($summary['suara_2024']) }}</div>
                </div>
                @php
                    $profilPct = $summary['total_rw_all'] > 0 ? round(($summary['profil_terisi'] / $summary['total_rw_all']) * 100) : 0;
                @endphp
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Profil RW</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['profil_terisi']) }} / {{ number_format($summary['total_rw_all']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($summary['profil_lengkap']) }} lengkap · {{ number_format($profilPct) }}% terisi</div>
                    <div style="margin-top:8px;height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ $profilPct }}%;background:#3b82f6;border-radius:999px;"></div>
                    </div>
                </div>
            </div>

            <!-- Row 1: 3-column layout (Map, Selected Village Detail, List of Villages) -->
            <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
                <!-- Column 1: Map Card -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Peta Sebaran {{ strtoupper($activeTab) }}</div>
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
                    
                    <!-- Roadmap Target Bertahap -->
                    <div style="margin-top:12px;display:grid;grid-template-columns:repeat(4,1fr);gap:6px;">
                        @foreach ([2026, 2027, 2028, 2029] as $yr)
                            @php
                                $targetVal = $milestones[$activeTab . '_' . $yr] ?? 0;
                                $pctVal = $milestones[$activeTab . '_pct_' . $yr] ?? 0;
                                $isCurrentYr = $yr === $selectedTahun;
                            @endphp
                            <div style="border:0.5px solid {{ $isCurrentYr ? '#fed7aa' : '#e5e5e5' }};background:{{ $isCurrentYr ? '#fff7f1' : '#fafafa' }};border-radius:8px;padding:6px 4px;text-align:center;">
                                <div style="font-size:9px;color:#888;font-weight:600;">{{ $yr }}</div>
                                <div style="font-size:14px;font-weight:700;color:#1a1a1a;margin-top:2px;">{{ number_format($targetVal) }}</div>
                                <div style="font-size:9px;color:#fe5000;font-weight:600;margin-top:1px;">{{ number_format($pctVal, 1) }}%</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Column 2: Selected Village Detail -->
                @php $selectedVillage = $this->selectedVillageDetail; @endphp
                @if ($selectedVillage)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Detail Wilayah</div>
                            <button type="button" wire:click="closeVillageDetail" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:14px;">✕</button>
                        </div>
                        
                        <div style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:12px;padding-right:4px;">
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">{{ $selectedVillage['desa'] }}</h2>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Kec. {{ $selectedVillage['kecamatan'] }} · {{ $selectedVillage['dapil'] }}</div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;background:#f8fafc;padding:8px 10px;border-radius:8px;border:0.5px solid #e2e8f0;font-size:11px;">
                                <div>
                                    <span style="color:#64748b;">RW / RT</span>
                                    <div style="font-weight:700;color:#1e293b;margin-top:2px;">{{ $selectedVillage['jumlah_rw'] }} RW / {{ $selectedVillage['jumlah_rt'] }} RT</div>
                                </div>
                                <div>
                                    <span style="color:#64748b;">TPS / DPT</span>
                                    <div style="font-weight:700;color:#1e293b;margin-top:2px;">{{ $selectedVillage['jumlah_tps'] }} / {{ number_format($selectedVillage['jumlah_dpt']) }}</div>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:11px;">
                                <div style="border:0.5px solid #e5e5e5;padding:8px 10px;border-radius:8px;">
                                    <span style="color:#666;">Suara PKS 2024</span>
                                    <div style="font-size:14px;font-weight:700;color:#111827;margin-top:2px;">{{ number_format($selectedVillage['suara_pks_2024']) }}</div>
                                </div>
                                <div style="border:0.5px solid #e5e5e5;padding:8px 10px;border-radius:8px;">
                                    <span style="color:#666;">Target Suara 2029</span>
                                    <div style="font-size:14px;font-weight:700;color:#fe5000;margin-top:2px;">{{ number_format($selectedVillage['target_suara_2029']) }}</div>
                                </div>
                            </div>

                            <!-- KORWE / KORTE Targets & Actuals -->
                            <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:8px 10px;display:grid;gap:6px;font-size:11px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-weight:600;color:#111827;">Status KORWE ({{ $selectedTahun }})</span>
                                    <span style="font-weight:700;color:#fe5000;">{{ $selectedVillage['korwe_formed'] }} / {{ $selectedVillage['korwe_target'] }}</span>
                                </div>
                                <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $selectedVillage['korwe_target'] > 0 ? min(100, ($selectedVillage['korwe_formed'] / $selectedVillage['korwe_target']) * 100) : 0 }}%;background:#fe5000;"></div>
                                </div>
                            </div>

                            <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:8px 10px;display:grid;gap:6px;font-size:11px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-weight:600;color:#111827;">Status KORTE ({{ $selectedTahun }})</span>
                                    <span style="font-weight:700;color:#fe5000;">{{ $selectedVillage['korte_formed'] }} / {{ $selectedVillage['korte_target'] }}</span>
                                </div>
                                <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $selectedVillage['korte_target'] > 0 ? min(100, ($selectedVillage['korte_formed'] / $selectedVillage['korte_target']) * 100) : 0 }}%;background:#fe5000;"></div>
                                </div>
                            </div>

                            <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:8px 10px;display:grid;gap:6px;font-size:11px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-weight:600;color:#111827;">Status Penggalang</span>
                                    <span style="font-weight:700;color:#fe5000;">{{ $selectedVillage['penggalang_formed'] }} / {{ $selectedVillage['penggalang_target'] }}</span>
                                </div>
                                <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $selectedVillage['penggalang_target'] > 0 ? min(100, ($selectedVillage['penggalang_formed'] / $selectedVillage['penggalang_target']) * 100) : 0 }}%;background:#fe5000;"></div>
                                </div>
                            </div>

                            <div style="border:0.5px solid #cbd5e1;background:#eff6ff;border-radius:8px;padding:8px 10px;display:grid;gap:6px;font-size:11px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="font-weight:600;color:#1e3a8a;">Profil RW Terisi</span>
                                    <span style="font-weight:700;color:#2563eb;">{{ $selectedVillage['profil_terisi'] }} / {{ $selectedVillage['jumlah_rw'] }}</span>
                                </div>
                                <div style="height:6px;background:#dbeafe;border-radius:999px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $selectedVillage['profil_pct'] }}%;background:#2563eb;"></div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:10px;border-top:0.5px solid #e5e5e5;padding-top:10px;">
                            <a href="{{ route('infra-rtrw.detail', ['targetWilayah' => $selectedVillage['id'], 'tab' => $activeTab]) }}" wire:navigate style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:8px;background:#fe5000;color:white;text-decoration:none;font-size:12px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:background 0.15s;" onmouseover="this.style.background='#d94400'" onmouseout="this.style.background='#fe5000'">
                                <i class="ti ti-edit" style="font-size:14px;"></i> Input Detail Infra
                            </a>
                        </div>
                    </div>
                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;color:#71717a;box-sizing:border-box;">
                        <div style="width:48px;height:48px;border-radius:50%;background:#fff7f1;color:#fe5000;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="ti ti-map-pin" style="font-size:24px;"></i>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah</div>
                        <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                            Pilih desa/kelurahan dari peta sebaran atau daftar di samping untuk melihat rincian target, jumlah RT/RW, dan progress terbentuk.
                        </div>
                    </div>
                @endif

                <!-- Column 3: Daftar Kelurahan/Desa -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;">
                    <div style="font-size:10px;color:#fe5000;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Daftar Kelurahan/Desa</div>
                    <div style="flex:1;overflow-y:auto;display:grid;gap:6px;padding-right:4px;">
                        @php
                            $wilayahList = \App\Models\TargetWilayah::query()
                                ->when($selectedDapil !== '', fn ($q) => $q->where('dapil', $selectedDapil))
                                ->when($selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $selectedKecamatan))
                                ->orderBy('desa')
                                ->get();
                            
                            if ($activeTab === 'penggalang') {
                                $countsTable = 'penggalang_suaras';
                                $statusField = 'status';
                                $statusVal = 'aktif';
                                $targetColumn = 'target_penggalang_' . $selectedTahun;
                            } else {
                                $countsTable = $activeTab === 'korte' ? 'kortes' : 'korwes';
                                $statusField = 'status';
                                $statusVal = 'terbentuk';
                                $targetColumn = $activeTab === 'korte' ? 'target_korte_' . $selectedTahun : 'target_korwe_' . $selectedTahun;
                            }

                            $actualCounts = DB::table($countsTable)
                                ->where($statusField, $statusVal)
                                ->select('target_wilayah_id')
                                ->selectRaw('COUNT(*) as total_formed')
                                ->groupBy('target_wilayah_id')
                                ->pluck('total_formed', 'target_wilayah_id');
                        @endphp
                        
                        @forelse ($wilayahList as $w)
                            @php
                                $count = $actualCounts[$w->id] ?? 0;
                                $target = $w->{$targetColumn} ?? 0;
                                $isActive = $selectedVillageId === $w->id;
                                
                                if ($target > 0) {
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
                                } else {
                                    $dotColor = '#22c55e';
                                    $badgeBg = '#dcfce7';
                                    $badgeText = '#166534';
                                }
                            @endphp
                            <button type="button" 
                                wire:click="selectVillage('{{ $w->id }}')" 
                                style="width:100%;text-align:left;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border:0.5px solid {{ $isActive ? '#fe5000' : '#e5e5e5' }};border-radius:8px;background:{{ $isActive ? '#fff7f1' : 'white' }};cursor:pointer;transition:all 0.15s ease-in-out;"
                                onmouseover="this.style.borderColor='#fe5000'"
                                onmouseout="this.style.borderColor='{{ $isActive ? '#fe5000' : '#e5e5e5' }}'">
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

            <!-- Row 2: 2-column main grid (Tabel Detail Wilayah on Left, Progress per Dapil on Right) -->
            <div style="display:grid;grid-template-columns:minmax(0,3.1fr) minmax(340px,1.9fr);gap:14px;padding:0 20px 20px;" class="infra-main-grid">
                <!-- Kolom Kiri: Tabel Detail Wilayah -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;box-sizing:border-box;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Tabel Detail Wilayah</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Daftar desa dan progres pembentukan</div>
                        </div>
                    </div>

                    <div style="display:grid;gap:12px;">
                        @forelse ($desaGroups as $kecamatan => $rows)
                            @php
                                $totalRw = $rows->sum('jumlah_rw');
                                $totalRt = $rows->sum('jumlah_rt');
                                $totalKorweTarget = $rows->sum('target_korwe_' . $selectedTahun);
                                $totalKorteTarget = $rows->sum('target_korte_' . $selectedTahun);
                                $totalKorweTerbentuk = $rows->sum('korwes_terbentuk');
                                $totalKorteTerbentuk = $rows->sum('kortes_terbentuk');
                                $totalTargetPenggalang = $rows->sum('target_penggalang_' . $selectedTahun);
                                $totalPenggalangTerbentuk = $rows->sum('penggalangs_terbentuk');
                                $totalRwPrioritas = $rows->sum('rw_prioritas_count');
                                $totalProfilTerisi = $rows->sum('profil_terisi_count');
                                $totalSuara2024 = $rows->sum('suara_pks_2024');
                                $totalTarget2029 = $rows->sum('target_suara_2029');

                                if ($activeTab === 'penggalang') {
                                    $totalProgressTarget = $totalTargetPenggalang;
                                    $totalProgressDone = $totalPenggalangTerbentuk;
                                } elseif ($activeTab === 'korte') {
                                    $totalProgressTarget = $totalKorteTarget;
                                    $totalProgressDone = $totalKorteTerbentuk;
                                } else {
                                    $totalProgressTarget = $totalKorweTarget;
                                    $totalProgressDone = $totalKorweTerbentuk;
                                }
                                $totalProgressPercent = $totalProgressTarget > 0 ? min(100, round(($totalProgressDone / $totalProgressTarget) * 100, 1)) : 0;
                                $totalProfilPct = $totalRw > 0 ? round(($totalProfilTerisi / $totalRw) * 100) : 0;
                            @endphp

                            <details style="border:0.5px solid #e5e5e5;border-radius:12px;overflow:hidden;background:#fafafa;" open>
                                <summary style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;cursor:pointer;list-style:none;">
                                    <div>
                                        <div style="font-size:13px;font-weight:500;color:#1a1a1a;">{{ $kecamatan }}</div>
                                        <div style="font-size:11px;color:#888;margin-top:2px;">{{ $rows->count() }} desa · {{ number_format($totalRw) }} RW · {{ number_format($totalRt) }} RT</div>
                                    </div>
                                    <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.8px;">Buka / Tutup</div>
                                </summary>

                                <div style="overflow-x:auto;border-top:0.5px solid #e5e5e5;background:white;">
                                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                        <thead style="background:#fafafa;">
                                            <tr style="border-bottom:0.5px solid #e5e5e5;">
                                                <th style="text-align:left;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Kecamatan / Desa</th>
                                                <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">RW</th>
                                                <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">RT</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">RW Prioritas</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Profil</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORWE</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORTE</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Penggalang</th>
                                                <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">PKS 2024</th>
                                                <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Target 2029</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Progress</th>
                                                <th style="text-align:center;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="background:#fff7f1;border-bottom:0.5px solid #f1f5f9;">
                                                <td style="padding:10px 12px;font-weight:500;color:#1a1a1a;">Total {{ $kecamatan }}</td>
                                                <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($totalRw) }}</td>
                                                <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($totalRt) }}</td>
                                                <td style="padding:10px 12px;text-align:center;color:#525252;">
                                                    @if ($totalRwPrioritas > 0)
                                                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;border-radius:999px;background:#ffedd5;color:#c2410c;font-size:11px;font-weight:600;">{{ number_format($totalRwPrioritas) }}</span>
                                                    @else
                                                        <span style="font-size:11px;color:#d4d4d8;">-</span>
                                                    @endif
                                                </td>
                                                <td style="padding:10px 12px;text-align:center;">
                                                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                                        <div style="width:40px;height:6px;border-radius:999px;background:#e5e7eb;overflow:hidden;">
                                                            <div style="width:{{ $totalProfilPct }}%;height:100%;background:#3b82f6;border-radius:999px;"></div>
                                                        </div>
                                                        <span style="font-size:10px;color:{{ $totalProfilPct > 0 ? '#2563eb' : '#9ca3af' }};">{{ number_format($totalProfilTerisi) }}/{{ number_format($totalRw) }}</span>
                                                    </div>
                                                </td>
                                                <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $totalKorweTerformed = $totalKorweTerbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $totalKorweTerbentuk > 0 ? '500' : '400' }};">{{ number_format($totalKorweTerbentuk) }}</span> / {{ number_format($totalKorweTarget) }}</td>
                                                <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $totalKorteTerformed = $totalKorteTerbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $totalKorteTerbentuk > 0 ? '500' : '400' }};">{{ number_format($totalKorteTerbentuk) }}</span> / {{ number_format($totalKorteTarget) }}</td>
                                                <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $totalPenggalangTerformed = $totalPenggalangTerbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $totalPenggalangTerbentuk > 0 ? '500' : '400' }};">{{ number_format($totalPenggalangTerbentuk) }}</span> / {{ number_format($totalTargetPenggalang) }}</td>
                                                <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($totalSuara2024) }}</td>
                                                <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($totalTarget2029) }}</td>
                                                <td style="padding:10px 12px;text-align:center;">
                                                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                                        <div style="width:56px;height:6px;border-radius:999px;background:#f3f4f6;overflow:hidden;">
                                                            <div style="width:{{ $totalProgressPercent }}%;height:100%;background:#fe5000;border-radius:999px;"></div>
                                                        </div>
                                                        <span style="font-size:10px;color:#888;">{{ number_format($totalProgressPercent, 1) }}%</span>
                                                    </div>
                                                </td>
                                                <td style="padding:10px 12px;text-align:center;color:#aaa;">-</td>
                                            </tr>

                                            @foreach ($rows as $tw)
                                                @php
                                                    $korweTarget = (int) $tw->{'target_korwe_' . $selectedTahun};
                                                    $korteTarget = (int) $tw->{'target_korte_' . $selectedTahun};
                                                    $penggalangTarget = (int) $tw->{'target_penggalang_' . $selectedTahun};

                                                    if ($activeTab === 'penggalang') {
                                                        $progressTarget = $penggalangTarget;
                                                        $progressDone = (int) $tw->penggalangs_terbentuk;
                                                    } elseif ($activeTab === 'korte') {
                                                        $progressTarget = $korteTarget;
                                                        $progressDone = (int) $tw->kortes_terbentuk;
                                                    } else {
                                                        $progressTarget = $korweTarget;
                                                        $progressDone = (int) $tw->korwes_terbentuk;
                                                    }
                                                    $persen = $progressTarget > 0 ? min(100, round(($progressDone / $progressTarget) * 100, 1)) : 0;
                                                @endphp
                                                <tr style="border-bottom:0.5px solid #f1f5f9;">
                                                    <td style="padding:10px 12px;">
                                                        <div style="font-weight:500;color:#1a1a1a;">{{ $tw->desa }}</div>
                                                        <div style="font-size:11px;color:#888;margin-top:2px;">{{ $tw->dapil }}</div>
                                                    </td>
                                                    <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($tw->jumlah_rw) }}</td>
                                                    <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($tw->jumlah_rt) }}</td>
                                                    <td style="padding:10px 12px;text-align:center;">
                                                        @if ($tw->rw_prioritas_count > 0)
                                                            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;border-radius:999px;background:#ffedd5;color:#c2410c;font-size:11px;font-weight:600;">{{ number_format($tw->rw_prioritas_count) }}</span>
                                                        @else
                                                            <span style="font-size:11px;color:#d4d4d8;">-</span>
                                                        @endif
                                                    </td>
                                                    @php
                                                        $profilPct = $tw->jumlah_rw > 0 ? round(($tw->profil_terisi_count / $tw->jumlah_rw) * 100) : 0;
                                                    @endphp
                                                    <td style="padding:10px 12px;text-align:center;">
                                                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                                            <div style="width:40px;height:6px;border-radius:999px;background:#e5e7eb;overflow:hidden;">
                                                                <div style="width:{{ $profilPct }}%;height:100%;background:#3b82f6;border-radius:999px;"></div>
                                                            </div>
                                                            <span style="font-size:10px;color:{{ $profilPct > 0 ? '#2563eb' : '#9ca3af' }};">{{ number_format($tw->profil_terisi_count) }}/{{ number_format($tw->jumlah_rw) }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $tw->korwes_terbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $tw->korwes_terbentuk > 0 ? '500' : '400' }};">{{ number_format($tw->korwes_terbentuk) }}</span> / {{ number_format($korweTarget) }}</td>
                                                    <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $tw->kortes_terbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $tw->kortes_terbentuk > 0 ? '500' : '400' }};">{{ number_format($tw->kortes_terbentuk) }}</span> / {{ number_format($korteTarget) }}</td>
                                                    <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $tw->penggalangs_terbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $tw->penggalangs_terbentuk > 0 ? '500' : '400' }};">{{ number_format($tw->penggalangs_terbentuk) }}</span> / {{ number_format($penggalangTarget) }}</td>
                                                    <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($tw->suara_pks_2024) }}</td>
                                                    <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($tw->target_suara_2029) }}</td>
                                                    <td style="padding:10px 12px;text-align:center;">
                                                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                                            <div style="width:56px;height:6px;border-radius:999px;background:#f3f4f6;overflow:hidden;">
                                                                <div style="width:{{ $persen }}%;height:100%;background:#fe5000;border-radius:999px;"></div>
                                                            </div>
                                                            <span style="font-size:10px;color:#888;">{{ number_format($persen, 1) }}%</span>
                                                        </div>
                                                    </td>
                                                    <td style="padding:10px 12px;text-align:center;">
                                                        <a href="{{ route('infra-rtrw.detail', $tw) }}" wire:navigate style="display:inline-flex;align-items:center;padding:5px 10px;font-size:11px;border-radius:6px;border:0.5px solid #fe5000;color:#fe5000;text-decoration:none;font-weight:500;">
                                                            Input
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @empty
                            <div style="border:0.5px dashed #d4d4d8;border-radius:12px;padding:28px;text-align:center;font-size:12px;color:#888;">Tidak ada data desa untuk filter yang dipilih.</div>
                        @endforelse
                    </div>

                    <div style="border-top:0.5px solid #e5e5e5;padding-top:12px;margin-top:12px;">
                        {{ $desaData->links() }}
                    </div>
                </div>

                <!-- Kolom Kanan: Mapping Infrastruktur Wilayah -->
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;box-sizing:border-box;display:flex;flex-direction:column;min-height:450px;">
                    @if (!$selectedVillageId)
                        <!-- Placeholder jika belum ada desa terpilih -->
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#71717a;padding:24px;">
                            <div style="width:48px;height:48px;border-radius:50%;background:#fff7f1;color:#fe5000;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <i class="ti ti-map-pin" style="font-size:24px;"></i>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:6px;">Pilih Wilayah Terlebih Dahulu</div>
                            <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.5;">
                                Pilih kelurahan/desa dari peta sebaran atau daftar di atas untuk memantau dan menginput KORWE, KORTE, atau Penggalang secara langsung.
                            </div>
                        </div>
                    @else
                        <!-- List dinamis sesuai activeTab -->
                        @php
                            $selectedVillage = $this->selectedVillageDetail;
                        @endphp

                        @if ($activeTab === 'korwe')
                            <!-- KORWE MAPPING LIST -->
                            @php
                                $rwList = $this->filteredRwList;
                                $profilMap = $this->profilRwMap;
                                $rwStatusFilters = $this->rwStatusFilters;
                            @endphp
                            <div style="display:flex;flex-direction:column;height:100%;flex:1;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                    <div>
                                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Mapping RW ({{ $selectedVillage['desa'] }})</div>
                                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">{{ $this->rwList->count() }} RW · Urut berdasarkan prioritas</div>
                                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:8px;">
                                            @foreach ($rwStatusFilters as $filter)
                                                <button
                                                    wire:click="setRwStatusFilter('{{ $filter['key'] }}')"
                                                    type="button"
                                                    style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:999px;border:0.5px solid {{ $filter['active'] ? $filter['border'] : '#e5e5e5' }};background:{{ $filter['active'] ? $filter['bg'] : 'white' }};color:{{ $filter['active'] ? $filter['text'] : '#666' }};font-size:10px;font-weight:500;cursor:pointer;"
                                                >
                                                    @if ($filter['key'] !== '')
                                                        <span style="width:6px;height:6px;border-radius:999px;background:{{ $filter['text'] }};display:inline-block;"></span>
                                                    @endif
                                                    <span>{{ $filter['label'] }}</span>
                                                    <span style="font-size:9px;opacity:.8;">{{ number_format($filter['count']) }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div style="flex:1;overflow-y:auto;max-height:480px;display:grid;gap:8px;padding-right:4px;">
                                    @forelse ($rwList as $rw)
                                        @php
                                            $cfg = $rw->status_config;
                                            $korwe = $rw->korwe;
                                            $profil = $profilMap->get($rw->nomor_rw);
                                            $hasProfil = $profil && $profil->completion_percent > 0;
                                            $profilComplete = $profil && $profil->is_complete;
                                            $hasKorwe = $korwe && $korwe->status === 'terbentuk';
                                            $inProcess = $korwe && $korwe->status === 'proses';
                                            $accentColor = $hasKorwe ? '#16a34a' : ($inProcess ? '#f59e0b' : ($rw->prioritas_urutan <= 2 ? '#94a3b8' : '#d1d5db'));
                                            $rowStyle = 'border:0.5px solid #e5e7eb;background:#f9fafb;box-shadow:inset 3px 0 0 ' . $accentColor . ';';
                                        @endphp
                                        <div style="border-radius:10px;padding:10px 12px;{{ $rowStyle }}">
                                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                                <div>
                                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                                        <span style="font-size:12px;font-weight:600;color:#111827;">RW {{ $rw->nomor_rw }}</span>
                                                        <span style="padding:1px 6px;border-radius:999px;font-size:9px;font-weight:500;background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">{{ $cfg['label'] }}</span>
                                                        @if ($profilComplete)
                                                            <span style="padding:1px 6px;border-radius:999px;font-size:9px;font-weight:600;background:#dbeafe;color:#1d4ed8;">Profil ✓</span>
                                                        @endif
                                                    </div>
                                                    <div style="font-size:11px;color:#4b5563;margin-top:2px;">
                                                        @if ($hasKorwe)
                                                            <strong>{{ $korwe->nama_koordinator ?: 'Tanpa nama' }}</strong> ({{ $korwe->no_hp ?: '-' }})
                                                        @elseif ($inProcess)
                                                            <span style="color:#b45309;">Dalam proses: {{ $korwe->nama_koordinator ?: '-' }}</span>
                                                        @else
                                                            <span style="color:#9ca3af;">Belum ada koordinator</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div style="display:flex;gap:4px;">
                                                    @if ($hasKorwe)
                                                        <button wire:click="editKorwe('{{ $korwe->id }}')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:10px;font-weight:500;cursor:pointer;">Edit</button>
                                                        <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:10px;font-weight:500;cursor:pointer;">Profil</button>
                                                        <button wire:click="hapus('{{ $korwe->id }}')" onclick="return confirm('Yakin ingin menghapus KORWE ini?')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid #fecaca;background:#fef2f2;color:#b91c1c;font-size:10px;font-weight:500;cursor:pointer;">Hapus</button>
                                                    @elseif ($inProcess)
                                                        <button wire:click="editKorwe('{{ $korwe->id }}')" type="button" style="padding:4px 8px;border-radius:6px;border:none;background:#fe5000;color:white;font-size:10px;font-weight:500;cursor:pointer;">Edit</button>
                                                        <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:10px;font-weight:500;cursor:pointer;">Profil</button>
                                                    @else
                                                        <button wire:click="assignKorwe('{{ $rw->nomor_rw }}')" type="button" style="padding:4px 8px;border-radius:6px;border:none;background:#fe5000;color:white;font-size:10px;font-weight:600;cursor:pointer;">+ Assign</button>
                                                        <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:4px 8px;border-radius:6px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:10px;font-weight:500;cursor:pointer;">Profil</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:11px;">Tidak ada data RW.</div>
                                    @endforelse
                                </div>
                            </div>
                        @elseif ($activeTab === 'korte')
                            <!-- KORTE MAPPING LIST -->
                            @php
                                $korteGroups = $this->korteGroups;
                            @endphp
                            <div style="display:flex;flex-direction:column;height:100%;flex:1;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                    <div>
                                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Koordinator RT ({{ $selectedVillage['desa'] }})</div>
                                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Pantau &amp; input status KORTE per RT kelompokan per RW</div>
                                    </div>
                                </div>

                                <div style="flex:1;overflow-y:auto;max-height:480px;display:grid;gap:12px;padding-right:4px;">
                                    @foreach ($korteGroups as $group)
                                        <div style="border:0.5px solid #e5e5e5;border-radius:10px;background:#fafafa;overflow:hidden;padding:10px;">
                                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;border-bottom:0.5px solid #e5e5e5;padding-bottom:6px;">
                                                <span style="font-size:12px;font-weight:600;color:#111827;">RW {{ $group['rw'] }}</span>
                                                <button wire:click="openCreateForm(null, '{{ $group['rw'] }}')" type="button" style="padding:3px 8px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:10px;font-weight:500;cursor:pointer;">+ KORTE</button>
                                            </div>

                                            <div style="display:grid;gap:6px;">
                                                @foreach ($group['rows'] as $row)
                                                    @php
                                                        $rowStyle = match ($row['status']) {
                                                            'terbentuk' => 'border:0.5px solid #bbf7d0;background:#f0fdf4;',
                                                            'proses' => 'border:0.5px solid #fed7aa;background:#fff7f1;',
                                                            default => 'border:0.5px solid #e5e5e5;background:white;',
                                                        };
                                                        $badgeStyle = match ($row['status']) {
                                                            'terbentuk' => 'background:#dcfce7;color:#14532d;',
                                                            'proses' => 'background:#fff7f1;color:#993c1d;',
                                                            default => 'background:#f5f5f5;color:#888;',
                                                        };
                                                    @endphp
                                                    <div style="border-radius:8px;padding:8px 10px;{{ $rowStyle }}">
                                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                                            <div>
                                                                <div style="display:flex;align-items:center;gap:6px;">
                                                                    <span style="font-size:11px;font-weight:600;color:#1a1a1a;">RT {{ $row['nomor'] }}</span>
                                                                    <span style="padding:1px 6px;border-radius:999px;font-size:8px;text-transform:uppercase;font-weight:500;{{ $badgeStyle }}">{{ $row['status'] }}</span>
                                                                </div>
                                                                <div style="font-size:10px;color:#4b5563;margin-top:2px;">
                                                                    @if ($row['status'] === 'terbentuk')
                                                                        {{ $row['nama'] ?: 'Tanpa nama' }} ({{ $row['no_hp'] ?: '-' }})
                                                                    @elseif ($row['status'] === 'proses')
                                                                        <span style="color:#b45309;">Proses: {{ $row['nama'] ?: '-' }}</span>
                                                                    @else
                                                                        <span style="color:#9ca3af;">Belum ada koordinator</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div style="display:flex;gap:4px;">
                                                                @if ($row['status'] === 'belum')
                                                                    <button wire:click="openCreateForm('{{ $row['nomor'] }}', '{{ $group['rw'] }}')" type="button" style="padding:3px 6px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:10px;cursor:pointer;">+ Assign</button>
                                                                @else
                                                                    <button wire:click="openEditForm('{{ $row['id'] }}')" type="button" style="padding:3px 6px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:10px;cursor:pointer;">Edit</button>
                                                                    @if ($row['id'])
                                                                        <button wire:click="hapus('{{ $row['id'] }}')" onclick="return confirm('Yakin ingin menghapus KORTE ini?')" type="button" style="padding:3px 6px;border-radius:6px;border:0.5px solid #fecaca;background:#fef2f2;color:#b91c1c;font-size:10px;cursor:pointer;">Hapus</button>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($activeTab === 'penggalang')
                            <!-- PENGGALANG LIST -->
                            @php
                                $penggalangList = $this->penggalangList;
                            @endphp
                            <div style="display:flex;flex-direction:column;height:100%;flex:1;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                    <div>
                                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Daftar Penggalang Suara ({{ $selectedVillage['desa'] }})</div>
                                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Kader aktif penggalangan suara di wilayah RW/RT</div>
                                    </div>
                                    <button wire:click="openPenggalangForm" type="button" style="padding:5px 10px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:11px;font-weight:600;cursor:pointer;">+ Penggalang</button>
                                </div>

                                <div style="flex:1;overflow-y:auto;max-height:480px;display:grid;gap:10px;padding-right:4px;">
                                    @forelse ($penggalangList->groupBy('nomor_rw') as $rw => $members)
                                        <div style="border:0.5px solid #e5e5e5;border-radius:10px;background:#fafafa;padding:10px;">
                                            <div style="font-size:11px;color:#4b5563;font-weight:600;margin-bottom:6px;border-bottom:0.5px solid #e5e5e5;padding-bottom:4px;">RW {{ $rw }} ({{ $members->count() }} orang)</div>
                                            <div style="display:grid;gap:6px;">
                                                @foreach ($members as $pg)
                                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border-radius:8px;border:0.5px solid #e5e7eb;background:white;font-size:11px;">
                                                        <div style="min-width:0;flex:1;">
                                                            <div style="font-weight:600;color:#111827;">{{ $pg->nama }}</div>
                                                            <div style="font-size:10px;color:#6b7280;margin-top:1px;">
                                                                {{ $pg->no_wa ?? $pg->no_hp ?? '-' }} · RT: {{ $pg->rt ?? '-' }}
                                                            </div>
                                                        </div>
                                                        <div style="display:flex;align-items:center;gap:6px;">
                                                            <span style="font-size:9px;padding:2px 6px;border-radius:999px;background:#f3f4f6;color:#374151;">{{ \App\Models\PenggalangSuara::SUMBER_OPTIONS[$pg->sumber] ?? $pg->sumber }}</span>
                                                            <span style="font-size:10px;font-weight:600;color:#fe5000;">{{ number_format($pg->realisasi_jangkauan) }}/{{ number_format($pg->target_jangkauan) }}</span>
                                                            <button wire:click="editPenggalang('{{ $pg->id }}')" type="button" style="padding:3px 6px;border-radius:6px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:9px;cursor:pointer;">Edit</button>
                                                            <button wire:click="hapusPenggalang('{{ $pg->id }}')" onclick="return confirm('Yakin ingin menghapus penggalang suara ini?')" type="button" style="padding:3px 6px;border-radius:6px;border:0.5px solid #fecaca;background:#fef2f2;color:#b91c1c;font-size:9px;cursor:pointer;">Hapus</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div style="text-align:center;padding:32px 16px;font-size:12px;color:#9ca3af;border:1px dashed #d4d4d8;border-radius:8px;">
                                            Belum ada penggalang suara terdaftar di kelurahan/desa ini.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
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
            .infra-main-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 1200px) {
            .infra-summary-grid,
            .infra-top-grid,
            .infra-milestone-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .infra-summary-grid,
            .infra-top-grid,
            .infra-milestone-grid,
            .infra-form-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>

    <!-- FORM MODALS & DRAWERS -->
    @if ($showPenggalangForm && ($selectedVillage = $this->selectedVillageDetail))
        <div wire:click.self="resetPenggalangForm" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);padding:24px;backdrop-filter:blur(4px);">
            <div style="width:100%;max-width:680px;border-radius:24px;border:0.5px solid #e5e5e5;background:white;box-shadow:0 25px 50px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:0.5px solid #e5e5e5;">
                    <div>
                        <div style="font-size:18px;font-weight:600;color:#1a1a1a;">{{ $pgEditId ? 'Edit' : 'Tambah' }} Penggalang Suara</div>
                        <div style="font-size:12px;color:#888;margin-top:4px;">{{ $selectedVillage['desa'] }}, {{ $selectedVillage['kecamatan'] }}</div>
                    </div>
                    <button wire:click="resetPenggalangForm" type="button" style="padding:8px 12px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">Tutup</button>
                </div>

                <div style="display:grid;gap:16px;padding:20px 22px;">
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Nama</label>
                            <input wire:model.defer="pgNama" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                            @error('pgNama') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RW</label>
                            <input wire:model.defer="pgRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                            @error('pgRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No HP</label>
                            <input wire:model.defer="pgHp" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No WA</label>
                            <input wire:model.defer="pgWa" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RT</label>
                            <input wire:model.defer="pgRt" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Sumber</label>
                            <select wire:model.defer="pgSumber" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                                @foreach (\App\Models\PenggalangSuara::SUMBER_OPTIONS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Target Jangkauan</label>
                            <input wire:model.defer="pgTarget" type="number" min="0" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="resetPenggalangForm" type="button" style="padding:10px 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:12px;font-weight:500;cursor:pointer;">Batal</button>
                    <button wire:click="simpanPenggalang" type="button" style="padding:10px 14px;border-radius:10px;border:none;background:#ea580c;color:white;font-size:12px;font-weight:500;cursor:pointer;">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showForm && ($selectedVillage = $this->selectedVillageDetail))
        <div wire:click.self="closeForm" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);padding:24px;backdrop-filter:blur(4px);">
            <div style="width:100%;max-width:760px;border-radius:24px;border:0.5px solid #e5e5e5;background:white;box-shadow:0 25px 50px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:0.5px solid #e5e5e5;">
                    <div>
                        <div style="font-size:18px;font-weight:600;color:#1a1a1a;">{{ $editId ? 'Edit' : 'Tambah' }} {{ $activeTab === 'korwe' ? 'KORWE' : 'KORTE' }}</div>
                        <div style="font-size:12px;color:#888;margin-top:4px;">{{ $selectedVillage['desa'] }}, {{ $selectedVillage['kecamatan'] }}</div>
                    </div>
                    <button wire:click="closeForm" type="button" style="padding:8px 12px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">Tutup</button>
                </div>

                <div style="display:grid;gap:16px;padding:20px 22px;">
                    @if ($activeTab === 'korte')
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RW Induk</label>
                            <input wire:model.defer="formParentRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                            @error('formParentRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">{{ $activeTab === 'korwe' ? 'Nomor RW' : 'Nomor RT' }}</label>
                        <input wire:model.defer="formNomorRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                        @error('formNomorRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Nama Koordinator</label>
                            <input wire:model.defer="formNamaKoordinator" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                            @error('formNamaKoordinator') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No HP</label>
                            <input wire:model.defer="formNoHp" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                            @error('formNoHp') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Status</label>
                            <select wire:model.live="formStatus" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                                <option value="belum">Belum</option>
                                <option value="proses">Proses</option>
                                <option value="terbentuk">Terbentuk</option>
                            </select>
                            @error('formStatus') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>

                        @if ($formStatus === 'terbentuk')
                            <div>
                                <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Tanggal Terbentuk</label>
                                <input wire:model.defer="formTanggal" type="date" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;">
                                @error('formTanggal') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Catatan</label>
                        <textarea wire:model.defer="formCatatan" rows="4" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#111827;background:white;resize:vertical;"></textarea>
                        @error('formCatatan') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="closeForm" type="button" style="padding:10px 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:12px;font-weight:500;cursor:pointer;">Batal</button>
                    <button wire:click="simpan" type="button" style="padding:10px 14px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:500;cursor:pointer;">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showProfilDrawer && $profilRwId && ($selectedVillage = $this->selectedVillageDetail))
        @php
            $partyColors = [
                'PKB' => '#008000', 'GERINDRA' => '#C8102E', 'PDIP' => '#D72027', 'PDI-P' => '#D72027',
                'GOLKAR' => '#FFD700', 'NASDEM' => '#003087', 'BURUH' => '#E31937',
                'GELORA' => '#DC143C', 'PKS' => '#fe5000', 'PKN' => '#336699', 'HANURA' => '#4169E1',
                'GARUDA' => '#228B22', 'PAN' => '#005BAC', 'PBB' => '#009B3A', 'DEMOKRAT' => '#00529C',
                'PSI' => '#EC008C', 'PERINDO' => '#CC0000', 'PPP' => '#006600', 'UMMAT' => '#2E8B57'
            ];
            $tipologiLabels = [
                'perkampungan' => 'Perkampungan',
                'campuran' => 'Campuran (Kampung + Perumahan)',
                'perumahan' => 'Perumahan',
                'perkotaan' => 'Perkotaan',
                'pesisir' => 'Pesisir / Tambak',
                'industri' => 'Industri'
            ];
            $ekonomiLabels = [
                'pertanian' => 'Pertanian',
                'pabrik' => 'Pekerja Pabrik / Industri',
                'informal' => 'Pekerja Informal (Ojol/Freelance)',
                'pedagang' => 'Pedagang / Wiraswasta',
                'pns' => 'PNS / Karyawan',
                'nelayan' => 'Nelayan',
                'campuran' => 'Campuran'
            ];
        @endphp
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="closeProfilDrawer"></div>
        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100%;background:white;box-shadow:-8px 0 24px rgba(0,0,0,0.16);z-index:50;overflow-y:auto;">
            <div style="position:sticky;top:0;background:white;border-bottom:0.5px solid #e5e5e5;padding:16px;z-index:10;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:#1a1a1a;">Profil RW {{ $profilRwId }} - {{ $selectedVillage['desa'] }}</div>
                        <div style="font-size:11px;color:#888;margin-top:4px;">{{ $selectedVillage['kecamatan'] }} · {{ $selectedVillage['dapil'] }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @php
                            $statusCfg = \App\Models\TargetWilayah::STATUS_CONFIG[$autoFillData['status_wilayah'] ?? 'ZONA BERAT'] ?? \App\Models\TargetWilayah::STATUS_CONFIG['ZONA BERAT'];
                        @endphp
                        <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:600;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['text'] }};">{{ $statusCfg['label'] }}</span>
                        <button wire:click="closeProfilDrawer" type="button" style="width:28px;height:28px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#666;cursor:pointer;">x</button>
                    </div>
                </div>
            </div>

            @if (!$isEditingProfil)
                @php
                    $totalDpt = max(1, $autoFillData['dpt'] ?? 0);
                    $malePct = (($autoFillData['dpt_laki'] ?? 0) / $totalDpt) * 100;
                    $femalePct = (($autoFillData['dpt_perempuan'] ?? 0) / $totalDpt) * 100;
                    
                    $genZPct = (($autoFillData['gen_z'] ?? 0) / $totalDpt) * 100;
                    $millennialPct = (($autoFillData['millennial'] ?? 0) / $totalDpt) * 100;
                    $genXPct = (($autoFillData['gen_x'] ?? 0) / $totalDpt) * 100;
                    $boomerPct = (($autoFillData['boomer'] ?? 0) / $totalDpt) * 100;

                    $statusUpa = ($profilData['upa_rw_status'] ?? 'belum') === 'sudah' ? 'Sudah <span style="color:#666;">(' . ($profilData['upa_rw_nama'] ?: '-') . ')</span>' : 'Belum';
                    $statusRki = ($profilData['rki_status'] ?? 'belum') === 'sudah' ? 'Sudah <span style="color:#666;">(' . ($profilData['rki_nama'] ?: '-') . ')</span>' : 'Belum';
                    $statusSenam = ($profilData['senam_status'] ?? 'belum') === 'sudah' ? 'Sudah <span style="color:#666;">(' . ($profilData['senam_nama'] ?: '-') . ')</span>' : 'Belum';
                    $statusRelawan = ($profilData['relawan_milenial_status'] ?? 'belum') === 'sudah' ? 'Sudah <span style="color:#666;">(' . ($profilData['relawan_milenial_nama'] ?: '-') . ')</span>' : 'Belum';
                    $statusCaleg = ($profilData['caleg_terpilih_ada'] ?? false) ? 'Ada <span style="color:#666;">(' . ($profilData['caleg_terpilih_nama'] ?: '-') . ')</span>' : 'Tidak';

                    $statusKompetitor = ($profilData['kompetitor_status'] ?? 'tidak_tahu') === 'ada' ? 'Ada <span style="color:#666;">(' . ($profilData['kompetitor_detail'] ?: '-') . ')</span>' : (($profilData['kompetitor_status'] ?? 'tidak_tahu') === 'tidak' ? 'Tidak ada' : 'Tidak tahu');
                    $statusTimsuk = ($profilData['tim_sukses_status'] ?? 'tidak_tahu') === 'ada' ? 'Ada <span style="color:#666;">(' . ($profilData['tim_sukses_detail'] ?: '-') . ')</span>' : (($profilData['tim_sukses_status'] ?? 'tidak_tahu') === 'tidak' ? 'Tidak ada' : 'Tidak tahu');
                @endphp

                <div style="padding:16px; display:grid; gap:16px;">
                    <!-- DPT & PKS Stats Cards -->
                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:8px;">
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Dpt Wilayah</div>
                            <div style="font-size:18px; font-weight:600; color:#1a1a1a; margin-top:4px;">{{ number_format($autoFillData['dpt'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Estimasi PKS</div>
                            <div style="font-size:18px; font-weight:600; color:#fe5000; margin-top:4px;">~{{ number_format($autoFillData['estimasi_pks'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Laki-laki</div>
                            <div style="font-size:18px; font-weight:600; color:#1a1a1a; margin-top:4px;">{{ number_format($autoFillData['dpt_laki'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Perempuan</div>
                            <div style="font-size:18px; font-weight:600; color:#1a1a1a; margin-top:4px;">{{ number_format($autoFillData['dpt_perempuan'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Share PKS</div>
                            <div style="font-size:18px; font-weight:600; color:#fe5000; margin-top:4px;">{{ number_format(($autoFillData['estimasi_share'] ?? 0) * 100, 1, ',', '.') }}%</div>
                        </div>
                        <div style="background:#fafafa; border-radius:10px; padding:10px; border:0.5px solid #e5e5e5;">
                            <div style="font-size:10px; color:#888; text-transform:uppercase; font-weight:500;">Tps Terlibat</div>
                            <div style="font-size:18px; font-weight:600; color:#1a1a1a; margin-top:4px;">{{ number_format($autoFillData['tps_count'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <!-- Demografi -->
                    <div style="margin-top:8px;">
                        <div style="font-size:11px; color:#666; margin-bottom:6px; font-weight:500; text-transform:uppercase; letter-spacing:0.5px;">Demografi</div>
                        <div style="height:8px; border-radius:999px; background:#f3f4f6; overflow:hidden; display:flex;">
                            <span style="width:{{ $malePct }}%; background:#2563eb; height:100%;"></span>
                            <span style="width:{{ $femalePct }}%; background:#ec4899; height:100%;"></span>
                        </div>
                        <div style="margin-top:6px; font-size:11px; color:#666; display:flex; justify-content:space-between;">
                            <span>Laki-laki {{ number_format($malePct, 1, ',', '.') }}%</span>
                            <span>Perempuan {{ number_format($femalePct, 1, ',', '.') }}%</span>
                        </div>
                        
                        <div style="margin-top:10px; display:grid; gap:8px;">
                            @foreach([
                                ['Gen Z', $autoFillData['gen_z'] ?? 0, '#a78bfa', $genZPct],
                                ['Millennial', $autoFillData['millennial'] ?? 0, '#fe5000', $millennialPct],
                                ['Gen X', $autoFillData['gen_x'] ?? 0, '#16a34a', $genXPct],
                                ['Boomer', $autoFillData['boomer'] ?? 0, '#94a3b8', $boomerPct]
                            ] as $demo)
                                <div>
                                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#666; margin-bottom:4px;">
                                        <span>{{ $demo[0] }}</span>
                                        <span>{{ number_format($demo[3], 1, ',', '.') }}%</span>
                                    </div>
                                    <div style="height:8px; border-radius:999px; background:#f3f4f6; overflow:hidden;">
                                        <div style="width:{{ $demo[3] }}%; background:{{ $demo[2] }}; height:100%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Party Persaingan (Top 5) -->
                    @if (!empty($autoFillData['party_rows']))
                        <div style="margin-top:8px;">
                            <div style="font-size:11px; color:#fe5000; font-weight:600; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Peta Persaingan Partai (Top 5)</div>
                            <div style="display:grid; gap:10px; background:#fafafa; border-radius:10px; padding:12px; border:0.5px solid #e5e5e5;">
                                @foreach (array_slice($autoFillData['party_rows'], 0, 5) as $idx => $tp)
                                    @php
                                        $partyName = $tp['party_name'] ?? $tp['partai'] ?? '-';
                                        $votes = $tp['votes'] ?? $tp['suara'] ?? 0;
                                        $share = $tp['share'] ?? 0;
                                        $color = $partyColors[strtoupper($partyName)] ?? '#94a3b8';
                                        $isPks = strtoupper($partyName) === 'PKS';
                                        $barWidth = min(100, max(0, $share * 100));
                                    @endphp
                                    <div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; margin-bottom:4px;">
                                            <span style="font-weight:{{ $isPks ? '700' : '500' }}; color:{{ $isPks ? '#fe5000' : '#1e293b' }}; display:inline-flex; align-items:center; gap:4px;">
                                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $color }};"></span>
                                                {{ $idx + 1 }}. {{ $partyName }}
                                            </span>
                                            <span style="font-variant-numeric:tabular-nums; color:#475569; font-weight:{{ $isPks ? '600' : '400' }};">
                                                {{ number_format($votes, 0, ',', '.') }} suara ({{ number_format($share * 100, 1, ',', '.') }}%)
                                            </span>
                                        </div>
                                        <div style="height:6px; border-radius:999px; background:#e2e8f0; overflow:hidden;">
                                            <div style="width:{{ $barWidth }}%; background:{{ $color }}; height:100%; border-radius:999px;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Profil & Intel RW summary / empty -->
                    @if ($profilCompletion === 0)
                        <div style="margin-top:8px; border:0.5px solid #e5e5e5; border-radius:12px; background:#fff7f1; overflow:hidden;">
                            <div style="background:#fe5000; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; color:white;">
                                <span style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Profil & Intel RW</span>
                                <span style="font-size:11px; font-weight:700; background:rgba(255,255,255,0.25); padding:2px 6px; border-radius:6px;">Lengkap 0%</span>
                            </div>
                            <div style="padding:16px; text-align:center; background:white;">
                                <div style="font-size:12px; color:#666; margin-bottom:12px;">Belum ada data profil untuk RW ini. Lengkapi data intelijen lapangan untuk memetakan kondisi wilayah.</div>
                                <button type="button" wire:click="$set('isEditingProfil', true)" style="padding:8px 16px; border-radius:8px; border:none; background:#fe5000; color:white; font-size:12px; font-weight:600; cursor:pointer;">
                                    ✏️ Lengkapi Profil RW
                                </button>
                            </div>
                        </div>
                    @else
                        <div style="margin-top:8px; border:0.5px solid #e5e5e5; border-radius:12px; background:#fff7f1; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                            <div style="background:#fe5000; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; color:white;">
                                <span style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Profil & Intel RW</span>
                                <span style="font-size:11px; font-weight:700; background:rgba(255,255,255,0.25); padding:2px 6px; border-radius:6px;">Lengkap {{ $profilCompletion }}%</span>
                            </div>
                            <div style="padding:14px; display:grid; gap:12px; background:white; font-size:12px;">
                                
                                <div>
                                    <div style="font-size:11px; font-weight:700; color:#d97706; border-bottom:0.5px solid #fee2d5; padding-bottom:3px; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">1. Profil Wilayah</div>
                                    <div style="display:grid; gap:6px;">
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Tipologi RW:</span><strong style="color:#1f2937; text-align:right;">{{ $tipologiLabels[$profilData['tipologi'] ?? ''] ?? '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Ekonomi Dominan:</span><strong style="color:#1f2937; text-align:right;">{{ $ekonomiLabels[$profilData['ekonomi_dominan'] ?? ''] ?? '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Profil Umum Warga:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['profil_warga'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Faktor Menang/Kalah:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['faktor_penyebab'] ?: '-' }}</strong></div>
                                    </div>
                                </div>

                                <div>
                                    <div style="font-size:11px; font-weight:700; color:#16a34a; border-bottom:0.5px solid #dcfce7; padding-bottom:3px; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">2. Infrastruktur Partai</div>
                                    <div style="display:grid; gap:6px;">
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Anggota PKS di RW:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['anggota_pks'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Pengajian RW:</span><strong style="color:#1f2937; text-align:right;">{!! $statusUpa !!}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>RKI (Rumah Keluarga Indonesia):</span><strong style="color:#1f2937; text-align:right;">{!! $statusRki !!}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Titik Senam PKS:</span><strong style="color:#1f2937; text-align:right;">{!! $statusSenam !!}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Relawan Muda:</span><strong style="color:#1f2937; text-align:right;">{!! $statusRelawan !!}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Aleg Terpilih di RW (Bisa partai lain):</span><strong style="color:#1f2937; text-align:right;">{!! $statusCaleg !!}</strong></div>
                                    </div>
                                </div>

                                <div>
                                    <div style="font-size:11px; font-weight:700; color:#dc2626; border-bottom:0.5px solid #fee2e2; padding-bottom:3px; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">3. Peta Politik Lokal</div>
                                    <div style="display:grid; gap:6px;">
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Afiliasi Ketua RW & RT:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['afiliasi_rw_rt'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Afiliasi Kader Posyandu & DKM:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['afiliasi_posyandu_dkm'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Pengurus Kompetitor:</span><strong style="color:#1f2937; text-align:right;">{!! $statusKompetitor !!}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>Tim Sukses Lain:</span><strong style="color:#1f2937; text-align:right;">{!! $statusTimsuk !!}</strong></div>
                                    </div>
                                </div>

                                <div>
                                    <div style="font-size:11px; font-weight:700; color:#ea580c; border-bottom:0.5px solid #ffedd5; padding-bottom:3px; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">4. Strategi & PJ</div>
                                    <div style="display:grid; gap:6px;">
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Strategi Target Suara:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['strategi'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563;"><span>PJ Dakwah di RW:</span><strong style="color:#1f2937; text-align:right;">{{ $profilData['penanggung_jawab'] ?: '-' }}</strong></div>
                                        <div style="display:flex; justify-content:space-between; color:#4b5563; flex-direction:column; gap:2px;"><span>Keterangan Lain:</span><strong style="color:#1f2937; font-weight:500; white-space:pre-line;">{{ $profilData['keterangan_lain'] ?: '-' }}</strong></div>
                                    </div>
                                </div>

                                <button type="button" wire:click="$set('isEditingProfil', true)" style="width:100%; margin-top:8px; padding:10px; border-radius:8px; border:none; background:#fe5000; color:white; font-size:12px; font-weight:600; cursor:pointer; text-align:center;">
                                    ✏️ Edit Profil RW
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div style="padding:16px; display:grid; gap:16px;">
                    <!-- Auto fill info -->
                    <div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:#2563eb;margin-bottom:10px;">Data otomatis <span style="font-size:10px;padding:2px 6px;border-radius:999px;background:#dbeafe;color:#2563eb;">auto-fill</span></div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-bottom:8px;">
                            <div style="background:#fafafa;border-radius:10px;padding:10px;">
                                <div style="font-size:10px;color:#888;">Jumlah RT</div>
                                <div style="font-size:14px;font-weight:600;color:#1a1a1a;">{{ number_format($autoFillData['jumlah_rt'] ?? 0) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:10px;">
                                <div style="font-size:10px;color:#888;">DPT</div>
                                <div style="font-size:14px;font-weight:600;color:#1a1a1a;">{{ number_format($autoFillData['dpt'] ?? 0) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:10px;">
                                <div style="font-size:10px;color:#888;">Est. Suara PKS</div>
                                <div style="font-size:14px;font-weight:600;color:#ea580c;">~{{ number_format($autoFillData['estimasi_pks'] ?? 0) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:10px;">
                                <div style="font-size:10px;color:#888;">Target 2029</div>
                                <div style="font-size:14px;font-weight:600;color:#ea580c;">{{ number_format($autoFillData['target_suara'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div style="font-size:11px;color:#666;background:#fafafa;border-radius:10px;padding:10px;line-height:1.6;">
                            <strong>Caleg PKS tertinggi:</strong> {{ $autoFillData['caleg_pks_tertinggi'] ?? '-' }}<br>
                            <strong>Partai pemenang:</strong> {{ $autoFillData['partai_pemenang'] ?? '-' }}<br>
                            <strong>3 partai tertinggi:</strong> {{ $autoFillData['top_3_partai'] ?? '-' }}<br>
                            @if ($autoFillData['korwe_nama'] ?? null)
                                <strong>KORWE:</strong> {{ $autoFillData['korwe_nama'] }} ({{ $autoFillData['korwe_status'] }})
                            @endif
                        </div>
                    </div>

                    <!-- PROFIL WILAYAH -->
                    <div style="font-size:12px;font-weight:600;color:#d97706;border-bottom:1.5px solid #fee2d5;padding-bottom:4px;margin-top:8px;margin-bottom:4px;">PROFIL WILAYAH</div>
                    
                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Tipologi RW</label>
                        <select wire:model="profilData.tipologi" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;">
                            <option value="">- Pilih -</option>
                            @foreach ($tipologiLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Sumber Ekonomi Dominan</label>
                        <select wire:model="profilData.ekonomi_dominan" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;">
                            <option value="">- Pilih -</option>
                            @foreach ($ekonomiLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Profil Umum Warga</label>
                        <select wire:model="profilData.profil_warga" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;">
                            <option value="">- Pilih Profil -</option>
                            @foreach ([
                                'Agamis & Kondusif',
                                'Pragmatis & Transaksional',
                                'Nasionalis & Abangan',
                                'Heterogen & Individualis',
                                'Kritis & Akademis',
                                'Buruh & Pekerja'
                            ] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;" class="infra-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Suara PKS 2019</label>
                            <input wire:model="profilData.suara_pks_2019" type="number" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;color:#1f2937;" placeholder="0">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Jumlah KTA</label>
                            <input wire:model="profilData.jumlah_kta" type="number" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;color:#1f2937;" placeholder="0">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Faktor Penyebab Menang/Kalah</label>
                        <select wire:model.live="faktorSelect" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;margin-bottom:6px;">
                            <option value="">- Pilih Faktor Utama -</option>
                            @foreach ([
                                'Kekuatan Caleg Lokal',
                                'Ketokohan Tokoh Agama/Masyarakat',
                                'Program Kerja & Bantuan Nyata',
                                'Pragmatisme Politik Uang',
                                'Keaktifan Kader & Relawan',
                                'Kurangnya Sosialisasi/Kehadiran',
                                'Dominasi Partai Lain'
                            ] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        <textarea wire:model="faktorDesc" rows="2" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Keterangan tambahan faktor penyebab..."></textarea>
                    </div>

                    <!-- INFRASTRUKTUR PARTAI -->
                    <div style="font-size:12px;font-weight:600;color:#16a34a;border-bottom:1.5px solid #dcfce7;padding-bottom:4px;margin-top:8px;margin-bottom:4px;">INFRASTRUKTUR PARTAI</div>
                    
                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Anggota PKS di RW</label>
                        <textarea wire:model="profilData.anggota_pks" rows="2" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Nama + jenjang keanggotaan"></textarea>
                    </div>

                    @foreach ([
                        ['field' => 'upa_rw', 'label' => 'Pengajian RW', 'name_field' => 'upa_rw_nama', 'wa_field' => 'upa_rw_wa', 'placeholder' => 'Nama pembina'],
                        ['field' => 'rki', 'label' => 'RKI (Rumah Keluarga Indonesia)', 'name_field' => 'rki_nama', 'wa_field' => 'rki_wa', 'placeholder' => 'Nama penggerak'],
                        ['field' => 'senam', 'label' => 'Titik Senam PKS', 'name_field' => 'senam_nama', 'wa_field' => 'senam_wa', 'placeholder' => 'Nama instruktur'],
                        ['field' => 'relawan_milenial', 'label' => 'Relawan Muda', 'name_field' => 'relawan_milenial_nama', 'wa_field' => 'relawan_milenial_wa', 'placeholder' => 'Nama + jabatan']
                    ] as $item)
                        <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:10px;background:#f9fafb;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                                <span style="font-size:12px;font-weight:500;color:#374151;">{{ $item['label'] }}</span>
                                <select wire:model.live="profilData.{{ $item['field'] }}_status" style="height:30px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;background:white;font-size:12px;color:#1f2937;">
                                    <option value="belum">Belum</option>
                                    <option value="sudah">Sudah</option>
                                </select>
                            </div>
                            @if (($profilData[$item['field'] . '_status'] ?? 'belum') === 'sudah')
                                <div style="display:grid;gap:8px;">
                                    <input type="text" wire:model="profilData.{{ $item['name_field'] }}" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="{{ $item['placeholder'] }}">
                                    <input type="text" wire:model="{{ $item['wa_field'] }}" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="No WhatsApp (contoh: 08123456789)">
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:10px;background:#f9fafb;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                            <span style="font-size:12px;font-weight:500;color:#374151;">Aleg terpilih di RW (Bisa partai lain)</span>
                            <select wire:model.live="profilData.caleg_terpilih_ada" style="height:30px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;background:white;font-size:12px;color:#1f2937;">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        @if (($profilData['caleg_terpilih_ada'] ?? false))
                            <input type="text" wire:model="profilData.caleg_terpilih_nama" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="Nama caleg">
                        @endif
                    </div>

                    <!-- PETA POLITIK LOKAL -->
                    <div style="font-size:12px;font-weight:600;color:#dc2626;border-bottom:1.5px solid #fee2e2;padding-bottom:4px;margin-top:8px;margin-bottom:4px;">PETA POLITIK LOKAL</div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Afiliasi Ketua RW & RT</label>
                        <textarea wire:model="profilData.afiliasi_rw_rt" rows="3" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Ketua RW: Nama - Partai&#10;RT 1: Nama - Partai"></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Afiliasi Kader Posyandu & DKM</label>
                        <textarea wire:model="profilData.afiliasi_posyandu_dkm" rows="2" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Nama - organisasi - partai"></textarea>
                    </div>

                    <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:10px;background:#f9fafb;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                            <span style="font-size:12px;font-weight:500;color:#374151;">Pengurus kompetitor?</span>
                            <select wire:model.live="profilData.kompetitor_status" style="height:30px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;background:white;font-size:12px;color:#1f2937;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                        </div>
                        @if (($profilData['kompetitor_status'] ?? 'tidak_tahu') === 'ada')
                            <div style="display:grid;gap:8px;">
                                <input type="text" wire:model="profilData.kompetitor_detail" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="Nama + partai">
                                <input type="text" wire:model="kompetitor_wa" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="No WhatsApp kompetitor">
                            </div>
                        @endif
                    </div>

                    <div style="border:0.5px solid #e5e5e5;border-radius:8px;padding:10px;background:#f9fafb;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                            <span style="font-size:12px;font-weight:500;color:#374151;">Tim sukses lain?</span>
                            <select wire:model.live="profilData.tim_sukses_status" style="height:30px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;background:white;font-size:12px;color:#1f2937;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                        </div>
                        @if (($profilData['tim_sukses_status'] ?? 'tidak_tahu') === 'ada')
                            <div style="display:grid;gap:8px;">
                                <input type="text" wire:model="profilData.tim_sukses_detail" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="Nama + partai">
                                <input type="text" wire:model="tim_sukses_wa" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;color:#1f2937;" placeholder="No WhatsApp tim sukses">
                            </div>
                        @endif
                    </div>

                    <!-- STRATEGI & PENANGGUNG JAWAB -->
                    <div style="font-size:12px;font-weight:600;color:#ea580c;border-bottom:1px solid #ffedd5;padding-bottom:4px;margin-top:8px;margin-bottom:4px;">STRATEGI & PENANGGUNG JAWAB</div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Strategi Mencapai Target Suara</label>
                        <select wire:model.live="strategiSelect" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;margin-bottom:6px;">
                            <option value="">- Pilih Strategi Utama -</option>
                            @foreach ([
                                'Sapa Warga & Door to Door',
                                'Penyediaan Layanan Sosial',
                                'Penguatan Tokoh Kunci',
                                'Event / Kegiatan Komunitas',
                                'Kampanye Digital & Media Sosial',
                                'Penguatan Saksi & Pengawalan Suara'
                            ] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        <textarea wire:model="strategiDesc" rows="2" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Keterangan tambahan rencana aksi/strategi..."></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Penanggung Jawab Dakwah di RW</label>
                        <input type="text" wire:model="profilData.penanggung_jawab" style="width:100%;height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:13px;color:#1f2937;" placeholder="Nama + jenjang">
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:500;">Keterangan Lain</label>
                        <textarea wire:model="profilData.keterangan_lain" rows="2" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:8px 10px;background:white;font-size:13px;color:#1f2937;resize:vertical;" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>

                <div style="position:sticky;bottom:0;background:white;border-top:0.5px solid #e5e5e5;padding:16px;display:flex;gap:8px;">
                    <button wire:click="simpanProfil" type="button" style="flex:1;height:40px;border:none;border-radius:10px;background:#ea580c;color:white;font-size:13px;font-weight:600;cursor:pointer;">Simpan Profil</button>
                    <button wire:click="$set('isEditingProfil', false)" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;cursor:pointer;">Batal</button>
                </div>
            @endif
        </div>
    @endif
</div>
