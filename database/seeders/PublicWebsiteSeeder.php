<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\DataRw;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Galeri;
use App\Models\TargetWilayah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicWebsiteSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $namaPool = [
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

    private int $phoneCounter = 83000000;

    public function run(): void
    {
        $this->command?->info('=== Seeding data website publik ===');

        $creatorId = User::query()->where('email', 'admin1@bekasihebat.id')->value('id')
            ?? User::query()->value('id');

        $events = Event::query()
            ->where('is_public', true)
            ->whereIn('status', [Event::STATUS_DISETUJUI, Event::STATUS_SELESAI])
            ->orderBy('tanggal_mulai')
            ->get();

        $this->seedBerita($events, $creatorId);
        $this->seedGaleri($events, $creatorId);
        $newMembers = $this->seedPublicMembers();
        $regCount = $this->seedEventRegistrations($events, $newMembers);

        $hadir = EventRegistration::query()
            ->whereNotNull('attended_at')
            ->whereHas('user', fn ($query) => $query->where('email', 'like', 'website-member-%@bekasihebat.id'))
            ->count();

        $this->command?->info('');
        $this->command?->info('=== WEBSITE PUBLIK SEEDER SELESAI ===');
        $this->command?->info('Berita: '.Berita::query()->count().' artikel');
        $this->command?->info('Galeri: '.Galeri::query()->count().' item');
        $this->command?->info('Registrasi event publik: '.$regCount);
        $this->command?->info('Member website baru: '.$newMembers->count());
        $this->command?->info('Kehadiran event website: '.$hadir);
    }

    private function seedBerita(Collection $events, ?int $creatorId): void
    {
        $this->command?->info('1. Seeding berita...');

        $beritaThumbnails = [
            'kegiatan' => 'berita/berita-musker.jpg',
            'program' => 'berita/berita-senam-18desa.jpg',
            'dakwah' => 'berita/berita-musker.jpg',
            'opini' => 'berita/berita-workshop.jpg',
            'pengumuman' => 'berita/berita-kader.jpg',
        ];

        $eventMap = [
            'Baksos Kesehatan Gratis di Setu: 200 Warga Mendapat Layanan' => $events->firstWhere('judul', 'Baksos Kesehatan Gratis'),
            'Program Senam PKS Kini Hadir di 18 Desa, Ribuan Warga Antusias' => $events->firstWhere('judul', 'Jalan Santai Sehat Bersama'),
            'Workshop UMKM Digital Angkatan 2 Sukses Digelar di Cikarang' => $events->firstWhere('judul', 'Workshop UMKM Digital'),
        ];

        $artikels = [
            ['judul' => 'DPD PKS Kabupaten Bekasi Gelar Musyawarah Kerja Daerah 2026', 'kategori' => 'kegiatan', 'is_featured' => true, 'ringkasan' => 'Musyawarah Kerja Daerah DPD PKS Kabupaten Bekasi dihadiri seluruh pengurus DPC dan perwakilan kader dari 7 dapil. Agenda utama adalah penetapan target suara 350.000 dan roadmap program kerja menuju Pemilu 2029.', 'published_at' => '2026-05-28 10:00', 'views' => 1240],
            ['judul' => 'Baksos Kesehatan Gratis di Setu: 200 Warga Mendapat Layanan', 'kategori' => 'kegiatan', 'is_featured' => true, 'ringkasan' => 'DPD PKS Kabupaten Bekasi bersama tim medis relawan menggelar bakti sosial kesehatan gratis di Kecamatan Setu. Sebanyak 200 warga mendapat layanan pemeriksaan kesehatan, pengobatan gratis, dan pembagian sembako.', 'published_at' => '2026-05-25 09:00', 'views' => 890],
            ['judul' => 'Program Senam PKS Kini Hadir di 18 Desa, Ribuan Warga Antusias', 'kategori' => 'program', 'is_featured' => true, 'ringkasan' => 'Kelompok Senam Nusantara atau Senam PKS terus berkembang di Kabupaten Bekasi. Saat ini sudah ada banyak titik senam aktif di berbagai desa dan target akhirnya minimal 1 titik per desa.', 'published_at' => '2026-05-20 08:00', 'views' => 1560],
            ['judul' => 'Workshop UMKM Digital Angkatan 2 Sukses Digelar di Cikarang', 'kategori' => 'kegiatan', 'ringkasan' => 'Pelatihan digitalisasi UMKM angkatan kedua berhasil melatih 30 pelaku usaha mikro di Cikarang Barat. Peserta dibekali kemampuan pemasaran online, pengelolaan media sosial, dan pembukuan digital.', 'published_at' => '2026-05-18 14:00', 'views' => 432],
            ['judul' => 'Anggota DPRD PKS Reses di Dapil 3: Jembatan Rusak Jadi Prioritas', 'kategori' => 'kegiatan', 'ringkasan' => 'Anggota DPRD Kabupaten Bekasi dari Fraksi PKS melaksanakan reses di Dapil 3. Warga menyampaikan aspirasi terkait infrastruktur jembatan yang rusak dan drainase yang perlu diperbaiki.', 'published_at' => '2026-05-15 11:00', 'views' => 678],
            ['judul' => 'Safari Dakwah DPD ke 23 Kecamatan Capai Tahap ke-8', 'kategori' => 'dakwah', 'ringkasan' => 'Program Safari Dakwah DPD PKS Kabupaten Bekasi terus berjalan. Setiap kunjungan menggelar kajian, silaturahmi tokoh, dan diskusi program.', 'published_at' => '2026-05-12 09:00', 'views' => 345],
            ['judul' => 'Pelatihan Kader Angkatan 3: 25 Peserta Siap Jadi Pelopor', 'kategori' => 'kegiatan', 'ringkasan' => 'Diklat kader angkatan 3 resmi ditutup setelah tiga hari pelatihan intensif. Para peserta dinyatakan lulus dan naik jenjang dari Penggerak ke Pelopor.', 'published_at' => '2026-05-08 16:00', 'views' => 523],
            ['judul' => 'RKI Cijengkol Gelar Posyandu Gratis untuk Balita dan Ibu Hamil', 'kategori' => 'program', 'ringkasan' => 'Rumah Keluarga Indonesia di RW 003 Cijengkol menggelar posyandu gratis. Puluhan balita dan ibu hamil mendapat layanan pemeriksaan, imunisasi, dan konsultasi gizi.', 'published_at' => '2026-05-05 10:00', 'views' => 287],
            ['judul' => 'Jalan Santai Bersama Warga Tambun Selatan Diikuti 300 Peserta', 'kategori' => 'kegiatan', 'ringkasan' => 'Kegiatan jalan santai sehat bersama warga Tambun Selatan berlangsung meriah. Ratusan peserta mengikuti rute sepanjang 5 km dengan doorprize dan cek kesehatan gratis.', 'published_at' => '2026-04-28 07:00', 'views' => 892],
            ['judul' => 'Bazar Sembako Murah Jelang Ramadhan di Cikarang Selatan', 'kategori' => 'kegiatan', 'ringkasan' => 'DPC PKS Cikarang Selatan menggelar bazar sembako murah menjelang Ramadhan dengan antusiasme warga yang sangat tinggi.', 'published_at' => '2026-04-22 08:00', 'views' => 1102],
            ['judul' => 'Sapa Warga: 12.000 Kontak Terkumpul dari 342 RW', 'kategori' => 'program', 'ringkasan' => 'Program Sapa Warga terus menunjukkan progress positif. Belasan ribu kontak warga telah terkumpul dari ratusan RW di seluruh Kabupaten Bekasi.', 'published_at' => '2026-05-01 10:00', 'views' => 234],
            ['judul' => 'KORWE Terbentuk di 30 RW, Infrastruktur Pemenangan Mulai Kokoh', 'kategori' => 'program', 'ringkasan' => 'Pembentukan Koordinator RW terus berjalan dan menunjukkan progres yang konsisten untuk penguatan infrastruktur pemenangan di lapangan.', 'published_at' => '2026-04-15 14:00', 'views' => 456],
            ['judul' => 'Kader PKS Bekasi Tumbuh: 2.000 Kader Aktif Tersebar di 7 Dapil', 'kategori' => 'program', 'ringkasan' => 'Database kader mencatat ribuan kader aktif yang tersebar di 7 dapil dan terus diperkuat lewat pelatihan, UPA RW, dan program dakwah.', 'published_at' => '2026-04-10 09:00', 'views' => 389],
            ['judul' => 'Kajian Subuh Berjamaah: Puluhan Warga Hadiri di Masjid Al-Falah', 'kategori' => 'dakwah', 'ringkasan' => 'Program kajian subuh berjamaah diikuti puluhan warga secara rutin setiap Ahad pagi dengan suasana hangat dan penuh semangat.', 'published_at' => '2026-04-05 06:00', 'views' => 178],
            ['judul' => 'Tabligh Akbar Memperingati Isra Miraj di Tambun Utara', 'kategori' => 'dakwah', 'ringkasan' => 'DPC PKS Tambun Utara bersama masyarakat menggelar tabligh akbar memperingati Isra Miraj yang dihadiri ratusan jamaah.', 'published_at' => '2026-03-28 19:00', 'views' => 567],
            ['judul' => 'Opini: Pentingnya Infrastruktur Digital untuk Pemenangan Pemilu 2029', 'kategori' => 'opini', 'ringkasan' => 'Di era digital, pemenangan pemilu tidak bisa mengandalkan cara konvensional saja. Platform dashboard terintegrasi menjadi kebutuhan penting.', 'published_at' => '2026-03-20 10:00', 'views' => 234],
            ['judul' => 'Opini: Membangun Bekasi dari RW, Strategi Bottom-Up yang Terbukti', 'kategori' => 'opini', 'ringkasan' => 'Pendekatan membangun dari level paling bawah terbukti efektif. Program RKI, Senam PKS, dan Sapa Warga menunjukkan kekuatan strategi bottom-up.', 'published_at' => '2026-03-15 11:00', 'views' => 312],
            ['judul' => 'Pendaftaran Anggota Baru Dibuka, Daftar dan Dapatkan Kartu Digital', 'kategori' => 'pengumuman', 'ringkasan' => 'Pendaftaran anggota baru dibuka melalui website Bekasi Hebat dan setiap pendaftar bisa mendapatkan kartu anggota digital.', 'published_at' => '2026-05-01 08:00', 'views' => 1890],
            ['judul' => 'Jadwal Senam PKS Bulan Juni 2026 di Seluruh Kabupaten Bekasi', 'kategori' => 'pengumuman', 'ringkasan' => 'Jadwal lengkap senam PKS bulan Juni 2026 telah disusun di berbagai titik dan terbuka untuk seluruh warga.', 'published_at' => '2026-05-30 07:00', 'views' => 743],
            ['judul' => 'Pengajian Akbar Warga Bekasi 1 Perkuat Kolaborasi Lintas Komunitas', 'kategori' => 'kegiatan', 'ringkasan' => 'Pengajian akbar warga menjadi ruang silaturahmi lintas komunitas, memperkuat jaringan pelayanan, dan memperluas basis partisipasi publik.', 'published_at' => '2026-05-23 19:30', 'views' => 451],
        ];

        foreach ($artikels as $index => $artikel) {
            $slug = Str::slug($artikel['judul']);
            $paragraphs = [
                '<p>'.$artikel['ringkasan'].'</p>',
                '<p>Kegiatan ini merupakan bagian dari program kerja DPD PKS Kabupaten Bekasi untuk memperkuat jaringan pelayanan kepada masyarakat dan membangun kehadiran yang konsisten di level desa hingga RW.</p>',
                '<p>Selain mendorong manfaat langsung bagi warga, rangkaian kegiatan publik juga menjadi sarana menyerap aspirasi, memetakan kebutuhan wilayah, dan memperluas partisipasi masyarakat melalui kanal digital Bekasi Hebat.</p>',
                '<p>Warga yang ingin mengikuti program serupa dapat memantau agenda terbaru melalui website publik, halaman event, serta kanal komunikasi pengurus wilayah masing-masing.</p>',
            ];

            Berita::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $artikel['judul'],
                    'ringkasan' => $artikel['ringkasan'],
                    'konten' => implode("\n", $paragraphs),
                    'thumbnail' => $beritaThumbnails[$artikel['kategori']] ?? 'berita/berita-musker.jpg',
                    'kategori' => $artikel['kategori'],
                    'penulis' => ['Tim Media DPD', 'Komdigi Bekasi Hebat', 'Redaksi Bekasi Hebat'][$index % 3],
                    'is_featured' => (bool) ($artikel['is_featured'] ?? false),
                    'is_published' => true,
                    'published_at' => Carbon::parse($artikel['published_at']),
                    'views' => $artikel['views'],
                    'event_id' => $eventMap[$artikel['judul']]->id ?? null,
                    'created_by' => $creatorId,
                ]
            );
        }

        $this->command?->info('   -> '.Berita::query()->count().' artikel berita siap');
    }

    private function seedGaleri(Collection $events, ?int $creatorId): void
    {
        $this->command?->info('2. Seeding galeri...');

        $galeriData = [
            ['judul' => 'Musyawarah Kerja Daerah DPD 2026', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'event', 'tanggal' => '2026-05-28', 'is_featured' => true, 'lokasi' => 'Sekretariat DPD'],
            ['judul' => 'Baksos Kesehatan Gratis di Setu', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-05-25', 'is_featured' => true, 'lokasi' => 'Kecamatan Setu'],
            ['judul' => 'Senam PKS Pagi di Lapangan Cijengkol', 'file_path' => 'galeri/galeri-senam-wanasari.jpg', 'kategori' => 'senam', 'tanggal' => '2026-06-01', 'is_featured' => true, 'lokasi' => 'Cijengkol, Setu'],
            ['judul' => 'Pelatihan Kader Angkatan 3', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-08', 'is_featured' => true, 'lokasi' => 'Cikarang'],
            ['judul' => 'RKI Posyandu Balita di RW 003', 'file_path' => 'galeri/galeri-posyandu-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-05-05', 'is_featured' => true, 'lokasi' => 'Cijengkol, Setu'],
            ['judul' => 'Jalan Santai 300 Peserta di Tambun', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'event', 'tanggal' => '2026-04-28', 'is_featured' => true, 'lokasi' => 'Tambun Selatan'],
            ['judul' => 'Reses DPRD di Dapil 3', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-15', 'lokasi' => 'Dapil 3'],
            ['judul' => 'Safari Dakwah Kecamatan Cibarusah', 'file_path' => 'galeri/galeri-safari-dakwah.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-05-12', 'lokasi' => 'Cibarusah'],
            ['judul' => 'Workshop UMKM Digital', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-18', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Bazar Sembako Murah', 'file_path' => 'galeri/galeri-takjil.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-04-22', 'lokasi' => 'Cikarang Selatan'],
            ['judul' => 'Kajian Subuh Berjamaah', 'file_path' => 'galeri/galeri-tabligh-akbar.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-04-05', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Senam Pagi di Wanasari', 'file_path' => 'galeri/galeri-senam-wanasari.jpg', 'kategori' => 'senam', 'tanggal' => '2026-05-26', 'lokasi' => 'Wanasari, Cibitung'],
            ['judul' => 'Silaturahmi Tokoh Masyarakat', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-04-18', 'lokasi' => 'Serang Baru'],
            ['judul' => 'Pembagian Takjil Ramadhan', 'file_path' => 'galeri/galeri-takjil.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-03-15', 'lokasi' => 'Tambun Utara'],
            ['judul' => 'Tabligh Akbar Isra Mi\'raj', 'file_path' => 'galeri/galeri-tabligh-akbar.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-03-28', 'lokasi' => 'Tambun Utara'],
            ['judul' => 'RKI Kerajinan Tangan Ibu-ibu', 'file_path' => 'galeri/galeri-pembinaan-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-04-12', 'lokasi' => 'Bojongmangu'],
            ['judul' => 'Koordinasi DPC Dapil 1', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-04-08', 'lokasi' => 'Setu'],
            ['judul' => 'Senam PKS di Sukasari', 'file_path' => 'galeri/galeri-senam-wanasari.jpg', 'kategori' => 'senam', 'tanggal' => '2026-05-19', 'lokasi' => 'Serang Baru'],
            ['judul' => 'Donor Darah Massal', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-03-10', 'lokasi' => 'Cikarang Pusat'],
            ['judul' => 'Pembinaan Penggerak RKI', 'file_path' => 'galeri/galeri-pembinaan-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-04-25', 'lokasi' => 'Cibitung'],
            ['judul' => 'Visitasi KORWE Baru', 'file_path' => 'galeri/galeri-visitasi-korwe.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-10', 'lokasi' => 'Setu'],
            ['judul' => 'Senam Sehat Ceria Keluarga', 'file_path' => 'galeri/galeri-senam-wanasari.jpg', 'kategori' => 'senam', 'tanggal' => '2026-06-02', 'lokasi' => 'Cikarang Barat'],
            ['judul' => 'Diklat Mubaligh Dapil 1', 'file_path' => 'galeri/galeri-safari-dakwah.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-04-02', 'lokasi' => 'Setu'],
            ['judul' => 'Rapat Evaluasi Program Bulanan', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-30', 'lokasi' => 'Sekretariat DPD'],
        ];

        foreach ($galeriData as $index => $galeri) {
            $linkedEvent = $events->first(function (Event $event) use ($galeri): bool {
                return str_contains(Str::lower($event->judul), Str::lower(Str::before($galeri['judul'], ' di')))
                    || str_contains(Str::lower($galeri['judul']), Str::lower($event->jenis));
            });

            Galeri::query()->updateOrCreate(
                ['judul' => $galeri['judul'], 'tanggal' => Carbon::parse($galeri['tanggal'])->toDateString()],
                [
                    'deskripsi' => 'Dokumentasi kegiatan '.$galeri['judul'].' oleh DPD PKS Kabupaten Bekasi.',
                    'file_path' => $galeri['file_path'],
                    'thumbnail' => $galeri['file_path'],
                    'tipe' => 'foto',
                    'kategori' => $galeri['kategori'],
                    'lokasi' => $galeri['lokasi'],
                    'tanggal' => Carbon::parse($galeri['tanggal']),
                    'is_featured' => (bool) ($galeri['is_featured'] ?? false),
                    'is_published' => true,
                    'urutan' => $index,
                    'event_id' => $linkedEvent?->id,
                    'created_by' => $creatorId,
                ]
            );
        }

        $this->command?->info('   -> '.Galeri::query()->count().' item galeri siap');
    }

    private function seedPublicMembers(): Collection
    {
        $this->command?->info('3. Seeding member website publik...');

        $villages = TargetWilayah::query()->with('dataRws')->whereHas('dataRws')->inRandomOrder()->limit(20)->get();
        $referrers = collect();
        $members = collect();

        for ($i = 1; $i <= 20; $i++) {
            $members->push($this->upsertPublicMember(
                index: $i,
                emailPrefix: 'website-member',
                desa: $villages[($i - 1) % $villages->count()],
                referralCodeSource: null
            ));
        }

        $referrers = User::query()
            ->where('email', 'like', 'website-member-%@bekasihebat.id')
            ->whereNotNull('affiliate_code')
            ->get();

        for ($i = 1; $i <= 10; $i++) {
            $referrer = $referrers->random();
            $members->push($this->upsertPublicMember(
                index: $i,
                emailPrefix: 'affiliate-member',
                desa: $villages[($i + 5) % $villages->count()],
                referralCodeSource: $referrer->affiliate_code
            ));
        }

        $members = $members->unique('email')->values();
        $this->command?->info('   -> '.$members->count().' member publik siap');

        return $members;
    }

    private function seedEventRegistrations(Collection $events, Collection $newMembers): int
    {
        $this->command?->info('4. Seeding registrasi dan kehadiran event publik...');

        $registrations = 0;
        $members = User::query()
            ->where(function ($query): void {
                $query->where('email', 'like', 'website-member-%@bekasihebat.id')
                    ->orWhere('email', 'like', 'affiliate-member-%@bekasihebat.id');
            })
            ->get();

        foreach ($events as $event) {
            $sample = $members->shuffle()->take($event->status === Event::STATUS_SELESAI ? 18 : 10);

            foreach ($sample as $index => $member) {
                EventRegistration::query()->updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => $member->id],
                    [
                        'ticket_code' => sprintf('PUB-%04d-U%05d', $event->id, $member->id),
                        'status' => 'registered',
                        'affiliate_user_id' => str_starts_with((string) $member->email, 'affiliate-member-')
                            ? $newMembers->firstWhere('affiliate_code', str($member->address)->after('Referral: ')->toString())?->id
                            : null,
                        'attended_at' => $event->status === Event::STATUS_SELESAI
                            ? Carbon::parse($event->tanggal_mulai)->addMinutes(mt_rand(0, 120))
                            : null,
                    ]
                );
                $registrations++;
            }
        }

        return $registrations;
    }

    private function upsertPublicMember(int $index, string $emailPrefix, TargetWilayah $desa, ?string $referralCodeSource): User
    {
        $rw = DataRw::query()
            ->where('target_wilayah_id', $desa->id)
            ->inRandomOrder()
            ->first();

        $email = sprintf('%s-%02d@bekasihebat.id', $emailPrefix, $index);
        $name = $this->namaPool[array_rand($this->namaPool)];

        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'phone' => $this->randomHp(),
                'role' => 'community_member',
                'status' => 'aktif',
                'dapil' => $desa->dapil,
                'kecamatan' => $desa->kecamatan,
                'desa' => $desa->desa,
                'nomor_rw' => $rw?->nomor_rw,
                'member_number' => sprintf('BKH-%06d', $emailPrefix === 'website-member' ? 950000 + $index : 960000 + $index),
                'affiliate_code' => sprintf('PUB%04d', $emailPrefix === 'website-member' ? $index : $index + 100),
                'address' => $referralCodeSource ? 'Referral: '.$referralCodeSource : 'Registrasi website publik',
                'profile_completed_at' => now()->subDays(mt_rand(1, 60)),
                'birth_date' => now()->subYears(mt_rand(22, 48))->subDays(mt_rand(1, 365)),
                'gender' => mt_rand(0, 1) === 1 ? 'L' : 'P',
            ]
        );
    }

    private function randomHp(): string
    {
        $prefixes = ['0812', '0813', '0857', '0858', '0878', '0821', '0822'];
        $prefix = $prefixes[$this->phoneCounter % count($prefixes)];
        $number = str_pad((string) $this->phoneCounter, 8, '0', STR_PAD_LEFT);
        $this->phoneCounter++;

        return $prefix.$number;
    }
}
