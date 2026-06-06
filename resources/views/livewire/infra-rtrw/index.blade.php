<div style="min-height:100vh;background:#fafafa;">
    @php
        $summary = $this->summaryData;
        $milestones = $this->milestoneData;
        $dapilProgress = $this->dapilProgressData;
        $desaData = $this->desaData;
        $desaGroups = $desaData->getCollection()->groupBy('kecamatan');
    @endphp

    <div style="width:100%;margin:0;">
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
                    <div style="font-weight:500;font-size:14px;">Infra RT/RW</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
                        <option value="">Semua dapil</option>
                        @foreach ($this->getDapilOptions() as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedKecamatan" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua kecamatan</option>
                        @foreach ($this->getKecamatanOptions() as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedTahun" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                        @foreach ([2026, 2027, 2028, 2029] as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari desa..." style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;width:160px;background:#27272a;color:#f4f4f5;">
                    <button type="button" wire:click="$set('selectedDapil', ''); $set('selectedKecamatan', ''); $set('selectedTahun', 2026); $set('search', ''); $set('activeTab', 'korwe')" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
                    <button wire:click="export" type="button" style="padding:5px 12px;border-radius:6px;font-size:12px;background:#fe5000;color:white;border:none;cursor:pointer;">Export</button>
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">RW</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Dashboard Infra RT/RW</h1>
                    <div style="font-size:12px;color:#666;">Tracking pembentukan Koordinator RW (KORWE) &amp; Koordinator RT (KORTE)</div>
                </div>
                <div style="font-size:11px;color:#888;">
                    Fokus:
                    <span style="color:#fe5000;font-weight:500;">{{ $selectedDapil !== '' ? $selectedDapil : 'Semua Dapil' }}</span>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="infra-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Total RW</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['total_rw']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($summary['total_rt']) }} RT · {{ number_format($summary['total_desa']) }} desa</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target KORWE {{ $selectedTahun }}</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_korwe']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">{{ number_format($summary['persen_korwe'], 1) }}% dari {{ number_format($summary['total_rw']) }} RW · terbentuk {{ number_format($summary['korwe_terbentuk']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target KORTE {{ $selectedTahun }}</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_korte']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">{{ number_format($summary['persen_korte'], 1) }}% dari {{ number_format($summary['total_rt']) }} RT · terbentuk {{ number_format($summary['korte_terbentuk']) }}</div>
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

            <div style="display:grid;grid-template-columns:minmax(0,1.02fr) minmax(340px,0.98fr);gap:12px;padding:0 20px;align-items:start;" class="infra-top-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;">
                    <div>
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Target Bertahap</div>
                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Roadmap pembentukan koordinator</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#444;font-weight:500;margin-bottom:8px;">KORWE</div>
                        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;" class="infra-milestone-grid">
                            @foreach ([2026, 2027, 2028, 2029] as $yr)
                                <div style="border:0.5px solid {{ $yr === $selectedTahun ? '#fed7aa' : '#e5e5e5' }};background:{{ $yr === $selectedTahun ? '#fff7f1' : '#fafafa' }};border-radius:10px;padding:12px;text-align:center;">
                                    <div style="font-size:11px;color:#888;">{{ $yr }}</div>
                                    <div style="font-size:22px;font-weight:500;color:#1a1a1a;margin-top:4px;">{{ number_format($milestones['korwe_' . $yr]) }}</div>
                                    <div style="font-size:10px;color:#fe5000;margin-top:2px;">{{ number_format($milestones['korwe_pct_' . $yr], 1) }}%</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#444;font-weight:500;margin-bottom:8px;">KORTE</div>
                        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;" class="infra-milestone-grid">
                            @foreach ([2026, 2027, 2028, 2029] as $yr)
                                <div style="border:0.5px solid {{ $yr === $selectedTahun ? '#fed7aa' : '#e5e5e5' }};background:{{ $yr === $selectedTahun ? '#fff7f1' : '#fafafa' }};border-radius:10px;padding:12px;text-align:center;">
                                    <div style="font-size:11px;color:#888;">{{ $yr }}</div>
                                    <div style="font-size:22px;font-weight:500;color:#1a1a1a;margin-top:4px;">{{ number_format($milestones['korte_' . $yr]) }}</div>
                                    <div style="font-size:10px;color:#fe5000;margin-top:2px;">{{ number_format($milestones['korte_pct_' . $yr], 1) }}%</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Progress per Dapil</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Target vs aktual {{ $selectedTahun }}</div>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <button wire:click="$set('activeTab', 'korwe')" type="button" style="padding:6px 14px;border-radius:999px;font-size:11px;border:0.5px solid {{ $activeTab === 'korwe' ? '#1a1a1a' : '#e5e5e5' }};background:{{ $activeTab === 'korwe' ? '#1a1a1a' : 'white' }};color:{{ $activeTab === 'korwe' ? 'white' : '#666' }};cursor:pointer;">KORWE</button>
                            <button wire:click="$set('activeTab', 'korte')" type="button" style="padding:6px 14px;border-radius:999px;font-size:11px;border:0.5px solid {{ $activeTab === 'korte' ? '#1a1a1a' : '#e5e5e5' }};background:{{ $activeTab === 'korte' ? '#1a1a1a' : 'white' }};color:{{ $activeTab === 'korte' ? 'white' : '#666' }};cursor:pointer;">KORTE</button>
                        </div>
                    </div>

                    <div style="display:grid;gap:8px;">
                        @foreach ($dapilProgress as $dp)
                            <div style="display:grid;gap:6px;">
                                <div style="display:grid;grid-template-columns:72px 74px minmax(0,1fr) 40px;gap:10px;align-items:center;">
                                    <div style="font-size:12px;color:#1f2937;font-weight:500;">{{ $dp['dapil'] }}</div>
                                    <div style="font-size:11px;color:#888;text-align:right;">{{ number_format($dp['terbentuk']) }} / {{ number_format($dp['target']) }}</div>
                                    <div style="height:18px;background:#f3f4f6;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ max($dp['persen'], 2) }}%;background:#fe5000;border-radius:999px;display:flex;align-items:center;padding:0 8px;">
                                            @if ($dp['persen'] >= 8)
                                                <span style="font-size:10px;color:white;font-weight:500;">{{ number_format($dp['persen'], 1) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div style="font-size:11px;font-weight:500;text-align:right;color:{{ $dp['persen'] > 0 ? '#fe5000' : '#9ca3af' }};">{{ number_format($dp['persen'], 1) }}%</div>
                                </div>
                                <div style="display:grid;grid-template-columns:72px 74px minmax(0,1fr) 40px;gap:10px;align-items:center;">
                                    <div style="font-size:11px;color:#9ca3af;">Profil</div>
                                    <div style="font-size:11px;color:#9ca3af;text-align:right;">{{ number_format($dp['profil_terisi']) }} / {{ number_format($dp['total_rw']) }}</div>
                                    <div style="height:12px;background:#f3f4f6;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ max($dp['profil_persen'], 0) }}%;background:#3b82f6;border-radius:999px;min-width:{{ $dp['profil_persen'] > 0 ? '24px' : '0' }};"></div>
                                    </div>
                                    <div style="font-size:11px;font-weight:500;text-align:right;color:{{ $dp['profil_persen'] > 0 ? '#2563eb' : '#9ca3af' }};">{{ number_format($dp['profil_persen'], 1) }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="padding:14px 20px 20px;display:grid;gap:14px;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Tabel Detail Wilayah</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Daftar desa dan progres pembentukan</div>
                        </div>
                        <div style="font-size:11px;color:#888;">Klik tombol input untuk masuk ke detail desa</div>
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
                                $totalRwPrioritas = $rows->sum('rw_prioritas_count');
                                $totalProfilTerisi = $rows->sum('profil_terisi_count');
                                $totalSuara2024 = $rows->sum('suara_pks_2024');
                                $totalTarget2029 = $rows->sum('target_suara_2029');
                                $totalProgressTarget = $activeTab === 'korwe' ? $totalKorweTarget : $totalKorteTarget;
                                $totalProgressDone = $activeTab === 'korwe' ? $totalKorweTerbentuk : $totalKorteTerbentuk;
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
                                                <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $totalKorweTerbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $totalKorweTerbentuk > 0 ? '500' : '400' }};">{{ number_format($totalKorweTerbentuk) }}</span> / {{ number_format($totalKorweTarget) }}</td>
                                                <td style="padding:10px 12px;text-align:center;color:#525252;"><span style="color:{{ $totalKorteTerbentuk > 0 ? '#fe5000' : '#525252' }};font-weight:{{ $totalKorteTerbentuk > 0 ? '500' : '400' }};">{{ number_format($totalKorteTerbentuk) }}</span> / {{ number_format($totalKorteTarget) }}</td>
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
                                                    $progressTarget = $activeTab === 'korwe' ? $korweTarget : $korteTarget;
                                                    $progressDone = $activeTab === 'korwe' ? (int) $tw->korwes_terbentuk : (int) $tw->kortes_terbentuk;
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
            </div>
        </div>
    </div>

    <style>
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
            .infra-milestone-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
