# Prompt Bertahap Trae — Website Publik Bekasi Hebat (V2)

Website publik untuk warga umum. Layout terpisah dari dashboard admin (navbar, bukan sidebar). Data dari database yang sama. Referensi desain: file website-bekasi-hebat.html.

---

## PROMPT 1: Layout, Route, Homepage

```
Buat website publik Bekasi Hebat. Layout terpisah dari dashboard admin. Langsung buat, JANGAN test, JANGAN tanya.

== 1. LAYOUT PUBLIK ==

Buat layout baru: resources/views/components/layouts/public.blade.php

Layout ini TIDAK pakai sidebar. Pakai navbar + footer. Untuk halaman publik (beranda, event, berita, galeri, register).

HEADER:
- Sticky, bg-white, border-bottom 3px orange
- Brand: icon orange hexagon + "DPD PKS Kabupaten Bekasi" (sub) + "Bekasi Hebat" (main)
- Nav links: Beranda, Event, Berita, Galeri, Tentang
- CTA button: "Daftar" (orange pill, icon user-plus)
- Mobile: hamburger toggle

FOOTER:
- Dark (zinc-900), 4 kolom: branding+alamat, menu, program, lainnya
- Footer socials: IG, TikTok, YT, FB, WA
- Bottom: copyright + orange strip
- Link "Login Dashboard" → /login

== 2. ROUTES ==

Di routes/web.php, TANPA auth:

Route::get('/', App\Livewire\PublicSite\Home::class)->name('public.home');
Route::get('/events', App\Livewire\PublicSite\Events::class)->name('public.events');
Route::get('/events/{slug}', App\Livewire\PublicSite\EventDetail::class)->name('public.events.show');
Route::get('/berita', App\Livewire\PublicSite\Berita::class)->name('public.berita');
Route::get('/galeri', App\Livewire\PublicSite\Galeri::class)->name('public.galeri');
Route::get('/tentang', App\Livewire\PublicSite\Tentang::class)->name('public.tentang');
Route::get('/register', App\Livewire\KartuAnggota\Register::class)->name('member.register');
Route::get('/ref/{code}', App\Livewire\KartuAnggota\Register::class)->name('member.register.referral');

PENTING: Route admin (/dashboard, /infra-rtrw, dll) harus TIDAK bentrok. Jika homepage sekarang sudah di '/', pindahkan admin routes ke /admin/* prefix atau pastikan tidak konflik.

== 3. HOMEPAGE LIVEWIRE ==

File: app/Livewire/PublicSite/Home.php

Computed properties (data dari database):

getEventMendatangProperty():
- Event::where('is_public', true)->where('status', 'disetujui')->where('tanggal_mulai', '>=', now())
- withCount('registrations'), limit(3), orderBy tanggal_mulai asc

getStatsProperty():
- dapil: 7 (hardcoded)
- kecamatan: TargetWilayah::distinct()->count('kecamatan')
- desa: TargetWilayah::count()
- rw: DataRw::count()
- event_total: Event::where('status', 'selesai')->count()
- warga_terjangkau: KontakWarga::aktif()->count()
- kader_aktif: Kader::aktif()->count()
- titik_senam: TitikSenam::aktif()->count()
- member: Member::aktif()->count()

getProgramProperty(): array 4 program (RKI, KSN, Sapa Warga, Kaderisasi) dengan angka dari DB

getDprdProperty(): AnggotaDewan::aktif()->orderBy('dapil')->limit(4)->get()

Layout: components.layouts.public

== 4. VIEW HOMEPAGE ==

File: resources/views/livewire/public-site/home.blade.php

Ikuti desain dari website-bekasi-hebat.html. Convert ke Tailwind CSS + Blade.

SECTIONS (urutan dari atas):

1. HERO
- Background: dark blue gradient (bg-gradient-to-br from-blue-900 to-slate-900)
- Dot pattern overlay (CSS background-image SVG)
- Badge: "Menuju Pemilu 2029" (orange/transparent pill)
- H1: "Kabupaten <span class="text-orange-600">Bekasi Hebat</span>"
- Paragraf deskripsi
- 2 tombol: "Bergabung Sekarang" (orange) + "Lihat Program" (outline white)
- Counter stats: 4 angka dari $this->stats (dapil, kecamatan, desa, rw)
- Counter animasi pakai Alpine.js x-intersect atau JavaScript IntersectionObserver

2. EVENT MENDATANG
- Section header: tag "Segera Hadir" + h2 + subtitle
- Grid 3 kolom: card per event
- Card: image placeholder area + date badge (hari/bulan) + kategori tag + judul + meta (waktu, lokasi, pendaftar) + tombol "Daftar Sekarang" (link ke /events/{slug})
- Jika $this->eventMendatang kosong → "Belum ada event mendatang"

3. PROGRAM UNGGULAN
- Background: white
- Section header: tag "Program Kami" + h2
- Grid 4 kolom: card per program
- Card: icon besar (warna masing-masing) + nama + deskripsi + angka target

4. BERITA (placeholder)
- Tampilkan layout 2 kolom: featured besar + 5 list samping
- Untuk sekarang isi dengan data placeholder/statis
- Nanti akan di-connect ke tabel berita jika ada

5. ANGGOTA DPRD
- Background: white
- Grid 4 kolom: card per anggota dewan
- Card: foto placeholder (gray, user icon) + nama + jabatan + dapil badge + social links
- Data dari $this->dprd

6. STATISTIK
- Background: dark blue gradient (sama dengan hero)
- 5 counter: event total, warga terjangkau, kader aktif, titik senam, member
- Counter animasi (angka naik dari 0)

7. GALERI (placeholder)
- Grid 4 kolom, item pertama span 2x2
- Placeholder gray boxes
- Nanti connect ke foto dari modul-modul

8. CTA DAFTAR
- Background: orange
- H2 "Bergabung Bersama Kami"
- Tombol "Daftar & Dapatkan Kartu Anggota" → /register

ANIMASI:
- Semua section pakai fade-up animation (opacity + translateY)
- Trigger via IntersectionObserver
- Counter pakai easing animation (cubic-bezier)

Langsung buat semua. Jangan test.
```

