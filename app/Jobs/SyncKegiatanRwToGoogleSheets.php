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
        $kegiatan = KegiatanRw::with(["targetWilayah", "creator"])->find($this->kegiatanRwId);

        if (!$kegiatan) {
            Log::warning("SyncKegiatanRwToGoogleSheets: KegiatanRw tidak ditemukan.", ["id" => $this->kegiatanRwId]);
            return;
        }

        $sheets->ensureHeader();

        // Cocokkan urutan kolom dengan header Google Sheet:
        // Timestamp | WAKTU KEGIATAN | KAB/KOTA | KECAMATAN | KEL/DESA | RW |
        // DPR RI | DPRD PROV | DPRD KAB | TEMPAT KEGIATAN | JENIS KEGIATAN | SEGMEN |
        // KETERANGAN TAMBAHAN
        $row = [
            // Kolom A: Timestamp (waktu data masuk ke sheet)
            now()->format("Y-m-d H:i:s"),

            // Kolom B: WAKTU KEGIATAN
            $kegiatan->tanggal_kegiatan
                ? Carbon::parse($kegiatan->tanggal_kegiatan)->format("d/m/Y H:i")
                : "-",

            // Kolom C: KAB / KOTA
            "Kabupaten Bekasi",

            // Kolom D: KECAMATAN
            $kegiatan->targetWilayah?->kecamatan
                ?? $kegiatan->kecamatan
                ?? "-",

            // Kolom E: KEL / DESA
            $kegiatan->targetWilayah?->desa
                ?? $kegiatan->desa
                ?? "-",

            // Kolom F: RW
            $kegiatan->nomor_rw ?? "-",

            // Kolom G: ANGGOTA DPR RI YANG TERLIBAT / HADIR
            $kegiatan->dpr_ri_hadir ?? "TIDAK ADA",

            // Kolom H: ANGGOTA DPRD PROVINSI JAWA BARAT YANG TERLIBAT / HADIR
            $kegiatan->dprd_prov_hadir ?? "TIDAK ADA",

            // Kolom I: ANGGOTA DPRD KAB / KOTA YANG TERLIBAT / HADIR
            $kegiatan->dprd_kab_hadir ?? "TIDAK ADA",

            // Kolom J: TEMPAT KEGIATAN
            $kegiatan->tempat_kegiatan ?? "-",

            // Kolom K: JENIS KEGIATAN
            isset(KegiatanRw::JENIS_KEGIATAN[$kegiatan->jenis_kegiatan])
                ? KegiatanRw::JENIS_KEGIATAN[$kegiatan->jenis_kegiatan]["label"]
                : ($kegiatan->jenis_kegiatan ?? "-"),

            // Kolom L: SEGMEN
            $kegiatan->segmen ?? "-",

            // Kolom M: KETERANGAN TAMBAHAN
            $kegiatan->keterangan_tambahan ?? "-",

            // Kolom N: UPLOAD FOTO KEGIATAN (dikosongkan sesuai permintaan)
            "",
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
