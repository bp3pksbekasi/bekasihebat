<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DataRw;
use App\Models\Event;
use App\Models\EventApproval;
use App\Models\KegiatanRw;
use App\Models\Korte;
use App\Models\Korwe;
use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleKarangsatriaBekasi4Seeder extends Seeder
{
    public function run(): void
    {
        $target = TargetWilayah::query()
            ->where('dapil', 'BEKASI 4')
            ->where('kecamatan', 'TAMBUN UTARA')
            ->where('desa', 'KARANGSATRIA')
            ->firstOrFail();

        $creatorId = User::query()->orderBy('id')->value('id');
        $dpraId = (int) \Illuminate\Support\Facades\DB::table('dpra')->orderBy('id')->value('id');

        $rwSamples = [
            '008' => [
                'korwe' => [
                    'nama_koordinator' => 'Muladi',
                    'no_hp' => '081290001008',
                    'status' => 'terbentuk',
                    'catatan' => 'Korwe aktif dan rutin koordinasi mingguan dengan para ketua RT.',
                    'tanggal_terbentuk' => '2026-04-12',
                ],
                'kortes' => [
                    ['rt' => '001', 'nama' => 'Abdul Karim', 'hp' => '081311100801', 'status' => 'terbentuk', 'catatan' => 'Korte aktif untuk door-to-door.'],
                    ['rt' => '002', 'nama' => 'Jajang Nurjaman', 'hp' => '081311100802', 'status' => 'terbentuk', 'catatan' => 'Menjaga komunikasi dengan tokoh warga.'],
                    ['rt' => '003', 'nama' => 'Asep Hidayat', 'hp' => '081311100803', 'status' => 'proses', 'catatan' => 'Menunggu pengukuhan formal.'],
                    ['rt' => '004', 'nama' => 'Budi Santoso', 'hp' => '081311100804', 'status' => 'belum', 'catatan' => 'Masih tahap pemetaan kader.'],
                ],
                'kegiatan' => [
                    [
                        'jenis_kegiatan' => 'silaturahmi',
                        'tanggal_kegiatan' => '2026-05-08 19:30:00',
                        'pelaksana' => 'Muladi',
                        'jumlah_warga' => 18,
                        'catatan' => 'Silaturahmi tokoh lingkungan dan konsolidasi pasca pembentukan korwe.',
                        'tokoh_ditemui' => 'Pak DKM H. Soleh, Ketua RT 003',
                        'tindak_lanjut' => 'Buat grup koordinasi RW 008 dan jadwalkan pengajian bulanan.',
                        'jadwal_berikutnya' => '2026-06-12',
                        'tampil_galeri' => false,
                    ],
                    [
                        'jenis_kegiatan' => 'pengajian',
                        'tanggal_kegiatan' => '2026-05-24 20:00:00',
                        'pelaksana' => 'Muladi',
                        'jumlah_warga' => 42,
                        'catatan' => 'Pengajian warga sekaligus penguatan jejaring ibu-ibu majelis taklim.',
                        'tokoh_ditemui' => 'Ustadzah Neni dan koordinator majelis taklim',
                        'tindak_lanjut' => 'Dorong pembentukan relawan ibu-ibu untuk agenda sosial.',
                        'jadwal_berikutnya' => '2026-06-21',
                        'tampil_galeri' => true,
                    ],
                ],
                'profil' => [
                    'tipologi' => 'campuran',
                    'ekonomi_dominan' => 'pedagang',
                    'profil_warga' => 'Mayoritas keluarga muda dan pedagang kecil, responsif pada kegiatan sosial dan keagamaan.',
                    'suara_pks_2019' => 495,
                    'faktor_penyebab' => 'Kedekatan tokoh majelis taklim dan konsistensi agenda silaturahmi warga.',
                    'anggota_pks' => 'ada',
                    'jumlah_kta' => 18,
                    'upa_rw_status' => 'aktif',
                    'upa_rw_nama' => 'Ibu Rina',
                    'rki_status' => 'aktif',
                    'rki_nama' => 'Pak Yusuf',
                    'senam_status' => 'aktif',
                    'senam_nama' => 'Komunitas Senam RW 008',
                    'relawan_milenial_status' => 'aktif',
                    'relawan_milenial_nama' => 'Tim Pemuda RW 008',
                    'caleg_terpilih_ada' => true,
                    'caleg_terpilih_nama' => 'Caleg PKS Dapil 4',
                    'afiliasi_rw_rt' => 'Netral cenderung terbuka, hubungan baik dengan pengurus RW dan sebagian RT.',
                    'afiliasi_posyandu_dkm' => 'DKM dan kader posyandu cukup dekat dan mudah diajak kolaborasi.',
                    'kompetitor_status' => 'sedang',
                    'kompetitor_detail' => 'Ada pengaruh partai nasionalis di RT 004 namun belum dominan.',
                    'tim_sukses_status' => 'ada',
                    'tim_sukses_detail' => 'Ada tim sukses caleg lama, tapi sebagian sudah pasif.',
                    'strategi' => 'Perkuat agenda pengajian, layanan sosial, dan konsolidasi keluarga muda.',
                    'penanggung_jawab' => 'Muladi',
                    'keterangan_lain' => 'RW prioritas utama dengan jejaring organik yang sudah tumbuh.',
                ],
            ],
            '001' => [
                'korwe' => [
                    'nama_koordinator' => 'H. Rahmat',
                    'no_hp' => '081290001001',
                    'status' => 'terbentuk',
                    'catatan' => 'Korwe senior, fokus penguatan jaringan RT dan keluarga muda.',
                    'tanggal_terbentuk' => '2026-04-20',
                ],
                'kortes' => [
                    ['rt' => '001', 'nama' => 'Nurdin', 'hp' => '081311100101', 'status' => 'terbentuk', 'catatan' => 'Korte inti wilayah padat penduduk.'],
                    ['rt' => '002', 'nama' => 'Saepudin', 'hp' => '081311100102', 'status' => 'terbentuk', 'catatan' => 'Aktif mendata keluarga potensial.'],
                    ['rt' => '003', 'nama' => 'Yayan Sopyan', 'hp' => '081311100103', 'status' => 'proses', 'catatan' => 'Sudah siap ikut konsolidasi bulanan.'],
                ],
                'kegiatan' => [
                    [
                        'jenis_kegiatan' => 'door_to_door',
                        'tanggal_kegiatan' => '2026-05-15 16:00:00',
                        'pelaksana' => 'H. Rahmat',
                        'jumlah_warga' => 27,
                        'catatan' => 'Kunjungan rumah ke rumah fokus pemetaan simpul keluarga muda.',
                        'tokoh_ditemui' => 'Ibu PKK RW 001',
                        'tindak_lanjut' => 'Data keluarga potensial diserahkan ke tim korte.',
                        'jadwal_berikutnya' => '2026-06-05',
                        'tampil_galeri' => false,
                    ],
                ],
                'profil' => [
                    'tipologi' => 'perkampungan',
                    'ekonomi_dominan' => 'campuran',
                    'profil_warga' => 'Warga heterogen dengan ikatan RT cukup kuat dan tokoh lokal berpengaruh.',
                    'suara_pks_2019' => 467,
                    'faktor_penyebab' => 'Korwe senior aktif menjaga kedekatan dengan tokoh RT dan ibu-ibu PKK.',
                    'anggota_pks' => 'ada',
                    'jumlah_kta' => 12,
                    'upa_rw_status' => 'aktif',
                    'upa_rw_nama' => 'Bu Sari',
                    'rki_status' => 'proses',
                    'rki_nama' => 'Pak Ridwan',
                    'senam_status' => 'belum',
                    'senam_nama' => null,
                    'relawan_milenial_status' => 'proses',
                    'relawan_milenial_nama' => 'Komunitas futsal RW 001',
                    'caleg_terpilih_ada' => false,
                    'caleg_terpilih_nama' => null,
                    'afiliasi_rw_rt' => 'Pengurus RW cukup cair, sebagian RT dekat ke tokoh lokal non-partai.',
                    'afiliasi_posyandu_dkm' => 'Kader PKK lebih dominan daripada DKM.',
                    'kompetitor_status' => 'rendah',
                    'kompetitor_detail' => 'Kompetitor ada, namun tidak terlalu aktif pada isu lokal.',
                    'tim_sukses_status' => 'ada',
                    'tim_sukses_detail' => 'Masih ada simpul tim sukses lama di dua RT.',
                    'strategi' => 'Fokus pendekatan keluarga muda dan forum warga skala RT.',
                    'penanggung_jawab' => 'H. Rahmat',
                    'keterangan_lain' => 'Potensi penguatan KTA dan relawan milenial masih terbuka lebar.',
                ],
            ],
            '002' => [
                'korwe' => [
                    'nama_koordinator' => 'Dedi Mulyana',
                    'no_hp' => '081290001002',
                    'status' => 'proses',
                    'catatan' => 'Calon korwe sudah aktif hadir di kegiatan warga, menunggu pengukuhan.',
                    'tanggal_terbentuk' => '2026-05-10',
                ],
                'kortes' => [
                    ['rt' => '001', 'nama' => 'Hendra Gunawan', 'hp' => '081311100201', 'status' => 'terbentuk', 'catatan' => 'Menangani simpul pemuda.'],
                    ['rt' => '002', 'nama' => 'Rudi Hartono', 'hp' => '081311100202', 'status' => 'proses', 'catatan' => 'Masih pendekatan intensif.'],
                ],
                'kegiatan' => [
                    [
                        'jenis_kegiatan' => 'diskusi',
                        'tanggal_kegiatan' => '2026-05-27 19:45:00',
                        'pelaksana' => 'Dedi Mulyana',
                        'jumlah_warga' => 21,
                        'catatan' => 'Diskusi warga tentang sampah lingkungan dan kebutuhan pos ronda.',
                        'tokoh_ditemui' => 'Ketua karang taruna RW 002',
                        'tindak_lanjut' => 'Sinkronkan dengan proposal kegiatan sosial RT setempat.',
                        'jadwal_berikutnya' => '2026-06-14',
                        'tampil_galeri' => false,
                    ],
                ],
                'profil' => [
                    'tipologi' => 'campuran',
                    'ekonomi_dominan' => 'informal',
                    'profil_warga' => 'Didominasi pekerja informal dan pemuda, isu lingkungan cukup menonjol.',
                    'suara_pks_2019' => 217,
                    'faktor_penyebab' => 'Kegiatan diskusi lingkungan mulai diterima warga dan tokoh karang taruna.',
                    'anggota_pks' => 'sedikit',
                    'jumlah_kta' => 7,
                    'upa_rw_status' => 'proses',
                    'upa_rw_nama' => 'Bu Dewi',
                    'rki_status' => 'belum',
                    'rki_nama' => null,
                    'senam_status' => 'belum',
                    'senam_nama' => null,
                    'relawan_milenial_status' => 'aktif',
                    'relawan_milenial_nama' => 'Karang Taruna RW 002',
                    'caleg_terpilih_ada' => false,
                    'caleg_terpilih_nama' => null,
                    'afiliasi_rw_rt' => 'Ketua RW terbuka, namun beberapa RT masih wait and see.',
                    'afiliasi_posyandu_dkm' => 'Belum tergarap maksimal.',
                    'kompetitor_status' => 'sedang',
                    'kompetitor_detail' => 'Ada kompetitor yang aktif lewat kegiatan olahraga pemuda.',
                    'tim_sukses_status' => 'tidak_tahu',
                    'tim_sukses_detail' => 'Belum terpetakan rinci.',
                    'strategi' => 'Masuk lewat isu lingkungan, kepemudaan, dan forum diskusi warga.',
                    'penanggung_jawab' => 'Dedi Mulyana',
                    'keterangan_lain' => 'Butuh penguatan struktur sebelum ditetapkan sebagai korwe definitif.',
                ],
            ],
            '010' => [
                'korwe' => [
                    'nama_koordinator' => 'Ujang Supriatna',
                    'no_hp' => '081290001010',
                    'status' => 'belum',
                    'catatan' => 'Belum ada korwe definitif, masih pemetaan tokoh kunci.',
                    'tanggal_terbentuk' => null,
                ],
                'kortes' => [
                    ['rt' => '001', 'nama' => 'Ari Saputra', 'hp' => '081311101001', 'status' => 'proses', 'catatan' => 'Siap bantu kegiatan sosial.'],
                ],
                'kegiatan' => [
                    [
                        'jenis_kegiatan' => 'baksos',
                        'tanggal_kegiatan' => '2026-05-30 09:00:00',
                        'pelaksana' => 'Tim RW 010',
                        'jumlah_warga' => 35,
                        'catatan' => 'Bakti sosial dan pembagian paket sembako untuk lansia.',
                        'tokoh_ditemui' => 'Ketua RT 001 dan kader posyandu',
                        'tindak_lanjut' => 'Data lansia rentan diperbarui untuk agenda kesehatan.',
                        'jadwal_berikutnya' => '2026-06-18',
                        'tampil_galeri' => true,
                    ],
                ],
                'profil' => [
                    'tipologi' => 'perkampungan',
                    'ekonomi_dominan' => 'pabrik',
                    'profil_warga' => 'Banyak keluarga pekerja pabrik, cukup responsif jika kegiatan menyentuh kebutuhan langsung.',
                    'suara_pks_2019' => 324,
                    'faktor_penyebab' => 'Aksi sosial lebih diterima daripada pendekatan politik formal.',
                    'anggota_pks' => 'sedikit',
                    'jumlah_kta' => 5,
                    'upa_rw_status' => 'belum',
                    'upa_rw_nama' => null,
                    'rki_status' => 'belum',
                    'rki_nama' => null,
                    'senam_status' => 'proses',
                    'senam_nama' => 'Komunitas ibu-ibu RT 001',
                    'relawan_milenial_status' => 'proses',
                    'relawan_milenial_nama' => 'Pemuda RW 010',
                    'caleg_terpilih_ada' => false,
                    'caleg_terpilih_nama' => null,
                    'afiliasi_rw_rt' => 'Ketua RT cukup dekat, pengurus RW masih netral.',
                    'afiliasi_posyandu_dkm' => 'Posyandu lebih mudah didekati dibanding DKM.',
                    'kompetitor_status' => 'sedang',
                    'kompetitor_detail' => 'Kompetitor hadir saat momen bantuan sosial.',
                    'tim_sukses_status' => 'tidak_tahu',
                    'tim_sukses_detail' => 'Belum ada pemetaan aktor secara lengkap.',
                    'strategi' => 'Perbanyak aksi sosial dan bangun figur lokal sebelum pembentukan korwe.',
                    'penanggung_jawab' => 'Ujang Supriatna',
                    'keterangan_lain' => 'RW potensial untuk pendekatan layanan sosial berulang.',
                ],
            ],
        ];

        foreach ($rwSamples as $rw => $payload) {
            Korwe::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $target->id,
                    'nomor_rw' => $rw,
                ],
                [
                    ...$payload['korwe'],
                    'created_by' => $creatorId,
                ]
            );

            foreach ($payload['kortes'] as $korte) {
                Korte::query()->updateOrCreate(
                    [
                        'target_wilayah_id' => $target->id,
                        'nomor_rw' => $rw,
                        'nomor_rt' => $korte['rt'],
                    ],
                    [
                        'nama_koordinator' => $korte['nama'],
                        'no_hp' => $korte['hp'],
                        'status' => $korte['status'],
                        'catatan' => $korte['catatan'],
                        'tanggal_terbentuk' => $korte['status'] === 'terbentuk' ? '2026-05-03' : null,
                        'created_by' => $creatorId,
                    ]
                );
            }

            foreach ($payload['kegiatan'] as $kegiatan) {
                KegiatanRw::query()->updateOrCreate(
                    [
                        'target_wilayah_id' => $target->id,
                        'nomor_rw' => $rw,
                        'jenis_kegiatan' => $kegiatan['jenis_kegiatan'],
                        'tanggal_kegiatan' => $kegiatan['tanggal_kegiatan'],
                    ],
                    [
                        'dapil' => $target->dapil,
                        'kecamatan' => $target->kecamatan,
                        'desa' => $target->desa,
                        'pelaksana' => $kegiatan['pelaksana'],
                        'jumlah_warga' => $kegiatan['jumlah_warga'],
                        'catatan' => $kegiatan['catatan'],
                        'foto' => null,
                        'tampil_galeri' => $kegiatan['tampil_galeri'],
                        'tokoh_ditemui' => $kegiatan['tokoh_ditemui'],
                        'tindak_lanjut' => $kegiatan['tindak_lanjut'],
                        'jadwal_berikutnya' => $kegiatan['jadwal_berikutnya'],
                        'created_by' => $creatorId,
                    ]
                );
            }

            $profilPayload = [
                'target_wilayah_id' => $target->id,
                'nomor_rw' => $rw,
                'dapil' => $target->dapil,
                'kecamatan' => $target->kecamatan,
                'desa' => $target->desa,
                ...$payload['profil'],
                'filled_by' => $creatorId,
                'filled_at' => now(),
            ];

            $profil = ProfilRw::query()->updateOrCreate(
                [
                    'target_wilayah_id' => $target->id,
                    'nomor_rw' => $rw,
                ],
                $profilPayload
            );

            $completion = $profil->calculateCompletion();

            $profil->update([
                'completion_percent' => $completion,
                'is_complete' => $completion >= 80,
            ]);
        }

        $eventSamples = [
            [
                'rw' => '008',
                'jenis_kegiatan' => 'pengajian',
                'tanggal_kegiatan' => '2026-05-24 20:00:00',
                'judul' => 'Pengajian Warga RW 008 Karangsatria',
                'deskripsi' => 'Pengajian warga dan konsolidasi majelis taklim hasil tindak lanjut Sisir RW Karangsatria.',
                'jenis' => 'pengajian',
                'tanggal_mulai' => '2026-06-21 19:30:00',
                'tanggal_selesai' => '2026-06-21 22:00:00',
                'lokasi' => 'Masjid Jami RW 008, Karangsatria',
                'kapasitas' => 120,
                'status' => Event::STATUS_DISETUJUI,
                'level_approval' => 'selesai',
                'penyelenggara' => 'Sisir RW Karangsatria',
                'pic_nama' => 'Muladi',
                'pic_hp' => '081290001008',
                'is_public' => true,
                'organizer_dpra_id' => null,
                'approvals' => [
                    ['level' => 'dpra', 'status' => 'approved', 'catatan' => 'Layak jalan di tingkat RW.'],
                    ['level' => 'dpc', 'status' => 'approved', 'catatan' => 'Sinkron dengan agenda pembinaan warga.'],
                    ['level' => 'dpd', 'status' => 'approved', 'catatan' => 'Disetujui untuk publikasi terbatas.'],
                ],
            ],
            [
                'rw' => '002',
                'jenis_kegiatan' => 'diskusi',
                'tanggal_kegiatan' => '2026-05-27 19:45:00',
                'judul' => 'Diskusi Warga Lingkungan RW 002',
                'deskripsi' => 'Forum warga terkait lingkungan, sampah, dan konsolidasi relawan muda.',
                'jenis' => 'diskusi',
                'tanggal_mulai' => '2026-06-14 19:30:00',
                'tanggal_selesai' => '2026-06-14 21:30:00',
                'lokasi' => 'Balai Warga RW 002, Karangsatria',
                'kapasitas' => 80,
                'status' => Event::STATUS_MENUNGGU,
                'level_approval' => 'dpc',
                'penyelenggara' => 'Sisir RW Karangsatria',
                'pic_nama' => 'Dedi Mulyana',
                'pic_hp' => '081290001002',
                'is_public' => false,
                'organizer_dpra_id' => null,
                'approvals' => [
                    ['level' => 'dpra', 'status' => 'approved', 'catatan' => 'Sudah siap diajukan ke level berikutnya.'],
                    ['level' => 'dpc', 'status' => 'pending', 'catatan' => null],
                    ['level' => 'dpd', 'status' => 'pending', 'catatan' => null],
                ],
            ],
            [
                'rw' => '010',
                'jenis_kegiatan' => 'baksos',
                'tanggal_kegiatan' => '2026-05-30 09:00:00',
                'judul' => 'Bakti Sosial Lansia RW 010',
                'deskripsi' => 'Kegiatan sosial lanjutan untuk lansia dan keluarga rentan hasil pemetaan Sisir RW.',
                'jenis' => 'baksos',
                'tanggal_mulai' => '2026-06-18 08:00:00',
                'tanggal_selesai' => '2026-06-18 12:00:00',
                'lokasi' => 'Posyandu RW 010, Karangsatria',
                'kapasitas' => 60,
                'status' => Event::STATUS_DRAFT,
                'level_approval' => 'dpra',
                'penyelenggara' => 'Tim RW 010',
                'pic_nama' => 'Ujang Supriatna',
                'pic_hp' => '081290001010',
                'is_public' => false,
                'organizer_dpra_id' => null,
                'approvals' => [
                    ['level' => 'dpra', 'status' => 'pending', 'catatan' => null],
                    ['level' => 'dpc', 'status' => 'pending', 'catatan' => null],
                    ['level' => 'dpd', 'status' => 'pending', 'catatan' => null],
                ],
            ],
        ];

        foreach ($eventSamples as $sample) {
            $kegiatan = KegiatanRw::query()
                ->where('target_wilayah_id', $target->id)
                ->where('nomor_rw', $sample['rw'])
                ->where('jenis_kegiatan', $sample['jenis_kegiatan'])
                ->where('tanggal_kegiatan', $sample['tanggal_kegiatan'])
                ->first();

            if (! $kegiatan instanceof KegiatanRw) {
                continue;
            }

            $event = Event::query()->firstOrNew([
                'kegiatan_rw_id' => $kegiatan->id,
            ]);

            $event->forceFill([
                'uuid' => $event->uuid ?: (string) Str::uuid(),
                'judul' => $sample['judul'],
                'deskripsi' => $sample['deskripsi'],
                'jenis' => $sample['jenis'],
                'tanggal_mulai' => $sample['tanggal_mulai'],
                'tanggal_selesai' => $sample['tanggal_selesai'],
                'lokasi' => $sample['lokasi'],
                'lokasi_desa' => $target->desa,
                'lokasi_kecamatan' => $target->kecamatan,
                'lokasi_dapil' => $target->dapil,
                'kapasitas' => $sample['kapasitas'],
                'is_public' => $sample['is_public'],
                'status' => $sample['status'],
                'level_approval' => $sample['level_approval'],
                'penyelenggara' => $sample['penyelenggara'],
                'pic_nama' => $sample['pic_nama'],
                'pic_hp' => $sample['pic_hp'],
                'created_by' => $creatorId,
                'organizer_dpra_id' => $sample['organizer_dpra_id'] ?? $dpraId,
                'title' => $sample['judul'],
                'slug' => Str::slug($sample['judul']) . '-' . $sample['rw'],
                'description' => $sample['deskripsi'],
                'starts_at' => $sample['tanggal_mulai'],
                'ends_at' => $sample['tanggal_selesai'],
                'location_name' => $sample['lokasi'],
                'location_address' => $sample['lokasi'],
                'visibility' => $sample['is_public'] ? 'public' : 'internal',
                'max_participants' => $sample['kapasitas'],
            ])->save();

            foreach ($sample['approvals'] as $approval) {
                EventApproval::query()->updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'level' => $approval['level'],
                    ],
                    [
                        'status' => $approval['status'],
                        'approver_id' => $approval['status'] === 'approved' ? $creatorId : null,
                        'catatan' => $approval['catatan'],
                        'decided_at' => $approval['status'] === 'approved' ? now() : null,
                    ]
                );
            }
        }
    }
}
