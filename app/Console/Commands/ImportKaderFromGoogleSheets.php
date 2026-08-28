<?php

namespace App\Console\Commands;

use App\Models\Kader;
use App\Models\TargetWilayah;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportKaderFromGoogleSheets extends Command
{
    protected $signature = 'import:kader {spreadsheet_id} {--tab=Form Responses 1}';
    protected $description = 'Import data Korwe/Kortw/Penggalang dari Google Sheets';

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
            $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
            $client->setAuthConfig($credentialsPath);
            $client->setAccessType('offline');

            $service = new Sheets($client);
            $range = "{$tabName}!A2:I";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                $this->info('Tidak ada data ditemukan di spreadsheet tersebut.');
                return;
            }

            $count = 0;
            foreach ($values as $row) {
                if (empty($row[5])) {
                    continue; // Lewati jika nama kosong
                }

                $kecamatan = strtoupper(trim($row[2] ?? ''));
                $desa = strtoupper(trim($row[3] ?? ''));
                
                // Normalisasi RW (001 -> 1)
                $rw = trim($row[4] ?? '');
                $rw = ltrim($rw, '0');
                if ($rw === '') {
                    $rw = '0';
                }

                $nama = trim($row[5] ?? '');
                $noWa = trim($row[6] ?? '');
                $peranStr = strtoupper(trim($row[7] ?? ''));
                $nik = trim($row[8] ?? '');

                // Format nomor WA agar seragam (misal: ganti 08 jadi 628)
                if (str_starts_with($noWa, '08')) {
                    $noWa = '628' . substr($noWa, 2);
                }
                $noWa = preg_replace('/[^0-9]/', '', $noWa);

                // Cari Dapil dari TargetWilayah
                $wilayah = TargetWilayah::where('kecamatan', $kecamatan)
                    ->where('desa', $desa)
                    ->first();
                
                $dapil = $wilayah ? $wilayah->dapil : null;

                $isKorwe = Str::contains($peranStr, 'KORWE');
                $isKorte = Str::contains($peranStr, 'KORTW') || Str::contains($peranStr, 'KORTE');
                $isPenggalang = Str::contains($peranStr, 'PENGGALANG');

                // Siapkan data
                $data = [
                    'nama' => $nama,
                    'no_wa' => $noWa,
                    'dapil' => $dapil,
                    'kecamatan' => $kecamatan,
                    'desa' => $desa,
                    'nomor_rw' => $rw,
                    'is_korwe' => $isKorwe,
                    'is_korte' => $isKorte,
                    'is_penggalang' => $isPenggalang,
                    'status' => 'aktif',
                    'target_wilayah_id' => $wilayah ? $wilayah->id : null,
                ];

                if (!empty($nik)) {
                    $data['nik'] = $nik;
                    $kader = Kader::where('nik', $nik)->first();
                } else {
                    $kader = Kader::where('nama', $nama)->where('no_wa', $noWa)->first();
                }

                if ($kader) {
                    $kader->update($data);
                } else {
                    Kader::create($data);
                }

                $count++;
            }

            $this->info("Berhasil memproses {$count} data kader dari Google Sheets.");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
