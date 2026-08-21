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

<div class="min-h-screen p-5 bg-gray-50">
    <!-- Header Title -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Program</h1>
            <p class="text-sm text-gray-500 mt-1">Manajemen agenda, approval, dan publikasi kegiatan lintas wilayah.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-xs text-gray-500">Mode tampilan {{ $viewMode === 'table' ? 'tabel' : 'cards' }} · {{ number_format($events->total()) }} program</div>
            <a href="{{ route('events.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-600 shadow-sm transition">
                <i class="ti ti-plus mr-2 text-base"></i>
                Buat Program
            </a>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-5">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-[1.5]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Cari Program</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm" placeholder="Cari judul, lokasi, PIC...">
            </div>
            
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Dapil</label>
                <select wire:model.live="filterDapil" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Dapil</option>
                    @foreach($this->dapilOptions as $dapil)
                        <option value="{{ $dapil }}">{{ $dapil }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Kecamatan</label>
                <select wire:model.live="filterKecamatan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Kecamatan</option>
                    @foreach($this->kecamatanOptions as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Desa/Kelurahan</label>
                <select wire:model.live="filterDesa" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Desa</option>
                    @foreach($this->desaOptions as $desa)
                        <option value="{{ $desa }}">{{ $desa }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Bidang</label>
                <select wire:model.live="filterBidang" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Bidang</option>
                    @foreach($this->bidangOptions as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Cards (Event Summary Grid) -->
    <div class="grid gap-4 mb-6 event-summary-grid-new">
        @foreach ($counts as $status => $cfg)
            <button wire:click="setStatus('{{ $status }}')" type="button" class="bg-white border rounded-xl p-5 shadow-sm text-left transition-all hover:border-orange-300 {{ $filterStatus === $status ? 'ring-2 ring-orange-500 border-orange-500' : 'border-gray-200' }}">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">{{ $cfg['label'] }}</div>
                <div class="text-3xl font-bold" style="color:{{ $cfg['color'] }};">{{ number_format($cfg['count']) }}</div>
            </button>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">



            {{-- Filters on Top Right of Table --}}
            <div class="flex items-center justify-end gap-3 flex-wrap mb-4">
                <select wire:model.live="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua status</option>
                    @foreach (\App\Models\Event::STATUS_CONFIG as $key => $cfg)
                        <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterJenis" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua jenis</option>
                    @foreach (\App\Models\Event::JENIS_EVENT as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterLevel" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua level</option>
                    <option value="dpra">DPRa</option>
                    <option value="dpc">DPC</option>
                    <option value="dpd">DPD</option>
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
                                    
                                    {{-- Badge org_level --}}
                                    <span style="
                                        font-size:9px;padding:2px 7px;border-radius:4px;font-weight:500;
                                        background:{{ $event->org_level === 'dpd' ? '#fee2e2' : ($event->org_level === 'dpc' ? '#dbeafe' : '#dcfce7') }};
                                        color:{{ $event->org_level === 'dpd' ? '#991b1b' : ($event->org_level === 'dpc' ? '#1e3a8a' : '#14532d') }};">
                                        {{ strtoupper($event->org_level ?? '') }}
                                    </span>

                                    {{-- Badge bidang --}}
                                    @if($event->bidang)
                                    <span style="font-size:9px;padding:2px 7px;border-radius:4px;background:#f5f5f5;color:#52525b;">
                                        {{ $event->bidang->nama }}
                                    </span>
                                    @endif

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
                            Belum ada program pada filter ini.
                        </div>
                    @endforelse
                </div>
            @else
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;overflow:hidden;background:white;">
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead style="background:#fafafa;">
                                <tr style="border-bottom:0.5px solid #e5e7eb;">
                                    <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Judul Program</th>
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
                                            <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                                                <span style="display:inline-flex;padding:4px 8px;border-radius:999px;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};font-size:10px;font-weight:600;">{{ $cfg['label'] }}</span>
                                                
                                                <div style="display:flex;gap:4px;">
                                                    {{-- Badge org_level --}}
                                                    <span style="
                                                        font-size:9px;padding:2px 7px;border-radius:4px;font-weight:500;
                                                        background:{{ $event->org_level === 'dpd' ? '#fee2e2' : ($event->org_level === 'dpc' ? '#dbeafe' : '#dcfce7') }};
                                                        color:{{ $event->org_level === 'dpd' ? '#991b1b' : ($event->org_level === 'dpc' ? '#1e3a8a' : '#14532d') }};">
                                                        {{ strtoupper($event->org_level ?? '') }}
                                                    </span>

                                                    {{-- Badge bidang --}}
                                                    @if($event->bidang)
                                                    <span style="font-size:9px;padding:2px 7px;border-radius:4px;background:#f5f5f5;color:#52525b;border:0.5px solid #e5e5e5;">
                                                        {{ $event->bidang->nama }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
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
                                                @if(in_array($event->status, [\App\Models\Event::STATUS_DRAFT, \App\Models\Event::STATUS_DITOLAK], true))
                                                    <a href="{{ route('events.edit', $event) }}" wire:navigate style="padding:5px 9px;border-radius:7px;border:0.5px solid #d4d4d8;background:white;color:#444;text-decoration:none;font-size:11px;">Edit</a>
                                                @endif
                                                <button wire:click="confirmDelete('{{ $event->uuid }}')" type="button" style="padding:5px 9px;border-radius:7px;border:0.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:11px;cursor:pointer;">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="padding:34px 16px;text-align:center;font-size:12px;color:#888;background:#fafafa;">Belum ada program pada filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                {{ $events->links() }}
            </div>
        </div>
    </div>

    @if ($showDeleteConfirm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="cancelDelete"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:360px;max-width:calc(100vw - 32px);background:white;border-radius:14px;box-shadow:0 18px 40px rgba(0,0,0,0.16);z-index:50;padding:18px;">
            <div style="font-size:15px;font-weight:600;color:#1a1a1a;">Hapus program?</div>
            <div style="font-size:12px;color:#666;margin-top:6px;">Data program, approval, RAB, dan laporan terkait akan ikut terhapus.</div>
            <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:8px;">
                <button wire:click="cancelDelete" type="button" style="height:38px;padding:0 12px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;cursor:pointer;">Batal</button>
                <button wire:click="deleteEvent" type="button" style="height:38px;padding:0 12px;border-radius:8px;border:none;background:#dc2626;color:white;cursor:pointer;">Hapus</button>
            </div>
        </div>
    @endif

    <style>
        .event-summary-grid-new {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
        @media (max-width: 1100px) {
            .event-cards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
            .event-summary-grid-new {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .event-cards-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
            .event-summary-grid-new {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
        @media (max-width: 480px) {
            .event-summary-grid-new {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
</div>
