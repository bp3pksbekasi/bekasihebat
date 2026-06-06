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

        <div style="display:grid;grid-template-columns:minmax(0,5fr) minmax(0,7fr);gap:14px;padding:0 20px 20px;" class="rkiksn-main-grid">
            <div style="display:grid;gap:12px;align-content:start;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                        <div>
                            <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Daftar Desa</div>
                            <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">
                                {{ $isRki ? 'Progress titik RKI per desa' : 'Progress titik KSN per desa' }}
                            </div>
                        </div>
                        <div style="font-size:10px;color:#888;">Klik row untuk pilih desa</div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead style="background:#fafafa;">
                                <tr style="border-bottom:0.5px solid #e5e5e5;">
                                    <th style="text-align:left;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;">Desa</th>
                                    <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;">Aktif</th>
                                    <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;">Total</th>
                                    <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;">Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $desaRows = $isRki ? $this->rkiDesaList : $this->ksnDesaList;
                                @endphp
                                @forelse ($desaRows as $desa)
                                    <tr wire:key="desa-{{ $activeTab }}-{{ $desa->id }}" wire:click="selectDesa('{{ $desa->id }}')" style="cursor:pointer;border-bottom:0.5px solid #f1f5f9;background:{{ $selectedDesaId === $desa->id ? $accentSoft : 'white' }};">
                                        <td style="padding:8px 10px;">
                                            <div style="font-weight:600;color:#111827;">{{ $desa->desa }}</div>
                                            <div style="font-size:10px;color:#888;margin-top:2px;">{{ $desa->kecamatan }} · {{ $desa->dapil }}</div>
                                        </td>
                                        <td style="padding:8px 10px;text-align:center;color:{{ $accent }};font-weight:700;">
                                            {{ number_format($isRki ? $desa->rki_aktif : $desa->ksn_aktif) }}
                                        </td>
                                        <td style="padding:8px 10px;text-align:center;color:#444;">
                                            {{ number_format($isRki ? $desa->rki_total : $desa->ksn_total) }}
                                        </td>
                                        <td style="padding:8px 10px;text-align:center;color:#444;">
                                            {{ number_format($isRki ? $desa->jumlah_rw : 1) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding:24px;text-align:center;color:#888;">Belum ada desa untuk filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="border-top:0.5px solid #e5e5e5;margin-top:10px;padding-top:10px;">
                        {{ $desaRows->links('livewire::simple-tailwind') }}
                    </div>
                </div>

                @if ($selectedDesa)
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:{{ $accent }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Desa Terpilih</div>
                        <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ $selectedDesa->desa }}</div>
                        <div style="font-size:12px;color:#666;margin-top:3px;">{{ $selectedDesa->kecamatan }} · {{ $selectedDesa->dapil }}</div>
                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:12px;">
                            <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;background:#fafafa;">
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">RW</div>
                                <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">{{ number_format($selectedDesa->jumlah_rw) }}</div>
                            </div>
                            <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;background:#fafafa;">
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">{{ $isRki ? 'Titik RKI' : 'Titik KSN' }}</div>
                                <div style="font-size:18px;font-weight:700;color:{{ $accent }};margin-top:4px;">
                                    {{ number_format($isRki ? $selectedDesa->titikRkis()->count() : $selectedDesa->titikSenams()->count()) }}
                                </div>
                            </div>
                            <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;background:#fafafa;">
                                <div style="font-size:10px;color:#888;text-transform:uppercase;">Aktif</div>
                                <div style="font-size:18px;font-weight:700;color:#111827;margin-top:4px;">
                                    {{ number_format($isRki ? $selectedDesa->titikRkis()->where('status', 'aktif')->count() : $selectedDesa->titikSenams()->where('status', 'aktif')->count()) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div style="display:grid;gap:12px;align-content:start;">
                @if ($selectedDesa)
                    @if ($isRki)
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                <div>
                                    <div style="font-size:10px;color:#ec4899;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Detail Desa RKI</div>
                                    <div style="font-size:16px;font-weight:700;color:#111827;margin-top:2px;">{{ $selectedDesa->desa }}</div>
                                </div>
                                <button type="button" wire:click="openRkiForm" style="padding:7px 12px;border:none;border-radius:8px;background:#ec4899;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                    + Tambah titik RKI
                                </button>
                            </div>

                            <div style="display:grid;gap:10px;">
                                @forelse ($this->rkiDetail as $titik)
                                    <div wire:key="rki-card-{{ $titik->id }}" style="border:0.5px solid #f9a8d4;border-radius:12px;background:#fdf2f8;padding:12px;">
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                            <div>
                                                <div style="font-size:14px;font-weight:700;color:#9d174d;">RW {{ $titik->nomor_rw }}</div>
                                                <div style="font-size:12px;color:#4b5563;margin-top:4px;">{{ $titik->nama_penggerak }}</div>
                                                <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $titik->lokasi ?: 'Lokasi belum diisi' }}</div>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                                                <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $titik->status_config['bg'] }};color:{{ $titik->status_config['text'] }};font-weight:700;">
                                                    {{ $titik->status_config['label'] }}
                                                </span>
                                                <button type="button" wire:click="openRkiForm('{{ $titik->id }}')" style="padding:6px 10px;border:0.5px solid #f9a8d4;border-radius:8px;background:white;color:#9d174d;font-size:11px;font-weight:600;cursor:pointer;">
                                                    Edit
                                                </button>
                                                <button type="button" wire:click="openLogForm('{{ $titik->id }}', '{{ \App\Models\TitikRki::class }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#ec4899;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                                    Catat sesi
                                                </button>
                                            </div>
                                        </div>

                                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                            <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid #fbcfe8;">{{ $titik->hari_kegiatan ?: 'Hari belum diisi' }}</span>
                                            <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid #fbcfe8;">{{ $titik->jam_kegiatan ?: 'Jam belum diisi' }}</span>
                                            <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#6b7280;border:0.5px solid #fbcfe8;">Avg {{ number_format($titik->avg_peserta) }} peserta</span>
                                        </div>

                                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;">
                                            @forelse (($titik->jenis_kegiatan ?? []) as $jenis)
                                                <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:#fff;border:0.5px solid #f9a8d4;color:#9d174d;">
                                                    {{ \App\Models\TitikRki::JENIS_KEGIATAN_OPTIONS[$jenis] ?? $jenis }}
                                                </span>
                                            @empty
                                                <span style="font-size:10px;color:#9ca3af;">Jenis kegiatan belum dipilih</span>
                                            @endforelse
                                        </div>

                                        <div style="margin-top:12px;">
                                            <div style="font-size:10px;color:#9d174d;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Log sesi terbaru</div>
                                            <div style="display:grid;gap:6px;">
                                                @forelse ($titik->logSesis as $log)
                                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;background:white;border:0.5px solid #fbcfe8;border-radius:8px;padding:8px 10px;">
                                                        <div>
                                                            <div style="font-size:11px;font-weight:600;color:#111827;">{{ $log->tanggal_sesi?->format('d M Y H:i') }}</div>
                                                            <div style="font-size:10px;color:#666;margin-top:2px;">{{ number_format($log->jumlah_peserta) }} peserta · {{ $log->pelaksana ?: 'Pelaksana belum diisi' }}</div>
                                                        </div>
                                                        @if (!empty($log->foto))
                                                            <span style="font-size:10px;color:#9d174d;">{{ count($log->foto) }} foto</span>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <div style="font-size:11px;color:#9ca3af;">Belum ada log sesi.</div>
                                                @endforelse
                                            </div>
                                        </div>

                                        @if ($showLogForm && $expandedLogKey === \App\Models\TitikRki::class.'|'.$titik->id)
                                            <form wire:submit.prevent="simpanLog" style="margin-top:12px;border-top:0.5px solid #f9a8d4;padding-top:12px;display:grid;gap:10px;">
                                                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;" class="rkiksn-form-grid">
                                                    <div>
                                                        <div style="font-size:10px;color:#9d174d;font-weight:700;margin-bottom:4px;">Tanggal</div>
                                                        <input type="datetime-local" wire:model="logTanggal" style="width:100%;padding:8px 10px;border:0.5px solid #f9a8d4;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                    <div>
                                                        <div style="font-size:10px;color:#9d174d;font-weight:700;margin-bottom:4px;">Jumlah peserta</div>
                                                        <input type="number" min="1" wire:model="logPeserta" style="width:100%;padding:8px 10px;border:0.5px solid #f9a8d4;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                    <div>
                                                        <div style="font-size:10px;color:#9d174d;font-weight:700;margin-bottom:4px;">Pelaksana</div>
                                                        <input type="text" wire:model="logPelaksana" style="width:100%;padding:8px 10px;border:0.5px solid #f9a8d4;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="font-size:10px;color:#9d174d;font-weight:700;margin-bottom:4px;">Catatan</div>
                                                    <textarea wire:model="logCatatan" rows="2" style="width:100%;padding:8px 10px;border:0.5px solid #f9a8d4;border-radius:8px;font-size:12px;background:white;resize:vertical;"></textarea>
                                                </div>
                                                <div>
                                                    <div style="font-size:10px;color:#9d174d;font-weight:700;margin-bottom:4px;">Foto</div>
                                                    <input type="file" wire:model="logFoto" multiple style="font-size:12px;">
                                                    @if (!empty($logFoto))
                                                        <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ count($logFoto) }} file dipilih</div>
                                                    @endif
                                                </div>
                                                <div style="display:flex;justify-content:flex-end;gap:8px;">
                                                    <button type="button" wire:click="closeLogForm" style="padding:7px 10px;border:0.5px solid #f9a8d4;border-radius:8px;background:white;color:#9d174d;font-size:11px;font-weight:600;cursor:pointer;">Batal</button>
                                                    <button type="submit" style="padding:7px 12px;border:none;border-radius:8px;background:#ec4899;color:white;font-size:11px;font-weight:600;cursor:pointer;">Simpan sesi</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div style="padding:26px;border:0.5px dashed #f9a8d4;border-radius:12px;background:#fdf2f8;text-align:center;color:#9ca3af;font-size:12px;">
                                        Belum ada titik RKI di desa ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="font-size:10px;color:#ec4899;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">RW Belum Ada RKI</div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                @forelse ($this->rwBelumRki as $rw)
                                    <span style="font-size:11px;padding:6px 10px;border-radius:999px;border:0.5px solid #fbcfe8;background:#fdf2f8;color:#9d174d;">RW {{ $rw }}</span>
                                @empty
                                    <span style="font-size:12px;color:#9ca3af;">Semua RW sudah punya titik RKI.</span>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                <div>
                                    <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Detail Titik Senam</div>
                                    <div style="font-size:16px;font-weight:700;color:#111827;margin-top:2px;">{{ $selectedDesa->desa }}</div>
                                </div>
                                <button type="button" wire:click="openKsnForm" style="padding:7px 12px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                    + Tambah titik senam
                                </button>
                            </div>

                            <div style="display:grid;gap:10px;">
                                @forelse ($this->ksnDetail as $titik)
                                    <div wire:key="ksn-card-{{ $titik->id }}" style="border:0.5px solid #86efac;border-radius:12px;background:#f0fdf4;padding:12px;">
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                            <div>
                                                <div style="font-size:14px;font-weight:700;color:#166534;">{{ $titik->nama_titik }}</div>
                                                <div style="font-size:12px;color:#4b5563;margin-top:4px;">Instruktur: {{ $titik->instruktur }}</div>
                                                <div style="font-size:11px;color:#6b7280;margin-top:4px;">RW {{ $titik->lokasi_rw ?: '-' }} · {{ $titik->hari_senam ?: 'Hari belum diisi' }} {{ $titik->jam_senam ?: '' }}</div>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                                                <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $titik->status_config['bg'] }};color:{{ $titik->status_config['text'] }};font-weight:700;">
                                                    {{ $titik->status_config['label'] }}
                                                </span>
                                                <button type="button" wire:click="openKsnForm('{{ $titik->id }}')" style="padding:6px 10px;border:0.5px solid #86efac;border-radius:8px;background:white;color:#166534;font-size:11px;font-weight:600;cursor:pointer;">
                                                    Edit
                                                </button>
                                                <button type="button" wire:click="openLogForm('{{ $titik->id }}', '{{ \App\Models\TitikSenam::class }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                                    Catat sesi senam
                                                </button>
                                            </div>
                                        </div>

                                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                            <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#166534;border:0.5px solid #86efac;">Avg {{ number_format($titik->avg_peserta) }} peserta</span>
                                            @if ($titik->instruktur_2)
                                                <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#166534;border:0.5px solid #86efac;">Cadangan: {{ $titik->instruktur_2 }}</span>
                                            @endif
                                            @if ($titik->no_hp_instruktur)
                                                <span style="font-size:10px;padding:3px 7px;border-radius:999px;background:white;color:#166534;border:0.5px solid #86efac;">{{ $titik->no_hp_instruktur }}</span>
                                            @endif
                                        </div>

                                        <div style="margin-top:12px;">
                                            <div style="font-size:10px;color:#166534;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Log sesi</div>
                                            <div style="display:grid;gap:6px;">
                                                @forelse ($titik->logSesis as $log)
                                                    <div style="background:white;border:0.5px solid #bbf7d0;border-radius:8px;padding:8px 10px;">
                                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                                            <div>
                                                                <div style="font-size:11px;font-weight:600;color:#111827;">{{ $log->tanggal_sesi?->format('d M Y H:i') }}</div>
                                                                <div style="font-size:10px;color:#666;margin-top:2px;">{{ number_format($log->jumlah_peserta) }} peserta · {{ $log->pelaksana ?: 'Pelaksana belum diisi' }}</div>
                                                            </div>
                                                            @if (!empty($log->foto))
                                                                <span style="font-size:10px;color:#166534;">{{ count($log->foto) }} foto</span>
                                                            @endif
                                                        </div>
                                                        @if ($log->catatan)
                                                            <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ $log->catatan }}</div>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <div style="font-size:11px;color:#9ca3af;">Belum ada log sesi.</div>
                                                @endforelse
                                            </div>
                                        </div>

                                        @if ($showLogForm && $expandedLogKey === \App\Models\TitikSenam::class.'|'.$titik->id)
                                            <form wire:submit.prevent="simpanLog" style="margin-top:12px;border-top:0.5px solid #86efac;padding-top:12px;display:grid;gap:10px;">
                                                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;" class="rkiksn-form-grid">
                                                    <div>
                                                        <div style="font-size:10px;color:#166534;font-weight:700;margin-bottom:4px;">Tanggal</div>
                                                        <input type="datetime-local" wire:model="logTanggal" style="width:100%;padding:8px 10px;border:0.5px solid #86efac;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                    <div>
                                                        <div style="font-size:10px;color:#166534;font-weight:700;margin-bottom:4px;">Jumlah peserta</div>
                                                        <input type="number" min="1" wire:model="logPeserta" style="width:100%;padding:8px 10px;border:0.5px solid #86efac;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                    <div>
                                                        <div style="font-size:10px;color:#166534;font-weight:700;margin-bottom:4px;">Pelaksana</div>
                                                        <input type="text" wire:model="logPelaksana" style="width:100%;padding:8px 10px;border:0.5px solid #86efac;border-radius:8px;font-size:12px;background:white;">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="font-size:10px;color:#166534;font-weight:700;margin-bottom:4px;">Catatan</div>
                                                    <textarea wire:model="logCatatan" rows="2" style="width:100%;padding:8px 10px;border:0.5px solid #86efac;border-radius:8px;font-size:12px;background:white;resize:vertical;"></textarea>
                                                </div>
                                                <div>
                                                    <div style="font-size:10px;color:#166534;font-weight:700;margin-bottom:4px;">Foto</div>
                                                    <input type="file" wire:model="logFoto" multiple style="font-size:12px;">
                                                    @if (!empty($logFoto))
                                                        <div style="font-size:10px;color:#6b7280;margin-top:4px;">{{ count($logFoto) }} file dipilih</div>
                                                    @endif
                                                </div>
                                                <div style="display:flex;justify-content:flex-end;gap:8px;">
                                                    <button type="button" wire:click="closeLogForm" style="padding:7px 10px;border:0.5px solid #86efac;border-radius:8px;background:white;color:#166534;font-size:11px;font-weight:600;cursor:pointer;">Batal</button>
                                                    <button type="submit" style="padding:7px 12px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:11px;font-weight:600;cursor:pointer;">Simpan sesi</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div style="padding:26px;border:0.5px dashed #86efac;border-radius:12px;background:#f0fdf4;text-align:center;color:#9ca3af;font-size:12px;">
                                        Belum ada titik senam di desa ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:38px;text-align:center;color:#9ca3af;">
                        <i class="ti ti-map-pin-search" style="font-size:30px;display:block;margin-bottom:8px;" aria-hidden="true"></i>
                        Pilih desa dari tabel kiri untuk melihat detail {{ $isRki ? 'RKI' : 'KSN' }}.
                    </div>
                @endif
            </div>
        </div>
    </div>

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
                        <select wire:model="rkiRw" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih RW</option>
                            @foreach ($this->rwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Nama Penggerak</div>
                        <input type="text" wire:model="rkiPenggerak" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">HP</div>
                        <input type="text" wire:model="rkiHp" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Lokasi</div>
                        <input type="text" wire:model="rkiLokasi" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Hari</div>
                            <input type="text" wire:model="rkiHari" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                        </div>
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Jam</div>
                            <input type="text" wire:model="rkiJam" placeholder="09:00" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:6px;">Jenis kegiatan</div>
                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                            @foreach (\App\Models\TitikRki::JENIS_KEGIATAN_OPTIONS as $value => $label)
                                <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:0.5px solid #fbcfe8;border-radius:10px;font-size:12px;background:#fdf2f8;">
                                    <input type="checkbox" wire:model="rkiJenis" value="{{ $value }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Status</div>
                        <select wire:model="rkiStatus" style="width:100%;padding:10px 12px;border:0.5px solid #f9a8d4;border-radius:10px;font-size:12px;">
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
                        <select wire:model="ksnDesaId" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->desaOptions as $desa)
                                <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Nama titik</div>
                        <input type="text" wire:model="ksnNamaTitik" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Instruktur</div>
                        <input type="text" wire:model="ksnInstruktur" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">HP instruktur</div>
                        <input type="text" wire:model="ksnHpInstruktur" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Instruktur cadangan</div>
                        <input type="text" wire:model="ksnInstruktur2" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Hari senam</div>
                            <select wire:model="ksnHari" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                                <option value="">Pilih hari</option>
                                @foreach (\App\Models\TitikSenam::HARI_OPTIONS as $hari)
                                    <option value="{{ $hari }}">{{ ucfirst($hari) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Jam</div>
                            <input type="text" wire:model="ksnJam" placeholder="06:30" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Lokasi RW</div>
                        <input type="text" wire:model="ksnLokasiRw" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;font-weight:600;margin-bottom:4px;">Status</div>
                        <select wire:model="ksnStatus" style="width:100%;padding:10px 12px;border:0.5px solid #86efac;border-radius:10px;font-size:12px;">
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
        @media (max-width: 1280px) {
            .rkiksn-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 1024px) {
            .rkiksn-main-grid,
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
