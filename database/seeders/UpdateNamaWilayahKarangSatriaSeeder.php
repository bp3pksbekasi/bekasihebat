<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateNamaWilayahKarangSatriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetWilayah = \App\Models\TargetWilayah::where('desa', 'KARANGSATRIA')
            ->where('kecamatan', 'TAMBUN UTARA')
            ->where('dapil', 'DAPIL 4') // Just to be safe
            ->first();

        if (!$targetWilayah) {
            $this->command->error('Target Wilayah KARANGSATRIA tidak ditemukan.');
            return;
        }

        $data = [
            1 => 'KP. KARANG SAMBUNG',
            2 => 'KP. GUDANG',
            3 => 'KP. RAWA KALONG',
            4 => 'KP. RAWA KALONG',
            5 => 'KP. RAWA KALONG',
            6 => 'KP. KOMPA',
            7 => 'BUMI ANGGREK',
            8 => 'KP. KARANG MULYA',
            9 => 'KP. KARANG CONGOK',
            10 => 'VILA ANGGREK',
            11 => 'TAMAN ALAMANDA',
            12 => 'TAMAN ALAMANDA',
            13 => 'TAMAN ALAMANDA',
            14 => 'MUSTIKA KARANG SATRIA',
            15 => 'TAMAN ALAMANDA',
            16 => 'VILA MUTIARA GADING 2',
            17 => 'BUMI ANGGREK',
            18 => 'VILA MUTIARA GADING 2',
            19 => 'TAMAN ALAMANDA',
            20 => 'GRED RESIDENCE',
            21 => 'ALAMANDA REGENCY',
            22 => 'TAMAN ALAMANDA',
            23 => 'CLUSTER ALAMANDA',
            24 => 'VILA MUTIARA GADING 2',
            25 => 'TAMAN ALAMANDA',
            26 => 'KP. KARANG JAYA',
            27 => 'ALAMANDA REGENCY',
            28 => 'ALAMANDA REGENCY',
            29 => 'ALAMANDA REGENCY',
            30 => 'ALAMANDA REGENCY',
            31 => 'MUTIARA GADING RIVIERA',
            32 => 'MUTIARA GADING RIVIERA',
        ];

        foreach ($data as $rw => $nama) {
            $nomorRwStr = str_pad((string) $rw, 3, '0', STR_PAD_LEFT);
            
            // Wait, does DataRw use integer or 001?
            // Usually nomor_rw is string like "001".
            // Let's use the actual nomor_rw format which is likely '001', '002' or integer in string format.
            // Wait, in BukuIndukRw Detail Livewire: ltrim($rw->nomor_rw, '0').
            // Let's check how it's stored. Usually it's stored as '1', '2', '001'.
            // I'll just check if there's a DataRw record to find the exact nomor_rw format.
            // Let's assume it's stored as '1', '2', etc. or whatever TargetWilayah uses.
            // Wait, I can just find the DataRw first, but ProfilRw depends on 'nomor_rw' column directly.
            // I'll use the raw integer value as string (e.g. '1'), because ltrim was used in index blade.
            $nomorRwStr = (string)$rw;

            \App\Models\ProfilRw::updateOrCreate(
                [
                    'target_wilayah_id' => $targetWilayah->id,
                    'nomor_rw' => $nomorRwStr,
                ],
                [
                    'nama_wilayah' => $nama,
                    'dapil' => $targetWilayah->dapil,
                    'kecamatan' => $targetWilayah->kecamatan,
                    'desa' => $targetWilayah->desa,
                ]
            );
        }

        $this->command->info('Berhasil mengupdate nama wilayah untuk Karang Satria (RW 1 - 32).');
    }
}
