<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Dapil4DesaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villages = [
            'SUKAWANGI' => ['SUKARINGIN', 'SUKATENANG', 'SUKAKERTA', 'SUKAWANGI', 'SUKABUDI', 'SUKADAYA', 'SUKAMEKAR'],
            'TAMBELANG' => ['SUKABAKTI', 'SUKAMAJU', 'SUKAMANTRI', 'SUKARAHAYU', 'SUKARAJA', 'SUKARAPIH', 'SUKAWIJAYA'],
            'TAMBUN UTARA' => ['SRIAMUR', 'SRIJAYA', 'SRIMAHI', 'SRIMUKTI', 'SATRIAMEKAR', 'JEJALENJAYA', 'SATRIAJAYA'], // Excluded KARANGSATRIA
            'SUKATANI' => ['BANJARSARI', 'SUKAASIH', 'SUKADARMA', 'SUKAHURIP', 'SUKAMANAH', 'SUKAMULYA', 'SUKARUKUN'],
        ];

        foreach ($villages as $kecamatan => $desas) {
            foreach ($desas as $desa) {
                $slug = strtolower(str_replace(' ', '', $desa));
                $email = "{$slug}@bekasihebat.com";
                
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = new User();
                }
                
                $user->name = "DPRA " . $desa;
                $user->email = $email;
                $user->password = Hash::make('123456');
                $user->role = 'pengurus_dpra'; // User::ROLE_DPRA
                $user->org_level = 'dpra';
                $user->dapil = '4';
                $user->kecamatan = strtoupper($kecamatan);
                $user->desa = strtoupper($desa);
                
                if (!$user->phone) {
                    $user->phone = '081' . rand(10000000, 99999999);
                }
                
                $user->save();
            }
        }
    }
}

