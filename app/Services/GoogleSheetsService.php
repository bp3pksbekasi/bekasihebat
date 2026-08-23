<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private ?Sheets $sheetsService = null;
    private string $spreadsheetId;
    private string $tabName;

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

        $credentialsPath = base_path(config("services.google_sheets.credentials_path"));

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
                    "ID", "Tanggal Input", "Dapil", "Kecamatan", "Desa",
                    "Nomor RW", "Jenis Kegiatan", "Tanggal Kegiatan",
                    "Pelaksana", "Jumlah Warga", "Tokoh Ditemui",
                    "Catatan", "Tindak Lanjut", "Dicatat Oleh",
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