---

## PROMPT 2: Event Listing + Detail + Register

```
Buat halaman event listing dan detail. Langsung buat, JANGAN test.

== File: app/Livewire/PublicSite/Events.php ==

Halaman /events — listing event publik.

Properties: $filter = 'mendatang' (mendatang, semua, selesai)

getEventsProperty():
- Event::where('is_public', true)->where('status', 'disetujui')
- Filter by tanggal
- withCount('registrations')
- Paginate 9

VIEW: 
- Tab filter: Mendatang / Semua / Selesai
- Grid 3 kolom card (sama design homepage)
- Paginated

== File: app/Livewire/PublicSite/EventDetail.php ==

Halaman /events/{slug} — detail + registrasi.

Properties:
- $event (from slug, must be is_public + disetujui)
- Form: $regNama, $regHp, $regEmail, $regDapil, $regDesa, $regRw
- $registered = false

mount($slug): find event by slug, 404 jika tidak public/disetujui

register():
1. Validate nama + HP
2. Cek duplikat (HP di event sama)
3. Create EventRegistration
4. Auto-create Member jika belum ada (by no_hp)
5. Set $registered = true
6. Flash success

VIEW:
- Header event: judul besar, tanggal, lokasi, kategori badge
- Body 2 kolom:
  - Kiri (8fr): deskripsi lengkap, jadwal, info tambahan
  - Kanan (4fr): card registrasi
    - Jika belum register: form (nama, HP, email, dapil, desa, RW) + tombol "Daftar"
    - Jika sudah register: "Terima kasih! Anda terdaftar." + info kartu anggota
- Counter pendaftar (realtime)
- Share: tombol WA (wa.me link), copy link

== File: app/Livewire/PublicSite/Tentang.php ==

Halaman /tentang — statis.
- Tentang DPD PKS Kabupaten Bekasi
- Visi misi
- Cakupan wilayah (7 dapil, 23 kecamatan, 187 desa)
- Kontak

Langsung buat. Jangan test.
```

---

## PROMPT 3: Fix & Integrasi

```
Fix website publik dan integrasi dengan dashboard. Langsung fix, JANGAN test.

CHECKLIST:

LAYOUT:
1. Layout public load tanpa error (navbar + footer)
2. Semua link di navbar berfungsi
3. Mobile responsive (hamburger, grid collapse)
4. Footer link "Login Dashboard" → /login

HOMEPAGE:
5. Hero tampil, counter animasi berfungsi
6. Event dari DB (is_public + disetujui + tanggal >= now)
7. Program 4 card dengan angka dari DB
8. DPRD 4 card dari anggota_dewans
9. Stats counter animasi trigger saat scroll
10. CTA → /register
11. Fade-up animation

EVENT:
12. /events listing paginated, filter berfungsi
13. /events/{slug} detail tampil
14. Form registrasi: nama + HP required
15. Duplikat HP check per event
16. Auto-create member setelah register
17. Counter pendaftar update setelah register
18. Share WA button berfungsi
19. Event yang tidak public atau belum disetujui → 404

ROUTE:
20. Homepage (/) bisa diakses tanpa login
21. /events, /events/{slug} tanpa login
22. /register, /ref/{code} tanpa login
23. /dashboard tetap butuh login (tidak bentrok)
24. /tentang load

INTEGRASI DASHBOARD → WEBSITE:
25. Admin buat event + set is_public=true + approve → otomatis muncul di website
26. Admin batalkan event → hilang dari website
27. Registrasi dari website → masuk event_registrations (terlihat di dashboard admin)
28. Member baru dari register/event → masuk members (terlihat di Kartu Anggota admin)
29. Stats di homepage = data realtime dari database yang sama dengan dashboard

Langsung fix. Jangan test.
```
