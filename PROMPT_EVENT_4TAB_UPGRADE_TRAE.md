# Prompt Antigravity — Upgrade Form Event: 4 Tab (Adopsi dari Versi Lama)

Form event saat ini hanya punya tab Informasi Utama. Upgrade ke sistem 4 tab seperti versi lama: Informasi, Persiapan (RAB), Pelaksanaan (peserta + foto), Evaluasi (laporan). Semua data yang dibutuhkan sudah ada di tabel `event_budget_items`, `event_reports`, `event_pesertas`, `event_approvals` — tinggal buat form inputnya.

Langsung kerjakan, JANGAN test, JANGAN tanya.

---

## YANG PERLU DIUBAH

### 1. UPDATE file: `resources/views/livewire/events/form.blade.php`

Ganti seluruh isi form dengan sistem 4 tab menggunakan Alpine.js `x-data`.
Semua `wire:model` yang sudah ada JANGAN DIUBAH.
Yang baru ditambahkan adalah tab Persiapan, Pelaksanaan, Evaluasi.

### 2. UPDATE file: `app/Livewire/Events/Create.php`

Tambah properties baru untuk tab Persiapan:
```php
// Tab Persiapan
public string $speakers = '';
public string $fundingSource = '';
public string $budgetNotes = '';
public array $budgetItems = []; // array of [item, kategori, qty, satuan, harga_satuan, keterangan]

// Tab Pelaksanaan
public int $pesertaHadir = 0;
public array $dokFoto = [];

// Tab Evaluasi
public string $evaluasiRingkasan = '';
public string $evaluasiCatatan = '';
public string $evaluasiRealisasiAnggaran = '0';
public string $evaluasiRating = ''; // sesuai_target | kurang
```

Tambah method:
```php
public function addBudgetItem(): void
{
    $this->budgetItems[] = [
        'item' => '',
        'kategori' => '',
        'qty' => 1,
        'satuan' => 'pcs',
        'harga_satuan' => 0,
        'keterangan' => '',
    ];
}

public function removeBudgetItem(int $index): void
{
    unset($this->budgetItems[$index]);
    $this->budgetItems = array_values($this->budgetItems);
}

public function getTotalBudgetProperty(): float
{
    return collect($this->budgetItems)->sum(fn($item) => 
        (float)($item['qty'] ?? 1) * (float)($item['harga_satuan'] ?? 0)
    );
}
```

Update method `persist()` — setelah event dibuat, simpan budget items:
```php
// Setelah $event->save(); tambahkan:
if (!empty($this->budgetItems)) {
    foreach ($this->budgetItems as $item) {
        if (empty($item['item'])) continue;
        $qty = (int)($item['qty'] ?? 1);
        $harga = (float)str_replace(['.', ','], ['', '.'], $item['harga_satuan'] ?? '0');
        \App\Models\EventBudgetItem::create([
            'event_id' => $event->id,
            'item' => $item['item'],
            'kategori' => $item['kategori'] ?? null,
            'qty' => $qty,
            'satuan' => $item['satuan'] ?? 'pcs',
            'harga_satuan' => $harga,
            'subtotal' => $qty * $harga,
            'keterangan' => $item['keterangan'] ?? null,
        ]);
    }
}
```

### 3. UPDATE file: `app/Livewire/Events/Edit.php`

Tambah properties yang sama dengan Create.php di atas.
Di `mount()`, load budget items yang sudah ada:
```php
$this->budgetItems = $event->budgetItems->map(fn($b) => [
    'item' => $b->item,
    'kategori' => $b->kategori ?? '',
    'qty' => $b->qty,
    'satuan' => $b->satuan,
    'harga_satuan' => $b->harga_satuan,
    'keterangan' => $b->keterangan ?? '',
])->toArray();

$this->speakers = (string)($event->speakers ?? '');
$this->fundingSource = (string)($event->funding_source ?? '');
```

Tambah method addBudgetItem, removeBudgetItem, getTotalBudgetProperty yang sama.

Di method update/persist, delete budget items lama lalu insert baru:
```php
$event->budgetItems()->delete();
// lalu insert baru (sama dengan Create)
```

### 4. MIGRATION: Tambah kolom ke events table

Cek dulu apakah kolom sudah ada, jika belum buat migration baru:
```php
// File: database/migrations/2026_06_20_add_preparation_fields_to_events_table.php

Schema::table('events', function (Blueprint $table) {
    if (!Schema::hasColumn('events', 'speakers')) {
        $table->text('speakers')->nullable()->after('pic_hp');
    }
    if (!Schema::hasColumn('events', 'funding_source')) {
        $table->string('funding_source')->nullable()->after('speakers');
    }
    if (!Schema::hasColumn('events', 'target_program')) {
        $table->string('target_program')->nullable()->after('funding_source');
        // penambahan_anggota | pembinaan_internal | sosialisasi_partai
    }
    if (!Schema::hasColumn('events', 'requirements')) {
        $table->text('requirements')->nullable()->after('target_program');
    }
});
```

Jalankan: `php artisan migrate`

---

## FORM.BLADE.PHP — STRUKTUR BARU

Ganti seluruh `resources/views/livewire/events/form.blade.php`:

