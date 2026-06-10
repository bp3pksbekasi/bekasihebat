<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TargetWilayah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DapilLoginUsersSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('dapil');

        foreach (range(1, 7) as $number) {
            $dapil = 'BEKASI ' . $number;
            $target = TargetWilayah::query()
                ->where('dapil', $dapil)
                ->orderBy('kecamatan')
                ->orderBy('desa')
                ->first();

            if (! $target) {
                $this->command?->warn("Lewati {$dapil}: data target wilayah belum tersedia.");
                continue;
            }

            $kelurahanCode = $this->resolveKelurahanCode(
                kecamatan: (string) $target->kecamatan,
                desa: (string) $target->desa,
            );

            if ($kelurahanCode === null) {
                $this->command?->warn("Lewati {$dapil}: kelurahan untuk {$target->kecamatan} / {$target->desa} tidak ditemukan.");
                continue;
            }

            $email = sprintf('dapil%d@kbh.local', $number);
            $password = 'Dapil123!';

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'User ' . $dapil,
                    'phone' => sprintf('081900000%03d', $number),
                    'password' => Hash::make($password),
                    'member_number' => sprintf('DAPIL-%03d', $number),
                    'role' => 'dapil',
                    'gender' => 'L',
                    'kelurahan_code' => $kelurahanCode,
                    'address' => sprintf('Akun dashboard %s, RT 001/RW 001', $target->desa),
                    'profile_completed_at' => now(),
                ]
            );

            $user->syncRoles(['dapil']);

            $this->command?->info(sprintf(
                '%s dibuat: %s / %s (%s - %s)',
                $dapil,
                $email,
                $password,
                $target->kecamatan,
                $target->desa
            ));
        }
    }

    private function resolveKelurahanCode(string $kecamatan, string $desa): ?string
    {
        return DB::table('indonesia_villages as village')
            ->join('indonesia_districts as district', DB::raw('LEFT(village.code, 6)'), '=', 'district.code')
            ->whereRaw('UPPER(district.name) = ?', [mb_strtoupper($kecamatan)])
            ->whereRaw('UPPER(village.name) = ?', [mb_strtoupper($desa)])
            ->value('village.code');
    }
}
