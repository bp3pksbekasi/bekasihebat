# PROMPT: Implementasi Fitur "Program Arahan" — Bekasi Hebat

## Konteks Aplikasi

Ini adalah aplikasi Laravel (Livewire + Filament) bernama Bekasi Hebat, platform manajemen kampanye/pemenangan pemilu legislatif. Sebelum mengerjakan apa pun, pelajari dulu dua modul referensi berikut di codebase karena fitur baru ini **wajib meniru pola yang sudah ada**, bukan membuat pola baru:

1. **Modul Event** (`app/Models/Event.php`, `EventApproval.php`, `EventBudgetItem.php`, `EventReport.php`, `app/Livewire/Events/Create.php`, `resources/views/livewire/events/form.blade.php`) — acuan untuk struktur form lengkap: wizard multi-langkah, RAB (rencana anggaran biaya), alur approval berjenjang (DPRa → DPC → DPD), dan laporan pelaksanaan pasca-kegiatan.
2. **Modul Buku Induk RW** (`app/Livewire/BukuIndukRw/Index.php`, `Detail.php`, `resources/views/livewire/buku-induk-rw/*`) — ini adalah halaman "Peta Kekuatan RW" yang sudah ada. `Index.php` menampilkan daftar semua RW dengan agregat target vs realisasi Korwe/Korte/Penggalang dan status kelengkapan Profil RW. `Detail.php` adalah halaman satu RW dengan 6 tab (`profil_wilayah`, `peta_politik`, `strategi`, `struktur`, `realisasi`, `rekomendasi_ai`).

Model-model wilayah yang relevan dan sudah ada: `TargetWilayah` (desa, dengan `STATUS_CONFIG` 5 kategori: SANGAT KUAT, KUAT, POTENSIAL, RAWAN, ZONA BERAT), `DataRw` (RW, punya kolom `status_wilayah` dan `target_wilayah_id`), `Korwe`, `Korte`, `PenggalangSuara`, `ProfilRw`.

## Tujuan Fitur

Bangun modul **Program Arahan**: program kerja yang secara eksplisit menyasar satu RW (atau satu desa), terikat pada jenis target infrastruktur pemenangan (pembentukan Korwe/Korte, rekrutmen Penggalang), otomatis mewarisi status prioritas wilayah, dan realisasinya dihitung otomatis dari data lapangan yang sudah ada — bukan diisi manual. Tiga kebutuhan bisnis yang harus terpenuhi:

1. Program harus inline/terhubung langsung dengan target besar infrastruktur (target Korwe/Korte/Penggalang di `TargetWilayah`).
2. Program dieksekusi dengan mempertimbangkan status prioritas RW (`DataRw->status_wilayah`) — bukan asal buat program di RW mana saja.
3. Dashboard bisa memonitor persentase pelaksanaan program, dikelompokkan per status prioritas wilayah, untuk mendeteksi apakah program salah sasaran (menumpuk di wilayah yang sudah kuat, bukan di wilayah yang butuh digenjot).

## Skema Database

Buat 4 migration baru, **strukturnya adalah copy dari pola Event** dengan penyesuaian field:

### 1. `program_arahans` (tabel utama)

```php
Schema::create('program_arahans', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('org_level'); // dpra | dpc | dpd — sama seperti Event
    $table->uuid('bidang_dpd_id')->nullable();
    $table->foreign('bidang_dpd_id')->references('id')->on('bidang_dpds')->nullOnDelete();
    $table->uuid('target_wilayah_id');
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->cascadeOnDelete();
    $table->string('nomor_rw')->nullable(); // null = program level desa, bukan per-RW
    $table->string('status_wilayah_snapshot')->nullable(); // disalin dari DataRw->status_wilayah saat dibuat, TIDAK live-join
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->string('jenis_program'); // pembentukan_korwe | pembentukan_korte | rekrutmen_penggalang | sisir_rw | penguatan_upa_rw | lainnya
    $table->integer('target_angka')->default(0);
    $table->string('satuan')->nullable();
    $table->dateTime('tanggal_mulai');
    $table->dateTime('tanggal_selesai')->nullable(); // deadline
    $table->string('penyelenggara')->nullable();
    $table->string('pic_nama')->nullable();
    $table->string('pic_hp')->nullable();
    $table->string('status')->default('belum_mulai'); // belum_mulai | berjalan | selesai | tertunda
    $table->string('level_approval')->default('dpra'); // dpra | dpc | dpd | selesai — sama seperti Event
    $table->string('funding_source')->nullable();
    $table->text('budget_notes')->nullable();
    $table->string('cover_image')->nullable();

    if (Schema::getColumnType('users', 'id') === 'bigint') {
        $table->unsignedBigInteger('created_by')->nullable();
    } else {
        $table->uuid('created_by')->nullable();
    }
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['target_wilayah_id', 'nomor_rw']);
    $table->index(['status_wilayah_snapshot']);
    $table->index(['status']);
    $table->index(['jenis_program']);
});
```

