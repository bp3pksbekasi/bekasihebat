# Prompt Bertahap Trae — Modul Aspirasi & POKIR

Menu baru di sidebar group "STRATEGI & ANALISA". Tracking aspirasi warga → dewan PKS → SIPD → APBD → realisasi. Semi-otomatis: generate draft POKIR, 1-klik konfirmasi, auto-reminder, notif warga. Langsung buat, JANGAN test, JANGAN tanya.

---

## PROMPT 1: Migration + Model + Route

```
Buat modul Aspirasi & POKIR. Langsung buat, JANGAN test, JANGAN tanya.

PENTING: users.id = unsignedBigInteger (auto-increment), BUKAN uuid. Foreign key ke users harus match.

== 1. MIGRATION: create_aspirasis_table ==

```php
Schema::create('aspirasis', function (Blueprint $table) {
    $table->uuid('id')->primary();
    // Aspirasi
    $table->string('judul');
    $table->text('deskripsi');
    $table->string('kategori');              // infrastruktur, kesehatan, pendidikan, ekonomi, lingkungan, sosial
    $table->string('urgensi')->default('sedang'); // rendah, sedang, tinggi, mendesak
    // Lokasi
    $table->string('dapil');
    $table->string('kecamatan')->nullable();
    $table->string('desa')->nullable();
    $table->string('nomor_rw')->nullable();
    $table->string('alamat_detail')->nullable();
    $table->uuid('target_wilayah_id')->nullable();
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
    // Pelapor
    $table->string('nama_pelapor');
    $table->string('hp_pelapor')->nullable();
    $table->string('sumber');                // sisir_rw, reses, event, sapa_warga, langsung, website
    $table->uuid('sumber_id')->nullable();   // ID kegiatan/event sumber (opsional)
    // Assign dewan
    $table->unsignedBigInteger('assigned_dewan_id')->nullable();
    $table->foreign('assigned_dewan_id')->references('id')->on('anggota_dewans')->nullOnDelete();
    $table->dateTime('assigned_at')->nullable();
    // SIPD / POKIR
    $table->string('nomor_pokir')->nullable();     // nomor dari SIPD setelah diinput
    $table->dateTime('input_sipd_at')->nullable();  // kapan diinput ke SIPD
    $table->string('screenshot_sipd')->nullable();  // bukti screenshot
    // Status pipeline
    $table->string('status')->default('diterima');
    // diterima → assigned → input_sipd → verifikasi_bappeda → dianggarkan → terealisasi → ditolak
    $table->dateTime('verified_at')->nullable();
    $table->dateTime('dianggarkan_at')->nullable();
    $table->decimal('anggaran_nominal', 15, 0)->nullable(); // Rp
    $table->string('tahun_anggaran')->nullable();   // 2026, 2027
    $table->dateTime('realisasi_at')->nullable();
    $table->string('foto_realisasi')->nullable();
    // Draft POKIR (auto-generate)
    $table->text('draft_pokir')->nullable();        // teks format SIPD siap copy-paste
    // Feedback
    $table->text('feedback_warga')->nullable();     // catatan feedback ke warga
    $table->boolean('notif_warga_sent')->default(false);
    // Meta
    $table->text('catatan_internal')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['status']);
    $table->index(['dapil', 'status']);
    $table->index(['assigned_dewan_id']);
    $table->index(['kategori']);
    $table->index(['created_at']);
});
```

== 2. MIGRATION: create_aspirasi_logs_table ==

Timeline perubahan status (auto-track setiap perubahan).

```php
Schema::create('aspirasi_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('aspirasi_id');
    $table->foreign('aspirasi_id')->references('id')->on('aspirasis')->onDelete('cascade');
    $table->string('dari_status')->nullable();
    $table->string('ke_status');
    $table->string('aksi');                  // created, assigned, input_sipd, verified, dianggarkan, terealisasi, ditolak, reminder_sent
    $table->text('catatan')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['aspirasi_id', 'created_at']);
});
```

== 3. MIGRATION: create_aspirasi_reminders_table ==

Auto-reminder tracker.

```php
Schema::create('aspirasi_reminders', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('aspirasi_id');
    $table->foreign('aspirasi_id')->references('id')->on('aspirasis')->onDelete('cascade');
    $table->unsignedBigInteger('target_user_id');   // dewan yang diingatkan
    $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
    $table->string('channel')->default('system');    // system, wa (masa depan)
    $table->text('pesan');
    $table->boolean('is_read')->default(false);
    $table->timestamps();

    $table->index(['target_user_id', 'is_read']);
});
```

== 4. MODEL: Aspirasi ==

File: app/Models/Aspirasi.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'judul', 'deskripsi', 'kategori', 'urgensi',
        'dapil', 'kecamatan', 'desa', 'nomor_rw', 'alamat_detail', 'target_wilayah_id',
        'nama_pelapor', 'hp_pelapor', 'sumber', 'sumber_id',
        'assigned_dewan_id', 'assigned_at',
        'nomor_pokir', 'input_sipd_at', 'screenshot_sipd',
        'status', 'verified_at', 'dianggarkan_at', 'anggaran_nominal', 'tahun_anggaran',
        'realisasi_at', 'foto_realisasi',
        'draft_pokir', 'feedback_warga', 'notif_warga_sent',
        'catatan_internal', 'created_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime', 'input_sipd_at' => 'datetime',
        'verified_at' => 'datetime', 'dianggarkan_at' => 'datetime',
        'realisasi_at' => 'datetime', 'notif_warga_sent' => 'boolean',
    ];

    const STATUS_PIPELINE = [
        'diterima' => ['label' => 'Diterima', 'color' => '#0ea5e9', 'icon' => 'inbox', 'order' => 1],
        'assigned' => ['label' => 'Assigned Dewan', 'color' => '#2563eb', 'icon' => 'user-check', 'order' => 2],
        'input_sipd' => ['label' => 'Input SIPD', 'color' => '#7c3aed', 'icon' => 'database', 'order' => 3],
        'verifikasi_bappeda' => ['label' => 'Verifikasi BAPPEDA', 'color' => '#d97706', 'icon' => 'checkbox', 'order' => 4],
        'dianggarkan' => ['label' => 'Dianggarkan APBD', 'color' => '#16a34a', 'icon' => 'coin', 'order' => 5],
        'terealisasi' => ['label' => 'Terealisasi', 'color' => '#15803d', 'icon' => 'check', 'order' => 6],
        'ditolak' => ['label' => 'Ditolak/Tidak Layak', 'color' => '#dc2626', 'icon' => 'x', 'order' => 99],
    ];

    const KATEGORI_OPTIONS = [
        'infrastruktur' => 'Infrastruktur', 'kesehatan' => 'Kesehatan',
        'pendidikan' => 'Pendidikan', 'ekonomi' => 'Ekonomi',
        'lingkungan' => 'Lingkungan', 'sosial' => 'Sosial',
    ];

    const SUMBER_OPTIONS = [
        'sisir_rw' => 'Sisir RW', 'reses' => 'Reses DPRD', 'event' => 'Event',
        'sapa_warga' => 'Sapa Warga', 'langsung' => 'Input Langsung', 'website' => 'Website Publik',
    ];

    const URGENSI_OPTIONS = [
        'rendah' => ['label' => 'Rendah', 'color' => '#6b7280'],
        'sedang' => ['label' => 'Sedang', 'color' => '#d97706'],
        'tinggi' => ['label' => 'Tinggi', 'color' => '#dc2626'],
        'mendesak' => ['label' => 'Mendesak', 'color' => '#7f1d1d'],
    ];

    // Relationships
    public function targetWilayah() { return $this->belongsTo(TargetWilayah::class); }
    public function assignedDewan() { return $this->belongsTo(AnggotaDewan::class, 'assigned_dewan_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function logs() { return $this->hasMany(AspirasiLog::class)->orderByDesc('created_at'); }
    public function reminders() { return $this->hasMany(AspirasiReminder::class); }

    // Helpers
    public function getStatusConfigAttribute() { return self::STATUS_PIPELINE[$this->status] ?? self::STATUS_PIPELINE['diterima']; }

    public function getDurasiAttribute(): ?int
    {
        if (!$this->created_at) return null;
        return $this->created_at->diffInDays(now());
    }

    public function getDurasiSipdAttribute(): ?int
    {
        if (!$this->assigned_at || $this->status === 'diterima') return null;
        if ($this->input_sipd_at) return $this->assigned_at->diffInDays($this->input_sipd_at);
        return $this->assigned_at->diffInDays(now()); // belum diinput
    }

    // Auto-generate draft POKIR format SIPD
    public function generateDraftPokir(): string
    {
        $draft = "POKOK PIKIRAN DPRD\n";
        $draft .= "==========================================\n\n";
        $draft .= "Judul Usulan : {$this->judul}\n";
        $draft .= "Kategori     : " . (self::KATEGORI_OPTIONS[$this->kategori] ?? $this->kategori) . "\n";
        $draft .= "Lokasi       : {$this->desa}, Kec. {$this->kecamatan}, Kab. Bekasi\n";
        if ($this->nomor_rw) $draft .= "RW           : {$this->nomor_rw}\n";
        if ($this->alamat_detail) $draft .= "Detail Lokasi: {$this->alamat_detail}\n";
        $draft .= "\nUraian Permasalahan:\n{$this->deskripsi}\n";
        $draft .= "\nSumber Aspirasi: " . (self::SUMBER_OPTIONS[$this->sumber] ?? $this->sumber) . "\n";
        $draft .= "Pelapor      : {$this->nama_pelapor}\n";
        $draft .= "Dapil        : {$this->dapil}\n";
        $draft .= "\n==========================================\n";
        $draft .= "Diusulkan oleh Anggota DPRD PKS Kab. Bekasi\n";

        return $draft;
    }

    // Update status + log
    public function updateStatus(string $newStatus, ?string $catatan = null, ?int $userId = null): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => $newStatus]);

        AspirasiLog::create([
            'aspirasi_id' => $this->id,
            'dari_status' => $oldStatus,
            'ke_status' => $newStatus,
            'aksi' => $newStatus,
            'catatan' => $catatan,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    // Scopes
    public function scopeByDapil($q, $v) { return $q->where('dapil', $v); }
    public function scopeByStatus($q, $v) { return $q->where('status', $v); }
    public function scopeBelumAssign($q) { return $q->whereNull('assigned_dewan_id'); }
    public function scopeStuck($q, int $days = 14) {
        return $q->where('status', 'assigned')
            ->where('assigned_at', '<', now()->subDays($days));
    }
}
```

