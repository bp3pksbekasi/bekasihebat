<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DataRw;
use App\Models\Kader;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DummyAktivasiNiaSeeder extends Seeder
{
    /**
     * @var array<int, array<int, string>>
     */
    private array $nameMap = [
        1 => ['Ahmad Fauzan Pratama', 'Siti Nur Aisyah', 'Rizki Maulana Putra'],
        2 => ['Dewi Lestari Rahmah', 'Muhammad Farhan Akbar', 'Nabila Putri Ramadhani'],
        3 => ['Fajar Hidayat Saputra', 'Aulia Rahma Salsabila', 'Yoga Prasetyo'],
        4 => ['Intan Permata Sari', 'Bagas Dwi Kurniawan', 'Nadya Khairunnisa'],
        5 => ['Reza Alif Ramadhan', 'Meylani Safitri', 'Ilham Nur Hakim'],
        6 => ['Tasya Maharani', 'Dimas Ardiansyah', 'Syifa Nurhaliza'],
        7 => ['Rafi Haikal Fadillah', 'Putri Anindya Larasati', 'Andika Saputra'],
    ];

    public function run(): void
    {
        $creatorId = User::query()->value('id');
        $bidangSlugs = array_keys(User::BIDANG_OPTIONS);
        $rowsByDapil = $this->rwSamplesByDapil();

        if ($rowsByDapil->isEmpty()) {
            $this->command?->warn('Seeder dummy aktivasi NIA dilewati karena data RW belum tersedia.');

            return;
        }

        $inserted = 0;

        foreach ($rowsByDapil as $dapil => $rows) {
            $dapilNumber = $this->extractDapilNumber((string) $dapil);
            $names = $this->nameMap[$dapilNumber] ?? [
                "Dummy Aktivasi {$dapilNumber}-1",
                "Dummy Aktivasi {$dapilNumber}-2",
                "Dummy Aktivasi {$dapilNumber}-3",
            ];

            foreach ($rows->values() as $index => $row) {
                $nia = sprintf('32.16.%02d.%02d.%04d', $dapilNumber, $index + 1, ($dapilNumber * 100) + $index + 1);
                $phone = sprintf('081720%02d%04d', $dapilNumber, $index + 1);
                $isBidang = $index === 0;

                Kader::query()->updateOrCreate(
                    ['nia' => $nia],
                    [
                        'nama' => $names[$index] ?? "Dummy Aktivasi {$dapilNumber}-".($index + 1),
                        'no_hp' => $phone,
                        'no_wa' => $phone,
                        'email' => null,
                        'nik' => sprintf('3216%010d', ($dapilNumber * 1000) + $index + 1),
                        'no_kta' => sprintf('KTA-DUMMY-%02d-%02d', $dapilNumber, $index + 1),
                        'nia' => $nia,
                        'bidang_slug' => $isBidang ? $bidangSlugs[($dapilNumber - 1) % count($bidangSlugs)] : null,
                        'jenjang' => ['penggerak', 'pendukung', 'pelopor'][$index] ?? 'penggerak',
                        'tanggal_jenjang' => now()->subDays(($dapilNumber * 5) + ($index * 3))->toDateString(),
                        'dapil' => $row->dapil,
                        'kecamatan' => $row->kecamatan,
                        'desa' => $row->desa,
                        'nomor_rw' => $row->nomor_rw,
                        'nomor_rt' => str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                        'target_wilayah_id' => $row->target_wilayah_id,
                        'is_korwe' => $index === 0,
                        'is_korte' => $index === 1,
                        'is_upa' => $index === 2,
                        'jabatan_upa' => $index === 2 ? 'anggota' : null,
                        'is_penggalang' => $index === 1,
                        'is_saksi' => $index === 2,
                        'keahlian' => $this->skillsFor($index),
                        'bisa_deploy' => true,
                        'status' => 'aktif',
                        'is_activated' => false,
                        'catatan' => 'Dummy aktivasi NIA untuk testing verifikasi akun per dapil.',
                        'created_by' => $creatorId,
                    ]
                );

                $inserted++;
            }
        }

        $this->command?->info("Seeder dummy aktivasi NIA selesai. {$inserted} data kader dummy siap diuji.");
    }

    /**
     * @return Collection<string, Collection<int, DataRw>>
     */
    private function rwSamplesByDapil(): Collection
    {
        return DataRw::query()
            ->select('target_wilayah_id', 'dapil', 'kecamatan', 'desa', 'nomor_rw')
            ->whereNotNull('dapil')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->orderBy('nomor_rw')
            ->get()
            ->groupBy('dapil')
            ->map(fn (Collection $rows) => $rows->take(3)->values());
    }

    /**
     * @return array<int, string>
     */
    private function skillsFor(int $index): array
    {
        return match ($index) {
            0 => ['organisasi', 'public_speaking'],
            1 => ['medsos', 'dakwah'],
            default => ['pendidikan', 'teknologi'],
        };
    }

    private function extractDapilNumber(string $dapil): int
    {
        if (preg_match('/(\d+)/', $dapil, $matches) === 1) {
            return max(1, (int) $matches[1]);
        }

        return 1;
    }
}
