# Prompt Bertahap Trae — Redesign UI Bedah Dapil

Jalankan berurutan. Tunggu selesai per tahap.

**PATH PETA:**
- Peta Kabupaten Bekasi: `storage/peta/KABUPATEN BEKASI.png`
- Peta per Dapil: `storage/peta/1. PETA PER DAPIL/` (identifikasi dari nama file)
- Peta per Kecamatan: `storage/peta/2. PETA PER KECAMATAN/` (identifikasi dari nama file)

**DATA CSV:** `storage/app/private/import/`

---

## PROMPT 1: Publish Peta + Restructure Route

```
Modul Bedah Dapil sudah jalan tapi UI perlu di-redesign total. Langsung kerjakan, JANGAN test, JANGAN tanya.

TUGAS 1 — Publish file peta ke public agar bisa diakses browser:

Buat/update artisan command app/Console/Commands/PublishPemiluData.php:
Signature: pemilu:publish

Logic:
1. Copy semua .csv dari storage/app/private/import/ ke public/data/pemilu/
2. Copy peta kabupaten: storage/peta/KABUPATEN BEKASI.png → public/images/peta/kabupaten-bekasi.png
3. Scan folder storage/peta/1. PETA PER DAPIL/ → untuk setiap file .png:
   - Baca nama file, identifikasi nomor dapil (misal "Dapil 1.png" atau "DAPIL_1.png" dll)
   - Copy ke public/images/peta/dapil{N}.png (contoh: dapil1.png, dapil2.png, dst)
   - Tampilkan di console: "Copied: [nama_asli] → dapil{N}.png"
4. Scan folder storage/peta/2. PETA PER KECAMATAN/ → untuk setiap file .png:
   - Baca nama file, identifikasi nama kecamatan (misal "Kecamatan Setu.png" atau "SETU.png")
   - Normalize nama: lowercase, spasi → dash (contoh: kecamatan-setu.png, kecamatan-cikarang-pusat.png)
   - Copy ke public/images/peta/kecamatan/{nama_normalized}.png
   - Tampilkan di console
5. Tambahkan public/data/ dan public/images/peta/ ke .gitignore jika belum ada.
6. Di akhir tampilkan summary: total CSV, total peta dapil, total peta kecamatan yang di-copy.

Jalankan command ini: php artisan pemilu:publish

TUGAS 2 — Update routes:

Di routes/web.php, dalam middleware auth group, pastikan route berikut ada (ganti jika sudah ada):

Route::get('/bedah-dapil', function () {
    return view('bedah-dapil.index');
})->name('bedah-dapil.index');

Route::get('/bedah-dapil/pemilu-dprd', function () {
    return view('bedah-dapil.pemilu-dprd');
})->name('bedah-dapil.pemilu-dprd');

Route::get('/bedah-dapil/peta-wilayah', function () {
    return view('bedah-dapil.peta-wilayah');
})->name('bedah-dapil.peta-wilayah');

Langsung kerjakan. Jangan test.
```

---

## PROMPT 2: Redesign Halaman Utama — Main Dashboard

```
Redesign halaman bedah-dapil/pemilu-dprd dengan UI baru. GANTI TOTAL isi file resources/views/bedah-dapil/pemilu-dprd.blade.php.

Langsung ganti seluruh file, JANGAN test, JANGAN tanya.

PENTING: Jangan pakai wire:navigate pada link menuju halaman ini (JS berat perlu full page load). Halaman ini menggunakan layout <x-layouts.app.sidebar> tapi isi kontennya berdiri sendiri dengan styling custom.

PERHATIAN: File ini akan BESAR (3000-4000 baris) karena semua HTML + CSS + JS ada di satu file. Itu normal. Jangan pecah ke file terpisah.

=== DESIGN SYSTEM ===
Accent color: #fe5000 (orange PKS)
Background: #fafafa untuk body area, white untuk cards
Border: 0.5px solid #e5e5e5
Border radius: 10px untuk cards, 6px untuk badges/inputs, 999px untuk pills
Font size: 11px label, 12px body, 13px normal, 14px subtitle, 20px heading, 22px angka besar
Font weight: 400 normal, 500 bold (jangan pakai 600/700)
Text colors: #1a1a1a primary, #444 secondary, #666 tertiary, #888 hint
Status colors & config (5 status, SAMA PERSIS dengan referensi pemilu-dprd.html):
  - JAGA KUAT: color #15803d, bg #dcfce7, text #14532d — "PKS unggul jelas. Fokus menjaga basis dan mengunci loyalitas."
  - AMANKAN: color #65a30d, bg #ecfccb, text #3f6212 — "PKS sudah unggul tapi margin tipis. Perlu pengamanan suara."
  - REBUT REALISTIS: color #2563eb, bg #dbeafe, text #1e3a5f — "PKS belum unggul tapi jarak tipis, realistis direbut."
  - GARAP INTENSIF: color #d97706, bg #fff7f1, text #993c1d — "Potensi ada tapi butuh kerja lapangan lebih rapat."
  - ZONA BERAT: color #b91c1c, bg #fee2e2, text #991b1b — "PKS masih lemah, prioritas bangun fondasi dan jaringan."

=== STRUKTUR HTML ===

A) SUB-NAVBAR (di dalam content area, bukan mengganti sidebar)
Background #1a1a1a, full width di content area.
```html
<div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:12px 12px 0 0;">
  <div style="display:flex;align-items:center;gap:24px;">
    <!-- Logo -->
    <div style="display:flex;align-items:center;gap:8px;">
      <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
        <!-- icon target -->
      </div>
      <div style="font-weight:500;font-size:14px;">Bedah Dapil</div>
    </div>
    <!-- Tab Navigation -->
    <nav style="display:flex;gap:18px;font-size:12px;color:#aaa;">
      <a href="/bedah-dapil/pemilu-dprd" style="color:white;border-bottom:2px solid #fe5000;padding-bottom:10px;margin-bottom:-12px;text-decoration:none;">Dashboard</a>
      <a href="/bedah-dapil/peta-wilayah" style="color:#aaa;text-decoration:none;">Peta Wilayah</a>
      <span>Analisa Caleg</span>
    </nav>
  </div>
  <!-- User info -->
  <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#aaa;">
    <span>Login: <span style="color:white;">{{ auth()->user()->name }}</span></span>
    <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">
      {{ substr(auth()->user()->name, 0, 2) }}
    </div>
  </div>
</div>
```

B) FILTER TOOLBAR — horizontal, satu baris
```html
<div style="background:white;padding:14px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;gap:12px;">
  <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Filter scope:</div>
  <!-- Dapil select: bg orange muda saat ada pilihan -->
  <select id="dapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
    <option value="">Semua dapil</option>
  </select>
  <!-- Kecamatan select -->
  <select id="kecamatanSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
    <option value="">Semua kecamatan</option>
  </select>
  <!-- Status select -->
  <select id="statusSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
    <option value="">Semua status</option>
    <option value="JAGA KUAT">Jaga Kuat</option>
    <option value="AMANKAN">Amankan</option>
    <option value="REBUT REALISTIS">Rebut Realistis</option>
    <option value="GARAP INTENSIF">Garap Intensif</option>
    <option value="ZONA BERAT">Zona Berat</option>
  </select>
  <div style="flex:1;"></div>
  <!-- Search -->
  <input id="searchInput" type="text" placeholder="Cari desa..." style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;width:160px;">
  <!-- Sumber data (collapsible, jarang dipakai) -->
  <details style="font-size:11px;color:#888;">
    <summary style="cursor:pointer;">Sumber Data</summary>
    <div style="position:absolute;right:20px;background:white;border:0.5px solid #e5e5e5;border-radius:8px;padding:12px;margin-top:6px;z-index:10;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
      <div style="margin-bottom:8px;">
        <label style="font-size:11px;color:#666;">TPS CSV:</label>
        <input type="file" id="tpsFileInput" accept=".csv" style="font-size:11px;">
        <div id="sourceStatus" style="font-size:10px;color:#888;margin-top:2px;"></div>
      </div>
      <div>
        <label style="font-size:11px;color:#666;">DPT CSV:</label>
        <input type="file" id="dptFileInput" accept=".csv" style="font-size:11px;">
        <div id="dptStatus" style="font-size:10px;color:#888;margin-top:2px;"></div>
      </div>
    </div>
  </details>
</div>
```

C) HEADER — judul + info scope
```html
<div style="padding:20px 20px 0;">
  <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:6px;">
    <div>
      <h1 id="scopeHeading" style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Kabupaten Bekasi</h1>
      <div id="scopeSubheading" style="font-size:12px;color:#666;margin-top:2px;">Hasil Pemilu DPRD 2024</div>
    </div>
    <div style="font-size:11px;color:#888;">Data terakhir: {{ now()->format('d M Y') }}</div>
  </div>
</div>
```

D) SUMMARY CARDS — grid 4 kolom
4 cards:
- Total DPT (icon users, angka + subtext "X TPS · Y desa")
- Suara Sah (icon chart-bar, angka + "Partisipasi X%")
- Suara PKS — CARD INI SPECIAL: background gradient #fe5000 → #d94400, text putih (icon star, angka + "X% suara sah · peringkat Y")
- Kursi PKS (icon armchair, "X / 55", subtext "X% kursi DPRD")

Setiap card: bg white, border 0.5px #e5e5e5, rounded 10px, padding 14px. Label 11px #666 di atas, angka 22px bold di tengah, subtext 10px #888 di bawah.

```html
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:18px 0;padding:0 20px;">
  <div id="cardDpt" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;">...</div>
  <div id="cardSuaraSah" style="...">...</div>
  <div id="cardPks" style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;">...</div>
  <div id="cardKursi" style="...">...</div>
