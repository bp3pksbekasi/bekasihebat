# Prompt Bertahap Trae — Modul Event / Kegiatan

---

## PROMPT 1: Migration & Model

```
Buat modul Event/Kegiatan dengan approval berjenjang. Langsung buat, JANGAN test, JANGAN tanya.

KONTEKS: Laravel 12, Livewire, UUID (HasUuids). Database-driven.

== 1. MIGRATION: create_events_table ==

```php
Schema::create('events', function (Blueprint $table) {
    $table->uuid('id')->primary();
    // Dasar
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->string('jenis');                    // baksos, pengajian, senam, diskusi, pelatihan, musyawarah, bedah_rumah, kesehatan, pendidikan, lainnya
    $table->dateTime('tanggal_mulai');
    $table->dateTime('tanggal_selesai')->nullable();
    $table->string('lokasi');                   // alamat lengkap
    $table->string('lokasi_desa')->nullable();  // nama desa
    $table->string('lokasi_kecamatan')->nullable();
    $table->string('lokasi_dapil')->nullable();
    $table->integer('kapasitas')->default(0);   // 0 = unlimited
    // Visibility
    $table->boolean('is_public')->default(false);  // tampil di website publik?
    $table->string('cover_image')->nullable();     // path gambar cover
    // Status
    $table->string('status')->default('draft');    // draft, menunggu_approval, disetujui, ditolak, berlangsung, selesai, dibatalkan
    $table->string('level_approval')->default('dpra'); // level approval saat ini: dpra, dpc, dpd, selesai
    // Penyelenggara
    $table->string('penyelenggara')->nullable();   // nama organisasi/tim
    $table->string('pic_nama')->nullable();
    $table->string('pic_hp')->nullable();
    // Dari Sisir RW (nullable — event bisa dibuat tanpa dari Sisir RW)
    $table->uuid('kegiatan_rw_id')->nullable();
    $table->foreign('kegiatan_rw_id')->references('id')->on('kegiatan_rws')->nullOnDelete();
    // Meta
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['status']);
    $table->index(['is_public']);
    $table->index(['tanggal_mulai']);
    $table->index(['lokasi_dapil']);
});
```

Model Event (app/Models/Event.php):
- HasUuids, HasFactory
- fillable: semua kolom
- Casts: tanggal_mulai datetime, tanggal_selesai datetime, is_public boolean
- Constants:

```php
const STATUS_DRAFT = 'draft';
const STATUS_MENUNGGU = 'menunggu_approval';
const STATUS_DISETUJUI = 'disetujui';
const STATUS_DITOLAK = 'ditolak';
const STATUS_BERLANGSUNG = 'berlangsung';
const STATUS_SELESAI = 'selesai';
const STATUS_DIBATALKAN = 'dibatalkan';

const STATUS_CONFIG = [
    'draft' =>             ['label' => 'Draft',             'color' => '#888',    'bg' => '#f5f5f5'],
    'menunggu_approval' => ['label' => 'Menunggu Approval', 'color' => '#d97706', 'bg' => '#fff7f1'],
    'disetujui' =>         ['label' => 'Disetujui',         'color' => '#16a34a', 'bg' => '#dcfce7'],
    'ditolak' =>           ['label' => 'Ditolak',           'color' => '#dc2626', 'bg' => '#fee2e2'],
    'berlangsung' =>       ['label' => 'Berlangsung',       'color' => '#2563eb', 'bg' => '#dbeafe'],
    'selesai' =>           ['label' => 'Selesai',           'color' => '#16a34a', 'bg' => '#dcfce7'],
    'dibatalkan' =>        ['label' => 'Dibatalkan',        'color' => '#888',    'bg' => '#f5f5f5'],
];

const JENIS_EVENT = [
    'baksos' => 'Bakti Sosial',
    'pengajian' => 'Pengajian / Kajian',
    'senam' => 'Senam PKS',
    'diskusi' => 'Diskusi Warga',
    'pelatihan' => 'Pelatihan / Workshop',
    'musyawarah' => 'Musyawarah / Rapat',
    'bedah_rumah' => 'Bedah Rumah',
    'kesehatan' => 'Layanan Kesehatan',
    'pendidikan' => 'Bantuan Pendidikan',
    'lainnya' => 'Lainnya',
];

