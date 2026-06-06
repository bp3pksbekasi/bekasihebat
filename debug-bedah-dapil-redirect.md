# Debug Session: bedah-dapil-redirect

- Status: OPEN
- Symptom: `http://127.0.0.1:8000/bedah-dapil/pemilu-dprd` mengalami redirect loop / data tidak terbuka.
- Scope: routing, middleware auth/profile, session redirect chain, frontend data bootstrap.

## Hypotheses

1. Middleware `auth` mengarahkan request ke login karena sesi tidak dikenali.
2. Ada loop redirect antara login, dashboard, dan route bedah dapil.
3. Ada middleware atau komponen layout yang memaksa redirect karena profil belum lengkap.
4. Halaman berhasil dirender tetapi inisialisasi data frontend gagal.
5. Navigasi Livewire/Flux memicu request yang berbeda dari yang diuji via HTTP langsung.

## Evidence Log

- Belum ada evidence runtime baru pada sesi debug ini.

## Next Step

- Tambahkan instrumentation redirect/auth yang minimal.