</div>
```

E) SECTION TENGAH — layout 2 kolom (2fr 1fr)

KOLOM KIRI: Card "Perbandingan Dapil — Suara PKS per Dapil"
- Section label: uppercase 11px orange "PERBANDINGAN DAPIL"
- Title: 14px bold "Suara PKS per Dapil"
- Hint kanan: 11px #888 "Klik bar untuk drill-down"
- Horizontal bar chart (div-based, bukan canvas):
  Untuk setiap dapil 1-7:
  ```html
  <div style="display:flex;align-items:center;gap:10px;font-size:12px;">
    <div style="width:50px;color:#666;">Dapil N</div>
    <div style="flex:1;background:#f5f5f5;border-radius:4px;height:22px;">
      <div style="background:#fe5000;height:100%;width:{persen}%;border-radius:4px;display:flex;align-items:center;padding-left:8px;color:white;font-size:11px;font-weight:500;">{angka}</div>
    </div>
    <div style="width:46px;text-align:right;font-weight:500;">{share}%</div>
  </div>
  ```
  Width bar = (suara_dapil / max_suara_dapil) * 100
  Klik bar → update scope ke dapil tersebut (set dapilSelect, trigger render)
- ID container: id="dapilChartWrap"

KOLOM KANAN: Card "Ranking Partai — Top 5"
- Section label: "RANKING PARTAI"
- Title: "Top 5 di Bekasi"
- List 5 partai terbesar, dengan dot warna partai:
  ```html
  <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
    <div style="width:18px;color:#888;font-size:10px;">1</div>
    <div style="width:8px;height:8px;border-radius:50%;background:{warna_partai};"></div>
    <div style="flex:1;font-weight:500;">{nama}</div>
    <div style="font-weight:500;">{suara_K}</div>
    <div style="width:34px;text-align:right;color:#888;">{share}%</div>
  </div>
  ```
  Row PKS di-highlight: background:#fff7f1, padding:5px 6px, border-radius:5px
- Di bawah ranking, separator line, lalu DEMOGRAFI PEMILIH:
  Stacked bar horizontal (6px tinggi, 4 warna: Gen Z #a78bfa, Millennial #fe5000, Gen X #16a34a, Boomer #94a3b8)
  Label di bawah: "Z 18%  Mil 35%  X 31%  Boom 16%"
- ID containers: id="partyRankWrap", id="demographyBar"

WARNA PARTAI (hardcode):
```javascript
const partyColors = {
  "PKB": "#008000", "Gerindra": "#C8102E", "PDIP": "#D72027",
  "Golkar": "#FFD700", "Nasdem": "#003087", "Buruh": "#E31937",
  "Gelora": "#DC143C", "PKS": "#fe5000", "PKN": "#336699",
  "Hanura": "#4169E1", "Garuda": "#228B22", "PAN": "#005BAC",
  "PBB": "#009B3A", "Demokrat": "#00529C", "PSI": "#EC008C",
  "Perindo": "#CC0000", "PPP": "#006600", "Ummat": "#2E8B57"
};
```

F) STATUS PRIORITAS PKS — grid 4 kolom, card berwarna
```html
<div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
  <div style="margin-bottom:10px;">
    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Status Prioritas PKS</div>
    <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Sebaran <span id="statusTotalDesa">0</span> kelurahan</div>
  </div>
  <div id="statusDashboard" style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;"></div>
</div>
```

Setiap status card (di-render JS):
```html
<div style="background:{bgColor};border-radius:8px;padding:10px;cursor:pointer;" onclick="filterByStatus('{key}')">
  <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
    <div style="width:8px;height:8px;background:{dotColor};border-radius:50%;"></div>
    <div style="font-size:11px;color:{textColor};font-weight:500;">{label}</div>
  </div>
  <div style="font-size:20px;font-weight:500;color:{textColor};line-height:1;">{jumlah}</div>
  <div style="font-size:10px;color:{dotColor};margin-top:2px;">{criteria}</div>
</div>
```

Status config (hardcode):
```javascript
const statusConfig = {
  "JAGA KUAT":       { color: "#15803d", bg: "#dcfce7", text: "#14532d", label: "Jaga Kuat",       criteria: "PKS rank 1 & share ≥30%", description: "PKS unggul jelas. Fokus menjaga basis, merawat struktur, dan mengunci loyalitas." },
  "AMANKAN":         { color: "#65a30d", bg: "#ecfccb", text: "#3f6212", label: "Amankan",         criteria: "PKS rank 1, share <30%",   description: "PKS sudah unggul tetapi margin belum tebal. Perlu pengamanan suara dan penguatan tokoh lokal." },
  "REBUT REALISTIS": { color: "#2563eb", bg: "#dbeafe", text: "#1e3a5f", label: "Rebut Realistis", criteria: "PKS rank 2 & gap ≤5%",     description: "PKS belum unggul, tetapi jaraknya tipis dan realistis untuk direbut dengan kerja terfokus." },
  "GARAP INTENSIF":  { color: "#d97706", bg: "#fff7f1", text: "#993c1d", label: "Garap Intensif",  criteria: "PKS rank ≤3 atau share ≥12%", description: "Potensi ada, namun butuh kerja lapangan yang lebih rapat, terukur, dan konsisten." },
  "ZONA BERAT":      { color: "#b91c1c", bg: "#fee2e2", text: "#991b1b", label: "Zona Berat",     criteria: "PKS share <12% & rank >3",  description: "PKS masih lemah di wilayah ini. Prioritasnya membangun fondasi, jaringan, dan pengenalan." }
};
```

Klasifikasi (5 status, SAMA PERSIS dengan referensi pemilu-dprd.html):
```javascript
function classifyPriority(metrics) {
  const { pksVotes, share, rank, gapShare } = metrics;
  if (pksVotes <= 0) return { key: "ZONA BERAT", ...statusConfig["ZONA BERAT"] };
  if (rank === 1 && share >= 0.3) return { key: "JAGA KUAT", ...statusConfig["JAGA KUAT"] };
  if (rank === 1) return { key: "AMANKAN", ...statusConfig["AMANKAN"] };
  if (rank === 2 && gapShare <= 0.05) return { key: "REBUT REALISTIS", ...statusConfig["REBUT REALISTIS"] };
  if (rank <= 3 || share >= 0.12) return { key: "GARAP INTENSIF", ...statusConfig["GARAP INTENSIF"] };
  return { key: "ZONA BERAT", ...statusConfig["ZONA BERAT"] };
}
```

G) PETA INTERAKTIF — di bawah status dashboard, full width
Card peta:
- Section label: "PETA WILAYAH"
- Title: "{Nama Scope}" (berubah sesuai level)
- Gambar peta dari public:
  - Level kabupaten: /images/peta/kabupaten-bekasi.png
  - Level dapil: /images/peta/dapil{N}.png
  - Level kecamatan: /images/peta/kecamatan/{nama}.png
- Overlay marker dots (absolute positioned, ukuran proporsional suara, warna status)
- Legend di pojok kiri bawah
- Info "Ukuran = total suara PKS" di pojok kanan bawah

```html
<div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;margin-top:14px;">
  <div style="margin-bottom:12px;">
    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Peta Wilayah</div>
    <div id="mapTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Kabupaten Bekasi</div>
  </div>
  <div class="map-frame" style="position:relative;width:100%;aspect-ratio:4/3;border-radius:8px;overflow:hidden;border:0.5px solid #d4d4d4;background:#e8efe0;">
    <img id="mapImage" style="width:100%;height:100%;object-fit:contain;" alt="Peta">
    <div id="mapOverlay" style="position:absolute;inset:0;"></div>
    <div id="mapPlaceholder" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#888;font-size:11px;">
      <div style="text-align:center;">Memuat peta...</div>
    </div>
    <!-- Legend -->
    <div style="position:absolute;bottom:8px;left:8px;background:rgba(255,255,255,0.95);padding:5px 8px;border-radius:5px;font-size:9px;display:flex;gap:8px;border:0.5px solid #e5e5e5;" id="mapLegend"></div>
  </div>
</div>
```

Map config (pakai yang sudah ada di referensi, sudah ada BEKASI 1 dan BEKASI 4):
```javascript
const mapConfigs = {
  "BEKASI 1": {
    image: "/images/peta/dapil1.png",
    villages: [
      { name: "CIJENGKOL", district: "SETU", x: 18.2, y: 18.6 },
      // ... (SALIN SEMUA villages dari mapConfigs yang sudah ada di file saat ini)
    ]
  },
  "BEKASI 4": {
    image: "/images/peta/dapil4.png",
    villages: [
      // ... (SALIN SEMUA villages dari mapConfigs yang sudah ada di file saat ini)
    ]
  }
};
// Untuk dapil yang belum ada config: tampilkan gambar peta tanpa marker
```

H) DAFTAR KECAMATAN — tabel drill-down
Card tabel, di bawah peta:
- Section label: "DAFTAR KECAMATAN" / "DAFTAR DESA" (berubah sesuai level)
- Title: "X Kecamatan di Dapil Y" / "X Desa di Kecamatan Y"
- Search input di kanan header
- Tabel:
  Kolom: Nama | DPT | Suara Sah | Suara PKS | % PKS | Status
  Suara PKS warna #fe5000, % PKS bold, Status pakai pill badge
  Row hover: background #fafafa
  Row klikable → drill-down ke level bawah
- Container: id="drilldownTableWrap"

I) PANEL CALEG PKS (muncul saat drill-down ke level dapil)
Card caleg di samping peta (grid 2 kolom saat di level dapil):
- Section label: "CALEG PKS DAPIL N"
- Title: "Urutan Perolehan Suara 2024"
- Badge "X kursi diperoleh" (bg #dcfce7 jika >0)
- List caleg:
  ```html
  <!-- Caleg rank 1 (highlighted) -->
  <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#fff7f1;border-radius:7px;border:0.5px solid #fce4ce;">
    <div style="width:24px;height:24px;border-radius:50%;background:#fe5000;color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;">1</div>
    <div style="flex:1;">
      <div style="font-size:12px;font-weight:500;">{nama}</div>
      <div style="font-size:10px;color:#666;">No urut {n} · {gender}</div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:13px;font-weight:500;">{suara}</div>
      <div style="font-size:9px;color:#16a34a;font-weight:500;">TERPILIH</div>
    </div>
  </div>
  <!-- Caleg rank 2+ (normal) -->
  <div style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:7px;border:0.5px solid #e5e5e5;">
    <div style="width:22px;height:22px;border-radius:50%;background:#f5f5f5;color:#666;display:flex;align-items:center;justify-content:center;font-size:10px;">{rank}</div>
    <div style="flex:1;">
      <div style="font-size:12px;font-weight:500;">{nama}</div>
      <div style="font-size:10px;color:#666;">No urut {n} · {gender}</div>
    </div>
    <div style="font-size:12px;font-weight:500;">{suara}</div>
  </div>
  ```
  Tampilkan max 5 caleg, sisanya "+ X caleg lainnya"
  Di bawah: total suara PKS dapil (warna orange)
- Container: id="calegPanelWrap"

=== JAVASCRIPT ===

SALIN SEMUA logic JavaScript dari file pemilu-dprd.blade.php yang sudah ada saat ini. Fungsi-fungsi berikut HARUS tetap ada dan bekerja:
- parseSemicolonCsv(text)
- normalizeKey(value)
- resolveLatestDapil(rawDapil, rawKecamatan)
- buildDataset(rows)
- buildDptDataset(rows)
- analyzePks(partyMap, totalTps)
- classifyPriority(metrics) — UPDATE ke 4 status baru
- buildScopeData(dapilObj)
- getVisibleVillages(dapilObj)
- getSortedParties(partyMap)
- semua fungsi build* dan render* yang sudah ada

UPDATE fungsi render berikut untuk match desain baru:

renderSummaryCards(scopeData, dptScope):
- Update 4 card sesuai desain: DPT, Suara Sah, PKS (gradient), Kursi

renderDapilChart(dataset):
- BARU: render horizontal bar chart perbandingan dapil
- Hitung suara PKS per dapil, render bar
- Klik bar → set dapilSelect value, trigger change event

renderPartyRanking(scopeData):
- BARU: render top 5 partai dengan dot warna
- Highlight row PKS

renderDemographyBar(dptScope):
- BARU: render stacked bar demografi di bawah party ranking

renderStatusDashboard(rows):
- UPDATE: 4 card berwarna (bukan 5), pakai statusConfig baru

renderMap(visibleVillages):
- UPDATE: gambar peta sesuai level scope aktif
- Kabupaten → kabupaten-bekasi.png
- Dapil → dapil{N}.png
- Kecamatan → kecamatan/{nama}.png

renderDrilldownTable(rows):
- BARU: tabel kecamatan/desa dengan kolom DPT, Suara Sah, Suara PKS, %, Status pill

renderCalegPanel(scopeData):
- BARU: panel caleg PKS, tampil saat level dapil

render():
- UPDATE fungsi utama: panggil semua render sub-fungsi dengan urutan baru

AUTO-LOAD tetap sama:
- fetch("/data/pemilu/tps_dprd.csv")
- fetch DPT files

EVENT HANDLERS:
- dapilSelect, kecamatanSelect, statusSelect → update state, render()
- searchInput → debounce 300ms, render()
- Bar chart click → set dapil, render()
- Table row click → drill-down
- Status card click → filter status

BREADCRUMB — Tambahkan breadcrumb di bawah filter toolbar saat user sudah drill-down:
```html
<div id="breadcrumb" style="background:white;padding:10px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;gap:6px;font-size:11px;color:#888;">
  <span style="cursor:pointer;" onclick="resetScope()">Kabupaten Bekasi</span>
  <span>›</span>
  <span style="color:#fe5000;font-weight:500;" id="breadcrumbDapil">Dapil 1</span>
  <span>›</span>
  <span id="breadcrumbKecamatan" style="color:#ccc;">(pilih kecamatan)</span>
