@php
    $summary = $this->summary;
    $desaRows = $this->desaRows;
    $selectedTarget = $this->selectedTargetWilayah;
    $rwRows = $this->rwRows;
    $rwSummary = $this->rwSummary;
    $rtBadges = $this->rtBadges;
    $rwPenggalang = $this->rwPenggalang;
    $kontakRows = $this->kontakRows;
    $bulkPreview = $this->bulkPreview;
    $isKader = auth()->user()?->isKader() ?? false;
@endphp

<div style="min-height:100vh;background:#fafafa;">
    <div style="width:100%;margin:0;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:14px 14px 0 0;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti ti-address-book" style="font-size:16px;color:white;" aria-hidden="true"></i>
                    </div>
                    <div style="font-weight:500;font-size:14px;">Sapa Warga</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                    <div style="font-size:12px;color:#d4d4d8;font-weight:500;">{{ $isKader ? 'Scope :' : 'Filter :' }}</div>
                    @if ($isKader)
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:600;">{{ auth()->user()->dapil ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#27272a;color:#f4f4f5;">{{ auth()->user()->kecamatan ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#27272a;color:#f4f4f5;">{{ auth()->user()->desa ?: '-' }}</span>
                            <span style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:999px;font-size:12px;background:#fef3c7;color:#92400e;font-weight:700;">RW {{ auth()->user()->nomor_rw ?: '-' }}</span>
                        </div>
                    @else
                        <select wire:model.live="selectedDapil" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
                            <option value="">Semua dapil</option>
                            @foreach ($this->dapilOptions as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedKecamatan" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                            <option value="">Semua kecamatan</option>
                            @foreach ($this->kecamatanOptions as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="selectedDesa" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                            <option value="">Semua desa</option>
                            @foreach ($this->desaOptions as $desaOption)
                                <option value="{{ $desaOption }}">{{ $desaOption }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="$set('selectedDapil', ''); $set('selectedKecamatan', ''); $set('selectedDesa', '')" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
                    @endif
                </div>
            </div>
            <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">SW</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
            <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Sapa Warga</h1>
                    <div style="font-size:12px;color:#666;">Database kontak warga per RW untuk distribusi dan tindak lanjut lapangan</div>
                </div>
                <div style="font-size:11px;color:#888;">Target standar {{ number_format(\App\Models\KontakWarga::TARGET_PER_RW) }} kontak per RW</div>
            </div>

            @if (session()->has('success'))
                <div style="padding:14px 20px 0;">
                    <div style="border:0.5px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:10px;padding:10px 12px;font-size:12px;">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="sapa-summary-grid">
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Total Kontak</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['total_kontak']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Kontak aktif tersimpan</div>
                </div>
                <div style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">
                    <div style="font-size:11px;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;opacity:.9;">Target Kontak</div>
                    <div style="font-size:26px;font-weight:500;margin-top:6px;">{{ number_format($summary['target_kontak']) }}</div>
                    <div style="font-size:11px;margin-top:4px;opacity:.85;">Akumulasi target semua RW</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Desa Tersedia</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['desa_count']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Sesuai filter dapil/kecamatan/desa</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">RW Terisi</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['rw_terisi']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">{{ number_format($summary['progress_pct']) }}% progress target</div>
                </div>
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#666;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Penggalang Aktif</div>
                    <div style="font-size:26px;font-weight:500;color:#1a1a1a;margin-top:6px;">{{ number_format($summary['penggalang_aktif']) }}</div>
                    <div style="font-size:11px;color:#888;margin-top:4px;">Terkoneksi ke wilayah terfilter</div>
                </div>
            </div>

            <div style="padding:0 20px 20px;">
                <div style="display:grid;grid-template-columns:minmax(0,7fr) minmax(320px,3fr);gap:12px;align-items:start;" class="sapa-main-grid">
                    <div style="display:grid;gap:8px;" class="sapa-left-stack">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:12px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                                <div>
                                    <div style="font-size:10px;color:#fe5000;font-weight:600;letter-spacing:0.8px;text-transform:uppercase;">Daftar Desa</div>
                                    <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">Urut berdasarkan jumlah kontak aktif</div>
                                </div>
                                <div style="font-size:10px;color:#888;">Klik untuk pilih</div>
                            </div>

                            <div style="overflow-x:auto;">
                                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                    <thead style="background:#fafafa;">
                                        <tr style="border-bottom:0.5px solid #e5e5e5;">
                                            <th style="text-align:left;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Desa</th>
                                            <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Kontak</th>
                                            <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">RW Terisi</th>
                                            <th style="text-align:center;padding:8px 10px;font-size:10px;color:#666;text-transform:uppercase;letter-spacing:0.8px;font-weight:500;">Penggalang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($desaRows as $desa)
                                            <tr
                                                wire:key="desa-{{ $desa->id }}"
                                                @if (! $isKader) wire:click="selectDesa('{{ $desa->id }}')" @endif
                                                style="border-bottom:0.5px solid #f1f5f9;cursor:{{ $isKader ? 'default' : 'pointer' }};background:{{ $selectedTarget?->id === $desa->id ? '#fff7f1' : 'white' }};"
                                            >
                                                <td style="padding:8px 10px;">
                                                    <div style="font-weight:600;color:#1a1a1a;">{{ $desa->desa }}</div>
                                                    <div style="font-size:10px;color:#888;margin-top:2px;">{{ $desa->kecamatan }} · {{ $desa->dapil }}</div>
                                                </td>
                                                <td style="padding:8px 10px;text-align:center;color:#ea580c;font-weight:700;">{{ number_format($desa->kontak_count) }}</td>
                                                <td style="padding:8px 10px;text-align:center;color:#444;">{{ number_format((int) $desa->rw_terisi_count) }}/{{ number_format((int) $desa->jumlah_rw) }}</td>
                                                <td style="padding:8px 10px;text-align:center;color:#444;">{{ number_format((int) $desa->penggalang_count) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" style="padding:24px;text-align:center;font-size:12px;color:#888;">Belum ada data desa untuk filter yang dipilih.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div style="border-top:0.5px solid #e5e5e5;padding-top:10px;margin-top:10px;">
                                {{ $desaRows->links() }}
                            </div>
                        </div>

                        @if ($selectedTarget)
                            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:12px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;">
                                    <div style="font-size:10px;color:#fe5000;font-weight:600;letter-spacing:0.8px;text-transform:uppercase;">Pilih RW — {{ $selectedTarget->desa }}</div>
                                    <div style="font-size:10px;color:#888;">{{ $rwRows->count() }} RW</div>
                                </div>

                                <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:4px;" class="sapa-rw-grid">
                                    @foreach ($rwRows as $row)
                                        @php
                                            $count = (int) $row['kontak_count'];
                                            $bgColor = $count >= 200 ? '#dcfce7' : ($count >= 50 ? '#fff7f1' : ($count > 0 ? '#fef3c7' : 'transparent'));
                                            $borderColor = $selectedRw === $row['nomor_rw'] ? '#fe5000' : ($count >= 200 ? '#bbf7d0' : ($count >= 50 ? '#fed7aa' : ($count > 0 ? '#fde68a' : '#e5e7eb')));
                                        @endphp
                                        <button
                                            type="button"
                                            wire:key="rw-{{ $row['nomor_rw'] }}"
                                            wire:click="selectRw('{{ $row['nomor_rw'] }}')"
                                            style="padding:6px 4px;border-radius:8px;text-align:center;transition:all .15s ease;cursor:pointer;background:{{ $bgColor }};border:{{ $selectedRw === $row['nomor_rw'] ? '2px' : '0.5px' }} solid {{ $borderColor }};min-height:42px;"
                                        >
                                            <div style="font-size:11px;font-weight:700;color:#1a1a1a;line-height:1;">{{ ltrim($row['nomor_rw'], '0') ?: '0' }}</div>
                                            <div style="font-size:9px;margin-top:4px;color:{{ $count > 0 ? '#ea580c' : '#d4d4d8' }};font-weight:700;line-height:1;">{{ $count }}</div>
                                        </button>
                                    @endforeach
                                </div>

                                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:8px;font-size:9px;color:#888;">
                                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#dcfce7;border:0.5px solid #bbf7d0;"></span>&ge;200</span>
                                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#fff7f1;border:0.5px solid #fed7aa;"></span>50-199</span>
                                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;background:#fef3c7;border:0.5px solid #fde68a;"></span>1-49</span>
                                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:8px;height:8px;border-radius:2px;border:0.5px solid #e5e7eb;background:white;"></span>0</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        @if ($selectedTarget && $selectedRw)
                            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;overflow:hidden;">
                                <div style="padding:12px;border-bottom:0.5px solid #e5e5e5;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                                        <div>
                                            <div style="font-size:10px;color:#fe5000;font-weight:600;letter-spacing:0.8px;text-transform:uppercase;">RW {{ $selectedRw }} · {{ $selectedTarget->desa }}</div>
                                            <div style="display:flex;align-items:baseline;gap:6px;margin-top:4px;">
                                                <span style="font-size:22px;font-weight:700;color:#1a1a1a;line-height:1;">{{ number_format($rwSummary['total_kontak']) }}</span>
                                                <span style="font-size:12px;color:#888;">/ {{ number_format($rwSummary['target_kontak']) }} target</span>
                                            </div>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <span style="font-size:15px;font-weight:700;color:{{ $rwSummary['progress_pct'] >= 80 ? '#16a34a' : ($rwSummary['progress_pct'] >= 20 ? '#d97706' : '#9ca3af') }};">
                                                {{ number_format($rwSummary['progress_pct']) }}%
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="toggleBulkForm"
                                                style="padding:6px 10px;border:none;border-radius:6px;background:#fe5000;color:white;font-size:10px;font-weight:700;cursor:pointer;"
                                            >
                                                {{ $showBulkForm ? 'Tutup' : '+ Input' }}
                                            </button>
                                        </div>
                                    </div>

                                    <div style="height:6px;background:#f4f4f5;border-radius:999px;overflow:hidden;margin-bottom:10px;">
                                        <div style="height:100%;width:{{ min($rwSummary['progress_pct'], 100) }}%;background:{{ $rwSummary['progress_pct'] >= 80 ? '#16a34a' : ($rwSummary['progress_pct'] >= 20 ? '#d97706' : '#fe5000') }};border-radius:999px;"></div>
                                    </div>

                                    <div style="display:flex;gap:16px;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;">
                                        <div style="flex:1;min-width:220px;">
                                            <div style="font-size:10px;color:#888;margin-bottom:6px;">Sebaran per RT:</div>
                                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                                @forelse ($rtBadges as $badge)
                                                    <span style="font-size:9px;padding:3px 6px;border-radius:999px;font-weight:700;background:{{ $badge['bg'] }};color:{{ $badge['text'] }};">
                                                        RT {{ $badge['rt'] }}: {{ number_format($badge['total']) }}
                                                    </span>
                                                @empty
                                                    <span style="font-size:9px;color:#c4c4c4;">Belum ada data</span>
                                                @endforelse
                                            </div>
                                        </div>

                                        @if ($rwPenggalang->isNotEmpty())
                                            <div style="flex-shrink:0;">
                                                <div style="font-size:10px;color:#888;margin-bottom:6px;">Penggalang:</div>
                                                <div style="font-size:9px;padding:3px 6px;border-radius:999px;font-weight:700;background:#fff7ed;border:0.5px solid #fed7aa;color:#993c1d;">
                                                    {{ $rwPenggalang->count() }} aktif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($showBulkForm)
                                    <div style="padding:12px;border-bottom:0.5px solid #e5e5e5;background:#fff7f1;">
                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;">
                                            <div style="font-size:12px;font-weight:700;color:#c2410c;">Bulk input — RW {{ $selectedRw }}</div>
                                            <button type="button" wire:click="toggleBulkForm" style="font-size:11px;color:#888;background:none;border:none;cursor:pointer;">x Tutup</button>
                                        </div>

                                        <textarea
                                            wire:model.live.debounce.500ms="bulkText"
                                            rows="3"
                                            placeholder="Ahmad Fauzi, 08123456789&#10;Siti Nurhaliza, 08567891234"
                                            style="width:100%;padding:10px;border:0.5px solid #fdba74;border-radius:8px;background:white;font-size:12px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;resize:vertical;"
                                        ></textarea>
                                        @error('bulkText')
                                            <div style="font-size:11px;color:#b91c1c;margin-top:6px;">{{ $message }}</div>
                                        @enderror

                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                            <span style="font-size:10px;color:#666;">{{ number_format($bulkPreview['ready_to_save']) }} kontak valid · duplikat otomatis diskip</span>
                                            <button
                                                type="button"
                                                wire:click="saveBulk"
                                                style="padding:6px 12px;border:none;border-radius:6px;background:#fe5000;color:white;font-size:10px;font-weight:700;cursor:pointer;"
                                            >
                                                Simpan {{ number_format($bulkPreview['ready_to_save']) }}
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <div style="padding:8px 12px;background:#fafafa;border-bottom:0.5px solid #e5e5e5;">
                                    <div style="display:flex;align-items:center;gap:8px;padding:7px 9px;background:white;border:0.5px solid #e5e5e5;border-radius:8px;">
                                        <i class="ti ti-search" style="font-size:12px;color:#a1a1aa;" aria-hidden="true"></i>
                                        <input wire:model.live.debounce.300ms="detailSearch" type="text" placeholder="Cari..." style="border:none;outline:none;font-size:12px;width:100%;background:transparent;color:#18181b;">
                                        <span style="font-size:9px;color:#a1a1aa;white-space:nowrap;">{{ $kontakRows->total() }}</span>
                                    </div>
                                </div>

                                <div style="max-height:340px;overflow-y:auto;">
                                    @if ($kontakRows->count() > 0)
                                        @foreach ($kontakRows as $kontak)
                                            <div style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-bottom:0.5px solid #f1f5f9;font-size:12px;background:{{ $loop->even ? '#fafafa' : 'white' }};">
                                                <i class="ti ti-brand-whatsapp" style="font-size:13px;color:#25d366;" aria-hidden="true"></i>
                                                <span style="font-weight:600;color:#1a1a1a;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $kontak->nama }}</span>
                                                <span style="color:#888;white-space:nowrap;">{{ $kontak->no_wa ?: '-' }}</span>
                                                @if ($kontak->rt)
                                                    <span style="font-size:9px;padding:2px 5px;border-radius:999px;background:#f4f4f5;color:#71717a;white-space:nowrap;">RT {{ $kontak->rt }}</span>
                                                @endif
                                                <button
                                                    type="button"
                                                    wire:click="deactivateContact('{{ $kontak->id }}')"
                                                    style="border:none;background:none;color:#d4d4d8;cursor:pointer;padding:0;"
                                                >
                                                    <i class="ti ti-trash" style="font-size:12px;" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="text-align:center;padding:32px 18px;font-size:12px;color:#a1a1aa;">
                                            Belum ada kontak. Klik "+ Input" untuk mulai.
                                        </div>
                                    @endif
                                </div>

                                @if ($kontakRows->hasPages())
                                    <div style="padding:8px 12px;background:#fafafa;border-top:0.5px solid #e5e5e5;">
                                        {{ $kontakRows->links('livewire::simple-tailwind') }}
                                    </div>
                                @endif
                            </div>
                        @elseif ($selectedTarget)
                            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:28px;text-align:center;font-size:14px;color:#a1a1aa;">
                                &larr; Klik salah satu RW di grid
                            </div>
                        @else
                            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:28px;text-align:center;font-size:14px;color:#a1a1aa;">
                                <i class="ti ti-address-book" style="font-size:24px;display:block;margin-bottom:6px;" aria-hidden="true"></i>
                                Klik desa di tabel kiri
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sapa-left-stack {
            position: sticky;
            top: 12px;
        }

        @media (max-width: 1280px) {
            .sapa-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .sapa-main-grid,
            .sapa-detail-layout,
            .sapa-bulk-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .sapa-rw-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 920px) {
            .sapa-rw-summary-grid,
            .sapa-left-stack {
                grid-template-columns: minmax(0, 1fr) !important;
                position: static !important;
            }
        }

        @media (max-width: 760px) {
            .sapa-summary-grid,
            .sapa-rw-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