== 5. MODEL: AspirasiLog ==

File: app/Models/AspirasiLog.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AspirasiLog extends Model
{
    use HasUuids;

    protected $fillable = ['aspirasi_id', 'dari_status', 'ke_status', 'aksi', 'catatan', 'user_id'];

    public function aspirasi() { return $this->belongsTo(Aspirasi::class); }
    public function user() { return $this->belongsTo(User::class); }
}
```

== 6. MODEL: AspirasiReminder ==

File: app/Models/AspirasiReminder.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AspirasiReminder extends Model
{
    use HasUuids;

    protected $fillable = ['aspirasi_id', 'target_user_id', 'channel', 'pesan', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public function aspirasi() { return $this->belongsTo(Aspirasi::class); }
    public function targetUser() { return $this->belongsTo(User::class, 'target_user_id'); }
}
```

== 7. ROUTE ==

Route::get('/aspirasi', App\Livewire\Aspirasi\Index::class)->middleware(['auth'])->name('aspirasi.index');

== 8. SIDEBAR ==

Tambahkan di group STRATEGI & ANALISA (setelah Bedah Dapil):

```html
<a href="{{ route('aspirasi.index') }}" wire:navigate
   class="...sesuai pattern sidebar yang ada...">
    <i class="ti ti-message-chatbot" style="font-size:18px;"></i>
    <span>Aspirasi & POKIR</span>
    @php $belumAssign = \App\Models\Aspirasi::belumAssign()->count(); @endphp
    @if($belumAssign > 0)
        <span class="...badge pattern...">{{ $belumAssign }}</span>
    @endif
</a>
```

