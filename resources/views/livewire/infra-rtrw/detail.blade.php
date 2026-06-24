@php
    $summary = $this->summaryData;
    $yearlyTargets = $this->yearlyTargets;
    $korweRows = $this->korweRows;
    $korteGroups = $this->korteGroups;
    $profilStats = $this->profilStats;
    $profilMap = $this->profilRwMap;
    $filteredRwList = $this->filteredRwList;
    $rwStatusFilters = $this->rwStatusFilters;
    $dapilNumber = trim(str_replace('BEKASI', '', $targetWilayah->dapil));
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20 7V12C20 17 16.5 20.74 12 22C7.5 20.74 4 17 4 12V7L12 3Z" stroke="white" stroke-width="1.5"/>
                            <path d="M12 7V17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M7 12H17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div style="font-weight:500;font-size:14px;">Detail Infrastruktur</div>
                </div>
                <div style="font-size:11px;color:#aaa;">
                    {{ $targetWilayah->dapil }} · {{ $targetWilayah->kecamatan }} · {{ $targetWilayah->desa }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#aaa;">
                <span>Target aktif: <span style="color:white;">{{ $this->activeYear }}</span></span>
                <a href="{{ route('infra-rtrw.index') }}" wire:navigate style="padding:6px 12px;border-radius:999px;border:0.5px solid #3f3f46;color:white;text-decoration:none;">Kembali</a>
            </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
            <div style="padding:12px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;gap:6px;flex-wrap:wrap;font-size:11px;color:#888;">
                <a href="{{ route('infra-rtrw.index') }}" wire:navigate style="color:#fe5000;text-decoration:none;font-weight:500;">Infrastruktur</a>
                <span>›</span>
                <span style="color:#666;">{{ $targetWilayah->dapil }}</span>
                <span>›</span>
                <span style="color:#666;">{{ $targetWilayah->kecamatan }}</span>
                <span>›</span>
                <span style="color:#1a1a1a;font-weight:500;">{{ $targetWilayah->desa }}</span>
            </div>

            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">{{ $targetWilayah->desa }}, {{ $targetWilayah->kecamatan }}</h1>
                    <div style="font-size:12px;color:#666;margin-top:2px;">Dapil {{ $dapilNumber }} · {{ number_format($targetWilayah->jumlah_rw) }} RW · {{ number_format($targetWilayah->jumlah_rt) }} RT · {{ number_format($targetWilayah->jumlah_tps) }} TPS · DPT: {{ number_format($targetWilayah->jumlah_dpt) }}</div>
                </div>
                <div style="font-size:11px;color:#888;">PKS 2024: <span style="color:#fe5000;font-weight:500;">{{ number_format($targetWilayah->suara_pks_2024) }}</span></div>
            </div>

            @if (session()->has('success'))
                <div style="padding:14px 20px 0;">
                    <div style="border:0.5px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:10px;padding:10px 12px;font-size:12px;">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="detail-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Suara PKS 2024</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($targetWilayah->suara_pks_2024) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Ranking #{{ number_format($targetWilayah->ranking_pks) }} · {{ number_format((float) $targetWilayah->persentase_pks, 2) }}%</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Target 2029</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($targetWilayah->target_suara_2029) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Kekurangan {{ number_format($targetWilayah->kekurangan_suara) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">KORWE</div>
                        <div style="font-size:10px;opacity:.85;">Target {{ $this->activeYear }}</div>
                    </div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['korwe_formed']) }}/{{ number_format($summary['korwe_target']) }}</div>
                    <div style="margin-top:8px;height:6px;background:rgba(255,255,255,0.18);border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ min(100, max(0, $summary['korwe_percent'])) }}%;background:white;border-radius:999px;"></div>
                    </div>
                    <div style="font-size:11px;margin-top:6px;opacity:.9;">{{ number_format($summary['korwe_percent'], 1) }}% tercapai</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">KORTE</div>
                        <div style="font-size:10px;opacity:.85;">Target {{ $this->activeYear }}</div>
                    </div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['korte_formed']) }}/{{ number_format($summary['korte_target']) }}</div>
                    <div style="margin-top:8px;height:6px;background:rgba(255,255,255,0.18);border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ min(100, max(0, $summary['korte_percent'])) }}%;background:white;border-radius:999px;"></div>
                    </div>
                    <div style="font-size:11px;margin-top:6px;opacity:.9;">{{ number_format($summary['korte_percent'], 1) }}% tercapai</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Profil RW</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($profilStats['profil_terisi']) }} / {{ number_format($profilStats['total_rw']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($profilStats['profil_lengkap']) }} lengkap · {{ number_format($profilStats['pct_profil']) }}% terisi</div>
                    <div style="margin-top:8px;height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ $profilStats['pct_profil'] }}%;background:#3b82f6;border-radius:999px;"></div>
                    </div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">PENGGALANG</div>
                        <div style="font-size:10px;opacity:.85;">Target {{ $this->activeYear }}</div>
                    </div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['penggalang_formed']) }}/{{ number_format($summary['penggalang_target']) }}</div>
                    <div style="margin-top:8px;height:6px;background:rgba(255,255,255,0.18);border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ min(100, max(0, $summary['penggalang_percent'])) }}%;background:white;border-radius:999px;"></div>
                    </div>
                    <div style="font-size:11px;margin-top:6px;opacity:.9;">{{ number_format($summary['penggalang_percent'], 1) }}% tercapai</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">UPA RW</div>
                    <div style="font-size:26px;font-weight:500;color:#2563eb;margin-top:6px;">{{ number_format($this->upaSummary['rw_dengan_upa']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($this->upaSummary['total_member']) }} anggota</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Saksi TPS</div>
                    <div style="font-size:26px;font-weight:500;color:#16a34a;margin-top:6px;">{{ number_format($this->saksiSummary['terkonfirmasi']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">/ {{ number_format($this->saksiSummary['total_tps']) }} TPS</div>
                </div>
            </div>

            <div style="padding:0 20px 14px;">
                <div style="display:flex;gap:4px;background:#f4f4f5;border-radius:10px;padding:4px;flex-wrap:wrap;">
                    @foreach ([
                        'korwe' => ['label' => 'KORWE/KORTE', 'icon' => 'user-shield'],
                        'penggalang' => ['label' => 'Penggalang Suara', 'icon' => 'speakerphone'],
                        'upa' => ['label' => 'UPA RW', 'icon' => 'home-check'],
                        'saksi' => ['label' => 'Saksi TPS', 'icon' => 'eye-check'],
                    ] as $key => $tab)
                        <button
                            wire:click="$set('activeMainTab', '{{ $key }}')"
                            type="button"
                            style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;min-width:160px;padding:8px 10px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $activeMainTab === $key ? 'white' : 'transparent' }};color:{{ $activeMainTab === $key ? '#ea580c' : '#666' }};box-shadow:{{ $activeMainTab === $key ? '0 1px 2px rgba(0,0,0,.08)' : 'none' }};">
                            <i class="ti ti-{{ $tab['icon'] }}" style="font-size:14px;" aria-hidden="true"></i>
                            <span>{{ $tab['label'] }}</span>
                            @if ($key === 'penggalang')
                                <span style="font-size:9px;padding:2px 6px;border-radius:999px;background:#ffedd5;color:#ea580c;">{{ $this->penggalangList->count() }}</span>
                            @elseif ($key === 'upa')
                                <span style="font-size:9px;padding:2px 6px;border-radius:999px;background:#dbeafe;color:#2563eb;">{{ $this->upaList->count() }}</span>
                            @elseif ($key === 'saksi')
                                <span style="font-size:9px;padding:2px 6px;border-radius:999px;background:#dcfce7;color:#16a34a;">{{ $this->saksiList->count() }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($activeMainTab === 'korwe')
            <div style="display:grid;grid-template-columns:minmax(0,0.92fr) minmax(340px,1.08fr);gap:12px;padding:0 20px;align-items:start;" class="detail-top-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="margin-bottom:10px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Target per Tahun</div>
                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Ringkasan target dan aktual</div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead style="background:#fafafa;">
                                <tr style="border-bottom:0.5px solid #e5e5e5;">
                                    <th style="text-align:left;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Tahun</th>
                                    <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORWE Target</th>
                                    <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORWE Aktual</th>
                                    <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORTE Target</th>
                                    <th style="text-align:right;padding:10px 12px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">KORTE Aktual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($yearlyTargets as $row)
                                    <tr style="border-bottom:0.5px solid #f1f5f9;background:{{ $row['active'] ? '#fff7f1' : 'white' }};">
                                        <td style="padding:10px 12px;font-weight:500;color:#1a1a1a;">{{ $row['year'] }}</td>
                                        <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($row['korwe_target']) }}</td>
                                        <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($row['korwe_formed']) }}</td>
                                        <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($row['korte_target']) }}</td>
                                        <td style="padding:10px 12px;text-align:right;color:#525252;">{{ number_format($row['korte_formed']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Pembentukan Koordinator</div>
                            <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Input dan monitor progres desa</div>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <button wire:click="setActiveTab('korwe')" type="button" style="padding:6px 14px;border-radius:999px;font-size:11px;border:0.5px solid {{ $activeTab === 'korwe' ? '#1a1a1a' : '#e5e5e5' }};background:{{ $activeTab === 'korwe' ? '#1a1a1a' : 'white' }};color:{{ $activeTab === 'korwe' ? 'white' : '#666' }};cursor:pointer;">KORWE</button>
                            <button wire:click="setActiveTab('korte')" type="button" style="padding:6px 14px;border-radius:999px;font-size:11px;border:0.5px solid {{ $activeTab === 'korte' ? '#1a1a1a' : '#e5e5e5' }};background:{{ $activeTab === 'korte' ? '#1a1a1a' : 'white' }};color:{{ $activeTab === 'korte' ? 'white' : '#666' }};cursor:pointer;">KORTE</button>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;border-top:0.5px solid #e5e5e5;padding-top:12px;">
                        <div style="font-size:12px;color:#666;">
                            {{ $activeTab === 'korwe' ? 'Koordinator RW' : 'Koordinator RT' }} aktif untuk {{ $targetWilayah->desa }}
                        </div>
                        <button wire:click="openCreateForm" type="button" style="padding:7px 12px;border-radius:8px;font-size:12px;background:#fe5000;color:white;border:none;cursor:pointer;font-weight:500;">
                            + {{ $activeTab === 'korwe' ? 'Tambah KORWE' : 'Tambah KORTE' }}
                        </button>
                    </div>

                    <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:12px;background:#fafafa;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:12px;">
                            <span style="font-weight:500;color:#1a1a1a;">
                                @if ($activeTab === 'korwe')
                                    {{ number_format($summary['korwe_formed']) }}/{{ number_format($summary['korwe_target']) }}
                                @else
                                    {{ number_format($summary['korte_formed']) }}/{{ number_format($summary['korte_target']) }}
                                @endif
                            </span>
                            <span style="color:#666;">
                                @if ($activeTab === 'korwe')
                                    {{ number_format($summary['korwe_percent'], 1) }}%
                                @else
                                    {{ number_format($summary['korte_percent'], 1) }}%
                                @endif
                            </span>
                        </div>
                        <div style="margin-top:8px;height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ $activeTab === 'korwe' ? min(100, max(0, $summary['korwe_percent'])) : min(100, max(0, $summary['korte_percent'])) }}%;background:#fe5000;border-radius:999px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding:14px 20px 0;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    @if ($activeTab === 'korwe')
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                            <div>
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Mapping RW</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">{{ $this->rwList->count() }} RW · urut berdasarkan prioritas pembentukan KORWE</div>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                    @foreach ($rwStatusFilters as $filter)
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
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:11px;color:#888;">
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:999px;background:#16a34a;display:inline-block;"></span>Terbentuk</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:999px;background:#f59e0b;display:inline-block;"></span>Proses</span>
                                <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:999px;background:#d4d4d8;display:inline-block;"></span>Belum</span>
                            </div>
                        </div>

                        @if ($this->rwList->isEmpty())
                            <div style="text-align:center;padding:36px 18px;font-size:12px;color:#888;border:0.5px dashed #d4d4d8;border-radius:12px;background:#fafafa;">
                                Data RW belum tersedia. Jalankan <code style="background:#f4f4f5;border-radius:6px;padding:2px 6px;font-size:11px;">php artisan import:data-rw</code>
                            </div>
                        @elseif ($filteredRwList->isEmpty())
                            <div style="text-align:center;padding:36px 18px;font-size:12px;color:#888;border:0.5px dashed #d4d4d8;border-radius:12px;background:#fafafa;">
                                Tidak ada RW dengan status <strong style="color:#1a1a1a;">{{ \App\Models\TargetWilayah::STATUS_CONFIG[$this->rwStatusFilter]['label'] ?? $this->rwStatusFilter }}</strong> pada desa ini.
                            </div>
                        @else
                            <div style="display:grid;gap:8px;">
                            @foreach ($filteredRwList as $rw)
                                @php
                                    $cfg = $rw->status_config;
                                    $korwe = $rw->korwe;
                                    $profil = $profilMap->get($rw->nomor_rw);
                                    $hasProfil = $profil && $profil->completion_percent > 0;
                                    $profilComplete = $profil && $profil->is_complete;
                                    $hasKorwe = $korwe && $korwe->status === 'terbentuk';
                                    $inProcess = $korwe && $korwe->status === 'proses';
                                    $accentColor = $hasKorwe
                                        ? '#16a34a'
                                        : ($inProcess
                                            ? '#f59e0b'
                                            : ($rw->prioritas_urutan <= 2 ? '#94a3b8' : '#d1d5db'));
                                    $rowStyle = 'border:0.5px solid #e5e7eb;background:#f3f4f6;box-shadow:inset 3px 0 0 ' . $accentColor . ';';
                                @endphp
                                <div style="border-radius:12px;padding:14px;{{ $rowStyle }}">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                        <div style="display:flex;align-items:flex-start;gap:12px;min-width:0;flex:1;">
                                            @if ($hasKorwe)
                                                <div style="width:28px;height:28px;border-radius:8px;background:#16a34a;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;">✓</div>
                                            @elseif ($inProcess)
                                                <div style="width:28px;height:28px;border-radius:8px;background:#f59e0b;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0;">⟳</div>
                                            @else
                                                <div style="width:28px;height:28px;border-radius:8px;border:2px solid #d4d4d8;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:10px;font-weight:700;flex-shrink:0;">#{{ $rw->prioritas_urutan }}</div>
                                            @endif

                                            <div style="min-width:0;">
                                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                                <div style="font-size:13px;font-weight:500;color:#1a1a1a;">RW {{ $rw->nomor_rw }}</div>
                                                <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:500;background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">{{ $cfg['label'] }}</span>
                                                @if ($profilComplete)
                                                    <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:600;background:#dbeafe;color:#1d4ed8;">Profil ✓</span>
                                                @endif
                                                @if ($rw->prioritas_urutan <= 2 && ! $hasKorwe)
                                                    <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:600;background:#ffedd5;color:#c2410c;">PRIORITAS</span>
                                                @endif
                                            </div>
                                            <div style="font-size:12px;color:#666;margin-top:6px;">
                                                @if ($hasKorwe)
                                                    {{ $korwe->nama_koordinator ?: 'Koordinator belum diisi' }} ({{ $korwe->no_hp ?: '-' }})
                                                @elseif ($inProcess)
                                                    {{ $korwe->nama_koordinator ?: 'Koordinator sedang diproses' }} (sedang diproses)
                                                @else
                                                    Belum ada koordinator
                                                @endif
                                            </div>
                                            <div style="font-size:11px;color:#888;margin-top:6px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                <span>DPT: {{ number_format($rw->dpt) }}</span>
                                                <span>{{ number_format($rw->jumlah_rt) }} RT</span>
                                                <span>Est. PKS: ~{{ number_format($rw->estimasi_pks) }}</span>
                                                <span>Share: {{ number_format((float) $rw->estimasi_share * 100, 1) }}%</span>
                                                <span>Rank: #{{ number_format($rw->estimasi_ranking) }}</span>
                                            </div>
                                            <div style="font-size:10px;color:#9ca3af;margin-top:4px;">
                                                Z:{{ number_format($rw->gen_z) }} · Mil:{{ number_format($rw->millennial) }} · X:{{ number_format($rw->gen_x) }} · Boom:{{ number_format($rw->boomer) }}
                                            </div>
                                            @if (($korwe?->tanggal_terbentuk) || ($korwe?->catatan))
                                                <div style="font-size:11px;color:#888;margin-top:6px;">
                                                    {{ $korwe?->tanggal_terbentuk ? 'Tanggal: ' . $korwe->tanggal_terbentuk->format('d M Y') : '' }}
                                                    @if ($korwe?->tanggal_terbentuk && $korwe?->catatan)
                                                        <span style="margin:0 6px;">·</span>
                                                    @endif
                                                    {{ $korwe?->catatan ?: '' }}
                                                </div>
                                            @endif
                                        </div>
                                        </div>

                                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                            @if ($hasKorwe)
                                                <div style="text-align:right;min-width:110px;">
                                                    <div style="font-size:12px;font-weight:600;color:#166534;">{{ $korwe->nama_koordinator }}</div>
                                                    <div style="font-size:10px;color:#16a34a;margin-top:2px;">{{ $korwe->no_hp ?: '-' }}</div>
                                                    <div style="display:flex;justify-content:flex-end;gap:6px;margin-top:8px;">
                                                        <button wire:click="editKorwe('{{ $korwe->id }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;font-weight:500;cursor:pointer;">Edit</button>
                                                        <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:11px;font-weight:500;cursor:pointer;">{{ $profilComplete ? 'Profil ✓' : ($hasProfil ? 'Profil ' . $profil->completion_percent . '%' : 'Profil') }}</button>
                                                        <button wire:click="hapus('{{ $korwe->id }}')" onclick="return confirm('Yakin ingin menghapus data ini?')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #fecaca;background:#fef2f2;color:#b91c1c;font-size:11px;font-weight:500;cursor:pointer;">Hapus</button>
                                                    </div>
                                                </div>
                                            @elseif ($inProcess)
                                                <div style="text-align:right;min-width:110px;">
                                                    <div style="font-size:12px;font-weight:600;color:#b45309;">{{ $korwe->nama_koordinator ?: 'Dalam proses' }}</div>
                                                    <div style="display:flex;justify-content:flex-end;gap:6px;margin-top:8px;">
                                                        <button wire:click="editKorwe('{{ $korwe->id }}')" type="button" style="padding:6px 10px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:11px;font-weight:500;cursor:pointer;">Edit</button>
                                                        <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:11px;font-weight:500;cursor:pointer;">{{ $profilComplete ? 'Profil ✓' : ($hasProfil ? 'Profil ' . $profil->completion_percent . '%' : 'Profil') }}</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                                    <button wire:click="assignKorwe('{{ $rw->nomor_rw }}')" type="button" style="padding:6px 10px;border-radius:8px;border:{{ $rw->prioritas_urutan <= 2 ? 'none' : '0.5px solid #d4d4d8' }};background:{{ $rw->prioritas_urutan <= 2 ? '#fe5000' : 'white' }};color:{{ $rw->prioritas_urutan <= 2 ? 'white' : '#444' }};font-size:11px;font-weight:600;cursor:pointer;">+ Assign</button>
                                                    <button wire:click="openProfil('{{ $rw->nomor_rw }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid {{ $profilComplete ? '#3b82f6' : ($hasProfil ? '#f59e0b' : '#d4d4d8') }};background:{{ $profilComplete ? '#eff6ff' : ($hasProfil ? '#fffbeb' : 'white') }};color:{{ $profilComplete ? '#2563eb' : ($hasProfil ? '#d97706' : '#444') }};font-size:11px;font-weight:500;cursor:pointer;">{{ $profilComplete ? 'Profil ✓' : ($hasProfil ? 'Profil ' . $profil->completion_percent . '%' : 'Profil') }}</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>

                            @php
                                $terbentuk = $this->rwList->filter(fn ($r) => $r->korwe && $r->korwe->status === 'terbentuk')->count();
                                $targetField = 'target_korwe_' . $this->activeYear;
                                $target = (int) ($targetWilayah->$targetField ?? 0);
                                $total = $this->rwList->count();
                                $persen = $target > 0 ? round(($terbentuk / $target) * 100) : 0;
                                $prioritasRw = $this->rwList->filter(fn ($r) => $r->prioritas_urutan <= 2 && (! $r->korwe || $r->korwe->status !== 'terbentuk'));
                            @endphp

                            <div style="margin-top:12px;border:0.5px solid #e5e5e5;border-radius:10px;padding:12px;background:#fafafa;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;">
                                    <span style="color:#666;">KORWE terbentuk:</span>
                                    <div style="flex:1;height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;min-width:160px;">
                                        <div style="height:100%;width:{{ min($persen, 100) }}%;background:#22c55e;border-radius:999px;"></div>
                                    </div>
                                    <span style="font-weight:600;color:#1f2937;">{{ number_format($terbentuk) }} / {{ number_format($target) }} target ({{ number_format($persen) }}%)</span>
                                    <span style="color:#9ca3af;">dari {{ number_format($total) }} RW total</span>
                                </div>
                            </div>
                            <div style="margin-top:8px;border:0.5px solid #e5e5e5;border-radius:10px;padding:12px;background:#fafafa;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;">
                                    <span style="color:#666;">Profil terisi:</span>
                                    <div style="flex:1;height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;min-width:160px;">
                                        <div style="height:100%;width:{{ $profilStats['pct_profil'] }}%;background:#3b82f6;border-radius:999px;"></div>
                                    </div>
                                    <span style="font-weight:600;color:#1f2937;">{{ number_format($profilStats['profil_terisi']) }} / {{ number_format($profilStats['total_rw']) }} RW ({{ number_format($profilStats['pct_profil']) }}%)</span>
                                </div>
                            </div>

                            @if ($prioritasRw->isNotEmpty())
                                <div style="margin-top:12px;padding:12px 14px;border-radius:10px;background:#dbeafe;border-left:3px solid #2563eb;font-size:12px;color:#1e3a5f;">
                                    <span style="font-weight:600;">Fokus berikutnya:</span>
                                    Bentuk KORWE di
                                    @foreach ($prioritasRw->take(5) as $prw)
                                        <strong>RW {{ $prw->nomor_rw }}</strong> (est. PKS ~{{ number_format($prw->estimasi_pks) }}, {{ number_format($prw->dpt) }} DPT){{ ! $loop->last ? ', ' : '' }}
                                    @endforeach
                                    @if ($prioritasRw->count() > 5)
                                        dan {{ number_format($prioritasRw->count() - 5) }} RW lainnya
                                    @endif
                                </div>
                            @endif
                        @endif
                    @else
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                            <div>
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Koordinator RT</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Pantau status pembentukan per RT, dikelompokkan per RW</div>
                            </div>
                            <div style="font-size:11px;color:#888;">Kelompok RW memudahkan input KORTE</div>
                        </div>

                        <div style="display:grid;gap:12px;">
                            @foreach ($korteGroups as $group)
                                <div style="border:0.5px solid #e5e5e5;border-radius:12px;background:#fafafa;overflow:hidden;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 14px;border-bottom:0.5px solid #e5e5e5;">
                                        <div style="font-size:13px;font-weight:500;color:#1a1a1a;">RW {{ $group['rw'] }}</div>
                                        <button wire:click="openCreateForm(null, '{{ $group['rw'] }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;font-weight:500;cursor:pointer;">+ Tambah KORTE</button>
                                    </div>

                                    <div style="display:grid;gap:8px;padding:12px;">
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
                                            <div style="border-radius:12px;padding:14px;{{ $rowStyle }}">
                                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                                    <div style="min-width:0;">
                                                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                                            <div style="font-size:13px;font-weight:500;color:#1a1a1a;">RT {{ $row['nomor'] }}</div>
                                                            <span style="padding:4px 10px;border-radius:999px;font-size:10px;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;{{ $badgeStyle }}">{{ $row['status'] }}</span>
                                                        </div>
                                                        <div style="font-size:12px;color:#666;margin-top:6px;">
                                                            @if ($row['status'] === 'terbentuk')
                                                                {{ $row['nama'] ?: 'Koordinator belum diisi' }} ({{ $row['no_hp'] ?: '-' }})
                                                            @elseif ($row['status'] === 'proses')
                                                                {{ $row['nama'] ?: 'Koordinator sedang diproses' }} (sedang diproses)
                                                            @else
                                                                Belum ada koordinator
                                                            @endif
                                                        </div>
                                                        @if ($row['tanggal'] || $row['catatan'])
                                                            <div style="font-size:11px;color:#888;margin-top:6px;">
                                                                {{ $row['tanggal'] ? 'Tanggal: ' . $row['tanggal'] : '' }}
                                                                @if ($row['tanggal'] && $row['catatan'])
                                                                    <span style="margin:0 6px;">·</span>
                                                                @endif
                                                                {{ $row['catatan'] ?: '' }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                                        @if ($row['status'] === 'belum')
                                                            <button wire:click="openCreateForm('{{ $row['nomor'] }}', '{{ $group['rw'] }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;font-weight:500;cursor:pointer;">+ Assign</button>
                                                        @else
                                                            <button wire:click="openEditForm('{{ $row['id'] }}')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;font-weight:500;cursor:pointer;">Edit</button>
                                                            @if ($row['id'])
                                                                <button wire:click="hapus('{{ $row['id'] }}')" onclick="return confirm('Yakin ingin menghapus data ini?')" type="button" style="padding:6px 10px;border-radius:8px;border:0.5px solid #fecaca;background:#fef2f2;color:#b91c1c;font-size:11px;font-weight:500;cursor:pointer;">Hapus</button>
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
                    @endif
                </div>
            </div>
            @endif

            @if ($activeMainTab === 'penggalang')
                @php $pgSum = $this->penggalangSummary; @endphp
                <div style="padding:0 20px 14px;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px;" class="detail-summary-grid">
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Terdaftar</div>
                                <div style="font-size:24px;font-weight:600;color:#ea580c;margin-top:4px;">{{ number_format($pgSum['total']) }}</div>
                                <div style="font-size:10px;color:#9ca3af;">target {{ number_format($pgSum['target']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Aktif</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ number_format($pgSum['aktif']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Warga terjangkau</div>
                                <div style="font-size:24px;font-weight:600;color:#16a34a;margin-top:4px;">{{ number_format($pgSum['jangkauan']) }}</div>
                                <div style="font-size:10px;color:#9ca3af;">target {{ number_format($pgSum['targetJangkauan']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Progress</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ $pgSum['target'] > 0 ? round($pgSum['total'] / $pgSum['target'] * 100) : 0 }}%</div>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">Daftar penggalang suara — {{ $targetWilayah->desa }}</div>
                            <button wire:click="openPenggalangForm" type="button" style="padding:7px 12px;border:none;border-radius:8px;background:#ea580c;color:white;font-size:12px;font-weight:600;cursor:pointer;">+ Tambah</button>
                        </div>

                        @forelse ($this->penggalangList->groupBy('nomor_rw') as $rw => $members)
                            <div style="margin-bottom:12px;">
                                <div style="font-size:10px;color:#888;font-weight:600;margin-bottom:6px;">RW {{ $rw }} ({{ $members->count() }} orang)</div>
                                @foreach ($members as $pg)
                                    <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:10px;border:0.5px solid #e5e7eb;background:#fff;margin-bottom:6px;font-size:12px;">
                                        <i class="ti ti-user" style="font-size:14px;color:#fe5000;" aria-hidden="true"></i>
                                        <div style="flex:1;min-width:0;">
                                            <span style="font-weight:600;color:#1a1a1a;">{{ $pg->nama }}</span>
                                            <span style="color:#888;margin-left:6px;">{{ $pg->no_wa ?? $pg->no_hp ?? '-' }}</span>
                                        </div>
                                        <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:#f4f4f5;color:#525252;">{{ \App\Models\PenggalangSuara::SUMBER_OPTIONS[$pg->sumber] ?? $pg->sumber }}</span>
                                        <span style="font-size:10px;color:#888;">{{ number_format($pg->realisasi_jangkauan) }}/{{ number_format($pg->target_jangkauan) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div style="text-align:center;padding:32px 18px;font-size:13px;color:#9ca3af;">Belum ada penggalang suara terdaftar</div>
                        @endforelse
                    </div>
                </div>
            @endif

            @if ($activeMainTab === 'upa')
                @php $upaSum = $this->upaSummary; @endphp
                <div style="padding:0 20px 14px;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:14px;" class="detail-summary-grid">
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Anggota UPA</div>
                                <div style="font-size:24px;font-weight:600;color:#2563eb;margin-top:4px;">{{ number_format($upaSum['total_member']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">RW dengan UPA</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ number_format($upaSum['rw_dengan_upa']) }} / {{ number_format($upaSum['total_rw']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Coverage</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ number_format($upaSum['pct']) }}%</div>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">Anggota UPA per RW</div>
                            <button wire:click="openUpaForm" type="button" style="padding:7px 12px;border:none;border-radius:8px;background:#2563eb;color:white;font-size:12px;font-weight:600;cursor:pointer;">+ Tambah</button>
                        </div>

                        @foreach ($this->upaList->groupBy('nomor_rw') as $rw => $members)
                            <div style="margin-bottom:12px;padding:12px;border-radius:12px;border:0.5px solid #e5e7eb;background:#fafafa;">
                                <div style="font-size:10px;color:#888;font-weight:600;margin-bottom:8px;">RW {{ $rw }}</div>
                                @foreach ($members as $upa)
                                    @php
                                        $jabatanStyle = match ($upa->jabatan) {
                                            'pembina' => 'background:#f3e8ff;color:#7e22ce;',
                                            'ketua' => 'background:#dbeafe;color:#1d4ed8;',
                                            default => 'background:#f4f4f5;color:#525252;',
                                        };
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:12px;">
                                        <span style="font-size:10px;padding:3px 7px;border-radius:999px;font-weight:600;{{ $jabatanStyle }}">{{ \App\Models\UpaRwMember::JABATAN_OPTIONS[$upa->jabatan] ?? $upa->jabatan }}</span>
                                        <span style="font-weight:600;color:#1a1a1a;">{{ $upa->nama }}</span>
                                        <span style="color:#888;">{{ $upa->no_hp ?? '-' }}</span>
                                        <span style="margin-left:auto;font-size:10px;color:#888;">{{ \App\Models\UpaRwMember::ASAL_OPTIONS[$upa->asal] ?? $upa->asal }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        @if ($this->upaList->isEmpty())
                            <div style="text-align:center;padding:32px 18px;font-size:13px;color:#9ca3af;">Belum ada anggota UPA RW</div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($activeMainTab === 'saksi')
                @php
                    $skSum = $this->saksiSummary;
                    $allKorte = \App\Models\Korte::query()
                        ->where('target_wilayah_id', $targetWilayah->id)
                        ->where('status', 'terbentuk')
                        ->orderBy('nomor_rw')
                        ->orderBy('nomor_rt')
                        ->get();
                @endphp
                <div style="padding:0 20px 14px;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px;" class="detail-summary-grid">
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Total TPS</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ number_format($skSum['total_tps']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Saksi ditunjuk</div>
                                <div style="font-size:24px;font-weight:600;color:#16a34a;margin-top:4px;">{{ number_format($skSum['total_saksi']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Terkonfirmasi</div>
                                <div style="font-size:24px;font-weight:600;color:#16a34a;margin-top:4px;">{{ number_format($skSum['terkonfirmasi']) }}</div>
                            </div>
                            <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                                <div style="font-size:11px;color:#888;">Coverage TPS</div>
                                <div style="font-size:24px;font-weight:600;color:#18181b;margin-top:4px;">{{ number_format($skSum['pct']) }}%</div>
                            </div>
                        </div>

                        <div style="font-size:12px;font-weight:600;color:#1a1a1a;margin-bottom:4px;">KORTE yang menjadi saksi TPS</div>
                        <div style="font-size:10px;color:#9ca3af;margin-bottom:10px;">Klik toggle di KORTE untuk menandai sebagai saksi, lalu assign TPS.</div>

                        @forelse ($allKorte as $korte)
                            <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:10px;border:0.5px solid {{ $korte->is_saksi_tps ? '#bbf7d0' : '#e5e7eb' }};background:{{ $korte->is_saksi_tps ? '#f0fdf4' : 'white' }};margin-bottom:6px;font-size:12px;">
                                <button wire:click="toggleSaksi('{{ $korte->id }}')" type="button" style="width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:{{ $korte->is_saksi_tps ? 'none' : '1px solid #d4d4d8' }};background:{{ $korte->is_saksi_tps ? '#22c55e' : 'white' }};color:white;cursor:pointer;">
                                    @if ($korte->is_saksi_tps)
                                        <i class="ti ti-check" style="font-size:12px;" aria-hidden="true"></i>
                                    @endif
                                </button>
                                <div style="flex:1;min-width:0;">
                                    <span style="font-weight:600;color:#1a1a1a;">{{ $korte->nama_koordinator }}</span>
                                    <span style="color:#888;margin-left:6px;">RT {{ $korte->nomor_rt }} · RW {{ $korte->nomor_rw }}</span>
                                </div>
                                @if ($korte->is_saksi_tps)
                                    <select wire:change="assignTps('{{ $korte->id }}', $event.target.value)" style="height:28px;font-size:10px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 8px;">
                                        <option value="">Pilih TPS</option>
                                        @for ($t = 1; $t <= $targetWilayah->jumlah_tps; $t++)
                                            @php $tpsLabel = 'TPS ' . str_pad((string) $t, 3, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $tpsLabel }}" @selected($korte->assigned_tps === $tpsLabel)>{{ $tpsLabel }}</option>
                                        @endfor
                                    </select>
                                    <span style="font-size:10px;padding:3px 7px;border-radius:999px;font-weight:600;background:{{ $korte->status_saksi === 'terkonfirmasi' ? '#dcfce7' : '#fef3c7' }};color:{{ $korte->status_saksi === 'terkonfirmasi' ? '#166534' : '#b45309' }};">
                                        {{ $korte->status_saksi === 'terkonfirmasi' ? 'Confirmed' : 'Siap' }}
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div style="text-align:center;padding:32px 18px;font-size:13px;color:#9ca3af;">Belum ada KORTE terbentuk. Bentuk KORTE dulu di tab KORWE/KORTE.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <div style="padding:14px 20px 20px;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <div style="width:38px;height:38px;border-radius:10px;background:#fe500015;display:flex;align-items:center;justify-content:center;color:#fe5000;font-size:18px;">i</div>
                        <div>
                            <div style="font-size:14px;font-weight:500;color:#1a1a1a;">Info Target</div>
                            <div style="font-size:12px;color:#666;margin-top:4px;">
                                Target rata-rata per RW: {{ number_format((float) $targetWilayah->target_avg_per_rw, 2) }} suara ·
                                per RT: {{ number_format((float) $targetWilayah->target_avg_per_rt, 2) }} suara ·
                                per TPS: {{ number_format((float) $targetWilayah->target_avg_per_tps, 2) }} suara ·
                                per Rumah/RT: {{ number_format((float) $targetWilayah->target_avg_per_rumah, 2) }} suara
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($showPenggalangForm)
        <div wire:click.self="resetPenggalangForm" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);padding:24px;backdrop-filter:blur(4px);">
            <div style="width:100%;max-width:680px;border-radius:24px;border:0.5px solid #e5e5e5;background:white;box-shadow:0 25px 50px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:0.5px solid #e5e5e5;">
                    <div>
                        <div style="font-size:18px;font-weight:600;color:#1a1a1a;">Tambah Penggalang Suara</div>
                        <div style="font-size:12px;color:#888;margin-top:4px;">{{ $targetWilayah->desa }}, {{ $targetWilayah->kecamatan }}</div>
                    </div>
                    <button wire:click="resetPenggalangForm" type="button" style="padding:8px 12px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">Tutup</button>
                </div>

                <div style="display:grid;gap:16px;padding:20px 22px;">
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Nama</label>
                            <input wire:model.defer="pgNama" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('pgNama') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RW</label>
                            <input wire:model.defer="pgRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('pgRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No HP</label>
                            <input wire:model.defer="pgHp" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No WA</label>
                            <input wire:model.defer="pgWa" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RT</label>
                            <input wire:model.defer="pgRt" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Sumber</label>
                            <select wire:model.defer="pgSumber" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                @foreach (\App\Models\PenggalangSuara::SUMBER_OPTIONS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Target Jangkauan</label>
                            <input wire:model.defer="pgTarget" type="number" min="0" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
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

    @if ($showUpaForm)
        <div wire:click.self="resetUpaForm" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);padding:24px;backdrop-filter:blur(4px);">
            <div style="width:100%;max-width:680px;border-radius:24px;border:0.5px solid #e5e5e5;background:white;box-shadow:0 25px 50px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:0.5px solid #e5e5e5;">
                    <div>
                        <div style="font-size:18px;font-weight:600;color:#1a1a1a;">Tambah Anggota UPA RW</div>
                        <div style="font-size:12px;color:#888;margin-top:4px;">{{ $targetWilayah->desa }}, {{ $targetWilayah->kecamatan }}</div>
                    </div>
                    <button wire:click="resetUpaForm" type="button" style="padding:8px 12px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">Tutup</button>
                </div>

                <div style="display:grid;gap:16px;padding:20px 22px;">
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Nama</label>
                            <input wire:model.defer="upaNama" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('upaNama') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RW</label>
                            <input wire:model.defer="upaRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('upaRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No HP</label>
                            <input wire:model.defer="upaHp" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Jabatan</label>
                            <select wire:model.defer="upaJabatan" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                @foreach (\App\Models\UpaRwMember::JABATAN_OPTIONS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Asal</label>
                            <select wire:model.defer="upaAsal" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                @foreach (\App\Models\UpaRwMember::ASAL_OPTIONS as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Korwe terkait (opsional)</label>
                            <select wire:model.defer="upaKorweId" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                <option value="">- Pilih KORWE -</option>
                                @foreach ($targetWilayah->korwes->where('status', 'terbentuk') as $korwe)
                                    <option value="{{ $korwe->id }}">RW {{ $korwe->nomor_rw }} - {{ $korwe->nama_koordinator ?: 'KORWE' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Korte terkait (opsional)</label>
                            <select wire:model.defer="upaKorteId" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                <option value="">- Pilih KORTE -</option>
                                @foreach ($targetWilayah->kortes->where('status', 'terbentuk') as $korte)
                                    <option value="{{ $korte->id }}">RW {{ $korte->nomor_rw }} · RT {{ $korte->nomor_rt }} - {{ $korte->nama_koordinator ?: 'KORTE' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="resetUpaForm" type="button" style="padding:10px 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:12px;font-weight:500;cursor:pointer;">Batal</button>
                    <button wire:click="simpanUpa" type="button" style="padding:10px 14px;border-radius:10px;border:none;background:#2563eb;color:white;font-size:12px;font-weight:500;cursor:pointer;">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showForm)
        <div wire:click.self="closeForm" style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);padding:24px;backdrop-filter:blur(4px);">
            <div style="width:100%;max-width:760px;border-radius:24px;border:0.5px solid #e5e5e5;background:white;box-shadow:0 25px 50px rgba(0,0,0,0.18);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:0.5px solid #e5e5e5;">
                    <div>
                        <div style="font-size:18px;font-weight:600;color:#1a1a1a;">{{ $editId ? 'Edit' : 'Tambah' }} {{ $activeTab === 'korwe' ? 'KORWE' : 'KORTE' }}</div>
                        <div style="font-size:12px;color:#888;margin-top:4px;">{{ $targetWilayah->desa }}, {{ $targetWilayah->kecamatan }}</div>
                    </div>
                    <button wire:click="closeForm" type="button" style="padding:8px 12px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">Tutup</button>
                </div>

                <div style="display:grid;gap:16px;padding:20px 22px;">
                    @if ($activeTab === 'korte')
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">RW Induk</label>
                            <input wire:model.defer="formParentRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('formParentRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">{{ $activeTab === 'korwe' ? 'Nomor RW' : 'Nomor RT' }}</label>
                        <input wire:model.defer="formNomorRw" type="text" placeholder="001" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                        @error('formNomorRw') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Nama Koordinator</label>
                            <input wire:model.defer="formNamaKoordinator" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('formNamaKoordinator') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">No HP</label>
                            <input wire:model.defer="formNoHp" type="text" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                            @error('formNoHp') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Status</label>
                            <select wire:model.live="formStatus" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;background:white;">
                                <option value="belum">Belum</option>
                                <option value="proses">Proses</option>
                                <option value="terbentuk">Terbentuk</option>
                            </select>
                            @error('formStatus') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                        </div>

                        @if ($formStatus === 'terbentuk')
                            <div>
                                <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Tanggal Terbentuk</label>
                                <input wire:model.defer="formTanggal" type="date" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;">
                                @error('formTanggal') <div style="margin-top:6px;font-size:11px;color:#dc2626;">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#444;margin-bottom:6px;">Catatan</label>
                        <textarea wire:model.defer="formCatatan" rows="4" style="width:100%;padding:11px 12px;border-radius:12px;border:0.5px solid #e5e5e5;font-size:13px;color:#1f2937;resize:vertical;"></textarea>
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

    @if ($showProfilDrawer && $profilRwId)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="closeProfilDrawer"></div>
        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100%;background:white;box-shadow:-8px 0 24px rgba(0,0,0,0.16);z-index:50;overflow-y:auto;">
            <div style="position:sticky;top:0;background:white;border-bottom:0.5px solid #e5e5e5;padding:16px;z-index:10;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:#1a1a1a;">Profil RW {{ $profilRwId }} - {{ $targetWilayah->desa }}</div>
                        <div style="font-size:11px;color:#888;margin-top:4px;">{{ $targetWilayah->kecamatan }} · {{ $targetWilayah->dapil }}</div>
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

            <div style="padding:16px;display:grid;gap:16px;">
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

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#d97706;">Profil wilayah</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Tipologi RW</label>
                        <select wire:model="profilData.tipologi" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;">
                            <option value="">- Pilih -</option>
                            @foreach (\App\Models\ProfilRw::TIPOLOGI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Sumber ekonomi dominan</label>
                        <select wire:model="profilData.ekonomi_dominan" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;">
                            <option value="">- Pilih -</option>
                            @foreach (\App\Models\ProfilRw::EKONOMI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Profil umum warga</label>
                        <textarea wire:model="profilData.profil_warga" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Agama, kebiasaan, pragmatisme pemilih..."></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Suara PKS 2019</label>
                            <input wire:model="profilData.suara_pks_2019" type="number" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="0">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Jumlah KTA</label>
                            <input wire:model="profilData.jumlah_kta" type="number" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Faktor penyebab menang/kalah</label>
                        <textarea wire:model="profilData.faktor_penyebab" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Caleg lokal, tokoh kuat, pragmatisme..."></textarea>
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#16a34a;">Infrastruktur partai</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Anggota PKS di RW</label>
                        <textarea wire:model="profilData.anggota_pks" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Nama anggota"></textarea>
                    </div>
                    @php
                        $infraItems = [
                            ['field' => 'upa_rw', 'label' => 'Pengajian RW', 'name_field' => 'upa_rw_nama', 'placeholder' => 'Nama pembina'],
                            ['field' => 'rki', 'label' => 'RKI (Rumah Keluarga Indonesia)', 'name_field' => 'rki_nama', 'placeholder' => 'Nama penggerak'],
                            ['field' => 'senam', 'label' => 'Titik Senam PKS', 'name_field' => 'senam_nama', 'placeholder' => 'Nama instruktur'],
                            ['field' => 'relawan_milenial', 'label' => 'Relawan Muda', 'name_field' => 'relawan_milenial_nama', 'placeholder' => 'Nama + jabatan'],
                        ];
                    @endphp
                    @foreach ($infraItems as $item)
                        <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                                <span style="font-size:12px;font-weight:500;color:#1f2937;">{{ $item['label'] }}</span>
                                <select wire:model.live="profilData.{{ $item['field'] }}_status" style="height:30px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                    <option value="belum">Belum</option>
                                    <option value="sudah">Sudah</option>
                                </select>
                            </div>
                            @if (($profilData[$item['field'] . '_status'] ?? 'belum') === 'sudah')
                                <input wire:model="profilData.{{ $item['name_field'] }}" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;" placeholder="{{ $item['placeholder'] }}">
                            @endif
                        </div>
                    @endforeach
                    <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                            <span style="font-size:12px;font-weight:500;color:#1f2937;">Aleg terpilih di RW (Bisa partai lain)</span>
                            <select wire:model.live="profilData.caleg_terpilih_ada" style="height:30px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        @if (($profilData['caleg_terpilih_ada'] ?? false))
                            <input wire:model="profilData.caleg_terpilih_nama" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;" placeholder="Nama caleg">
                        @endif
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#dc2626;">Peta politik lokal</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Afiliasi Ketua RW & RT</label>
                        <textarea wire:model="profilData.afiliasi_rw_rt" rows="3" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Ketua RW: Nama - Partai&#10;RT 1: Nama - Partai"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Afiliasi Kader Posyandu & DKM</label>
                        <textarea wire:model="profilData.afiliasi_posyandu_dkm" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Nama - organisasi - partai"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Pengurus kompetitor?</label>
                            <select wire:model.live="profilData.kompetitor_status" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if (($profilData['kompetitor_status'] ?? '') === 'ada')
                                <input wire:model="profilData.kompetitor_detail" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;margin-top:6px;" placeholder="Nama + partai">
                            @endif
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Tim sukses lain?</label>
                            <select wire:model.live="profilData.tim_sukses_status" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if (($profilData['tim_sukses_status'] ?? '') === 'ada')
                                <input wire:model="profilData.tim_sukses_detail" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;margin-top:6px;" placeholder="Nama + partai">
                            @endif
                        </div>
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#ea580c;">Strategi & penanggung jawab</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Strategi mencapai target suara</label>
                        <textarea wire:model="profilData.strategi" rows="3" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Rencana aksi untuk meningkatkan suara"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Penanggung jawab dakwah di RW</label>
                        <input wire:model="profilData.penanggung_jawab" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="Nama penanggung jawab">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Keterangan lain</label>
                        <textarea wire:model="profilData.keterangan_lain" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>
            </div>
            <div style="position:sticky;bottom:0;background:white;border-top:0.5px solid #e5e5e5;padding:16px;display:flex;gap:8px;">
                <button wire:click="simpanProfil" type="button" style="flex:1;height:40px;border:none;border-radius:10px;background:#ea580c;color:white;font-size:13px;font-weight:600;cursor:pointer;">Simpan Profil</button>
                <button wire:click="closeProfilDrawer" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;cursor:pointer;">Batal</button>
            </div>
        </div>
    @endif

    <style>
        @media (max-width: 1200px) {
            .detail-summary-grid,
            .detail-top-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .detail-summary-grid,
            .detail-top-grid,
            .detail-form-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
