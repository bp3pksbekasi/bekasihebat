<?php

namespace Database\Seeders;

use App\Models\BidangDpd;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserBidangBaruSeeder extends Seeder
{
    public function run(): void
    {
        $bidangs = BidangDpd::where('is_active', true)->whereNotNull('singkatan')->get();
        $password = Hash::make('PKS123!');

        foreach ($bidangs as $bidang) {
            $singkatan = strtolower(trim($bidang->singkatan));
            if (empty($singkatan)) {
                continue;
            }

            $email = $singkatan . '@bekasihebat.com';
            
            // Nama user, misal: Pengurus BPKK
            $name = 'Pengurus ' . strtoupper($singkatan);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => User::ROLE_BIDANG,
                    'bidang_slug' => $bidang->slug,
                    'org_level' => User::ORG_LEVEL_DPD,
                    'status' => 'aktif',
                    'phone' => fake()->unique()->numerify('08##########'),
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([User::ROLE_BIDANG]);
        }
    }
}
