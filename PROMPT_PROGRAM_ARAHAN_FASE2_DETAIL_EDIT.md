# PROMPT (Lanjutan/Fase 2): Detail & Edit Program Arahan — Bekasi Hebat

## Konteks

Fase 1 (lihat `PROMPT_PROGRAM_ARAHAN.md`) sudah menghasilkan: migration, model, wizard `ProgramArahan\Create`, tab ringkas di `BukuIndukRw\Detail`, dan perluasan dashboard `BukuIndukRw\Index`. Kartu program di tab tersebut sekarang tampil dengan aksi "Buat Program" (baru) dan "Buat Laporan" (menutup program), tapi **belum ada jalur untuk melihat detail atau mengedit program yang sudah tersimpan**. Fase ini menambahkan itu.

Sebelum menulis kode, pelajari dua referensi berikut di codebase:

1. `app/Livewire/Events/Detail.php` + view-nya — halaman kerja penuh untuk satu Event: submit approval, approve/reject per level, kelola RAB, isi laporan, kelola peserta, semua dalam satu komponen.
2. `app/Livewire/Events/Edit.php` + `resources/views/livewire/events/form.blade.php` — reuse total form yang sama dengan Create, cuma `mount(Event $event)` mengisi properti dari data existing alih-alih default kosong.

**Catatan penting**: `Events\Edit` saat ini **tidak mengunci field apa pun** berdasarkan status — semua field bisa diedit bebas kapan saja, termasuk setelah approval diajukan. Untuk Program Arahan, kita akan membuat perilaku yang **lebih ketat dari Event** (lihat Aturan Bisnis di bawah) karena field seperti wilayah/jenis program/target angka punya konsekuensi ke approval berjenjang yang sudah berjalan.

## Yang Perlu Dibangun

### 1. `app/Livewire/ProgramArahan/Detail.php` — halaman kerja penuh

Route: `program-arahan.detail`, `/program-arahan/{programArahan}`.

Fungsinya, mengikuti pola `Events\Detail`:

- Tampilkan seluruh data program: wilayah (dapil/kecamatan/desa/RW), badge status wilayah **saat ini** vs `status_wilayah_snapshot` **saat program dibuat** berdampingan (kalau berbeda, beri highlight kecil — ini sinyal wilayah berubah kondisi sejak program direncanakan)
- Tampilkan `jenis_program`, `target_angka`/`satuan`, deskripsi, tanggal mulai/selesai, penyelenggara, PIC
- Progress bar realisasi (dari `getRealisasiAttribute()`) vs target
- Tab/section RAB: tampilkan `budgetItems`, total anggaran; kalau org_level butuh approval dan level saat ini sesuai, sediakan tombol tambah/edit/hapus item (reuse logic `editBudgetItem()`/`saveBudgetItem()`/`removeBudgetItem()` dari `Events\Detail`)
- Section approval: tampilkan status tiap level (dpra/dpc/dpd) dengan tombol `approve(level)`/`reject(level)` untuk user yang berwenang — copy persis logic dari `Events\Detail::approve()`/`reject()`
- Section **Personel** (baru, tidak ada di Event): daftar `ProgramArahanPersonel` yang tertaut, dengan aksi "Tautkan Korwe/Korte/Penggalang yang sudah ada di RW ini" (dropdown pilih dari data existing di RW tersebut) dan "Hapus tautan"
- Section laporan: kalau `ProgramArahanReport` sudah ada, tampilkan isinya; kalau belum, tombol ke `program-arahan.report`
- Tombol "Edit" di header, mengarah ke `program-arahan.edit` — **hanya muncul kalau status masih `belum_mulai`** (lihat Aturan Bisnis)

### 2. `app/Livewire/ProgramArahan/Edit.php` — reuse wizard Create

Route: `program-arahan.edit`, `/program-arahan/{programArahan}/edit`.

