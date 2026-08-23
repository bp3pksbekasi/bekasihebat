<?php

namespace App\Jobs;

use App\Models\KegiatanRw;
use App\Services\GoogleSheetsService;
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
    public int $backoff = 60; // retry setelah 60 detik jika gagal

    public function __construct(
        private readonly string $kegiatanRwId
    ) {}

    public function handle(GoogleSheetsService $sheets): void
    {
        $kegiatan = KegiatanRw::with(["targetWilayah", "creator"])->find($this->kegiatanRwId);

        if (!$kegiatan) {
            Log::warning("SyncKegiatanRwToGoogleSheets: KegiatanRw tidak ditemukan.", ["id" => $this->kegiatanRwId]);
            return;
        }

        $sheets->ensureHeader();

        $row = [
            (string) $kegiatan->id,
            now()->format("Y-m-d H:i:s"),
            $kegiatan->targetWilayah?->dapil ?? "-",
            $kegiatan->targetWilayah?->kecamatan ?? "-",
            $kegiatan->targetWilayah?->desa ?? "-",
            "RW " . ($kegiatan->nomor_rw ?? "-"),
            $kegiatan->jenis ?? "-",
            $kegiatan->tanggal_kegiatan ? \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format("d/m/Y H:i") : "-",
            $kegiatan->pelaksana ?? "-",
            (string) ($kegiatan->jumlah_warga ?? 0),
            $kegiatan->tokoh_ditemui ?? "-",
            $kegiatan->catatan ?? "-",
            $kegiatan->tindak_lanjut ?? "-",
            $kegiatan->creator?->name ?? "-",
        ];

        $sheets->upsert($row);

        Log::info("SyncKegiatanRwToGoogleSheets: Berhasil sinkronisasi.", ["id" => $this->kegiatanRwId]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncKegiatanRwToGoogleSheets: Gagal.", [
            "id"    => $this->kegiatanRwId,
            "error" => $exception->getMessage(),
        ]);
    }
}

