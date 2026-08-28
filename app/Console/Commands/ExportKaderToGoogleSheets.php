<?php

namespace App\Console\Commands;

use App\Models\Kader;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use Illuminate\Console\Command;

class ExportKaderToGoogleSheets extends Command
{
    protected $signature = 'export:kader {spreadsheet_id} {--tab=Form Responses 1}';
    protected $description = 'Export data Korwe/Kortw/Penggalang ke Google Sheets';

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
            $client->setScopes([Sheets::SPREADSHEETS]); // Perlu akses write
            $client->setAuthConfig($credentialsPath);
            $client->setAccessType('offline');

            $service = new Sheets($client);
            
            $this->info("Menyiapkan data dari database...");
            $kaders = Kader::orderBy('kecamatan')->orderBy('desa')->orderBy('nomor_rw')->get();
            
            $values = [
                ['Timestamp', 'KAB / KOTA', 'KECAMATAN', 'KEL / DESA', 'RW', 'NAMA LENGKAP SESUAI KTP', 'Nomor Whatsapp', 'PERAN', 'NOMOR KTP / NIK']
            ];

            foreach ($kaders as $kader) {
                $peranArray = [];
                if ($kader->is_korwe) $peranArray[] = 'KORWE';
                if ($kader->is_korte) $peranArray[] = 'KORTW';
                if ($kader->is_penggalang) $peranArray[] = 'PENGGALANG';
                
                $peran = implode(', ', $peranArray);
                
                $values[] = [
                    $kader->created_at ? $kader->created_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s'),
                    'KAB BEKASI',
                    $kader->kecamatan ?? '',
                    $kader->desa ?? '',
                    $kader->nomor_rw ?? '',
                    $kader->nama ?? '',
                    $kader->no_wa ?? '',
                    $peran,
                    $kader->nik ?? ''
                ];
            }

            $this->info("Mengekspor " . (count($values) - 1) . " data ke Google Sheets...");

            // Bersihkan data lama terlebih dahulu (opsional, tapi disarankan agar data ter-replace dengan sempurna)
            $clearRequest = new ClearValuesRequest();
            $service->spreadsheets_values->clear($spreadsheetId, "{$tabName}!A:I", $clearRequest);

            // Masukkan data baru
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