### 2. `program_arahan_approvals` — copy persis struktur `event_approvals`

```php
Schema::create('program_arahan_approvals', function (Blueprint $table) {
    $table->id();
    $table->uuid('program_arahan_id');
    $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
    $table->string('level'); // dpra | dpc | dpd
    $table->string('status')->default('pending'); // pending | approved | rejected
    $table->unsignedBigInteger('approver_id')->nullable();
    $table->text('catatan')->nullable();
    $table->dateTime('decided_at')->nullable();
    $table->timestamps();
});
```

### 3. `program_arahan_budget_items` — copy persis struktur `event_budget_items`

```php
Schema::create('program_arahan_budget_items', function (Blueprint $table) {
    $table->id();
    $table->uuid('program_arahan_id');
    $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
    $table->string('item');
    $table->string('kategori')->nullable();
    $table->integer('qty')->default(1);
    $table->string('satuan')->default('Pcs');
    $table->decimal('harga_satuan', 15, 2)->default(0);
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->text('keterangan')->nullable();
    $table->timestamps();
});
```

### 4. `program_arahan_reports` — adaptasi dari `event_reports`, field realisasi diganti ke metrik infrastruktur

```php
Schema::create('program_arahan_reports', function (Blueprint $table) {
    $table->id();
    $table->uuid('program_arahan_id');
    $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
    $table->text('ringkasan')->nullable();
    $table->integer('jumlah_korwe_terbentuk')->default(0);
    $table->integer('jumlah_korte_terbentuk')->default(0);
    $table->integer('jumlah_penggalang_terekrut')->default(0);
    $table->text('evaluasi')->nullable();
    $table->text('tindak_lanjut')->nullable();
    $table->json('foto')->nullable();
    $table->decimal('realisasi_anggaran', 15, 2)->nullable();
    $table->string('rating')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
});
```

### 5. `program_arahan_personel` — TABEL BARU, tidak ada padanan di Event, ini kunci integrasi ke infrastruktur

```php
Schema::create('program_arahan_personel', function (Blueprint $table) {
    $table->id();
    $table->uuid('program_arahan_id');
    $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
    $table->string('infra_type'); // korwe | korte | penggalang
    $table->uuid('infra_id'); // FK polimorfik manual ke Korwe.id / Korte.id / PenggalangSuara.id
    $table->timestamps();

    $table->unique(['program_arahan_id', 'infra_type', 'infra_id']);
});
```

## Model Eloquent

Buat `app/Models/ProgramArahan.php`, `ProgramArahanApproval.php`, `ProgramArahanBudgetItem.php`, `ProgramArahanReport.php`, `ProgramArahanPersonel.php`. Ikuti gaya penulisan model lain di codebase (`declare(strict_types=1)`, `HasUuids`, `HasFactory`).

`ProgramArahan` harus punya:

