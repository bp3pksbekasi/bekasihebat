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
