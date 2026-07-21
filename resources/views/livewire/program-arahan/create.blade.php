<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">
        
        {{-- DARK HEADER --}}
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-radius:14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="{{ route('buku-induk-rw.index') }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:rgba(255,255,255,.08);color:#f5f5f5;font-size:12px;text-decoration:none;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <div>
                    <div style="font-size:15px;font-weight:500;">Buat Program Arahan</div>
                    <div style="font-size:11px;color:#a3a3a3;margin-top:2px;">Rencanakan program pemenangan berbasis wilayah.</div>
                </div>
            </div>
            <div style="font-size:11px;color:#a3a3a3;">{{ now()->format('d M Y H:i') }}</div>
        </div>

        {{-- WHITE BODY --}}
        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;padding:0;">
            
            {{-- TAB NAVIGATION & ACTION BUTTONS --}}
            <div style="padding:20px 20px 0;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;margin-bottom:10px;">Langkah Pendaftaran</div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <a href="{{ route('buku-induk-rw.index') }}" wire:navigate style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:13px;display:inline-flex;align-items:center;text-decoration:none;">
                        Batal
                    </a>
                    @if($infoStep === 4)
                    <button wire:click="simpanDraft" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;font-weight:500;cursor:pointer;">
                        <span wire:loading.remove wire:target="simpanDraft">Simpan Draft</span>
                        <span wire:loading wire:target="simpanDraft">Menyimpan...</span>
                    </button>
                    <button wire:click="simpanDanAjukan" type="button" style="height:40px;padding:0 20px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                        <span wire:loading.remove wire:target="simpanDanAjukan">Ajukan Program</span>
                        <span wire:loading wire:target="simpanDanAjukan">Memproses...</span>
                    </button>
                    @endif
                </div>
            </div>

            <div style="padding:20px;">

        {{-- FLASH / ERROR --}}
        @if(session('message'))
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">{{ session('message') }}</div>
        @endif
        @if($errors->any())
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#fef2f2;border:0.5px solid #fecaca;color:#dc2626;font-size:12px;">{{ $errors->first() }}</div>
        @endif

        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
            {{-- Progress Indicator --}}
            <div style="display:flex;align-items:center;margin-bottom:24px;border-bottom:0.5px solid #e5e5e5;padding-bottom:16px;overflow-x:auto;">
                <div style="display:flex;align-items:center;gap:8px;white-space:nowrap;{{ $infoStep >= 1 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;' }}">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;{{ $infoStep >= 1 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;' }}">1</div>
                    <span style="font-size:13px;">Wilayah & Jenis</span>
                </div>
                <div style="flex:1;min-width:30px;height:1px;background:#e5e5e5;margin:0 16px;"></div>
                <div style="display:flex;align-items:center;gap:8px;white-space:nowrap;{{ $infoStep >= 2 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;' }}">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;{{ $infoStep >= 2 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;' }}">2</div>
                    <span style="font-size:13px;">Info Program</span>
                </div>
                <div style="flex:1;min-width:30px;height:1px;background:#e5e5e5;margin:0 16px;"></div>
                <div style="display:flex;align-items:center;gap:8px;white-space:nowrap;{{ $infoStep >= 3 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;' }}">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;{{ $infoStep >= 3 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;' }}">3</div>
                    <span style="font-size:13px;">Anggaran</span>
                </div>
                <div style="flex:1;min-width:30px;height:1px;background:#e5e5e5;margin:0 16px;"></div>
                <div style="display:flex;align-items:center;gap:8px;white-space:nowrap;{{ $infoStep >= 4 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;' }}">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;{{ $infoStep >= 4 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;' }}">4</div>
                    <span style="font-size:13px;">Review</span>
                </div>
            </div>

            {{-- Step 1 --}}
            @if($infoStep === 1)
            <div>
                <div style="display:grid;gap:14px;grid-template-columns:1fr 1fr;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Dapil</label>
                        <select wire:model.live="lokasiDapil" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            <option value="">Semua Dapil</option>
                            @foreach($this->dapilOptions as $dapil)
                                <option value="{{ $dapil }}">{{ $dapil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Kecamatan</label>
                        <select wire:model.live="lokasiKecamatan" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            <option value="">Semua Kecamatan</option>
                            @foreach($this->kecamatanOptions as $kecamatan)
                                <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Desa *</label>
                        <select wire:model.live="lokasiDesa" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            <option value="">Semua Desa</option>
                            @foreach($this->desaOptions as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Nomor RW (Opsional)</label>
                        <select wire:model.live="nomorRw" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;" {{ empty($this->rwOptions) ? 'disabled' : '' }}>
                            <option value="">Pilih RW (Jika program per RW)</option>
                            @foreach($this->rwOptions as $rw)
                                <option value="{{ $rw }}">RW {{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($this->statusWilayahConfig)
                <div style="margin-top:14px;padding:12px;border-radius:8px;background:{{ $this->statusWilayahConfig['bg'] }};border:0.5px solid {{ $this->statusWilayahConfig['text'] }}33;">
                    <div style="font-size:11px;color:{{ $this->statusWilayahConfig['text'] }};margin-bottom:4px;">Status Prioritas Wilayah Saat Ini:</div>
                    <div style="font-size:14px;font-weight:600;color:{{ $this->statusWilayahConfig['text'] }};">{{ $this->statusWilayahConfig['label'] }}</div>
                </div>
                @endif

                <div style="display:grid;gap:14px;margin-top:14px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Jenis Program *</label>
                        <select wire:model="jenisProgram" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            @foreach(\App\Models\ProgramArahan::JENIS_PROGRAM as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Target Angka *</label>
                            <input wire:model="targetAngka" type="number" min="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Satuan (Opsional)</label>
                            <input wire:model="satuan" type="text" placeholder="Orang, Titik, RW..." style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="nextStep" type="button" style="padding:8px 18px;border-radius:8px;background:#fe5000;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                </div>
            </div>
            @endif

            {{-- Step 2 --}}
            @if($infoStep === 2)
            <div>
                <div style="display:grid;gap:14px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Judul Program *</label>
                        <input wire:model="judul" type="text" placeholder="Contoh: Pembentukan Korwe RW 05" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Tujuan, sasaran..." style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Tanggal Mulai *</label>
                            <input wire:model="tanggalMulai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Tanggal Selesai (Opsional)</label>
                            <input wire:model="tanggalSelesai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Penyelenggara</label>
                            <input wire:model="penyelenggara" type="text" placeholder="DPRa / DPC..." style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">PIC Nama</label>
                            <input wire:model="picNama" type="text" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">PIC WA/HP</label>
                            <input wire:model="picHp" type="text" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Level Organisasi *</label>
                            <select wire:model.live="orgLevel" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                @foreach($this->orgLevelOptions as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($orgLevel === 'dpd')
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Bidang DPD (Opsional)</label>
                            <select wire:model="bidangDpdId" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                <option value="">- Semua Bidang -</option>
                                @foreach($this->bidangOptions as $bdg)
                                    <option value="{{ $bdg->id }}">{{ $bdg->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="prevStep" type="button" style="padding:8px 18px;border-radius:8px;background:#f4f4f5;color:#444;border:0.5px solid #d4d4d8;font-size:13px;font-weight:600;cursor:pointer;"><i class="ti ti-arrow-left"></i> Sebelumnya</button>
                    <button wire:click="nextStep" type="button" style="padding:8px 18px;border-radius:8px;background:#fe5000;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                </div>
            </div>
            @endif

            {{-- Step 3 --}}
            @if($infoStep === 3)
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;">Rencana Anggaran Biaya (RAB) Opsional</div>
                    <button type="button" wire:click="addBudgetItem" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;background:#fe5000;color:white;font-size:11px;border:none;cursor:pointer;">
                        <i class="ti ti-plus" style="font-size:12px;"></i> Tambah item
                    </button>
                </div>

                @if(empty($budgetItems))
                <div style="text-align:center;padding:24px;color:#aaa;font-size:12px;">
                    <i class="ti ti-receipt" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                    Belum ada item anggaran. Klik "Tambah item" jika butuh RAB.
                </div>
                @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#fafafa;border-bottom:0.5px solid #e5e5e5;">
                                <th style="padding:8px;text-align:left;color:#666;font-weight:500;">Item</th>
                                <th style="padding:8px;text-align:left;color:#666;font-weight:500;">Kategori</th>
                                <th style="padding:8px;text-align:left;color:#666;font-weight:500;width:80px;">Qty</th>
                                <th style="padding:8px;text-align:left;color:#666;font-weight:500;width:100px;">Satuan</th>
                                <th style="padding:8px;text-align:left;color:#666;font-weight:500;width:130px;">Harga (Rp)</th>
                                <th style="padding:8px;text-align:right;color:#666;font-weight:500;width:130px;">Total (Rp)</th>
                                <th style="padding:8px;text-align:center;width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgetItems as $i => $item)
                            <tr style="border-bottom:0.5px solid #e5e5e5;">
                                <td style="padding:8px;"><input wire:model="budgetItems.{{ $i }}.item" type="text" placeholder="Contoh: Konsumsi" style="width:100%;border:1px solid #d4d4d8;border-radius:4px;padding:4px 8px;font-size:12px;"></td>
                                <td style="padding:8px;"><input wire:model="budgetItems.{{ $i }}.kategori" type="text" style="width:100%;border:1px solid #d4d4d8;border-radius:4px;padding:4px 8px;font-size:12px;"></td>
                                <td style="padding:8px;"><input wire:model="budgetItems.{{ $i }}.qty" type="number" min="1" style="width:100%;border:1px solid #d4d4d8;border-radius:4px;padding:4px 8px;font-size:12px;"></td>
                                <td style="padding:8px;"><input wire:model="budgetItems.{{ $i }}.satuan" type="text" style="width:100%;border:1px solid #d4d4d8;border-radius:4px;padding:4px 8px;font-size:12px;"></td>
                                <td style="padding:8px;"><input wire:model="budgetItems.{{ $i }}.harga_satuan" type="number" min="0" style="width:100%;border:1px solid #d4d4d8;border-radius:4px;padding:4px 8px;font-size:12px;"></td>
                                <td style="padding:8px;text-align:right;font-weight:500;color:#333;">Rp {{ number_format(((float)($item['qty']??1) * (float)($item['harga_satuan']??0)), 0, ',', '.') }}</td>
                                <td style="padding:8px;text-align:center;">
                                    <button type="button" wire:click="removeBudgetItem({{ $i }})" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px;"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="padding:8px;text-align:right;font-size:13px;font-weight:600;color:#444;">TOTAL RAB :</td>
                                <td style="padding:8px;text-align:right;font-size:13px;font-weight:600;color:#fe5000;">
                                    Rp {{ number_format($this->totalBudget, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
                <div style="display:grid;gap:10px;margin-top:14px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Sumber dana</label>
                        <input wire:model="fundingSource" type="text" placeholder="Iuran / Sponsor / DPD / Hibah" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Catatan anggaran</label>
                        <textarea wire:model="budgetNotes" rows="3" placeholder="Catatan tambahan terkait anggaran" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="prevStep" type="button" style="padding:8px 18px;border-radius:8px;background:#f4f4f5;color:#444;border:0.5px solid #d4d4d8;font-size:13px;font-weight:600;cursor:pointer;"><i class="ti ti-arrow-left"></i> Sebelumnya</button>
                    <button wire:click="nextStep" type="button" style="padding:8px 18px;border-radius:8px;background:#fe5000;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                </div>
            </div>
            @endif

            {{-- Step 4 --}}
            @if($infoStep === 4)
            <div>
                <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:12px;">Review Program</div>
                <div style="background:#fafafa;padding:16px;border-radius:12px;font-size:13px;margin-bottom:20px;border:0.5px solid #e5e5e5;">
                    <table style="width:100%;">
                        <tr>
                            <td style="padding:4px 0;width:150px;color:#666;">Judul</td>
                            <td style="padding:4px 0;font-weight:500;color:#1f2937;">{{ $judul }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;color:#666;">Wilayah</td>
                            <td style="padding:4px 0;font-weight:500;color:#1f2937;">{{ $lokasiDesa ?: 'Semua' }} {{ $nomorRw ? 'RW '.$nomorRw : '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;color:#666;">Jenis Program</td>
                            <td style="padding:4px 0;font-weight:500;color:#1f2937;">{{ \App\Models\ProgramArahan::JENIS_PROGRAM[$jenisProgram] ?? $jenisProgram }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;color:#666;">Target Angka</td>
                            <td style="padding:4px 0;font-weight:500;color:#1f2937;">{{ $targetAngka }} {{ $satuan }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;color:#666;">Total RAB</td>
                            <td style="padding:4px 0;font-weight:500;color:#fe5000;">Rp {{ number_format($this->totalBudget, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

                <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:12px;">Alur Persetujuan (Approval)</div>
                <div style="display:flex;gap:12px;">
                    <div style="padding:8px 12px;background:#fff7ed;color:#c2410c;border:0.5px solid #fed7aa;border-radius:6px;font-size:12px;font-weight:600;">1. DPRa</div>
                    <div style="padding:8px 12px;background:#fff7ed;color:#c2410c;border:0.5px solid #fed7aa;border-radius:6px;font-size:12px;font-weight:600;">2. DPC</div>
                    <div style="padding:8px 12px;background:#fff7ed;color:#c2410c;border:0.5px solid #fed7aa;border-radius:6px;font-size:12px;font-weight:600;">3. DPD</div>
                </div>
                <div style="font-size:11px;color:#888;margin-top:6px;">Alur persetujuan ini akan otomatis dibuat setelah program diajukan.</div>

                <div style="display:flex;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                    <button wire:click="prevStep" type="button" style="padding:8px 18px;border-radius:8px;background:#f4f4f5;color:#444;border:0.5px solid #d4d4d8;font-size:13px;font-weight:600;cursor:pointer;"><i class="ti ti-arrow-left"></i> Sebelumnya</button>
                    <div>
                        <button wire:click="simpanDanAjukan" type="button" style="padding:8px 18px;border-radius:8px;background:#16a34a;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Ajukan Program</button>
                    </div>
                </div>
            </div>
            @endif
        </div>

            </div>
        </div>
    </div>
</div>
