# Debug Session: infra-rtrw-fixes

- Status: OPEN
- Symptom: Modul Infra RT/RW perlu dicek dan diperbaiki sesuai checklist runtime, data, navigasi, dan form Livewire.
- Scope: import target wilayah, route/sidebar, halaman index, halaman detail, CRUD KORWE/KORTE, agregasi progress.

## Hypotheses

1. Ada mismatch path view `infra-rtrw` vs `infra-rt-rw` yang membuat render view tidak konsisten.
2. Query agregasi summary/progress memakai alias atau basis query yang salah sehingga angka tidak akurat.
3. Form Livewire detail belum sinkron dengan tab aktif `korwe/korte`, sehingga CRUD menyasar model yang salah.
4. Route dan sidebar belum sepenuhnya konsisten untuk menu Infra RT/RW dan tombol `Input`.
5. Grouping tabel desa atau counts `withCount` tidak cocok dengan data import sehingga tabel terlihat kosong/aneh.

## Evidence Log

- `TargetWilayah::count()` = `217`, jadi impor data memenuhi syarat minimum.
- `Index@render`, `Index@summaryData`, `Index@dapilProgressData`, dan `Index@desaData` terekam normal pada `.dbg/trae-debug-log-infra-rtrw-fixes.ndjson`.
- `Detail@mount`, `Detail@render`, dan `Detail@summaryData` juga terekam normal untuk sampel desa `BOJONGMANGU`.
- Output runtime index menunjukkan milestone `KORTE 2028` bisa mencapai `102.0%`, sehingga tampilan persentase perlu dijaga agar tidak melewati 100%.
- Dari inspeksi kode, alur edit `simpan()` di detail memakai `updateOrCreate` berbasis nomor RW/RT, sehingga saat nomor diubah berisiko membuat row baru alih-alih mengedit row lama.

## Hypothesis Status

1. View path mismatch `infra-rtrw` vs `infra-rt-rw`:
   - Status: PARTIAL
   - Evidence: Runtime index tetap render, tetapi path file tidak konsisten dengan konvensi detail dan brief.
2. Agregasi summary/progress salah:
   - Status: PARTIAL
   - Evidence: Summary dan progress utama terhitung, tetapi persentase milestone bisa lebih dari 100%.
3. Form detail tidak sinkron dengan tab/model:
   - Status: CONFIRMED
   - Evidence: Jalur simpan edit masih berbasis `updateOrCreate` pada composite key, bukan `editId`.
4. Route/sidebar tidak konsisten:
   - Status: REJECTED
   - Evidence: Route `infra-rtrw.index` dan `infra-rtrw.detail` terdaftar, sidebar sudah mengarah ke route yang benar.
5. Grouping tabel/counter kosong:
   - Status: REJECTED
   - Evidence: `desaData` total `217` dan halaman detail sampel memuat data target dengan normal.

## Next Step

- Terapkan fix minimal pada path view index, tampilan persentase/growth, dan alur edit detail agar tidak menduplikasi data.
