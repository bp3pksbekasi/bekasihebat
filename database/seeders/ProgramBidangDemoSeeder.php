<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AgendaDpd;
use App\Models\BidangDpd;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgramBidangDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BidangDpdSeeder::class);

        $creatorId = User::query()->value('id');
        $tahun = (string) now()->year;
        $marker = 'Seeder demo Program Bidang';

        $configs = [
            'advokasi' => [
                'pic_nama' => 'Tim Advokasi DPD',
                'pic_hp' => '0812-1000-0001',
                'primary' => [
                    'nama' => 'Posko Advokasi Warga',
                    'deskripsi' => 'Pendampingan aduan warga per dapil dan layanan konsultasi hukum dasar.',
                    'target_angka' => 12,
                    'realisasi' => 8,
                    'satuan' => 'kasus',
                    'periode' => 'triwulan_2',
                    'deadline' => now()->addDays(45)->toDateString(),
                    'pic_nama' => 'Ust. Rudi Hartono',
                    'pic_hp' => '0812-1000-1101',
                    'dapil' => 'Dapil 1',
                    'jenis_agenda' => 'kunjungan',
                ],
            ],
            'relawan-saksi' => [
                'pic_nama' => 'Tim Relawan & Saksi',
                'pic_hp' => '0812-1000-0002',
                'primary' => [
                    'nama' => 'Konsolidasi Relawan Inti',
                    'deskripsi' => 'Penguatan relawan inti dan simulasi koordinasi saksi TPS.',
                    'target_angka' => 20,
                    'realisasi' => 14,
                    'satuan' => 'relawan',
                    'periode' => 'triwulan_2',
                    'deadline' => now()->addDays(60)->toDateString(),
                    'pic_nama' => 'Ahmad Fikri',
                    'pic_hp' => '0812-1000-1102',
                    'dapil' => 'Dapil 2',
                    'jenis_agenda' => 'rapat',
                ],
            ],
            'polhukam' => [
                'pic_nama' => 'Tim Polhukam',
                'pic_hp' => '0812-1000-0003',
                'primary' => [
                    'nama' => 'Forum Kajian Isu Strategis',
                    'deskripsi' => 'Pemetaan isu politik, hukum, dan keamanan untuk bahan komunikasi publik.',
                    'target_angka' => 6,
                    'realisasi' => 3,
                    'satuan' => 'forum',
                    'periode' => 'semester_1',
                    'deadline' => now()->addDays(50)->toDateString(),
                    'pic_nama' => 'Drs. Hendra Wijaya',
                    'pic_hp' => '0812-1000-1103',
                    'dapil' => 'Dapil 3',
                    'jenis_agenda' => 'musyawarah',
                ],
            ],
            'ekonomi' => [
                'pic_nama' => 'Tim Ekonomi',
                'pic_hp' => '0812-1000-0004',
                'primary' => [
                    'nama' => 'Kelas UMKM Kader',
                    'deskripsi' => 'Pembinaan usaha keluarga kader dan pendampingan akses pasar lokal.',
                    'target_angka' => 15,
                    'realisasi' => 15,
                    'satuan' => 'peserta',
                    'periode' => 'semester_1',
                    'deadline' => now()->subDays(5)->toDateString(),
                    'pic_nama' => 'Siti Rahma',
                    'pic_hp' => '0812-1000-1104',
                    'dapil' => 'Dapil 4',
                    'jenis_agenda' => 'pelatihan',
                ],
            ],
            'pendidikan-kesehatan' => [
                'pic_nama' => 'Tim DikKes',
                'pic_hp' => '0812-1000-0005',
                'primary' => [
                    'nama' => 'Layanan Edukasi & Cek Kesehatan',
                    'deskripsi' => 'Roadshow edukasi sekolah dan skrining kesehatan ringan di wilayah binaan.',
                    'target_angka' => 10,
                    'realisasi' => 6,
                    'satuan' => 'kegiatan',
                    'periode' => 'triwulan_3',
                    'deadline' => now()->addDays(75)->toDateString(),
                    'pic_nama' => 'dr. Luthfiah',
                    'pic_hp' => '0812-1000-1105',
                    'dapil' => 'Dapil 5',
                    'jenis_agenda' => 'sosialisasi',
                ],
            ],
            'keumatan-dakwah' => [
                'pic_nama' => 'Tim Keumatan',
                'pic_hp' => '0812-1000-0006',
                'primary' => [
                    'nama' => 'Safari Dakwah Komunitas',
                    'deskripsi' => 'Penguatan pembinaan keumatan, majelis taklim, dan dakwah sosial.',
                    'target_angka' => 18,
                    'realisasi' => 9,
                    'satuan' => 'majelis',
                    'periode' => 'sepanjang_tahun',
                    'deadline' => now()->addDays(90)->toDateString(),
                    'pic_nama' => 'Ust. Salman',
                    'pic_hp' => '0812-1000-1106',
                    'dapil' => 'Dapil 6',
                    'jenis_agenda' => 'sosialisasi',
                ],
            ],
            'perempuan-keluarga' => [
                'pic_nama' => 'Tim Perempuan & Keluarga',
                'pic_hp' => '0812-1000-0007',
                'primary' => [
                    'nama' => 'Sekolah Ibu Tangguh',
                    'deskripsi' => 'Pendampingan keluarga tangguh dan penguatan peran perempuan di lingkungan warga.',
                    'target_angka' => 8,
                    'realisasi' => 5,
                    'satuan' => 'kelas',
                    'periode' => 'triwulan_2',
                    'deadline' => now()->addDays(40)->toDateString(),
                    'pic_nama' => 'Hj. Nur Aisyah',
                    'pic_hp' => '0812-1000-1107',
                    'dapil' => 'Dapil 7',
                    'jenis_agenda' => 'pelatihan',
                ],
            ],
            'kepemudaan' => [
                'pic_nama' => 'Tim Kepemudaan',
                'pic_hp' => '0812-1000-0008',
                'primary' => [
                    'nama' => 'Akademi Aktivis Muda',
                    'deskripsi' => 'Pelatihan kepemimpinan dan jejaring pemuda per kecamatan.',
                    'target_angka' => 30,
                    'realisasi' => 18,
                    'satuan' => 'peserta',
                    'periode' => 'semester_2',
                    'deadline' => now()->addDays(80)->toDateString(),
                    'pic_nama' => 'Rizky Maulana',
                    'pic_hp' => '0812-1000-1108',
                    'dapil' => 'Dapil 3',
                    'jenis_agenda' => 'pelatihan',
                ],
            ],
            'tani-nelayan-lh' => [
                'pic_nama' => 'Tim Tani & LH',
                'pic_hp' => '0812-1000-0009',
                'primary' => [
                    'nama' => 'Gerakan Kebun Warga',
                    'deskripsi' => 'Demo plot urban farming dan edukasi lingkungan hidup di desa binaan.',
                    'target_angka' => 14,
                    'realisasi' => 7,
                    'satuan' => 'lokasi',
                    'periode' => 'semester_2',
                    'deadline' => now()->addDays(70)->toDateString(),
                    'pic_nama' => 'Yusuf Tani',
                    'pic_hp' => '0812-1000-1109',
                    'dapil' => 'Dapil 4',
                    'jenis_agenda' => 'kunjungan',
                ],
            ],
            'ketenagakerjaan' => [
                'pic_nama' => 'Tim Naker',
                'pic_hp' => '0812-1000-0010',
                'primary' => [
                    'nama' => 'Job Matching Kader Muda',
                    'deskripsi' => 'Klinik kerja dan pendampingan kesiapan tenaga kerja muda.',
                    'target_angka' => 25,
                    'realisasi' => 10,
                    'satuan' => 'peserta',
                    'periode' => 'triwulan_3',
                    'deadline' => now()->addDays(65)->toDateString(),
                    'pic_nama' => 'Dedi Pratama',
                    'pic_hp' => '0812-1000-1110',
                    'dapil' => 'Dapil 2',
                    'jenis_agenda' => 'pelatihan',
                ],
            ],
            'seni-budaya' => [
                'pic_nama' => 'Tim Seni & Budaya',
                'pic_hp' => '0812-1000-0011',
                'primary' => [
                    'nama' => 'Festival Budaya Kampung',
                    'deskripsi' => 'Panggung budaya lokal untuk memperkuat kedekatan dengan komunitas warga.',
                    'target_angka' => 5,
                    'realisasi' => 2,
                    'satuan' => 'event',
                    'periode' => 'semester_2',
                    'deadline' => now()->addDays(100)->toDateString(),
                    'pic_nama' => 'Aulia Senja',
                    'pic_hp' => '0812-1000-1111',
                    'dapil' => 'Dapil 5',
                    'jenis_agenda' => 'sosialisasi',
                ],
            ],
            'humas' => [
                'pic_nama' => 'Tim Humas DPD',
                'pic_hp' => '0812-1000-0012',
                'primary' => [
                    'nama' => 'Media Visit & Press Briefing',
                    'deskripsi' => 'Penguatan hubungan media lokal dan penyusunan narasi agenda partai.',
                    'target_angka' => 9,
                    'realisasi' => 6,
                    'satuan' => 'media',
                    'periode' => 'triwulan_2',
                    'deadline' => now()->addDays(35)->toDateString(),
                    'pic_nama' => 'Farhan Media',
                    'pic_hp' => '0812-1000-1112',
                    'dapil' => 'Dapil 1',
                    'jenis_agenda' => 'rapat',
                ],
            ],
            'komdigi' => [
                'pic_nama' => 'Tim Komdigi',
                'pic_hp' => '0812-1000-0013',
                'primary' => [
                    'nama' => 'Dashboard Konten Digital',
                    'deskripsi' => 'Penguatan alur produksi konten, kalender konten, dan distribusi kanal digital.',
                    'target_angka' => 40,
                    'realisasi' => 22,
                    'satuan' => 'konten',
                    'periode' => 'sepanjang_tahun',
                    'deadline' => now()->addDays(55)->toDateString(),
                    'pic_nama' => 'Nadia Digital',
                    'pic_hp' => '0812-1000-1113',
                    'dapil' => 'Dapil 6',
                    'jenis_agenda' => 'rapat',
                ],
            ],
        ];

        foreach ($configs as $slug => $config) {
            $bidang = BidangDpd::query()->where('slug', $slug)->first();

            if (! $bidang) {
                continue;
            }

            $bidang->update([
                'pic_nama' => $config['pic_nama'],
                'pic_hp' => $config['pic_hp'],
            ]);

            $primary = $config['primary'];
            $programUtama = ProgramKerja::query()->updateOrCreate(
                [
                    'bidang_dpd_id' => $bidang->id,
                    'nama_program' => $primary['nama'],
                    'tahun' => $tahun,
                ],
                [
                    'deskripsi' => $primary['deskripsi'],
                    'target_teks' => null,
                    'target_angka' => $primary['target_angka'],
                    'realisasi' => $primary['realisasi'],
                    'satuan' => $primary['satuan'],
                    'periode' => $primary['periode'],
                    'deadline' => $primary['deadline'],
                    'pic_nama' => $primary['pic_nama'],
                    'pic_hp' => $primary['pic_hp'],
                    'status' => $primary['realisasi'] === 0 ? 'belum_mulai' : 'berjalan',
                    'progress_pct' => 0,
                    'catatan' => $marker,
                    'created_by' => $creatorId,
                ]
            );
            $programUtama->updateProgress();

            $secondaryTarget = max((int) ceil($primary['target_angka'] / 2), 4);
            $secondaryRealisasi = (int) floor($secondaryTarget / 2);
            $programMonitoring = ProgramKerja::query()->updateOrCreate(
                [
                    'bidang_dpd_id' => $bidang->id,
                    'nama_program' => 'Monitoring & Evaluasi '.$bidang->nama,
                    'tahun' => $tahun,
                ],
                [
                    'deskripsi' => 'Monitoring capaian, evaluasi bulanan, dan rapat tindak lanjut bidang '.$bidang->nama.'.',
                    'target_teks' => 'Laporan evaluasi bulanan',
                    'target_angka' => $secondaryTarget,
                    'realisasi' => $secondaryRealisasi,
                    'satuan' => 'laporan',
                    'periode' => 'bulanan',
                    'deadline' => now()->addDays(30)->toDateString(),
                    'pic_nama' => $config['pic_nama'],
                    'pic_hp' => $config['pic_hp'],
                    'status' => $secondaryRealisasi === 0 ? 'tertunda' : 'berjalan',
                    'progress_pct' => 0,
                    'catatan' => $marker,
                    'created_by' => $creatorId,
                ]
            );
            $programMonitoring->updateProgress();

            AgendaDpd::query()->updateOrCreate(
                [
                    'judul' => 'Pelaksanaan '.$primary['nama'],
                    'tanggal_mulai' => now()->subDays(10)->setTime(9, 0, 0),
                ],
                [
                    'bidang_dpd_id' => $bidang->id,
                    'program_kerja_id' => $programUtama->id,
                    'jenis' => $primary['jenis_agenda'],
                    'tanggal_selesai' => now()->subDays(10)->setTime(12, 0, 0),
                    'lokasi' => 'Sekretariat '.$bidang->nama,
                    'dapil_terkait' => $primary['dapil'],
                    'peserta_target' => max((int) ceil($primary['target_angka'] * 1.5), 10),
                    'peserta_hadir' => max((int) ceil($primary['target_angka'] * 1.2), 8),
                    'status' => 'selesai',
                    'catatan' => $marker,
                    'hasil' => 'Agenda selesai dan capaian program bergerak sesuai target bidang.',
                    'created_by' => $creatorId,
                ]
            );

            AgendaDpd::query()->updateOrCreate(
                [
                    'judul' => 'Rapat tindak lanjut '.$bidang->nama,
                    'tanggal_mulai' => now()->addDays(4)->setTime(13, 30, 0),
                ],
                [
                    'bidang_dpd_id' => $bidang->id,
                    'program_kerja_id' => $programMonitoring->id,
                    'jenis' => 'rapat',
                    'tanggal_selesai' => now()->addDays(4)->setTime(15, 30, 0),
                    'lokasi' => 'Aula DPD Kabupaten Bekasi',
                    'dapil_terkait' => $primary['dapil'],
                    'peserta_target' => 12,
                    'peserta_hadir' => 0,
                    'status' => 'dijadwalkan',
                    'catatan' => $marker,
                    'hasil' => null,
                    'created_by' => $creatorId,
                ]
            );

            AgendaDpd::query()->updateOrCreate(
                [
                    'judul' => 'Kunjungan lapangan '.$bidang->nama,
                    'tanggal_mulai' => now()->addDays(9)->setTime(8, 30, 0),
                ],
                [
                    'bidang_dpd_id' => $bidang->id,
                    'program_kerja_id' => $programUtama->id,
                    'jenis' => 'kunjungan',
                    'tanggal_selesai' => now()->addDays(9)->setTime(11, 30, 0),
                    'lokasi' => 'Wilayah '.$primary['dapil'],
                    'dapil_terkait' => $primary['dapil'],
                    'peserta_target' => 20,
                    'peserta_hadir' => 0,
                    'status' => 'dijadwalkan',
                    'catatan' => $marker,
                    'hasil' => null,
                    'created_by' => $creatorId,
                ]
            );
        }

        $this->command?->info('Seeder demo Program Bidang selesai dijalankan.');
    }
}