- Constant `STATUS_CONFIG` — copy persis dari `ProgramKerja::STATUS_CONFIG` (belum_mulai/berjalan/selesai/tertunda dengan warna masing-masing).
- Constant `JENIS_PROGRAM` — array label untuk `pembentukan_korwe`, `pembentukan_korte`, `rekrutmen_penggalang`, `sisir_rw`, `penguatan_upa_rw`, `lainnya`.
- Relasi: `targetWilayah()` (BelongsTo `TargetWilayah`), `bidang()` (BelongsTo `BidangDpd`), `approvals()` (HasMany), `budgetItems()` (HasMany), `report()` (HasOne), `personel()` (HasMany `ProgramArahanPersonel`), `creator()` (BelongsTo `User`).
- Accessor `getRealisasiAttribute()`: hitung `$this->personel()->count()` — **ini adalah realisasi otomatis, jangan buat kolom realisasi manual di tabel utama.**
- Accessor `getProgressPctAttribute()`: `target_angka > 0 ? round((realisasi / target_angka) * 100) : 0`, clamp maksimum 100.
- Accessor `getDataRwAttribute()`: lookup `DataRw` berdasarkan `target_wilayah_id` + `nomor_rw` (untuk menampilkan status wilayah *saat ini*, dibandingkan dengan `status_wilayah_snapshot` saat program dibuat — kedua nilai ini sengaja ditampilkan berdampingan di UI kalau berbeda).

## Livewire Components

### 1. `app/Livewire/ProgramArahan/Create.php` — halaman penuh, wizard 4 langkah

Contoh dan strukturnya **copy dari `app/Livewire/Events/Create.php`**, sesuaikan field. Wizard 4 langkah (state `currentStep` integer 1-4):

**Langkah 1 — Wilayah & Jenis Program**
- Select cascading: Dapil → Kecamatan → Desa (pola sama seperti `dapilOptions()`/`kecamatanOptions()`/`desaOptions()` di Events\Create) → Nomor RW
- Begitu RW dipilih, tampilkan badge read-only status wilayah RW itu (dari `DataRw->status_wilayah`, warna dari `TargetWilayah::STATUS_CONFIG`) — ini bukan input, murni informasi
- Select `jenis_program`
- Input `target_angka` + `satuan`

**Langkah 2 — Informasi Program**
- `judul`, `deskripsi` (textarea)
- `tanggal_mulai`, `tanggal_selesai`
- `penyelenggara`, `pic_nama`, `pic_hp`
- (opsional, ikut pola Event) `bidang_dpd_id`

**Langkah 3 — Anggaran (opsional)**
- Array `budgetItems` dengan `addBudgetItem()`/`removeBudgetItem()`/`totalBudget()` — **copy persis logic dari `Events\Create`**
- `funding_source`, `budget_notes`

**Langkah 4 — Review & Approval**
- Ringkasan seluruh input dari langkah 1-3
- Tampilkan preview alur approval yang akan dibuat (dpra → dpc → dpd, pending) — bukan interaktif di sini, cuma preview
- Dua tombol: `simpanDraft()` (status = `belum_mulai`, tidak membuat baris approval) dan `simpanDanAjukan()` (status mengikuti logic `persist()` di Events\Create: kalau org_level dpd dan ada bidang → `berjalan` menunggu approval, selain itu langsung `berjalan`)

Method `persist()` **wajib menyalin `status_wilayah_snapshot`** dari `DataRw` yang cocok dengan `target_wilayah_id` + `nomor_rw` pada saat program disimpan.

### 2. `app/Livewire/ProgramArahan/Report.php` — form laporan pelaksanaan (dipanggil dari Detail RW atau dari halaman program)

Input: `ringkasan`, `jumlah_korwe_terbentuk`, `jumlah_korte_terbentuk`, `jumlah_penggalang_terekrut`, `evaluasi`, `tindak_lanjut`, upload foto, `realisasi_anggaran`, `rating`. Saat submit: buat `ProgramArahanReport`, lalu update `status` program jadi `selesai` jika laporan lengkap.

### 3. Perluasan `app/Livewire/BukuIndukRw/Detail.php`

- Tambah `'program_arahan'` ke daftar valid di `setActiveTab()`
- Tambah computed property `getProgramArahanListProperty()`: `ProgramArahan::where('target_wilayah_id', $this->dataRw->target_wilayah_id)->where(function($q){ $q->where('nomor_rw', $this->profilRwId)->orWhereNull('nomor_rw'); })->get()`
- Tampilkan sebagai list ringkas (judul, jenis_program, status badge, progress bar realisasi/target) — **jangan taruh form lengkap di sini**, cukup tombol "+ Buat Program Baru" yang redirect ke route `program-arahan.create` dengan query string prefill (`target_wilayah_id`, `nomor_rw`)

