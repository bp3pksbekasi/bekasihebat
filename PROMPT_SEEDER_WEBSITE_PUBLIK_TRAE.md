# Prompt Trae — Seeder Data Dummy Website Publik

Buat migration tabel yang belum ada (berita, galeri) + seeder data dummy khusus untuk website publik agar terlihat hidup saat ujicoba. Terpisah dari seeder backend. Langsung buat, JANGAN test, JANGAN tanya.

---

```
== 1. MIGRATION: create_beritas_table ==

Artikel berita/kegiatan untuk website publik.

```php
Schema::create('beritas', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('judul');
    $table->string('slug')->unique();
    $table->text('ringkasan')->nullable();         // 2-3 kalimat untuk card
    $table->longText('konten');                     // HTML content
    $table->string('thumbnail')->nullable();        // path gambar
    $table->string('kategori')->default('kegiatan'); // kegiatan, program, dakwah, opini, pengumuman
    $table->string('penulis')->nullable();
    $table->boolean('is_featured')->default(false); // tampil di homepage
    $table->boolean('is_published')->default(true);
    $table->dateTime('published_at');
    $table->integer('views')->default(0);
    // Link ke event/program (opsional)
    $table->uuid('event_id')->nullable();
    $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['kategori', 'is_published']);
    $table->index(['published_at']);
    $table->index(['is_featured']);
});
```

== 2. MIGRATION: create_galeris_table ==

Foto/video dokumentasi kegiatan.

```php
Schema::create('galeris', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->string('file_path');                    // path gambar
    $table->string('thumbnail')->nullable();
    $table->string('tipe')->default('foto');        // foto, video
    $table->string('kategori')->default('kegiatan'); // kegiatan, event, baksos, senam, rki, dakwah
    $table->string('lokasi')->nullable();           // desa/kecamatan
    $table->date('tanggal')->nullable();
    $table->boolean('is_featured')->default(false); // tampil di homepage grid
    $table->boolean('is_published')->default(true);
    $table->integer('urutan')->default(0);
    // Link ke event (opsional)
    $table->uuid('event_id')->nullable();
    $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['kategori', 'is_published']);
    $table->index(['is_featured']);
});
```

== 3. MODEL: Berita ==

File: app/Models/Berita.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'konten', 'thumbnail',
        'kategori', 'penulis', 'is_featured', 'is_published',
        'published_at', 'views', 'event_id', 'created_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    const KATEGORI_OPTIONS = [
        'kegiatan' => 'Kegiatan',
        'program' => 'Program',
        'dakwah' => 'Dakwah',
        'opini' => 'Opini',
        'pengumuman' => 'Pengumuman',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($berita) {
            if (!$berita->slug) {
                $berita->slug = Str::slug($berita->judul);
                $i = 1;
                while (static::where('slug', $berita->slug)->exists()) {
                    $berita->slug = Str::slug($berita->judul) . '-' . $i++;
                }
            }
        });
    }

    public function event() { return $this->belongsTo(Event::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopePublished($q) { return $q->where('is_published', true)->where('published_at', '<=', now()); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeByKategori($q, $v) { return $q->where('kategori', $v); }
}
```

== 4. MODEL: Galeri ==