</div>
```
Sembunyikan breadcrumb saat di level kabupaten.

PENTING:
- Pertahankan SEMUA logic parsing CSV dan data processing yang sudah ada dan bekerja.
- Hanya ubah bagian HTML/CSS rendering.
- Jika ada fungsi render yang belum dibuat, buat sekarang.
- Jangan pecah ke file JS/CSS external, semua di satu blade file.
- Test dengan buka browser: /bedah-dapil/pemilu-dprd — data harus auto-load, summary harus ada angka, chart & tabel harus terisi.

Langsung kerjakan. JANGAN buat test.
```

---

## PROMPT 3: Fix, Polish & Pastikan Semua Berjalan

```
Cek dan fix halaman bedah-dapil/pemilu-dprd. Buka di browser, perbaiki semua yang error atau belum jadi. JANGAN test, langsung fix.

CHECKLIST:

1. HALAMAN LOAD TANPA ERROR:
   - Buka /bedah-dapil/pemilu-dprd
   - Cek console browser, fix semua JS error
   - CSV harus auto-load dari /data/pemilu/tps_dprd.csv

2. SUMMARY CARDS menampilkan angka (bukan 0):
   - Total DPT, Suara Sah, Suara PKS (card gradient orange), Kursi PKS
   - Subtext di bawah angka harus terisi

3. DAPIL BAR CHART terisi:
   - 7 bar horizontal, masing-masing ada angka dan persentase
   - Klik bar → scope berubah ke dapil yang diklik

4. PARTY RANKING terisi:
   - Top 5 partai, PKS di-highlight background orange muda
   - Dot warna sesuai partai

5. STATUS DASHBOARD terisi:
   - 4 card (Kuat, Berkembang, Potensial, Lemah) dengan jumlah desa
   - Klik card → filter desa/kelurahan sesuai status

6. PETA MUNCUL:
   - Gambar peta ter-load sesuai scope (kabupaten/dapil/kecamatan)
   - Marker muncul di atas peta untuk BEKASI 1 dan BEKASI 4
   - Marker warna sesuai status, ukuran proporsional suara

7. TABEL DRILL-DOWN terisi:
   - Level kabupaten: tabel dapil
   - Level dapil: tabel kecamatan
   - Level kecamatan: tabel desa/kelurahan
   - Setiap row punya kolom DPT, Suara PKS, %, Status pill

8. BREADCRUMB berfungsi:
   - Muncul saat di level dapil/kecamatan
   - Klik "Kabupaten Bekasi" → reset ke level atas
   - Klik nama dapil → kembali ke level dapil

9. FILTER BERFUNGSI:
   - Dropdown Dapil → update scope
   - Dropdown Kecamatan → filter kecamatan
   - Dropdown Status → filter by status
   - Search → filter nama desa

10. PANEL CALEG:
    - Muncul saat di level dapil
    - Tampilkan caleg PKS urut suara terbanyak
    - Caleg rank 1 di-highlight orange

JIKA ada fitur yang belum dibuat dari desain, buat sekarang.
JIKA ada style yang tidak match desain (warna, spacing, font size), perbaiki.
JIKA gambar peta tidak ketemu di path, tampilkan placeholder "Peta belum tersedia" (jangan error).

Langsung fix semua. Jangan test.
```

---

## CATATAN UNTUK USER

### Urutan:
1. **Prompt 1** → Publish peta + setup routes
2. **Prompt 2** → Redesign UI total (file besar, sabar tunggu)
3. **Prompt 3** → Fix & polish

### Setelah Prompt 1:
Cek apakah file peta sudah ter-copy dengan benar:
- `public/images/peta/kabupaten-bekasi.png` harus ada
- `public/images/peta/dapil1.png` sampai `dapil7.png` harus ada
- `public/images/peta/kecamatan/` harus ada isinya

Jika command publish error (nama file aneh / format tidak dikenali), perbaiki mapping manual dan jalankan ulang.

### Jika Trae Kewalahan di Prompt 2:
File terlalu besar bisa bikin Trae timeout. Jika terjadi, pecah jadi 2:
- Prompt 2A: "Buat HTML + CSS saja, kosongkan <script>"
- Prompt 2B: "Isi <script> dengan JavaScript logic"

---

## PROMPT 4: Fitur Drill-Down RW/RT + Detail Drawer

```
Lanjutan modul Bedah Dapil. Tambahkan fitur breakdown sampai level RW dan RT. Data ini berasal dari CSV DPT yang sudah di-load (kolom rt, rw per row).

Langsung kerjakan, JANGAN test, JANGAN tanya.

KONTEKS:
Data DPT CSV (dpt_dapil1_rt_rw.csv dll) punya kolom: district;village;tps;rt;rw;dpt;male;female;gen_z;millennial;gen_x;boomer;age_known;age_unknown

Satu TPS bisa punya banyak row RT/RW. Ini artinya satu TPS melayani beberapa RT/RW. Kita bisa estimasi kekuatan PKS per RW/RT dengan distribusi proporsi DPT.

TUGAS:

A) PASTIKAN fungsi-fungsi berikut SUDAH ADA di JavaScript halaman pemilu-dprd.blade.php. Jika belum ada, BUAT. Jika sudah ada, JANGAN hapus:

1. buildDptDataset(rows):
   - Parse CSV DPT, build tpsMap dan villageMap
   - Setiap TPS entry menyimpan rows[] berisi {district, village, tps, rt, rw, dpt, male, female, gen_z, millennial, gen_x, boomer}
   - Return: { totalDpt, totalTps, totalVillages, generation, tpsMap, villageMap }

2. buildDptScopeData(targetVillages):
   - Untuk setiap desa yang visible, cocokkan TPS-nya dengan data DPT
   - Untuk setiap row RT/RW di TPS yang cocok:
     - Hitung share = row.dpt / tps.totalDpt (proporsi DPT wilayah vs total DPT TPS)
     - Distribusikan suara partai ke RW/RT berdasarkan share (estimasi)
     - Aggregate ke rwMap dan rtMap
   - Return: { available, totalScopeTps, matchedTps, missingTps, totalDpt, totalMale, totalFemale, generation, missingVillages, rwRows, rtRows }

3. transformEstimatedRows(groupMap):
   - Transform rwMap/rtMap jadi sorted array
   - Untuk setiap item, jalankan analyzePks() untuk hitung ranking PKS
   - Sort by pks.totalVotes descending

4. addScaledPartyMap(targetMap, sourceMap, factor):
   - Distribute suara partai dari TPS ke RW/RT berdasarkan factor (proporsi DPT)
   - targetMap[partai].partyVotes += sourceMap[partai].partyVotes * factor
   - Sama untuk candidateVotes dan per-candidate

B) TAMBAHKAN TAB RW/RT DI PANEL KANAN atau di section terpisah.

Saat user sudah drill-down ke level desa/kelurahan, tampilkan tabs:
- Tab "Ringkasan" (default) — info umum desa, suara partai
- Tab "RW" — tabel breakdown per RW
- Tab "RT" — tabel breakdown per RT
- Tab "Demografi" — gender + generasi
- Tab "Program" — rekomendasi strategi

Tab styling (pill style, match desain):
```html
<div style="display:flex;gap:4px;margin-bottom:14px;">
  <button class="tab-btn active" data-tab="summary">Ringkasan</button>
  <button class="tab-btn" data-tab="rw">RW</button>
  <button class="tab-btn" data-tab="rt">RT</button>
  <button class="tab-btn" data-tab="demography">Demografi</button>
  <button class="tab-btn" data-tab="program">Program</button>
</div>
```

C) RENDER TABEL RW — renderRwTable(rwRows):

Container: div#rwTabPane

Catatan di atas tabel:
"Angka PKS pada tabel ini adalah estimasi hasil distribusi suara TPS ke level RW berdasarkan proporsi DPT wilayah."

Tabel kolom:
| RW | Desa | DPT Wilayah | Estimasi PKS | Share | TPS Terlibat | Status | Detail |
- RW format: "RW 001", "RW 002" dst
- Estimasi PKS: tampilkan dengan prefix "~" karena estimasi (contoh: ~145)
- Share: persentase PKS
- Status: pill badge sesuai klasifikasi (Jaga Kuat/Amankan/Rebut Realistis/Garap Intensif/Zona Berat)
- Detail: tombol "Buka" → buka detail drawer

Styling tabel match desain:
- Header: 11px uppercase, #666, border-bottom 0.5px
- Row: padding 9px, hover #fafafa, cursor pointer
- Suara PKS warna #fe5000
- Tampilkan max 25 rows, sisanya "Tampilkan semua (X rows)" button

D) RENDER TABEL RT — renderRtTable(rtRows):

Container: div#rtTabPane

Catatan: "Urutan RT menggunakan estimasi kekuatan PKS dari distribusi suara TPS menurut proporsi DPT wilayah RT."

Tabel kolom:
| RT / RW | Desa | DPT Wilayah | Estimasi PKS | TPS | Status | Detail |
- RT/RW format: "RT 001 / RW 005"
- Tampilkan max 30 rows

E) DETAIL DRAWER — slide-in panel dari kanan

Saat user klik "Buka" di tabel RW/RT, tampilkan drawer:

```html
<div id="detailDrawer" class="hidden" style="position:fixed;top:0;right:0;width:420px;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;transition:transform 0.2s;">
  <!-- Header -->
  <div style="padding:16px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;">
    <div>
      <div id="detailDrawerTitle" style="font-size:15px;font-weight:500;">RW 001 - Cijengkol</div>
      <div id="detailDrawerSubtitle" style="font-size:11px;color:#888;margin-top:2px;">Setu | Dominan Laki-laki | 3 TPS</div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
      <div id="detailDrawerBadge" style="display:flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font-size:10px;font-weight:500;">
        <i style="width:6px;height:6px;border-radius:50%;display:inline-block;"></i>
        <span>Kuat</span>
      </div>
      <button id="detailDrawerClose" style="width:28px;height:28px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>
  </div>
  <!-- Content -->
  <div id="detailDrawerContent" style="padding:16px 20px;"></div>