```blade
<div x-data="{ tab: 'info' }" style="min-height:100vh;background:#fafafa;">

    {{-- HEADER --}}
    <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
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

    {{-- TAB NAVIGATION --}}
    <div style="background:white;border-bottom:0.5px solid #e5e5e5;padding:0 20px;display:flex;gap:4px;">
        @foreach ([
            ['key' => 'info',        'icon' => 'ti-info-circle',   'label' => 'Informasi'],
            ['key' => 'persiapan',   'icon' => 'ti-clipboard-list','label' => 'Persiapan'],
            ['key' => 'pelaksanaan', 'icon' => 'ti-player-play',   'label' => 'Pelaksanaan'],
            ['key' => 'evaluasi',    'icon' => 'ti-chart-bar',     'label' => 'Evaluasi'],
        ] as $t)
        <button type="button"
            @click="tab='{{ $t['key'] }}'"
            :style="tab==='{{ $t['key'] }}' ? 'color:#fe5000;border-bottom:2px solid #fe5000;font-weight:500;' : 'color:#888;'"
            style="display:inline-flex;align-items:center;gap:5px;padding:12px 14px;font-size:12px;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;">
            <i class="ti {{ $t['icon'] }}" style="font-size:14px;"></i>
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    <div style="padding:16px 20px;">

        {{-- FLASH / ERROR --}}
        @if(session('message'))
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">{{ session('message') }}</div>
        @endif
        @if($errors->any())
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#fef2f2;border:0.5px solid #fecaca;color:#dc2626;font-size:12px;">{{ $errors->first() }}</div>
        @endif
        @if($sourceKegiatan !== [])
            <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#fff7ed;border:0.5px solid #fed7aa;color:#9a3412;font-size:12px;">
                Dibuat dari kegiatan Sisir RW — {{ $sourceKegiatan['desa'] }} RW {{ $sourceKegiatan['rw'] }} · {{ $sourceKegiatan['tanggal'] }}
            </div>
        @endif

        {{-- ========== TAB: INFORMASI ========== --}}
        <div x-show="tab==='info'">
            <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,.95fr);gap:14px;" class="event-form-grid">

                {{-- Kiri --}}
                <div style="display:grid;gap:12px;align-content:start;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                        <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Informasi utama</div>
                        <div style="display:grid;gap:12px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Judul event *</label>
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
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Jenis event *</label>
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
                    </div>

                    {{-- Pihak terkait --}}
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                        <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Pihak terkait</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
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
                    </div>
                </div>

                {{-- Kanan --}}
                <div style="display:grid;gap:12px;align-content:start;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;">
                        <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Waktu & lokasi</div>
                        <div style="display:grid;gap:12px;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
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
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
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
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
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

        {{-- ========== TAB: PELAKSANAAN ========== --}}
        <div x-show="tab==='pelaksanaan'" style="display:none;">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;max-width:600px;">
                <div style="font-size:10px;color:#fe5000;font-weight:500;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px;">Data pelaksanaan</div>
                <div style="display:grid;gap:12px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Jumlah peserta hadir</label>
                        <input wire:model="pesertaHadir" type="number" min="0" placeholder="0" style="width:200px;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        <p style="font-size:11px;color:#aaa;margin-top:4px;">Isi setelah event selesai dilaksanakan.</p>
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Foto dokumentasi (opsional)</label>
                        <input wire:model="dokFoto" type="file" accept="image/*,video/*" multiple style="font-size:12px;">
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
        </div>

        {{-- ========== TAB: EVALUASI ========== --}}
        <div x-show="tab==='evaluasi'" style="display:none;">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;max-width:700px;">
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

        {{-- ACTION BUTTONS --}}
        <div style="margin-top:16px;display:flex;justify-content:flex-end;align-items:center;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('events.index') }}" wire:navigate style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#666;font-size:13px;display:inline-flex;align-items:center;text-decoration:none;">
                Batal
            </a>
            <button wire:click="simpanDraft" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;font-weight:500;cursor:pointer;">
                <span wire:loading.remove wire:target="simpanDraft">{{ $submitDraftLabel }}</span>
                <span wire:loading wire:target="simpanDraft">Menyimpan...</span>
            </button>
            <button wire:click="saveAndSubmit" type="button" style="height:40px;padding:0 20px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                <span wire:loading.remove wire:target="saveAndSubmit">{{ $submitApprovalLabel }}</span>
                <span wire:loading wire:target="saveAndSubmit">Memproses...</span>
            </button>
        </div>

    </div>

    <style>
        @media(max-width:900px) {
            .event-form-grid { grid-template-columns: minmax(0,1fr) !important; }
        }
    </style>
</div>
```

---

## CATATAN PENTING

1. **Alpine.js** `x-show` sudah tersedia via Flux UI — tidak perlu install.

2. **`$this->totalBudget`** adalah computed property di Livewire — perlu tambah `#[Computed]` attribute:
   ```php
   #[Computed]
   public function totalBudget(): float
   {
       return collect($this->budgetItems)->sum(fn($item) =>
           (float)($item['qty'] ?? 1) * (float)($item['harga_satuan'] ?? 0)
       );
   }
   ```

3. **Tab Pelaksanaan dan Evaluasi** di form Create — data ini disimpan ke `event_reports` tabel. Tambahkan di method `persist()` setelah event dibuat:
   ```php
   if ($this->pesertaHadir > 0 || $this->evaluasiRingkasan !== '') {
       \App\Models\EventReport::create([
           'event_id' => $event->id,
           'ringkasan' => $this->evaluasiRingkasan ?: $judul,
           'peserta_hadir' => $this->pesertaHadir,
           'evaluasi' => $this->evaluasiCatatan ?: null,
           'realisasi_anggaran' => (float)$this->evaluasiRealisasiAnggaran,
           'created_by' => auth()->id(),
       ]);
   }
   ```

4. **Field `target_program`, `requirements`, `speakers`, `funding_source`** — tambah ke `$fillable` Event model dan ke method `persist()` sebelum `$event->save()`.

5. **Edit.php** — load data yang sama dari event saat mount, dan di update() hapus budget items lama lalu insert baru.

6. Jalankan: `php artisan migrate` untuk kolom baru.

Langsung kerjakan. Jangan test.