File: app/Models/Galeri.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'judul', 'deskripsi', 'file_path', 'thumbnail', 'tipe',
        'kategori', 'lokasi', 'tanggal', 'is_featured', 'is_published',
        'urutan', 'event_id', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    const KATEGORI_OPTIONS = [
        'kegiatan' => 'Kegiatan', 'event' => 'Event', 'baksos' => 'Baksos',
        'senam' => 'Senam PKS', 'rki' => 'RKI', 'dakwah' => 'Dakwah',
    ];

    public function event() { return $this->belongsTo(Event::class); }
    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
}
```

== 5. SEEDER: PublicWebsiteSeeder ==

File: database/seeders/PublicWebsiteSeeder.php

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Member;
use App\Models\Kehadiran;
use App\Models\TargetWilayah;
use App\Models\DataRw;

class PublicWebsiteSeeder extends Seeder
{
    private function randomHP()
    {
        $prefix = ['0812','0813','0857','0858','0878','0821','0822'];
        return $prefix[array_rand($prefix)] . rand(10000000, 99999999);
    }

    private $namaPool = [
        'Ahmad Rizki', 'Siti Rahmawati', 'Dedi Hermawan', 'Rina Agustina',
        'Yusuf Hakim', 'Nuraini', 'Asep Hidayat', 'Dewi Kartika',
        'Maman Abdurahman', 'Euis Komariah', 'Jajang Sudrajat', 'Tuti Sukaesih',
        'Agus Wahyudi', 'Cucu Susilawati', 'Nandang Kosim', 'Ai Rohmah',
        'Bambang Sutejo', 'Marlina Sari', 'Hendra Setiawan', 'Fitri Amalia',
        'Usman Efendi', 'Sri Wahyuni', 'Rudi Permana', 'Lestari Dewi',
        'Endang Supriatna', 'Wati Mulyani', 'Cecep Nurjaman', 'Yanti Herlina',
        'Koswara', 'Nurhayati', 'Tatang Iskandar', 'Imas Rosidah',
        'Dadan Junaedi', 'Eneng Nurjanah', 'Ade Suryana', 'Nining Herlina',
        'Iwan Gunawan', 'Sumiati', 'Fajar Sidik', 'Ratna Komala',
    ];

    public function run()
    {
        $this->command->info('=== Seeding data website publik ===');

        // ================================================
        // 1. BERITA / ARTIKEL (20 artikel)
        // ================================================
        $this->command->info('1. Seeding berita...');

        $artikels = [
            // FEATURED (3)
            [
                'judul' => 'DPD PKS Kabupaten Bekasi Gelar Musyawarah Kerja Daerah 2026',
                'kategori' => 'kegiatan', 'is_featured' => true,
                'ringkasan' => 'Musyawarah Kerja Daerah DPD PKS Kabupaten Bekasi dihadiri seluruh pengurus DPC dan perwakilan kader dari 7 dapil. Agenda utama adalah penetapan target suara 350.000 dan roadmap program kerja menuju Pemilu 2029.',
                'published_at' => '2026-05-28 10:00',
                'views' => 1240,
            ],
            [
                'judul' => 'Baksos Kesehatan Gratis di Setu: 200 Warga Mendapat Layanan',
                'kategori' => 'kegiatan', 'is_featured' => true,
                'ringkasan' => 'DPD PKS Kabupaten Bekasi bersama tim medis relawan menggelar bakti sosial kesehatan gratis di Kecamatan Setu. Sebanyak 200 warga mendapat layanan pemeriksaan kesehatan, pengobatan gratis, dan pembagian sembako.',
                'published_at' => '2026-05-25 09:00',
                'views' => 890,
            ],
            [
                'judul' => 'Program Senam PKS Kini Hadir di 18 Desa, Ribuan Warga Antusias',
                'kategori' => 'program', 'is_featured' => true,
                'ringkasan' => 'Kelompok Senam Nusantara (KSN) atau Senam PKS terus berkembang di Kabupaten Bekasi. Saat ini sudah ada 18 titik senam aktif di berbagai desa. Target akhir tahun: minimal 1 titik per desa (187 desa).',
                'published_at' => '2026-05-20 08:00',
                'views' => 1560,
            ],
            // KEGIATAN (7)
            [
                'judul' => 'Workshop UMKM Digital Angkatan 2 Sukses Digelar di Cikarang',
                'kategori' => 'kegiatan',
                'ringkasan' => 'Pelatihan digitalisasi UMKM angkatan kedua berhasil melatih 30 pelaku usaha mikro di Cikarang Barat. Peserta dibekali kemampuan pemasaran online, pengelolaan media sosial, dan pembukuan digital.',
                'published_at' => '2026-05-18 14:00',
                'views' => 432,
            ],
            [
                'judul' => 'Anggota DPRD PKS Reses di Dapil 3: Jembatan Rusak Jadi Prioritas',
                'kategori' => 'kegiatan',
                'ringkasan' => 'Anggota DPRD Kabupaten Bekasi dari Fraksi PKS melaksanakan reses di Dapil 3. Warga menyampaikan aspirasi terkait infrastruktur jembatan yang rusak dan drainase yang perlu diperbaiki.',
                'published_at' => '2026-05-15 11:00',
                'views' => 678,
            ],
            [
                'judul' => 'Safari Dakwah DPD ke 23 Kecamatan Capai Tahap ke-8',
                'kategori' => 'dakwah',
                'ringkasan' => 'Program Safari Dakwah DPD PKS Kabupaten Bekasi terus berjalan. Sudah 8 dari 23 kecamatan dikunjungi. Setiap kunjungan menggelar kajian, silaturahmi tokoh, dan diskusi program.',
                'published_at' => '2026-05-12 09:00',
                'views' => 345,
            ],
            [
                'judul' => 'Pelatihan Kader Angkatan 3: 25 Peserta Siap Jadi Pelopor',
                'kategori' => 'kegiatan',
                'ringkasan' => 'Diklat kader angkatan 3 resmi ditutup setelah 3 hari pelatihan intensif. 25 peserta dinyatakan lulus dan naik jenjang dari Penggerak ke Pelopor.',
                'published_at' => '2026-05-08 16:00',
                'views' => 523,
            ],
            [
                'judul' => 'RKI Cijengkol Gelar Posyandu Gratis untuk Balita dan Ibu Hamil',
                'kategori' => 'program',
                'ringkasan' => 'Rumah Keluarga Indonesia (RKI) di RW 003 Cijengkol menggelar posyandu gratis. 35 balita dan 12 ibu hamil mendapat layanan pemeriksaan, imunisasi, dan konsultasi gizi.',
                'published_at' => '2026-05-05 10:00',
                'views' => 287,
            ],
            [
                'judul' => 'Jalan Santai Bersama Warga Tambun Selatan Diikuti 300 Peserta',
                'kategori' => 'kegiatan',
                'ringkasan' => 'Kegiatan jalan santai sehat bersama warga Tambun Selatan berlangsung meriah. Sekitar 300 peserta dari berbagai RW mengikuti rute sepanjang 5 km. Ada doorprize dan cek kesehatan gratis.',
                'published_at' => '2026-04-28 07:00',
                'views' => 892,
            ],
            [
                'judul' => 'Bazar Sembako Murah Jelang Ramadhan di Cikarang Selatan',
                'kategori' => 'kegiatan',
                'ringkasan' => 'DPC PKS Cikarang Selatan menggelar bazar sembako murah menjelang Ramadhan. Minyak goreng, beras, gula, dan telur dijual dengan harga di bawah pasaran. Antusiasme warga sangat tinggi.',
                'published_at' => '2026-04-22 08:00',
                'views' => 1102,
            ],
            // PROGRAM (3)
            [
                'judul' => 'Sapa Warga: 12.000 Kontak Terkumpul dari 342 RW',
                'kategori' => 'program',
                'ringkasan' => 'Program Sapa Warga terus menunjukkan progress positif. Per Mei 2026, sudah 12.000 kontak warga terkumpul dari 342 RW di seluruh Kabupaten Bekasi. Target akhir: 200 kontak per RW.',
                'published_at' => '2026-05-01 10:00',
                'views' => 234,
            ],
            [
                'judul' => 'KORWE Terbentuk di 30 RW, Infrastruktur Pemenangan Mulai Kokoh',
                'kategori' => 'program',
                'ringkasan' => 'Pembentukan Koordinator RW (KORWE) terus berjalan. Per akhir Mei 2026, sudah 30 RW memiliki KORWE aktif. Target 2026 adalah 15% dari 1.248 RW prioritas.',
                'published_at' => '2026-04-15 14:00',
                'views' => 456,
            ],
            [
                'judul' => 'Kader PKS Bekasi Tumbuh: 2.000 Kader Aktif Tersebar di 7 Dapil',
                'kategori' => 'program',
                'ringkasan' => 'Database kader DPD PKS Kabupaten Bekasi mencatat lebih dari 2.000 kader aktif yang tersebar di 7 dapil. Kaderisasi terus dilakukan melalui pelatihan, UPA RW, dan program dakwah.',
                'published_at' => '2026-04-10 09:00',
                'views' => 389,
            ],
            // DAKWAH (2)
            [
                'judul' => 'Kajian Subuh Berjamaah: Puluhan Warga Hadiri di Masjid Al-Falah',
                'kategori' => 'dakwah',
                'ringkasan' => 'Program kajian subuh berjamaah di Masjid Al-Falah Cikarang Barat diikuti puluhan warga secara rutin setiap Ahad pagi. Kajian dibawakan oleh ustadz dari jaringan dakwah PKS.',
                'published_at' => '2026-04-05 06:00',
                'views' => 178,
            ],
            [
                'judul' => 'Tabligh Akbar Memperingati Isra Mi\'raj di Tambun Utara',
                'kategori' => 'dakwah',
                'ringkasan' => 'DPC PKS Tambun Utara bersama masyarakat menggelar tabligh akbar memperingati Isra Mi\'raj. Acara dihadiri ratusan jamaah dan diisi oleh penceramah dari DPD.',
                'published_at' => '2026-03-28 19:00',
                'views' => 567,
            ],
            // OPINI (2)
            [
                'judul' => 'Opini: Pentingnya Infrastruktur Digital untuk Pemenangan Pemilu 2029',
                'kategori' => 'opini',
                'ringkasan' => 'Di era digital, pemenangan pemilu tidak bisa mengandalkan cara konvensional saja. DPD PKS Kabupaten Bekasi mengembangkan platform dashboard terintegrasi untuk memantau seluruh program.',
                'published_at' => '2026-03-20 10:00',
                'views' => 234,
            ],
            [
                'judul' => 'Opini: Membangun Bekasi dari RW — Strategi Bottom-Up yang Terbukti',
                'kategori' => 'opini',
                'ringkasan' => 'Pendekatan membangun dari level paling bawah (RW) terbukti efektif. Program RKI, Senam PKS, dan Sapa Warga adalah contoh nyata strategi bottom-up PKS di Kabupaten Bekasi.',
                'published_at' => '2026-03-15 11:00',
                'views' => 312,
            ],
            // PENGUMUMAN (2)
            [
                'judul' => 'Pendaftaran Anggota Baru Dibuka — Daftar dan Dapatkan Kartu Digital',
                'kategori' => 'pengumuman',
                'ringkasan' => 'DPD PKS Kabupaten Bekasi membuka pendaftaran anggota baru melalui website Bekasi Hebat. Setiap pendaftar akan mendapatkan kartu anggota digital dengan QR code.',
                'published_at' => '2026-05-01 08:00',
                'views' => 1890,
            ],
            [
                'judul' => 'Jadwal Senam PKS Bulan Juni 2026 di Seluruh Kabupaten Bekasi',
                'kategori' => 'pengumuman',
                'ringkasan' => 'Berikut jadwal lengkap senam PKS (KSN) bulan Juni 2026 di berbagai titik di Kabupaten Bekasi. Kegiatan gratis dan terbuka untuk seluruh warga.',
                'published_at' => '2026-05-30 07:00',
                'views' => 743,
            ],
        ];

        foreach ($artikels as $a) {
            // Generate konten HTML dari ringkasan
            $paragraphs = [];
            $paragraphs[] = '<p>' . $a['ringkasan'] . '</p>';
            $paragraphs[] = '<p>Kegiatan ini merupakan bagian dari program kerja DPD PKS Kabupaten Bekasi dalam rangka memperkuat jaringan dan pelayanan kepada masyarakat menuju Pemilu 2029. Dengan target 350.000 suara, setiap kegiatan dirancang untuk menjangkau warga di level paling bawah yaitu RW.</p>';
            $paragraphs[] = '<p>"Kami berkomitmen untuk terus hadir di tengah masyarakat. Setiap program yang kami jalankan bukan sekadar seremonial, tapi benar-benar memberikan manfaat nyata bagi warga," ujar salah satu pengurus DPD.</p>';
            $paragraphs[] = '<p>Warga yang ingin berpartisipasi dalam kegiatan serupa dapat mendaftar melalui website Bekasi Hebat atau menghubungi pengurus DPC di masing-masing kecamatan. Pendaftaran anggota baru juga dibuka secara online dengan fasilitas kartu anggota digital.</p>';

            Berita::create([
                'judul' => $a['judul'],
                'kategori' => $a['kategori'],
                'ringkasan' => $a['ringkasan'],
                'konten' => implode("\n", $paragraphs),
                'penulis' => ['Tim Media DPD', 'Komdigi Bekasi Hebat', 'Redaksi'][rand(0,2)],
                'is_featured' => $a['is_featured'] ?? false,
                'is_published' => true,
                'published_at' => Carbon::parse($a['published_at']),
                'views' => $a['views'],
            ]);
        }
        $this->command->info('   → 20 artikel berita dibuat');

        // ================================================
        // 2. GALERI (24 foto)
        // ================================================
        $this->command->info('2. Seeding galeri...');

        $galeriData = [
            // Featured (6)
            ['judul' => 'Musyawarah Kerja Daerah DPD 2026', 'kategori' => 'event', 'tanggal' => '2026-05-28', 'is_featured' => true, 'lokasi' => 'Sekretariat DPD'],
            ['judul' => 'Baksos Kesehatan Gratis di Setu', 'kategori' => 'baksos', 'tanggal' => '2026-05-25', 'is_featured' => true, 'lokasi' => 'Kecamatan Setu'],
            ['judul' => 'Senam PKS Pagi di Lapangan Cijengkol', 'kategori' => 'senam', 'tanggal' => '2026-06-01', 'is_featured' => true, 'lokasi' => 'Cijengkol, Setu'],
            ['judul' => 'Pelatihan Kader Angkatan 3', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-08', 'is_featured' => true, 'lokasi' => 'Cikarang'],
            ['judul' => 'RKI Posyandu Balita di RW 003', 'kategori' => 'rki', 'tanggal' => '2026-05-05', 'is_featured' => true, 'lokasi' => 'Cijengkol, Setu'],
            ['judul' => 'Jalan Santai 300 Peserta di Tambun', 'kategori' => 'event', 'tanggal' => '2026-04-28', 'is_featured' => true, 'lokasi' => 'Tambun Selatan'],
            // Regular (18)
            ['judul' => 'Reses DPRD di Dapil 3', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-15', 'lokasi' => 'Dapil 3'],
            ['judul' => 'Safari Dakwah Kecamatan Cibarusah', 'kategori' => 'dakwah', 'tanggal' => '2026-05-12', 'lokasi' => 'Cibarusah'],
            ['judul' => 'Workshop UMKM Digital', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-18', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Bazar Sembako Murah', 'kategori' => 'baksos', 'tanggal' => '2026-04-22', 'lokasi' => 'Cikarang Selatan'],
            ['judul' => 'Kajian Subuh Berjamaah', 'kategori' => 'dakwah', 'tanggal' => '2026-04-05', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Senam Pagi di Wanasari', 'kategori' => 'senam', 'tanggal' => '2026-05-26', 'lokasi' => 'Wanasari, Cibitung'],
            ['judul' => 'Silaturahmi Tokoh Masyarakat', 'kategori' => 'kegiatan', 'tanggal' => '2026-04-18', 'lokasi' => 'Serang Baru'],
            ['judul' => 'Pembagian Takjil Ramadhan', 'kategori' => 'baksos', 'tanggal' => '2026-03-15', 'lokasi' => 'Tambun Utara'],
            ['judul' => 'Tabligh Akbar Isra Mi\'raj', 'kategori' => 'dakwah', 'tanggal' => '2026-03-28', 'lokasi' => 'Tambun Utara'],
            ['judul' => 'RKI Kerajinan Tangan Ibu-ibu', 'kategori' => 'rki', 'tanggal' => '2026-04-12', 'lokasi' => 'Bojongmangu'],
            ['judul' => 'Koordinasi DPC Dapil 1', 'kategori' => 'kegiatan', 'tanggal' => '2026-04-08', 'lokasi' => 'Setu'],
            ['judul' => 'Senam PKS di Sukasari', 'kategori' => 'senam', 'tanggal' => '2026-05-19', 'lokasi' => 'Serang Baru'],
            ['judul' => 'Donor Darah Massal', 'kategori' => 'baksos', 'tanggal' => '2026-03-10', 'lokasi' => 'Cikarang Pusat'],
            ['judul' => 'Pembinaan Penggerak RKI', 'kategori' => 'rki', 'tanggal' => '2026-04-25', 'lokasi' => 'Cibitung'],
            ['judul' => 'Visitasi KORWE Baru', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-10', 'lokasi' => 'Setu'],
            ['judul' => 'Senam Sehat Ceria Keluarga', 'kategori' => 'senam', 'tanggal' => '2026-06-02', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Diklat Mubaligh Dapil 1', 'kategori' => 'dakwah', 'tanggal' => '2026-04-02', 'lokasi' => 'Setu'],
            ['judul' => 'Rapat Evaluasi Program Bulanan', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-30', 'lokasi' => 'Sekretariat DPD'],
        ];

        foreach ($galeriData as $i => $g) {
            Galeri::create([
                'judul' => $g['judul'],
                'deskripsi' => 'Dokumentasi kegiatan ' . $g['judul'] . ' oleh DPD PKS Kabupaten Bekasi.',
                'file_path' => 'galeri/placeholder-' . ($i + 1) . '.jpg',
                'tipe' => 'foto',
                'kategori' => $g['kategori'],
                'lokasi' => $g['lokasi'],
                'tanggal' => Carbon::parse($g['tanggal']),
                'is_featured' => $g['is_featured'] ?? false,
                'is_published' => true,
                'urutan' => $i,
            ]);
        }
        $this->command->info('   → 24 galeri foto dibuat');

        // ================================================
        // 3. EVENT REGISTRATIONS dari publik (~60)
        // ================================================
        $this->command->info('3. Seeding event registrations publik...');
        $publicEvents = Event::where('is_public', true)->whereIn('status', ['disetujui','selesai'])->get();
        $regCount = 0;

        foreach ($publicEvents as $event) {
            $count = rand(15, 35);
            for ($r = 0; $r < $count; $r++) {
                $hp = $this->randomHP();
                if (EventRegistration::where('event_id', $event->id)->where('no_hp', $hp)->exists()) continue;

                EventRegistration::create([
                    'event_id' => $event->id,
                    'nama' => $this->namaPool[array_rand($this->namaPool)],
                    'no_hp' => $hp,
                    'email' => rand(0,1) ? strtolower(Str::slug($this->namaPool[array_rand($this->namaPool)], '')) . '@gmail.com' : null,
                    'created_at' => Carbon::parse($event->tanggal_mulai)->subDays(rand(1, 14)),
                ]);
                $regCount++;
            }
        }
        $this->command->info("   → {$regCount} registrasi event publik");

        // ================================================
        // 4. MEMBERS dari website + affiliate (~30 tambahan)
        // ================================================
        $this->command->info('4. Seeding members website...');
        $existingMembers = Member::all();
        $newMembers = collect();

        // 20 via website langsung
        for ($m = 0; $m < 20; $m++) {
            $desa = TargetWilayah::inRandomOrder()->first();
            $rw = DataRw::where('target_wilayah_id', $desa->id)->inRandomOrder()->first();

            $member = Member::create([
                'nama' => $this->namaPool[array_rand($this->namaPool)],
                'no_hp' => $this->randomHP(),
                'no_wa' => $this->randomHP(),
                'email' => strtolower(Str::random(8)) . '@gmail.com',
                'dapil' => $desa->dapil,
                'kecamatan' => $desa->kecamatan,
                'desa' => $desa->desa,
                'nomor_rw' => $rw?->nomor_rw,
                'target_wilayah_id' => $desa->id,
                'sumber' => 'website',
                'tanggal_bergabung' => Carbon::now()->subDays(rand(1, 60)),
                'status' => 'aktif',
            ]);
            $newMembers->push($member);
        }

        // 10 via affiliate
        $affiliators = $existingMembers->merge($newMembers)->filter(fn($m) => $m->referral_code);
        for ($m = 0; $m < 10; $m++) {
            $referrer = $affiliators->random();
            $desa = TargetWilayah::inRandomOrder()->first();

            $member = Member::create([
                'nama' => $this->namaPool[array_rand($this->namaPool)],
                'no_hp' => $this->randomHP(),
                'dapil' => $desa->dapil,
                'kecamatan' => $desa->kecamatan,
                'desa' => $desa->desa,
                'target_wilayah_id' => $desa->id,
                'sumber' => 'affiliate',
                'referred_by' => $referrer->id,
                'tanggal_bergabung' => Carbon::now()->subDays(rand(1, 30)),
                'status' => 'aktif',
            ]);
            $referrer->increment('referral_count');
        }
        $this->command->info('   → 30 members baru (20 website + 10 affiliate)');

        // ================================================
        // 5. KEHADIRAN tambahan (~40)
        // ================================================
        $this->command->info('5. Seeding kehadiran event...');
        $selesaiEvents = Event::where('status', 'selesai')->get();
        $allMembers = Member::aktif()->inRandomOrder()->limit(40)->get();
        $hadir = 0;

        foreach ($allMembers as $member) {
            $event = $selesaiEvents->random();
            if (Kehadiran::where('member_id', $member->id)->where('hadir_id', $event->id)->exists()) continue;

            Kehadiran::create([
                'member_id' => $member->id,
                'hadir_type' => Event::class,
                'hadir_id' => $event->id,
                'nama_kegiatan' => $event->nama,
                'waktu_scan' => Carbon::parse($event->tanggal_mulai)->addMinutes(rand(0, 180)),
                'metode' => ['qr','manual'][rand(0,1)],
            ]);
            $hadir++;
        }
        $this->command->info("   → {$hadir} kehadiran event");

        // ================================================
        // SUMMARY
        // ================================================
        $this->command->info('');
        $this->command->info('=== WEBSITE PUBLIK SEEDER SELESAI ===');
        $this->command->info('');
        $this->command->info('Konten website:');
        $this->command->info('  Berita:        20 artikel (3 featured, 5 kategori)');
        $this->command->info('  Galeri:        24 foto (6 featured, 6 kategori)');
        $this->command->info('  Registrasi:    ~' . $regCount . ' pendaftaran event');
        $this->command->info('  Members baru:  30 (20 website + 10 affiliate)');
        $this->command->info('  Kehadiran:     ' . $hadir . ' scan');
        $this->command->info('');
        $this->command->info('Kategori berita: kegiatan, program, dakwah, opini, pengumuman');
        $this->command->info('Kategori galeri: event, baksos, senam, rki, dakwah, kegiatan');
        $this->command->info('');
        $this->command->info('NOTE: Thumbnail/foto menggunakan placeholder.');
        $this->command->info('Ganti file di storage/app/public/galeri/ dengan foto asli.');
    }
}
```

