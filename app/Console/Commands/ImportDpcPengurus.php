<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportDpcPengurus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dpc-pengurus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import DPC structural data from hardcoded array to users table';

    private array $dpcData = [
        ['dpc' => 'Cikarang Selatan', 'ketua' => 'Diar Ardian', 'wa_ketua' => '08159469911', 'sekretaris' => 'Salman Al-Farisi', 'wa_sekretaris' => '085335504400', 'bendahara' => 'Muhamad Mustofa', 'wa_bendahara' => '083815906733', 'kaderisasi' => 'Dede Kundrat', 'wa_kaderisasi' => '08973736733', 'spkk' => 'Frida Ayuningtyas', 'wa_spkk' => '081271406725', 'kepemudaan' => 'Oman Lesmana', 'wa_kepemudaan' => '082110034640'],
        ['dpc' => 'Sukakarya', 'ketua' => 'Gunin jaenudin', 'wa_ketua' => '085886692214', 'sekretaris' => 'Masrur al-haedar', 'wa_sekretaris' => '085890087228', 'bendahara' => 'Da\'i supriadi', 'wa_bendahara' => '082122492045', 'kaderisasi' => 'Edi mz', 'wa_kaderisasi' => '081281913270', 'spkk' => 'Nurjanah', 'wa_spkk' => '085775076912', 'kepemudaan' => 'Wawan', 'wa_kepemudaan' => '081910983452'],
        ['dpc' => 'Serang Baru', 'ketua' => 'MUHAMMAD ABDUH', 'wa_ketua' => '08128078847', 'sekretaris' => 'NUR WALUYO', 'wa_sekretaris' => '081311221788', 'bendahara' => 'ANDI DARMAWAN', 'wa_bendahara' => '081808320618', 'kaderisasi' => 'RUSDIANTO', 'wa_kaderisasi' => '08159493878', 'spkk' => 'SUHENI', 'wa_spkk' => '089635978014', 'kepemudaan' => 'DEDI WIJAYA', 'wa_kepemudaan' => '085776726001'],
        ['dpc' => 'Babelan', 'ketua' => 'Agus Dwi Sasongko', 'wa_ketua' => '081388958244', 'sekretaris' => 'Warmu Hibban', 'wa_sekretaris' => '081210200871', 'bendahara' => 'Trimanto', 'wa_bendahara' => '085101723821', 'kaderisasi' => 'H. Hanif Setiawan', 'wa_kaderisasi' => '085773049129', 'spkk' => 'Ismawati', 'wa_spkk' => '087804631162', 'kepemudaan' => 'Akram Rizqullah', 'wa_kepemudaan' => '085216538171'],
        ['dpc' => 'Setu', 'ketua' => 'Akhmad Jajuli SST MM', 'wa_ketua' => '081511240607', 'sekretaris' => 'Muhammad Fauzan SPt MM', 'wa_sekretaris' => '081288219275', 'bendahara' => 'H. Pajarso', 'wa_bendahara' => '081398625355', 'kaderisasi' => 'M.Abduh', 'wa_kaderisasi' => '085959674295', 'spkk' => 'Sri Rejeki', 'wa_spkk' => '081586038747', 'kepemudaan' => 'Novin Syahputra', 'wa_kepemudaan' => '08986800038'],
        ['dpc' => 'Karang Bahagia', 'ketua' => 'Ardiyansyah', 'wa_ketua' => '0812-8099-5095', 'sekretaris' => 'Taufik Jaelani', 'wa_sekretaris' => '0813-8005-9728', 'bendahara' => 'Nurman', 'wa_bendahara' => '0857-1757-9495', 'kaderisasi' => 'Rofik Hidayanto', 'wa_kaderisasi' => '0813-1150-4210', 'spkk' => 'Nunung Maryati', 'wa_spkk' => '0856-1545-787', 'kepemudaan' => 'David', 'wa_kepemudaan' => '0852-1713-1722'],
        ['dpc' => 'Bojong Mangu', 'ketua' => 'Yana Hendriyana', 'wa_ketua' => '085777480389', 'sekretaris' => 'Ojim Suryana', 'wa_sekretaris' => '085693222268', 'bendahara' => 'Dono', 'wa_bendahara' => '081380095049', 'kaderisasi' => 'Ahmad Kusmayadi', 'wa_kaderisasi' => '089505892485', 'spkk' => 'Menyusul', 'wa_spkk' => null, 'kepemudaan' => 'Menyusul', 'wa_kepemudaan' => null],
        ['dpc' => 'Sukawangi', 'ketua' => 'Sanusi', 'wa_ketua' => '0856-7665-667', 'sekretaris' => 'Masim', 'wa_sekretaris' => '0895-0568-0570', 'bendahara' => 'Tabroni', 'wa_bendahara' => '0857-7037-4390', 'kaderisasi' => 'Amir Hot', 'wa_kaderisasi' => '0812-2314-7099', 'spkk' => 'Marsanih', 'wa_spkk' => '0857-1740-9878', 'kepemudaan' => 'Marwan', 'wa_kepemudaan' => '0857-7847-9356'],
        ['dpc' => 'Tambun Utara', 'ketua' => 'Marhadi', 'wa_ketua' => '08561315195', 'sekretaris' => 'Deni Wahyudi', 'wa_sekretaris' => '081282037468', 'bendahara' => 'Arif Sihabudin', 'wa_bendahara' => '08979327367', 'kaderisasi' => 'Hamdan', 'wa_kaderisasi' => '081314226263', 'spkk' => 'Sri Hidayah', 'wa_spkk' => '081285587841', 'kepemudaan' => 'Suhartono', 'wa_kepemudaan' => '087780630773'],
        ['dpc' => 'Tarumajaya', 'ketua' => 'Endang Sudrajat', 'wa_ketua' => '0813-8880-0481', 'sekretaris' => 'Muhammad Ilham', 'wa_sekretaris' => '0819-0828-8990', 'bendahara' => 'Hermoyo Widyantoro', 'wa_bendahara' => '0888-1214-039', 'kaderisasi' => 'Saiful Bahri SH,I', 'wa_kaderisasi' => '0878-7818-2152', 'spkk' => 'Ely Suryani', 'wa_spkk' => '0812-1163-2540', 'kepemudaan' => 'Hermanto Madelis', 'wa_kepemudaan' => '0878-8077-7500'],
        ['dpc' => 'Tambun Selatan', 'ketua' => 'Imam Nugroho', 'wa_ketua' => '082360191983', 'sekretaris' => 'Roby Abdullah Mustika', 'wa_sekretaris' => '085720992707', 'bendahara' => 'Budi Santosa', 'wa_bendahara' => '085289644605', 'kaderisasi' => 'Irwan Trianto', 'wa_kaderisasi' => '089664896726', 'spkk' => 'Deni Trisnawati', 'wa_spkk' => '081316860169', 'kepemudaan' => 'Tasirun', 'wa_kepemudaan' => '081286382487'],
        ['dpc' => 'Sukatani', 'ketua' => 'ENJANG FERI GINANJAR S.Sy', 'wa_ketua' => '081282575192', 'sekretaris' => 'WARGIONO SIGIT', 'wa_sekretaris' => '085719379945', 'bendahara' => 'ISZUL IQBAL HAIRONI', 'wa_bendahara' => '085959677698', 'kaderisasi' => 'UST. DIMYAT', 'wa_kaderisasi' => '089674106123', 'spkk' => 'FATIHAH S.PD', 'wa_spkk' => '082218354808', 'kepemudaan' => 'AGUS SUNARDI', 'wa_kepemudaan' => '089513784019'],
        ['dpc' => 'Muaragembong', 'ketua' => 'Gosan Haikal Muzakir. S.M.', 'wa_ketua' => '081315869244', 'sekretaris' => 'Miki rizal', 'wa_sekretaris' => '081413331522', 'bendahara' => 'Nur Aini', 'wa_bendahara' => '081410749024', 'kaderisasi' => 'Redi abu fakhri', 'wa_kaderisasi' => '089653972475', 'spkk' => 'Masitoh', 'wa_spkk' => '085719014854', 'kepemudaan' => 'Bahrudin jamil', 'wa_kepemudaan' => '085715131047'],
        ['dpc' => 'Pebayuran', 'ketua' => 'Eman Sulaeman, S. Pt. S.Pd. MM', 'wa_ketua' => '085692031013', 'sekretaris' => 'Yana', 'wa_sekretaris' => '0856102009', 'bendahara' => 'Nisjaya Asmoro', 'wa_bendahara' => '089609901747', 'kaderisasi' => 'Agus Sudono', 'wa_kaderisasi' => '085819769668', 'spkk' => 'Ulfah Fauziah', 'wa_spkk' => '085720400071', 'kepemudaan' => 'Yanta', 'wa_kepemudaan' => '083895282872'],
        ['dpc' => 'Cabang Bungin', 'ketua' => 'Saefuddin Maska,S.Ag.M.A', 'wa_ketua' => '081284236258', 'sekretaris' => 'Abdul Rohman, S.Pd', 'wa_sekretaris' => '081510781949', 'bendahara' => 'Sajam,S.Pd.I', 'wa_bendahara' => '085881262615', 'kaderisasi' => 'Madrudin Junaedi, S.Pd.I', 'wa_kaderisasi' => '085893701360', 'spkk' => 'Nuriah', 'wa_spkk' => '08871736193', 'kepemudaan' => 'Muridin,S.Pd.I', 'wa_kepemudaan' => '081289803968'],
        ['dpc' => 'Cikarang Utara', 'ketua' => 'Deviyanto Hidayat', 'wa_ketua' => '08118884805', 'sekretaris' => 'RINDIOKO', 'wa_sekretaris' => '081219140056', 'bendahara' => 'Lutfi Makmur', 'wa_bendahara' => '081380409755', 'kaderisasi' => 'Denny Dwi Indriyanto', 'wa_kaderisasi' => '081314025287', 'spkk' => 'Fitriyani Agustina', 'wa_spkk' => '081324600231', 'kepemudaan' => 'Edi Rohendi', 'wa_kepemudaan' => '081514186302'],
        ['dpc' => 'Cibitung', 'ketua' => 'Kholid Abdullah', 'wa_ketua' => '081384435710', 'sekretaris' => 'Lesmana', 'wa_sekretaris' => '081510691631', 'bendahara' => 'Fazri Azhar Ramdani', 'wa_bendahara' => '08119186869', 'kaderisasi' => 'Iskandar', 'wa_kaderisasi' => '0895359730164', 'spkk' => 'Nina Wati', 'wa_spkk' => '081284616695', 'kepemudaan' => 'Ilham Suryahadi', 'wa_kepemudaan' => '0813-9863-7362'],
        ['dpc' => 'Cikarang Timur', 'ketua' => 'Abdurokhman', 'wa_ketua' => '0821-2583-0531', 'sekretaris' => 'Dede Supriadi', 'wa_sekretaris' => '0856-1871-056', 'bendahara' => 'Ponijan', 'wa_bendahara' => '0813-8346-7371', 'kaderisasi' => 'Rizali Noor', 'wa_kaderisasi' => '0813-1623-9733', 'spkk' => 'Mulyani', 'wa_spkk' => '0813-1948-9056', 'kepemudaan' => 'Arif Rubiyanto', 'wa_kepemudaan' => '0878-7559-9055'],
        ['dpc' => 'Tambelang', 'ketua' => 'Tajudin.S.P.di', 'wa_ketua' => '081908284671', 'sekretaris' => 'Ishmah Amalia,S.I.Kom', 'wa_sekretaris' => '085715430316', 'bendahara' => 'Maman Sulaeman', 'wa_bendahara' => '085880999073', 'kaderisasi' => 'Wardiana Kusumah', 'wa_kaderisasi' => '081385382597', 'spkk' => 'sementara istri ketua dpc', 'wa_spkk' => '081908284671', 'kepemudaan' => 'Habibi', 'wa_kepemudaan' => '089676178670'],
        ['dpc' => 'Kedungwaringin', 'ketua' => 'Ujang Roswandi', 'wa_ketua' => '081285668186', 'sekretaris' => 'Dian Sonata', 'wa_sekretaris' => '0895328036457', 'bendahara' => 'Tedi Hidayat', 'wa_bendahara' => '088297112376', 'kaderisasi' => 'Abdurrosyad', 'wa_kaderisasi' => '081282852184', 'spkk' => 'Desi Martini', 'wa_spkk' => '085224041246', 'kepemudaan' => 'M.Muhtadi', 'wa_kepemudaan' => '0895386861090'],
        ['dpc' => 'Cibarusah', 'ketua' => 'Septo Susanto', 'wa_ketua' => '081282269985', 'sekretaris' => 'Subhana', 'wa_sekretaris' => '085691101366', 'bendahara' => 'Asep Mumuh', 'wa_bendahara' => '087873816287', 'kaderisasi' => 'Deden Saefudin', 'wa_kaderisasi' => '082393925009', 'spkk' => 'Tri Lestari', 'wa_spkk' => '085892211095', 'kepemudaan' => 'N/A', 'wa_kepemudaan' => null],
        ['dpc' => 'Cikarang Barat', 'ketua' => 'Deni Halman', 'wa_ketua' => '08111870813', 'sekretaris' => 'Kamali Kumar', 'wa_sekretaris' => '081283708347', 'bendahara' => 'Ahmad Fathoni', 'wa_bendahara' => '081296709980', 'kaderisasi' => 'Muryanto', 'wa_kaderisasi' => '081310720319', 'spkk' => 'Aisyah Bali Putri', 'wa_spkk' => '08128906348', 'kepemudaan' => 'Sayudi', 'wa_kepemudaan' => '0818618186'],
        ['dpc' => 'Cikarang Pusat', 'ketua' => 'EKO SUJANTO', 'wa_ketua' => '085776979706', 'sekretaris' => 'AHMAD KUSMAYADI', 'wa_sekretaris' => '089505892485', 'bendahara' => 'RIZKI WICAKSANA', 'wa_bendahara' => '081212210940', 'kaderisasi' => 'ARDI KURNIAWAN', 'wa_kaderisasi' => '081298515571', 'spkk' => 'KARTINI', 'wa_spkk' => '08991023663', 'kepemudaan' => 'WAYAN', 'wa_kepemudaan' => '081296681665'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting previous imports...');
        // Hapus user yang sebelumnya diimpor menggunakan fake email ini
        User::where('email', 'like', '%@dpc.bekasihebat.local')->delete();

        $this->info('Starting DPC Pengurus import with new rules...');
        $countImported = 0;

        foreach ($this->dpcData as $row) {
            $dpcName = trim($row['dpc']);
            $this->info("Processing DPC: {$dpcName}");

            // Kita skip kepemudaan sesuai instruksi
            $positions = ['ketua', 'sekretaris', 'bendahara', 'kaderisasi', 'spkk'];

            foreach ($positions as $pos) {
                $name = $row[$pos] ?? null;
                $phone = $row["wa_{$pos}"] ?? null;

                if (!$name || strtolower($name) === 'menyusul' || strtolower($name) === 'n/a') {
                    continue;
                }

                if (!$phone || strtolower($phone) === 'menyusul' || strtolower($phone) === 'n/a') {
                    continue;
                }

                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                if (str_starts_with($cleanPhone, '62')) {
                    $cleanPhone = '0' . substr($cleanPhone, 2);
                }

                if (strlen($cleanPhone) < 9) {
                    continue;
                }

                // Determine role and bidang
                $role = 'pengurus_dpc';
                $bidangSlug = null;

                if ($pos === 'spkk') {
                    $role = 'pengurus_bidang';
                    $bidangSlug = 'perempuan'; // Asumsi Bipeka = Perempuan
                }

                // Cek duplikasi phone
                if (User::where('phone', $cleanPhone)->exists()) {
                    $this->info("Skipping duplicate phone: {$cleanPhone} for {$name}");
                    continue;
                }

                $user = User::create([
                    'name' => $name,
                    'phone' => $cleanPhone,
                    'email' => $cleanPhone . '@dpc.bekasihebat.local',
                    'password' => Hash::make('BekasiHebat123'),
                    'role' => $role,
                    'org_level' => 'dpc',
                    'kecamatan' => $dpcName,
                    'bidang_slug' => $bidangSlug,
                    'status' => 'nonaktif',
                ]);
                $user->assignRole($role);
                $countImported++;
            }
        }

        $this->info("Import complete. Total Imported: {$countImported}");
    }
}
