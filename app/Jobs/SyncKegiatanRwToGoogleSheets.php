<?php

namespace App\Jobs;

use App\Models\KegiatanRw;
use App\Services\GoogleSheetsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncKegiatanRwToGoogleSheets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly string $kegiatanRwId
    ) {}

    public function handle(GoogleSheetsService $sheets): void
    {
        try {
            echo "Memulai Job SyncKegiatanRwToGoogleSheets untuk ID: " . $this->kegiatanRwId . "\n";
            $kegiatan = KegiatanRw::with(["targetWilayah", "creator"])->find($this->kegiatanRwId);

            if (!$kegiatan) {
                $msg = "DATA TIDAK DITEMUKAN DI DATABASE (ID: {$this->kegiatanRwId}). Ini biasanya karena job berjalan lebih cepat dari database (race condition).";
                echo $msg . "\n";
                Log::warning("SyncKegiatanRwToGoogleSheets: " . $msg);
                return;
            }

            echo "Data ditemukan! Melakukan autentikasi ke Google Sheets...\n";
            $sheets->ensureHeader();
            
            echo "Header Google Sheet dipastikan ada. Mempersiapkan row...\n";

            $row = [
                now()->format("Y-m-d H:i:s"),
                $kegiatan->tanggal_kegiatan ? Carbon::parse($kegiatan->tanggal_kegiatan)->format("d/m/Y H:i") : "-",
                "Kabupaten Bekasi",
                $kegiatan->targetWilayah?->kecamatan ?? $kegiatan->kecamatan ?? "-",
                $kegiatan->targetWilayah?->desa ?? $kegiatan->desa ?? "-",
                $kegiatan->nomor_rw ?? "-",
                $kegiatan->dpr_ri_hadir ?? "TIDAK ADA",
                $kegiatan->dprd_prov_hadir ?? "TIDAK ADA",
                $kegiatan->dprd_kab_hadir ?? "TIDAK ADA",
                $kegiatan->tempat_kegiatan ?? "-",
                isset(KegiatanRw::JENIS_KEGIATAN[$kegiatan->jenis_kegiatan]) ? KegiatanRw::JENIS_KEGIATAN[$kegiatan->jenis_kegiatan]["label"] : ($kegiatan->jenis_kegiatan ?? "-"),
                $kegiatan->segmen ?? "-",
                $kegiatan->keterangan_tambahan ?? "-",
                "",
            ];

            echo "Mengirim data ke Google Sheets...\n";
            $sheets->upsert($row);
            
            echo "==== BERHASIL SINKRONISASI KE GOOGLE SHEETS! ====\n";
            Log::info("SyncKegiatanRwToGoogleSheets: Berhasil sinkron.", ["id" => $this->kegiatanRwId]);

        } catch (\Throwable $e) {
            $msg = "SyncKegiatanRwToGoogleSheets: Gagal. " . $e->getMessage() . " di baris " . $e->getLine();
            echo "\n============================================\n";
            echo "ERROR GOOGLE SHEETS:\n";
            echo $msg . "\n";
            echo "============================================\n";
            Log::error($msg);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncKegiatanRwToGoogleSheets: Gagal.", [
            "id"    => $this->kegiatanRwId,
            "error" => $exception->getMessage(),
        ]);
    }
}