const LEVEL_APPROVAL = [
    'dpra' => ['label' => 'DPRa', 'order' => 1],
    'dpc'  => ['label' => 'DPC',  'order' => 2],
    'dpd'  => ['label' => 'DPD',  'order' => 3],
    'selesai' => ['label' => 'Selesai', 'order' => 4],
];
```

- Relations:
```php
public function approvals() { return $this->hasMany(EventApproval::class); }
public function budgetItems() { return $this->hasMany(EventBudgetItem::class); }
public function report() { return $this->hasOne(EventReport::class); }
public function registrations() { return $this->hasMany(EventRegistration::class); }
public function creator() { return $this->belongsTo(User::class, 'created_by'); }
public function kegiatanRw() { return $this->belongsTo(KegiatanRw::class); }
```

- Scopes: scopePublic, scopeByStatus, scopeByDapil, scopeUpcoming, scopeApproved
- Accessor: getStatusConfigAttribute, getTotalBudgetAttribute (sum budget items), getRegistrationCountAttribute

== 2. MIGRATION: create_event_approvals_table ==

```php
Schema::create('event_approvals', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('event_id');
    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    $table->string('level');              // dpra, dpc, dpd
    $table->string('status')->default('pending'); // pending, approved, rejected
    $table->uuid('approver_id')->nullable();
    $table->foreign('approver_id')->references('id')->on('users')->nullOnDelete();
    $table->text('catatan')->nullable();  // alasan approve/reject
    $table->timestamp('decided_at')->nullable();
    $table->timestamps();

    $table->index(['event_id', 'level']);
    $table->index(['status']);
});
```

Model EventApproval (app/Models/EventApproval.php):
- HasUuids, HasFactory
- fillable: event_id, level, status, approver_id, catatan, decided_at
- Casts: decided_at datetime
- Relations: event(), approver()

== 3. MIGRATION: create_event_budget_items_table ==

```php
Schema::create('event_budget_items', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('event_id');
    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    $table->string('item');               // nama item
    $table->string('kategori')->nullable(); // logistik, konsumsi, transport, honor, lainnya
    $table->integer('qty')->default(1);
    $table->string('satuan')->default('pcs'); // pcs, paket, orang, hari
    $table->decimal('harga_satuan', 15, 2)->default(0);
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->text('keterangan')->nullable();
    $table->timestamps();

    $table->index('event_id');
});
```

Model EventBudgetItem (app/Models/EventBudgetItem.php):
- HasUuids, HasFactory
- fillable: semua
- Relations: event()

== 4. MIGRATION: create_event_reports_table ==

```php
Schema::create('event_reports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('event_id');
    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    $table->text('ringkasan');
    $table->integer('peserta_hadir')->default(0);
    $table->text('evaluasi')->nullable();
    $table->text('tindak_lanjut')->nullable();
    $table->json('foto')->nullable();       // array path foto
    $table->decimal('realisasi_anggaran', 15, 2)->default(0);
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();
});
```

Model EventReport (app/Models/EventReport.php):
- HasUuids, HasFactory
- fillable: semua
- Casts: foto array
- Relations: event(), creator()

== 5. MIGRATION: create_event_registrations_table ==

```php
Schema::create('event_registrations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('event_id');
    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    $table->uuid('user_id');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->string('status')->default('registered'); // registered, confirmed, attended, cancelled
    $table->uuid('affiliate_user_id')->nullable();   // jika daftar via link affiliate
    $table->timestamp('attended_at')->nullable();
    $table->timestamps();

    $table->unique(['event_id', 'user_id']);
    $table->index(['event_id', 'status']);
});
```

Model EventRegistration (app/Models/EventRegistration.php):
- HasUuids, HasFactory
- fillable: semua
- Casts: attended_at datetime
- Relations: event(), user(), affiliate()

Jalankan: php artisan migrate
Jangan buat test.
```

---

## PROMPT 2: Route, Sidebar, Livewire — CRUD Event

