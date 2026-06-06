@php
    $kpi = $this->kpi;
    $databaseTab = true;
    $upaTab = false;
    $pelatihanTab = false;
    $selectedPelatihan = $this->selectedPelatihan;
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
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#eff6ff;color:#1d4ed8;font-weight:500;">
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
                <div style="font-size:12px;color:#666;">Database kader, deployment UPA, dan pelatihan berjenjang untuk penguatan wilayah.</div>
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
                <button type="button" wire:click="setActiveTab('database')" style="padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $databaseTab ? '#dbeafe' : 'transparent' }};color:{{ $databaseTab ? '#1d4ed8' : '#71717a' }};">Database Kader</button>
            </div>
        </div>

        @if ($databaseTab)
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

            <div style="display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px;padding:0 20px 20px;" class="kaderisasi-main-grid">
                <div style="display:grid;gap:12px;align-content:start;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Komposisi Jenjang</div>
                        <div style="display:grid;gap:8px;">
                            @foreach ($this->jenjangChart as $item)
                                @php $max = max($this->jenjangChart->max('count'), 1); @endphp
                                <div style="display:grid;grid-template-columns:90px 1fr 36px;align-items:center;gap:8px;">
                                    <div style="font-size:11px;color:#374151;">{{ $item['label'] }}</div>
                                    <div style="height:8px;background:#eff6ff;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ round(($item['count'] / $max) * 100) }}%;background:{{ $item['color'] }};"></div>
                                    </div>
                                    <div style="font-size:11px;font-weight:700;color:#111827;text-align:right;">{{ $item['count'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                            <div>
                                <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Database Kader</div>
                                <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Daftar kader PKS Kabupaten Bekasi</div>
                            </div>
                            <div style="font-size:10px;color:#888;">{{ number_format($this->kaderList->total()) }} kader</div>
                        </div>

                        <div style="overflow:auto;">
                            <table style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr style="border-bottom:0.5px solid #e5e5e5;">
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Kader</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Wilayah</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Jenjang</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Peran</th>
                                        <th style="text-align:left;padding:10px 12px;font-size:10px;color:#888;text-transform:uppercase;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->kaderList as $kader)
                                        <tr style="border-bottom:0.5px solid #f1f5f9;">
                                            <td style="padding:10px 12px;">
                                                <div style="font-size:12px;font-weight:600;color:#111827;">{{ $kader->nama }}</div>
                                                <div style="font-size:11px;color:#888;margin-top:3px;">{{ $kader->no_wa ?: $kader->no_hp ?: '-' }}</div>
                                            </td>
                                            <td style="padding:10px 12px;font-size:11px;color:#525252;">
                                                {{ $kader->desa ?: '-' }}{{ $kader->nomor_rw ? ' · RW '.$kader->nomor_rw : '' }}{{ $kader->nomor_rt ? ' / RT '.$kader->nomor_rt : '' }}
                                                <div style="font-size:10px;color:#9ca3af;margin-top:3px;">{{ $kader->kecamatan ?: '-' }}</div>
                                            </td>
                                            <td style="padding:10px 12px;">
                                                <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $kader->jenjang_config['gradient'] ?? $kader->jenjang_config['color'] }};color:{{ $kader->jenjang_config['text'] }};font-weight:600;box-shadow:inset 0 0 0 0.5px rgba(255,255,255,0.18);">
                                                    {{ $kader->jenjang_config['label'] }}
                                                </span>
                                            </td>
                                            <td style="padding:10px 12px;font-size:11px;color:#525252;">{{ $kader->roles ? implode(', ', $kader->roles) : '-' }}</td>
                                            <td style="padding:10px 12px;">
                                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                                    <button type="button" wire:click="editKader('{{ $kader->id }}')" style="font-size:10px;color:#2563eb;text-decoration:underline;background:none;border:none;cursor:pointer;">Edit</button>
                                                    <button type="button" wire:click="hapusKader('{{ $kader->id }}')" wire:confirm="Nonaktifkan kader ini?" style="font-size:10px;color:#dc2626;text-decoration:underline;background:none;border:none;cursor:pointer;">Nonaktifkan</button>
                                                    @if ($kader->bisa_deploy && $kader->status === 'aktif')
                                                        <button type="button" wire:click="openDeployForm('{{ $kader->id }}')" style="font-size:10px;color:#16a34a;text-decoration:underline;background:none;border:none;cursor:pointer;">Deploy</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="padding:30px;text-align:center;color:#9ca3af;font-size:12px;">Belum ada data kader.</td>
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
                        <div style="font-size:10px;color:#d97706;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Deployment Recommendation</div>
                        <div style="display:grid;gap:8px;">
                            @forelse ($this->deployRecommendations as $recommendation)
                                <div style="border:0.5px solid #e5e5e5;border-radius:12px;padding:10px 12px;background:#fafafa;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;">{{ $recommendation['summary'] }}</div>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;">
                                        @foreach ($recommendation['candidates'] as $candidate)
                                            <button type="button" wire:click="openDeployForm('{{ $candidate->id }}')" style="font-size:10px;padding:4px 8px;border-radius:999px;border:0.5px solid #bbf7d0;background:#f0fdf4;color:#166534;cursor:pointer;">
                                                {{ $candidate->nama }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                    Belum ada rekomendasi deployment.
                                </div>
                            @endforelse
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
                </div>
            </div>
        @elseif ($upaTab)
            <div style="padding:18px 20px 0;">
                <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;" class="kaderisasi-summary-grid">
                    @foreach ($this->upaPerDapil as $row)
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="font-size:10px;color:#16a34a;text-transform:uppercase;font-weight:700;letter-spacing:0.8px;">{{ $row['dapil'] }}</div>
                            <div style="font-size:26px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($row['rw_upa']) }}</div>
                            <div style="font-size:11px;color:#71717a;margin-top:4px;">/ {{ number_format($row['total_rw']) }} RW</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px;padding:18px 20px 20px;" class="kaderisasi-main-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#d97706;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Desa Padat Kader</div>
                    <div style="display:grid;gap:10px;">
                        @forelse ($this->deployRecommendations as $recommendation)
                            <div style="border:0.5px solid #e5e5e5;border-radius:12px;padding:12px;background:#fafafa;">
                                <div style="font-size:12px;font-weight:700;color:#111827;">{{ $recommendation['summary'] }}</div>
                                <div style="font-size:10px;color:#71717a;margin-top:4px;">Tujuan kosong tersedia: {{ $recommendation['empty_rws']->pluck('desa')->unique()->implode(', ') }}</div>
                                <div style="margin-top:10px;display:grid;gap:6px;">
                                    @foreach ($recommendation['candidates'] as $candidate)
                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:8px 10px;border-radius:10px;background:white;border:0.5px solid #e5e5e5;">
                                            <div>
                                                <div style="font-size:12px;font-weight:600;color:#111827;">{{ $candidate->nama }}</div>
                                                <div style="font-size:10px;color:#71717a;margin-top:2px;">{{ $candidate->jenjang_config['label'] }} · RW {{ $candidate->nomor_rw ?: '-' }}</div>
                                            </div>
                                            <button type="button" wire:click="openDeployForm('{{ $candidate->id }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                                Deploy
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div style="padding:24px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                Belum ada desa dengan kepadatan kader yang bisa direkomendasikan untuk deploy.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Log Deployment Terbaru</div>
                    <div style="display:grid;gap:8px;">
                        @forelse ($this->deploymentRows as $deployment)
                            <div style="border:0.5px solid #e5e5e5;border-radius:12px;padding:10px 12px;background:#fafafa;">
                                <div style="font-size:12px;font-weight:700;color:#111827;">{{ $deployment->kader?->nama ?: 'Kader' }}</div>
                                <div style="font-size:10px;color:#71717a;margin-top:3px;">{{ $deployment->dari_desa ?: '-' }} RW {{ $deployment->dari_rw ?: '-' }} → {{ $deployment->ke_desa }} RW {{ $deployment->ke_rw }}</div>
                                <div style="font-size:10px;color:#71717a;margin-top:4px;">{{ $deployment->tanggal_deploy?->format('d M Y') }} · {{ ucfirst($deployment->alasan ?: 'deploy') }}</div>
                            </div>
                        @empty
                            <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                Belum ada log deployment.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            @php $pelSummary = $this->pelatihanSummary; @endphp
            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:18px 20px 0;" class="kaderisasi-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Tahun Ini</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($pelSummary['tahun_ini']) }}</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Peserta Kumulatif</div>
                    <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($pelSummary['peserta_kumulatif']) }}</div>
                </div>
                <div style="background:linear-gradient(135deg,#d97706,#b45309);border-radius:12px;padding:14px;color:white;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Lulus Naik Jenjang</div>
                    <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($pelSummary['naik_jenjang']) }}</div>
                </div>
            </div>

            <div style="padding:18px 20px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#d97706;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Program Pelatihan</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Pelatihan kader dan progres peserta</div>
                    </div>
                    <button type="button" wire:click="openPelatihanForm" style="padding:6px 10px;border:none;border-radius:8px;background:#d97706;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                        + Buat pelatihan baru
                    </button>
                </div>

                <div style="display:grid;gap:10px;">
                    @forelse ($this->pelatihanList as $pelatihan)
                        <div style="border:0.5px solid {{ $selectedPelatihanId === $pelatihan->id ? '#d97706' : '#e5e5e5' }};border-radius:12px;padding:12px;background:{{ $selectedPelatihanId === $pelatihan->id ? '#fffbeb' : 'white' }};">
                            <button type="button" wire:click="selectPelatihan('{{ $pelatihan->id }}')" style="width:100%;text-align:left;border:none;background:none;padding:0;cursor:pointer;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:#111827;">{{ $pelatihan->nama_pelatihan }}</div>
                                        <div style="font-size:10px;color:#71717a;margin-top:3px;">{{ \App\Models\Kader::JENJANG_OPTIONS[$pelatihan->jenjang_target]['label'] ?? ucfirst($pelatihan->jenjang_target) }} · {{ \App\Models\Pelatihan::JENIS_OPTIONS[$pelatihan->jenis] ?? ucfirst($pelatihan->jenis) }}</div>
                                        <div style="font-size:10px;color:#71717a;margin-top:3px;">{{ $pelatihan->tanggal_mulai?->format('d M Y') }} · {{ $pelatihan->lokasi ?: 'Lokasi belum diisi' }}</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                        <span style="font-size:10px;color:#92400e;background:#fef3c7;padding:3px 6px;border-radius:999px;">{{ $pelatihan->peserta_count }}/{{ $pelatihan->kapasitas ?: 0 }} peserta</span>
                                        <button type="button" wire:click.stop="editPelatihan('{{ $pelatihan->id }}')" style="font-size:10px;color:#2563eb;text-decoration:underline;background:none;border:none;cursor:pointer;">Edit</button>
                                    </div>
                                </div>
                            </button>

                            @if ($selectedPelatihanId === $pelatihan->id && $selectedPelatihan)
                                <div style="margin-top:12px;padding-top:12px;border-top:0.5px solid #fcd34d;">
                                    <div style="display:flex;align-items:end;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                                        <div style="flex:1;min-width:220px;">
                                            <label style="display:block;font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:5px;">Tambah peserta</label>
                                            <select wire:model="pesertaKaderId" style="width:100%;padding:9px 10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                                                <option value="">Pilih kader</option>
                                                @foreach ($this->eligiblePeserta as $kader)
                                                    <option value="{{ $kader->id }}">{{ $kader->nama }} · {{ $kader->desa ?: '-' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="button" wire:click="tambahPeserta('{{ $selectedPelatihan->id }}', '{{ $pesertaKaderId }}')" @disabled($pesertaKaderId === '') style="padding:9px 12px;border:none;border-radius:10px;background:#d97706;color:white;font-size:11px;font-weight:700;cursor:pointer;">
                                            Tambah peserta
                                        </button>
                                    </div>

                                    <div style="display:grid;gap:8px;">
                                        @forelse ($selectedPelatihan->peserta as $peserta)
                                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:10px;border-radius:10px;background:white;border:0.5px solid #f3f4f6;">
                                                <div>
                                                    <div style="font-size:12px;font-weight:600;color:#111827;">{{ $peserta->nama }}</div>
                                                    <div style="font-size:10px;color:#71717a;margin-top:3px;">{{ $peserta->jenjang_config['label'] }} · {{ ucfirst($peserta->pivot->status) }}</div>
                                                </div>
                                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                                    @if (! $peserta->pivot->naik_jenjang)
                                                        <button type="button" wire:click="luluskanPeserta('{{ $selectedPelatihan->id }}', '{{ $peserta->id }}')" style="padding:6px 10px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:10px;font-weight:700;cursor:pointer;">
                                                            Luluskan
                                                        </button>
                                                    @else
                                                        <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#dcfce7;color:#166534;">Naik jenjang</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                                Belum ada peserta.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="padding:30px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;background:white;">
                            Belum ada data pelatihan.
                        </div>
                    @endforelse
                </div>

                <div style="margin-top:12px;">
                    {{ $this->pelatihanList->links('livewire::simple-tailwind') }}
                </div>
            </div>
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
                    <input type="text" wire:model="kNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    @error('kNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">HP</label>
                        <input type="text" wire:model="kHp" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">WA</label>
                        <input type="text" wire:model="kWa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">NIK</label>
                        <input type="text" wire:model="kNik" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">No KTA</label>
                        <input type="text" wire:model="kKta" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    </div>
                </div>

                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;">Jenjang & Lokasi</div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenjang</label>
                    <select wire:model="kJenjang" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                        @foreach (\App\Models\Kader::JENJANG_OPTIONS as $key => $config)
                            <option value="{{ $key }}">{{ $config['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Dapil</label>
                        <select wire:model.live="kDapil" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih dapil</option>
                            @foreach ($this->dapilOptions as $dapil)
                                <option value="{{ $dapil }}">{{ $dapil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kecamatan</label>
                        <select wire:model.live="kKecamatan" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                        <select wire:model.live="kDesa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->formDesaOptions as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RW</label>
                        <select wire:model.live="kRw" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">RW</option>
                            @foreach ($this->formRwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RT</label>
                        <select wire:model="kRt" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                        <select wire:model="kJabatanUpa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                    <label style="font-size:12px;color:#374151;"><input type="checkbox" wire:model="kBisaDeploy"> Bisa deploy</label>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Status</label>
                        <select wire:model="kStatus" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                    <input type="text" wire:model="pelNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    @error('pelNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenjang target</label>
                        <select wire:model="pelJenjangTarget" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            @foreach (\App\Models\Kader::JENJANG_OPTIONS as $key => $config)
                                <option value="{{ $key }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenis</label>
                        <select wire:model="pelJenis" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            @foreach (\App\Models\Pelatihan::JENIS_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Tanggal</label>
                        <input type="date" wire:model="pelTanggal" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                        @error('pelTanggal') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kapasitas</label>
                        <input type="number" min="0" wire:model="pelKapasitas" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Lokasi</label>
                    <input type="text" wire:model="pelLokasi" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Instruktur</label>
                    <input type="text" wire:model="pelInstruktur" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                <div style="font-size:15px;font-weight:700;color:#111827;">Deploy Kader</div>
                <div style="font-size:11px;color:#71717a;margin-top:2px;">Pindahkan penugasan kader ke wilayah baru</div>
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
                        <select wire:model.live="deployKeDapil" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih dapil</option>
                            @foreach ($this->dapilOptions as $dapil)
                                <option value="{{ $dapil }}">{{ $dapil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Kecamatan</label>
                        <select wire:model.live="deployKeKecamatan" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
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
                        <select wire:model.live="deployKeDesa" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih desa</option>
                            @foreach ($this->deployDesaOptions as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">RW</label>
                        <select wire:model="deployKeRw" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                            <option value="">Pilih RW</option>
                            @foreach ($this->deployRwOptions as $rw)
                                <option value="{{ $rw }}">{{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Alasan</label>
                    <select wire:model="deployAlasan" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;">
                        <option value="kebutuhan_wilayah">Kebutuhan wilayah</option>
                        <option value="pemerataan">Pemerataan</option>
                        <option value="permintaan">Permintaan</option>
                    </select>
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetDeployForm" style="padding:10px 14px;border:0.5px solid #d4d4d4;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="deployKader" style="padding:10px 14px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:700;cursor:pointer;">Deploy</button>
            </div>
        </div>
    @endif

    <style>
        @media (max-width: 1280px) {
            .kaderisasi-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .kaderisasi-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 1024px) {
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
