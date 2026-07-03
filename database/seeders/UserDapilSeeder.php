<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserDapilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $email = "dapil{$i}@bekasihebat.com";
            
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = new User();
            }
            
            $user->name = "Pengurus Dapil " . $i;
            $user->email = $email;
            $user->password = Hash::make("dapil{$i}.123");
            $user->role = 'dapil'; // User::ROLE_DAPIL
            $user->org_level = 'dapil';
            $user->dapil = (string)$i;
            
            if (!$user->phone) {
                $user->phone = '081' . rand(10000000, 99999999);
            }
            
            $user->save();
        }
    }
}