</div>
<div id="detailDrawerBackdrop" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;"></div>
```

Isi drawer (di-render JS):

Panel 1 — Ringkasan RW/RT:
Grid 2x3: DPT Wilayah, Estimasi PKS, Laki-laki, Perempuan, Share PKS, TPS Terlibat
Styling: background #fafafa, rounded 8px, padding 10px. Label 10px uppercase #888, angka 16px bold.

Panel 2 — Demografi:
- Gender progress bar (biru laki vs pink perempuan, dengan persentase)
- Generasi breakdown (4 bar: Gen Z, Millennial, Gen X, Boomer)
Styling: bar height 8px, rounded, warna: Z=#a78bfa, Mil=#fe5000, X=#16a34a, Boom=#94a3b8

Panel 3 — Program Prioritas:
- Karakter wilayah (berdasarkan demografi dominan)
- Fokus aksi (berdasarkan status)
- Daftar program rekomendasi (ordered list)
- Pesan utama
- Format kegiatan
- Segmen sasaran

Fungsi buildProgramRecommendation(context) — generate rekomendasi berdasarkan:
```javascript
function buildProgramRecommendation({ statusKey, totalDpt, totalMale, totalFemale, generation }) {
  const territory = getTerritoryCharacter(generation, totalMale, totalFemale);
  let focus, action, programs, message, activityFormat, segments;
  
  switch (statusKey) {
    case "JAGA KUAT":
      focus = "Pertahankan & Perkuat Basis";
      action = "Jaga loyalitas, aktifkan kader, tingkatkan militansi";
      programs = ["Konsolidasi kader per RW", "Silaturahmi rutin tokoh", "Program bantuan sosial berkelanjutan"];
      message = "Wilayah ini sudah kuat, fokus menjaga agar tidak digerogoti";
      break;
    case "AMANKAN":
      focus = "Amankan Margin & Perkuat Tokoh";
      action = "Pengamanan suara, penguatan tokoh lokal, jaga kader tetap solid";
      programs = ["Pendataan pemilih loyal", "Penguatan tokoh RT/RW", "Monitoring ancaman kompetitor"];
      message = "Sudah unggul tapi margin tipis, jangan lengah";
      break;
    case "REBUT REALISTIS":
      focus = "Rebut dengan Kerja Terfokus";
      action = "Target swing voters, kampanye intensif, mobilisasi maksimal";
      programs = ["Identifikasi swing voters per RT", "Door-to-door campaign terfokus", "Program populis quick-win"];
      message = "Jarak tipis, bisa direbut jika kerja lebih rapat dari kompetitor";
      break;
    case "GARAP INTENSIF":
      focus = "Garap Intensif & Konsisten";
      action = "Identifikasi tokoh lokal, bangun jaringan baru, program populis";
      programs = ["Kerjasama tokoh masyarakat/RT-RW", "Bakti sosial targeted", "Diskusi warga rutin"];
      message = "Ada peluang tapi butuh kerja keras lapangan yang terukur";
      break;
    case "ZONA BERAT":
    default:
      focus = "Bangun Fondasi";
      action = "Mulai dari nol: kenalkan partai, cari simpatisan awal";
      programs = ["Pengenalan tokoh & program partai", "Kegiatan keagamaan/sosial", "Identifikasi potensi kader"];
      message = "Investasi jangka panjang, jangan target tinggi dulu";
      break;
  }
  
  // Adjust by demography
  segments = [];
  if (generation.millennial > generation.gen_x && generation.millennial > generation.boomer) {
    segments.push("Millennial (dominan)");
    activityFormat = "Digital campaign + kegiatan komunitas modern";
  } else if (generation.gen_x > generation.millennial) {
    segments.push("Gen X (dominan)");
    activityFormat = "Pertemuan warga + pendekatan personal";
  } else {
    segments.push("Campuran usia");
    activityFormat = "Kombinasi digital dan tatap muka";
  }
  
  if (totalMale > totalFemale * 1.1) segments.push("Mayoritas laki-laki");
  else if (totalFemale > totalMale * 1.1) segments.push("Mayoritas perempuan");
  
  return { territory, focus, action, programs, message, activityFormat, segments, score: totalDpt };
}
```

Panel 4 — Catatan Lapangan:
- Top caleg PKS di wilayah ini
- Skor prioritas program
- Catatan bahwa data masih estimasi

F) EVENT HANDLERS:
- Tab buttons → switch tab pane visibility
- "Buka" button di tabel RW/RT → openDetailDrawer(type, key)
- Backdrop click atau ✕ button → closeDetailDrawer()
- Drawer close: remove class "hidden", reset state.detailDrawer

G) UPDATE render() function:
- Saat di level desa: render tabs dan tabel RW/RT jika data DPT tersedia
- Panggil buildDptScopeData() untuk generate rwRows dan rtRows
- Panggil renderRwTable(), renderRtTable()
- Panggil renderDetailDrawer() jika state.detailDrawer aktif

H) STYLING:
.tab-btn {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  border: 0.5px solid #e5e5e5;
  background: white;
  color: #666;
  cursor: pointer;
}
.tab-btn.active {
  background: #1a1a1a;
  color: white;
  border-color: transparent;
}
.tab-pane { display: none; }
.tab-pane.active { display: block; }

Drawer hidden: transform: translateX(100%); → visible: transform: translateX(0);

Langsung kerjakan. Jangan test.
```

---

## CATATAN LANJUTAN

### Setelah Prompt 4:
- Data RW/RT hanya tersedia untuk dapil yang punya file DPT (saat ini: Dapil 1, Dapil 4, Dapil 5)
- Untuk dapil tanpa data DPT, tab RW/RT tampilkan: "Data DPT belum tersedia untuk dapil ini. Upload file DPT via menu Sumber Data."
- Estimasi suara RW/RT menggunakan proporsi DPT: jika RW punya 30% DPT dari TPS tersebut, maka ~30% suara TPS dialokasikan ke RW itu

---

## PROMPT 5: Halaman Analisa Caleg

