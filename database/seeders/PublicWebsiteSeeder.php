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
            ['judul' => 'Program Senam PKS Kini Hadir di 18 Desa, Ribuan Warga Antusias', 'kategori' => 'program', 'is_featured' => true, 'ringkasan' => 'Kelompok Senam Nusantara atau Senam PKS terus berkembang di Kabupaten Bekasi. Saat ini sudah ada banyak titik senam aktif di berbagai desa and target akhirnya minimal 1 titik per desa.', 'published_at' => '2026-05-20 08:00', 'views' => 1560],
            ['judul' => 'Workshop UMKM Digital Angkatan 2 Sukses Digelar di Cikarang', 'kategori' => 'kegiatan', 'ringkasan' => 'Pelatihan digitalisasi UMKM angkatan kedua berhasil melatih 30 pelaku usaha mikro di Cikarang Barat. Peserta dibekali kemampuan pemasaran online, pengelolaan media sosial, dan pembukuan digital.', 'published_at' => '2026-05-18 14:00', 'views' => 432],
            ['judul' => 'Anggota DPRD PKS Reses di Dapil 3: Jembatan Ruak Jadi Prioritas', 'kategori' => 'kegiatan', 'ringkasan' => 'Anggota DPRD Kabupaten Bekasi dari Fraksi PKS melaksanakan reses di Dapil 3. Warga menyampaikan aspirasi terkait infrastruktur jembatan yang rusak dan drainase yang perlu diperbaiki.', 'published_at' => '2026-05-15 11:00', 'views' => 678],
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
            ['judul' => 'DPC PKS Babelan Respon Cepat Kebakaran Lapak Pemulung', 'kategori' => 'kegiatan', 'ringkasan' => 'Tim relawan PKS Babelan langsung menyalurkan bantuan makanan, pakaian layak pakai, dan perlengkapan bayi untuk korban kebakaran lapak pemulung di Kebalen.', 'published_at' => '2026-05-14 10:00', 'views' => 310],
            ['judul' => 'Pelatihan Kebencanaan Relawan PKS Kabupaten Bekasi Diikuti 100 Pemimpin', 'kategori' => 'kegiatan', 'ringkasan' => 'Bidang Relawan DPD PKS Kabupaten Bekasi mengadakan pelatihan kesiapsiagaan bencana bagi 100 relawan muda di bumi perkemahan Karang Kitri, Bojongmangu.', 'published_at' => '2026-05-11 08:30', 'views' => 420],
            ['judul' => 'DPRa PKS Wanasari Gelar Fogging Gratis Cegah Demam Berdarah', 'kategori' => 'kegiatan', 'ringkasan' => 'Mengantisipasi lonjakan kasus DBD di musim pancaroba, DPRa PKS Wanasari, Cibitung melakukan pengasapan (fogging) gratis di lingkungan pemukiman RW 015.', 'published_at' => '2026-05-09 13:00', 'views' => 245],
            ['judul' => 'Advokasi Kesehatan PKS Bekasi Berhasil Bantu Warga Dapatkan Hak BPJS', 'kategori' => 'program', 'ringkasan' => 'Tim Advokasi Kesehatan DPD PKS Bekasi berhasil memfasilitasi pengaktifan kembali kartu BPJS Kesehatan seorang warga kurang mampu di Setu yang membutuhkan operasi darurat.', 'published_at' => '2026-05-04 15:30', 'views' => 520],
            ['judul' => 'RKI Tambun Selatan Luncurkan Gerakan Ketahanan Keluarga', 'kategori' => 'program', 'ringkasan' => 'Rumah Keluarga Indonesia (RKI) Tambun Selatan secara resmi meluncurkan Gerakan Ketahanan Keluarga guna membekali para ibu dengan pengetahuan parenting dan kewirausahaan.', 'published_at' => '2026-05-02 09:15', 'views' => 330],
            ['judul' => 'Tokoh Masyarakat Bojongmangu Apresiasi Kiprah Nyata Relawan PKS', 'kategori' => 'kegiatan', 'ringkasan' => 'Dalam kunjungan silaturahmi tokoh di Kecamatan Bojongmangu, tokoh masyarakat mengapresiasi kehadiran nyata relawan PKS yang konsisten membantu warga tanpa pamrih.', 'published_at' => '2026-04-29 11:00', 'views' => 610],
            ['judul' => 'Konsolidasi Struktur PKS Dapil 1 Targetkan Peningkatan Suara Signifikan', 'kategori' => 'kegiatan', 'ringkasan' => 'Struktur DPC dan DPRa PKS di Dapil 1 menggelar konsolidasi internal untuk mematangkan strategi pemenangan dan penyelarasan target suara di Pemilu mendatang.', 'published_at' => '2026-04-25 16:00', 'views' => 180],
            ['judul' => 'DPC PKS Cikarang Utara Salurkan Bantuan Air Bersih ke Desa Terdampak Kekeringan', 'kategori' => 'kegiatan', 'ringkasan' => 'Menanggapi keluhan warga akibat kekeringan, DPC PKS Cikarang Utara menyalurkan 5 tangki air bersih untuk warga Desa Karangraharja yang mengalami krisis air.', 'published_at' => '2026-04-20 10:30', 'views' => 410],
            ['judul' => 'Kajian Ramadan PKS Cikarang Timur Diikuti Ratusan Pemuda Hijrah', 'kategori' => 'dakwah', 'ringkasan' => 'Bidang Kepemudaan PKS Cikarang Timur sukses menyelenggarakan kajian Ramadan khusus pemuda yang mengusung tema produktivitas dan spiritualitas di era digital.', 'published_at' => '2026-04-18 19:30', 'views' => 280],
            ['judul' => 'Santunan Anak Yatim dan Dhuafa Semarakkan Ramadan di DPC PKS Tarumajaya', 'kategori' => 'kegiatan', 'ringkasan' => 'Sebanyak 100 anak yatim dan dhuafa di wilayah Tarumajaya mendapatkan santunan berupa paket sembako, alat tulis, dan uang tunai dari para donatur PKS.', 'published_at' => '2026-04-14 16:30', 'views' => 350],
            ['judul' => 'PKS Bekasi Dorong Peningkatan Kualitas Layanan Posyandu Balita', 'kategori' => 'program', 'ringkasan' => 'DPD PKS Kabupaten Bekasi melalui Bidang Perempuan terus mendorong peningkatan kualitas layanan Posyandu guna mendukung program penurunan stunting anak nasional.', 'published_at' => '2026-04-08 09:00', 'views' => 195],
            ['judul' => 'PKS Bekasi Sukses Gelar Lomba Baca Kitab Kuning Tingkat Kabupaten', 'kategori' => 'dakwah', 'ringkasan' => 'Bidang Pembangunan Keumatan PKS Kabupaten Bekasi sukses menyelenggarakan Lomba Baca Kitab Kuning (LBKK) ke-8 yang diikuti puluhan santri dari berbagai pondok pesantren.', 'published_at' => '2026-04-03 14:00', 'views' => 390],
            ['judul' => 'RKI Cibarusah Berikan Bantuan Sembako untuk Lansia Kurang Mampu', 'kategori' => 'program', 'ringkasan' => 'Anggota RKI Cibarusah membagikan puluhan paket sembako secara langsung ke rumah-rumah lansia kurang mampu sebagai bagian dari aksi sosial bulanan RKI Peduli.', 'published_at' => '2026-03-29 10:00', 'views' => 210],
            ['judul' => 'PKS Muda Bekasi Luncurkan Komunitas Kreatif untuk Salurkan Bakat Pemuda', 'kategori' => 'program', 'ringkasan' => 'Bidang Kepemudaan DPD PKS meluncurkan wadah kreativitas PKS Muda Bekasi guna mewadahi bakat anak muda di bidang multimedia, musik religi, dan kepenulisan.', 'published_at' => '2026-03-24 15:00', 'views' => 475],
            ['judul' => 'Silaturahmi DPC PKS Karangbahagia ke Tokoh Agama Perkuat Ukhuwah Islamiyah', 'kategori' => 'dakwah', 'ringkasan' => 'Pengurus DPC PKS Karangbahagia melakukan silaturahmi ke kediaman pimpinan pondok pesantren guna memohon doa restu dan masukan demi kemajuan pembangunan Bekasi.', 'published_at' => '2026-03-18 13:00', 'views' => 290],
            ['judul' => 'Opini: Menatap Masa Depan Pendidikan Kabupaten Bekasi yang Berkelanjutan', 'kategori' => 'opini', 'ringkasan' => 'Pendidikan berkualitas adalah kunci kemakmuran daerah. PKS berkomitmen memperjuangkan akses pendidikan merata bagi warga Kabupaten Bekasi melalui kebijakan anggaran pro-rakyat.', 'published_at' => '2026-03-12 09:30', 'views' => 310],
            ['judul' => 'Opini: Urgensi Ketahanan Pangan Daerah di Tengah Ketidakpastian Iklim Global', 'kategori' => 'opini', 'ringkasan' => 'Kabupaten Bekasi harus mampu mandiri pangan. Pemberdayaan petani lokal dan teknologi pertanian perkotaan menjadi solusi penting yang ditawarkan PKS.', 'published_at' => '2026-03-08 11:00', 'views' => 240],
            ['judul' => 'Pengumuman: Pendaftaran Beasiswa Pendidikan Anak Kader PKS Bekasi Dibuka', 'kategori' => 'pengumuman', 'ringkasan' => 'DPD PKS Kabupaten Bekasi mengumumkan pembukaan pendaftaran program beasiswa prestasi dan bantuan biaya sekolah bagi putra-putri kader yang kurang mampu.', 'published_at' => '2026-03-02 08:00', 'views' => 780],
            ['judul' => 'Pengumuman: Layanan Mobil Ambulans Gratis PKS Siap Melayani Warga 24 Jam', 'kategori' => 'pengumuman', 'ringkasan' => 'DPD PKS Kabupaten Bekasi menegaskan komitmennya untuk terus melayani warga melalui fasilitas mobil ambulans gratis yang siap siaga 24 jam untuk keadaan darurat.', 'published_at' => '2026-02-25 07:30', 'views' => 920],
            ['judul' => 'Pelatihan Pembuatan Kompos Organik RKI Wanasari Diikuti Ibu Rumah Tangga', 'kategori' => 'program', 'ringkasan' => 'RKI Kelurahan Wanasari menggelar pelatihan pembuatan pupuk kompos dari sampah organik rumah tangga guna mengurangi volume pembuangan sampah lingkungan.', 'published_at' => '2026-02-20 10:00', 'views' => 275],
            ['judul' => 'PKS Bekasi Utara Salurkan Bantuan Seragam Sekolah Gratis untuk Siswa Yatim', 'kategori' => 'kegiatan', 'ringkasan' => 'Sebagai bentuk kepedulian terhadap pendidikan anak yatim, relawan PKS Bekasi Utara menyalurkan puluhan paket seragam dan tas sekolah gratis menyambut tahun ajaran baru.', 'published_at' => '2026-02-15 14:00', 'views' => 320],
            ['judul' => 'Reses Anggota DPRD Fraksi PKS di Serang Baru Serap Aspirasi Petani Lokal', 'kategori' => 'kegiatan', 'ringkasan' => 'Anggota DPRD Kabupaten Bekasi dari Fraksi PKS melaksanakan reses di Serang Baru untuk menyerap keluhan terkait kelangkaan pupuk subsidi dan masalah saluran irigasi sawah.', 'published_at' => '2026-02-10 11:30', 'views' => 460],
            ['judul' => 'DPC PKS Sukatani Sukses Selenggarakan Turnamen Futsal Pemuda Hebat Cup', 'kategori' => 'kegiatan', 'ringkasan' => 'Sebanyak 16 tim futsal karang taruna mengikuti turnamen Pemuda Hebat Cup yang diinisiasi oleh Bidang Kepemudaan PKS Sukatani guna membina sportivitas remaja.', 'published_at' => '2026-02-05 16:30', 'views' => 540],
            ['judul' => 'PKS Bekasi Berikan Layanan Cek Kesehatan Gratis untuk Suporter Sepakbola', 'kategori' => 'kegiatan', 'ringkasan' => 'Tim medis relawan PKS mendirikan posko cek kesehatan gratis (tekanan darah dan asam urat) bagi suporter sepakbola lokal yang menghadiri pertandingan di Stadion Wibawa Mukti.', 'published_at' => '2026-01-28 15:00', 'views' => 380],
            ['judul' => 'DPC PKS Cikarang Pusat Sosialisasikan Bahaya Penyalahgunaan Narkoba', 'kategori' => 'kegiatan', 'ringkasan' => 'Bekerja sama dengan praktisi kesehatan, PKS Cikarang Pusat menggelar seminar sosialisasi pencegahan narkoba untuk remaja masjid dan karang taruna setempat.', 'published_at' => '2026-01-22 09:30', 'views' => 215],
            ['judul' => 'Kajian Tafsir Al-Quran Rutin DPC PKS Pebayuran Disambut Hangat Jamaah Masjid', 'kategori' => 'dakwah', 'ringkasan' => 'Program pembinaan dakwah berupa kajian tafsir Al-Quran yang diadakan PKS Pebayuran berjalan rutin dua mingguan dengan jamaah yang terus bertambah dari berbagai RT.', 'published_at' => '2026-01-15 19:30', 'views' => 180],
            ['judul' => 'Opini: Revitalisasi Pasar Tradisional untuk Menjaga Eksistensi Ekonomi Rakyat', 'kategori' => 'opini', 'ringkasan' => 'Pasar tradisional adalah urat nadi perekonomian lokal. PKS Bekasi mendorong revitalisasi sarana pasar tradisional agar tetap bersih, nyaman, dan mampu bersaing dengan pasar modern.', 'published_at' => '2026-01-08 10:00', 'views' => 340],
            ['judul' => 'Pengumuman: DPD PKS Buka Posko Pengaduan Kasus Kekerasan Perempuan dan Anak', 'kategori' => 'pengumuman', 'ringkasan' => 'Bidang Perempuan DPD PKS Kabupaten Bekasi bekerja sama dengan psikolog membuka layanan posko pendampingan dan konseling gratis bagi korban kekerasan keluarga.', 'published_at' => '2026-01-03 08:30', 'views' => 650],
            ['judul' => 'DPC PKS Sukawangi Gelar Aksi Tanam Pohon untuk Penghijauan Lingkungan', 'kategori' => 'kegiatan', 'ringkasan' => 'Relawan PKS Sukawangi membagikan dan menanam 300 bibit pohon buah bersama warga setempat guna meningkatkan penghijauan dan mencegah erosi di bantaran sungai.', 'published_at' => '2025-12-25 09:00', 'views' => 270],
            ['judul' => 'DPD PKS Kabupaten Bekasi Adakan Silaturahmi Akhir Tahun Bersama Media', 'kategori' => 'kegiatan', 'ringkasan' => 'Pengurus harian DPD PKS Kabupaten Bekasi mengadakan gathering akhir tahun bersama jurnalis media lokal guna mempererat kemitraan penyebaran informasi pembangunan daerah.', 'published_at' => '2025-12-28 19:00', 'views' => 490],
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
            ['judul' => 'Penyaluran Air Bersih Cikarang Utara', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-04-20', 'lokasi' => 'Cikarang Utara'],
            ['judul' => 'Pelatihan Kebencanaan Relawan PKS', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-05-11', 'lokasi' => 'Bojongmangu'],
            ['judul' => 'Fogging DBD Gratis Cibitung', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-05-09', 'lokasi' => 'Cibitung'],
            ['judul' => 'Gerakan Ketahanan Keluarga RKI', 'file_path' => 'galeri/galeri-pembinaan-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-05-02', 'lokasi' => 'Tambun Selatan'],
            ['judul' => 'Aksi Tanam Pohon Sukawangi', 'file_path' => 'galeri/galeri-visitasi-korwe.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2025-12-25', 'lokasi' => 'Sukawangi'],
            ['judul' => 'Kajian Ramadan Pemuda Cikarang Timur', 'file_path' => 'galeri/galeri-safari-dakwah.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-04-18', 'lokasi' => 'Cikarang Timur'],
            ['judul' => 'Santunan Anak Yatim DPC Tarumajaya', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-04-14', 'lokasi' => 'Tarumajaya'],
            ['judul' => 'LBKK Lomba Baca Kitab Kuning DPD', 'file_path' => 'galeri/galeri-tabligh-akbar.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-04-03', 'lokasi' => 'Cikarang Pusat'],
            ['judul' => 'Lomba Futsal Pemuda Sukatani', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'event', 'tanggal' => '2026-02-05', 'lokasi' => 'Sukatani'],
            ['judul' => 'Posko Kesehatan Suporter Wibawa Mukti', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-01-28', 'lokasi' => 'Cikarang Timur'],
            ['judul' => 'Sosialisasi Anti Narkoba Cikarang Pusat', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'event', 'tanggal' => '2026-01-22', 'lokasi' => 'Cikarang Pusat'],
            ['judul' => 'Tanam Bidit Buah di Karangbahagia', 'file_path' => 'galeri/galeri-visitasi-korwe.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-03-15', 'lokasi' => 'Karangbahagia'],
            ['judul' => 'Posyandu Lansia RKI Wanasari', 'file_path' => 'galeri/galeri-posyandu-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-02-12', 'lokasi' => 'Cibitung'],
            ['judul' => 'Turnamen Bulutangkis DPC Babelan', 'file_path' => 'galeri/galeri-reses-dprd.jpg', 'kategori' => 'event', 'tanggal' => '2026-01-18', 'lokasi' => 'Babelan'],
            ['judul' => 'Bantuan Seragam Sekolah Bekasi Utara', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-02-15', 'lokasi' => 'Bekasi Utara'],
            ['judul' => 'Safari Dakwah Kecamatan Serang Baru', 'file_path' => 'galeri/galeri-safari-dakwah.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-05-02', 'lokasi' => 'Serang Baru'],
            ['judul' => 'Posko Pengaduan Kekerasan DPD', 'file_path' => 'galeri/galeri-pembinaan-rki.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-01-05', 'lokasi' => 'Sekretariat DPD'],
            ['judul' => 'Gathering Akhir Tahun Jurnalis Media', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2025-12-28', 'lokasi' => 'Cikarang Pusat'],
            ['judul' => 'Konsolidasi Struktur PKS Dapil 1', 'file_path' => 'galeri/galeri-koordinasi-dpc.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-04-25', 'lokasi' => 'Setu'],
            ['judul' => 'Bazar Ramadan Cikarang Timur', 'file_path' => 'galeri/galeri-takjil.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-04-12', 'lokasi' => 'Cikarang Timur'],
            ['judul' => 'Silaturahmi Tokoh Agama Karangbahagia', 'file_path' => 'galeri/galeri-tabligh-akbar.jpg', 'kategori' => 'dakwah', 'tanggal' => '2026-03-18', 'lokasi' => 'Karangbahagia'],
            ['judul' => 'Posyandu Gratis Balita Cijengkol', 'file_path' => 'galeri/galeri-posyandu-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-05-05', 'lokasi' => 'Setu'],
            ['judul' => 'Penyuluhan Gizi Ibu Hamil', 'file_path' => 'galeri/galeri-posyandu-rki.jpg', 'kategori' => 'rki', 'tanggal' => '2026-04-22', 'lokasi' => 'Tambun Selatan'],
            ['judul' => 'Fogging Nyamuk Desa Wanasari', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'baksos', 'tanggal' => '2026-05-08', 'lokasi' => 'Cibitung'],
            ['judul' => 'Senam PKS Wanasari Cibitung', 'file_path' => 'galeri/galeri-senam-wanasari.jpg', 'kategori' => 'senam', 'tanggal' => '2026-05-26', 'lokasi' => 'Cibitung'],
            ['judul' => 'Penyaluran Ambulans Keadaan Darurat', 'file_path' => 'galeri/galeri-donor-darah.jpg', 'kategori' => 'kegiatan', 'tanggal' => '2026-02-25', 'lokasi' => 'Cikarang Pusat'],
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
