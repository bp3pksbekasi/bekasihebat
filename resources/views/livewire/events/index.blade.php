@php
    $summary = $this->summary;
    $events = $this->events;
    $counts = [
        'draft' => ['label' => 'Draft', 'color' => '#888888', 'count' => $summary['draft']],
        'menunggu_approval' => ['label' => 'Menunggu', 'color' => '#d97706', 'count' => $summary['menunggu']],
        'disetujui' => ['label' => 'Disetujui', 'color' => '#16a34a', 'count' => $summary['disetujui']],
        'berlangsung' => ['label' => 'Berlangsung', 'color' => '#2563eb', 'count' => $summary['berlangsung']],
        'selesai' => ['label' => 'Selesai', 'color' => '#16a34a', 'count' => $summary['selesai']],
    ];
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-radius:14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:15px;font-weight:500;">Kegiatan / Event</div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                    <select wire:model.live="filterStatus" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:170px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua status</option>
                        @foreach (\App\Models\Event::STATUS_CONFIG as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterJenis" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:170px;background:#27272a;color:#f4f4f5;">
                        <option value="">Semua jenis</option>
                        @foreach (\App\Models\Event::JENIS_EVENT as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterDapil" style="height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;min-width:160px;background:#fff7f1;color:#993c1d;font-weight:500;">
                        <option value="">Semua dapil</option>
                        @foreach ($this->dapilOptions as $dapil)
                            <option value="{{ $dapil }}">{{ $dapil }}</option>
                        @endforeach
                    </select>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul, lokasi, PIC..." style="width:220px;height:38px;border-radius:8px;border:0.5px solid #3f3f46;padding:0 12px;font-size:12px;background:#27272a;color:#f4f4f5;">
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">EV</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;padding:16px 20px 20px;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            <div style="padding-top:2px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Kegiatan / Event</h1>
                    <div style="font-size:12px;color:#666;">Manajemen agenda, approval, dan publikasi kegiatan lintas wilayah.</div>
                </div>
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                    <div style="font-size:11px;color:#888;">Mode tampilan {{ $viewMode === 'table' ? 'tabel' : 'cards' }} · {{ number_format($events->total()) }} event</div>
                    <a href="{{ route('events.create') }}" wire:navigate style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;background:#fe5000;color:white;text-decoration:none;font-size:12px;font-weight:600;">
                        <i class="ti ti-plus" aria-hidden="true"></i>
                        <span>Buat Event Baru</span>
                    </a>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px;margin:16px 0;" class="event-summary-grid">
                @foreach ($counts as $status => $cfg)
                    <button wire:click="setStatus('{{ $status }}')" type="button" style="text-align:center;border-radius:10px;padding:14px 10px;border:0.5px solid {{ $filterStatus === $status ? '#fb923c' : '#e5e7eb' }};background:{{ $filterStatus === $status ? '#fff7ed' : 'white' }};cursor:pointer;">
                        <div style="font-size:24px;font-weight:600;color:{{ $cfg['color'] }};">{{ number_format($cfg['count']) }}</div>
                        <div style="font-size:11px;color:#666;margin-top:4px;">{{ $cfg['label'] }}</div>
                    </button>
                @endforeach
            </div>

            @if ($viewMode === 'cards')
                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;" class="event-cards-grid">
                    @forelse ($events as $event)
                        @php $cfg = $event->status_config; @endphp
                        <a href="{{ route('events.detail', $event) }}" wire:navigate style="display:block;border-radius:14px;border:0.5px solid #e5e7eb;background:white;overflow:hidden;text-decoration:none;color:inherit;">
                            <div style="position:relative;height:116px;background:linear-gradient(135deg, {{ $cfg['color'] }}20, {{ $cfg['color'] }}40);display:flex;align-items:center;justify-content:center;">
                                @if ($event->cover_image)
                                    <img src="{{ asset('storage/' . $event->cover_image) }}" alt="Cover" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="ti ti-calendar-event" style="font-size:30px;color:{{ $cfg['color'] }};opacity:0.6;" aria-hidden="true"></i>
                                @endif
                                <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;flex-wrap:wrap;">
                                    <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};font-weight:600;">{{ $cfg['label'] }}</span>
                                    @if ($event->is_public)
                                        <span style="font-size:10px;padding:3px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-weight:600;">Publik</span>
                                    @endif
                                </div>
                            </div>
                            <div style="padding:14px;">
                                <div style="font-size:11px;color:#888;">{{ $event->tanggal_mulai?->format('d M Y, H:i') ?? '-' }}</div>
                                <div style="font-size:14px;font-weight:600;color:#1a1a1a;margin-top:6px;line-height:1.45;">{{ $event->judul }}</div>
                                <div style="font-size:11px;color:#666;margin-top:6px;">{{ $event->jenis_label }} · {{ $event->lokasi_desa ?? $event->lokasi }}</div>
                                <div style="display:flex;align-items:center;gap:5px;margin-top:10px;">
                                    @foreach (['dpra', 'dpc', 'dpd'] as $level)
                                        @php $approval = $event->approvals->firstWhere('level', $level); @endphp
                                        <span style="width:8px;height:8px;border-radius:50%;display:inline-block;background:{{ $approval?->status === 'approved' ? '#22c55e' : ($approval?->status === 'rejected' ? '#ef4444' : ($event->status === 'menunggu_approval' && $event->level_approval === $level ? '#f97316' : '#d4d4d8')) }};"></span>
                                    @endforeach
                                    <span style="font-size:10px;color:#888;margin-left:2px;">{{ $event->approvals->where('status', 'approved')->count() }}/3 approved</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div style="grid-column:1/-1;border:0.5px dashed #d4d4d8;border-radius:12px;padding:34px 16px;text-align:center;font-size:12px;color:#888;background:#fafafa;">
                            Belum ada event pada filter ini.
                        </div>
                    @endforelse
                </div>
            @else
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;overflow:hidden;background:white;">
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead style="background:#fafafa;">
                                <tr style="border-bottom:0.5px solid #e5e7eb;">
                                    <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Judul</th>
                                    <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Jenis</th>
                                    <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Tanggal</th>
                                    <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Lokasi</th>
                                    <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Status</th>
                                    <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Approval</th>
                                    <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Publik</th>
                                    <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $event)
                                    @php $cfg = $event->status_config; @endphp
                                    <tr style="border-bottom:0.5px solid #f1f5f9;">
                                        <td style="padding:12px;">
                                            <div style="font-weight:600;color:#1a1a1a;">{{ $event->judul }}</div>
                                            <div style="font-size:10px;color:#888;margin-top:4px;">PIC: {{ $event->pic_nama ?: '-' }}</div>
                                        </td>
                                        <td style="padding:12px;color:#525252;">{{ $event->jenis_label }}</td>
                                        <td style="padding:12px;color:#525252;">{{ $event->tanggal_mulai?->format('d M Y, H:i') ?? '-' }}</td>
                                        <td style="padding:12px;color:#525252;">{{ $event->lokasi_desa ?? $event->lokasi }}</td>
                                        <td style="padding:12px;text-align:center;">
                                            <span style="display:inline-flex;padding:4px 8px;border-radius:999px;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};font-size:10px;font-weight:600;">{{ $cfg['label'] }}</span>
                                        </td>
                                        <td style="padding:12px;text-align:center;">
                                            <div style="display:flex;align-items:center;justify-content:center;gap:5px;">
                                                @foreach (['dpra', 'dpc', 'dpd'] as $level)
                                                    @php $approval = $event->approvals->firstWhere('level', $level); @endphp
                                                    <span title="{{ strtoupper($level) }}" style="width:9px;height:9px;border-radius:50%;display:inline-block;background:{{ $approval?->status === 'approved' ? '#22c55e' : ($approval?->status === 'rejected' ? '#ef4444' : ($event->status === 'menunggu_approval' && $event->level_approval === $level ? '#f97316' : '#d4d4d8')) }};"></span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td style="padding:12px;text-align:center;">
                                            <button wire:click="togglePublic('{{ $event->uuid }}')" type="button" style="width:40px;height:22px;border-radius:999px;border:none;padding:2px;background:{{ $event->status === 'disetujui' ? ($event->is_public ? '#16a34a' : '#d4d4d8') : '#e5e7eb' }};cursor:pointer;{{ $event->status !== 'disetujui' ? 'opacity:.7;' : '' }}">
                                                <span style="display:block;width:18px;height:18px;border-radius:50%;background:white;transform:translateX({{ $event->is_public ? '18px' : '0' }});transition:transform .2s;"></span>
                                            </button>
                                        </td>
                                        <td style="padding:12px;text-align:center;">
                                            <div style="display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;">
                                                <a href="{{ route('events.detail', $event) }}" wire:navigate style="padding:5px 9px;border-radius:7px;border:0.5px solid #d4d4d8;background:white;color:#444;text-decoration:none;font-size:11px;">Lihat</a>
                                                <a href="{{ route('events.edit', $event) }}" wire:navigate style="padding:5px 9px;border-radius:7px;border:0.5px solid #d4d4d8;background:white;color:#444;text-decoration:none;font-size:11px;">Edit</a>
                                                <button wire:click="confirmDelete('{{ $event->uuid }}')" type="button" style="padding:5px 9px;border-radius:7px;border:0.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:11px;cursor:pointer;">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="padding:34px 16px;text-align:center;font-size:12px;color:#888;background:#fafafa;">Belum ada event pada filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div style="margin-top:14px;">
                {{ $events->links() }}
            </div>
        </div>
    </div>

    @if ($showDeleteConfirm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="cancelDelete"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:360px;max-width:calc(100vw - 32px);background:white;border-radius:14px;box-shadow:0 18px 40px rgba(0,0,0,0.16);z-index:50;padding:18px;">
            <div style="font-size:15px;font-weight:600;color:#1a1a1a;">Hapus event?</div>
            <div style="font-size:12px;color:#666;margin-top:6px;">Data event, approval, RAB, dan laporan terkait akan ikut terhapus.</div>
            <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:8px;">
                <button wire:click="cancelDelete" type="button" style="height:38px;padding:0 12px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;cursor:pointer;">Batal</button>
                <button wire:click="deleteEvent" type="button" style="height:38px;padding:0 12px;border-radius:8px;border:none;background:#dc2626;color:white;cursor:pointer;">Hapus</button>
            </div>
        </div>
    @endif

    <style>
        @media (max-width: 1100px) {
            .event-summary-grid,
            .event-cards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .event-summary-grid,
            .event-cards-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