```
Buat halaman CRUD Event dengan Livewire. Langsung buat, JANGAN test.

== 1. ROUTES ==

Di routes/web.php, dalam middleware auth:

Route::prefix('events')->middleware(['auth'])->group(function () {
    Route::get('/', App\Livewire\Events\Index::class)->name('events.index');
    Route::get('/create', App\Livewire\Events\Create::class)->name('events.create');
    Route::get('/{event}', App\Livewire\Events\Detail::class)->name('events.detail');
    Route::get('/{event}/edit', App\Livewire\Events\Edit::class)->name('events.edit');
});

== 2. SIDEBAR ==

Di sidebar.blade.php, pada group "Manajemen", update menu "Kegiatan / Event":
- Ganti href '#' ke route('events.index')
- Hapus badge "Segera"
- Tambahkan sub-menu (tampil jika request()->routeIs('events.*')):
  - Semua Event → events.index
  - Buat Baru → events.create
  - Perlu Approval → events.index (dengan filter)

== 3. LIVEWIRE: Events/Index.php ==

Properties:
- $filterStatus = ''
- $filterJenis = ''
- $filterDapil = ''
- $search = ''
- $showDeleteConfirm = false
- $deleteId = null

Computed:
- getSummaryProperty(): count per status (draft, menunggu, disetujui, berlangsung, selesai)
- getEventsProperty(): paginated query dengan filter, orderBy tanggal_mulai desc, eager load creator+approvals

View: resources/views/livewire/events/index.blade.php

Layout:
- Header: "Kegiatan / Event" + tombol "+ Buat Event Baru" (link ke events.create)
- Filter bar: Status (All/Draft/Menunggu/Disetujui/Ditolak/Berlangsung/Selesai), Jenis, Dapil, Search
- Summary strip: 5 angka inline (Draft X, Menunggu X, Disetujui X, Berlangsung X, Selesai X) — klikable untuk filter
- Tabel event:
  Kolom: Judul | Jenis | Tanggal | Lokasi | Status | Approval | Publik | Aksi
  - Status: badge pill warna
  - Approval: progress dots (3 dot: DPRa ✓/○, DPC ✓/○, DPD ✓/○)
  - Publik: toggle switch kecil (hanya aktif jika status = disetujui)
  - Aksi: Lihat, Edit, Hapus
- Pagination

== 4. LIVEWIRE: Events/Create.php ==

Properties — form fields:
$judul, $deskripsi, $jenis, $tanggalMulai, $tanggalSelesai, $lokasi, $lokasiDesa, $lokasiKecamatan, $lokasiDapil, $kapasitas, $isPublic, $penyelenggara, $picNama, $picHp, $coverImage
$fromKegiatanRwId = null (jika dari Sisir RW)

Mount:
- Cek query param ?from_kegiatan={id}
- Jika ada, load KegiatanRw → prefill: jenis, lokasi (desa+kecamatan+dapil), PIC (pelaksana), deskripsi (catatan)

Simpan:
- Create Event (status=draft)
- Jika fromKegiatanRwId, set kegiatan_rw_id
- Create 3 EventApproval (dpra pending, dpc pending, dpd pending)
- Redirect ke events.detail

View: resources/views/livewire/events/create.blade.php

Form layout (2 kolom):
- Kiri: judul, deskripsi (textarea), jenis (select), penyelenggara, PIC nama + HP
- Kanan: tanggal mulai, tanggal selesai, lokasi (text), desa+kecamatan+dapil (selects), kapasitas, cover image (upload)
- Checkbox "Tampilkan di website publik" (is_public)
- Jika dari Sisir RW: badge info "Dibuat dari kegiatan Sisir RW — {desa} RW {rw} — {tanggal}"
- Tombol: "Simpan Draft" + "Simpan & Ajukan Approval"

Method ajukanApproval(): set status = menunggu_approval, level_approval = dpra

== 5. LIVEWIRE: Events/Detail.php ==

Route model binding: public Event $event

View: resources/views/livewire/events/detail.blade.php

Layout:
- Breadcrumb: Event > {judul}
- Header: judul, badge status, badge jenis, tanggal, lokasi
- Info card: penyelenggara, PIC, kapasitas, publik/internal
- Jika dari Sisir RW: link "Lihat kegiatan asal"

Section: Approval Tracker
- 3 step horizontal: DPRa → DPC → DPD
- Setiap step: dot status (pending=abu, approved=hijau, rejected=merah), nama approver, tanggal, catatan
- Jika user punya role yang sesuai level saat ini: tampilkan tombol "Setujui" / "Tolak" + textarea catatan

```html
<div class="flex items-center gap-3">
  @foreach(['dpra' => 'DPRa', 'dpc' => 'DPC', 'dpd' => 'DPD'] as $level => $label)
    @php
      $approval = $event->approvals->where('level', $level)->first();
      $isCurrent = $event->level_approval === $level && $event->status === 'menunggu_approval';
    @endphp
    <div class="flex-1 text-center">
      <div class="w-10 h-10 rounded-full mx-auto flex items-center justify-center
        {{ $approval && $approval->status === 'approved' ? 'bg-green-500 text-white' :
           ($approval && $approval->status === 'rejected' ? 'bg-red-500 text-white' :
           ($isCurrent ? 'bg-orange-500 text-white animate-pulse' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-400')) }}">
        @if($approval && $approval->status === 'approved') ✓
        @elseif($approval && $approval->status === 'rejected') ✕
        @elseif($isCurrent) ●
        @else ○ @endif
      </div>
      <div class="text-xs font-medium mt-1">{{ $label }}</div>
      @if($approval && $approval->decided_at)
        <div class="text-[10px] text-zinc-400">{{ $approval->decided_at->format('d M Y') }}</div>
      @endif
    </div>
    @if(!$loop->last)
      <div class="flex-shrink-0 w-12 h-px bg-zinc-300 dark:bg-zinc-600"></div>
    @endif
  @endforeach
</div>
```

Method approve($level):
- Validate: user role harus sesuai level
- Update EventApproval: status=approved, approver_id, decided_at
- Advance level_approval ke level berikutnya
- Jika semua approved: event status = disetujui
- Flash message

Method reject($level, $catatan):
- Update EventApproval: status=rejected
- Event status = ditolak
- Flash message

Section: RAB (Rancangan Anggaran)
- Tabel budget items: Item, Kategori, Qty, Satuan, Harga, Subtotal
- Tombol "+ Tambah Item"
- Total di bawah tabel
- Form inline untuk tambah/edit item

Section: Laporan Kegiatan (muncul setelah event selesai)
- Jika belum ada: tombol "Buat Laporan"
- Form: ringkasan, peserta hadir, evaluasi, tindak lanjut, upload foto, realisasi anggaran
- Jika sudah ada: tampilkan laporan + foto galeri

Section: Pendaftaran Peserta (jika is_public)
- Tabel: Nama, Email, Status (registered/confirmed/attended), Tanggal daftar
- Statistik: total daftar, confirmed, hadir

== 6. LIVEWIRE: Events/Edit.php ==

Sama seperti Create, tapi load data existing. Hanya bisa edit jika status = draft atau ditolak (bisa revisi lalu ajukan ulang).

Langsung buat semua file. Jangan test.
```

---

## PROMPT 3: Integrasi Sisir RW → Event

```
Tambahkan tombol "Jadikan Event" di modul Sisir RW. Langsung kerjakan, JANGAN test.

== 1. UPDATE Sisir RW view ==

Di resources/views/livewire/bedah-dapil/sisir-rw.blade.php, pada setiap item timeline kegiatan, tambahkan tombol "Jadikan Event":

```html
{{-- Di setiap kegiatan di timeline --}}
<div class="flex gap-2 mt-2">
    <button wire:click="editKegiatan('{{ $kegiatan->id }}')" class="text-[10px] text-zinc-500 underline">Edit</button>
    @if(!$kegiatan->event_id_linked)
        <a href="{{ route('events.create', ['from_kegiatan' => $kegiatan->id]) }}"
           class="text-[10px] text-orange-600 font-medium flex items-center gap-1 hover:underline">
            <i class="ti ti-calendar-plus" style="font-size:11px;" aria-hidden="true"></i>
            Jadikan Event
        </a>
    @else
        <a href="{{ route('events.detail', $kegiatan->event_id_linked) }}"
           class="text-[10px] text-blue-600 flex items-center gap-1 hover:underline">
            <i class="ti ti-external-link" style="font-size:11px;" aria-hidden="true"></i>
            Lihat Event
        </a>
    @endif
</div>
```

== 2. UPDATE KegiatanRw model ==

Tambahkan relationship dan accessor:
```php
public function event()
{
    return $this->hasOne(Event::class, 'kegiatan_rw_id');
}

public function getEventIdLinkedAttribute()
{
    return $this->event?->id;
}
```

== 3. UPDATE form Sisir RW ==

Di drawer form catat kegiatan, tambahkan checkbox di bawah:
```html
<div class="mt-2">
    <label class="flex items-center gap-2 text-xs text-zinc-500 cursor-pointer">
        <input wire:model="formJadikanEvent" type="checkbox" class="rounded border-zinc-300">
        Langsung jadikan event setelah simpan
    </label>
</div>
```

Di SisirRw.php, tambahkan property:
```php
public $formJadikanEvent = false;
```

Update simpanKegiatan(): setelah create KegiatanRw, jika formJadikanEvent == true:
```php
if ($this->formJadikanEvent) {
    return redirect()->route('events.create', ['from_kegiatan' => $kegiatan->id]);
}
```

== 4. UPDATE form Sisir RW — tambah checkbox galeri ==

```html
<label class="flex items-center gap-2 text-xs text-zinc-500 cursor-pointer">
    <input wire:model="formTampilGaleri" type="checkbox" class="rounded border-zinc-300">
    Tampilkan foto di galeri website publik
</label>
```

Tambahkan kolom ke kegiatan_rws jika belum ada:
Migration: add_galeri_flag_to_kegiatan_rws
```php
$table->boolean('tampil_galeri')->default(false);
```

Langsung kerjakan. Jangan test.
```

---

## PROMPT 4: Dashboard Event + Summary di Sidebar

```
Perbaiki halaman index event dan tambahkan summary yang berguna. Langsung kerjakan, JANGAN test.

== 1. SUMMARY CARDS di Event Index ==

Di atas tabel event, tambahkan summary strip:
```html
<div class="flex gap-2 mb-4">
    @php
        $counts = [
            'draft' => ['label' => 'Draft', 'color' => '#888', 'count' => $summary['draft']],
            'menunggu_approval' => ['label' => 'Menunggu', 'color' => '#d97706', 'count' => $summary['menunggu']],
            'disetujui' => ['label' => 'Disetujui', 'color' => '#16a34a', 'count' => $summary['disetujui']],
            'berlangsung' => ['label' => 'Berlangsung', 'color' => '#2563eb', 'count' => $summary['berlangsung']],
            'selesai' => ['label' => 'Selesai', 'color' => '#16a34a', 'count' => $summary['selesai']],
        ];
    @endphp
    @foreach($counts as $status => $cfg)
        <button wire:click="$set('filterStatus', '{{ $filterStatus === $status ? '' : $status }}')"
            class="flex-1 rounded-lg p-3 text-center border transition-colors
            {{ $filterStatus === $status ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/20' : 'border-zinc-200 dark:border-zinc-700' }}">
            <div class="text-lg font-medium" style="color:{{ $cfg['color'] }};">{{ $cfg['count'] }}</div>
            <div class="text-xs text-zinc-500">{{ $cfg['label'] }}</div>
        </button>
    @endforeach
</div>
```

== 2. EVENT CARDS (alternatif view, selain tabel) ==

Tambahkan toggle view: Tabel / Cards. Pada mode Cards:

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
    @foreach($this->events as $event)
        @php $cfg = $event->status_config; @endphp
        <a href="{{ route('events.detail', $event) }}" wire:navigate
           class="block rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors">
            {{-- Cover --}}
            <div class="h-28 flex items-center justify-center"
                 style="background:linear-gradient(135deg, {{ $cfg['color'] }}20, {{ $cfg['color'] }}40);">
                @if($event->cover_image)
                    <img src="{{ Storage::url($event->cover_image) }}" class="w-full h-full object-cover" alt="">
                @else
                    <i class="ti ti-calendar-event" style="font-size:28px;color:{{ $cfg['color'] }};opacity:0.5;" aria-hidden="true"></i>
                @endif
                {{-- Badges overlay --}}
                <div class="absolute top-2 left-2 flex gap-1">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">{{ $cfg['label'] }}</span>
                    @if($event->is_public)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">Publik</span>
                    @endif
                </div>
            </div>
            {{-- Info --}}
            <div class="p-3">
                <div class="text-xs text-zinc-400 flex items-center gap-1">
                    <i class="ti ti-calendar" style="font-size:11px;" aria-hidden="true"></i>
                    {{ $event->tanggal_mulai->format('d M Y, H:i') }}
                </div>
                <div class="text-sm font-medium mt-1 line-clamp-2">{{ $event->judul }}</div>
                <div class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                    <i class="ti ti-map-pin" style="font-size:11px;" aria-hidden="true"></i>
                    {{ $event->lokasi_desa ?? $event->lokasi }}
                </div>
                {{-- Approval dots --}}
                <div class="flex items-center gap-1 mt-2">
                    @foreach(['dpra', 'dpc', 'dpd'] as $level)
                        @php $appr = $event->approvals->where('level', $level)->first(); @endphp
                        <div class="w-2 h-2 rounded-full
                            {{ $appr && $appr->status === 'approved' ? 'bg-green-500' :
                               ($appr && $appr->status === 'rejected' ? 'bg-red-500' : 'bg-zinc-300') }}"></div>
                    @endforeach
                    <span class="text-[10px] text-zinc-400 ml-1">
                        {{ $event->approvals->where('status', 'approved')->count() }}/3 approved
                    </span>
                </div>
            </div>
        </a>
    @endforeach
</div>
```

== 3. DASHBOARD HOME — Tambah widget event ==

Di resources/views/dashboard.blade.php, tambahkan section setelah quick access:

```html
{{-- Upcoming Events --}}
@php
    $upcomingEvents = \App\Models\Event::where('status', 'disetujui')
        ->where('tanggal_mulai', '>=', now())
        ->orderBy('tanggal_mulai')
        ->limit(3)->get();
    $pendingApproval = \App\Models\Event::where('status', 'menunggu_approval')->count();
@endphp

@if($upcomingEvents->isNotEmpty() || $pendingApproval > 0)
<div class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-medium">Event mendatang</h2>
        <a href="{{ route('events.index') }}" wire:navigate class="text-xs text-orange-600 font-medium">Lihat semua →</a>
    </div>
    @if($pendingApproval > 0)
        <div class="mb-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 flex items-center gap-2">
            <i class="ti ti-alert-circle" style="font-size:16px;color:#d97706;" aria-hidden="true"></i>
            <span class="text-xs text-amber-700 dark:text-amber-400">{{ $pendingApproval }} event menunggu approval</span>
            <a href="{{ route('events.index', ['status' => 'menunggu_approval']) }}" class="ml-auto text-xs text-orange-600 font-medium">Review →</a>
        </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @foreach($upcomingEvents as $event)
            {{-- mini event card --}}
        @endforeach
    </div>
</div>
@endif
```

== 4. SIDEBAR BADGE — count pending approval ==

Di sidebar.blade.php, pada menu "Kegiatan / Event", tambahkan badge count pending:

```html
@php $pendingCount = \App\Models\Event::where('status', 'menunggu_approval')->count(); @endphp
<span class="flex-1">Kegiatan / Event</span>
@if($pendingCount > 0)
    <span style="font-size:9px;padding:2px 6px;border-radius:99px;background:#d9770630;color:#d97706;font-weight:500;">{{ $pendingCount }}</span>
@endif
```

Langsung kerjakan. Jangan test.
```

---

## PROMPT 5: Fix & Polish

```
Cek dan fix modul Event. Langsung fix, JANGAN test.

CHECKLIST:

1. Halaman /events load tanpa error
2. Summary strip angka muncul (awal semua 0, normal)
3. Tombol "Buat Event Baru" → halaman create load
4. Form create: isi semua field → simpan draft → redirect ke detail
5. Halaman detail: info event tampil lengkap
6. Approval tracker: 3 step (DPRa, DPC, DPD) tampil sebagai dots
7. Tombol "Ajukan Approval" di detail → status berubah ke menunggu_approval
8. Tombol "Setujui" di approval → advance ke level berikutnya
9. Setelah semua 3 level approved → status = disetujui
10. Tombol "Tolak" → status = ditolak, bisa revisi dan ajukan ulang
11. RAB: bisa tambah, edit, hapus budget item
12. Laporan: form muncul setelah event selesai
13. Toggle publik: hanya bisa diaktifkan jika status = disetujui
14. Tabel event: filter status, jenis, search berfungsi
15. Dari Sisir RW: "Jadikan Event" → create form pre-filled dari data kegiatan
16. Sidebar: menu event aktif, badge pending approval muncul
17. Dashboard: widget event mendatang muncul

FIX APPROVAL LOGIC:
- User dengan role 'admin' bisa approve semua level
- User dengan role 'dpd' bisa approve level dpd
- User dengan role 'dapil' bisa approve level dpc
- User dengan role 'kecamatan' bisa approve level dpra
- Jika user tidak berhak approve level saat ini, sembunyikan tombol

FIX VISUAL:
- Card event dan tabel harus match design system (accent #fe5000)
- Badge status warna sesuai STATUS_CONFIG
- Approval dots: hijau (approved), merah (rejected), orange pulse (current), abu (pending)

Langsung fix. Jangan test.
```
