<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PublishPemiluData extends Command
{
    protected $signature = 'pemilu:publish';

    protected $description = 'Copy file CSV pemilu dan semua peta ke folder public untuk modul Bedah Dapil';

    public function handle(): int
    {
        $importSource = storage_path('app/private/import');
        $csvDest = public_path('data/pemilu');
        $petaDest = public_path('images/peta');
        $kabupatenMapSource = storage_path('peta/KABUPATEN BEKASI.png');
        $dapilMapSource = storage_path('peta/1. PETA PER DAPIL');
        $kecamatanMapSource = storage_path('peta/2. PETA PER KECAMATAN');
        $kecamatanDest = public_path('images/peta/kecamatan');

        File::ensureDirectoryExists($csvDest);
        File::ensureDirectoryExists($petaDest);
        File::ensureDirectoryExists($kecamatanDest);

        $csvCount = 0;
        if (File::isDirectory($importSource)) {
            foreach (File::allFiles($importSource) as $file) {
                if ($file->getExtension() !== 'csv') {
                    continue;
                }

                $destination = $csvDest . DIRECTORY_SEPARATOR . $file->getFilename();
                File::copy($file->getRealPath(), $destination);
                $csvCount += 1;
                $this->line(sprintf('Copied: %s -> %s', $file->getFilename(), $file->getFilename()));
            }
        }

        $dapilCount = 0;
        if (File::exists($kabupatenMapSource)) {
            File::copy($kabupatenMapSource, $petaDest . DIRECTORY_SEPARATOR . 'kabupaten-bekasi.png');
            $this->line('Copied: KABUPATEN BEKASI.png -> kabupaten-bekasi.png');
        }

        if (File::isDirectory($dapilMapSource)) {
            foreach (File::files($dapilMapSource) as $file) {
                if ($file->getExtension() !== 'png') {
                    continue;
                }

                $dapilNumber = $this->extractDapilNumber($file->getFilenameWithoutExtension());
                if ($dapilNumber === null) {
                    $this->warn(sprintf('Skipped (nomor dapil tidak dikenali): %s', $file->getFilename()));
                    continue;
                }

                $targetName = sprintf('dapil%d.png', $dapilNumber);
                File::copy($file->getRealPath(), $petaDest . DIRECTORY_SEPARATOR . $targetName);
                $dapilCount += 1;
                $this->line(sprintf('Copied: %s -> %s', $file->getFilename(), $targetName));
            }
        }

        $kecamatanCount = 0;
        if (File::isDirectory($kecamatanMapSource)) {
            foreach (File::files($kecamatanMapSource) as $file) {
                if ($file->getExtension() !== 'png') {
                    continue;
                }

                $normalizedName = $this->normalizeKecamatanFilename($file->getFilenameWithoutExtension());
                $targetName = $normalizedName . '.png';

                File::copy($file->getRealPath(), $kecamatanDest . DIRECTORY_SEPARATOR . $targetName);
                $kecamatanCount += 1;
                $this->line(sprintf('Copied: %s -> %s', $file->getFilename(), $targetName));
            }
        }

        $this->newLine();
        $this->info(sprintf(
            'Selesai. Total CSV: %d, total peta dapil: %d, total peta kecamatan: %d.',
            $csvCount,
            $dapilCount,
            $kecamatanCount
        ));

        return self::SUCCESS;
    }

    private function extractDapilNumber(string $filename): ?int
    {
        if (preg_match('/(?:dapil|dap)\D*([1-7])|(^|\D)([1-7])(?=\D|$)/i', $filename, $matches) !== 1) {
            return null;
        }

        $value = $matches[1] ?: ($matches[3] ?? null);

        return $value !== null ? (int) $value : null;
    }

    private function normalizeKecamatanFilename(string $filename): string
    {
        $normalized = preg_replace('/^\d+(?:\.\d+)*\.?\s*/', '', $filename) ?? $filename;
        $normalized = preg_replace('/^kecamatan\s+/i', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', trim($normalized)) ?? trim($normalized);

        return Str::slug($normalized, '-');
    }
}
