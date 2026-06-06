<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TargetWilayah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ApprovedPublicEventsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding 10 event publik berstatus approved...');

        $villages = TargetWilayah::query()
            ->whereHas('dataRws')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->limit(10)
            ->get();

        if ($villages->isEmpty()) {
            $this->command?->error('Tidak ada target wilayah dengan data RW untuk dijadikan lokasi event.');

            return;
        }

        $creatorId = User::query()->where('email', 'admin1@bekasihebat.id')->value('id')
            ?? User::query()->value('id');
        $dpraId = (int) \Illuminate\Support\Facades\DB::table('dpra')->orderBy('id')->value('id');

        collect($this->eventBlueprints())
            ->values()
            ->each(function (array $blueprint, int $index) use ($villages, $creatorId, $dpraId): void {
                /** @var TargetWilayah $village */
                $village = $villages[$index % $villages->count()];
                $startsAt = Carbon::parse($blueprint['tanggal_mulai']);
                $slug = Str::slug($blueprint['judul']);

                $description = $this->buildDescription($blueprint, $village);
                $location = $blueprint['lokasi_prefix'].' '.$village->desa;
                $endsAt = (clone $startsAt)->addHours((int) $blueprint['durasi_jam']);

                $event = Event::query()->firstOrNew(['slug' => $slug]);
                $event->uuid = $event->uuid ?: (string) Str::uuid();
                $event->title = $blueprint['judul'];
                $event->description = $description;
                $event->starts_at = $startsAt;
                $event->ends_at = $endsAt;
                $event->location_name = $location;
                $event->location_address = $location;
                $event->visibility = 'public';
                $event->organizer_dpra_id = $dpraId;
                $event->judul = $blueprint['judul'];
                $event->deskripsi = $description;
                $event->jenis = $blueprint['jenis'];
                $event->tanggal_mulai = $startsAt;
                $event->tanggal_selesai = $endsAt;
                $event->lokasi = $location;
                $event->lokasi_desa = $village->desa;
                $event->lokasi_kecamatan = $village->kecamatan;
                $event->lokasi_dapil = $village->dapil;
                $event->kapasitas = $blueprint['kapasitas'];
                $event->max_participants = $blueprint['kapasitas'];
                $event->is_public = true;
                $event->cover_image = $this->eventCoverForJenis((string) $blueprint['jenis']);
                $event->status = Event::STATUS_DISETUJUI;
                $event->level_approval = 'selesai';
                $event->penyelenggara = $blueprint['penyelenggara'];
                $event->pic_nama = $blueprint['pic_nama'];
                $event->pic_hp = $blueprint['pic_hp'];
                $event->created_by = $creatorId;
                $event->save();
            });

        $this->command?->info('10 event publik approved siap digunakan.');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function eventBlueprints(): array
    {
        return [
            [
                'judul' => 'Bakti Sosial Pemeriksaan Kesehatan Warga',
                'jenis' => 'kesehatan',
                'tanggal_mulai' => '2026-06-14 08:00:00',
                'durasi_jam' => 5,
                'kapasitas' => 180,
                'lokasi_prefix' => 'Aula warga',
                'penyelenggara' => 'Bidang Pendidikan dan Kesehatan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'dr. Miftah Fauziah',
                'pic_hp' => '081290001101',
            ],
            [
                'judul' => 'Pengajian Keluarga dan Silaturahmi Tokoh Masyarakat',
                'jenis' => 'pengajian',
                'tanggal_mulai' => '2026-06-18 19:30:00',
                'durasi_jam' => 3,
                'kapasitas' => 220,
                'lokasi_prefix' => 'Masjid jami',
                'penyelenggara' => 'Bidang Pembangunan Keumatan dan Dakwah DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Ustadz Rahmat Hidayat',
                'pic_hp' => '081290001102',
            ],
            [
                'judul' => 'Pelatihan UMKM Rumah Tangga Berbasis Pemasaran Digital',
                'jenis' => 'pelatihan',
                'tanggal_mulai' => '2026-06-21 09:00:00',
                'durasi_jam' => 6,
                'kapasitas' => 90,
                'lokasi_prefix' => 'Gedung serbaguna',
                'penyelenggara' => 'Bidang Ekonomi, Keuangan dan Industri DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Novi Andriani',
                'pic_hp' => '081290001103',
            ],
            [
                'judul' => 'Senam Nusantara dan Pemeriksaan Gula Darah Gratis',
                'jenis' => 'senam',
                'tanggal_mulai' => '2026-06-26 06:15:00',
                'durasi_jam' => 3,
                'kapasitas' => 250,
                'lokasi_prefix' => 'Lapangan utama',
                'penyelenggara' => 'Binapora DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Santi Wulandari',
                'pic_hp' => '081290001104',
            ],
            [
                'judul' => 'Dialog Warga tentang Infrastruktur Lingkungan dan Drainase',
                'jenis' => 'diskusi',
                'tanggal_mulai' => '2026-06-29 19:45:00',
                'durasi_jam' => 3,
                'kapasitas' => 120,
                'lokasi_prefix' => 'Balai pertemuan',
                'penyelenggara' => 'Bidang Politik, Hukum dan Keamanan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Asep Firmansyah',
                'pic_hp' => '081290001105',
            ],
            [
                'judul' => 'Kajian Remaja Masjid dan Penguatan Kepemimpinan Pemuda',
                'jenis' => 'pengajian',
                'tanggal_mulai' => '2026-07-03 19:15:00',
                'durasi_jam' => 3,
                'kapasitas' => 160,
                'lokasi_prefix' => 'Pusat kegiatan pemuda',
                'penyelenggara' => 'Bidang Kepemudaan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Ilham Maulana',
                'pic_hp' => '081290001106',
            ],
            [
                'judul' => 'Pelatihan Relawan Layanan Warga Tingkat Kecamatan',
                'jenis' => 'pelatihan',
                'tanggal_mulai' => '2026-07-06 08:30:00',
                'durasi_jam' => 7,
                'kapasitas' => 110,
                'lokasi_prefix' => 'Aula kecamatan',
                'penyelenggara' => 'Bidang Relawan dan Saksi Nasional DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Rizky Kurniawan',
                'pic_hp' => '081290001107',
            ],
            [
                'judul' => 'Bazar Pangan Murah dan Edukasi Gizi Keluarga',
                'jenis' => 'baksos',
                'tanggal_mulai' => '2026-07-11 07:30:00',
                'durasi_jam' => 6,
                'kapasitas' => 300,
                'lokasi_prefix' => 'Halaman kantor desa',
                'penyelenggara' => 'Bidang Perempuan dan Ketahanan Keluarga DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Dewi Kusumaningrum',
                'pic_hp' => '081290001108',
            ],
            [
                'judul' => 'Musyawarah Kerja Wilayah untuk Sinkronisasi Program RW',
                'jenis' => 'musyawarah',
                'tanggal_mulai' => '2026-07-16 13:00:00',
                'durasi_jam' => 5,
                'kapasitas' => 85,
                'lokasi_prefix' => 'Ruang rapat',
                'penyelenggara' => 'Sekretariat DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Fahmi Saepulloh',
                'pic_hp' => '081290001109',
            ],
            [
                'judul' => 'Bimbingan Belajar Gratis untuk Siswa Menjelang Tahun Ajaran Baru',
                'jenis' => 'pendidikan',
                'tanggal_mulai' => '2026-07-20 08:00:00',
                'durasi_jam' => 4,
                'kapasitas' => 140,
                'lokasi_prefix' => 'Sanggar belajar',
                'penyelenggara' => 'Bidang Pendidikan dan Kesehatan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Rina Apriliani',
                'pic_hp' => '081290001110',
            ],
        ];
    }

    private function buildDescription(array $blueprint, TargetWilayah $village): string
    {
        return sprintf(
            '%s diselenggarakan untuk warga %s, Kecamatan %s, dengan fokus memperkuat pelayanan masyarakat, kolaborasi lingkungan, dan partisipasi publik di %s.',
            $blueprint['judul'],
            $village->desa,
            $village->kecamatan,
            $village->dapil
        );
    }

    private function eventCoverForJenis(string $jenis): string
    {
        return [
            'baksos' => 'events/event-baksos-kesehatan.jpg',
            'pengajian' => 'events/event-pengajian.jpg',
            'senam' => 'events/event-senam.jpg',
            'diskusi' => 'events/event-dialog-warga.jpg',
            'pelatihan' => 'events/event-pelatihan-umkm.jpg',
            'musyawarah' => 'events/event-musyawarah.jpg',
            'bedah_rumah' => 'events/event-baksos-kesehatan.jpg',
            'kesehatan' => 'events/event-baksos-kesehatan.jpg',
            'pendidikan' => 'events/event-pelatihan-relawan.jpg',
            'lainnya' => 'events/event-dialog-warga.jpg',
        ][$jenis] ?? 'events/event-dialog-warga.jpg';
    }
}
