<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Dapil4KecamatanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatans = ['Sukatani', 'Sukawangi', 'Tambelang'];

        foreach ($kecamatans as $kec) {
            $slug = strtolower(str_replace(' ', '', $kec));
            $email = "{$slug}@bekasihebat.com";
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = new User();
            }
            
            $user->name = "DPC " . $kec;
            $user->email = $email;
            $user->password = Hash::make('123456');
            $user->role = 'pengurus_dpc';
            $user->org_level = 'dpc';
            $user->dapil = '4';
            $user->kecamatan = strtoupper($kec);
            
            if (!$user->phone) {
                $user->phone = '081' . rand(10000000, 99999999);
            }
            
            $user->save();
        }
    }
}

