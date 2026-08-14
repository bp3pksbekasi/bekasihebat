<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfilRw;
use App\Models\DataRw;
use App\Models\TargetWilayah;

class UpdateKarangsatriaRtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $target = TargetWilayah::where('desa', 'KARANGSATRIA')->first();

        if (!$target) {
            $this->command->error("Target Wilayah Karangsatria tidak ditemukan!");
            return;
        }

        // 1. Perbaiki nama wilayah RW 1 dan RW 9 (di ProfilRw)
        // Dalam kode, RW di-pad dengan nol (misalnya 001, 009)
        ProfilRw::where('target_wilayah_id', $target->id)->where('nomor_rw', '001')->update(['nama_wilayah' => 'KP. KARANG CONGOK']);
        ProfilRw::where('target_wilayah_id', $target->id)->where('nomor_rw', '009')->update(['nama_wilayah' => 'KP. KARANG SAMBUNG']);
        $this->command->info("Nama wilayah RW 1 dan 9 diperbarui (ProfilRw).");

        // 2. Update jumlah RT (di DataRw)
        $rt_counts = [
            1 => 6, 2 => 4, 3 => 6, 4 => 9, 5 => 13, 6 => 7, 7 => 6, 8 => 13,
            9 => 2, 10 => 6, 11 => 6, 12 => 4, 13 => 4, 14 => 21, 15 => 5, 16 => 8,
            17 => 9, 18 => 9, 19 => 4, 20 => 7, 21 => 12, 22 => 6, 23 => 6, 24 => 7,
            25 => 3, 26 => 4, 27 => 6, 28 => 9, 29 => 4, 30 => 5, 31 => 9, 32 => 4,
        ];

        foreach ($rt_counts as $rw => $count) {
            $paddedRw = str_pad((string)$rw, 3, '0', STR_PAD_LEFT);
            DataRw::where('target_wilayah_id', $target->id)
                    ->where('nomor_rw', $paddedRw)
                    ->update(['jumlah_rt' => $count]);
        }
        
        $this->command->info("Jumlah RT untuk 32 RW berhasil diupdate (DataRw)!");
    }
}
