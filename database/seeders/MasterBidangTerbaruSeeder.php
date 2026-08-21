<?php

namespace Database\Seeders;

use App\Models\BidangDpd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterBidangTerbaruSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Badan Pembinaan Pejabat Publik', 'BPPPu', null, null, null, null],
            ['Badan Penelitian dan Pengembangan', 'BALITBANG', null, null, null, null],
            ['Badan Diplomasi dan Pembinaan Luar Negeri', 'BDPLN', null, null, null, null],
            ['Badan Legislasi Partai', 'BLP', null, null, null, null],
            ['Badan Pembinaan dan Pengembangan Cabang', 'BPPC', 'Eko Sutrisno', '0812-135-8486', 'Prayono, S.Kom', '085814838217'],
            ['Bidang Advokasi Partai', 'BAP', null, null, null, null],
            ['Bidang Relawan dan Saksi Nasional', 'BRSN', 'Mikail Ruzqi, S.T', '08128263605', 'Akhmad Taslim, A.M.', '08121942544'],
            ['Bidang Politik Hukum dan Keamanan', 'Polhukam', 'SAEFUL ISLAM, SH., M.A.', '081224562141', 'SUPRIYANTO, S.H', '081224626392'],
            ['Bidang Ekonomi Keuangan dan Industri', 'Ekuin', null, null, null, null],
            ['Bidang Pendidikan dan Kesehatan', 'BPdK', 'Dr.Ida Saidah, M.Si', '081212919208', null, null],
            ['Bidang Energi Lingkungan Hidup dan Perubahan Iklim', 'BELHPI', 'Aries Suwarno', '081212061312', 'TRI KARSONO, S.Sos', '0811942229'],
            ['Bidang Pemenangan PEMILU dan PILKADA', 'BP3', 'H NURDIN RIVALDY SAg MSi.', '081315260559', 'Andrie Cahyono, SE.', '0811585291'],
            ['Bidang Ketenagakerjaan', 'Bidnaker', 'Yusuf Fathullah Fajri AMd', '082112363568', 'Nur Waluyo, SH.', '081311221788'],
            ['Bidang Petani Peternak dan Nelayan', 'BPPN', null, null, 'Ismail Marzuki', '085694666861'],
            ['Bidang Pemberdayaan UMKM dan Ekonomi Kreatif', 'BUMKraf', 'Ir. Tajudin Nur, ST, MT', '081286291034', 'Mahmudin Purwo Saputro', '085889676450'],
            ['Bidang Koperasi dan Desa', 'Kopdes', 'H Amin Syafii, SPi MM', '0816651384', 'Oky Satria Nugraha, S.T.', '085269727743'],
            ['Bidang Pembinaan dan Pengembangan Olahraga', 'Binapora', 'Fahrudin', '085697003943', 'Muhamad Dedi Suryadi, S.Kom, M.Kom', '082111415252'],
            ['Bidang Komunikasi dan Digital', 'Komdigi', 'Sutrisno', '081311054814', 'M Lukman Hakim', '081296413030'],
            ['Bidang Pembinaan Masyarakat Rentan dan Disabilitas', 'BPMRD', 'Puji Lestari Amd', '085210029003', null, null],
            ['Bidang Pembinaan Umat dan Kerukunan Beragama', 'BPUKB', 'H. Muhammad Hasan Hidayatulah, MM', '081385681188', 'M Ali Mahfuzh, S.Ag', '081385524853'],
            ['Bidang Kepanduan dan Bela Negara', 'BKBN', 'TARYONO, SE', '08129680479', 'M Guntur Yadi', '08118481708'],
            ['Bidang Seni dan Budaya', 'Senbud', 'Dharmaji Santoso, M.Pd.', '081399315439', 'Tomy Johan Fauzi, SE, MM', '081385134408'],
            ['Bidang Pemuda Pelajar dan Mahasiswa', 'BPPM', 'Fazri Azhar Ramdani S.Si, MT', '08119186969', 'Salman Al-Farisi, S.H., M.B.A., CHRM.', '085335504400'],
            ['Bidang Perempuan dan Keluarga', 'BiPeKa', 'Rika Febrika, S.Psi', '081310004436', 'Diah Nahdiyati', '085217007505'],
            ['Bidang Kaderisasi Anggota Partai', 'BKAP', 'Kawidi, S.Pd.I', '08129393771', 'Muhammad Haritzahzen, S.Si', '08989532217'],
            ['Bidang Pelatihan dan Pengembangan Kepemimpinan Partai', 'BPPKP', 'M. Abduh', '085959674295', 'Rahmat Fajar, S. Pd. Gr', '081290406057'],
            ['Kantor Staf Presiden', 'KSP', null, null, null, null],
        ];

        foreach ($data as $index => $row) {
            $nama = $row[0];
            $singkatan = $row[1];
            $kabid = $row[2];
            $nohpkabid = $row[3];
            $sekbid = $row[4];
            $nohpsekbid = $row[5];

            BidangDpd::updateOrCreate(
                ['nama' => $nama],
                [
                    'singkatan' => $singkatan,
                    'slug' => Str::slug($nama),
                    'kabid' => $kabid,
                    'nohpkabid' => $nohpkabid,
                    'sekbid' => $sekbid,
                    'nohpsekbid' => $nohpsekbid,
                    'periode' => '2025-2030',
                    'is_active' => true,
                    'urutan' => $index + 1,
                    // Keep existing color/icon if they exist, or set default empty.
                ]
            );
        }
    }
}
