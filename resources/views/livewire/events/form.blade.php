<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div x-data="{ tab: 'info', infoStep: 1 }" style="width:100%;margin:0;box-sizing:border-box;">
        
        {{-- DARK HEADER --}}
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-radius:14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="{{ route('events.index') }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:rgba(255,255,255,.08);color:#f5f5f5;font-size:12px;text-decoration:none;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <div>
                    <div style="font-size:15px;font-weight:500;">{{ $pageTitle }}</div>
                    <div style="font-size:11px;color:#a3a3a3;margin-top:2px;">{{ $pageSubtitle }}</div>
                </div>
            </div>
            <div style="font-size:11px;color:#a3a3a3;">{{ now()->format('d M Y H:i') }}</div>
        </div>

        {{-- WHITE BODY --}}
        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;padding:0;">
            
            {{-- TAB NAVIGATION & ACTION BUTTONS --}}
            <div style="padding:20px 20px 0;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;margin-bottom:10px;">Kelengkapan Data</div>
                    <div style="overflow-x:auto;">
                        <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:#f4f4f5;border:0.5px solid #e4e4e7;max-width:100%;">
                        @foreach ([
                            ['key' => 'info',        'label' => 'Informasi'],
                            ['key' => 'persiapan',   'label' => 'Persiapan'],
                            ['key' => 'evaluasi',    'label' => 'Pelaksanaan & Evaluasi'],
                        ] as $t)
                        <button type="button"
                            @click="tab='{{ $t['key'] }}'"
                            :style="'padding:8px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;' + (tab==='{{ $t['key'] }}' ? 'background:#fed7aa;color:#c2410c;' : 'background:transparent;color:#71717a;')">{{ $t['label'] }}</button>
                        @endforeach
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <a href="{{ route('events.index') }}" wire:navigate style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:13px;display:inline-flex;align-items:center;text-decoration:none;">
                        Batal
                    </a>
                    <button wire:click="simpanDraft" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;font-weight:500;cursor:pointer;">
                        <span wire:loading.remove wire:target="simpanDraft">Simpan Draft</span>
                        <span wire:loading wire:target="simpanDraft">Menyimpan...</span>
                    </button>
                    @if(isset($orgLevel) && $orgLevel === 'dpd')
                    <button wire:click="saveAndSubmit" type="button" style="height:40px;padding:0 20px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                        <span wire:loading.remove wire:target="saveAndSubmit">Ajukan Approval</span>
                        <span wire:loading wire:target="saveAndSubmit">Memproses...</span>
                    </button>
                    @else
                    <button wire:click="saveAndSubmit" type="button" style="height:40px;padding:0 20px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                        <span wire:loading.remove wire:target="saveAndSubmit">Simpan & Aktifkan</span>
                        <span wire:loading wire:target="saveAndSubmit">Memproses...</span>
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
        @if(!empty($sourceKegiatan))
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#fff7ed;border:0.5px solid #fed7aa;color:#9a3412;font-size:12px;">
                Dibuat dari kegiatan Sisir RW — {{ $sourceKegiatan['desa'] ?? '' }} RW {{ $sourceKegiatan['rw'] ?? '' }} · {{ $sourceKegiatan['tanggal'] ?? '' }}
            </div>
        @endif

        {{-- ========== TAB: INFORMASI ========== --}}
        <div x-show="tab==='info'">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">

                    {{-- Progress Indicator --}}
                    <div style="display:flex;align-items:center;margin-bottom:24px;border-bottom:0.5px solid #e5e5e5;padding-bottom:16px;overflow-x:auto;">
                        <div @click="infoStep = 1" :style="'display:flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;' + (infoStep >= 1 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;')">
                            <div :style="'width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;' + (infoStep >= 1 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;')">1</div>
                            <span style="font-size:13px;">Informasi Utama</span>
                        </div>
                        <div style="flex:1;min-width:30px;height:1px;background:#e5e5e5;margin:0 16px;"></div>
                        <div @click="infoStep = 2" :style="'display:flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;' + (infoStep >= 2 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;')">
                            <div :style="'width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;' + (infoStep >= 2 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;')">2</div>
                            <span style="font-size:13px;">Pihak Terkait</span>
                        </div>
                        <div style="flex:1;min-width:30px;height:1px;background:#e5e5e5;margin:0 16px;"></div>
                        <div @click="infoStep = 3" :style="'display:flex;align-items:center;gap:8px;cursor:pointer;white-space:nowrap;' + (infoStep >= 3 ? 'color:#fe5000;font-weight:600;' : 'color:#a1a1aa;')">
                            <div :style="'width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;' + (infoStep >= 3 ? 'background:#fe5000;color:white;' : 'background:#f4f4f5;color:#a1a1aa;')">3</div>
                            <span style="font-size:13px;">Waktu & Lokasi</span>
                        </div>
                    </div>

                {{-- Step 1: Informasi Utama --}}
                <div x-show="infoStep === 1">
                    <div style="display:grid;gap:14px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Judul program *</label>
                            <input wire:model="judul" type="text" placeholder="Contoh: Pengajian RW 08" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            @error('judul') <div style="font-size:10px;color:#dc2626;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Deskripsi</label>
                            <textarea wire:model="deskripsi" rows="5" placeholder="Agenda, tujuan, target peserta..." style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Persyaratan peserta (opsional)</label>
                            <textarea wire:model="requirements" rows="3" placeholder="Contoh: Warga RW 08, membawa KTP, dll" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Jenis program *</label>
                                <select wire:model="jenis" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                    @foreach(\App\Models\Event::JENIS_EVENT as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Target program</label>
                                <select wire:model="targetProgram" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                    <option value="">- Pilih -</option>
                                    <option value="penambahan_anggota">Penambahan Anggota</option>
                                    <option value="pembinaan_internal">Pembinaan Internal</option>
                                    <option value="sosialisasi_partai">Sosialisasi Partai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                        <button type="button" @click="infoStep = 2" style="padding:8px 18px;border-radius:8px;background:#fe5000;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                    </div>
                </div>

                {{-- Step 2: Pihak Terkait --}}
                <div x-show="infoStep === 2" style="display:none;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div style="grid-column: span 2;">
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Level penyelenggara *</label>
                            <select wire:model="orgLevel" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                @foreach($this->orgLevelOptions as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(isset($orgLevel) && $orgLevel === 'dpd')
                        <div style="grid-column: span 2;">
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Bidang DPD (opsional)</label>
                            <select wire:model="bidangDpdId" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                <option value="">- Pilih Bidang -</option>
                                @foreach($this->bidangOptions as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Penyelenggara</label>
                            <input wire:model="penyelenggara" type="text" placeholder="DPRa / Tim Lapangan" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">PIC nama</label>
                            <input wire:model="picNama" type="text" placeholder="Nama PIC" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">PIC HP</label>
                            <input wire:model="picHp" type="text" placeholder="08xxxxxxxxxx" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Pembicara (opsional)</label>
                            <input wire:model="speakers" type="text" placeholder="Nama ustadz / narasumber" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                        <button type="button" @click="infoStep = 1" style="padding:8px 18px;border-radius:8px;background:#f4f4f5;color:#444;border:0.5px solid #d4d4d8;font-size:13px;font-weight:600;cursor:pointer;"><i class="ti ti-arrow-left"></i> Sebelumnya</button>
                        <button type="button" @click="infoStep = 3" style="padding:8px 18px;border-radius:8px;background:#fe5000;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                    </div>
                </div>

                {{-- Step 3: Waktu & Lokasi --}}
                <div x-show="infoStep === 3" style="display:none;">
                    <div style="display:grid;gap:14px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Tanggal mulai *</label>
                                <input wire:model="tanggalMulai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Tanggal selesai</label>
                                <input wire:model="tanggalSelesai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Alamat lengkap *</label>
                            <input wire:model="lokasi" type="text" placeholder="Nama tempat / alamat" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Dapil</label>
                                <select wire:model.live="lokasiDapil" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                    <option value="">- Dapil -</option>
                                    @foreach($this->dapilOptions as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Kecamatan</label>
                                <select wire:model.live="lokasiKecamatan" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                    <option value="">- Kecamatan -</option>
                                    @foreach($this->kecamatanOptions as $k) <option value="{{ $k }}">{{ $k }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Desa</label>
                                <select wire:model="lokasiDesa" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                    <option value="">- Desa -</option>
                                    @foreach($this->desaOptions as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Kapasitas</label>
                                <input wire:model="kapasitas" type="number" min="0" placeholder="0 = unlimited" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Cover image</label>
                                <input wire:model="coverImage" type="file" accept="image/*" style="font-size:12px;width:100%;padding-top:8px;">
                            </div>
                        </div>
                        @if(!empty($existingCover))
                        <div>
                            <div style="font-size:11px;color:#666;margin-bottom:5px;">Cover saat ini</div>
                            <img src="{{ asset('storage/'.$existingCover) }}" style="width:120px;height:80px;object-fit:cover;border-radius:8px;border:0.5px solid #e5e5e5;">
                        </div>
                        @endif
                        <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:#666;cursor:pointer;">
                            <input wire:model="isPublic" type="checkbox">
                            <span>Tampilkan di website publik</span>
                        </label>
                    </div>
                        <div style="display:flex;justify-content:space-between;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                            <button type="button" @click="infoStep = 2" style="padding:8px 18px;border-radius:8px;background:#f4f4f5;color:#444;border:0.5px solid #d4d4d8;font-size:13px;font-weight:600;cursor:pointer;"><i class="ti ti-arrow-left"></i> Sebelumnya</button>
                            <button type="button" @click="tab = 'persiapan'" style="padding:8px 18px;border-radius:8px;background:#16a34a;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">Lanjut ke Persiapan <i class="ti ti-arrow-right"></i></button>
                        </div>
                    </div>

                </div>
            </div>

        {{-- ========== TAB: PERSIAPAN ========== --}}
        <div x-show="tab==='persiapan'" style="display:none;">
            <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,.8fr);gap:14px;" class="event-form-grid">

                {{-- RAB --}}
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;">Rencana Anggaran Biaya (RAB)</div>
                        <button type="button" wire:click="addBudgetItem" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;background:#fe5000;color:white;font-size:11px;border:none;cursor:pointer;">
                            <i class="ti ti-plus" style="font-size:12px;"></i> Tambah item
                        </button>
                    </div>

                    @if(empty($budgetItems))
                    <div style="text-align:center;padding:24px;color:#aaa;font-size:12px;">
                        <i class="ti ti-receipt" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                        Belum ada item anggaran. Klik "Tambah item" untuk mulai.
                    </div>
                    @else
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead>
                                <tr style="background:#fafafa;border-bottom:0.5px solid #e5e5e5;">
                                    <th style="padding:8px;text-align:left;color:#666;font-weight:500;">Item</th>
                                    <th style="padding:8px;text-align:left;color:#666;font-weight:500;">Kategori</th>
                                    <th style="padding:8px;text-align:center;color:#666;font-weight:500;width:60px;">Qty</th>
                                    <th style="padding:8px;text-align:left;color:#666;font-weight:500;width:70px;">Satuan</th>
                                    <th style="padding:8px;text-align:right;color:#666;font-weight:500;">Harga/satuan</th>
                                    <th style="padding:8px;text-align:right;color:#666;font-weight:500;">Subtotal</th>
                                    <th style="padding:8px;width:30px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budgetItems as $i => $bi)
                                <tr style="border-bottom:0.5px solid #f0f0f0;">
                                    <td style="padding:6px 8px;">
                                        <input wire:model="budgetItems.{{ $i }}.item" type="text" placeholder="Nama item" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                    </td>
                                    <td style="padding:6px 8px;">
                                        <input wire:model="budgetItems.{{ $i }}.kategori" type="text" placeholder="ATK / Konsumsi / dll" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;">
                                    </td>
                                    <td style="padding:6px 8px;">
                                        <input wire:model="budgetItems.{{ $i }}.qty" type="number" min="1" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;text-align:center;">
                                    </td>
                                    <td style="padding:6px 8px;">
                                        <input wire:model="budgetItems.{{ $i }}.satuan" type="text" placeholder="pcs" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 6px;font-size:12px;">
                                    </td>
                                    <td style="padding:6px 8px;">
                                        <input wire:model="budgetItems.{{ $i }}.harga_satuan" type="number" min="0" placeholder="0" style="width:100%;height:32px;border-radius:6px;border:0.5px solid #d4d4d8;padding:0 8px;font-size:12px;text-align:right;">
                                    </td>
                                    <td style="padding:6px 8px;text-align:right;color:#666;">
                                        Rp {{ number_format(($bi['qty'] ?? 1) * ($bi['harga_satuan'] ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td style="padding:6px 8px;">
                                        <button type="button" wire:click="removeBudgetItem({{ $i }})" style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:14px;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:#fafafa;border-top:0.5px solid #e5e5e5;">
                                    <td colspan="5" style="padding:8px;text-align:right;font-size:12px;font-weight:500;color:#444;">Total RAB:</td>
                                    <td style="padding:8px;text-align:right;font-size:13px;font-weight:600;color:#fe5000;">
                                        Rp {{ number_format($this->totalBudget, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Kanan: Info persiapan --}}
                <div style="display:grid;gap:12px;align-content:start;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                        <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Info persiapan</div>
                        <div style="display:grid;gap:10px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Sumber dana</label>
                                <input wire:model="fundingSource" type="text" placeholder="Iuran / Sponsor / DPD / Hibah" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Catatan anggaran</label>
                                <textarea wire:model="budgetNotes" rows="3" placeholder="Catatan tambahan terkait anggaran" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== TAB: EVALUASI ========== --}}
        <div x-show="tab==='evaluasi'" style="display:none;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:stretch;" class="event-form-grid">
                
                {{-- Data Pelaksanaan --}}
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                    <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Data pelaksanaan</div>
                    <div style="display:grid;gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Jumlah peserta hadir</label>
                            <input wire:model="pesertaHadir" type="number" min="0" placeholder="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            <p style="font-size:11px;color:#aaa;margin-top:4px;">Isi setelah event selesai dilaksanakan.</p>
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Foto dokumentasi (opsional)</label>
                            <input wire:model="dokFoto" type="file" accept="image/*,video/*" multiple style="font-size:12px;width:100%;">
                            <p style="font-size:11px;color:#aaa;margin-top:4px;">Format: JPG, PNG, MP4. Bisa pilih beberapa file.</p>
                        </div>
                        <div style="padding:12px;border-radius:8px;background:#fff7ed;border:0.5px solid #fed7aa;">
                            <p style="font-size:12px;color:#92400e;">
                                <i class="ti ti-info-circle" style="font-size:13px;"></i>
                                Data peserta detail bisa diinput di halaman detail event setelah event disimpan (fitur Bulk Paste Peserta).
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Evaluasi & Laporan --}}
                <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                    <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Evaluasi & laporan</div>
                    <div style="display:grid;gap:12px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Penilaian acara</label>
                                <select wire:model="evaluasiRating" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                    <option value="">- Pilih -</option>
                                    <option value="sesuai_target">✅ Sesuai target</option>
                                    <option value="kurang">⚠ Kurang / perlu perbaikan</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Realisasi anggaran (Rp)</label>
                                <input wire:model="evaluasiRealisasiAnggaran" type="number" min="0" placeholder="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Ringkasan hasil kegiatan</label>
                            <textarea wire:model="evaluasiRingkasan" rows="4" placeholder="Ceritakan jalannya acara, pencapaian, dan hal-hal menonjol..." style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Evaluasi & perbaikan ke depan</label>
                            <textarea wire:model="evaluasiCatatan" rows="3" placeholder="Apa yang perlu diperbaiki untuk event berikutnya?" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                        </div>
                        <div style="padding:12px;border-radius:8px;background:#f0f9ff;border:0.5px solid #bae6fd;">
                            <p style="font-size:12px;color:#0369a1;">
                                <i class="ti ti-info-circle" style="font-size:13px;"></i>
                                Laporan Pertanggungjawaban (LPJ) lengkap bisa dibuat di halaman detail event setelah semua data terisi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

        </div>
    </div>
    
    <style>
        @media(max-width:900px) {
            .event-form-grid { grid-template-columns: minmax(0,1fr) !important; }
        }
    </style>
</div>