Jalankan: php artisan migrate
Jangan test.
```

---

## PROMPT 2: Livewire Component + View

```
Buat Livewire component untuk halaman Aspirasi & POKIR. Langsung buat, JANGAN test.

== File: app/Livewire/Aspirasi/Index.php ==

Properties:
- $selectedDapil, $filterStatus, $filterKategori, $search
- $selectedAspirasiId (untuk detail panel kanan)

Form input aspirasi:
- $showForm, $editId
- $fJudul, $fDeskripsi, $fKategori, $fUrgensi
- $fDapil, $fKecamatan, $fDesa, $fRw, $fAlamat
- $fNamaPelapor, $fHpPelapor, $fSumber

Form 1-klik konfirmasi SIPD:
- $showKonfirmasiSipd, $konfirmasiAspirasiId
- $fNomorPokir, $fScreenshotSipd

Form update status:
- $showUpdateStatus, $updateAspirasiId
- $fNewStatus, $fAnggaranNominal, $fTahunAnggaran, $fFotoRealisasi, $fCatatan

Computed:

getPipelineSummaryProperty():
- Count per status: diterima, assigned, input_sipd, verifikasi_bappeda, dianggarkan, terealisasi
- Total aspirasi
- Belum assign count
- Stuck count (assigned > 14 hari tapi belum input SIPD)