Jalankan setelah DummyDataSeeder:
```bash
php artisan db:seed --class=PublicWebsiteSeeder
```

== 6. UPDATE HOMEPAGE — Connect ke Berita ==

Di app/Livewire/PublicSite/Home.php, tambahkan:

```php
public function getBeritaFeaturedProperty()
{
    return Berita::published()->featured()
        ->orderByDesc('published_at')
        ->first();
}

public function getBeritaListProperty()
{
    return Berita::published()
        ->where('is_featured', false)
        ->orderByDesc('published_at')
        ->limit(5)->get();
}
```

Di view homepage, ganti section berita placeholder dengan data dari DB:
- Featured besar: $this->beritaFeatured (judul, ringkasan, tanggal, views)
- List samping: $this->beritaList (judul, tanggal)

== 7. UPDATE HALAMAN /berita ==

Di app/Livewire/PublicSite/Berita.php:

```php
public $filterKategori = '';

public function getBeritaListProperty()
{
    return Berita::published()
        ->when($this->filterKategori, fn($q, $v) => $q->where('kategori', $v))
        ->orderByDesc('published_at')
        ->paginate(12);
}

public function getKategoriOptionsProperty()
{
    return Berita::KATEGORI_OPTIONS;
}
```

View: grid 3 kolom card berita (thumbnail placeholder, kategori badge, judul, ringkasan, tanggal, views). Filter by kategori. Paginated.

== 8. UPDATE HALAMAN /galeri ==

Di app/Livewire/PublicSite/Galeri.php:

```php
public $filterKategori = '';

public function getGaleriListProperty()
{
    return Galeri::published()
        ->when($this->filterKategori, fn($q, $v) => $q->where('kategori', $v))
        ->orderByDesc('tanggal')
        ->paginate(20);
}
```

View: grid masonry (4 kolom, item pertama span 2x2). Filter by kategori. Hover overlay. Placeholder gray jika gambar belum ada.

Langsung buat semua. Jangan test.
```
