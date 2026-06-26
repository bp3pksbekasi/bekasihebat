<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AgendaDpd;
use App\Models\AnggotaDewan;
use App\Models\AuditLog;
use App\Models\BidangDpd;
use App\Models\DataRw;
use App\Models\DistribusiMateri;
use App\Models\Event;
use App\Models\EventPeserta;
use App\Models\EventRegistration;
use App\Models\Kader;
use App\Models\KegiatanRw;
use App\Models\KontakWarga;
use App\Models\Korte;
use App\Models\Korwe;
use App\Models\LogSesi;
use App\Models\MateriDigital;
use App\Models\PenggalangSuara;
use App\Models\ProfilRw;
use App\Models\ProgramKerja;
use App\Models\TargetWilayah;
use App\Models\TitikRki;
use App\Models\TitikSenam;
use App\Models\UpaRwMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    private const WINDOW_START = '2025-12-01 08:00:00';

    private const WINDOW_END = '2026-06-30 18:00:00';

    /** @var array<int, string> */
    private array $namaLaki = [
        'Ahmad Fauzi', 'Dedi Kurniawan', 'Ihsan Maulana', 'Udin Saepudin', 'Rohman Hakim',
        'Asep Suryadi', 'Ade Firmansyah', 'Cecep Hidayat', 'Nandang Hermawan', 'Yusuf Abdillah',
        'Hasan Basri', 'Ujang Suryana', 'Tatang Suherman', 'Ending Sudrajat', 'Koswara',
        'Dadan Ramdani', 'Agus Salim', 'Maman Sulaeman', 'Jajang Nurjaman', 'Eman Sulaeman',
        'Iwan Setiawan', 'Bambang Irawan', 'Hendra Gunawan', 'Ridwan Kamil', 'Fikri Abdurrahman',
        'Zaenal Arifin', 'Mulyadi', 'Wahyu Hidayat', 'Rudi Hartono', 'Slamet Riyadi',
        'Fajar Nugroho', 'Andi Wijaya', 'Budi Santoso', 'Deni Ramdan', 'Edi Supriadi',
        'Gani Mulyadi', 'Hamdan', 'Ilham Saputra', 'Joko Prasetyo', 'Kurnia Sandi',
    ];

    /** @var array<int, string> */
    private array $namaPerempuan = [
        'Siti Nurhaliza', 'Imas Komariah', 'Tiwi Rahayu', 'Dewi Sartika', 'Euis Susilawati',
        'Neng Rina', 'Yanti Suryani', 'Ai Nurhasanah', 'Siti Aisyah', 'Nining Herlina',
        'Iis Dahlia', 'Entin Supriatin', 'Cucu Rahayu', 'Imas Masitoh', 'Eneng Siti',
        'Wati Sukaesih', 'Marlina', 'Nurhayati', 'Sumiati', 'Tuti Alawiyah',
        'Sri Mulyani', 'Ratna Dewi', 'Fitri Handayani', 'Lina Marlina', 'Kartini',
        'Rini Soemarno', 'Wulan Sari', 'Mey Sulastri',
    ];

    private int $phoneCounter = 81000000;

    private int $niaCounter = 900000000;

    public function run(): void
    {
        mt_srand(20260605);

        $this->call([
            RolesAndPermissionsSeeder::class,
            PoliticalPartiesSeeder::class,
        ]);

        $this->command?->info('=== Mulai seeding data dummy lengkap ===');

        $focusDesas = TargetWilayah::query()
            ->withCount('dataRws')
            ->whereHas('dataRws')
            ->orderByDesc('data_rws_count')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->limit(8)
            ->get();

        if ($focusDesas->count() < 8) {
            $this->command?->error('Minimal 8 desa dengan data RW harus tersedia.');

            return;
        }

        $desaAktif = $focusDesas->take(3)->values();
        $desaSedang = $focusDesas->slice(3, 3)->values();
        $desaBaru = $focusDesas->slice(6, 2)->values();

        $this->command?->info('Fokus desa: '.$focusDesas->pluck('desa')->implode(', '));

        $bidangs = $this->seedBidangs();
        $kaders = $this->seedKaders($desaAktif, $desaSedang, $desaBaru);
        [$admins, $bidangUsers, $kaderUsers] = $this->seedUsers($kaders, $bidangs);
        $creatorId = (int) ($admins->first()?->id ?? User::query()->value('id') ?? 1);

        $this->seedKorweKorte($desaAktif, $creatorId);
        $this->seedPenggalangUpa($desaAktif, $creatorId);
        $this->seedProfilRw($desaAktif, $desaSedang, $creatorId);
        $this->seedSisirRw($desaAktif, $creatorId);
        $contacts = $this->seedSapaWarga($desaAktif, $creatorId);
        $events = $this->seedEvents($desaAktif, $desaSedang, $creatorId);
        $this->syncPublicEventCovers();
        $memberUsers = $this->seedCommunityMembers($focusDesas);
        $this->seedEventRegistrationsAndParticipants($events, $memberUsers, $contacts, $creatorId);
        $this->seedRkiKsn($desaAktif, $creatorId);
        $this->seedProgramKerjaAgenda($bidangs, $creatorId);
        $this->seedSosmed();
        $this->seedMateriDigital($focusDesas, $creatorId);
        $this->seedAuditLogs($admins, $bidangUsers, $kaderUsers, $memberUsers);

        $this->command?->info('');
        $this->command?->info('=== SEEDING DUMMY SELESAI ===');
        $this->command?->info('Login utama:');
        $this->command?->info('- admin1@bekasihebat.id / password');
        $this->command?->info('- admin2@bekasihebat.id / password');
        $this->command?->info('- perempuan@bekasihebat.id / password');
        $this->command?->info('- dikkes@bekasihebat.id / password');
        $this->command?->info('- kader1@bekasihebat.id s/d kader5@bekasihebat.id / password');
    }

    private function seedBidangs(): Collection
    {
        $this->command?->info('1. Seeding bidang DPD...');

        $rows = [
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

        return collect($rows)->map(function (array $row, int $index) {
            return BidangDpd::query()->updateOrCreate(
                ['slug' => $row['slug']],
                $row + [
                    'pic_nama' => $this->randomNama($index % 2 === 0 ? 'L' : 'P'),
                    'pic_hp' => $this->nextPhone(),
                    'urutan' => $index + 1,
                ]
            );
        });
    }

    private function seedKaders(Collection $desaAktif, Collection $desaSedang, Collection $desaBaru): Collection
    {
        $this->command?->info('2. Seeding kader...');

        $kaders = collect();
        $jenjangPool = ['penggerak', 'penggerak', 'pendukung', 'pendukung', 'pelopor', 'pelopor', 'madya', 'dewasa', 'purna'];

        foreach ($desaAktif as $desa) {
            $rws = $this->rwRows((string) $desa->id, 6);
            foreach ($rws as $rwIndex => $rw) {
                $jumlah = 4 + ($rwIndex % 3);
                for ($i = 0; $i < $jumlah; $i++) {
                    $kaders->push($this->upsertKader(
                        village: $desa,
                        nomorRw: (string) $rw->nomor_rw,
                        nomorRt: str_pad((string) (($i % max((int) ($rw->jumlah_rt ?? 4), 1)) + 1), 3, '0', STR_PAD_LEFT),
                        jenjang: $jenjangPool[array_rand($jenjangPool)],
                        flags: [
                            'is_korwe' => $i === 0,
                            'is_korte' => $i === 1,
                            'is_upa' => $i === 2,
                            'jabatan_upa' => $i === 2 ? 'anggota' : null,
                            'is_penggalang' => $i === 3,
                            'is_saksi' => $i === 1 && $rwIndex % 2 === 0,
                            'bisa_deploy' => $i % 2 === 0,
                        ]
                    ));
                }
            }
        }

        foreach ($desaSedang as $desa) {
            foreach ($this->rwRows((string) $desa->id, 3) as $rw) {
                for ($i = 0; $i < 3; $i++) {
                    $kaders->push($this->upsertKader(
                        village: $desa,
                        nomorRw: (string) $rw->nomor_rw,
                        nomorRt: str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                        jenjang: ['penggerak', 'pendukung', 'pelopor'][array_rand(['penggerak', 'pendukung', 'pelopor'])],
                        flags: ['bisa_deploy' => $i !== 2]
                    ));
                }
            }
        }

        foreach ($desaBaru as $desa) {
            foreach ($this->rwRows((string) $desa->id, 2) as $rw) {
                for ($i = 0; $i < 2; $i++) {
                    $kaders->push($this->upsertKader(
                        village: $desa,
                        nomorRw: (string) $rw->nomor_rw,
                        nomorRt: str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                        jenjang: 'penggerak',
                        flags: ['bisa_deploy' => false]
                    ));
                }
            }
        }

        $this->command?->info('   -> '.$kaders->count().' kader dummy siap');

        return $kaders;
    }

    private function seedUsers(Collection $kaders, Collection $bidangs): array
    {
        $this->command?->info('3. Seeding users...');

        $pool = $kaders->values();
        $admins = collect();
        $bidangUsers = collect();
        $kaderUsers = collect();

        for ($i = 1; $i <= 2; $i++) {
            /** @var Kader $kader */
            $kader = $pool->shift();
            $user = User::query()->updateOrCreate(
                ['email' => "admin{$i}@bekasihebat.id"],
                [
                    'name' => $kader->nama,
                    'password' => Hash::make('password'),
                    'phone' => $kader->no_wa ?: $kader->no_hp,
                    'nia' => $kader->nia,
                    'kader_id' => $kader->id,
                    'role' => User::ROLE_ADMIN,
                    'dapil' => $kader->dapil,
                    'kecamatan' => $kader->kecamatan,
                    'desa' => $kader->desa,
                    'nomor_rw' => $kader->nomor_rw,
                    'status' => 'aktif',
                    'last_login_at' => $this->randomDateTime()->subHours($i),
                ]
            );
            $user->syncRoles(['admin_dpd']);
            $user->givePermissionTo(PermissionNames::all());
            $kader->update(['is_activated' => true]);
            $admins->push($user);
        }

        foreach ($bidangs as $bidang) {
            /** @var Kader $kader */
            $kader = $pool->shift();
            $user = User::query()->updateOrCreate(
                ['email' => $bidang->slug.'@bekasihebat.id'],
                [
                    'name' => $kader->nama,
                    'password' => Hash::make('password'),
                    'phone' => $kader->no_wa ?: $kader->no_hp,
                    'nia' => $kader->nia,
                    'kader_id' => $kader->id,
                    'role' => User::ROLE_BIDANG,
                    'bidang_slug' => $bidang->slug,
                    'dapil' => $kader->dapil,
                    'kecamatan' => $kader->kecamatan,
                    'desa' => $kader->desa,
                    'nomor_rw' => $kader->nomor_rw,
                    'status' => 'aktif',
                    'last_login_at' => mt_rand(0, 1) === 1 ? $this->randomDateTime() : null,
                ]
            );
            $user->syncRoles(['pengurus_bidang']);
            if ($bidang->slug === 'perempuan') {
                $user->givePermissionTo(User::menuPermissionName('rki'));
            }
            if ($bidang->slug === 'dikkes') {
                $user->givePermissionTo(User::menuPermissionName('ksn'));
            }
            $kader->update(['is_activated' => true, 'bidang_slug' => $bidang->slug]);
            $bidangUsers->push($user);
        }

        for ($i = 1; $i <= 5; $i++) {
            /** @var Kader $kader */
            $kader = $pool->shift();
            $user = User::query()->updateOrCreate(
                ['email' => "kader{$i}@bekasihebat.id"],
                [
                    'name' => $kader->nama,
                    'password' => Hash::make('password'),
                    'phone' => $kader->no_wa ?: $kader->no_hp,
                    'nia' => $kader->nia,
                    'kader_id' => $kader->id,
                    'role' => User::ROLE_KADER,
                    'dapil' => $kader->dapil,
                    'kecamatan' => $kader->kecamatan,
                    'desa' => $kader->desa,
                    'nomor_rw' => $kader->nomor_rw,
                    'status' => 'aktif',
                    'last_login_at' => mt_rand(0, 1) === 1 ? $this->randomDateTime() : null,
                ]
            );
            $user->syncRoles(['kader']);
            $kader->update(['is_activated' => true]);
            $kaderUsers->push($user);
        }

        return [$admins, $bidangUsers, $kaderUsers];
    }

    private function seedKorweKorte(Collection $desaAktif, int $creatorId): void
    {
        $this->command?->info('4. Seeding KORWE & KORTE...');

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 5) as $rwIndex => $rw) {
                $korwe = Korwe::query()->updateOrCreate(
                    ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw],
                    [
                        'nama_koordinator' => $this->randomNama('L'),
                        'no_hp' => $this->nextPhone(),
                        'status' => 'terbentuk',
                        'catatan' => 'Seeder dummy wilayah',
                        'tanggal_terbentuk' => $this->randomDate('2025-12-01', '2026-03-31'),
                        'created_by' => $creatorId,
                    ]
                );
                $this->stamp($korwe, $this->randomDateTime());

                $rtCount = min(max((int) ($rw->jumlah_rt ?? 4), 3), 6);
                for ($rt = 1; $rt <= $rtCount; $rt++) {
                    $korte = Korte::query()->updateOrCreate(
                        [
                            'target_wilayah_id' => $desa->id,
                            'nomor_rw' => $rw->nomor_rw,
                            'nomor_rt' => str_pad((string) $rt, 3, '0', STR_PAD_LEFT),
                        ],
                        [
                            'nama_koordinator' => $this->randomNama($rt % 2 === 0 ? 'P' : null),
                            'no_hp' => $this->nextPhone(),
                            'status' => 'terbentuk',
                            'is_saksi_tps' => $rt <= 2,
                            'assigned_tps' => $rt <= 2 ? 'TPS '.str_pad((string) (10 + $rwIndex + $rt), 3, '0', STR_PAD_LEFT) : null,
                            'status_saksi' => $rt <= 2 ? 'terkonfirmasi' : 'belum',
                            'catatan' => 'Seeder dummy KORTE',
                            'tanggal_terbentuk' => $this->randomDate('2025-12-01', '2026-04-30'),
                            'created_by' => $creatorId,
                        ]
                    );
                    $this->stamp($korte, $this->randomDateTime());
                }
            }
        }
    }

    private function seedPenggalangUpa(Collection $desaAktif, int $creatorId): void
    {
        $this->command?->info('5. Seeding penggalang suara & UPA...');

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 4) as $rw) {
                for ($i = 1; $i <= 3; $i++) {
                    $phone = $this->nextPhone();
                    $penggalang = PenggalangSuara::query()->updateOrCreate(
                        ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw, 'no_hp' => $phone],
                        [
                            'dapil' => $desa->dapil,
                            'kecamatan' => $desa->kecamatan,
                            'desa' => $desa->desa,
                            'nama' => $this->randomNama(),
                            'no_wa' => $phone,
                            'rt' => str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                            'sumber' => ['korwe', 'korte', 'upa', 'warga', 'event'][($i - 1) % 5],
                            'target_jangkauan' => 12 + $i,
                            'realisasi_jangkauan' => 4 + $i + mt_rand(0, 5),
                            'status' => 'aktif',
                            'catatan' => 'Seeder dummy penggalang',
                            'created_by' => $creatorId,
                        ]
                    );
                    $this->stamp($penggalang, $this->randomDateTime());
                }

                $korwe = Korwe::query()->where('target_wilayah_id', $desa->id)->where('nomor_rw', $rw->nomor_rw)->first();
                $korte = Korte::query()->where('target_wilayah_id', $desa->id)->where('nomor_rw', $rw->nomor_rw)->first();
                foreach (['pembina', 'ketua', 'sekretaris', 'anggota'] as $jabatan) {
                    $upa = UpaRwMember::query()->updateOrCreate(
                        ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw, 'jabatan' => $jabatan],
                        [
                            'dapil' => $desa->dapil,
                            'kecamatan' => $desa->kecamatan,
                            'desa' => $desa->desa,
                            'nama' => $this->randomNama($jabatan === 'pembina' ? 'L' : null),
                            'no_hp' => $this->nextPhone(),
                            'asal' => ['korwe', 'korte', 'kader_baru'][mt_rand(0, 2)],
                            'korwe_id' => $korwe?->id,
                            'korte_id' => $korte?->id,
                            'status' => $jabatan === 'anggota' ? 'aktif' : 'terbentuk',
                            'catatan' => 'Seeder dummy UPA',
                            'created_by' => $creatorId,
                        ]
                    );
                    $this->stamp($upa, $this->randomDateTime());
                }
            }
        }
    }

    private function seedProfilRw(Collection $desaAktif, Collection $desaSedang, int $creatorId): void
    {
        $this->command?->info('6. Seeding profil RW...');

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 5) as $rw) {
                $profil = ProfilRw::query()->updateOrCreate(
                    ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw],
                    [
                        'dapil' => $desa->dapil,
                        'kecamatan' => $desa->kecamatan,
                        'desa' => $desa->desa,
                        'tipologi' => ['perkampungan', 'campuran', 'perumahan'][mt_rand(0, 2)],
                        'ekonomi_dominan' => ['pabrik', 'pedagang', 'informal', 'campuran'][mt_rand(0, 3)],
                        'profil_warga' => 'Mayoritas pekerja industri dan keluarga muda, dengan aktivitas pengajian serta komunitas ibu-ibu cukup aktif.',
                        'suara_pks_2019' => mt_rand(25, 90),
                        'faktor_penyebab' => 'Basis tokoh lokal cukup berpengaruh, tetapi masih perlu penguatan jaringan RT dan komunitas keluarga.',
                        'anggota_pks' => mt_rand(6, 20),
                        'jumlah_kta' => mt_rand(10, 36),
                        'upa_rw_status' => ['sudah', 'proses'][mt_rand(0, 1)],
                        'upa_rw_nama' => 'UPA RW '.(string) $rw->nomor_rw,
                        'rki_status' => ['sudah', 'proses'][mt_rand(0, 1)],
                        'rki_nama' => 'RKI RW '.(string) $rw->nomor_rw,
                        'senam_status' => ['sudah', 'belum'][mt_rand(0, 1)],
                        'senam_nama' => 'Senam RW '.(string) $rw->nomor_rw,
                        'relawan_milenial_status' => ['sudah', 'belum'][mt_rand(0, 1)],
                        'relawan_milenial_nama' => mt_rand(0, 1) ? 'Relawan Muda '.(string) $rw->nomor_rw : null,
                        'caleg_terpilih_ada' => mt_rand(0, 1) === 1,
                        'caleg_terpilih_nama' => mt_rand(0, 1) ? 'Ahmad Syaikhu' : null,
                        'afiliasi_rw_rt' => 'Ketua RW dan sebagian RT bersikap terbuka selama pendekatan dilakukan konsisten.',
                        'afiliasi_posyandu_dkm' => 'DKM, posyandu, dan majelis taklim menjadi simpul pengaruh yang efektif.',
                        'kompetitor_status' => ['ada', 'terbatas', 'rendah'][mt_rand(0, 2)],
                        'kompetitor_detail' => 'Kompetitor utama bergerak di jaringan tokoh lokal dan komunitas olahraga.',
                        'tim_sukses_status' => ['ada', 'terbatas'][mt_rand(0, 1)],
                        'tim_sukses_detail' => 'Perlu penjagaan setelah pemetaan tokoh dan penggalang selesai.',
                        'strategi' => 'Perbanyak kunjungan keluarga, aktifkan RKI/KSN, dan pastikan kontak warga mencapai target per RW.',
                        'penanggung_jawab' => $this->randomNama(),
                        'keterangan_lain' => 'Data dummy realistis untuk pengujian profil RW.',
                        'is_complete' => true,
                        'completion_percent' => mt_rand(72, 100),
                        'filled_by' => $creatorId,
                        'filled_at' => $this->randomDateTime(),
                    ]
                );
                $this->stamp($profil, $this->randomDateTime());
            }
        }

        foreach ($desaSedang as $desa) {
            foreach ($this->rwRows((string) $desa->id, 2) as $rw) {
                $profil = ProfilRw::query()->updateOrCreate(
                    ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw],
                    [
                        'dapil' => $desa->dapil,
                        'kecamatan' => $desa->kecamatan,
                        'desa' => $desa->desa,
                        'tipologi' => 'campuran',
                        'ekonomi_dominan' => 'pabrik',
                        'profil_warga' => 'Masih tahap pemetaan profil warga dan simpul komunitas.',
                        'jumlah_kta' => mt_rand(3, 10),
                        'upa_rw_status' => 'belum',
                        'rki_status' => 'proses',
                        'senam_status' => 'belum',
                        'strategi' => 'Lengkapi data tokoh, RT aktif, dan potensi relawan lokal.',
                        'penanggung_jawab' => $this->randomNama(),
                        'is_complete' => false,
                        'completion_percent' => mt_rand(20, 45),
                        'filled_by' => $creatorId,
                        'filled_at' => $this->randomDateTime(),
                    ]
                );
                $this->stamp($profil, $this->randomDateTime());
            }
        }
    }

    private function seedSisirRw(Collection $desaAktif, int $creatorId): void
    {
        $this->command?->info('7. Seeding Sisir RW...');

        $jenis = ['door_to_door', 'pengajian', 'baksos', 'silaturahmi', 'diskusi', 'kesehatan'];

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 3) as $rw) {
                for ($i = 1; $i <= 2 + mt_rand(0, 1); $i++) {
                    $date = $this->randomDate('2026-01-01', '2026-06-20');
                    $kegiatan = KegiatanRw::query()->updateOrCreate(
                        [
                            'target_wilayah_id' => $desa->id,
                            'nomor_rw' => $rw->nomor_rw,
                            'jenis_kegiatan' => $jenis[($i + mt_rand(0, 3)) % count($jenis)],
                            'tanggal_kegiatan' => $date,
                        ],
                        [
                            'dapil' => $desa->dapil,
                            'kecamatan' => $desa->kecamatan,
                            'desa' => $desa->desa,
                            'pelaksana' => $this->randomNama(),
                            'jumlah_warga' => mt_rand(12, 65),
                            'catatan' => 'Kegiatan berjalan lancar, respons warga baik, dan tindak lanjut dijadwalkan.',
                            'tampil_galeri' => mt_rand(0, 1) === 1,
                            'tokoh_ditemui' => mt_rand(0, 1) === 1 ? 'Ketua RT '.str_pad((string) mt_rand(1, 8), 3, '0', STR_PAD_LEFT) : null,
                            'tindak_lanjut' => 'Lanjutkan penjaringan kontak keluarga dan penguatan komunitas RW.',
                            'jadwal_berikutnya' => (clone $date)->addWeeks(2),
                            'created_by' => $creatorId,
                        ]
                    );
                    $this->stamp($kegiatan, $date);
                }
            }
        }
    }

    private function seedSapaWarga(Collection $desaAktif, int $creatorId): Collection
    {
        $this->command?->info('8. Seeding Sapa Warga...');

        $contacts = collect();

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 4) as $index => $rw) {
                $count = $index === 0 ? mt_rand(180, 220) : mt_rand(24, 58);

                for ($i = 1; $i <= $count; $i++) {
                    $phone = $this->nextPhone();
                    $contact = KontakWarga::query()->updateOrCreate(
                        ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw, 'no_wa' => $phone],
                        [
                            'dapil' => $desa->dapil,
                            'kecamatan' => $desa->kecamatan,
                            'desa' => $desa->desa,
                            'nama' => $this->randomNama(),
                            'no_hp' => $phone,
                            'rt' => str_pad((string) (($i % max((int) ($rw->jumlah_rt ?? 4), 1)) + 1), 3, '0', STR_PAD_LEFT),
                            'alamat' => 'RT '.str_pad((string) (($i % 8) + 1), 3, '0', STR_PAD_LEFT).' / RW '.$rw->nomor_rw,
                            'sumber' => ['bulk', 'manual', 'event', 'penggalang'][mt_rand(0, 3)],
                            'status' => 'aktif',
                            'created_by' => $creatorId,
                        ]
                    );
                    $this->stamp($contact, $this->randomDateTime());
                    $contacts->push($contact);
                }
            }
        }

        return $contacts;
    }

    private function seedEvents(Collection $desaAktif, Collection $desaSedang, int $creatorId): Collection
    {
        $this->command?->info('9. Seeding Events...');

        $rows = [
            ['judul' => 'Baksos Kesehatan Gratis', 'jenis' => 'kesehatan', 'status' => Event::STATUS_SELESAI, 'public' => true, 'tanggal' => '2026-05-20 08:00:00', 'village' => $desaAktif[0]],
            ['judul' => 'Pengajian Akbar Bersama', 'jenis' => 'pengajian', 'status' => Event::STATUS_SELESAI, 'public' => true, 'tanggal' => '2026-05-28 08:00:00', 'village' => $desaAktif[1]],
            ['judul' => 'Workshop UMKM Digital', 'jenis' => 'pelatihan', 'status' => Event::STATUS_DISETUJUI, 'public' => true, 'tanggal' => '2026-06-22 08:00:00', 'village' => $desaAktif[0]],
            ['judul' => 'Jalan Santai Sehat Bersama', 'jenis' => 'senam', 'status' => Event::STATUS_DISETUJUI, 'public' => true, 'tanggal' => '2026-06-29 06:30:00', 'village' => $desaAktif[2]],
            ['judul' => 'Seminar Parenting Islami', 'jenis' => 'pendidikan', 'status' => Event::STATUS_DISETUJUI, 'public' => true, 'tanggal' => '2026-06-26 09:00:00', 'village' => $desaSedang[0]],
            ['judul' => 'Pelatihan Kader Angkatan 4', 'jenis' => 'pelatihan', 'status' => Event::STATUS_MENUNGGU, 'public' => false, 'tanggal' => '2026-06-18 08:00:00', 'village' => $desaSedang[1]],
            ['judul' => 'Bazar Sembako Murah', 'jenis' => 'baksos', 'status' => Event::STATUS_MENUNGGU, 'public' => true, 'tanggal' => '2026-06-30 08:00:00', 'village' => $desaSedang[2]],
            ['judul' => 'Musyawarah DPC Dapil 3', 'jenis' => 'musyawarah', 'status' => Event::STATUS_DRAFT, 'public' => false, 'tanggal' => '2026-06-24 19:30:00', 'village' => $desaAktif[1]],
        ];

        return collect($rows)->map(function (array $row, int $index) use ($creatorId) {
            /** @var TargetWilayah $village */
            $village = $row['village'];
            $startsAt = Carbon::parse($row['tanggal']);
            $event = Event::query()->updateOrCreate(
                ['slug' => Str::slug($row['judul'])],
                [
                    'uuid' => Event::query()->where('slug', Str::slug($row['judul']))->value('uuid') ?: (string) Str::uuid(),
                    'judul' => $row['judul'],
                    'deskripsi' => 'Kegiatan '.$row['judul'].' untuk warga dan jaringan wilayah DPD PKS Kabupaten Bekasi.',
                    'jenis' => $row['jenis'],
                    'tanggal_mulai' => $startsAt,
                    'tanggal_selesai' => (clone $startsAt)->addHours($row['jenis'] === 'senam' ? 2 : 6),
                    'lokasi' => 'Balai / titik kegiatan '.$village->desa,
                    'lokasi_desa' => $village->desa,
                    'lokasi_kecamatan' => $village->kecamatan,
                    'lokasi_dapil' => $village->dapil,
                    'kapasitas' => 60 + ($index * 15),
                    'is_public' => $row['public'],
                    'cover_image' => $this->eventCoverForJenis((string) $row['jenis']),
                    'status' => $row['status'],
                    'level_approval' => in_array($row['status'], [Event::STATUS_DISETUJUI, Event::STATUS_SELESAI], true) ? 'selesai' : 'dpd',
                    'penyelenggara' => 'DPD PKS Kabupaten Bekasi',
                    'pic_nama' => $this->randomNama(),
                    'pic_hp' => $this->nextPhone(),
                    'created_by' => $creatorId,
                ]
            );
            $this->stamp($event, $startsAt->copy()->subWeeks(2));

            return $event;
        });
    }

    private function syncPublicEventCovers(): void
    {
        Event::query()
            ->get()
            ->each(function (Event $event): void {
                $event->forceFill([
                    'cover_image' => $this->eventCoverForJenis((string) $event->jenis),
                ])->saveQuietly();
            });
    }

    private function seedCommunityMembers(Collection $focusDesas): Collection
    {
        $this->command?->info('10. Seeding member publik berbasis users...');

        $members = collect();

        for ($i = 1; $i <= 50; $i++) {
            /** @var TargetWilayah $desa */
            $desa = $focusDesas[($i - 1) % $focusDesas->count()];
            $rw = DataRw::query()->where('target_wilayah_id', $desa->id)->orderBy('nomor_rw')->skip(($i - 1) % 4)->first();

            $user = User::query()->updateOrCreate(
                ['email' => sprintf('member%02d@bekasihebat.id', $i)],
                [
                    'name' => $this->randomNama($i % 3 === 0 ? 'P' : null),
                    'password' => Hash::make('password'),
                    'phone' => $this->nextPhone(),
                    'role' => 'community_member',
                    'status' => 'aktif',
                    'dapil' => $desa->dapil,
                    'kecamatan' => $desa->kecamatan,
                    'desa' => $desa->desa,
                    'nomor_rw' => $rw?->nomor_rw,
                    'member_number' => sprintf('BKH-%06d', 900000 + $i),
                    'affiliate_code' => sprintf('AFF%04d', $i),
                    'address' => 'RT 00'.(($i % 8) + 1).' / RW '.($rw?->nomor_rw ?? '001').', '.$desa->desa,
                    'profile_completed_at' => $this->randomDateTime(),
                ]
            );
            $user->syncRoles(['community_member']);
            $members->push($user);
        }

        return $members;
    }

    private function seedEventRegistrationsAndParticipants(Collection $events, Collection $memberUsers, Collection $contacts, int $creatorId): void
    {
        $this->command?->info('11. Seeding registrasi event & peserta event...');

        $contactIndex = 0;

        foreach ($events as $event) {
            $sampleMembers = $memberUsers->shuffle()->take($event->status === Event::STATUS_SELESAI ? 24 : 16);

            foreach ($sampleMembers as $index => $member) {
                $registration = EventRegistration::query()->updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => $member->id],
                    [
                        'uuid' => EventRegistration::query()
                            ->where('event_id', $event->id)
                            ->where('user_id', $member->id)
                            ->value('uuid') ?: (string) Str::uuid(),
                        'status' => 'registered',
                        'affiliate_user_id' => $index % 5 === 0 ? $sampleMembers->first()?->id : null,
                        'attended_at' => $event->status === Event::STATUS_SELESAI ? Carbon::parse($event->tanggal_mulai)->addMinutes(15 * ($index + 1)) : null,
                    ]
                );
                $this->stamp($registration, Carbon::parse($event->tanggal_mulai)->copy()->subDays(7));
            }

            if ($event->status !== Event::STATUS_SELESAI) {
                continue;
            }

            for ($i = 1; $i <= 48; $i++) {
                $contact = $contacts[$contactIndex % max($contacts->count(), 1)] ?? null;
                $contactIndex++;
                $phone = $contact?->no_wa ?: $this->nextPhone();

                $peserta = EventPeserta::query()->updateOrCreate(
                    ['event_id' => $event->id, 'no_hp' => $phone],
                    [
                        'nama' => $contact?->nama ?: $this->randomNama(),
                        'no_wa' => $phone,
                        'alamat' => $contact?->alamat,
                        'dapil' => $contact?->dapil ?: $event->lokasi_dapil,
                        'kecamatan' => $contact?->kecamatan ?: $event->lokasi_kecamatan,
                        'desa' => $contact?->desa ?: $event->lokasi_desa,
                        'nomor_rw' => $contact?->nomor_rw ?: str_pad((string) mt_rand(1, 15), 3, '0', STR_PAD_LEFT),
                        'nomor_rt' => $contact?->rt ?: str_pad((string) mt_rand(1, 8), 3, '0', STR_PAD_LEFT),
                        'target_wilayah_id' => $contact?->target_wilayah_id,
                        'kontak_warga_id' => $contact?->id,
                        'metode' => ['bulk', 'manual', 'qr_scan'][($i - 1) % 3],
                        'synced_sapa_warga' => $i % 4 !== 0,
                        'created_by' => $creatorId,
                    ]
                );
                $this->stamp($peserta, Carbon::parse($event->tanggal_mulai)->copy()->subDay());
            }
        }
    }

    private function seedRkiKsn(Collection $desaAktif, int $creatorId): void
    {
        $this->command?->info('12. Seeding RKI, KSN, dan log sesi...');

        $jenisRki = ['posyandu', 'kerajinan', 'paud', 'alquran', 'sembako', 'arisan', 'kesehatan'];

        foreach ($desaAktif as $desa) {
            foreach ($this->rwRows((string) $desa->id, 3) as $rwIndex => $rw) {
                $status = $rwIndex < 2 ? 'aktif' : 'pembentukan';
                $rki = TitikRki::query()->updateOrCreate(
                    ['target_wilayah_id' => $desa->id, 'nomor_rw' => $rw->nomor_rw],
                    [
                        'dapil' => $desa->dapil,
                        'kecamatan' => $desa->kecamatan,
                        'desa' => $desa->desa,
                        'nama_penggerak' => $this->randomNama('P'),
                        'no_hp_penggerak' => $this->nextPhone(),
                        'lokasi' => ['Musholla Al-Ikhlas', 'Rumah Ibu RT', 'Balai RW', 'Posyandu Mawar'][mt_rand(0, 3)],
                        'hari_kegiatan' => ['senin', 'rabu', 'jumat', 'sabtu'][mt_rand(0, 3)],
                        'jam_kegiatan' => ['09:00', '10:00', '14:00'][mt_rand(0, 2)],
                        'jenis_kegiatan' => array_slice($jenisRki, 0, mt_rand(2, 4)),
                        'avg_peserta' => mt_rand(15, 38),
                        'status' => $status,
                        'tanggal_aktif' => $status === 'aktif' ? $this->randomDate('2025-12-01', '2026-03-31') : null,
                        'catatan' => 'Seeder dummy RKI',
                        'created_by' => $creatorId,
                    ]
                );
                $this->stamp($rki, $this->randomDateTime());

                if ($status === 'aktif') {
                    for ($log = 1; $log <= mt_rand(3, 6); $log++) {
                        $sesi = LogSesi::query()->create([
                            'loggable_type' => TitikRki::class,
                            'loggable_id' => $rki->id,
                            'tanggal_sesi' => $this->randomDateTime('2026-01-01 08:00:00', '2026-06-25 17:00:00'),
                            'jumlah_peserta' => mt_rand(12, 42),
                            'pelaksana' => $rki->nama_penggerak,
                            'catatan' => 'Sesi rutin RKI dengan partisipasi warga stabil.',
                            'created_by' => $creatorId,
                        ]);
                        $this->stamp($sesi, Carbon::parse($sesi->tanggal_sesi));
                    }
                }
            }

            $senam = TitikSenam::query()->updateOrCreate(
                ['target_wilayah_id' => $desa->id, 'nama_titik' => 'Lapangan '.$desa->desa],
                [
                    'dapil' => $desa->dapil,
                    'kecamatan' => $desa->kecamatan,
                    'desa' => $desa->desa,
                    'instruktur' => $this->randomNama('P'),
                    'no_hp_instruktur' => $this->nextPhone(),
                    'instruktur_2' => $this->randomNama('P'),
                    'hari_senam' => ['sabtu', 'minggu'][mt_rand(0, 1)],
                    'jam_senam' => '06:30',
                    'lokasi_rw' => 'RW '.str_pad((string) mt_rand(1, 8), 3, '0', STR_PAD_LEFT),
                    'avg_peserta' => mt_rand(24, 55),
                    'status' => 'aktif',
                    'tanggal_aktif' => $this->randomDate('2025-12-15', '2026-02-28'),
                    'catatan' => 'Seeder dummy KSN/Binapora',
                    'created_by' => $creatorId,
                ]
            );
            $this->stamp($senam, $this->randomDateTime());

            for ($log = 1; $log <= mt_rand(4, 8); $log++) {
                $sesi = LogSesi::query()->create([
                    'loggable_type' => TitikSenam::class,
                    'loggable_id' => $senam->id,
                    'tanggal_sesi' => $this->randomDateTime('2026-01-01 06:00:00', '2026-06-29 09:00:00'),
                    'jumlah_peserta' => mt_rand(22, 60),
                    'pelaksana' => $senam->instruktur,
                    'catatan' => 'Sesi senam warga mingguan.',
                    'created_by' => $creatorId,
                ]);
                $this->stamp($sesi, Carbon::parse($sesi->tanggal_sesi));
            }
        }
    }

    private function seedProgramKerjaAgenda(Collection $bidangs, int $creatorId): void
    {
        $this->command?->info('13. Seeding program kerja & agenda...');

        $statusPool = ['berjalan', 'berjalan', 'selesai', 'belum_mulai', 'tertunda'];

        foreach ($bidangs->take(7) as $index => $bidang) {
            for ($i = 1; $i <= 3; $i++) {
                $status = $statusPool[($index + $i) % count($statusPool)];
                $target = mt_rand(6, 30);
                $realisasi = match ($status) {
                    'selesai' => $target,
                    'berjalan' => mt_rand(1, max($target - 1, 1)),
                    default => 0,
                };

                $program = ProgramKerja::query()->updateOrCreate(
                    ['bidang_dpd_id' => $bidang->id, 'nama_program' => 'Program '.$bidang->nama.' #'.$i],
                    [
                        'deskripsi' => 'Program dummy realistis untuk simulasi monitoring bidang '.$bidang->nama.'.',
                        'tahun' => '2026',
                        'target_teks' => $target.' kegiatan',
                        'target_angka' => $target,
                        'realisasi' => $realisasi,
                        'satuan' => ['kegiatan', 'peserta', 'kecamatan', 'desa'][mt_rand(0, 3)],
                        'periode' => ['Q1', 'Q2', 'Q3', 'Q4', 'sepanjang_tahun'][mt_rand(0, 4)],
                        'deadline' => $this->randomDate('2026-03-01', '2026-12-20'),
                        'pic_nama' => $bidang->pic_nama,
                        'pic_hp' => $bidang->pic_hp,
                        'status' => $status,
                        'progress_pct' => $target > 0 ? min((int) round(($realisasi / $target) * 100), 100) : 0,
                        'catatan' => 'Seeder dummy program kerja.',
                        'created_by' => $creatorId,
                    ]
                );
                $this->stamp($program, $this->randomDateTime());
            }
        }

        $agendaRows = [
            ['judul' => 'Rapat koordinasi DPD + DPC', 'jenis' => 'rapat', 'status' => 'selesai', 'tgl' => '2026-05-15 09:00:00'],
            ['judul' => 'Visitasi KORWE Dapil 1', 'jenis' => 'kunjungan', 'status' => 'selesai', 'tgl' => '2026-05-20 13:00:00'],
            ['judul' => 'Workshop sosmed DPC', 'jenis' => 'pelatihan', 'status' => 'selesai', 'tgl' => '2026-05-22 09:00:00'],
            ['judul' => 'Evaluasi bulanan DPD', 'jenis' => 'rapat', 'status' => 'selesai', 'tgl' => '2026-05-28 14:00:00'],
            ['judul' => 'Baksos kesehatan massal', 'jenis' => 'sosialisasi', 'status' => 'selesai', 'tgl' => '2026-05-30 08:00:00'],
            ['judul' => 'Rapat persiapan Musda', 'jenis' => 'rapat', 'status' => 'dijadwalkan', 'tgl' => '2026-06-08 09:00:00'],
            ['judul' => 'Pelatihan kader angkatan 4', 'jenis' => 'pelatihan', 'status' => 'dijadwalkan', 'tgl' => '2026-06-12 08:00:00'],
            ['judul' => 'Kunjungan lapangan Dapil 5', 'jenis' => 'kunjungan', 'status' => 'dijadwalkan', 'tgl' => '2026-06-15 10:00:00'],
            ['judul' => 'Musyawarah bidang Dakwah', 'jenis' => 'musyawarah', 'status' => 'dijadwalkan', 'tgl' => '2026-06-20 19:30:00'],
        ];

        foreach ($agendaRows as $index => $agendaRow) {
            /** @var BidangDpd $bidang */
            $bidang = $bidangs[$index % $bidangs->count()];
            $program = ProgramKerja::query()->where('bidang_dpd_id', $bidang->id)->orderBy('nama_program')->first();
            $agenda = AgendaDpd::query()->updateOrCreate(
                ['judul' => $agendaRow['judul']],
                [
                    'bidang_dpd_id' => $bidang->id,
                    'program_kerja_id' => $program?->id,
                    'jenis' => $agendaRow['jenis'],
                    'tanggal_mulai' => Carbon::parse($agendaRow['tgl']),
                    'tanggal_selesai' => Carbon::parse($agendaRow['tgl'])->addHours(3),
                    'lokasi' => 'Sekretariat DPD PKS Kabupaten Bekasi',
                    'dapil_terkait' => 'BEKASI '.(($index % 7) + 1),
                    'peserta_target' => mt_rand(15, 40),
                    'peserta_hadir' => $agendaRow['status'] === 'selesai' ? mt_rand(10, 35) : 0,
                    'status' => $agendaRow['status'],
                    'catatan' => 'Agenda dummy untuk simulasi tindak lanjut program.',
                    'hasil' => $agendaRow['status'] === 'selesai' ? 'Agenda terlaksana dengan tindak lanjut per bidang.' : null,
                    'created_by' => $creatorId,
                ]
            );
            $this->stamp($agenda, Carbon::parse($agendaRow['tgl'])->subDays(10));
        }
    }

    private function seedSosmed(): void
    {
        $this->command?->info('14. Seeding anggota dewan asli...');

        $this->call([
            AnggotaDewanSeeder::class,
        ]);
    }

    private function seedMateriDigital(Collection $focusDesas, int $creatorId): void
    {
        $this->command?->info('15. Seeding materi digital...');

        $rows = [
            ['judul' => 'Poster Baksos Kesehatan Juni 2026', 'jenis' => 'poster', 'status' => 'published'],
            ['judul' => 'Video Profil DPD PKS Kab. Bekasi', 'jenis' => 'video', 'status' => 'published'],
            ['judul' => 'Infografis Program RKI', 'jenis' => 'infografis', 'status' => 'published'],
            ['judul' => 'Flyer Senam PKS Mingguan', 'jenis' => 'flyer', 'status' => 'draft'],
            ['judul' => 'Poster Pendaftaran Anggota', 'jenis' => 'poster', 'status' => 'draft'],
        ];

        foreach ($rows as $index => $row) {
            $materi = MateriDigital::query()->updateOrCreate(
                ['judul' => $row['judul']],
                [
                    'jenis' => $row['jenis'],
                    'deskripsi' => 'Materi digital dummy untuk distribusi jaringan dan pengujian modul.',
                    'file_path' => 'materi/placeholder.jpg',
                    'thumbnail' => 'materi/thumb-placeholder.jpg',
                    'topik' => ['pelayanan', 'rekrutmen', 'program', 'event', 'branding'][$index % 5],
                    'distribusi_count' => $row['status'] === 'published' ? 1 : 0,
                    'status' => $row['status'],
                    'created_by' => $creatorId,
                ]
            );
            $this->stamp($materi, $this->randomDateTime());

            if ($row['status'] !== 'published') {
                continue;
            }

            /** @var TargetWilayah $target */
            $target = $focusDesas[$index % $focusDesas->count()];
            $distribusi = DistribusiMateri::query()->updateOrCreate(
                ['materi_digital_id' => $materi->id, 'channel' => ['wa_blast', 'wa_grup_korwe', 'wa_grup_korte'][$index % 3]],
                [
                    'target_dapil' => $target->dapil,
                    'target_rw_count' => mt_rand(45, 180),
                    'terkirim' => mt_rand(30, 170),
                    'terbaca' => mt_rand(10, 120),
                    'tanggal_distribusi' => $this->randomDate('2026-04-01', '2026-06-30'),
                    'catatan' => 'Distribusi dummy untuk pengujian dashboard materi digital.',
                    'created_by' => $creatorId,
                ]
            );
            $this->stamp($distribusi, Carbon::parse($distribusi->tanggal_distribusi));
        }
    }

    private function seedAuditLogs(Collection $admins, Collection $bidangUsers, Collection $kaderUsers, Collection $memberUsers): void
    {
        $this->command?->info('16. Seeding audit log...');

        $users = $admins->concat($bidangUsers)->concat($kaderUsers)->concat($memberUsers->take(10));
        $actions = ['login', 'create_event', 'update_kontak_warga', 'sync_infra', 'approve_event', 'generate_member_card'];

        foreach ($users as $index => $user) {
            $log = AuditLog::query()->create([
                'user_id' => $user->id,
                'action' => $actions[$index % count($actions)],
                'description' => 'Aktivitas dummy untuk pengujian histori audit.',
                'ip_address' => '127.0.0.'.(($index % 20) + 1),
                'user_agent' => 'Dummy Seeder Agent/1.0',
                'metadata' => [
                    'source' => 'dummy-seeder',
                    'email' => $user->email,
                ],
            ]);
            $this->stamp($log, $this->randomDateTime());
        }
    }

    private function upsertKader(TargetWilayah $village, string $nomorRw, string $nomorRt, string $jenjang, array $flags = []): Kader
    {
        $nia = $this->nextNia();
        $kader = Kader::query()->updateOrCreate(
            ['nia' => $nia],
            [
                'nama' => $this->randomNama(),
                'no_hp' => $this->nextPhone(),
                'no_wa' => $this->nextPhone(),
                'email' => 'kader.'.strtolower(str_replace(' ', '.', $nia)).'@dummy.kbh.local',
                'no_kta' => 'KTA-'.$nia,
                'jenjang' => $jenjang,
                'tanggal_jenjang' => $this->randomDate('2025-12-01', '2026-06-30'),
                'dapil' => $village->dapil,
                'kecamatan' => $village->kecamatan,
                'desa' => $village->desa,
                'nomor_rw' => $nomorRw,
                'nomor_rt' => $nomorRt,
                'target_wilayah_id' => $village->id,
                'is_korwe' => (bool) ($flags['is_korwe'] ?? false),
                'is_korte' => (bool) ($flags['is_korte'] ?? false),
                'is_upa' => (bool) ($flags['is_upa'] ?? false),
                'jabatan_upa' => $flags['jabatan_upa'] ?? null,
                'is_penggalang' => (bool) ($flags['is_penggalang'] ?? false),
                'is_saksi' => (bool) ($flags['is_saksi'] ?? false),
                'keahlian' => $this->randomSkills(),
                'bisa_deploy' => (bool) ($flags['bisa_deploy'] ?? true),
                'status' => 'aktif',
                'is_activated' => false,
                'catatan' => 'Seeder dummy kader',
            ]
        );
        $this->stamp($kader, $this->randomDateTime());

        return $kader;
    }

    private function rwRows(string $targetWilayahId, int $limit): Collection
    {
        return DataRw::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->orderBy('nomor_rw')
            ->limit($limit)
            ->get();
    }

    private function randomNama(?string $gender = null): string
    {
        if ($gender === 'L') {
            return $this->namaLaki[array_rand($this->namaLaki)];
        }

        if ($gender === 'P') {
            return $this->namaPerempuan[array_rand($this->namaPerempuan)];
        }

        $all = array_merge($this->namaLaki, $this->namaPerempuan);

        return $all[array_rand($all)];
    }

    private function randomSkills(): array
    {
        $skills = array_keys(Kader::KEAHLIAN_OPTIONS);
        shuffle($skills);

        return array_slice($skills, 0, mt_rand(1, 3));
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

    private function nextPhone(): string
    {
        $prefixes = ['0812', '0813', '0821', '0822', '0852', '0856', '0857', '0858', '0878'];
        $prefix = $prefixes[$this->phoneCounter % count($prefixes)];
        $number = str_pad((string) $this->phoneCounter, 8, '0', STR_PAD_LEFT);
        $this->phoneCounter++;

        return $prefix.$number;
    }

    private function nextNia(): string
    {
        $this->niaCounter++;
        $n = $this->niaCounter;

        return sprintf('%03d.%03d.%03d', intdiv($n, 1000000) % 1000, intdiv($n, 1000) % 1000, $n % 1000);
    }

    private function randomDate(string $from, string $to): Carbon
    {
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
        $days = max((int) ceil((float) $start->diffInDays($end)), 1);

        return $start->copy()->addDays(mt_rand(0, $days));
    }

    private function randomDateTime(?string $from = null, ?string $to = null): Carbon
    {
        $start = Carbon::parse($from ?? self::WINDOW_START);
        $end = Carbon::parse($to ?? self::WINDOW_END);
        $seconds = max((int) ceil((float) $start->diffInSeconds($end)), 1);

        return $start->copy()->addSeconds(mt_rand(0, $seconds));
    }

    private function stamp(Model $model, Carbon $createdAt): void
    {
        $updatedAt = $createdAt->copy()->addHours(mt_rand(1, 72));
        $model->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ])->saveQuietly();
    }
}

final class PermissionNames
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            User::menuPermissionName('dashboard'),
            User::menuPermissionName('infra-rtrw'),
            User::menuPermissionName('sapa-warga'),
            User::menuPermissionName('sisir-rw'),
            User::menuPermissionName('kaderisasi'),
            User::menuPermissionName('pemilu-dprd'),
            User::menuPermissionName('analisa-caleg'),
            User::menuPermissionName('peta-kekuatan-rw'),
            User::menuPermissionName('event'),
            User::menuPermissionName('event-view'),
            User::menuPermissionName('sosial-media'),
            User::menuPermissionName('program-kerja'),
            User::menuPermissionName('pengaturan'),
            User::menuPermissionName('profil'),
            User::menuPermissionName('rki'),
            User::menuPermissionName('ksn'),
        ];
    }
}