getAspirasiListProperty():
- Paginated, filtered by dapil, status, kategori, search
- With assignedDewan, logs
- OrderBy: belum assign first, then by created_at desc

getDetailProperty():
- If selectedAspirasiId → load aspirasi with logs, reminders, dewan
- Timeline dari logs

getKinerjaDewanProperty():
- Per anggota dewan: count per status, skor kinerja
- Skor = (input_sipd/assigned * 40) + (dianggarkan/input_sipd * 30) + (terealisasi/dianggarkan * 30)
- Sort by skor desc

getSumberBreakdownProperty():
- Count per sumber (sisir_rw, reses, event, sapa_warga, langsung, website)

getKategoriBreakdownProperty():
- Count per kategori

getStuckAspirasiProperty():
- Aspirasi yang assigned > 14 hari tapi belum input SIPD

Methods:

simpanAspirasi():
1. Create aspirasi
2. Auto-generate draft POKIR → save ke draft_pokir field
3. Log: aksi=created
4. Jika dapil match anggota dewan → auto-suggest assign

assignDewan($aspirasiId, $dewanId):
1. Update assigned_dewan_id + assigned_at
2. Update status → 'assigned'
3. Log: aksi=assigned
4. Create reminder untuk dewan: "Anda mendapat aspirasi baru untuk diinput ke SIPD"

konfirmasiInputSipd():
1. Validate nomor_pokir required
2. Update: nomor_pokir, input_sipd_at=now, screenshot_sipd (jika ada)
3. Update status → 'input_sipd'
4. Log: aksi=input_sipd, catatan="POKIR: {nomor_pokir}"

updateStatus($aspirasiId):
1. Update status sesuai $fNewStatus
2. Jika dianggarkan: simpan anggaran_nominal + tahun_anggaran
3. Jika terealisasi: simpan foto_realisasi + realisasi_at
4. Log status change
5. Jika terealisasi: auto-set feedback_warga

copyDraftPokir($aspirasiId):
- Generate draft POKIR → copy ke clipboard via JS
- Flash message: "Draft POKIR sudah dicopy, paste ke SIPD"

bukaSipd():
- Redirect ke URL SIPD (new tab): https://sipd.kemendagri.go.id

kirimReminderManual($aspirasiId):
- Create reminder untuk dewan assigned
- Pesan: "Aspirasi '{judul}' sudah {X} hari belum diinput SIPD. Segera input."

== VIEW ==

Layout: sidebar admin. Accent: #0ea5e9.

BARIS 1: Header + filter + tombol "Input aspirasi"

BARIS 2: Pipeline visual (6 dots connected by lines, count per status, total, stuck alert)

BARIS 3: 3 tab (Daftar Aspirasi, Kinerja Dewan, Tampilan Publik)

TAB DAFTAR ASPIRASI:
- 2 kolom (7fr + 5fr)
- Kiri: list aspirasi (card per item)
  - Status badge warna, kategori badge, urgensi badge
  - Judul + deskripsi 1 baris
  - Nama pelapor, nama dewan (jika assigned), sumber, tanggal
  - Jika terealisasi: anggaran + bukti
  - Jika belum assign: tombol "Assign ke dewan" (dropdown dewan per dapil)
  - Filter + search + paginated
- Kanan: detail aspirasi (muncul saat klik item)
  - Header: judul, status badge, urgensi
  - Info pelapor + lokasi
  - Deskripsi lengkap
  - Dewan assigned (nama + foto + dapil)
  - Draft POKIR (textarea readonly, tombol "Copy ke clipboard")
  - Tombol "Buka SIPD" (link external new tab)
  - Timeline status changes (dari logs) — timestamp + user + catatan
  - Tombol aksi sesuai status saat ini:
    - Status diterima → "Assign ke dewan"
    - Status assigned → "Konfirmasi sudah input SIPD" (input nomor POKIR + screenshot)
    - Status input_sipd → "Update: Diverifikasi BAPPEDA"
    - Status verifikasi → "Update: Dianggarkan" (input nominal + tahun)
    - Status dianggarkan → "Update: Terealisasi" (upload foto bukti)
  - Tombol "Kirim reminder ke dewan" (jika stuck)

