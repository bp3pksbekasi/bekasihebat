<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BidangDpd;
use Illuminate\Database\Seeder;

class BidangDpdSeeder extends Seeder
{
    public function run(): void
    {
        $bidangs = [
            ['nama' => 'Advokasi', 'slug' => 'advokasi', 'icon' => 'scale', 'color' => '#ef4444', 'urutan' => 1],
            ['nama' => 'Relawan & Saksi', 'slug' => 'relawan-saksi', 'icon' => 'shield-check', 'color' => '#f97316', 'urutan' => 2],
            ['nama' => 'Polhukam', 'slug' => 'polhukam', 'icon' => 'shield', 'color' => '#dc2626', 'urutan' => 3],
            ['nama' => 'Ekonomi', 'slug' => 'ekonomi', 'icon' => 'coin', 'color' => '#16a34a', 'urutan' => 4],
            ['nama' => 'Pendidikan & Kesehatan', 'slug' => 'pendidikan-kesehatan', 'icon' => 'stethoscope', 'color' => '#2563eb', 'urutan' => 5],
            ['nama' => 'Keumatan & Dakwah', 'slug' => 'keumatan-dakwah', 'icon' => 'book', 'color' => '#8b5cf6', 'urutan' => 6],
            ['nama' => 'Perempuan & Keluarga', 'slug' => 'perempuan-keluarga', 'icon' => 'heart', 'color' => '#ec4899', 'urutan' => 7],
            ['nama' => 'Kepemudaan', 'slug' => 'kepemudaan', 'icon' => 'users', 'color' => '#0ea5e9', 'urutan' => 8],
            ['nama' => 'Tani, Nelayan & LH', 'slug' => 'tani-nelayan-lh', 'icon' => 'leaf', 'color' => '#22c55e', 'urutan' => 9],
            ['nama' => 'Ketenagakerjaan', 'slug' => 'ketenagakerjaan', 'icon' => 'briefcase', 'color' => '#14b8a6', 'urutan' => 10],
            ['nama' => 'Seni & Budaya', 'slug' => 'seni-budaya', 'icon' => 'palette', 'color' => '#a855f7', 'urutan' => 11],
            ['nama' => 'Humas', 'slug' => 'humas', 'icon' => 'speakerphone', 'color' => '#7c3aed', 'urutan' => 12],
            ['nama' => 'Komdigi', 'slug' => 'komdigi', 'icon' => 'device-mobile', 'color' => '#1d4ed8', 'urutan' => 13],
        ];

        $targetSlugs = collect($bidangs)->pluck('slug');

        foreach ($bidangs as $bidang) {
            BidangDpd::query()->updateOrCreate(
                ['slug' => $bidang['slug']],
                $bidang
            );
        }

        BidangDpd::query()
            ->whereNotIn('slug', $targetSlugs)
            ->doesntHave('programKerjas')
            ->doesntHave('agendas')
            ->delete();
    }
}
