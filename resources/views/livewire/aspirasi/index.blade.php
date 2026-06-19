@php
    $summary = $this->pipelineSummary;
    $list = $this->aspirasiList;
    $detail = $this->detail;
    $kinerja = $this->kinerjaDewan;
    $stuckList = $this->stuckAspirasi;
    $publicPreview = $this->publikPreview;
    $user = auth()->user();
@endphp

<div style="min-height:100vh;background:#fafafa;">
    <div style="width:100%;margin:0;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-radius:14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:15px;font-weight:500;">Aspirasi & POKIR</div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                <select wire:model.live="selectedDapil" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:160px;background:#fff7f1;color:#993c1d;font-weight:500;" @if ($this->accessScope['is_dapil'] ?? false) disabled @endif>
                    <option value="">Semua dapil</option>
                    @foreach ($this->dapilOptions as $dapil)
                        <option value="{{ $dapil }}">{{ $dapil }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:170px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua status</option>
                    @foreach (\App\Models\Aspirasi::STATUS_PIPELINE as $key => $cfg)
                        <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterKategori" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:170px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua kategori</option>
                    @foreach (\App\Models\Aspirasi::KATEGORI_OPTIONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari judul, pelapor, desa" style="width:220px;height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;background:#27272a;color:#f4f4f5;">
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;flex:0 0 auto;">
                {{ $user?->initials() }}
                </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e7eb;border-top:none;border-radius:0 0 14px 14px;padding:18px 20px 20px;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:10px;background:#ecfeff;border:0.5px solid #a5f3fc;color:#0f766e;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:22px;font-weight:600;color:#111827;margin:0;">Aspirasi & POKIR</h1>
                    <div style="font-size:12px;color:#6b7280;margin-top:4px;">Monitoring aspirasi warga dari tahap diterima hingga terealisasi, lengkap dengan draft POKIR dan reminder.</div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
                    <button wire:click="openForm" type="button" style="height:40px;padding:0 16px;border:none;border-radius:10px;background:#0ea5e9;color:white;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
                        + Input Aspirasi
                    </button>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:16px;" class="aspirasi-summary-grid">
                <div style="padding:10px 12px;border-radius:12px;background:#f8fafc;border:0.5px solid #e2e8f0;min-width:120px;">
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;">Total Aspirasi</div>
                    <div style="font-size:24px;font-weight:700;color:#0f172a;margin-top:4px;">{{ number_format($summary['total']) }}</div>
                </div>
                <div style="padding:10px 12px;border-radius:12px;background:#fff7ed;border:0.5px solid #fed7aa;min-width:120px;">
                    <div style="font-size:11px;color:#c2410c;text-transform:uppercase;">Belum Assign</div>
                    <div style="font-size:24px;font-weight:700;color:#9a3412;margin-top:4px;">{{ number_format($summary['belum_assign']) }}</div>
                </div>
                <div style="padding:10px 12px;border-radius:12px;background:#fef2f2;border:0.5px solid #fecaca;min-width:120px;">
                    <div style="font-size:11px;color:#b91c1c;text-transform:uppercase;">Stuck &gt; 14 Hari</div>
                    <div style="font-size:24px;font-weight:700;color:#991b1b;margin-top:4px;">{{ number_format($summary['stuck']) }}</div>
                </div>
            </div>

            <div style="margin-top:18px;display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;" class="aspirasi-pipeline-grid">
                @foreach ($this->pipelineStages as $stage)
                    <button wire:click="$set('filterStatus', '{{ $stage['key'] }}')" type="button" style="text-align:left;border:none;border-radius:14px;padding:14px;background:{{ $filterStatus === $stage['key'] ? $stage['bg'] : 'white' }};border:0.5px solid {{ $filterStatus === $stage['key'] ? $stage['color'] : '#e5e7eb' }};cursor:pointer;position:relative;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                            <div style="width:38px;height:38px;border-radius:999px;background:{{ $stage['color'] }};color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $loop->iteration }}</div>
                            <div style="font-size:24px;font-weight:700;color:#111827;">{{ number_format($stage['count']) }}</div>
                        </div>
                        <div style="font-size:12px;font-weight:600;color:#111827;margin-top:10px;">{{ $stage['label'] }}</div>
                        @if (! $loop->last)
                            <div style="position:absolute;right:-8px;top:28px;width:16px;height:2px;background:#cbd5e1;"></div>
                        @endif
                    </button>
                @endforeach
            </div>

            @if ($stuckList->isNotEmpty())
                <div style="margin-top:14px;padding:12px 14px;border-radius:12px;background:#fff7ed;border:0.5px solid #fed7aa;">
                    <div style="font-size:12px;font-weight:700;color:#9a3412;">Perlu ditindaklanjuti</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                        @foreach ($stuckList as $stuck)
                            <button wire:click="selectAspirasi('{{ $stuck->id }}')" type="button" style="padding:6px 10px;border:none;border-radius:999px;background:white;color:#9a3412;font-size:11px;font-weight:600;cursor:pointer;border:0.5px solid #fdba74;">
                                {{ $stuck->judul }} · {{ $stuck->durasi_sipd }} hari
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:18px;">
                @php
                    $tabs = [
                        'daftar' => 'Daftar Aspirasi',
                        'kinerja' => 'Kinerja Dewan',
                        'publik' => 'Tampilan Publik',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <button wire:click="setActiveTab('{{ $key }}')" type="button" style="padding:8px 14px;border-radius:999px;border:0.5px solid {{ $activeTab === $key ? '#0ea5e9' : '#e5e7eb' }};background:{{ $activeTab === $key ? '#e0f2fe' : 'white' }};color:{{ $activeTab === $key ? '#0369a1' : '#6b7280' }};font-size:12px;font-weight:600;cursor:pointer;">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if ($activeTab === 'daftar')
                <div style="display:grid;grid-template-columns:minmax(0,1.65fr) minmax(320px,.95fr);gap:16px;margin-top:16px;align-items:start;" class="aspirasi-main-grid">
                    <div style="display:grid;gap:10px;">
                        @forelse ($list as $item)
                            @php
                                $statusCfg = $item->status_config;
                                $urgensiCfg = $item->urgensi_config;
                            @endphp
                            <div style="border-radius:14px;padding:14px;background:{{ $selectedAspirasiId === $item->id ? '#f0f9ff' : 'white' }};border:0.5px solid {{ $selectedAspirasiId === $item->id ? '#7dd3fc' : '#e5e7eb' }};box-shadow:{{ $selectedAspirasiId === $item->id ? '0 10px 24px rgba(14,165,233,.10)' : '0 2px 8px rgba(15,23,42,.04)' }};">
                                <div wire:click="selectAspirasi('{{ $item->id }}')" style="cursor:pointer;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                            <span style="padding:4px 9px;border-radius:999px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:10px;font-weight:700;">{{ $statusCfg['label'] }}</span>
                                            <span style="padding:4px 9px;border-radius:999px;background:#f1f5f9;color:#334155;font-size:10px;font-weight:700;">{{ \App\Models\Aspirasi::KATEGORI_OPTIONS[$item->kategori] ?? ucfirst($item->kategori) }}</span>
                                            <span style="padding:4px 9px;border-radius:999px;background:{{ $urgensiCfg['bg'] }};color:{{ $urgensiCfg['color'] }};font-size:10px;font-weight:700;">{{ $urgensiCfg['label'] }}</span>
                                        </div>
                                        <div style="font-size:11px;color:#94a3b8;">{{ $item->created_at?->diffForHumans() }}</div>
                                    </div>
                                    <div style="font-size:15px;font-weight:700;color:#111827;margin-top:10px;line-height:1.45;">{{ $item->judul }}</div>
                                    <div style="font-size:12px;color:#6b7280;line-height:1.6;margin-top:4px;">{{ \Illuminate\Support\Str::limit($item->deskripsi, 140) }}</div>
                                    <div style="display:flex;gap:12px;flex-wrap:wrap;font-size:11px;color:#64748b;margin-top:10px;">
                                        <span><i class="ti ti-user" style="font-size:12px;"></i> {{ $item->nama_pelapor }}</span>
                                        <span><i class="ti ti-map-pin" style="font-size:12px;"></i> {{ $item->desa ?: '-' }}, {{ $item->kecamatan ?: '-' }}</span>
                                        <span><i class="ti ti-building-bank" style="font-size:12px;"></i> {{ $item->assignedDewan?->nama ?? 'Belum assign' }}</span>
                                        <span><i class="ti ti-source-code" style="font-size:12px;"></i> {{ \App\Models\Aspirasi::SUMBER_OPTIONS[$item->sumber] ?? ucfirst($item->sumber) }}</span>
                                    </div>
                                </div>

                                @if (! $item->assigned_dewan_id)
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:12px;padding-top:12px;border-top:0.5px solid #e2e8f0;">
                                        <select wire:model="assignSelection.{{ $item->id }}" style="height:34px;min-width:220px;padding:0 30px 0 10px;border-radius:8px;border:0.5px solid #cbd5e1;background:white;font-size:12px;color:#111827;">
                                            <option value="">Assign ke dewan</option>
                                            @foreach ($this->dewanGroupedByDapil as $dapil => $dewanItems)
                                                @continue($dapil !== ($item->dapil ?: $dapil) && $item->dapil !== '')
                                                <optgroup label="{{ $dapil }}">
                                                    @foreach ($dewanItems as $dewan)
                                                        <option value="{{ $dewan->id }}">{{ $dewan->nama }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <button wire:click="assignDewan('{{ $item->id }}')" type="button" style="height:34px;padding:0 12px;border:none;border-radius:8px;background:#0ea5e9;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                            Assign
                                        </button>
                                    </div>
                                @elseif ($item->status === 'terealisasi')
                                    <div style="margin-top:12px;padding-top:12px;border-top:0.5px solid #e2e8f0;font-size:11px;color:#166534;">
                                        Anggaran {{ $item->anggaran_nominal ? 'Rp '.number_format((float) $item->anggaran_nominal, 0, ',', '.') : '-' }}
                                        @if ($item->foto_realisasi)
                                            · bukti realisasi tersedia
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div style="padding:32px;border-radius:14px;border:0.5px dashed #cbd5e1;background:#f8fafc;text-align:center;font-size:12px;color:#64748b;">
                                Belum ada aspirasi yang cocok dengan filter saat ini.
                            </div>
                        @endforelse

                        @if ($list->hasPages())
                            <div style="padding-top:6px;">
                                {{ $list->links() }}
                            </div>
                        @endif
                    </div>

                    <div style="border:0.5px solid #e5e7eb;border-radius:14px;background:white;padding:16px;align-self:start;position:sticky;top:16px;max-width:100%;overflow:hidden;">
                        @if ($detail)
                            @php
                                $statusCfg = $detail->status_config;
                                $urgensiCfg = $detail->urgensi_config;
                            @endphp
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;line-height:1.35;">{{ $detail->judul }}</div>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                        <span style="padding:4px 9px;border-radius:999px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:10px;font-weight:700;">{{ $statusCfg['label'] }}</span>
                                        <span style="padding:4px 9px;border-radius:999px;background:{{ $urgensiCfg['bg'] }};color:{{ $urgensiCfg['color'] }};font-size:10px;font-weight:700;">{{ $urgensiCfg['label'] }}</span>
                                    </div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $detail->created_at?->format('d M Y H:i') }}</div>
                            </div>

                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px;">
                                <div style="padding:10px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;">Pelapor</div>
                                    <div style="font-size:13px;font-weight:600;color:#111827;margin-top:4px;">{{ $detail->nama_pelapor }}</div>
                                    <div style="font-size:11px;color:#64748b;margin-top:3px;">{{ $detail->hp_pelapor ?: 'HP belum diisi' }}</div>
                                </div>
                                <div style="padding:10px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;">Lokasi</div>
                                    <div style="font-size:13px;font-weight:600;color:#111827;margin-top:4px;">{{ $detail->desa ?: '-' }}</div>
                                    <div style="font-size:11px;color:#64748b;margin-top:3px;">{{ $detail->kecamatan ?: '-' }} · {{ $detail->dapil }}</div>
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Deskripsi Aspirasi</div>
                                <div style="font-size:13px;color:#475569;line-height:1.7;margin-top:6px;">{{ $detail->deskripsi }}</div>
                            </div>

                            <div style="margin-top:14px;padding:12px;border-radius:12px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div>
                                        <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Dewan Penanggung Jawab</div>
                                        <div style="font-size:14px;font-weight:700;color:#111827;margin-top:4px;">{{ $detail->assignedDewan?->nama ?? 'Belum ditetapkan' }}</div>
                                        <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $detail->assignedDewan?->jabatan ?? '-' }} · {{ $detail->assignedDewan?->dapil ?? $detail->dapil }}</div>
                                    </div>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <select wire:model="assignSelection.{{ $detail->id }}" style="height:34px;min-width:190px;padding:0 28px 0 10px;border-radius:8px;border:0.5px solid #cbd5e1;background:white;font-size:12px;color:#111827;">
                                            <option value="">Pilih dewan</option>
                                            @foreach (($this->dewanGroupedByDapil[$detail->dapil] ?? collect()) as $dewan)
                                                <option value="{{ $dewan->id }}">{{ $dewan->nama }}</option>
                                            @endforeach
                                        </select>
                                        <button wire:click="assignDewan('{{ $detail->id }}')" type="button" style="height:34px;padding:0 12px;border:none;border-radius:8px;background:#0ea5e9;color:white;font-size:12px;font-weight:600;cursor:pointer;">{{ $detail->assigned_dewan_id ? 'Ubah assign' : 'Assign' }}</button>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Draft POKIR</div>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <button wire:click="copyDraftPokir('{{ $detail->id }}')" type="button" style="height:34px;padding:0 12px;border-radius:8px;border:0.5px solid #bae6fd;background:#ecfeff;color:#0369a1;font-size:12px;font-weight:600;cursor:pointer;">Copy ke clipboard</button>
                                        <button wire:click="bukaSipd" type="button" style="height:34px;padding:0 12px;border-radius:8px;border:none;background:#111827;color:white;font-size:12px;font-weight:600;cursor:pointer;">Buka SIPD</button>
                                    </div>
                                </div>
                                <textarea readonly rows="9" style="width:100%;margin-top:8px;border-radius:12px;border:0.5px solid #cbd5e1;padding:12px;font-size:12px;line-height:1.6;background:#f8fafc;">{{ $detail->draft_pokir ?: $detail->generateDraftPokir() }}</textarea>
                            </div>

                            <div style="margin-top:14px;">
                                <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Timeline Status</div>
                                <div style="display:grid;gap:8px;margin-top:8px;">
                                    @forelse ($detail->logs as $log)
                                        <div style="display:grid;grid-template-columns:96px minmax(0,1fr);gap:10px;padding:10px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                            <div style="font-size:10px;color:#64748b;">{{ $log->created_at?->format('d M Y H:i') }}</div>
                                            <div>
                                                <div style="font-size:12px;font-weight:700;color:#111827;">{{ ucfirst(str_replace('_', ' ', $log->aksi)) }}</div>
                                                <div style="font-size:11px;color:#64748b;margin-top:3px;">{{ $log->user?->name ?? 'Sistem' }}</div>
                                                @if ($log->catatan)
                                                    <div style="font-size:11px;color:#475569;line-height:1.6;margin-top:4px;">{{ $log->catatan }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div style="padding:14px;border-radius:10px;background:#f8fafc;border:0.5px dashed #cbd5e1;font-size:12px;color:#64748b;">Belum ada timeline perubahan.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                                @if ($detail->status === 'diterima')
                                    <button wire:click="$set('activeTab', 'daftar')" type="button" style="height:36px;padding:0 12px;border:none;border-radius:10px;background:#0ea5e9;color:white;font-size:12px;font-weight:600;">Assign ke dewan</button>
                                @elseif ($detail->status === 'assigned')
                                    <button wire:click="openKonfirmasiSipd('{{ $detail->id }}')" type="button" style="height:36px;padding:0 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:600;">Konfirmasi sudah input SIPD</button>
                                @elseif ($detail->status === 'input_sipd')
                                    <button wire:click="openUpdateStatus('{{ $detail->id }}', 'verifikasi_bappeda')" type="button" style="height:36px;padding:0 12px;border:none;border-radius:10px;background:#d97706;color:white;font-size:12px;font-weight:600;">Update: Diverifikasi BAPPEDA</button>
                                @elseif ($detail->status === 'verifikasi_bappeda')
                                    <button wire:click="openUpdateStatus('{{ $detail->id }}', 'dianggarkan')" type="button" style="height:36px;padding:0 12px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:600;">Update: Dianggarkan</button>
                                @elseif ($detail->status === 'dianggarkan')
                                    <button wire:click="openUpdateStatus('{{ $detail->id }}', 'terealisasi')" type="button" style="height:36px;padding:0 12px;border:none;border-radius:10px;background:#15803d;color:white;font-size:12px;font-weight:600;">Update: Terealisasi</button>
                                @endif

                                @if ($detail->status === 'assigned' && ($detail->durasi_sipd ?? 0) > 14)
                                    <button wire:click="kirimReminderManual('{{ $detail->id }}')" type="button" style="height:36px;padding:0 12px;border-radius:10px;border:0.5px solid #fdba74;background:#fff7ed;color:#9a3412;font-size:12px;font-weight:600;cursor:pointer;">
                                        Kirim reminder ke dewan
                                    </button>
                                @endif
                            </div>
                        @else
                            <div style="padding:26px;border-radius:12px;border:0.5px dashed #cbd5e1;background:#f8fafc;text-align:center;font-size:12px;color:#64748b;">
                                Pilih salah satu aspirasi di daftar untuk melihat detail, timeline, dan aksi lanjutan.
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($activeTab === 'kinerja')
                <div style="margin-top:16px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;" class="aspirasi-kinerja-grid">
                    @forelse ($kinerja as $row)
                        @php
                            $scoreColor = $row['score'] >= 70 ? '#15803d' : ($row['score'] >= 30 ? '#b45309' : '#b91c1c');
                            $scoreBg = $row['score'] >= 70 ? '#dcfce7' : ($row['score'] >= 30 ? '#fff7ed' : '#fef2f2');
                        @endphp
                        <div style="border-radius:14px;border:0.5px solid #e5e7eb;background:white;padding:16px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:#111827;">{{ $row['dewan']->nama }}</div>
                                    <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $row['dewan']->jabatan }} · {{ $row['dewan']->dapil }}</div>
                                </div>
                                <div style="padding:6px 10px;border-radius:999px;background:{{ $scoreBg }};color:{{ $scoreColor }};font-size:12px;font-weight:700;">
                                    {{ number_format($row['score']) }}%
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:14px;">
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">Assigned</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($row['assigned']) }}</div>
                                </div>
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">Input SIPD</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($row['input_sipd']) }}</div>
                                </div>
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">Verifikasi</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($row['verifikasi_bappeda']) }}</div>
                                </div>
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">APBD</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($row['dianggarkan']) }}</div>
                                </div>
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">Realisasi</div>
                                    <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($row['terealisasi']) }}</div>
                                </div>
                                <div style="padding:9px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:10px;color:#64748b;">Rasio</div>
                                    <div style="font-size:18px;font-weight:700;color:{{ $scoreColor }};margin-top:4px;">{{ number_format($row['score']) }}</div>
                                </div>
                            </div>
                            @if ($loop->iteration <= 3)
                                <div style="margin-top:12px;font-size:11px;color:#64748b;">Peringkat #{{ $loop->iteration }}</div>
                            @elseif ($row['score'] < 30)
                                <div style="margin-top:12px;font-size:11px;color:#b91c1c;">Perlu percepatan tindak lanjut aspirasi.</div>
                            @endif
                        </div>
                    @empty
                        <div style="grid-column:1/-1;padding:28px;border-radius:14px;border:0.5px dashed #cbd5e1;background:#f8fafc;text-align:center;font-size:12px;color:#64748b;">
                            Belum ada data kinerja dewan karena belum ada aspirasi yang di-assign.
                        </div>
                    @endforelse
                </div>
            @else
                <div style="margin-top:16px;display:grid;grid-template-columns:minmax(0,1.2fr) minmax(320px,.8fr);gap:14px;" class="aspirasi-public-grid">
                    <div style="border-radius:14px;border:0.5px solid #e5e7eb;background:white;padding:18px;">
                        <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Preview Publik</div>
                        <div style="font-size:28px;font-weight:800;color:#111827;margin-top:6px;">{{ number_format($publicPreview['total']) }} Aspirasi Diperjuangkan</div>
                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:16px;">
                            <div style="padding:14px;border-radius:12px;background:#e0f2fe;border:0.5px solid #7dd3fc;">
                                <div style="font-size:11px;color:#0369a1;">Masuk SIPD</div>
                                <div style="font-size:24px;font-weight:800;color:#0c4a6e;margin-top:4px;">{{ number_format($publicPreview['sipd']) }}</div>
                            </div>
                            <div style="padding:14px;border-radius:12px;background:#dcfce7;border:0.5px solid #86efac;">
                                <div style="font-size:11px;color:#166534;">Dianggarkan</div>
                                <div style="font-size:24px;font-weight:800;color:#14532d;margin-top:4px;">{{ number_format($publicPreview['dianggarkan']) }}</div>
                            </div>
                            <div style="padding:14px;border-radius:12px;background:#f0fdf4;border:0.5px solid #bbf7d0;">
                                <div style="font-size:11px;color:#166534;">Terealisasi</div>
                                <div style="font-size:24px;font-weight:800;color:#14532d;margin-top:4px;">{{ number_format($publicPreview['terealisasi']) }}</div>
                            </div>
                        </div>

                        <div style="margin-top:18px;font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Success Stories</div>
                        <div style="display:grid;gap:10px;margin-top:10px;">
                            @forelse ($publicPreview['stories'] as $story)
                                <div style="padding:12px;border-radius:12px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $story->judul }}</div>
                                    <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $story->desa ?: '-' }} · {{ $story->assignedDewan?->nama ?? 'Dewan PKS Kabupaten Bekasi' }}</div>
                                    <div style="font-size:12px;color:#166534;font-weight:700;margin-top:8px;">{{ $story->anggaran_nominal ? 'Rp '.number_format((float) $story->anggaran_nominal, 0, ',', '.') : 'Nominal belum dicatat' }}</div>
                                </div>
                            @empty
                                <div style="padding:18px;border-radius:12px;background:#f8fafc;border:0.5px dashed #cbd5e1;text-align:center;font-size:12px;color:#64748b;">
                                    Success story akan muncul setelah ada aspirasi berstatus terealisasi.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div style="display:grid;gap:14px;align-self:start;">
                        <div style="border-radius:14px;border:0.5px solid #e5e7eb;background:white;padding:16px;">
                            <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Sumber Aspirasi</div>
                            <div style="display:grid;gap:8px;margin-top:10px;">
                                @foreach ($this->sumberBreakdown as $item)
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 10px;border-radius:10px;background:#f8fafc;border:0.5px solid #e2e8f0;">
                                        <span style="font-size:12px;color:#334155;">{{ $item['label'] }}</span>
                                        <strong style="font-size:12px;color:#111827;">{{ number_format($item['count']) }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div style="border-radius:14px;border:0.5px solid #e5e7eb;background:white;padding:16px;">
                            <div style="font-size:11px;color:#0ea5e9;font-weight:700;text-transform:uppercase;">Breakdown Kategori</div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                @foreach ($this->kategoriBreakdown as $item)
                                    <span style="padding:6px 10px;border-radius:999px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:600;">
                                        {{ $item['label'] }} · {{ number_format($item['count']) }}
                                    </span>
                                @endforeach
                            </div>
                            <a href="{{ route('public.aspirasi') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;height:36px;padding:0 12px;border-radius:10px;background:#0ea5e9;color:white;font-size:12px;font-weight:700;text-decoration:none;">
                                Sampaikan Aspirasi
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($showForm)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:70;" wire:click="closeForm"></div>
        <div style="position:fixed;top:0;right:0;bottom:0;width:min(520px,100vw);background:white;z-index:71;overflow-y:auto;box-shadow:-10px 0 30px rgba(15,23,42,.15);">
            <div style="padding:18px;border-bottom:0.5px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:18px;font-weight:700;color:#111827;">Input Aspirasi</div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px;">Simpan aspirasi baru dan generate draft POKIR secara otomatis.</div>
                </div>
                <button wire:click="closeForm" type="button" style="width:32px;height:32px;border:none;border-radius:999px;background:#f1f5f9;color:#475569;cursor:pointer;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                <input wire:model="fJudul" type="text" placeholder="Judul aspirasi" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                <textarea wire:model="fDeskripsi" rows="5" placeholder="Deskripsi masalah dan kebutuhan warga" style="width:100%;border-radius:10px;border:0.5px solid #cbd5e1;padding:10px 12px;font-size:13px;resize:vertical;color:#111827;background:white;"></textarea>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <select wire:model.live="fKategori" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        @foreach (\App\Models\Aspirasi::KATEGORI_OPTIONS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="fUrgensi" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        @foreach (\App\Models\Aspirasi::URGENSI_OPTIONS as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <select wire:model.live="fDapil" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        <option value="">Pilih dapil</option>
                        @foreach ($this->dapilOptions as $dapil)
                            <option value="{{ $dapil }}">{{ $dapil }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="fKecamatan" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        <option value="">Pilih kecamatan</option>
                        @foreach ($this->kecamatanOptions as $kecamatan)
                            <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($this->autoSuggestDewan)
                    <div style="padding:10px 12px;border-radius:10px;background:#ecfeff;border:0.5px solid #a5f3fc;font-size:12px;color:#0f766e;">
                        Dapil ini otomatis diarahkan ke <strong>{{ $this->autoSuggestDewan->nama }}</strong> sebagai penanggung jawab, namun tetap bisa diubah manual setelah aspirasi tersimpan.
                    </div>
                @endif
                <div style="display:grid;grid-template-columns:minmax(0,1fr) 100px;gap:10px;">
                    <select wire:model.live="fDesa" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        <option value="">Pilih desa</option>
                        @foreach ($this->desaOptions as $desa)
                            <option value="{{ $desa->desa }}">{{ $desa->desa }}</option>
                        @endforeach
                    </select>
                    <input wire:model="fRw" type="text" placeholder="RW" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                </div>
                <input wire:model="fAlamat" type="text" placeholder="Alamat detail" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <input wire:model="fNamaPelapor" type="text" placeholder="Nama pelapor" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                    <input wire:model="fHpPelapor" type="text" placeholder="HP pelapor" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <select wire:model="fSumber" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        @foreach (\App\Models\Aspirasi::SUMBER_OPTIONS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input wire:model="fSumberId" type="text" placeholder="ID sumber (opsional)" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                </div>
                <textarea wire:model="fCatatanInternal" rows="3" placeholder="Catatan internal (opsional)" style="width:100%;border-radius:10px;border:0.5px solid #cbd5e1;padding:10px 12px;font-size:13px;resize:vertical;color:#111827;background:white;"></textarea>
                <div style="display:flex;justify-content:flex-end;gap:8px;">
                    <button wire:click="closeForm" type="button" style="height:40px;padding:0 14px;border-radius:10px;border:0.5px solid #cbd5e1;background:white;color:#475569;font-size:12px;font-weight:600;">Batal</button>
                    <button wire:click="simpanAspirasi" type="button" style="height:40px;padding:0 14px;border:none;border-radius:10px;background:#0ea5e9;color:white;font-size:12px;font-weight:700;">Simpan Aspirasi</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showKonfirmasiSipd)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:72;"></div>
        <div style="position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);width:min(460px,calc(100vw - 24px));background:white;border-radius:16px;z-index:73;box-shadow:0 20px 40px rgba(15,23,42,.18);padding:18px;">
            <div style="font-size:18px;font-weight:700;color:#111827;">Konfirmasi Input SIPD</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">Masukkan nomor POKIR dari SIPD dan unggah screenshot bila tersedia.</div>
            <div style="display:grid;gap:10px;margin-top:14px;">
                <input wire:model="fNomorPokir" type="text" placeholder="Nomor POKIR" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                <input wire:model="fScreenshotSipd" type="file" style="font-size:12px;">
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px;">
                <button wire:click="$set('showKonfirmasiSipd', false)" type="button" style="height:38px;padding:0 12px;border-radius:10px;border:0.5px solid #cbd5e1;background:white;color:#475569;font-size:12px;font-weight:600;">Batal</button>
                <button wire:click="konfirmasiInputSipd" type="button" style="height:38px;padding:0 12px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;">Konfirmasi</button>
            </div>
        </div>
    @endif

    @if ($showUpdateStatus)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:72;"></div>
        <div style="position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);width:min(460px,calc(100vw - 24px));background:white;border-radius:16px;z-index:73;box-shadow:0 20px 40px rgba(15,23,42,.18);padding:18px;">
            <div style="font-size:18px;font-weight:700;color:#111827;">Update Status Aspirasi</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">Lengkapi data sesuai tahap status yang akan dicatat.</div>
            <div style="display:grid;gap:10px;margin-top:14px;">
                <select wire:model="fNewStatus" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                    <option value="verifikasi_bappeda">Verifikasi BAPPEDA</option>
                    <option value="dianggarkan">Dianggarkan</option>
                    <option value="terealisasi">Terealisasi</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                @if ($fNewStatus === 'dianggarkan')
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        <input wire:model="fAnggaranNominal" type="number" min="0" placeholder="Nominal anggaran" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                        <input wire:model="fTahunAnggaran" type="text" placeholder="Tahun anggaran" style="height:40px;border-radius:10px;border:0.5px solid #cbd5e1;padding:0 12px;font-size:13px;color:#111827;background:white;">
                    </div>
                @endif
                @if ($fNewStatus === 'terealisasi')
                    <input wire:model="fFotoRealisasi" type="file" style="font-size:12px;">
                @endif
                <textarea wire:model="fCatatan" rows="3" placeholder="Catatan perubahan status" style="width:100%;border-radius:10px;border:0.5px solid #cbd5e1;padding:10px 12px;font-size:13px;resize:vertical;color:#111827;background:white;"></textarea>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px;">
                <button wire:click="$set('showUpdateStatus', false)" type="button" style="height:38px;padding:0 12px;border-radius:10px;border:0.5px solid #cbd5e1;background:white;color:#475569;font-size:12px;font-weight:600;">Batal</button>
                <button wire:click="updateStatus" type="button" style="height:38px;padding:0 12px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:700;">Simpan Status</button>
            </div>
        </div>
    @endif

    <script>
        window.addEventListener('aspirasi-copy-draft', event => {
            const text = event.detail?.text || '';
            if (!text) return;
            navigator.clipboard?.writeText(text);
        });

        window.addEventListener('aspirasi-open-sipd', event => {
            const url = event.detail?.url || 'https://sipd.kemendagri.go.id';
            window.open(url, '_blank', 'noopener');
        });
    </script>

    <style>
        @media (max-width: 1380px) {
            .aspirasi-main-grid {
                grid-template-columns: 1fr !important;
            }
        }
        @media (max-width: 1180px) {
            .aspirasi-main-grid,
            .aspirasi-public-grid {
                grid-template-columns: 1fr !important;
            }
            .aspirasi-pipeline-grid,
            .aspirasi-kinerja-grid,
            .aspirasi-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 760px) {
            .aspirasi-pipeline-grid,
            .aspirasi-kinerja-grid,
            .aspirasi-summary-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>