TAB KINERJA DEWAN:
- Ranking card per anggota dewan
- Skor kinerja (hijau ≥70%, amber 30-69%, merah <30%)
- 6 angka pipeline per dewan (Diterima → Assigned → SIPD → Verified → APBD → Realisasi)
- Medal emoji untuk top 3
- Alert untuk yang rendah

TAB TAMPILAN PUBLIK (preview):
- Preview apa yang tampil di website publik
- Counter besar: "X Aspirasi Diperjuangkan"
- 3 angka: masuk SIPD, dianggarkan, terealisasi
- Success stories: aspirasi yang terealisasi (judul + dewan + nominal)
- Tombol "Sampaikan Aspirasi" untuk warga

FORM INPUT ASPIRASI (drawer kanan):
- Judul, Deskripsi (textarea)
- Kategori (select 6), Urgensi (select 4)
- Lokasi: dapil→kecamatan→desa→RW (cascading), alamat detail
- Pelapor: nama + HP
- Sumber (select: Sisir RW, Reses, Event, Sapa Warga, Langsung, Website)
- Tombol Simpan → auto-generate draft POKIR

FORM KONFIRMASI SIPD (inline expand di detail):
- Input: Nomor POKIR dari SIPD
- Upload: Screenshot SIPD (opsional)
- Tombol: "Konfirmasi — Sudah Input SIPD" (1 klik)
- Auto: timestamp, status berubah, log tercatat

Langsung buat semua. Jangan test.
```

---

## PROMPT 3: Auto-Reminder + Halaman Publik + Integrasi

```
Tambahkan auto-reminder, halaman publik, dan integrasi ke modul lain. Langsung buat, JANGAN test.

== 1. COMMAND: Auto-Reminder ==

Buat scheduled command yang jalan harian:
File: app/Console/Commands/AspirasiReminderCommand.php

```php
php artisan aspirasi:reminder
```

Logic:
- Cari aspirasi status='assigned' dan assigned_at > 7 hari lalu DAN belum ada reminder dalam 7 hari terakhir
- Buat AspirasiReminder untuk setiap dewan yang punya aspirasi stuck
- Pesan: "Bapak/Ibu {nama dewan}, ada {X} aspirasi yang belum diinput ke SIPD. Tertua: {judul} ({Y hari lalu). Mohon segera diinput."

Cari juga:
- status='input_sipd' > 30 hari → reminder ke admin: "Cek SIPD: {X} aspirasi belum diverifikasi BAPPEDA"
- status='dianggarkan' > 90 hari → reminder ke admin: "Cek realisasi: {X} aspirasi sudah dianggarkan tapi belum terealisasi"

Register di schedule (app/Console/Kernel.php atau routes/console.php):
```php
Schedule::command('aspirasi:reminder')->dailyAt('08:00');
```

== 2. NOTIFIKASI BELL (di navbar) ==

Di sidebar/navbar admin, tambahkan icon bell yang menunjukkan jumlah reminder unread:

```php
$unreadReminders = auth()->check()
    ? AspirasiReminder::where('target_user_id', auth()->id())->where('is_read', false)->count()
    : 0;
```

Tampilkan badge merah di icon bell. Klik → dropdown list reminders.

== 3. HALAMAN PUBLIK: Aspirasi Warga ==

Route (publik, tanpa auth):
```php
Route::get('/aspirasi-warga', App\Livewire\PublicSite\AspirasiWarga::class)->name('public.aspirasi');
```

Component: app/Livewire/PublicSite/AspirasiWarga.php

Computed:
- totalAspirasi, inputSipd, dianggarkan, terealisasi (counts)
- successStories: aspirasi terealisasi, ordered by realisasi_at desc, limit 5
- perKategori: count per kategori (pie chart data)

