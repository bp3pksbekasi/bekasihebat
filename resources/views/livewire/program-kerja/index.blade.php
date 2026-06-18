@php
    $summary = $this->summary;
    $selectedBidang = $this->selectedBidang;
@endphp

<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#fe5000;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-clipboard-list" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div style="font-size:14px;font-weight:600;">Program Kerja DPD</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                <select wire:model.live="selectedTahun" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
                    @for ($tahun = (int) date('Y') + 1; $tahun >= 2025; $tahun--)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">PK</div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
        @if (session()->has('message'))
            <div style="margin:14px 20px 0;padding:10px 12px;border-radius:10px;background:#ecfdf5;color:#166534;font-size:12px;border:0.5px solid #bbf7d0;">
                {{ session('message') }}
            </div>
        @endif

        <div style="padding:18px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Program Kerja DPD</h1>
                <div style="font-size:12px;color:#666;">Monitoring program tahunan, progres capaian, dan agenda kerja lintas bidang.</div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;">
                <button type="button" wire:click="openProgramForm" style="padding:6px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;background:#fe5000;color:white;cursor:pointer;">
                    + Tambah program
                </button>
                <button type="button" wire:click="openAgendaForm" style="padding:6px 12px;border:0.5px solid #16a34a;border-radius:8px;font-size:12px;font-weight:600;background:#f0fdf4;color:#166534;cursor:pointer;">
                    + Catat agenda
                </button>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;padding:18px 20px;" class="program-summary-grid">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Program</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($summary['totalProgram']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Tahun {{ $selectedTahun }}</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Berjalan</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($summary['berjalan']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Program aktif</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Selesai</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($summary['selesai']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Program tuntas</div>
            </div>
            <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:12px;padding:14px;color:white;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Progress</div>
                <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($summary['avgProgress']) }}%</div>
                <div style="font-size:11px;margin-top:4px;opacity:.85;">Rata-rata capaian</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Agenda Bulan Ini</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($summary['agendaBulanIni']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Kegiatan terjadwal</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Agenda Pending</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($summary['agendaPending']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">7 hari ke depan</div>
            </div>
        </div>

        <div style="padding:0 20px 12px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:8px;" class="program-bidang-grid">
                @foreach ($this->bidangList as $bidang)
                    @php
                        $palette = $loop->even
                            ? [
                                'bg' => '#fff7f1',
                                'border' => '#fed7aa',
                                'iconBg' => '#fe500015',
                                'icon' => '#fe5000',
                                'title' => '#c2410c',
                                'track' => '#fed7aa',
                                'fill' => '#fe5000',
                            ]
                            : [
                                'bg' => '#f5f7ff',
                                'border' => '#c7d2fe',
                                'iconBg' => '#2563eb15',
                                'icon' => '#2563eb',
                                'title' => '#1d4ed8',
                                'track' => '#c7d2fe',
                                'fill' => '#2563eb',
                            ];
                    @endphp
                    <button type="button" wire:key="bidang-card-{{ $bidang->id }}" wire:click="selectBidang('{{ $bidang->id }}')"
                        class="rounded-lg text-left transition-all cursor-pointer"
                        style="padding:11px 10px;background:{{ $palette['bg'] }};border:{{ $selectedBidangId === $bidang->id ? '1.5px' : '0.5px' }} solid {{ $selectedBidangId === $bidang->id ? $palette['fill'] : $palette['border'] }};box-shadow:{{ $selectedBidangId === $bidang->id ? '0 8px 18px rgba(15,23,42,0.08)' : 'none' }};">
                        <div style="display:flex;align-items:center;gap:7px;margin-bottom:6px;">
                            <div style="width:22px;height:22px;border-radius:7px;display:flex;align-items:center;justify-content:center;background:{{ $palette['iconBg'] }};">
                                <i class="ti ti-{{ $bidang->icon }}" style="font-size:12px;color:{{ $palette['icon'] }};" aria-hidden="true"></i>
                            </div>
                            <span style="font-size:11px;font-weight:700;color:{{ $palette['title'] }};line-height:1.2;">{{ $bidang->nama }}</span>
                        </div>
                        <div style="font-size:22px;font-weight:700;color:#111827;line-height:1;margin-bottom:2px;">{{ number_format((int) $bidang->program_count) }}</div>
                        <div style="font-size:9px;color:#71717a;line-height:1.2;">program</div>
                        <div style="height:4px;margin-top:8px;border-radius:999px;overflow:hidden;background:{{ $palette['track'] }};">
                            <div style="height:100%;width:{{ $bidang->progress }}%;background:{{ $palette['fill'] }};"></div>
                        </div>
                        <div style="font-size:9px;margin-top:5px;color:{{ $palette['title'] }};font-weight:600;">{{ $bidang->progress }}%</div>
                    </button>
                @endforeach
            </div>
        </div>

        <div style="display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);gap:14px;padding:0 20px 20px;" class="program-main-grid">
            <div style="display:grid;gap:12px;align-content:start;">
                @if ($selectedBidang)
                    <div style="background:white;border:0.5px solid {{ $selectedBidang->color }}30;border-radius:12px;padding:14px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                            <div style="display:flex;align-items:flex-start;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:{{ $selectedBidang->color }}15;display:flex;align-items:center;justify-content:center;">
                                    <i class="ti ti-{{ $selectedBidang->icon }}" style="font-size:18px;color:{{ $selectedBidang->color }};" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <div style="font-size:16px;font-weight:700;color:#111827;">{{ $selectedBidang->nama }}</div>
                                    <div style="font-size:11px;color:#71717a;margin-top:2px;">PIC: {{ $selectedBidang->pic_nama ?: 'Belum diisi' }}{{ $selectedBidang->pic_hp ? ' · '.$selectedBidang->pic_hp : '' }}</div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span style="font-size:13px;font-weight:700;color:{{ $selectedBidang->color }};">{{ $selectedBidang->progress }}%</span>
                                <button type="button" wire:click="openProgramForm('{{ $selectedBidang->id }}')" style="padding:6px 10px;border:none;border-radius:8px;background:{{ $selectedBidang->color }};color:white;font-size:11px;font-weight:600;cursor:pointer;">
                                    Tambah program
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:10px;color:{{ $selectedBidang->color }};font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Program bidang {{ $selectedBidang->nama }}</div>
                        <div style="display:grid;gap:8px;margin-top:10px;">
                            @forelse ($this->programList as $program)
                                @php $cfg = $program->status_config; @endphp
                                <div style="padding:12px;border-radius:12px;border:0.5px solid {{ $cfg['color'] }}30;background:#fff;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                            <div style="width:16px;height:16px;border-radius:5px;display:flex;align-items:center;justify-content:center;background:{{ $cfg['bg'] }};">
                                                <i class="ti ti-{{ $program->status === 'selesai' ? 'check' : ($program->status === 'berjalan' ? 'player-play' : ($program->status === 'tertunda' ? 'clock' : 'circle')) }}" style="font-size:10px;color:{{ $cfg['color'] }};" aria-hidden="true"></i>
                                            </div>
                                            <span style="font-size:12px;font-weight:600;color:#111827;">{{ $program->nama_program }}</span>
                                            <span style="font-size:9px;padding:3px 6px;border-radius:999px;background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">{{ $cfg['label'] }}</span>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <button type="button" wire:click="openRealisasi('{{ $program->id }}')" style="font-size:10px;color:#2563eb;text-decoration:underline;background:none;border:none;cursor:pointer;">Update</button>
                                            <button type="button" wire:click="editProgram('{{ $program->id }}')" style="font-size:10px;color:#71717a;text-decoration:underline;background:none;border:none;cursor:pointer;">Edit</button>
                                            <button type="button" wire:click="hapusProgram('{{ $program->id }}')" wire:confirm="Hapus program ini?" style="font-size:10px;color:#dc2626;text-decoration:underline;background:none;border:none;cursor:pointer;">Hapus</button>
                                        </div>
                                    </div>
                                    <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:10px;color:#71717a;margin-bottom:8px;">
                                        <span>Target: {{ $program->target_teks ?: trim($program->target_angka.' '.$program->satuan) ?: '-' }}</span>
                                        <span>Capaian: {{ number_format($program->realisasi) }}/{{ number_format($program->target_angka) }}</span>
                                        @if ($program->pic_nama)<span>PIC: {{ $program->pic_nama }}</span>@endif
                                        @if ($program->deadline)<span>Deadline: {{ $program->deadline->format('d M Y') }}</span>@endif
                                    </div>
                                    <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $program->progress_pct }}%;background:{{ $cfg['color'] }};"></div>
                                    </div>
                                    <div style="font-size:9px;text-align:right;margin-top:4px;color:{{ $cfg['color'] }};">{{ $program->progress_pct }}%</div>

                                    @if ($showRealisasiForm && $realProgramId === $program->id)
                                        <div style="margin-top:10px;padding:10px 12px;border-radius:10px;background:#eff6ff;border:0.5px solid #bfdbfe;">
                                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                                                <div>
                                                    <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.8px;">Update realisasi</div>
                                                    <div style="font-size:11px;color:#475569;margin-top:2px;">Capaian saat ini: {{ number_format($program->realisasi) }}</div>
                                                </div>
                                                <button type="button" wire:click="resetRealisasiForm" style="border:none;background:none;color:#64748b;font-size:11px;cursor:pointer;">
                                                    Tutup
                                                </button>
                                            </div>
                                            <div style="display:flex;align-items:end;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                                <div style="flex:1;min-width:120px;">
                                                    <label style="display:block;font-size:10px;font-weight:600;color:#475569;margin-bottom:5px;">Angka realisasi baru</label>
                                                    <input type="number" min="0" wire:model="realAngka" style="width:100%;padding:9px 10px;border:0.5px solid #bfdbfe;border-radius:9px;font-size:12px;background:white;">
                                                    @error('realAngka') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                                                </div>
                                                <button type="button" wire:click="simpanRealisasi" style="padding:9px 12px;border:none;border-radius:9px;background:#2563eb;color:white;font-size:11px;font-weight:700;cursor:pointer;">
                                                    Simpan
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($program->agendas->isNotEmpty())
                                        <div style="margin-top:10px;padding-top:8px;border-top:0.5px dashed #e5e7eb;">
                                            <div style="font-size:9px;color:#71717a;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Agenda terakhir</div>
                                            <div style="display:grid;gap:6px;">
                                                @foreach ($program->agendas as $agenda)
                                                    <div style="font-size:10px;color:#525252;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                                        <span>{{ $agenda->judul }}</span>
                                                        <span style="color:#9ca3af;">{{ $agenda->tanggal_mulai?->format('d M') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div style="padding:30px;border:0.5px dashed #d4d4d8;border-radius:12px;background:#fafafa;text-align:center;color:#9ca3af;font-size:12px;">
                                    Belum ada program untuk bidang ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                @else
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:40px;text-align:center;color:#9ca3af;">
                        <i class="ti ti-building-community" style="font-size:30px;display:block;margin-bottom:8px;" aria-hidden="true"></i>
                        Klik salah satu bidang di atas untuk membuka program kerja.
                    </div>
                @endif
            </div>

            <div style="display:grid;gap:12px;align-content:start;">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                        <div>
                            <div style="font-size:10px;color:#16a34a;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Agenda mendatang</div>
                            <div style="font-size:12px;color:#111827;font-weight:600;margin-top:2px;">7 hari ke depan</div>
                        </div>
                        <button type="button" wire:click="openAgendaForm" style="padding:6px 10px;border:0.5px solid #16a34a;border-radius:8px;background:#f0fdf4;color:#166534;font-size:11px;font-weight:600;cursor:pointer;">
                            + Catat agenda DPD
                        </button>
                    </div>
                    <div style="display:grid;gap:8px;">
                        @forelse ($this->agendaMendatang as $agenda)
                            <div style="border-left:3px solid {{ $agenda->bidang->color ?? '#16a34a' }};padding:10px 12px;background:#fafafa;border-radius:0 10px 10px 0;">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                                    <div>
                                        <div style="font-size:11px;color:#71717a;">{{ $agenda->tanggal_mulai?->translatedFormat('D, d M · H:i') }}</div>
                                        <div style="font-size:12px;font-weight:700;color:#111827;margin-top:2px;">{{ $agenda->judul }}</div>
                                        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:5px;">
                                            <span style="font-size:9px;padding:3px 6px;border-radius:999px;background:#eff6ff;color:#1d4ed8;">{{ \App\Models\AgendaDpd::JENIS_OPTIONS[$agenda->jenis] ?? ucfirst($agenda->jenis) }}</span>
                                            @if ($agenda->lokasi)
                                                <span style="font-size:9px;color:#71717a;">{{ $agenda->lokasi }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" wire:click="selesaikanAgenda('{{ $agenda->id }}')" style="padding:5px 8px;border:none;border-radius:8px;background:#16a34a;color:white;font-size:10px;font-weight:600;cursor:pointer;">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                Belum ada agenda terjadwal dalam 7 hari ke depan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                    <div style="font-size:10px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:10px;">Kegiatan terbaru</div>
                    <div style="display:grid;gap:10px;">
                        @forelse ($this->kegiatanTerbaru as $agenda)
                            <div style="display:flex;gap:10px;align-items:flex-start;">
                                <div style="width:10px;height:10px;border-radius:999px;background:{{ $agenda->bidang->color ?? '#2563eb' }};margin-top:4px;flex-shrink:0;"></div>
                                <div style="flex:1;">
                                    <div style="font-size:11px;color:#71717a;">{{ $agenda->tanggal_mulai?->format('d M Y H:i') }}</div>
                                    <div style="font-size:12px;font-weight:700;color:#111827;margin-top:2px;">{{ $agenda->judul }}</div>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                                        @if ($agenda->bidang)
                                            <span style="font-size:9px;padding:3px 6px;border-radius:999px;background:{{ $agenda->bidang->color }}15;color:{{ $agenda->bidang->color }};">{{ $agenda->bidang->nama }}</span>
                                        @endif
                                        <span style="font-size:9px;color:#71717a;">Peserta hadir: {{ number_format($agenda->peserta_hadir) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="padding:20px;text-align:center;color:#9ca3af;font-size:12px;border:0.5px dashed #d4d4d8;border-radius:12px;">
                                Belum ada kegiatan selesai.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($showProgramForm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60;" wire:click="resetProgramForm"></div>
        <div style="position:fixed;top:0;right:0;width:420px;max-width:100%;height:100vh;background:white;z-index:61;box-shadow:-10px 0 30px rgba(0,0,0,0.12);overflow-y:auto;">
            <div style="padding:18px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">{{ $pgEditId ? 'Edit Program' : 'Tambah Program' }}</div>
                    <div style="font-size:11px;color:#71717a;margin-top:2px;">Isi target dan timeline program bidang</div>
                </div>
                <button type="button" wire:click="resetProgramForm" style="border:none;background:none;color:#71717a;font-size:14px;cursor:pointer;">✕</button>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Bidang</label>
                    <select wire:model="pgBidangId" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        <option value="">Pilih bidang</option>
                        @foreach ($this->bidangOptions as $bidang)
                            <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                        @endforeach
                    </select>
                    @error('pgBidangId') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Nama program</label>
                    <input type="text" wire:model="pgNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    @error('pgNama') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Deskripsi</label>
                    <textarea wire:model="pgDeskripsi" rows="3" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1.2fr .8fr .8fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Target teks</label>
                        <input type="text" wire:model="pgTargetTeks" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Target angka</label>
                        <input type="number" min="0" wire:model="pgTargetAngka" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Satuan</label>
                        <input type="text" wire:model="pgSatuan" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Periode</label>
                        <select wire:model="pgPeriode" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            <option value="Q1">Q1</option>
                            <option value="Q2">Q2</option>
                            <option value="Q3">Q3</option>
                            <option value="Q4">Q4</option>
                            <option value="sepanjang_tahun">Sepanjang tahun</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Deadline</label>
                        <input type="date" wire:model="pgDeadline" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">PIC</label>
                        <input type="text" wire:model="pgPicNama" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Status</label>
                        <select wire:model="pgStatus" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @foreach (\App\Models\ProgramKerja::STATUS_CONFIG as $key => $status)
                                <option value="{{ $key }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetProgramForm" style="padding:10px 14px;border:0.5px solid #d4d4d4;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="simpanProgram" style="padding:10px 14px;border:none;border-radius:10px;background:#fe5000;color:white;font-size:12px;font-weight:700;cursor:pointer;">Simpan</button>
            </div>
        </div>
    @endif

    @if ($showAgendaForm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60;" wire:click="resetAgendaForm"></div>
        <div style="position:fixed;top:0;right:0;width:420px;max-width:100%;height:100vh;background:white;z-index:61;box-shadow:-10px 0 30px rgba(0,0,0,0.12);overflow-y:auto;">
            <div style="padding:18px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">Catat Agenda DPD</div>
                    <div style="font-size:11px;color:#71717a;margin-top:2px;">Jadwalkan rapat, kunjungan, atau kegiatan bidang</div>
                </div>
                <button type="button" wire:click="resetAgendaForm" style="border:none;background:none;color:#71717a;font-size:14px;cursor:pointer;">✕</button>
            </div>
            <div style="padding:18px;display:grid;gap:12px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Bidang</label>
                    <select wire:model.live="agBidangId" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        <option value="">Umum / lintas bidang</option>
                        @foreach ($this->bidangOptions as $bidang)
                            <option value="{{ $bidang->id }}">{{ $bidang->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Program terkait</label>
                    <select wire:model="agProgramId" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        <option value="">Opsional</option>
                        @foreach ($this->programOptions as $program)
                            <option value="{{ $program->id }}">{{ $program->nama_program }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Judul</label>
                    <input type="text" wire:model="agJudul" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    @error('agJudul') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Jenis</label>
                        <select wire:model="agJenis" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                            @foreach (\App\Models\AgendaDpd::JENIS_OPTIONS as $key => $jenis)
                                <option value="{{ $key }}">{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Tanggal</label>
                        <input type="datetime-local" wire:model="agTanggal" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                        @error('agTanggal') <div style="font-size:10px;color:#dc2626;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Lokasi</label>
                        <input type="text" wire:model="agLokasi" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Dapil terkait</label>
                        <input type="text" wire:model="agDapil" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Peserta target</label>
                    <input type="number" min="0" wire:model="agPesertaTarget" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#444;margin-bottom:5px;">Catatan</label>
                    <textarea wire:model="agCatatan" rows="3" style="width:100%;padding:10px;border:0.5px solid #d4d4d4;border-radius:10px;font-size:12px;color:#111827;background:white;"></textarea>
                </div>
            </div>
            <div style="padding:18px;border-top:0.5px solid #e5e5e5;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" wire:click="resetAgendaForm" style="padding:10px 14px;border:0.5px solid #d4d4d4;border-radius:10px;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                <button type="button" wire:click="simpanAgenda" style="padding:10px 14px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:12px;font-weight:700;cursor:pointer;">Simpan</button>
            </div>
        </div>
    @endif

    <style>
        @media (max-width: 1280px) {
            .program-bidang-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }

            .program-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 1024px) {
            .program-main-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .program-bidang-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 720px) {
            .program-bidang-grid,
            .program-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
    </style>
</div>
