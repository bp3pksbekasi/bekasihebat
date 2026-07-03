<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Dapil123UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahs = DB::table('target_wilayahs')
            ->whereIn('dapil', ['BEKASI 1', 'BEKASI 2', 'BEKASI 3'])
            ->select('dapil', 'kecamatan', 'desa')
            ->distinct()
            ->get();

        $kecamatans = [];
        $desas = [];

        foreach ($wilayahs as $w) {
            $kecamatans[$w->kecamatan] = $w->dapil;
            $desas[] = $w;
        }

        // 1. Create DPC (Kecamatan)
        foreach ($kecamatans as $kecamatan => $dapil) {
            $slug = strtolower(str_replace(' ', '', $kecamatan));
            $email = "{$slug}@bekasihebat.com";
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = new User();
            }
            
            $user->name = "DPC " . $kecamatan;
            $user->email = $email;
            $user->password = Hash::make('123456');
            $user->role = 'pengurus_dpc';
            $user->org_level = 'dpc';
            $user->dapil = str_replace('BEKASI ', '', $dapil);
            $user->kecamatan = strtoupper($kecamatan);
            
            if (!$user->phone) {
                $user->phone = '081' . rand(10000000, 99999999);
            }
            $user->save();
        }

        // 2. Create DPRA (Desa)
        foreach ($desas as $w) {
            $slug = strtolower(str_replace(' ', '', $w->desa));
            $email = "{$slug}@bekasihebat.com";
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = new User();
            }
            
            $user->name = "DPRA " . $w->desa;
            $user->email = $email;
            $user->password = Hash::make('123456');
            $user->role = 'pengurus_dpra';
            $user->org_level = 'dpra';
            $user->dapil = str_replace('BEKASI ', '', $w->dapil);
            $user->kecamatan = strtoupper($w->kecamatan);
            $user->desa = strtoupper($w->desa);
            
            if (!$user->phone) {
                $user->phone = '081' . rand(10000000, 99999999);
            }
            $user->save();
        }
    }
}