View:
- Counter besar: "X Aspirasi Telah Diperjuangkan oleh Dewan PKS"
- 3 metric: masuk SIPD, dianggarkan APBD, terealisasi
- Success stories: card per aspirasi terealisasi (judul, lokasi, dewan nama, nominal APBD, foto bukti)
- Breakdown kategori (badges)
- Form "Sampaikan Aspirasi Anda" (nama, HP, dapil, desa, RW, kategori, deskripsi) → create aspirasi sumber='website'
- TIDAK tampilkan: aspirasi yang belum terealisasi, nama pelapor asli, internal notes

Tambahkan link di navbar publik:
```html
<a href="{{ route('public.aspirasi') }}">Aspirasi</a>
```

Tambahkan section di homepage (di atas CTA daftar):
```html
<section>
    Counter: "X aspirasi diperjuangkan" + 3 angka
    2-3 success story terbaru
    Tombol: "Sampaikan Aspirasi →"
</section>
```

== 4. INTEGRASI: Sisir RW → Aspirasi ==

Di modul Sisir RW, saat catat kegiatan dan ada aspirasi/keluhan warga:
- Tambahkan tombol "Jadikan Aspirasi" di kegiatan yang punya catatan/keluhan
- Klik → auto-fill form aspirasi: sumber='sisir_rw', sumber_id=kegiatan_id, dapil/desa/rw dari kegiatan, nama pelapor dari tokoh_ditemui

== 5. INTEGRASI: Event → Aspirasi ==

Di modul Event peserta:
- Saat input peserta, ada field "Aspirasi" (opsional)
- Jika diisi → auto-create aspirasi sumber='event'

== 6. INTEGRASI: Dashboard Admin ==

Tambahkan KPI di dashboard:
```php
$aspirasiTotal = Aspirasi::count();
$aspirasiSipd = Aspirasi::where('status', 'input_sipd')->orWhere('status', 'verifikasi_bappeda')->count();
$aspirasiRealisasi = Aspirasi::where('status', 'terealisasi')->count();
$aspirasiStuck = Aspirasi::stuck(14)->count();
```

Tampilkan di section "Ringkasan Operasional" atau card baru.

Langsung buat semua. Jangan test.
```

---

## PROMPT 4: Fix & Polish

```
Fix modul Aspirasi & POKIR. Langsung fix, JANGAN test.

CHECKLIST:

MIGRATION & MODEL:
1. Migration aspirasis, aspirasi_logs, aspirasi_reminders berhasil
2. Foreign key ke users = unsignedBigInteger (bukan uuid)
3. Foreign key ke anggota_dewans valid
4. Model Aspirasi: generateDraftPokir() menghasilkan teks format SIPD
5. Model Aspirasi: updateStatus() auto-create log

HALAMAN ADMIN:
6. Menu "Aspirasi & POKIR" muncul di sidebar
7. Badge belum-assign muncul jika ada
8. Pipeline visual 6 tahap + count + stuck alert
9. Tab Daftar: list aspirasi filtered, paginated
10. Form input aspirasi → simpan → draft POKIR auto-generate
11. Assign dewan (dropdown per dapil) → status berubah → log tercatat
12. Konfirmasi SIPD: input nomor POKIR → 1 klik → status berubah + timestamp
13. Tombol "Copy draft POKIR" → clipboard + flash message
14. Tombol "Buka SIPD" → new tab sipd.kemendagri.go.id
15. Update status (verified/dianggarkan/terealisasi) → form sesuai konteks
16. Upload foto realisasi berfungsi
17. Timeline logs di detail panel (tanggal + user + aksi + catatan)
18. Tab Kinerja Dewan: ranking + skor + 6 angka pipeline
19. Stuck alert: aspirasi > 14 hari belum input SIPD

SEMI-OTOMATIS:
20. Draft POKIR auto-generate dari data aspirasi (format text SIPD)
21. 1-klik konfirmasi (bukan form panjang)
22. Reminder command jalan: php artisan aspirasi:reminder
23. Bell notification badge di navbar (unread count)
24. Reminder dropdown list berfungsi

HALAMAN PUBLIK:
25. /aspirasi-warga load tanpa login
26. Counter + success stories tampil
27. Form "Sampaikan Aspirasi" → create aspirasi sumber='website'
28. TIDAK tampilkan data internal (status selain terealisasi, catatan internal)
29. Link di navbar publik + section di homepage

INTEGRASI:
30. Sisir RW: tombol "Jadikan Aspirasi" berfungsi
31. Dashboard admin: KPI aspirasi tampil

Langsung fix. Jangan test.
```
