<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TargetWilayah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompletedPublicEventsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding 10 event publik berstatus completed...');

        $villages = TargetWilayah::query()
            ->whereHas('dataRws')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->limit(10)
            ->get();

        if ($villages->isEmpty()) {
            $this->command?->error('Tidak ada target wilayah dengan data RW untuk dijadikan lokasi event selesai.');

            return;
        }

        $creatorId = User::query()->where('email', 'admin1@bekasihebat.id')->value('id')
            ?? User::query()->value('id');
        $dpraId = (int) DB::table('dpra')->orderBy('id')->value('id');

        collect($this->eventBlueprints())
            ->values()
            ->each(function (array $blueprint, int $index) use ($villages, $creatorId, $dpraId): void {
                /** @var TargetWilayah $village */
                $village = $villages[$index % $villages->count()];
                $startsAt = Carbon::parse($blueprint['tanggal_mulai']);
                $endsAt = (clone $startsAt)->addHours((int) $blueprint['durasi_jam']);
                $slug = Str::slug($blueprint['judul']);
                $description = $this->buildDescription($blueprint, $village);
                $location = $blueprint['lokasi_prefix'].' '.$village->desa;

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
                $event->status = Event::STATUS_SELESAI;
                $event->level_approval = 'selesai';
                $event->penyelenggara = $blueprint['penyelenggara'];
                $event->pic_nama = $blueprint['pic_nama'];
                $event->pic_hp = $blueprint['pic_hp'];
                $event->created_by = $creatorId;
                $event->save();
            });

        $this->command?->info('10 event publik completed siap digunakan.');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function eventBlueprints(): array
    {
        return [
            [
                'judul' => 'Khitanan Massal untuk Keluarga Prasejahtera',
                'jenis' => 'kesehatan',
                'tanggal_mulai' => '2026-02-08 08:00:00',
                'durasi_jam' => 7,
                'kapasitas' => 120,
                'lokasi_prefix' => 'Gedung serbaguna',
                'penyelenggara' => 'Bidang Pendidikan dan Kesehatan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'dr. Laila Anwar',
                'pic_hp' => '081290002101',
            ],
            [
                'judul' => 'Pasar Murah Ramadhan dan Santunan Dhuafa',
                'jenis' => 'baksos',
                'tanggal_mulai' => '2026-03-07 07:00:00',
                'durasi_jam' => 6,
                'kapasitas' => 320,
                'lokasi_prefix' => 'Halaman balai warga',
                'penyelenggara' => 'Bidang Perempuan dan Ketahanan Keluarga DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Siti Mardiah',
                'pic_hp' => '081290002102',
            ],
            [
                'judul' => 'Pengajian Akbar Menyambut Bulan Ramadhan',
                'jenis' => 'pengajian',
                'tanggal_mulai' => '2026-02-21 19:30:00',
                'durasi_jam' => 3,
                'kapasitas' => 260,
                'lokasi_prefix' => 'Masjid raya',
                'penyelenggara' => 'Bidang Pembangunan Keumatan dan Dakwah DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Ustadz Dani Ramdani',
                'pic_hp' => '081290002103',
            ],
            [
                'judul' => 'Senam Sehat Keluarga dan Cek Tekanan Darah',
                'jenis' => 'senam',
                'tanggal_mulai' => '2026-03-15 06:00:00',
                'durasi_jam' => 3,
                'kapasitas' => 280,
                'lokasi_prefix' => 'Lapangan kecamatan',
                'penyelenggara' => 'Binapora DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Nadia Rahmawati',
                'pic_hp' => '081290002104',
            ],
            [
                'judul' => 'Pelatihan Pengelolaan Keuangan UMKM Perempuan',
                'jenis' => 'pelatihan',
                'tanggal_mulai' => '2026-03-22 09:00:00',
                'durasi_jam' => 5,
                'kapasitas' => 95,
                'lokasi_prefix' => 'Aula kelurahan',
                'penyelenggara' => 'Bidang Ekonomi, Keuangan dan Industri DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Yulia Kurniasih',
                'pic_hp' => '081290002105',
            ],
            [
                'judul' => 'Dialog Pemuda tentang Lapangan Kerja dan Keterampilan',
                'jenis' => 'diskusi',
                'tanggal_mulai' => '2026-04-10 19:30:00',
                'durasi_jam' => 3,
                'kapasitas' => 130,
                'lokasi_prefix' => 'Rumah aspirasi',
                'penyelenggara' => 'Bidang Kepemudaan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Fajar Prasetyo',
                'pic_hp' => '081290002106',
            ],
            [
                'judul' => 'Musyawarah Relawan Pelayanan Lingkungan Tingkat Desa',
                'jenis' => 'musyawarah',
                'tanggal_mulai' => '2026-04-18 13:00:00',
                'durasi_jam' => 4,
                'kapasitas' => 90,
                'lokasi_prefix' => 'Balai desa',
                'penyelenggara' => 'Bidang Relawan dan Saksi Nasional DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Ridwan Hakim',
                'pic_hp' => '081290002107',
            ],
            [
                'judul' => 'Festival Anak Soleh dan Lomba Kreativitas Islami',
                'jenis' => 'pendidikan',
                'tanggal_mulai' => '2026-04-26 08:00:00',
                'durasi_jam' => 6,
                'kapasitas' => 210,
                'lokasi_prefix' => 'Kompleks pendidikan',
                'penyelenggara' => 'Bidang Pendidikan dan Kesehatan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Hana Fitriani',
                'pic_hp' => '081290002108',
            ],
            [
                'judul' => 'Aksi Bersih Sungai dan Edukasi Lingkungan Warga',
                'jenis' => 'lainnya',
                'tanggal_mulai' => '2026-05-03 07:30:00',
                'durasi_jam' => 5,
                'kapasitas' => 150,
                'lokasi_prefix' => 'Bantaran sungai',
                'penyelenggara' => 'Bidang Tani, Nelayan dan Lingkungan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'Agus Priatna',
                'pic_hp' => '081290002109',
            ],
            [
                'judul' => 'Temu Warga dan Serap Aspirasi Infrastruktur Kampung',
                'jenis' => 'diskusi',
                'tanggal_mulai' => '2026-05-17 19:45:00',
                'durasi_jam' => 3,
                'kapasitas' => 170,
                'lokasi_prefix' => 'Pusat kegiatan warga',
                'penyelenggara' => 'Bidang Politik, Hukum dan Keamanan DPD PKS Kabupaten Bekasi',
                'pic_nama' => 'M. Arifin',
                'pic_hp' => '081290002110',
            ],
        ];
    }

    private function buildDescription(array $blueprint, TargetWilayah $village): string
    {
        return sprintf(
            '%s telah dilaksanakan di %s, Kecamatan %s, sebagai bagian dari pelayanan masyarakat dan penguatan kolaborasi warga di %s.',
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
