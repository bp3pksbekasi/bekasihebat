<?php

namespace App\Jobs;

use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncInfraToGoogleSheetsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $uniqueFor = 60; // Hanya boleh ada 1 job di antrian dalam 1 menit

    public function uniqueId()
    {
        return 'sync_infra_google_sheets';
    }

    public function handle(): void
    {
        $spreadsheetId = config('services.google_sheets.infra_spreadsheet_id');
        $tabName = config('services.google_sheets.infra_tab_name', 'Form Responses 1');

        if (empty($spreadsheetId)) {
            Log::warning('SyncInfraToGoogleSheetsJob: infra_spreadsheet_id belum diatur di .env');
            return;
        }

        $credentialsPath = base_path(config('services.google_sheets.credentials_path'));

        if (!file_exists($credentialsPath)) {
            Log::error("SyncInfraToGoogleSheetsJob: File credentials Google tidak ditemukan di: {$credentialsPath}");
            return;
        }

        try {
            $client = new Client();
            $client->setApplicationName('Bekasi Hebat Sync');
            $client->setScopes([Sheets::SPREADSHEETS]);
            $client->setAuthConfig($credentialsPath);
            $client->setAccessType('offline');

            $service = new Sheets($client);
            
            $values = [
                ['Timestamp', 'KAB / KOTA', 'KECAMATAN', 'KEL / DESA', 'RW', 'NAMA LENGKAP SESUAI KTP', 'Nomor Whatsapp', 'PERAN', 'NOMOR KTP / NIK']
            ];

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
                    ''
                ];
            }

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

            $penggalangs = PenggalangSuara::get();
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

            $clearRequest = new ClearValuesRequest();
            $service->spreadsheets_values->clear($spreadsheetId, "{$tabName}!A:I", $clearRequest);

            $body = new ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $service->spreadsheets_values->update($spreadsheetId, "{$tabName}!A1:I", $body, $params);

            Log::info("SyncInfraToGoogleSheetsJob berhasil dieksekusi. " . (count($values) - 1) . " baris tersinkron.");
            
        } catch (\Exception $e) {
            Log::error('SyncInfraToGoogleSheetsJob Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
