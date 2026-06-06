@php
    $statusCfg = $event->status_config;
    $currentApprovalLevel = $event->level_approval;
    $registrations = $event->registrations;
    $registrationStats = [
        'total' => $registrations->count(),
        'confirmed' => $registrations->where('status', 'confirmed')->count(),
        'attended' => $registrations->where('status', 'attended')->count(),
    ];
@endphp

<div style="min-height:100vh;background:#fafafa;">
    <div style="width:100%;margin:0;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <div style="font-size:11px;color:#a3a3a3;">Event &gt; {{ $event->judul }}</div>
                <div style="font-size:18px;font-weight:600;margin-top:4px;">{{ $event->judul }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                @if (in_array($event->status, ['draft', 'ditolak'], true))
                    <button wire:click="submitForApproval" type="button" style="height:38px;padding:0 14px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                        Ajukan Approval
                    </button>
                @endif
                @if ($event->status === 'disetujui')
                    <button wire:click="setEventStatus('berlangsung')" type="button" style="height:38px;padding:0 12px;border-radius:10px;border:0.5px solid #dbeafe;background:#eff6ff;color:#2563eb;font-size:12px;cursor:pointer;">
                        Tandai Berlangsung
                    </button>
                    <button wire:click="setEventStatus('selesai')" type="button" style="height:38px;padding:0 12px;border-radius:10px;border:0.5px solid #bbf7d0;background:#ecfdf3;color:#166534;font-size:12px;cursor:pointer;">
                        Tandai Selesai
                    </button>
                @endif
                @if ($event->status !== 'dibatalkan')
                    <button wire:click="setEventStatus('dibatalkan')" type="button" style="height:38px;padding:0 12px;border-radius:10px;border:0.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:12px;cursor:pointer;">
                        Batalkan
                    </button>
                @endif
                @if (in_array($event->status, ['draft', 'ditolak'], true))
                    <a href="{{ route('events.edit', $event) }}" wire:navigate style="display:inline-flex;align-items:center;height:38px;padding:0 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;text-decoration:none;font-size:12px;">
                        Edit
                    </a>
                @endif
            </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;padding:16px 20px 20px;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                <span style="padding:5px 10px;border-radius:999px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:11px;font-weight:600;">{{ $statusCfg['label'] }}</span>
                <span style="padding:5px 10px;border-radius:999px;background:#fff7ed;color:#c2410c;font-size:11px;font-weight:600;">{{ $event->jenis_label }}</span>
                <span style="font-size:11px;color:#666;">{{ $event->tanggal_mulai?->format('d M Y, H:i') ?? '-' }}</span>
                <span style="font-size:11px;color:#666;">{{ $event->lokasi }}</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:14px;" class="event-detail-summary-grid">
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Penyelenggara</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->penyelenggara ?: '-' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Dibuat oleh {{ $event->creator?->name ?? '-' }}</div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">PIC</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->pic_nama ?: '-' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ $event->pic_hp ?: 'No HP belum diisi' }}</div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Kapasitas</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->kapasitas > 0 ? number_format($event->kapasitas) . ' peserta' : 'Unlimited' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ $event->lokasi_desa ?: '-' }} · {{ $event->lokasi_kecamatan ?: '-' }}</div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Publikasi</div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                        <div>
                            <div style="font-size:15px;font-weight:600;color:#1a1a1a;">{{ $event->is_public ? 'Publik' : 'Internal' }}</div>
                            <div style="font-size:11px;color:#888;margin-top:4px;">Toggle aktif hanya saat event disetujui</div>
                        </div>
                        <button wire:click="togglePublic" type="button" style="width:42px;height:24px;border:none;border-radius:999px;padding:2px;background:{{ $event->status === 'disetujui' ? ($event->is_public ? '#16a34a' : '#d4d4d8') : '#e5e7eb' }};cursor:pointer;">
                            <span style="display:block;width:20px;height:20px;border-radius:50%;background:white;transform:translateX({{ $event->is_public ? '18px' : '0' }});transition:transform .2s;"></span>
                        </button>
                    </div>
                </div>
            </div>

            @if ($event->deskripsi)
                <div style="margin-bottom:14px;padding:14px;border-radius:12px;border:0.5px solid #e5e7eb;background:white;">
                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Deskripsi</div>
                    <div style="font-size:13px;color:#444;line-height:1.7;margin-top:8px;">{{ $event->deskripsi }}</div>
                </div>
            @endif

            @if ($event->kegiatanRw)
                <div style="margin-bottom:14px;padding:11px 12px;border-radius:10px;background:#eff6ff;border:0.5px solid #bfdbfe;color:#1d4ed8;font-size:12px;">
                    Dibuat dari kegiatan Sisir RW di {{ $event->kegiatanRw->desa }} RW {{ $event->kegiatanRw->nomor_rw }}.
                    <a href="{{ route('sisir-rw.index') }}" wire:navigate style="color:#1d4ed8;font-weight:600;text-decoration:none;">Lihat kegiatan asal</a>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:minmax(0,1.05fr) minmax(360px,0.95fr);gap:14px;" class="event-detail-top-grid">
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Approval Tracker</div>
                    <div style="margin-top:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        @foreach (['dpra' => 'DPRa', 'dpc' => 'DPC', 'dpd' => 'DPD'] as $level => $label)
                            @php
                                $approval = $event->approvals->firstWhere('level', $level);
                                $isCurrent = $event->level_approval === $level && $event->status === 'menunggu_approval';
                            @endphp
                            <div style="flex:1;min-width:110px;text-align:center;">
                                <div style="width:42px;height:42px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;background:{{ $approval?->status === 'approved' ? '#22c55e' : ($approval?->status === 'rejected' ? '#ef4444' : ($isCurrent ? '#f97316' : '#e5e7eb')) }};color:white;font-size:16px;font-weight:700;">
                                    {{ $approval?->status === 'approved' ? '✓' : ($approval?->status === 'rejected' ? '✕' : ($isCurrent ? '●' : '○')) }}
                                </div>
                                <div style="font-size:11px;font-weight:600;color:#1a1a1a;margin-top:8px;">{{ $label }}</div>
                                <div style="font-size:10px;color:#888;margin-top:2px;">{{ $approval?->approver?->name ?? ($approval?->status === 'pending' ? 'Pending' : '-') }}</div>
                                @if ($approval?->decided_at)
                                    <div style="font-size:10px;color:#888;margin-top:2px;">{{ $approval->decided_at->format('d M Y H:i') }}</div>
                                @endif
                                @if ($approval?->catatan)
                                    <div style="font-size:10px;color:#666;line-height:1.5;margin-top:5px;">{{ $approval->catatan }}</div>
                                @endif
                            </div>
                            @if (! $loop->last)
                                <div style="width:32px;height:1px;background:#d4d4d8;"></div>
                            @endif
                        @endforeach
                    </div>

                    @if ($event->status === 'menunggu_approval' && $this->canApproveLevel($currentApprovalLevel))
                        <div style="margin-top:16px;padding-top:16px;border-top:0.5px solid #e5e7eb;">
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">Approval level {{ strtoupper($currentApprovalLevel) }}</div>
                            <textarea wire:model="approvalNotes.{{ $currentApprovalLevel }}" rows="3" placeholder="Catatan approve / reject" style="width:100%;margin-top:8px;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                            <div style="margin-top:10px;display:flex;gap:8px;">
                                <button wire:click="approve('{{ $currentApprovalLevel }}')" type="button" style="height:38px;padding:0 14px;border-radius:10px;border:none;background:#16a34a;color:white;font-size:12px;font-weight:600;cursor:pointer;">Setujui</button>
                                <button wire:click="reject('{{ $currentApprovalLevel }}')" type="button" style="height:38px;padding:0 14px;border-radius:10px;border:none;background:#dc2626;color:white;font-size:12px;font-weight:600;cursor:pointer;">Tolak</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:10px;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RAB</div>
                            <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">Rancangan anggaran kegiatan</div>
                        </div>
                        <div style="font-size:11px;color:#888;">Total {{ number_format($event->total_budget, 0, ',', '.') }}</div>
                    </div>

                    <div style="display:grid;gap:8px;">
                        @forelse ($event->budgetItems as $item)
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;padding:10px;border-radius:10px;background:#fafafa;border:0.5px solid #eef2f7;">
                                <div>
                                    <div style="font-size:12px;font-weight:600;color:#1a1a1a;">{{ $item->item }}</div>
                                    <div style="font-size:11px;color:#666;margin-top:4px;">{{ $item->qty }} {{ $item->satuan }} · {{ $item->kategori ?: 'Umum' }} · {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div style="font-size:12px;font-weight:600;color:#1a1a1a;">{{ number_format((float) $item->subtotal, 0, ',', '.') }}</div>
                                    <button wire:click="editBudgetItem('{{ $item->id }}')" type="button" style="padding:5px 9px;border-radius:7px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:11px;cursor:pointer;">Edit</button>
                                    <button wire:click="removeBudgetItem('{{ $item->id }}')" type="button" style="padding:5px 9px;border-radius:7px;border:0.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:11px;cursor:pointer;">Hapus</button>
                                </div>
                            </div>
                        @empty
                            <div style="padding:18px;border-radius:10px;border:0.5px dashed #d4d4d8;background:#fafafa;text-align:center;font-size:12px;color:#888;">Belum ada item anggaran.</div>
                        @endforelse
                    </div>

                    <div style="margin-top:12px;padding-top:12px;border-top:0.5px solid #e5e7eb;display:grid;gap:10px;">
                        <div style="font-size:12px;font-weight:600;color:#1a1a1a;">{{ $budgetEditId ? 'Edit item' : '+ Tambah item' }}</div>
                        <input wire:model="budgetItem" type="text" placeholder="Nama item" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;" class="event-detail-budget-grid">
                            <input wire:model="budgetKategori" type="text" placeholder="Kategori" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                            <input wire:model="budgetQty" type="number" min="1" placeholder="Qty" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                            <input wire:model="budgetSatuan" type="text" placeholder="Satuan" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                            <input wire:model="budgetHargaSatuan" type="number" min="0" step="0.01" placeholder="Harga" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                        </div>
                        <textarea wire:model="budgetKeterangan" rows="2" placeholder="Keterangan item" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:12px;resize:vertical;"></textarea>
                        <button wire:click="saveBudgetItem" type="button" style="height:38px;padding:0 14px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;width:max-content;">
                            {{ $budgetEditId ? 'Update Item' : 'Simpan Item' }}
                        </button>
                    </div>
                </div>
            </div>

            @php $pSum = $this->pesertaSummary; @endphp
            <div style="margin-top:14px;border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Peserta Event</div>
                        <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">
                            {{ number_format($pSum['total']) }} peserta
                            <span style="font-size:11px;color:#888;">· {{ number_format($pSum['unik_rw']) }} RW berbeda</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @if ($pSum['unsynced'] > 0)
                            <button wire:click="syncSemuaKeSapaWarga" type="button" style="padding:7px 10px;border-radius:8px;border:0.5px solid #86efac;background:#f0fdf4;color:#166534;font-size:11px;font-weight:600;cursor:pointer;">
                                Sync {{ number_format($pSum['unsynced']) }} ke Sapa Warga
                            </button>
                        @endif
                        <button wire:click="$set('showPesertaForm', true)" type="button" style="padding:7px 12px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:11px;font-weight:600;cursor:pointer;">
                            + Input Peserta
                        </button>
                    </div>
                </div>

                @if ($pSum['per_rw']->isNotEmpty())
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
                        @foreach ($pSum['per_rw'] as $rw => $count)
                            @php
                                $badgeBg = $count >= 10 ? '#dcfce7' : ($count >= 5 ? '#fef3c7' : '#f4f4f5');
                                $badgeText = $count >= 10 ? '#166534' : ($count >= 5 ? '#b45309' : '#71717a');
                            @endphp
                            <span style="padding:4px 8px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeText }};font-size:10px;font-weight:600;">
                                RW {{ $rw === '?' ? '?' : $rw }}: {{ number_format($count) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if ($showPesertaForm)
                    <div style="margin-top:12px;padding:12px;border-radius:12px;border:0.5px solid #fed7aa;background:#fff7ed;">
                        <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:white;border:0.5px solid #fed7aa;">
                            <button wire:click="$set('pesertaTab', 'bulk')" type="button" style="padding:7px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $pesertaTab === 'bulk' ? '#fe5000' : 'transparent' }};color:{{ $pesertaTab === 'bulk' ? 'white' : '#71717a' }};">
                                Bulk paste
                            </button>
                            <button wire:click="$set('pesertaTab', 'single')" type="button" style="padding:7px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $pesertaTab === 'single' ? '#fe5000' : 'transparent' }};color:{{ $pesertaTab === 'single' ? 'white' : '#71717a' }};">
                                Satu-satu
                            </button>
                        </div>

                        @if ($pesertaTab === 'bulk')
                            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:12px;" class="event-peserta-grid">
                                <select wire:model.live="bulkDefaultDapil" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                    <option value="">Dapil (opsional)</option>
                                    @foreach ($this->bulkDapilOptions as $dapil)
                                        <option value="{{ $dapil }}">{{ $dapil }}</option>
                                    @endforeach
                                </select>
                                <select wire:model.live="bulkDefaultKecamatan" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                    <option value="">Kecamatan</option>
                                    @foreach ($this->bulkKecamatanOptions as $kecamatan)
                                        <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                                    @endforeach
                                </select>
                                <select wire:model.live="bulkDefaultDesa" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                    <option value="">Desa</option>
                                    @foreach ($this->bulkDesaOptions as $desaOption)
                                        <option value="{{ $desaOption->id }}">{{ $desaOption->desa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="font-size:11px;color:#666;margin-top:10px;">Paste format: <strong>nama, RW, no HP, aspirasi (opsional)</strong> per baris</div>
                            <textarea wire:model.live.debounce.500ms="bulkPesertaText" rows="5" placeholder="Ahmad Fauzi, 003, 08123456789, Perbaikan drainase gang utama&#10;Siti Nurhaliza, 008, 08567891234&#10;Pak Rohman, 012, 08789012345, Penerangan jalan lingkungan" style="width:100%;margin-top:8px;border-radius:10px;border:0.5px solid #fdba74;padding:10px 12px;font-size:12px;line-height:1.6;background:white;resize:vertical;"></textarea>

                            @if (count($bulkPesertaParsed) > 0)
                                <div style="margin-top:10px;padding:10px;border-radius:10px;border:0.5px solid #e5e7eb;background:white;max-height:180px;overflow-y:auto;">
                                    <div style="font-size:10px;color:#888;margin-bottom:6px;">Preview: {{ number_format(count($bulkPesertaParsed)) }} peserta terdeteksi</div>
                                    @foreach ($bulkPesertaParsed as $i => $peserta)
                                        <div style="padding:4px 6px;border-radius:6px;font-size:11px;background:{{ $i % 2 === 1 ? '#fafafa' : 'transparent' }};">
                                            <strong>{{ $peserta['nama'] }}</strong>
                                            @if ($peserta['rw'])
                                                <span style="color:#888;">· RW {{ $peserta['rw'] }}</span>
                                            @endif
                                            @if ($peserta['hp'])
                                                <span style="color:#888;">· {{ $peserta['hp'] }}</span>
                                            @endif
                                            @if (!empty($peserta['aspirasi']))
                                                <div style="color:#444;margin-top:2px;">{{ \Illuminate\Support\Str::limit($peserta['aspirasi'], 80) }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                <div style="font-size:11px;color:#666;">{{ number_format(count($bulkPesertaParsed)) }} peserta · auto-sync ke Sapa Warga jika ada RW + HP</div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <button wire:click="$set('showPesertaForm', false)" type="button" style="padding:7px 12px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">
                                        Batal
                                    </button>
                                    <button wire:click="simpanBulkPeserta" type="button" style="padding:7px 12px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;{{ count($bulkPesertaParsed) === 0 ? 'opacity:.5;pointer-events:none;' : '' }}">
                                        Simpan {{ number_format(count($bulkPesertaParsed)) }} peserta
                                    </button>
                                </div>
                            </div>
                        @else
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:12px;" class="event-peserta-grid">
                                <input wire:model.live="spNama" type="text" placeholder="Nama *" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                <input wire:model.live="spHp" type="text" placeholder="No HP/WA" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:8px;" class="event-peserta-grid">
                                <select wire:model.live="spDesaId" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                    <option value="">Desa</option>
                                    @foreach ($this->singleDesaOptions as $desaOption)
                                        <option value="{{ $desaOption->id }}">{{ $desaOption->desa }}</option>
                                    @endforeach
                                </select>
                                <input wire:model.live="spRw" type="text" placeholder="RW" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                                <input wire:model.live="spRt" type="text" placeholder="RT (opsional)" style="height:36px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;font-size:12px;background:white;">
                            </div>
                            <textarea wire:model.live="spAspirasi" rows="3" placeholder="Aspirasi peserta (opsional)" style="width:100%;margin-top:8px;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px;font-size:12px;background:white;resize:vertical;"></textarea>
                            <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
                                <button wire:click="simpanSinglePeserta" type="button" style="padding:7px 12px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                    Simpan
                                </button>
                                <button wire:click="$set('showPesertaForm', false)" type="button" style="padding:7px 12px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:12px;cursor:pointer;">
                                    Batal
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <div style="margin-top:12px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:11px;color:#888;">
                    <span>Daftar peserta</span>
                    <span>
                        <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span> synced</span>
                        <span style="display:inline-flex;align-items:center;gap:5px;margin-left:12px;"><span style="width:8px;height:8px;border-radius:50%;background:#d4d4d8;display:inline-block;"></span> belum</span>
                    </span>
                </div>

                <div style="display:grid;gap:10px;margin-top:10px;">
                    @forelse ($this->pesertaList->groupBy(fn ($row) => $row->nomor_rw ?: '?') as $rw => $members)
                        <div>
                            <div style="font-size:11px;color:#666;font-weight:600;margin-bottom:6px;">
                                {{ $rw === '?' ? 'RW belum diketahui' : 'RW '.$rw }} ({{ number_format($members->count()) }} orang)
                            </div>
                            <div style="display:grid;gap:4px;">
                                @foreach ($members as $peserta)
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 10px;border-radius:10px;background:{{ $loop->even ? '#fafafa' : 'white' }};border:0.5px solid #f1f5f9;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $peserta->synced_sapa_warga ? '#22c55e' : '#d4d4d8' }};display:inline-block;flex:0 0 auto;"></span>
                                        <span style="font-size:12px;font-weight:600;color:#1a1a1a;flex:1 1 180px;">{{ $peserta->nama }}</span>
                                        <span style="font-size:11px;color:#888;">{{ $peserta->no_hp ?? '-' }}</span>
                                        @if ($peserta->nomor_rt)
                                            <span style="padding:3px 6px;border-radius:999px;background:#f4f4f5;color:#666;font-size:10px;">RT {{ $peserta->nomor_rt }}</span>
                                        @endif
                                        @if ($peserta->aspirasi)
                                            <span style="padding:3px 6px;border-radius:999px;background:#ecfeff;color:#0369a1;font-size:10px;">Ada aspirasi</span>
                                        @endif
                                        <span style="font-size:10px;color:#999;text-transform:uppercase;">{{ $peserta->metode }}</span>
                                        <button wire:click="hapusPeserta('{{ $peserta->id }}')" wire:confirm="Hapus peserta?" type="button" style="padding:4px 6px;border:none;background:transparent;color:#a3a3a3;cursor:pointer;">
                                            <i class="ti ti-x" style="font-size:12px;" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div style="padding:18px;border-radius:10px;border:0.5px dashed #d4d4d8;background:#fafafa;text-align:center;font-size:12px;color:#888;">
                            Belum ada peserta. Klik "+ Input Peserta" untuk mulai.
                        </div>
                    @endforelse
                </div>
            </div>

            <div style="display:grid;grid-template-columns:minmax(0,1fr);gap:14px;margin-top:14px;">
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div>
                            <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Laporan Kegiatan</div>
                            <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">Ringkasan pelaksanaan dan evaluasi</div>
                        </div>
                        <div style="font-size:11px;color:#888;">Tersedia setelah event berstatus selesai</div>
                    </div>

                    @if ($event->status === 'selesai' || $event->report)
                        <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(260px,0.9fr);gap:12px;margin-top:12px;" class="event-report-grid">
                            <div style="display:grid;gap:10px;">
                                <textarea wire:model="reportRingkasan" rows="4" placeholder="Ringkasan kegiatan" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                                <textarea wire:model="reportEvaluasi" rows="3" placeholder="Evaluasi" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                                <textarea wire:model="reportTindakLanjut" rows="3" placeholder="Tindak lanjut" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                            </div>
                            <div style="display:grid;gap:10px;">
                                <input wire:model="reportPesertaHadir" type="number" min="0" placeholder="Peserta hadir" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                                <input wire:model="reportRealisasiAnggaran" type="number" min="0" step="0.01" placeholder="Realisasi anggaran" style="height:38px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:12px;">
                                <input wire:model="reportFoto" type="file" multiple accept="image/*" style="font-size:12px;width:100%;">
                                @if ($reportExistingFoto !== [])
                                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                        @foreach ($reportExistingFoto as $foto)
                                            <div style="width:74px;height:74px;border-radius:10px;overflow:hidden;background:#f4f4f5;">
                                                <img src="{{ asset('storage/' . $foto) }}" alt="Foto laporan" style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <button wire:click="saveReport" type="button" style="height:38px;padding:0 14px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;width:max-content;">Simpan Laporan</button>
                            </div>
                        </div>
                    @else
                        <div style="margin-top:12px;padding:18px;border-radius:10px;border:0.5px dashed #d4d4d8;background:#fafafa;text-align:center;font-size:12px;color:#888;">
                            Tandai event sebagai selesai untuk mulai mengisi laporan.
                        </div>
                    @endif
                </div>

                @if ($event->is_public)
                    <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                            <div>
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Pendaftaran Peserta</div>
                                <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">Statistik dan daftar peserta publik</div>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <span style="padding:4px 8px;border-radius:999px;background:#fafafa;font-size:11px;color:#666;">Daftar {{ $registrationStats['total'] }}</span>
                                <span style="padding:4px 8px;border-radius:999px;background:#eff6ff;font-size:11px;color:#2563eb;">Confirmed {{ $registrationStats['confirmed'] }}</span>
                                <span style="padding:4px 8px;border-radius:999px;background:#ecfdf3;font-size:11px;color:#166534;">Hadir {{ $registrationStats['attended'] }}</span>
                            </div>
                        </div>
                        <div style="margin-top:12px;overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                <thead style="background:#fafafa;">
                                    <tr style="border-bottom:0.5px solid #e5e7eb;">
                                        <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Nama</th>
                                        <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Email</th>
                                        <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Status</th>
                                        <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($registrations as $registration)
                                        <tr style="border-bottom:0.5px solid #f1f5f9;">
                                            <td style="padding:10px 12px;color:#1a1a1a;">{{ $registration->user?->name ?? '-' }}</td>
                                            <td style="padding:10px 12px;color:#666;">{{ $registration->user?->email ?? '-' }}</td>
                                            <td style="padding:10px 12px;text-align:center;color:#666;">{{ ucfirst($registration->status) }}</td>
                                            <td style="padding:10px 12px;color:#666;">{{ $registration->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="padding:18px;text-align:center;font-size:12px;color:#888;background:#fafafa;">Belum ada peserta terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 1100px) {
            .event-detail-summary-grid,
            .event-detail-top-grid,
            .event-report-grid,
            .event-detail-budget-grid,
            .event-peserta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .event-detail-summary-grid,
            .event-detail-top-grid,
            .event-report-grid,
            .event-detail-budget-grid,
            .event-peserta-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