```
Buat halaman baru "Analisa Caleg" untuk modul Bedah Dapil. Halaman ini menganalisis perolehan suara per caleg dari data CSV yang sama (tps_dprd.csv). Langsung buat semua file, JANGAN test, JANGAN tanya.

DATA YANG DIPAKAI: CSV yang sama dengan halaman pemilu-dprd (/data/pemilu/tps_dprd.csv)
Kolom relevan: dapil, kecamatan, desa, tps, partai_id, partai, nomor_urut, nama, gender, suara
- nomor_urut = "0" → suara partai (skip untuk analisa caleg)
- nomor_urut > 0 → suara caleg individu

TUGAS 1 — Tambah route di routes/web.php (dalam middleware auth group):

Route::get('/bedah-dapil/analisa-caleg', function () {
    return view('bedah-dapil.analisa-caleg');
})->name('bedah-dapil.analisa-caleg');

TUGAS 2 — Buat file: resources/views/bedah-dapil/analisa-caleg.blade.php

Layout: <x-layouts.app.sidebar>
Jangan pakai wire:navigate (JS berat).

PERHATIAN: File ini akan besar (2000-3000 baris), itu normal. Semua HTML + CSS + JS di satu file.

=== DESIGN SYSTEM (sama dengan pemilu-dprd) ===
Accent: #fe5000
Background: #fafafa body, white cards
Border: 0.5px solid #e5e5e5, radius 10px card, 6px input, 999px pill
Font: 11px label, 12px body, 14px subtitle, 20px heading, 22px angka besar
Text: #1a1a1a primary, #444 secondary, #666 tertiary, #888 hint
Warna partai (hardcode):
```javascript
const partyColors = {
  "PKB": "#008000", "Gerindra": "#C8102E", "PDIP": "#D72027",
  "Golkar": "#FFD700", "Nasdem": "#003087", "Buruh": "#E31937",
  "Gelora": "#DC143C", "PKS": "#fe5000", "PKN": "#336699",
  "Hanura": "#4169E1", "Garuda": "#228B22", "PAN": "#005BAC",
  "PBB": "#009B3A", "Demokrat": "#00529C", "PSI": "#EC008C",
  "Perindo": "#CC0000", "PPP": "#006600", "Ummat": "#2E8B57"
};
```

=== STRUKTUR HTML ===

A) SUB-NAVBAR (sama dengan pemilu-dprd, tapi tab "Analisa Caleg" yang active):
```html
<div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:12px 12px 0 0;">
  <div style="display:flex;align-items:center;gap:24px;">
    <div style="display:flex;align-items:center;gap:8px;">
      <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">🎯</div>
      <div style="font-weight:500;font-size:14px;">Bedah Dapil</div>
    </div>
    <nav style="display:flex;gap:18px;font-size:12px;color:#aaa;">
      <a href="{{ route('bedah-dapil.pemilu-dprd') }}" style="color:#aaa;text-decoration:none;">Dashboard</a>
      <a href="{{ route('bedah-dapil.peta-wilayah') }}" style="color:#aaa;text-decoration:none;">Peta Wilayah</a>
      <span>Hasil Pemilu</span>
      <a href="{{ route('bedah-dapil.analisa-caleg') }}" style="color:white;border-bottom:2px solid #fe5000;padding-bottom:10px;margin-bottom:-12px;text-decoration:none;">Analisa Caleg</a>
    </nav>
  </div>
  <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#aaa;">
    <span>Login: <span style="color:white;">{{ auth()->user()->name }}</span></span>
    <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">
      {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
    </div>
  </div>
</div>
```

B) FILTER TOOLBAR:
```html
<div style="background:white;padding:12px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
  <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Scope:</div>
  <select id="dapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;">
    <option value="">Semua dapil</option>
  </select>
  <select id="partaiSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
    <option value="">Semua partai</option>
    <!-- Diisi JS dari data -->
  </select>
  <select id="genderSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
    <option value="">Semua gender</option>
    <option value="L">Laki-laki</option>
    <option value="P">Perempuan</option>
  </select>
  <div style="flex:1;"></div>
  <input id="searchInput" type="text" placeholder="Cari nama caleg..." style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;width:200px;">
  <details style="font-size:11px;color:#888;">
    <summary style="cursor:pointer;">Sumber Data</summary>
    <div style="position:absolute;right:20px;background:white;border:0.5px solid #e5e5e5;border-radius:8px;padding:12px;margin-top:6px;z-index:10;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
      <label style="font-size:11px;color:#666;">TPS CSV:</label>
      <input type="file" id="csvFileInput" accept=".csv" style="font-size:11px;">
      <div id="sourceStatus" style="font-size:10px;color:#888;margin-top:4px;"></div>
    </div>
  </details>
</div>
```

C) HEADER:
```html
<div style="padding:20px 20px 0;">
  <h1 id="pageHeading" style="font-size:20px;font-weight:500;margin:0;">Analisa Caleg — Kabupaten Bekasi</h1>
  <div id="pageSubheading" style="font-size:12px;color:#666;margin-top:2px;">Perbandingan perolehan suara 854 caleg dari 17 partai · 7 dapil</div>
</div>
```

D) SUMMARY CARDS — grid 5 kolom:
```html
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin:18px 0;padding:0 20px;">
```
Card 1: Total Caleg (icon: users, angka besar, sub: "X partai")
Card 2: Total Suara Caleg (icon: chart-bar, angka, sub: "Suara individu caleg")
Card 3: Caleg PKS — GRADIENT CARD (#fe5000): angka caleg PKS, sub: "X L · Y P"
Card 4: Suara Caleg PKS — GRADIENT CARD: total suara, sub: "X% dari total · rank Y"
Card 5: Rata-rata Suara/Caleg (icon: trending-up, angka, sub: "PKS: X vs rata-rata: Y")

Setiap card style sama: bg white, border 0.5px #e5e5e5, rounded 10px, padding 14px.
Card PKS: background gradient #fe5000 → #d94400, text white.

E) SECTION: RANKING CALEG + SEBARAN SUARA PKS — grid 2 kolom (1fr 1fr)

KOLOM KIRI: "Ranking Caleg — Top 15 Peraih Suara"
Container: id="calegRankingWrap"
- Section label: "RANKING CALEG" (11px orange uppercase)
- Title: "Top 15 peraih suara tertinggi" + "(Dapil X)" jika filtered
- Hint: "Klik nama untuk lihat detail"

Render sebagai list card (BUKAN tabel):
```html
<!-- Caleg terpilih -->
<div style="display:flex;align-items:center;gap:8px;padding:8px;background:#f0fdf4;border-radius:7px;border:0.5px solid #bbf7d0;margin-bottom:4px;cursor:pointer;" onclick="selectCaleg('{calegKey}')">
  <div style="width:24px;height:24px;border-radius:50%;background:{partyColor};color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;">{rank}</div>
  <div style="flex:1;min-width:0;">
    <div style="font-size:12px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{nama}</div>
    <div style="font-size:10px;color:#666;display:flex;align-items:center;gap:3px;">
      <span style="width:6px;height:6px;border-radius:50%;background:{partyColor};display:inline-block;"></span>
      {partai} · No. {nomor_urut} · {gender}
    </div>
  </div>
  <div style="text-align:right;">
    <div style="font-size:13px;font-weight:500;">{suara_formatted}</div>
    <div style="font-size:9px;color:#16a34a;font-weight:500;">TERPILIH</div>
  </div>
</div>

<!-- Caleg PKS (highlighted orange) -->
<div style="...;background:#fff7f1;border:0.5px solid #fce4ce;...">
  <!-- Sama tapi angka suara warna #fe5000 -->
</div>

<!-- Caleg biasa -->
<div style="...;border:0.5px solid #e5e5e5;...">
  <!-- Tanpa badge TERPILIH, tanpa highlight -->
</div>
```

KOLOM KANAN: "Caleg PKS — Sebaran Suara per Wilayah"
Container: id="pksSebaranWrap"
- Section label: "CALEG PKS DAPIL X"
- Title: "Sebaran suara per kecamatan"

Untuk setiap caleg PKS (urut by suara desc):
```html
<div style="margin-bottom:14px;">
  <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px;">
    <div>
      <span style="font-size:13px;font-weight:500;">{nama}</span>
      <span style="font-size:10px;color:#666;margin-left:6px;">No. {nomor_urut} · {gender} · {total_suara} suara</span>
    </div>
    <!-- Badge TERPILIH jika rank 1 di partai dan masuk kursi -->
  </div>
  <!-- Horizontal bar per kecamatan -->
  <div style="display:flex;flex-direction:column;gap:3px;">
    <!-- Untuk setiap kecamatan (urut by suara desc): -->
    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
      <div style="width:85px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{kecamatan}</div>
      <div style="flex:1;background:#f5f5f5;border-radius:3px;height:14px;">
        <div style="background:#fe5000;height:100%;width:{persen}%;border-radius:3px;min-width:{min}px;display:flex;align-items:center;padding-left:4px;color:white;font-size:9px;">
          {suara}
        </div>
      </div>
    </div>
  </div>
  <!-- Basis kuat -->
  <div style="font-size:10px;color:#888;margin-top:4px;">
    Basis kuat: {top_3_desa_dengan_suara_terbanyak}
  </div>
</div>
<div style="border-top:0.5px solid #f0f0f0;padding-top:10px;margin-top:10px;">
  <!-- Caleg PKS berikutnya -->
</div>
```

F) SECTION: HEAD-TO-HEAD + ANALISA GENDER — grid 2 kolom

KOLOM KIRI: "Head-to-Head PKS vs Kompetitor"
Container: id="headToHeadWrap"
- Section label: "HEAD-TO-HEAD"
- Title: "PKS vs kompetitor per desa"
- Subtitle: "Perbandingan suara PKS dengan 2 partai terbesar di setiap desa"

Tabel:
Kolom: Desa | Kecamatan | PKS | {Partai #1} | {Partai #2} | Rank PKS
- Header kolom partai pakai nama partai + dot warna
- Kolom PKS warna #fe5000
- Kolom Rank PKS pakai pill badge:
  - Rank 1: bg #dcfce7 text #14532d
  - Rank 2: bg #dbeafe text #1e3a5f
  - Rank 3: bg #fff7f1 text #993c1d
  - Rank 4+: bg #fee2e2 text #991b1b
- Row hover: bg #fafafa
- Sort default by suara PKS desc
- Tampilkan max 20 row, tombol "Tampilkan semua"

Cara hitung: Untuk setiap desa, sum suara per partai (nomor_urut > 0), tentukan ranking PKS.

KOLOM KANAN: "Analisa Gender Caleg"
Container: id="genderAnalysisWrap"
- Section label: "ANALISA GENDER"
- Title: "Komposisi caleg & efektivitas suara"

Sub-section 1: Komposisi L/P per partai (top 5 partai)
Stacked bar horizontal per partai:
```html
<div style="display:flex;align-items:center;gap:8px;font-size:12px;margin-bottom:6px;">
  <div style="width:55px;color:#666;">{partai}</div>
  <div style="flex:1;display:flex;height:16px;border-radius:3px;overflow:hidden;">
    <div style="background:#3b82f6;width:{persen_L}%;display:flex;align-items:center;justify-content:center;color:white;font-size:9px;">{count} L</div>
    <div style="background:#ec4899;width:{persen_P}%;display:flex;align-items:center;justify-content:center;color:white;font-size:9px;">{count} P</div>
  </div>
</div>
```

Sub-section 2: Rata-rata suara per gender
```html
<div style="margin-top:14px;padding-top:12px;border-top:0.5px solid #f0f0f0;">
  <div style="font-size:11px;color:#888;margin-bottom:8px;">Rata-rata suara per caleg</div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
    <div style="background:#eff6ff;border-radius:8px;padding:10px;text-align:center;">
      <div style="font-size:10px;color:#3b82f6;">Laki-laki</div>
      <div style="font-size:18px;font-weight:500;color:#1e40af;">{rata2_L}</div>
      <div style="font-size:10px;color:#888;">{total_caleg_L} caleg</div>
    </div>
    <div style="background:#fdf2f8;border-radius:8px;padding:10px;text-align:center;">
      <div style="font-size:10px;color:#ec4899;">Perempuan</div>
      <div style="font-size:18px;font-weight:500;color:#be185d;">{rata2_P}</div>
      <div style="font-size:10px;color:#888;">{total_caleg_P} caleg</div>
    </div>
  </div>
</div>
```

Sub-section 3: Insight text
```html
<div style="margin-top:12px;font-size:11px;color:#444;line-height:1.6;padding:10px;background:#fafafa;border-radius:6px;">
  <!-- Generated insight, contoh: -->
  PKS punya {n_perempuan} caleg perempuan di {dapil}. 
  {nama_caleg_P_tertinggi} meraih suara tertinggi ({suara}). 
  Caleg perempuan PKS meraup {persen}% total suara PKS di dapil ini.
</div>
```

G) SECTION: TABEL CALEG LENGKAP — full width
Container: id="fullTableWrap"
- Section label: "TABEL CALEG LENGKAP"
- Title: "Semua caleg {Dapil X} — {n} caleg, {m} partai"
- Tombol "Ekspor CSV" di header kanan

Tabel sortable:
Kolom: # | Nama | Partai | No. Urut | L/P | Suara | % dari Partai | Basis Kuat
- #: ranking by suara (semua partai)
- Nama: font-weight 500
- Partai: dot warna + nama partai
- No. Urut: center align
- L/P: center, badge biru (L) atau pink (P)
- Suara: right align, bold. Caleg PKS warna #fe5000
- % dari Partai: (suara caleg / total suara semua caleg partai tersebut * 100)
- Basis Kuat: 2-3 desa dimana caleg ini dapat suara terbanyak (font-size 10px, color #666)

Row styling:
- Caleg PKS: background #fff7f1
- Caleg terpilih (semua partai): background #f0fdf4
- Normal: white
- Hover: #fafafa

Sort: klik header kolom untuk sort (toggle asc/desc). Default: by suara desc.
Pagination: 25 per page, tombol prev/next.

H) DETAIL DRAWER — slide-in dari kanan (saat klik nama caleg)
Container: id="calegDrawer"

```html
<div id="calegDrawer" style="position:fixed;top:0;right:0;width:440px;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;transform:translateX(100%);transition:transform 0.2s;" class="hidden">
```

Drawer header:
- Nama caleg (15px bold)
- Partai + dot warna + No. Urut + Gender
- Badge TERPILIH (jika applicable)
- Tombol close ✕

Drawer content panels:

Panel 1 — Ringkasan:
Grid 2x3:
- Total Suara | Ranking (dari semua caleg dapil)
- % dari Partai | Jumlah TPS
- Ranking PKS Internal | Gender

Panel 2 — Sebaran per Kecamatan:
Horizontal bar chart (sama seperti di panel kanan halaman utama)
Setiap kecamatan: nama, bar, angka suara

Panel 3 — Top 10 Desa:
Tabel: Desa | Kecamatan | Suara | % dari total caleg ini
Sort by suara desc, max 10 row

Panel 4 — Perbandingan dengan Caleg Separtai:
Bar chart semua caleg di partai yang sama:
- Highlight caleg yang sedang dilihat
- Tampilkan siapa yang terpilih

Panel 5 — Heatmap Desa:
Mini grid/tabel yang menunjukkan penyebaran suara di SEMUA desa:
- Desa dengan suara tinggi: background warna gelap (heat)
- Desa dengan suara rendah: background warna terang
- Ini menunjukkan "coverage" caleg — apakah suara terpusat atau menyebar

=== JAVASCRIPT ===

DATA PROCESSING:

1. parseSemicolonCsv(text) — sama dengan di pemilu-dprd
2. normalizeKey(value) — sama

3. buildCalegDataset(rows):
   Input: parsed CSV rows
   Output:
   ```javascript
   {
     dapils: Map<dapilName, {
       calegMap: Map<calegKey, {
         key, nama, partaiId, partai, nomorUrut, gender,
         totalSuara,
         desaMap: Map<desaKey, { desa, kecamatan, suara }>,
         kecamatanMap: Map<kecName, { kecamatan, suara }>,
         tpsCount
       }>,
       partyMap: Map<partaiId, {
         partaiId, partai,
         calegList: [calegObj...],
         totalSuaraCaleg,
         calegCount, lakilaki, perempuan
       }>
     }>
   }
   ```
   Logic:
   - Skip rows dengan nomor_urut = "0" (itu suara partai, bukan caleg)
   - calegKey = `${dapil}__${partaiId}__${nomorUrut}__${normalizeKey(nama)}`
   - Untuk setiap row: akumulasi suara ke caleg, per desa, per kecamatan
   - Group caleg ke party

4. buildScopeData(dapilKey):
   - Ambil semua caleg di dapil (atau semua dapil jika dapilKey kosong)
   - Sort by totalSuara desc → ranking
   - Hitung PKS ranking, share, dll
   - Return { allCaleg, pksCaleg, topPartai, genderStats, headToHead }

5. buildHeadToHead(dapilData):
   - Untuk setiap desa, aggregate suara per partai
   - Tentukan ranking PKS per desa
   - Sort by suara PKS desc
   - Return array of { desa, kecamatan, parties: [{partai, suara}...], pksRank, pksSuara }

6. buildGenderStats(dapilData):
   - Per partai: count L/P caleg, total suara L/P
   - Rata-rata suara per gender
   - Insight text generation
   - Return { perParty, avgMale, avgFemale, totalMale, totalFemale, insight }

RENDER FUNCTIONS:

- renderSummaryCards(scopeData) — update 5 summary cards
- renderCalegRanking(scopeData) — top 15 caleg semua partai
- renderPksSebaran(scopeData) — sebaran suara caleg PKS per kecamatan
- renderHeadToHead(scopeData) — tabel PKS vs kompetitor per desa
- renderGenderAnalysis(scopeData) — stacked bar + stats + insight
- renderFullTable(scopeData) — tabel lengkap semua caleg, sortable, paginated
- renderCalegDrawer(calegObj) — drawer detail caleg

EVENT HANDLERS:
- dapilSelect.change → update scope, render
- partaiSelect.change → filter, render
- genderSelect.change → filter, render
- searchInput.input → debounce 300ms, filter nama caleg, render
- Klik nama caleg (di ranking, tabel, atau sebaran) → openCalegDrawer(calegKey)
- Drawer close → transform translateX(100%)
- Table header click → sort kolom, re-render tabel
- Pagination prev/next

SORT FUNCTION untuk tabel:
```javascript
let currentSort = { column: 'suara', direction: 'desc' };

function sortCaleg(calegList, column, direction) {
  return [...calegList].sort((a, b) => {
    let valA, valB;
    switch(column) {
      case 'nama': valA = a.nama; valB = b.nama; break;
      case 'partai': valA = a.partai; valB = b.partai; break;
      case 'suara': valA = a.totalSuara; valB = b.totalSuara; break;
      case 'nomorUrut': valA = a.nomorUrut; valB = b.nomorUrut; break;
      default: valA = a.totalSuara; valB = b.totalSuara;
    }
    if (typeof valA === 'string') return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
    return direction === 'asc' ? valA - valB : valB - valA;
  });
}
```

AUTO-LOAD: Saat halaman ready, fetch("/data/pemilu/tps_dprd.csv"), parse, buildCalegDataset, render.

HELPER FUNCTIONS:
- formatNumber(n) — Intl.NumberFormat("id-ID")
- toTitleCase(s) — uppercase first letter each word
- escapeHtml(s) — sanitize
- getPartyColor(partai) — lookup dari partyColors
- truncate(s, len) — potong string + "..."

PENTING:
- Caleg PKS selalu di-highlight (bg #fff7f1) di tabel dan ranking
- Saat filter partai = "PKS", ranking dan tabel hanya tampilkan caleg PKS
- Saat filter dapil dipilih, semua section update ke scope dapil tersebut
- Heading dan subheading update sesuai scope aktif
- Drawer harus bisa dibuka dari mana saja (ranking, tabel, sebaran)
- Pastikan tidak N+1: aggregate data di buildCalegDataset, bukan saat render

Langsung buat file lengkap. Jangan test. Pastikan halaman bisa dibuka dan data ter-load.
```

---

## PROMPT 6: Fix Analisa Caleg + Integrasi Navigasi

```
Cek dan fix halaman bedah-dapil/analisa-caleg. Langsung fix, JANGAN test.

CHECKLIST:

1. Halaman /bedah-dapil/analisa-caleg load tanpa JS error
2. CSV auto-load, dropdown dapil terisi BEKASI 1-7
3. Summary cards menampilkan angka (total caleg, suara, PKS stats)
4. Ranking caleg top 15 muncul — caleg PKS di-highlight orange
5. Sebaran suara caleg PKS muncul — bar horizontal per kecamatan
6. Head-to-Head tabel terisi — PKS vs 2 partai terbesar per desa
7. Gender analysis — stacked bar + insight text
8. Tabel lengkap — sortable (klik header), paginated, searchable
9. Drawer detail caleg — klik nama caleg → drawer slide in dari kanan
10. Filter berfungsi: dapil, partai, gender, search

FIX NAVIGASI ANTAR HALAMAN:
- Di halaman pemilu-dprd, pastikan link "Analisa Caleg" di sub-navbar mengarah ke /bedah-dapil/analisa-caleg (bukan span, harus <a>)
- Di halaman analisa-caleg, link "Dashboard" mengarah ke /bedah-dapil/pemilu-dprd
- Semua link antar halaman bedah-dapil JANGAN pakai wire:navigate (karena JS berat)

FIX SIDEBAR MENU:
Di sidebar.blade.php, pastikan menu "Bedah Dapil" sudah ada dan mengarah ke bedah-dapil.index.

Jika ada fitur yang belum di-render, buat sekarang. Minimal:
- renderSummaryCards harus jalan
- renderCalegRanking harus jalan
- renderFullTable harus jalan (ini yang paling penting)

Langsung fix. Jangan test.
```

---

## PROMPT 7: Fitur Rencana Aksi per Wilayah (Opsi B — Tanpa Database)

```
Lanjutan modul Bedah Dapil. Tambahkan fitur "Rencana Aksi" yang otomatis generate program kerja berdasarkan status wilayah. Fitur ini TANPA DATABASE — semua di-generate JS di browser, bisa export/print.

Langsung kerjakan, JANGAN test, JANGAN tanya.

TUGAS: Update file resources/views/bedah-dapil/pemilu-dprd.blade.php — tambahkan fitur rencana aksi.

=== KONSEP ===
Setiap status wilayah (JAGA KUAT, AMANKAN, REBUT REALISTIS, GARAP INTENSIF, ZONA BERAT) punya "resep aksi" baku. Saat user melihat detail RW/Desa, otomatis muncul program kerja yang harus dilakukan di wilayah tersebut.

=== A) KONFIGURASI AKSI (hardcode di JavaScript) ===

```javascript
const aksiConfig = {
  "JAGA KUAT": {
    prioritas: "PERTAHANKAN",
    warna: "#15803d",
    frekuensi: "Rutin bulanan",
    programs: [
      { nama: "Konsolidasi kader aktif per RT", kategori: "Organisasi", target: "1 kader aktif per RT", deadline: "Bulan 1-2" },
      { nama: "Pendataan pemilih loyal by name & address", kategori: "Data", target: "80% pemilih loyal terdata", deadline: "Bulan 1-3" },
      { nama: "Silaturahmi rutin tokoh masyarakat", kategori: "Jaringan", target: "2x per bulan", deadline: "Rutin" },
      { nama: "Program bantuan sosial berkelanjutan", kategori: "Sosial", target: "1 kegiatan per bulan", deadline: "Rutin" },
      { nama: "Monitoring ancaman kompetitor masuk", kategori: "Intelijen", target: "Laporan bulanan", deadline: "Rutin" },
      { nama: "Pengajian/diskusi rutin warga", kategori: "Keagamaan", target: "2x per bulan", deadline: "Rutin" }
    ],
    targetUtama: "Pertahankan share ≥30%, zero penurunan suara",
    pic: "Ketua Ranting + Kader Senior",
    pesan: "Wilayah sudah kuat. Fokus menjaga loyalitas, merawat struktur, jangan sampai lengah."
  },
  "AMANKAN": {
    prioritas: "PERKUAT MARGIN",
    warna: "#65a30d",
    frekuensi: "2x sebulan",
    programs: [
      { nama: "Penguatan tokoh RT/RW sebagai influencer lokal", kategori: "Jaringan", target: "1 tokoh per RT aktif", deadline: "Bulan 1-2" },
      { nama: "Ekspansi ke RT yang masih lemah", kategori: "Ekspansi", target: "50% RT lemah terjangkau", deadline: "Bulan 2-4" },
      { nama: "Rekrutmen relawan baru per RT", kategori: "Organisasi", target: "2 relawan baru per RT", deadline: "Bulan 1-3" },
      { nama: "Program bantuan spesifik komunitas", kategori: "Sosial", target: "1 program per 2 bulan", deadline: "Bulan 2+" },
      { nama: "Door-to-door di area swing voters", kategori: "Kampanye", target: "Jangkau 100 KK per bulan", deadline: "Bulan 3+" },
      { nama: "Pendataan pemilih potensial", kategori: "Data", target: "Database 60% pemilih", deadline: "Bulan 1-4" }
    ],
    targetUtama: "Naikkan share ke ≥30% (upgrade ke JAGA KUAT)",
    pic: "Ketua Ranting + Koordinator RW",
    pesan: "Sudah unggul tapi margin tipis. Perkuat basis dan ekspansi ke RT yang belum tergarap."
  },
  "REBUT REALISTIS": {
    prioritas: "REBUT & MENANGKAN",
    warna: "#2563eb",
    frekuensi: "3-4x sebulan",
    programs: [
      { nama: "Identifikasi & mapping swing voters per RT", kategori: "Data", target: "Data 200 swing voters", deadline: "Bulan 1" },
      { nama: "Kampanye door-to-door intensif", kategori: "Kampanye", target: "150 KK per minggu", deadline: "Bulan 1-6" },
      { nama: "Program populis quick-win (bantuan langsung)", kategori: "Sosial", target: "1 program per bulan", deadline: "Bulan 1+" },
      { nama: "Kolaborasi tokoh masyarakat netral", kategori: "Jaringan", target: "3 tokoh netral per RW", deadline: "Bulan 1-3" },
      { nama: "Counter narasi kompetitor", kategori: "Komunikasi", target: "1 konten per minggu", deadline: "Rutin" },
      { nama: "Mobilisasi pemilih (transport, reminder)", kategori: "Logistik", target: "Rencana mobilisasi 100% TPS", deadline: "Bulan 6" },
      { nama: "Rekrutmen saksi TPS yang solid", kategori: "Organisasi", target: "2 saksi per TPS", deadline: "Bulan 4-6" }
    ],
    targetUtama: "Tutup gap suara, targetkan rank 1 (upgrade ke AMANKAN/JAGA KUAT)",
    pic: "Tim Khusus Dapil + Caleg PKS",
    pesan: "Jarak tipis dan realistis direbut! Ini wilayah dengan ROI tertinggi — alokasi resources di sini."
  },
  "GARAP INTENSIF": {
    prioritas: "GARAP & BANGUN",
    warna: "#d97706",
    frekuensi: "2x sebulan",
    programs: [
      { nama: "Bangun jaringan tokoh lokal (RT/RW/ulama)", kategori: "Jaringan", target: "1 tokoh per RW", deadline: "Bulan 1-3" },
      { nama: "Bakti sosial targeted (kesehatan/pendidikan)", kategori: "Sosial", target: "1 baksos per kuartal", deadline: "Kuartal" },
      { nama: "Diskusi warga rutin di musholla/pos RT", kategori: "Keagamaan", target: "1x per bulan", deadline: "Rutin" },
      { nama: "Rekrut kader dari komunitas lokal", kategori: "Organisasi", target: "5 kader per RW", deadline: "Bulan 3-6" },
      { nama: "Branding PKS via kegiatan nyata (bukan banner)", kategori: "Komunikasi", target: "1 kegiatan berdampak per bulan", deadline: "Rutin" }
    ],
    targetUtama: "Naikkan share ke ≥15% dalam 1 tahun (upgrade ke REBUT REALISTIS)",
    pic: "Koordinator Kecamatan",
    pesan: "Potensi ada tapi butuh kerja lapangan konsisten dan terukur. Fokus bangun relasi, bukan kampanye."
  },
  "ZONA BERAT": {
    prioritas: "BANGUN FONDASI",
    warna: "#b91c1c",
    frekuensi: "1x sebulan",
    programs: [
      { nama: "Survey awal: kenapa PKS lemah di sini?", kategori: "Data", target: "1 laporan survey", deadline: "Bulan 1" },
      { nama: "Cari 1 'pintu masuk' (tokoh/isu lokal)", kategori: "Jaringan", target: "1 kontak person per RW", deadline: "Bulan 1-2" },
      { nama: "Kegiatan keagamaan/sosial sederhana", kategori: "Sosial", target: "1 kegiatan per kuartal", deadline: "Kuartal" },
      { nama: "Pengenalan wajah caleg/tokoh PKS ke warga", kategori: "Komunikasi", target: "1 kegiatan perkenalan", deadline: "Bulan 2-3" },
      { nama: "Identifikasi 3 isu lokal yang bisa digarap PKS", kategori: "Data", target: "3 isu teridentifikasi", deadline: "Bulan 1-2" }
    ],
    targetUtama: "Minimal 1 kontak person per RW, target share 5% dalam 2 tahun",
    pic: "DPC + Kader Sukarela",
    pesan: "Investasi jangka panjang. Jangan target tinggi dulu, fokus bangun fondasi dan kenal warga."
  }
};
```

=== B) TAMBAHKAN TAB "RENCANA AKSI" ===

Di detail drawer (saat klik RW/Desa), tambahkan tab baru setelah tab yang sudah ada.

Tabs menjadi: Ringkasan | RW | RT | Demografi | Program | **Rencana Aksi**

Atau jika detail drawer sudah pakai panel, tambahkan panel baru "Rencana Aksi" di paling bawah.

=== C) RENDER RENCANA AKSI ===

Fungsi renderRencanaAksi(statusKey, wilayahInfo):

```html
<!-- Header -->
<div style="background:{aksi.warna}10;border:0.5px solid {aksi.warna}40;border-radius:10px;padding:14px;margin-bottom:12px;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
    <div>
      <div style="font-size:11px;color:{aksi.warna};text-transform:uppercase;font-weight:500;letter-spacing:0.8px;">Rencana Aksi — {statusLabel}</div>
      <div style="font-size:14px;font-weight:500;color:#1a1a1a;margin-top:2px;">{wilayah_nama} · {wilayah_level}</div>
    </div>
    <div style="display:flex;gap:6px;">
      <button onclick="exportAksiExcel()" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;cursor:pointer;">📥 Excel</button>
      <button onclick="printAksi()" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;cursor:pointer;">🖨️ Print</button>
    </div>
  </div>
  <div style="font-size:12px;color:#444;line-height:1.5;">{aksi.pesan}</div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:10px;">
    <div style="background:white;border-radius:6px;padding:8px;text-align:center;">
      <div style="font-size:10px;color:#888;">Prioritas</div>
      <div style="font-size:12px;font-weight:500;color:{aksi.warna};">{aksi.prioritas}</div>
    </div>
    <div style="background:white;border-radius:6px;padding:8px;text-align:center;">
      <div style="font-size:10px;color:#888;">Frekuensi</div>
      <div style="font-size:12px;font-weight:500;">{aksi.frekuensi}</div>
    </div>
    <div style="background:white;border-radius:6px;padding:8px;text-align:center;">
      <div style="font-size:10px;color:#888;">PIC</div>
      <div style="font-size:12px;font-weight:500;">{aksi.pic}</div>
    </div>
  </div>
</div>

<!-- Target Utama -->
<div style="background:#fafafa;border-radius:8px;padding:10px 14px;margin-bottom:12px;border-left:3px solid {aksi.warna};">
  <div style="font-size:10px;color:#888;text-transform:uppercase;">Target Utama</div>
  <div style="font-size:13px;font-weight:500;color:#1a1a1a;margin-top:2px;">{aksi.targetUtama}</div>
</div>

<!-- Checklist Program -->
<div style="font-size:12px;font-weight:500;margin-bottom:8px;">Checklist Program ({programs.length} item)</div>

<!-- Untuk setiap program: -->
<div style="border:0.5px solid #e5e5e5;border-radius:8px;margin-bottom:6px;overflow:hidden;">
  <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:white;">
    <input type="checkbox" style="width:16px;height:16px;accent-color:{aksi.warna};" data-program="{index}">
    <div style="flex:1;min-width:0;">
      <div style="font-size:12px;font-weight:500;color:#1a1a1a;">{program.nama}</div>
      <div style="font-size:10px;color:#666;display:flex;gap:10px;margin-top:2px;">
        <span style="background:#f5f5f5;padding:1px 6px;border-radius:4px;">{program.kategori}</span>
        <span>Target: {program.target}</span>
        <span>Deadline: {program.deadline}</span>
      </div>
    </div>
  </div>
  <!-- Expanded: input catatan (toggle on click) -->
  <div class="aksi-note hidden" style="padding:8px 12px;background:#fafafa;border-top:0.5px solid #e5e5e5;">
    <textarea placeholder="Catatan pelaksanaan..." style="width:100%;border:0.5px solid #d4d4d4;border-radius:4px;padding:6px;font-size:11px;resize:vertical;min-height:40px;"></textarea>
    <div style="display:flex;gap:6px;margin-top:4px;">
      <input type="date" style="border:0.5px solid #d4d4d4;border-radius:4px;padding:3px 6px;font-size:11px;">
      <select style="border:0.5px solid #d4d4d4;border-radius:4px;padding:3px 6px;font-size:11px;">
        <option>Belum mulai</option>
        <option>Sedang berjalan</option>
        <option>Selesai</option>
        <option>Terkendala</option>
      </select>
    </div>
  </div>
</div>

<!-- Progress Bar -->
<div style="margin-top:12px;">
  <div style="display:flex;justify-content:space-between;font-size:11px;color:#888;margin-bottom:4px;">
    <span>Progress</span>
    <span id="progressText">0 / {total} program</span>
  </div>
  <div style="width:100%;height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden;">
    <div id="progressBar" style="height:100%;background:{aksi.warna};border-radius:4px;width:0%;transition:width 0.3s;"></div>
  </div>
</div>
```

CATATAN: Checkbox dan catatan TIDAK tersimpan di database (belum ada). Data hilang saat refresh halaman. Ini by design untuk Opsi B. Nanti di Opsi A akan disimpan ke database.

Untuk sekarang, simpan state checkbox di JavaScript (object in-memory):
```javascript
const aksiState = {}; // { "RW_KEY": { checkedItems: [0,2,4], notes: {"0": "sudah dilakukan..."} } }
```

=== D) FUNGSI EXPORT EXCEL ===

exportAksiExcel(wilayahInfo, aksiData):
- Generate CSV/Excel dari data rencana aksi
- Kolom: No | Program | Kategori | Target | Deadline | Status | Catatan | PIC
- Nama file: "Rencana_Aksi_{Desa}_{RW}_{tanggal}.csv"
- Trigger download

```javascript
function exportAksiExcel(wilayahNama, statusKey) {
  const aksi = aksiConfig[statusKey];
  if (!aksi) return;
  
  let csv = "No,Program,Kategori,Target,Deadline,Status,Catatan,PIC\n";
  aksi.programs.forEach((p, i) => {
    csv += `${i+1},"${p.nama}","${p.kategori}","${p.target}","${p.deadline}","Belum mulai","","${aksi.pic}"\n`;
  });
  
  const blob = new Blob(["\ufeff" + csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `Rencana_Aksi_${wilayahNama.replace(/\s+/g, "_")}_${new Date().toISOString().split("T")[0]}.csv`;
  link.click();
}
```

=== E) FUNGSI PRINT ===

printAksi(wilayahInfo, aksiData):
- Buka window baru dengan layout print-friendly
- Header: logo + nama wilayah + status + tanggal generate
- Tabel checklist (checkbox kosong untuk diisi manual)
- Kolom: ☐ | No | Program | Kategori | Target | Deadline | Catatan
- Footer: PIC, target utama, pesan strategi

```javascript
function printAksi(wilayahNama, statusKey, wilayahDetail) {
  const aksi = aksiConfig[statusKey];
  if (!aksi) return;
  
  const w = window.open("", "_blank");
  w.document.write(`
    <html><head><title>Rencana Aksi - ${wilayahNama}</title>
    <style>
      body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
      h1 { font-size: 16px; margin: 0; }
      h2 { font-size: 13px; color: #666; margin: 4px 0 16px; }
      table { width: 100%; border-collapse: collapse; margin-top: 12px; }
      th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 11px; }
      th { background: #f5f5f5; font-weight: 600; }
      .header-info { display: flex; justify-content: space-between; margin-bottom: 12px; }
      .status-badge { padding: 4px 12px; border-radius: 4px; color: white; font-weight: 600; background: ${aksi.warna}; }
      .footer { margin-top: 20px; font-size: 11px; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
      @media print { body { padding: 0; } }
    </style></head><body>
    <div class="header-info">
      <div>
        <h1>Rencana Aksi — ${wilayahNama}</h1>
        <h2>${wilayahDetail || ''}</h2>
      </div>
      <div style="text-align:right;">
        <span class="status-badge">${statusKey}</span>
        <div style="font-size:10px;color:#888;margin-top:4px;">Dicetak: ${new Date().toLocaleDateString("id-ID")}</div>
      </div>
    </div>
    <div style="background:#f9f9f9;padding:8px 12px;border-radius:4px;margin-bottom:12px;">
      <strong>Target Utama:</strong> ${aksi.targetUtama}<br>
      <strong>PIC:</strong> ${aksi.pic} | <strong>Frekuensi:</strong> ${aksi.frekuensi}
    </div>
    <table>
      <thead>
        <tr><th style="width:30px;">☐</th><th style="width:25px;">No</th><th>Program</th><th>Kategori</th><th>Target</th><th>Deadline</th><th style="width:120px;">Catatan</th></tr>
      </thead>
      <tbody>
        ${aksi.programs.map((p, i) => `
          <tr><td style="text-align:center;">☐</td><td>${i+1}</td><td>${p.nama}</td><td>${p.kategori}</td><td>${p.target}</td><td>${p.deadline}</td><td></td></tr>
        `).join("")}
      </tbody>
    </table>
    <div class="footer">
      <strong>Pesan Strategi:</strong> ${aksi.pesan}
    </div>
    </body></html>
  `);
  w.document.close();
  w.print();
}
```

=== F) TOMBOL GENERATE RENCANA AKSI MASSAL ===

Di halaman utama (pemilu-dprd), tambahkan tombol di section status dashboard atau di toolbar:

```html
<button onclick="generateAksiMassal()" style="padding:6px 14px;background:#fe5000;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
  📋 Generate Rencana Aksi Semua RW
</button>
```

generateAksiMassal():
- Untuk semua RW yang visible (sesuai filter aktif):
  - Ambil status wilayah
  - Generate checklist dari aksiConfig
- Export ke satu file Excel besar:
  Kolom: Dapil | Kecamatan | Desa | RW | Status | DPT | Est. PKS | Program | Kategori | Target | Deadline | PIC | Catatan
- Setiap RW menghasilkan N baris (N = jumlah program sesuai status)
- Nama file: "Rencana_Aksi_Massal_{Dapil}_{tanggal}.csv"
- Ini menghasilkan "buku kerja lapangan" yang bisa dibagikan ke semua koordinator

```javascript
function generateAksiMassal() {
  // Ambil semua RW dari dptScope yang aktif
  const dptScope = getCurrentDptScope();
  if (!dptScope || !dptScope.rwRows.length) {
    alert("Data RW belum tersedia. Pastikan data DPT sudah di-load.");
    return;
  }
  
  let csv = "Dapil,Kecamatan,Desa,RW,Status,DPT,Est. PKS,No,Program,Kategori,Target,Deadline,PIC,Status Pelaksanaan,Catatan\n";
  
  dptScope.rwRows.forEach(rw => {
    const statusKey = rw.status?.key || "ZONA BERAT";
    const aksi = aksiConfig[statusKey];
    if (!aksi) return;
    
    aksi.programs.forEach((p, i) => {
      csv += [
        `"${state.currentDapil}"`,
        `"${toTitleCase(rw.district)}"`,
        `"${toTitleCase(rw.village)}"`,
        `"RW ${rw.rw}"`,
        `"${statusKey}"`,
        rw.totalDpt,
        Math.round(rw.pks?.totalVotes || 0),
        i + 1,
        `"${p.nama}"`,
        `"${p.kategori}"`,
        `"${p.target}"`,
        `"${p.deadline}"`,
        `"${aksi.pic}"`,
        `"Belum mulai"`,
        `""`
      ].join(",") + "\n";
    });
  });
  
  const blob = new Blob(["\ufeff" + csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `Rencana_Aksi_Massal_${state.currentDapil.replace(/\s+/g, "_")}_${new Date().toISOString().split("T")[0]}.csv`;
  link.click();
}
```

=== G) SUMMARY AKSI DI DASHBOARD STATUS ===

Di section status dashboard (5 card status), tambahkan info aksi:
Setiap card status, di bawah jumlah desa, tambahkan:
- "X program per RW" (jumlah program dari aksiConfig)
- "Prioritas: {aksi.prioritas}"

Dan tambahkan card summary baru di bawah status dashboard:

```html
<div style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;margin-top:10px;">
  <div style="display:flex;align-items:center;justify-content:space-between;">
    <div>
      <div style="font-size:11px;color:#fe5000;font-weight:500;text-transform:uppercase;letter-spacing:0.8px;">Ringkasan Rencana Aksi</div>
      <div style="font-size:13px;font-weight:500;margin-top:2px;">Total <span id="totalAksiRw">0</span> RW · <span id="totalAksiProgram">0</span> program kerja</div>
    </div>
    <button onclick="generateAksiMassal()" style="padding:6px 14px;background:#fe5000;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;white-space:nowrap;">
      📋 Export Rencana Aksi (Excel)
    </button>
  </div>
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-top:10px;font-size:11px;" id="aksiSummaryGrid">
    <!-- Di-render JS: per status, berapa RW, berapa total program -->
  </div>
</div>
```

renderAksiSummary(dptScope):
- Hitung per status: berapa RW, berapa total program (RW_count * programs.length)
- Update totalAksiRw, totalAksiProgram, aksiSummaryGrid
- Grid menampilkan: "JAGA KUAT: 12 RW · 72 program" dll

Langsung kerjakan semua. Jangan test.
```

---

## PROMPT 8: Fix Rencana Aksi + Persiapan Upgrade ke Database (Opsi A)

```
Cek dan fix fitur Rencana Aksi. Lalu siapkan fondasi untuk upgrade ke database nanti. Langsung kerjakan, JANGAN test.

CHECKLIST FIX:

1. Tab/panel "Rencana Aksi" muncul saat buka detail RW atau Desa
2. Checklist program terisi otomatis sesuai status wilayah
3. Checkbox bisa di-klik (centang/uncentang) — progress bar update
4. Klik program → expand catatan (toggle)
5. Tombol "Export Excel" bisa download CSV per wilayah
6. Tombol "Print" buka window baru dengan layout cetak
7. Tombol "Export Rencana Aksi (Excel)" massal di dashboard → download CSV semua RW
8. Ringkasan aksi di bawah status dashboard terisi (total RW, total program)
9. Jika data DPT belum ada, tampilkan pesan: "Data RW belum tersedia, load data DPT terlebih dahulu"

PERSIAPAN DATABASE (Opsi A — belum dijalankan, hanya buat file):

Buat migration file (JANGAN jalankan migrate, hanya buat file saja):

File: database/migrations/xxxx_create_rencana_aksis_table.php
```php
Schema::create('rencana_aksis', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('wilayah_key'); // format: "KECAMATAN__DESA__RW" atau "KECAMATAN__DESA"
    $table->string('wilayah_level'); // 'rw', 'desa', 'kecamatan'
    $table->string('wilayah_nama');
    $table->string('dapil');
    $table->string('kecamatan');
    $table->string('desa')->nullable();
    $table->string('rw')->nullable();
    $table->string('status_wilayah'); // JAGA KUAT, AMANKAN, dll
    $table->integer('program_index'); // index program di aksiConfig
    $table->string('program_nama');
    $table->string('program_kategori');
    $table->string('target');
    $table->string('deadline')->nullable();
    $table->string('pic')->nullable();
    $table->string('status_pelaksanaan')->default('belum_mulai'); // belum_mulai, berjalan, selesai, terkendala
    $table->text('catatan')->nullable();
    $table->date('tanggal_mulai')->nullable();
    $table->date('tanggal_selesai')->nullable();
    $table->string('bukti_foto')->nullable(); // path foto
    $table->uuid('updated_by')->nullable();
    $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();
    
    $table->index(['wilayah_key', 'program_index']);
    $table->index(['dapil', 'status_wilayah']);
    $table->index(['status_pelaksanaan']);
});
```

Buat model (JANGAN buat controller/route dulu):
File: app/Models/RencanaAksi.php
- HasUuids, HasFactory
- fillable: semua kolom di atas
- Casts: tanggal_mulai → date, tanggal_selesai → date
- Scope: scopeByDapil, scopeByStatus, scopeByWilayah

Buat juga catatan di komentar di model:
```php
// TODO: Upgrade Rencana Aksi ke database
// 1. Jalankan migration: php artisan migrate
// 2. Buat API endpoint: POST /api/rencana-aksi (simpan checklist)
// 3. Buat API endpoint: GET /api/rencana-aksi/{wilayah_key} (load checklist)
// 4. Update JS di pemilu-dprd.blade.php:
//    - Saat checkbox diklik → POST ke API
//    - Saat detail drawer dibuka → GET dari API
//    - Fallback ke aksiConfig jika API gagal
// 5. Buat dashboard progress: berapa % program selesai per dapil/kecamatan
```

Langsung buat file migration dan model. JANGAN jalankan migrate. JANGAN buat controller/route. Ini hanya persiapan untuk nanti.

Jangan test.
```