### 4. Perluasan `app/Livewire/BukuIndukRw/Index.php`

Tambahkan ke array `$summary` yang sudah ada di `render()`:

```php
$programStats = \App\Models\ProgramArahan::query()
    ->whereIn('target_wilayah_id', $uniqueWilayahs->pluck('id'))
    ->selectRaw('status_wilayah_snapshot, status, COUNT(*) as total')
    ->groupBy('status_wilayah_snapshot', 'status')
    ->get()
    ->groupBy('status_wilayah_snapshot')
    ->map(function ($rows) {
        $total = $rows->sum('total');
        $selesai = $rows->where('status', 'selesai')->sum('total');
        return [
            'total' => $total,
            'selesai' => $selesai,
            'pct' => $total > 0 ? (int) round(($selesai / $total) * 100) : 0,
        ];
    });

$summary['program_per_status'] = $programStats;
```

Di view, render sebagai 5 baris horizontal bar (satu per `TargetWilayah::STATUS_CONFIG`), pakai warna `bg`/`text` yang sama persis dengan yang sudah dipakai untuk badge status RW di tabel. **Tambahkan highlight visual (mis. ikon peringatan) kalau `pct` di RAWAN atau POTENSIAL lebih rendah daripada di SANGAT KUAT/KUAT** — itu sinyal program salah sasaran.

## Routes

Tambahkan di `routes/web.php`, dekat grup route `infra-rtrw` dan `buku-induk-rw`:

```php
Route::prefix('program-arahan')->middleware(['auth'])->group(function () {
    Route::get('/create', \App\Livewire\ProgramArahan\Create::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.create');
    Route::get('/{programArahan}/laporan', \App\Livewire\ProgramArahan\Report::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.report');
});
```

## Aturan Bisnis Penting (Jangan Dilewatkan)

1. **Realisasi tidak pernah diinput manual di form.** Progress program selalu dihitung dari `ProgramArahanPersonel` (jumlah Korwe/Korte/Penggalang yang ditandai sebagai hasil program ini) dibandingkan `target_angka`.
2. **`status_wilayah_snapshot` disalin sekali saat program dibuat, tidak pernah di-update otomatis** — histori pelaporan tidak boleh berubah walau status RW itu berubah belakangan. Kalau butuh perbandingan "status sekarang vs status saat program dibuat", tampilkan dua badge berdampingan di UI, jangan menimpa snapshot.
3. **Alur approval reuse 100% dari pola Event** — jangan desain ulang, tinggal ganti nama tabel.
4. **Form lengkap (wizard) selalu halaman terpisah**, tab di Detail RW hanya menampilkan ringkasan + tombol.
5. **Anggaran (RAB) opsional** — banyak program (terutama pembentukan Korwe/Korte yang murni koordinasi) tidak butuh biaya, jangan wajibkan minimal 1 item.

## Definition of Done

- [ ] 5 migration baru berhasil dijalankan tanpa error di database yang sudah berisi data (test dengan `php artisan migrate`)
- [ ] Wizard 4 langkah di `/program-arahan/create` bisa menyimpan draft maupun mengajukan approval, dengan validasi tiap langkah
- [ ] Badge status wilayah di Langkah 1 muncul otomatis dan benar begitu RW dipilih
- [ ] Tab "Program Arahan" muncul di Detail RW, menampilkan daftar program di RW tersebut dengan progress bar realisasi yang benar
- [ ] Menandai Korwe/Korte/Penggalang sebagai "hasil dari program X" langsung menaikkan angka realisasi program itu tanpa refresh manual data lain
- [ ] `$summary['program_per_status']` di halaman Peta Kekuatan RW menampilkan breakdown 5 status dengan warna yang konsisten dengan badge status RW yang sudah ada di tabel
- [ ] Approval chain (dpra/dpc/dpd) berjalan dengan logic yang sama persis seperti modul Event
