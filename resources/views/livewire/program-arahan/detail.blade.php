@php
    $statusCfg = \App\Models\ProgramArahan::STATUS_CONFIG[$programArahan->status] ?? ['label' => $programArahan->status, 'bg' => '#eee', 'color' => '#333'];
    $currentApprovalLevel = $programArahan->level_approval;
    $isBidangDpd = $programArahan->org_level === 'dpd' && !empty($programArahan->bidang_dpd_id);
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="{{ route('buku-induk-rw.index') }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:rgba(255,255,255,.08);color:#f5f5f5;font-size:12px;text-decoration:none;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <div>
                    <div style="font-size:11px;color:#a3a3a3;">Program Arahan &gt; {{ $programArahan->judul }}</div>
                    <div style="font-size:18px;font-weight:600;margin-top:4px;">{{ $programArahan->judul }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                @if ($this->canEdit)
                    <a href="{{ route('program-arahan.edit', $programArahan) }}" wire:navigate style="display:inline-flex;align-items:center;height:38px;padding:0 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;text-decoration:none;font-size:12px;">
                        Edit Program
                    </a>
                @endif
            </div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;padding:16px 20px 20px;border-radius:0 0 14px 14px;overflow:hidden;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                <span style="padding:5px 10px;border-radius:999px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:11px;font-weight:600;">{{ $statusCfg['label'] }}</span>
                <span style="padding:5px 10px;border-radius:999px;background:#fff7ed;color:#c2410c;font-size:11px;font-weight:600;">{{ \App\Models\ProgramArahan::JENIS_PROGRAM[$programArahan->jenis_program] ?? $programArahan->jenis_program }}</span>
                <span style="font-size:11px;color:#666;">Dibuat: {{ $programArahan->created_at?->format('d M Y, H:i') ?? '-' }}</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:14px;">
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Target Wilayah</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $programArahan->targetWilayah?->desa ?? 'Semua Desa' }} {{ $programArahan->nomor_rw ? 'RW ' . ltrim($programArahan->nomor_rw, '0') : '' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Kec. {{ $programArahan->targetWilayah?->kecamatan ?? '-' }}</div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">PIC & Penyelenggara</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $programArahan->pic_nama ?: '-' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ $programArahan->penyelenggara ?: '-' }}</div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Target / Realisasi</div>
                    <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">
                        {{ $programArahan->realisasi }} / {{ $programArahan->target_angka }} {{ $programArahan->satuan }}
                    </div>
                    <div style="width:100%;height:6px;background:#f4f4f5;border-radius:3px;margin-top:8px;overflow:hidden;">
                        <div style="height:100%;background:#fe5000;width:{{ $programArahan->progress_pct }}%;"></div>
                    </div>
                </div>
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;padding:14px;background:white;">
                    <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Jadwal</div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $programArahan->tanggal_mulai ? $programArahan->tanggal_mulai->format('d M Y') : '-' }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">s/d {{ $programArahan->tanggal_selesai ? $programArahan->tanggal_selesai->format('d M Y') : '-' }}</div>
                </div>
            </div>

            @if ($programArahan->deskripsi)
                <div style="margin-bottom:14px;padding:14px;border-radius:12px;border:0.5px solid #e5e7eb;background:white;">
                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Deskripsi Program</div>
                    <div style="font-size:13px;color:#444;line-height:1.7;margin-top:8px;">{{ $programArahan->deskripsi }}</div>
                </div>
            @endif

            <div style="display:grid;gap:14px;{{ $programArahan->status !== 'belum_mulai' ? 'grid-template-columns:minmax(0,1.05fr) minmax(360px,0.95fr);' : 'grid-template-columns:minmax(0,1fr);' }}">
                
                @if($programArahan->status !== 'belum_mulai')
                <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Approval Tracker</div>
                    <div style="margin-top:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        @foreach (['dpra' => 'DPRa', 'dpc' => 'DPC', 'dpd' => 'DPD'] as $level => $label)
                            @php
                                $approval = $programArahan->approvals->firstWhere('level', $level);
                                $isCurrent = $programArahan->level_approval === $level && $programArahan->status === 'berjalan';
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

                    @if ($programArahan->status === 'berjalan' && $this->canApproveLevel($currentApprovalLevel))
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
                @endif

                <div style="display:flex;flex-direction:column;gap:14px;">
                    {{-- Personnel Section --}}
                    <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Personel ({{ $programArahan->personel->count() }})</div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                            <select wire:model.live="personelType" style="flex:1;min-width:120px;height:36px;border-radius:8px;border:1px solid #d4d4d8;padding:0 10px;font-size:12px;">
                                <option value="">Pilih Jenis...</option>
                                <option value="korwe">Korwe</option>
                                <option value="korte">Korte</option>
                                <option value="penggalang">Penggalang</option>
                            </select>
                            <select wire:model="personelId" style="flex:2;min-width:180px;height:36px;border-radius:8px;border:1px solid #d4d4d8;padding:0 10px;font-size:12px;" {{ !$personelType ? 'disabled' : '' }}>
                                <option value="">Pilih Personel...</option>
                                @foreach($this->availablePersonnel as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nik }})</option>
                                @endforeach
                            </select>
                            <button wire:click="linkPersonel" type="button" style="height:36px;padding:0 14px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;" {{ !$personelId ? 'disabled' : '' }}>
                                Tambah
                            </button>
                        </div>
                        
                        @if($programArahan->personel->count() > 0)
                        <div style="margin-top:14px;overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                <tbody>
                                    @foreach($programArahan->personel as $p)
                                    <tr style="border-bottom:1px solid #f4f4f5;">
                                        <td style="padding:8px 4px;color:#444;text-transform:capitalize;">{{ $p->personel_type }}</td>
                                        <td style="padding:8px 4px;font-weight:500;color:#1a1a1a;">
                                            @if($p->personel_type === 'korwe' && $p->korwe)
                                                {{ $p->korwe->nama }}
                                            @elseif($p->personel_type === 'korte' && $p->korte)
                                                {{ $p->korte->nama }}
                                            @elseif($p->personel_type === 'penggalang' && $p->penggalangSuara)
                                                {{ $p->penggalangSuara->nama }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="padding:8px 4px;text-align:right;">
                                            <button wire:click="unlinkPersonel('{{ $p->id }}')" type="button" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px;"><i class="ti ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div style="margin-top:14px;padding:12px;background:#fafafa;border:1px dashed #d4d4d8;border-radius:8px;text-align:center;font-size:12px;color:#a3a3a3;">
                                Belum ada personel ditautkan.
                            </div>
                        @endif
                    </div>

                    {{-- Budget Section --}}
                    <div style="border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:14px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RAB / Anggaran</div>
                        
                        @if ($this->budgetEditId || $budgetItem !== '' || $budgetKategori !== '')
                        <div style="background:#fafafa;padding:12px;border-radius:10px;margin-top:12px;border:1px solid #e5e7eb;">
                            <div style="font-size:12px;font-weight:600;margin-bottom:8px;">{{ $this->budgetEditId ? 'Edit Item' : 'Tambah Item' }}</div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <input wire:model="budgetItem" type="text" placeholder="Item" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                <input wire:model="budgetKategori" type="text" placeholder="Kategori" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                <input wire:model="budgetQty" type="number" placeholder="Qty" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                <input wire:model="budgetSatuan" type="text" placeholder="Satuan" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                <input wire:model="budgetHargaSatuan" type="number" placeholder="Harga Satuan" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                <input wire:model="budgetKeterangan" type="text" placeholder="Keterangan" style="width:100%;height:32px;border-radius:6px;border:1px solid #d4d4d8;padding:0 8px;font-size:12px;">
                            </div>
                            <div style="display:flex;gap:8px;margin-top:10px;justify-content:flex-end;">
                                <button wire:click="cancelBudgetEdit" type="button" style="height:32px;padding:0 12px;border-radius:6px;border:1px solid #d4d4d8;background:white;color:#444;font-size:12px;cursor:pointer;">Batal</button>
                                <button wire:click="saveBudgetItem" type="button" style="height:32px;padding:0 12px;border-radius:6px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;">Simpan</button>
                            </div>
                        </div>
                        @else
                        <div style="text-align:right;margin-top:8px;">
                            <button wire:click="$set('budgetItem', 'Baru')" type="button" style="height:32px;padding:0 12px;border-radius:6px;border:none;background:#f5f5f5;color:#444;font-size:12px;font-weight:500;cursor:pointer;">+ Tambah Item</button>
                        </div>
                        @endif

                        @if($programArahan->budgetItems->count() > 0)
                        <div style="margin-top:14px;overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                <thead>
                                    <tr style="border-bottom:1px solid #e5e5e5;text-align:left;color:#666;">
                                        <th style="padding:8px 4px;font-weight:500;">Item</th>
                                        <th style="padding:8px 4px;font-weight:500;">Qty</th>
                                        <th style="padding:8px 4px;font-weight:500;text-align:right;">Subtotal</th>
                                        <th style="padding:8px 4px;width:60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($programArahan->budgetItems as $item)
                                        @php $grandTotal += $item->subtotal; @endphp
                                        <tr style="border-bottom:1px solid #f4f4f5;">
                                            <td style="padding:8px 4px;color:#1a1a1a;">
                                                <div>{{ $item->item }}</div>
                                                <div style="font-size:10px;color:#888;">{{ $item->kategori ?: 'Tanpa kategori' }}</div>
                                            </td>
                                            <td style="padding:8px 4px;color:#444;">{{ $item->qty }} {{ $item->satuan }} <br><span style="font-size:10px;color:#888;">@ Rp{{ number_format($item->harga_satuan,0,',','.') }}</span></td>
                                            <td style="padding:8px 4px;text-align:right;font-weight:500;">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                                            <td style="padding:8px 4px;text-align:right;">
                                                <button wire:click="editBudgetItem('{{ $item->id }}')" type="button" style="background:none;border:none;color:#444;cursor:pointer;padding:2px;"><i class="ti ti-pencil"></i></button>
                                                <button wire:click="removeBudgetItem('{{ $item->id }}')" type="button" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:2px;"><i class="ti ti-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="padding:8px 4px;text-align:right;font-weight:600;">Total</td>
                                        <td style="padding:8px 4px;text-align:right;font-weight:600;color:#fe5000;">Rp {{ number_format($grandTotal,0,',','.') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @else
                            <div style="margin-top:14px;padding:12px;background:#fafafa;border:1px dashed #d4d4d8;border-radius:8px;text-align:center;font-size:12px;color:#a3a3a3;">
                                Belum ada rencana anggaran (RAB).
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
