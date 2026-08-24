<?php

namespace App\Console\Commands;

use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use App\Models\User;
use App\Services\GoogleSheetsService;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportSisirRwFromGoogleSheets extends Command
{
    protected $signature = 'import:sisir-rw';
    protected $description = 'Import data Sisir RW dari Google Sheets lama ke database';

    public function handle()
    {
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $tabName = config('services.google_sheets.tab_name', 'Data Sisir RW');

        if (empty($spreadsheetId)) {
            $this->error('GOOGLE_SHEET_SISIR_RW_ID tidak ditemukan di .env');
            return;
        }

        $credentialsPath = base_path(config('services.google_sheets.credentials_path'));
        if (!file_exists($credentialsPath)) {
            $this->error('File credential JSON tidak ditemukan di ' . $credentialsPath);
            return;
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $service = new Sheets($client);

        $range = "{$tabName}!A2:N";
        $this->info("Mengambil data dari Google Sheet: {$spreadsheetId} - {$range}");

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
        } catch (\Exception $e) {
            $this->error('Gagal mengakses Google Sheets: ' . $e->getMessage());
            return;
        }

        if (empty($values)) {
            $this->info('Tidak ada data ditemukan.');
            return;
        }

        $adminUser = User::where('role', 'admin')->first() ?? User::first();
        if (!$adminUser) {
            $this->error('Tidak ada user admin di database untuk dijadikan creator.');
            return;
        }

        $imported = 0;
        $skipped = 0;

        foreach ($values as $index => $row) {
            $waktuKegiatan = $row[1] ?? null;
            if (empty($waktuKegiatan)) {
                $skipped++;
                continue;
            }

            $kecamatan = trim($row[3] ?? '');
            $desa = trim($row[4] ?? '');
            $rw = trim($row[5] ?? '');

            if (empty($kecamatan) || empty($desa) || empty($rw)) {
                $skipped++;
                continue;
            }

            // Bersihkan nomor RW (misal "001" jadi "001", "RW 01" jadi "001", dll)
            $rw = preg_replace('/[^0-9]/', '', $rw);
            $rw = str_pad($rw, 3, '0', STR_PAD_LEFT);

            $targetWilayah = TargetWilayah::where('kecamatan', 'LIKE', "%{$kecamatan}%")
                ->where('desa', 'LIKE', "%{$desa}%")
                ->first();

            if (!$targetWilayah) {
                $this->warn("Baris " . ($index + 2) . ": Wilayah tidak ditemukan ({$kecamatan} - {$desa})");
                $skipped++;
                continue;
            }

            try {
                // Tanggal format di GSheet biasanya d/m/Y atau m/d/Y, kita coba parse
                $tanggal = Carbon::parse(str_replace('/', '-', $waktuKegiatan));
            } catch (\Exception $e) {
                $tanggal = now();
            }

            $jenisRaw = strtolower(trim($row[10] ?? ''));
            $jenisKey = 'lainnya';
            foreach (KegiatanRw::JENIS_KEGIATAN as $key => $config) {
                if (strtolower($config['label']) == $jenisRaw) {
                    $jenisKey = $key;
                    break;
                }
            }

            // Tidak menggunakan "pelaksana" dari gabungan dewan, set default 'Tim Sisir RW'
            $pelaksana = 'Tim Sisir RW';

            KegiatanRw::updateOrCreate(
                [
                    'target_wilayah_id' => $targetWilayah->id,
                    'nomor_rw' => $rw,
                    'tanggal_kegiatan' => $tanggal->format('Y-m-d'), // Hanya samakan tanggalnya
                ],
                [
                    'dapil' => $targetWilayah->dapil,
                    'kecamatan' => $targetWilayah->kecamatan,
                    'desa' => $targetWilayah->desa,
                    'jenis_kegiatan' => $jenisKey !== 'lainnya' ? $jenisKey : ($row[10] ?? 'Lainnya'),
                    'segmen' => $row[11] ?? null,
                    'pelaksana' => $pelaksana,
                    'dpr_ri_hadir' => $row[6] ?? 'TIDAK ADA',
                    'dprd_prov_hadir' => $row[7] ?? 'TIDAK ADA',
                    'dprd_kab_hadir' => $row[8] ?? 'TIDAK ADA',
                    'tempat_kegiatan' => $row[9] ?? null,
                    'jumlah_warga' => 0,
                    'catatan' => $row[12] ?? null,
                    'created_by' => $adminUser->id,
                ]
            );

            $imported++;
        }

        $this->info("Import selesai! Berhasil: {$imported}, Dilewati (Duplikat/Tidak Valid): {$skipped}");
    }
}
