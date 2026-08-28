<?php

namespace App\Console\Commands;

use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use Illuminate\Console\Command;

class ExportKaderToGoogleSheets extends Command
{
    protected $signature = 'export:kader {spreadsheet_id} {--tab=Form Responses 1}';
    protected $description = 'Export data Korwe/Kortw/Penggalang dari form Input Infrastruktur ke Google Sheets';

    public function handle()
    {
        $spreadsheetId = $this->argument('spreadsheet_id');
        $tabName = $this->option('tab');

        $credentialsPath = base_path(config('services.google_sheets.credentials_path'));

        if (!file_exists($credentialsPath)) {
            $this->error("File credentials Google tidak ditemukan di: {$credentialsPath}");
            return;
        }

        try {
            $client = new Client();
            $client->setApplicationName('Bekasi Hebat Sync');
            $client->setScopes([Sheets::SPREADSHEETS]); 
            $client->setAuthConfig($credentialsPath);
            $client->setAccessType('offline');

            $service = new Sheets($client);
            
            $this->info("Menyiapkan data dari database infrastruktur...");
            
            $values = [
                ['Timestamp', 'KAB / KOTA', 'KECAMATAN', 'KEL / DESA', 'RW', 'NAMA LENGKAP SESUAI KTP', 'Nomor Whatsapp', 'PERAN', 'NOMOR KTP / NIK']
            ];

            // 1. Ambil Korwe
            $korwes = Korwe::with('targetWilayah')->get();
            foreach ($korwes as $k) {
                $values[] = [
                    $k->created_at ? $k->created_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
                    'KAB BEKASI',
                    $k->targetWilayah->kecamatan ?? '',
                    $k->targetWilayah->desa ?? '',
                    $k->nomor_rw ?? '',
                    $k->nama_koordinator ?? '',
                    $k->no_hp ?? '',
                    'KORWE',
                    '' // NIK tidak ada di tabel Korwe
                ];
            }

            // 2. Ambil Korte
            $kortes = Korte::with('targetWilayah')->get();
            foreach ($kortes as $k) {
                $values[] = [
                    $k->created_at ? $k->created_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
                    'KAB BEKASI',
                    $k->targetWilayah->kecamatan ?? '',
                    $k->targetWilayah->desa ?? '',
                    $k->nomor_rw ?? '',
                    $k->nama_koordinator ?? '',
                    $k->no_hp ?? '',
                    'KORTW',
                    ''
                ];
            }

            // 3. Ambil Penggalang
            $penggalangs = PenggalangSuara::get(); // Sudah ada dapil, kecamatan, desa di tabel
            foreach ($penggalangs as $p) {
                $values[] = [
                    $p->created_at ? $p->created_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
                    'KAB BEKASI',
                    $p->kecamatan ?? '',
                    $p->desa ?? '',
                    $p->nomor_rw ?? '',
                    $p->nama ?? '',
                    $p->no_wa ?? $p->no_hp ?? '',
                    'PENGGALANG',
                    ''
                ];
            }

            $this->info("Mengekspor " . (count($values) - 1) . " data ke Google Sheets...");

            $clearRequest = new ClearValuesRequest();
            $service->spreadsheets_values->clear($spreadsheetId, "{$tabName}!A:I", $clearRequest);

            $body = new ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $result = $service->spreadsheets_values->update($spreadsheetId, "{$tabName}!A1:I", $body, $params);

            $this->info(sprintf("Sukses! %d baris berhasil diupdate.", $result->getUpdatedCells()));
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
