<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private ?Sheets $sheetsService = null;
    private ?string $spreadsheetId;
    private ?string $tabName;

    public function __construct()
    {
        $this->spreadsheetId = config("services.google_sheets.spreadsheet_id");
        $this->tabName       = config("services.google_sheets.tab_name", "Data Sisir RW");
    }

    private function getService(): Sheets
    {
        if ($this->sheetsService !== null) {
            return $this->sheetsService;
        }

        if (empty($this->spreadsheetId)) {
            throw new \Exception("Google Sheets API: SPREADSHEET_ID belum diatur di .env");
        }

        $credentialsPath = base_path(config("services.google_sheets.credentials_path"));
        if (!file_exists($credentialsPath)) {
            throw new \Exception("Google Sheets API: File credential JSON tidak ditemukan di " . $credentialsPath);
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([Sheets::SPREADSHEETS]);

        $this->sheetsService = new Sheets($client);

        return $this->sheetsService;
    }

    public function ensureHeader(): void
    {
        try {
            $service  = $this->getService();
            $range    = "{$this->tabName}!A1:N1";
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $values   = $response->getValues();

            if (empty($values)) {
                $headers = [[
                    "Timestamp", "WAKTU KEGIATAN", "KAB / KOTA", "KECAMATAN", "KEL / DESA", "RW", "ANGGOTA DPR RI YANG TERLIBAT / HADIR", "ANGGOTA DPRD PROVINSI JAWA BARAT YANG TERLIBAT / HADIR", "ANGGOTA DPRD KAB / KOTA YANG TERLIBAT / HADIR", "TEMPAT KEGIATAN", "JENIS KEGIATAN", "SEGMEN", "KETERANGAN TAMBAHAN", "UPLOAD FOTO KEGIATAN",
                ]];
                $body = new ValueRange(["values" => $headers]);
                $service->spreadsheets_values->update(
                    $this->spreadsheetId,
                    "{$this->tabName}!A1",
                    $body,
                    ["valueInputOption" => "RAW"]
                );
            }
        } catch (\Exception $e) {
            Log::warning("GoogleSheetsService: Gagal memastikan header.", ["error" => $e->getMessage()]);
        }
    }

    private function findRowById(Sheets $service, string $id): ?int
    {
        $response = $service->spreadsheets_values->get(
            $this->spreadsheetId,
            "{$this->tabName}!A:A"
        );
        $values = $response->getValues() ?? [];
        foreach ($values as $rowIndex => $row) {
            if (($row[0] ?? "") === $id) {
                return $rowIndex + 1;
            }
        }
        return null;
    }

    public function upsert(array $rowData): void
    {
        $service = $this->getService();
        $id      = (string) ($rowData[0] ?? "");
        $body    = new ValueRange(["values" => [$rowData]]);

        $existingRow = $this->findRowById($service, $id);

        if ($existingRow !== null) {
            $service->spreadsheets_values->update(
                $this->spreadsheetId,
                "{$this->tabName}!A{$existingRow}",
                $body,
                ["valueInputOption" => "USER_ENTERED"]
            );
        } else {
            $service->spreadsheets_values->append(
                $this->spreadsheetId,
                "{$this->tabName}!A1",
                $body,
                ["valueInputOption" => "USER_ENTERED", "insertDataOption" => "INSERT_ROWS"]
            );
        }
    }
}