- Reuse view yang sama dengan `ProgramArahan\Create` (pola sama seperti `Events\Edit` yang reuse `livewire.events.form`), lewat `@include` dengan flag `$isEdit = true`
- `mount(ProgramArahan $programArahan)`: isi semua properti wizard dari data existing, termasuk `budgetItems` array dari relasi `budgetItems()`
- Method simpan memanggil `update()` bukan `create()`, tapi **field yang dikunci tidak boleh ikut ter-update** meskipun ada di request (validasi ulang di server, jangan cuma disabled di UI)

### 3. Aturan Bisnis: Penguncian Field

Ini yang membedakan dari Event (yang tidak mengunci apa pun). Terapkan begini:

| Status program | Field yang boleh diedit |
|---|---|
| `belum_mulai` (draft) | Semua field bebas |
| `berjalan` (approval sudah diajukan/berjalan) | Hanya: `deskripsi`, `tanggal_selesai`, `penyelenggara`, `pic_nama`, `pic_hp`, `budgetItems`, `funding_source`, `budget_notes`. **Terkunci**: `target_wilayah_id`, `nomor_rw`, `jenis_program`, `target_angka`, `org_level` |
| `selesai` / `tertunda` | Tidak ada tombol Edit sama sekali — kalau perlu koreksi, harus lewat catatan di laporan, bukan ubah data historis |

Implementasi:
- Di `ProgramArahan\Edit::mount()`, hitung `$this->fieldsLocked = $programArahan->status !== 'belum_mulai'`
- Di view form, field yang terkunci diberi atribut `disabled` **plus** tetap divalidasi ulang di method simpan dengan mengambil nilai lama dari database untuk field terkunci, bukan dari input — supaya tidak bisa dibobol lewat request langsung
- Di `Detail.php`, tombol "Edit" disembunyikan total kalau status `selesai`/`tertunda` (bukan cuma field yang dikunci — waktunya sudah lewat untuk edit apa pun)

### 4. Update Kartu di Tab Program Arahan (`BukuIndukRw\Detail`)

Di partial/blade yang me-render kartu program (yang sudah menampilkan badge status, jenis, judul, target, progres, tombol Buat Laporan):

- Judul program (`Konsolidasi dengan Dewan` pada contoh) dibungkus `<a>` ke route `program-arahan.detail`
- Tambahkan ikon pensil kecil di pojok kartu (pola sama seperti ikon edit biru yang sudah dipakai untuk baris Korwe/Korte di tab `struktur`) yang mengarah ke `program-arahan.edit` — **ikon ini disembunyikan kalau status `selesai`/`tertunda`**, sesuai Aturan Bisnis di atas

### 5. Routes

Tambahkan di `routes/web.php`, dalam grup `program-arahan` yang sudah dibuat di Fase 1:

```php
Route::prefix('program-arahan')->middleware(['auth'])->group(function () {
    Route::get('/create', \App\Livewire\ProgramArahan\Create::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.create');
    Route::get('/{programArahan}', \App\Livewire\ProgramArahan\Detail::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.detail');
    Route::get('/{programArahan}/edit', \App\Livewire\ProgramArahan\Edit::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.edit');
    Route::get('/{programArahan}/laporan', \App\Livewire\ProgramArahan\Report::class)->middleware('menu:peta-kekuatan-rw')->name('program-arahan.report');
});
```

Pastikan `ProgramArahan` menggunakan route model binding UUID (tambahkan `uniqueIds()` dan `getRouteKeyName()` di model, persis pola yang dipakai `Event`).

## Definition of Done

- [ ] Dari kartu program di tab Program Arahan, klik judul membuka halaman detail lengkap (wilayah, info, RAB, approval, personel, laporan)
- [ ] Tombol edit hanya muncul kalau status `belum_mulai`
- [ ] Program berstatus `berjalan` masih bisa dibuka lewat Edit tapi field wilayah/jenis/target angka tampil terkunci (disabled) dan tetap tidak berubah walau di-submit paksa lewat request lain
- [ ] Program berstatus `selesai`/`tertunda` tidak menampilkan tombol Edit sama sekali
- [ ] Approve/reject per level approval berfungsi dari halaman Detail, konsisten dengan cara kerja approval Event
- [ ] Menautkan/melepas Korwe/Korte/Penggalang sebagai personel dari halaman Detail langsung memperbarui angka realisasi & progress bar
