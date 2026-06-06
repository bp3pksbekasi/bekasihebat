<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;

class PoliticalPartiesSeeder extends Seeder
{
    public function run(): void
    {
        $parties = [
            ['nomor_urut' => 1, 'code' => 'PKB', 'name' => 'PKB', 'full_name' => 'Partai Kebangkitan Bangsa', 'color_hex' => '#008000', 'is_tracked' => true],
            ['nomor_urut' => 2, 'code' => 'GERINDRA', 'name' => 'Gerindra', 'full_name' => 'Partai Gerakan Indonesia Raya', 'color_hex' => '#C8102E', 'is_tracked' => true],
            ['nomor_urut' => 3, 'code' => 'PDIP', 'name' => 'PDI-P', 'full_name' => 'Partai Demokrasi Indonesia Perjuangan', 'color_hex' => '#D72027', 'is_tracked' => true],
            ['nomor_urut' => 4, 'code' => 'GOLKAR', 'name' => 'Golkar', 'full_name' => 'Partai Golongan Karya', 'color_hex' => '#FFD700', 'is_tracked' => true],
            ['nomor_urut' => 5, 'code' => 'NASDEM', 'name' => 'NasDem', 'full_name' => 'Partai NasDem', 'color_hex' => '#005EA8', 'is_tracked' => true],
            ['nomor_urut' => 6, 'code' => 'BURUH', 'name' => 'Buruh', 'full_name' => 'Partai Buruh', 'color_hex' => '#FF6B00', 'is_tracked' => false],
            ['nomor_urut' => 7, 'code' => 'GELORA', 'name' => 'Gelora', 'full_name' => 'Partai Gelombang Rakyat Indonesia', 'color_hex' => '#1B4F8B', 'is_tracked' => false],
            ['nomor_urut' => 8, 'code' => 'PKS', 'name' => 'PKS', 'full_name' => 'Partai Keadilan Sejahtera', 'color_hex' => '#000000', 'is_tracked' => true],
            ['nomor_urut' => 9, 'code' => 'PKN', 'name' => 'PKN', 'full_name' => 'Partai Kebangkitan Nusantara', 'color_hex' => '#1E3A8A', 'is_tracked' => false],
            ['nomor_urut' => 10, 'code' => 'HANURA', 'name' => 'Hanura', 'full_name' => 'Partai Hati Nurani Rakyat', 'color_hex' => '#F58220', 'is_tracked' => false],
            ['nomor_urut' => 11, 'code' => 'GARUDA', 'name' => 'Garuda', 'full_name' => 'Partai Garda Republik Indonesia', 'color_hex' => '#FBBF24', 'is_tracked' => false],
            ['nomor_urut' => 12, 'code' => 'DEMOKRAT', 'name' => 'Demokrat', 'full_name' => 'Partai Demokrat', 'color_hex' => '#003E7E', 'is_tracked' => true],
            ['nomor_urut' => 13, 'code' => 'PSI', 'name' => 'PSI', 'full_name' => 'Partai Solidaritas Indonesia', 'color_hex' => '#D80027', 'is_tracked' => true],
            ['nomor_urut' => 14, 'code' => 'PERINDO', 'name' => 'Perindo', 'full_name' => 'Partai Persatuan Indonesia', 'color_hex' => '#0066B3', 'is_tracked' => false],
            ['nomor_urut' => 15, 'code' => 'PPP', 'name' => 'PPP', 'full_name' => 'Partai Persatuan Pembangunan', 'color_hex' => '#0F7B0F', 'is_tracked' => true],
            ['nomor_urut' => 16, 'code' => 'PARTAI_UMMAT', 'name' => 'Ummat', 'full_name' => 'Partai Ummat', 'color_hex' => '#1F2937', 'is_tracked' => false],
            ['nomor_urut' => 17, 'code' => 'PAN', 'name' => 'PAN', 'full_name' => 'Partai Amanat Nasional', 'color_hex' => '#1B5FAA', 'is_tracked' => true],
        ];

        foreach ($parties as $party) {
            PoliticalParty::updateOrCreate(
                ['nomor_urut' => $party['nomor_urut']],
                $party
            );
        }

        $this->command->info('Seeded ' . count($parties) . ' political parties. 10 partai tracked: PKB, Gerindra, PDIP, Golkar, NasDem, PKS, Demokrat, PSI, PPP, PAN');
    }
}
