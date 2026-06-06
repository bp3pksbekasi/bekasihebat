# Prompt Trae — Seeder Data Dummy Lengkap

Buat seeder data dummy yang realistis untuk ujicoba seluruh fitur. Data harus terasa nyata — nama Sunda/Betawi, distribusi tidak merata, ada timeline. Langsung buat, JANGAN test, JANGAN tanya.

---

```
Buat DatabaseSeeder dan semua seeder individual. Jalankan setelah semua migration selesai.

PENTING:
- Semua model pakai UUID → jangan hardcode ID, pakai variable atau ->id
- Gunakan data target_wilayahs yang SUDAH ADA dari import (jangan buat baru)
- Pilih 8 desa yang SUDAH ADA di database sebagai fokus dummy data
- Pastikan semua foreign key valid (target_wilayah_id harus exist)
- Timeline: data tersebar dari Desember 2025 — Juni 2026

== CARA JALANKAN ==
php artisan db:seed --class=DummyDataSeeder

JANGAN jalankan migrate:fresh (akan hapus data target_wilayahs yang sudah diimport).

== FILE: database/seeders/DummyDataSeeder.php ==

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// Import semua model
use App\Models\User;
use App\Models\Kader;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use App\Models\UpaRwMember;
use App\Models\ProfilRw;
use App\Models\DataRw;
use App\Models\KegiatanRw;
use App\Models\KontakWarga;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPeserta;
use App\Models\TitikRki;
use App\Models\TitikSenam;
use App\Models\LogSesi;
use App\Models\BidangDpd;
use App\Models\ProgramKerja;
use App\Models\AgendaDpd;
use App\Models\AnggotaDewan;
use App\Models\KontenMedsos;
use App\Models\MateriDigital;
use App\Models\DistribusiMateri;
use App\Models\Member;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use App\Models\TargetWilayah;

class DummyDataSeeder extends Seeder
{
    // Pool nama realistis Sunda/Betawi/Jawa Barat
    private $namaLaki = [
        'Ahmad Fauzi', 'Dedi Kurniawan', 'Ihsan Maulana', 'Udin Saepudin',
        'Rohman Hakim', 'Asep Suryadi', 'Ade Firmansyah', 'Cecep Hidayat',
        'Nandang Hermawan', 'Yusuf Abdillah', 'Hasan Basri', 'Ujang Suryana',
        'Tatang Suherman', 'Ending Sudrajat', 'Koswara', 'Dadan Ramdani',
        'Agus Salim', 'Maman Sulaeman', 'Jajang Nurjaman', 'Eman Sulaeman',
        'Iwan Setiawan', 'Bambang Irawan', 'Hendra Gunawan', 'Ridwan Kamil',
        'Fikri Abdurrahman', 'Zaenal Arifin', 'Mulyadi', 'Wahyu Hidayat',
        'Rudi Hartono', 'Slamet Riyadi', 'Fajar Nugroho', 'Andi Wijaya',
        'Budi Santoso', 'Deni Ramdan', 'Edi Supriadi', 'Gani Mulyadi',
        'Hamdan', 'Ilham Saputra', 'Joko Prasetyo', 'Kurnia Sandi',
    ];

    private $namaPerempuan = [
        'Siti Nurhaliza', 'Hj. Imas Komariah', 'Ibu Tiwi Rahayu', 'Dewi Sartika',
        'Euis Susilawati', 'Neng Rina', 'Yanti Suryani', 'Ai Nurhasanah',
        'Siti Aisyah', 'Nining Herlina', 'Iis Dahlia', 'Entin Supriatin',
        'Cucu Rahayu', 'Imas Masitoh', 'Eneng Siti', 'Wati Sukaesih',
        'Marlina', 'Nurhayati', 'Sumiati', 'Tuti Alawiyah',
        'Sri Mulyani', 'Ratna Dewi', 'Fitri Handayani', 'Lina Marlina',
        'Ani Yudhoyono', 'Kartini', 'Rini Soemarno', 'Wulan Sari',
    ];

    private $allNama;

    private function randomNama($gender = null)
    {
        if ($gender === 'L') return $this->namaLaki[array_rand($this->namaLaki)];
        if ($gender === 'P') return $this->namaPerempuan[array_rand($this->namaPerempuan)];
        return $this->allNama[array_rand($this->allNama)];
    }

    private function randomHP()
    {
        $prefix = ['0812','0813','0857','0858','0878','0821','0822','0852','0853','0856'];
        return $prefix[array_rand($prefix)] . rand(10000000, 99999999);
    }

    private function randomDate($from, $to)
    {
        return Carbon::parse($from)->addDays(rand(0, Carbon::parse($from)->diffInDays($to)));
    }

    public function run()
    {
        $this->allNama = array_merge($this->namaLaki, $this->namaPerempuan);

        $this->command->info('=== Mulai seeding data dummy ===');

        // Ambil 8 desa fokus yang SUDAH ADA di database
        $focusDesas = TargetWilayah::inRandomOrder()->limit(8)->get();
        if ($focusDesas->count() < 8) {
            $this->command->error('Minimal 8 desa harus ada di target_wilayahs. Jalankan import:target-wilayah dulu.');
            return;
        }

        // Kelompokkan: 3 desa aktif, 3 desa sedang, 2 desa baru mulai
        $desaAktif = $focusDesas->take(3);    // banyak data
        $desaSedang = $focusDesas->skip(3)->take(3); // data partial
        $desaBaru = $focusDesas->skip(6)->take(2);   // sedikit data

        $this->command->info("Fokus desa: " . $focusDesas->pluck('desa')->implode(', '));

        // ================================================
        // 1. BIDANG DPD (jika belum ada)
        // ================================================
        $this->command->info('1. Seeding bidang DPD...');
        $bidangs = [
            ['nama' => 'Advokasi Partai', 'slug' => 'advokasi', 'icon' => 'gavel', 'color' => '#6b7280'],
            ['nama' => 'Relawan & Saksi Nasional', 'slug' => 'relawan', 'icon' => 'shield', 'color' => '#fe5000'],
            ['nama' => 'Politik, Hukum & Keamanan', 'slug' => 'polhukam', 'icon' => 'scale', 'color' => '#dc2626'],
            ['nama' => 'Ekonomi, Keuangan & Industri', 'slug' => 'ekonomi', 'icon' => 'coin', 'color' => '#16a34a'],
            ['nama' => 'Pendidikan & Kesehatan', 'slug' => 'dikkes', 'icon' => 'stethoscope', 'color' => '#06b6d4'],
            ['nama' => 'Pembangunan Keumatan & Dakwah', 'slug' => 'dakwah', 'icon' => 'book', 'color' => '#f59e0b'],
            ['nama' => 'Perempuan & Ketahanan Keluarga', 'slug' => 'perempuan', 'icon' => 'gender-female', 'color' => '#ec4899'],
            ['nama' => 'Kepemudaan', 'slug' => 'pemuda', 'icon' => 'users', 'color' => '#3b82f6'],
            ['nama' => 'Tani, Nelayan & Lingkungan', 'slug' => 'tani', 'icon' => 'plant', 'color' => '#22c55e'],
            ['nama' => 'Ketenagakerjaan', 'slug' => 'naker', 'icon' => 'briefcase', 'color' => '#d97706'],
            ['nama' => 'Seni & Budaya', 'slug' => 'senbud', 'icon' => 'palette', 'color' => '#8b5cf6'],
            ['nama' => 'Hubungan Masyarakat', 'slug' => 'humas', 'icon' => 'speakerphone', 'color' => '#7c3aed'],
            ['nama' => 'Komunikasi Digital', 'slug' => 'komdigi', 'icon' => 'device-mobile', 'color' => '#0ea5e9'],
        ];
        foreach ($bidangs as $i => $b) {
            BidangDpd::firstOrCreate(['slug' => $b['slug']], array_merge($b, [
                'pic_nama' => $this->randomNama('L'),
                'pic_hp' => $this->randomHP(),
                'urutan' => $i + 1,
            ]));
        }

        // ================================================
        // 2. KADERS (~80)
        // ================================================
        $this->command->info('2. Seeding kaders...');
        $kaders = collect();
        $jenjangPool = ['penggerak','penggerak','penggerak','pendukung','pendukung','pelopor','pelopor','madya','dewasa','purna'];
        $niaCounter = 100;

        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(6)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                $count = rand(2, 5);
                for ($i = 0; $i < $count; $i++) {
                    $niaCounter++;
                    $nia = str_pad(intdiv($niaCounter, 1000000) % 1000, 3, '0', STR_PAD_LEFT) . '.'
                         . str_pad(intdiv($niaCounter, 1000) % 1000, 3, '0', STR_PAD_LEFT) . '.'
                         . str_pad($niaCounter % 1000, 3, '0', STR_PAD_LEFT);

                    $kader = Kader::create([
                        'nama' => $this->randomNama(),
                        'no_hp' => $this->randomHP(),
                        'nia' => $nia,
                        'jenjang' => $jenjangPool[array_rand($jenjangPool)],
                        'tanggal_jenjang' => $this->randomDate('2024-01-01', '2026-06-01'),
                        'dapil' => $desa->dapil,
                        'kecamatan' => $desa->kecamatan,
                        'desa' => $desa->desa,
                        'nomor_rw' => $rw,
                        'target_wilayah_id' => $desa->id,
                        'status' => 'aktif',
                        'bisa_deploy' => rand(0, 1),
                    ]);
                    $kaders->push($kader);
                }
            }
        }
        foreach ($desaSedang as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(3)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                $niaCounter++;
                $nia = str_pad(intdiv($niaCounter, 1000000) % 1000, 3, '0', STR_PAD_LEFT) . '.'
                     . str_pad(intdiv($niaCounter, 1000) % 1000, 3, '0', STR_PAD_LEFT) . '.'
                     . str_pad($niaCounter % 1000, 3, '0', STR_PAD_LEFT);
                $kader = Kader::create([
                    'nama' => $this->randomNama(),
                    'no_hp' => $this->randomHP(),
                    'nia' => $nia,
                    'jenjang' => ['penggerak','pendukung','pelopor'][array_rand(['penggerak','pendukung','pelopor'])],
                    'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                    'nomor_rw' => $rw, 'target_wilayah_id' => $desa->id, 'status' => 'aktif',
                ]);
                $kaders->push($kader);
            }
        }
        $this->command->info("   → {$kaders->count()} kader dibuat");

        // ================================================
        // 3. USERS (~20)
        // ================================================
        $this->command->info('3. Seeding users...');

        // 2 Admin DPD
        $adminBidangs = ['relawan', 'komdigi'];
        for ($i = 0; $i < 2; $i++) {
            $kader = $kaders->random();
            User::firstOrCreate(['email' => 'admin' . ($i + 1) . '@bekasihebat.id'], [
                'name' => $kader->nama,
                'password' => Hash::make('password'),
                'nia' => $kader->nia,
                'kader_id' => $kader->id,
                'role' => 'admin_dpd',
                'bidang_slug' => null,
                'dapil' => $kader->dapil, 'kecamatan' => $kader->kecamatan,
                'desa' => $kader->desa, 'nomor_rw' => $kader->nomor_rw,
                'status' => 'aktif',
                'last_login_at' => now()->subHours(rand(1, 48)),
            ]);
            $kader->update(['is_activated' => true]);
        }

        // 13 Pengurus Bidang (1 per bidang)
        $bidangSlugs = BidangDpd::pluck('slug');
        foreach ($bidangSlugs as $slug) {
            $kader = $kaders->where('is_activated', false)->random();
            User::firstOrCreate(['email' => $slug . '@bekasihebat.id'], [
                'name' => $kader->nama,
                'password' => Hash::make('password'),
                'nia' => $kader->nia,
                'kader_id' => $kader->id,
                'role' => 'pengurus_bidang',
                'bidang_slug' => $slug,
                'dapil' => $kader->dapil, 'kecamatan' => $kader->kecamatan,
                'desa' => $kader->desa, 'nomor_rw' => $kader->nomor_rw,
                'status' => 'aktif',
                'last_login_at' => rand(0,1) ? now()->subHours(rand(1, 72)) : null,
            ]);
            $kader->update(['is_activated' => true, 'bidang_slug' => $slug]);
        }

        // 5 Kader biasa
        for ($i = 0; $i < 5; $i++) {
            $kader = $kaders->where('is_activated', false)->random();
            User::firstOrCreate(['email' => 'kader' . ($i + 1) . '@bekasihebat.id'], [
                'name' => $kader->nama,
                'password' => Hash::make('password'),
                'nia' => $kader->nia,
                'kader_id' => $kader->id,
                'role' => 'kader',
                'dapil' => $kader->dapil, 'kecamatan' => $kader->kecamatan,
                'desa' => $kader->desa, 'nomor_rw' => $kader->nomor_rw,
                'status' => 'aktif',
                'last_login_at' => rand(0,1) ? now()->subHours(rand(1, 120)) : null,
            ]);
            $kader->update(['is_activated' => true]);
        }

        $this->command->info('   → 20 users dibuat (password: "password")');

        // ================================================
        // 4. KORWE (~30) & KORTE (~60)
        // ================================================
        $this->command->info('4. Seeding KORWE & KORTE...');
        $korweCount = 0;
        $korteCount = 0;

        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(5)->get();
            foreach ($rws as $rw) {
                // KORWE
                Korwe::firstOrCreate([
                    'target_wilayah_id' => $desa->id,
                    'nomor_rw' => $rw->nomor_rw,
                ], [
                    'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                    'nama_koordinator' => $this->randomNama('L'),
                    'no_hp' => $this->randomHP(),
                    'status' => 'terbentuk',
                ]);
                $korweCount++;

                // KORTE (2-4 per RW)
                $rtCount = rand(2, 4);
                for ($r = 1; $r <= $rtCount; $r++) {
                    $rt = str_pad($r, 3, '0', STR_PAD_LEFT);
                    Korte::firstOrCreate([
                        'target_wilayah_id' => $desa->id,
                        'nomor_rw' => $rw->nomor_rw,
                        'nomor_rt' => $rt,
                    ], [
                        'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                        'nama_koordinator' => $this->randomNama(),
                        'no_hp' => $this->randomHP(),
                        'status' => 'terbentuk',
                        'is_saksi_tps' => rand(0, 1),
                        'assigned_tps' => rand(0,1) ? 'TPS ' . str_pad(rand(1, 40), 3, '0', STR_PAD_LEFT) : null,
                        'status_saksi' => ['belum','siap','terkonfirmasi'][rand(0,2)],
                    ]);
                    $korteCount++;
                }
            }
        }
        $this->command->info("   → {$korweCount} KORWE, {$korteCount} KORTE");

        // ================================================
        // 5. PENGGALANG SUARA (~40) & UPA RW (~20)
        // ================================================
        $this->command->info('5. Seeding penggalang & UPA...');
        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(4)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                // 2-3 penggalang per RW
                for ($p = 0; $p < rand(2, 3); $p++) {
                    PenggalangSuara::create([
                        'target_wilayah_id' => $desa->id,
                        'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                        'nomor_rw' => $rw,
                        'nama' => $this->randomNama(),
                        'no_hp' => $this->randomHP(),
                        'sumber' => ['korwe','korte','warga','event'][rand(0,3)],
                        'target_jangkauan' => 10,
                        'realisasi_jangkauan' => rand(0, 10),
                        'status' => 'aktif',
                    ]);
                }
                // UPA: 2-3 members di beberapa RW
                if (rand(0, 1)) {
                    $jabatans = ['pembina','ketua','sekretaris','anggota'];
                    foreach (array_slice($jabatans, 0, rand(2, 4)) as $jab) {
                        UpaRwMember::create([
                            'target_wilayah_id' => $desa->id,
                            'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                            'nomor_rw' => $rw,
                            'nama' => $this->randomNama(),
                            'no_hp' => $this->randomHP(),
                            'jabatan' => $jab,
                            'asal' => ['korwe','korte','kader_baru'][rand(0,2)],
                        ]);
                    }
                }
            }
        }

        // ================================================
        // 6. PROFIL RW (~25)
        // ================================================
        $this->command->info('6. Seeding profil RW...');
        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(5)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                ProfilRw::firstOrCreate(['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw], [
                    'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                    'ketua_rw_nama' => $this->randomNama('L'),
                    'ketua_rw_hp' => $this->randomHP(),
                    'jumlah_kta' => rand(5, 30),
                    'upa_rw_status' => ['sudah','belum'][rand(0,1)],
                    'rki_status' => ['sudah','belum','proses'][rand(0,2)],
                    'senam_status' => ['sudah','belum'][rand(0,1)],
                    'completion_percent' => rand(20, 100),
                ]);
            }
        }
        foreach ($desaSedang as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(2)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                ProfilRw::firstOrCreate(['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw], [
                    'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                    'ketua_rw_nama' => $this->randomNama('L'),
                    'completion_percent' => rand(5, 40),
                ]);
            }
        }

        // ================================================
        // 7. SISIR RW (~15 kegiatan)
        // ================================================
        $this->command->info('7. Seeding sisir RW...');
        $jenisKegiatan = ['door_to_door','pengajian','baksos','silaturahmi','kunjungan_tokoh'];
        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(3)->pluck('nomor_rw');
            foreach ($rws as $rw) {
                for ($k = 0; $k < rand(1, 3); $k++) {
                    KegiatanRw::create([
                        'target_wilayah_id' => $desa->id,
                        'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                        'nomor_rw' => $rw,
                        'tanggal_kegiatan' => $this->randomDate('2026-01-01', '2026-06-01'),
                        'jenis_kegiatan' => $jenisKegiatan[array_rand($jenisKegiatan)],
                        'jumlah_warga' => rand(10, 60),
                        'tokoh_ditemui' => rand(0,1) ? $this->randomNama('L') . ' (Ketua RT)' : null,
                        'catatan' => 'Kegiatan berjalan lancar. Warga antusias.',
                    ]);
                }
            }
        }

        // ================================================
        // 8. SAPA WARGA (~500 kontak)
        // ================================================
        $this->command->info('8. Seeding sapa warga...');
        // 3 RW penuh (200+), beberapa partial
        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(4)->pluck('nomor_rw');
            foreach ($rws as $i => $rw) {
                $count = $i === 0 ? rand(180, 220) : rand(20, 60); // RW pertama penuh
                for ($c = 0; $c < $count; $c++) {
                    KontakWarga::create([
                        'target_wilayah_id' => $desa->id,
                        'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                        'nomor_rw' => $rw,
                        'nama' => $this->randomNama(),
                        'no_wa' => $this->randomHP(),
                        'rt' => str_pad(rand(1, 8), 3, '0', STR_PAD_LEFT),
                        'sumber' => ['bulk','manual','event','penggalang'][rand(0,3)],
                        'status' => 'aktif',
                        'created_at' => $this->randomDate('2026-01-01', '2026-06-01'),
                    ]);
                }
            }
        }

        // ================================================
        // 9. EVENTS (8)
        // ================================================
        $this->command->info('9. Seeding events...');
        $events = collect();

        $eventData = [
            ['nama' => 'Baksos Kesehatan Gratis', 'jenis' => 'baksos', 'status' => 'selesai', 'is_public' => true, 'tanggal' => '2026-05-20', 'lokasi' => 'Balai Desa ' . $desaAktif[0]->desa],
            ['nama' => 'Pengajian Akbar Bersama', 'jenis' => 'dakwah', 'status' => 'selesai', 'is_public' => true, 'tanggal' => '2026-05-28', 'lokasi' => 'Masjid Al-Ikhlas ' . $desaAktif[1]->desa],
            ['nama' => 'Workshop UMKM Digital', 'jenis' => 'pelatihan', 'status' => 'disetujui', 'is_public' => true, 'tanggal' => '2026-06-22', 'lokasi' => 'Aula Kecamatan ' . $desaAktif[0]->kecamatan],
            ['nama' => 'Jalan Santai Sehat Bersama', 'jenis' => 'olahraga', 'status' => 'disetujui', 'is_public' => true, 'tanggal' => '2026-06-29', 'lokasi' => 'Lapangan ' . $desaAktif[2]->desa],
            ['nama' => 'Seminar Parenting Islami', 'jenis' => 'edukasi', 'status' => 'disetujui', 'is_public' => true, 'tanggal' => '2026-07-05', 'lokasi' => 'Gedung Serbaguna'],
            ['nama' => 'Pelatihan Kader Angkatan 4', 'jenis' => 'pelatihan', 'status' => 'menunggu', 'is_public' => false, 'tanggal' => '2026-07-12', 'lokasi' => 'Sekretariat DPD'],
            ['nama' => 'Bazar Sembako Murah', 'jenis' => 'baksos', 'status' => 'menunggu', 'is_public' => true, 'tanggal' => '2026-07-20', 'lokasi' => $desaSedang[0]->desa],
            ['nama' => 'Musyawarah DPC Dapil 3', 'jenis' => 'musyawarah', 'status' => 'draft', 'is_public' => false, 'tanggal' => '2026-08-01', 'lokasi' => 'Sekretariat DPC'],
        ];

        foreach ($eventData as $ed) {
            $event = Event::create([
                'nama' => $ed['nama'],
                'slug' => Str::slug($ed['nama']),
                'jenis' => $ed['jenis'],
                'deskripsi' => 'Kegiatan ' . $ed['nama'] . ' yang diselenggarakan oleh DPD PKS Kabupaten Bekasi untuk warga.',
                'tanggal_mulai' => Carbon::parse($ed['tanggal'] . ' 08:00'),
                'tanggal_selesai' => Carbon::parse($ed['tanggal'] . ' 14:00'),
                'lokasi' => $ed['lokasi'],
                'status' => $ed['status'],
                'is_public' => $ed['is_public'],
                'target_wilayah_id' => $desaAktif[0]->id,
                'peserta_target' => rand(50, 200),
            ]);
            $events->push($event);
        }

        // Event peserta untuk yang selesai (event 0 dan 1)
        foreach ($events->where('status', 'selesai') as $event) {
            $pesertaCount = rand(40, 80);
            for ($p = 0; $p < $pesertaCount; $p++) {
                $rw = str_pad(rand(1, 15), 3, '0', STR_PAD_LEFT);
                EventPeserta::create([
                    'event_id' => $event->id,
                    'nama' => $this->randomNama(),
                    'no_hp' => $this->randomHP(),
                    'dapil' => $desaAktif[0]->dapil,
                    'kecamatan' => $desaAktif[0]->kecamatan,
                    'desa' => $desaAktif[0]->desa,
                    'nomor_rw' => $rw,
                    'target_wilayah_id' => $desaAktif[0]->id,
                    'metode' => ['bulk','manual','qr_scan'][rand(0,2)],
                    'synced_sapa_warga' => rand(0, 1),
                ]);
            }
            $event->update(['peserta_hadir' => $pesertaCount]);
        }

        // ================================================
        // 10. RKI (~10) & KSN (~8) + LOG SESI
        // ================================================
        $this->command->info('10. Seeding RKI & KSN...');
        $jenisRki = ['posyandu','kerajinan','paud','alquran','sembako','arisan'];
        foreach ($desaAktif as $desa) {
            $rws = DataRw::where('target_wilayah_id', $desa->id)->limit(3)->pluck('nomor_rw');
            foreach ($rws as $i => $rw) {
                $status = $i < 2 ? 'aktif' : 'pembentukan';
                $rki = TitikRki::create([
                    'target_wilayah_id' => $desa->id,
                    'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                    'nomor_rw' => $rw,
                    'nama_penggerak' => $this->randomNama('P'),
                    'no_hp_penggerak' => $this->randomHP(),
                    'lokasi' => ['Musholla Al-Ikhlas','Rumah Ibu RT','Balai RW','Pos Yandu'][rand(0,3)],
                    'hari_kegiatan' => ['senin','rabu','jumat','sabtu'][rand(0,3)],
                    'jam_kegiatan' => ['09:00','10:00','14:00'][rand(0,2)],
                    'jenis_kegiatan' => array_slice($jenisRki, 0, rand(2, 4)),
                    'avg_peserta' => rand(15, 35),
                    'status' => $status,
                    'tanggal_aktif' => $status === 'aktif' ? $this->randomDate('2025-06-01', '2026-03-01') : null,
                ]);
                // Log sesi untuk yang aktif
                if ($status === 'aktif') {
                    for ($s = 0; $s < rand(3, 6); $s++) {
                        LogSesi::create([
                            'loggable_type' => TitikRki::class,
                            'loggable_id' => $rki->id,
                            'tanggal_sesi' => $this->randomDate('2026-01-01', '2026-06-01'),
                            'jumlah_peserta' => rand(12, 40),
                            'pelaksana' => $rki->nama_penggerak,
                        ]);
                    }
                }
            }

            // KSN per desa (1 titik)
            $senam = TitikSenam::create([
                'target_wilayah_id' => $desa->id,
                'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                'nama_titik' => 'Lapangan ' . $desa->desa,
                'instruktur' => $this->randomNama('P'),
                'no_hp_instruktur' => $this->randomHP(),
                'hari_senam' => ['minggu','sabtu'][rand(0,1)],
                'jam_senam' => '06:30',
                'avg_peserta' => rand(25, 50),
                'status' => 'aktif',
                'tanggal_aktif' => $this->randomDate('2025-09-01', '2026-02-01'),
            ]);
            for ($s = 0; $s < rand(4, 8); $s++) {
                LogSesi::create([
                    'loggable_type' => TitikSenam::class,
                    'loggable_id' => $senam->id,
                    'tanggal_sesi' => $this->randomDate('2026-01-01', '2026-06-01'),
                    'jumlah_peserta' => rand(20, 55),
                    'pelaksana' => $senam->instruktur,
                ]);
            }
        }

        // ================================================
        // 11. PROGRAM KERJA (~21) & AGENDA (~12)
        // ================================================
        $this->command->info('11. Seeding program kerja & agenda...');
        $allBidangs = BidangDpd::all();
        $programStatus = ['berjalan','berjalan','selesai','belum_mulai','tertunda'];

        foreach ($allBidangs->take(7) as $bidang) {
            for ($p = 0; $p < 3; $p++) {
                $status = $programStatus[array_rand($programStatus)];
                $targetAngka = rand(5, 30);
                $realisasi = $status === 'selesai' ? $targetAngka : ($status === 'berjalan' ? rand(1, $targetAngka - 1) : 0);
                $pct = $targetAngka > 0 ? round($realisasi / $targetAngka * 100) : 0;

                ProgramKerja::create([
                    'bidang_dpd_id' => $bidang->id,
                    'nama_program' => 'Program ' . $bidang->nama . ' #' . ($p + 1),
                    'tahun' => '2026',
                    'target_angka' => $targetAngka,
                    'realisasi' => $realisasi,
                    'satuan' => ['kegiatan','peserta','kecamatan','desa'][rand(0,3)],
                    'target_teks' => $targetAngka . ' ' . ['kegiatan','peserta','kecamatan','desa'][rand(0,3)],
                    'periode' => ['Q1','Q2','Q3','Q4','sepanjang_tahun'][rand(0,4)],
                    'pic_nama' => $bidang->pic_nama,
                    'status' => $status,
                    'progress_pct' => $pct,
                ]);
            }
        }

        // Agenda
        $agendaData = [
            ['judul' => 'Rapat koordinasi DPD + DPC', 'jenis' => 'rapat', 'status' => 'selesai', 'tgl' => '2026-05-15'],
            ['judul' => 'Visitasi KORWE Dapil 1', 'jenis' => 'kunjungan', 'status' => 'selesai', 'tgl' => '2026-05-20'],
            ['judul' => 'Workshop sosmed DPC', 'jenis' => 'pelatihan', 'status' => 'selesai', 'tgl' => '2026-05-22'],
            ['judul' => 'Evaluasi bulanan DPD', 'jenis' => 'rapat', 'status' => 'selesai', 'tgl' => '2026-05-28'],
            ['judul' => 'Baksos kesehatan massal', 'jenis' => 'sosialisasi', 'status' => 'selesai', 'tgl' => '2026-05-30'],
            ['judul' => 'Rapat persiapan Musda', 'jenis' => 'rapat', 'status' => 'dijadwalkan', 'tgl' => '2026-06-08'],
            ['judul' => 'Pelatihan kader angkatan 4', 'jenis' => 'pelatihan', 'status' => 'dijadwalkan', 'tgl' => '2026-06-12'],
            ['judul' => 'Kunjungan lapangan Dapil 5', 'jenis' => 'kunjungan', 'status' => 'dijadwalkan', 'tgl' => '2026-06-15'],
            ['judul' => 'Musyawarah bidang Dakwah', 'jenis' => 'musyawarah', 'status' => 'dijadwalkan', 'tgl' => '2026-06-20'],
        ];
        foreach ($agendaData as $ag) {
            AgendaDpd::create([
                'bidang_dpd_id' => $allBidangs->random()->id,
                'judul' => $ag['judul'],
                'jenis' => $ag['jenis'],
                'tanggal_mulai' => Carbon::parse($ag['tgl'] . ' 09:00'),
                'lokasi' => 'Sekretariat DPD',
                'peserta_target' => rand(15, 40),
                'peserta_hadir' => $ag['status'] === 'selesai' ? rand(10, 35) : 0,
                'status' => $ag['status'],
            ]);
        }

        // ================================================
        // 12. ANGGOTA DEWAN (8) & KONTEN MEDSOS (~40)
        // ================================================
        $this->command->info('12. Seeding anggota dewan & konten medsos...');
        $dewanNames = [
            ['nama' => 'H. Ahmad Syaikhu', 'dapil' => 'BEKASI 1', 'ig' => 8200, 'tt' => 5400, 'yt' => 1200, 'skor' => 72],
            ['nama' => 'Hj. Siti Mukaromah', 'dapil' => 'BEKASI 2', 'ig' => 2100, 'tt' => 890, 'yt' => 0, 'skor' => 45],
            ['nama' => 'H. Bambang Irawan', 'dapil' => 'BEKASI 3', 'ig' => 4500, 'tt' => 0, 'yt' => 2800, 'skor' => 55],
            ['nama' => 'Ust. Firdaus', 'dapil' => 'BEKASI 4', 'ig' => 320, 'tt' => 0, 'yt' => 0, 'skor' => 18],
            ['nama' => 'Hj. Nurhasanah', 'dapil' => 'BEKASI 5', 'ig' => 1800, 'tt' => 1200, 'yt' => 500, 'skor' => 62],
            ['nama' => 'H. Deden Suryadi', 'dapil' => 'BEKASI 5', 'ig' => 950, 'tt' => 400, 'yt' => 0, 'skor' => 35],
            ['nama' => 'H. Endang Kosasih', 'dapil' => 'BEKASI 6', 'ig' => 3200, 'tt' => 2100, 'yt' => 800, 'skor' => 58],
            ['nama' => 'Hj. Ai Nurhayati', 'dapil' => 'BEKASI 7', 'ig' => 1500, 'tt' => 600, 'yt' => 200, 'skor' => 40],
        ];

        $platforms = ['instagram','tiktok','youtube'];
        $jenisKonten = ['reels','post','video','short','story'];
        $topikKonten = ['pelayanan','reses','aspirasi','edukasi','campaign'];

        foreach ($dewanNames as $dn) {
            $dewan = AnggotaDewan::create([
                'nama' => $dn['nama'], 'jabatan' => 'DPRD Kab. Bekasi', 'dapil' => $dn['dapil'],
                'no_hp' => $this->randomHP(),
                'instagram' => $dn['ig'] > 0 ? strtolower(str_replace([' ','.'], '', $dn['nama'])) : null,
                'ig_followers' => $dn['ig'],
                'tiktok' => $dn['tt'] > 0 ? strtolower(str_replace([' ','.'], '', $dn['nama'])) : null,
                'tt_followers' => $dn['tt'],
                'youtube' => $dn['yt'] > 0 ? $dn['nama'] : null,
                'yt_subscribers' => $dn['yt'],
                'skor_popularitas' => $dn['skor'],
                'target_popularitas' => 70,
                'status' => 'aktif',
            ]);

            // Konten: 3-8 per dewan
            $kontenCount = $dn['skor'] > 50 ? rand(5, 8) : rand(1, 3);
            for ($k = 0; $k < $kontenCount; $k++) {
                $platform = $platforms[array_rand($platforms)];
                KontenMedsos::create([
                    'anggota_dewan_id' => $dewan->id,
                    'platform' => $platform,
                    'jenis_konten' => $jenisKonten[array_rand($jenisKonten)],
                    'caption' => 'Kegiatan ' . $topikKonten[array_rand($topikKonten)] . ' di ' . $dn['dapil'],
                    'tanggal_posting' => $this->randomDate('2026-04-01', '2026-06-01'),
                    'likes' => rand(20, 500),
                    'comments' => rand(2, 50),
                    'shares' => rand(0, 30),
                    'views' => rand(200, 15000),
                    'topik' => $topikKonten[array_rand($topikKonten)],
                    'is_video_pelayanan' => rand(0, 1) && $k < 3,
                    'dapil_terkait' => $dn['dapil'],
                ]);
            }
        }

        // ================================================
        // 13. MATERI DIGITAL (5) + DISTRIBUSI
        // ================================================
        $this->command->info('13. Seeding materi digital...');
        $materiData = [
            ['judul' => 'Poster Baksos Kesehatan Juni 2026', 'jenis' => 'poster', 'status' => 'published'],
            ['judul' => 'Video Profil DPD PKS Kab. Bekasi', 'jenis' => 'video', 'status' => 'published'],
            ['judul' => 'Infografis Program RKI', 'jenis' => 'infografis', 'status' => 'published'],
            ['judul' => 'Flyer Senam PKS Mingguan', 'jenis' => 'flyer', 'status' => 'draft'],
            ['judul' => 'Poster Pendaftaran Anggota', 'jenis' => 'poster', 'status' => 'draft'],
        ];
        foreach ($materiData as $md) {
            $materi = MateriDigital::create([
                'judul' => $md['judul'], 'jenis' => $md['jenis'],
                'deskripsi' => 'Materi digital untuk distribusi ke jaringan.',
                'file_path' => 'materi/placeholder.jpg',
                'status' => $md['status'],
                'distribusi_count' => $md['status'] === 'published' ? rand(1, 5) : 0,
            ]);
            if ($md['status'] === 'published') {
                DistribusiMateri::create([
                    'materi_digital_id' => $materi->id,
                    'channel' => ['wa_blast','wa_grup_korwe','wa_grup_korte'][rand(0,2)],
                    'target_rw_count' => rand(50, 200),
                    'terkirim' => rand(30, 180),
                    'terbaca' => rand(10, 100),
                    'tanggal_distribusi' => $this->randomDate('2026-04-01', '2026-06-01'),
                ]);
            }
        }

        // ================================================
        // 14. MEMBERS (~50) + KEHADIRAN (~80)
        // ================================================
        $this->command->info('14. Seeding members & kehadiran...');
        $members = collect();
        for ($m = 0; $m < 50; $m++) {
            $sumber = ['website','website','event','event','event','affiliate'][rand(0,5)];
            $desa = $focusDesas->random();
            $rw = DataRw::where('target_wilayah_id', $desa->id)->inRandomOrder()->first();

            $member = Member::create([
                'nama' => $this->randomNama(),
                'no_hp' => $this->randomHP(),
                'no_wa' => $this->randomHP(),
                'dapil' => $desa->dapil, 'kecamatan' => $desa->kecamatan, 'desa' => $desa->desa,
                'nomor_rw' => $rw?->nomor_rw,
                'target_wilayah_id' => $desa->id,
                'sumber' => $sumber,
                'referred_by' => $sumber === 'affiliate' && $members->count() > 0 ? $members->random()->id : null,
                'tanggal_bergabung' => $this->randomDate('2026-01-01', '2026-06-01'),
                'status' => 'aktif',
            ]);
            $members->push($member);
        }

        // Update referral counts
        Member::all()->each(function ($m) {
            $m->update(['referral_count' => Member::where('referred_by', $m->id)->count()]);
        });

        // Kehadiran di event selesai
        $selesaiEvents = $events->where('status', 'selesai');
        foreach ($members->random(min(30, $members->count())) as $member) {
            $event = $selesaiEvents->random();
            Kehadiran::create([
                'member_id' => $member->id,
                'hadir_type' => Event::class,
                'hadir_id' => $event->id,
                'nama_kegiatan' => $event->nama,
                'waktu_scan' => Carbon::parse($event->tanggal_mulai)->addHours(rand(0, 3)),
                'metode' => ['qr','manual'][rand(0,1)],
            ]);
        }

        // ================================================
        // SUMMARY
        // ================================================
        $this->command->info('');
        $this->command->info('=== SEEDING SELESAI ===');
        $this->command->info('');
        $this->command->info('AKUN LOGIN (password: "password"):');
        $this->command->info('┌──────────────────────────────────┬──────────────────┐');
        $this->command->info('│ Admin DPD:                       │                  │');
        $this->command->info('│   admin1@bekasihebat.id          │ Semua menu       │');
        $this->command->info('│   admin2@bekasihebat.id          │ Semua menu       │');
        $this->command->info('│ Pengurus Bidang:                 │                  │');
        $this->command->info('│   relawan@bekasihebat.id         │ Infra, Sapa      │');
        $this->command->info('│   komdigi@bekasihebat.id         │ Web, Sosmed      │');
        $this->command->info('│   humas@bekasihebat.id           │ Sosmed, Event    │');
        $this->command->info('│   perempuan@bekasihebat.id       │ RKI              │');
        $this->command->info('│   dakwah@bekasihebat.id          │ Sisir, Event     │');
        $this->command->info('│   pemuda@bekasihebat.id          │ Kaderisasi       │');
        $this->command->info('│   (+ 7 bidang lain)@bekasihebat  │ Proker + Event   │');
        $this->command->info('│ Kader:                           │                  │');
        $this->command->info('│   kader1-5@bekasihebat.id        │ Sapa Warga only  │');
        $this->command->info('└──────────────────────────────────┴──────────────────┘');
        $this->command->info('');
    }
}
```

Register di DatabaseSeeder.php:
```php
public function run()
{
    $this->call(DummyDataSeeder::class);
}
```

Jalankan: php artisan db:seed --class=DummyDataSeeder
Jangan migrate:fresh (data import target_wilayahs harus tetap ada).
Jangan buat test.
```
