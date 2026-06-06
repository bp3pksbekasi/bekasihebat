<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AnggotaDewan;
use App\Models\Aspirasi;
use App\Models\KontenMedsos;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnggotaDewanSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->where('email', 'admin1@bekasihebat.id')->value('id')
            ?? User::query()->value('id');

        KontenMedsos::query()->delete();
        AnggotaDewan::query()->delete();

        $dewanData = [
            [
                'nama' => 'H. Yusuf Fathullah Fajri, A.Md.',
                'jabatan' => 'Anggota DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 1',
                'no_hp' => null,
                'suara_2024' => 8873,
                'status_petahana' => false,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => null,
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Setu, Cikarang Pusat, Serang Baru, Cibarusah, Bojongmangu',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'Budi Muhammad Mustafa',
                'jabatan' => 'Wakil Ketua DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 2',
                'no_hp' => null,
                'suara_2024' => 8827,
                'status_petahana' => true,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => 'Wakil Ketua DPRD',
                'jabatan_partai' => 'Ketua DPD PKS Kab. Bekasi',
                'wilayah_dapil' => 'Cibitung, Cikarang Barat',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'H. Nuryasin Suparmin, Lc.',
                'jabatan' => 'Anggota DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 3',
                'no_hp' => null,
                'suara_2024' => 10879,
                'status_petahana' => true,
                'jabatan_fraksi' => 'Ketua Fraksi PKS',
                'jabatan_dprd' => null,
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Tambun Selatan',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'Hj. Puji Lestari, A.Md.',
                'jabatan' => 'Anggota DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 4',
                'no_hp' => null,
                'suara_2024' => 8396,
                'status_petahana' => false,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => null,
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Sukawangi, Tambelang, Tambun Utara, Sukatani',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'Dra. Hj. Ani Rukmini, M.I.Kom.',
                'jabatan' => 'Ketua Komisi II DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 5',
                'no_hp' => null,
                'suara_2024' => 9901,
                'status_petahana' => true,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => 'Ketua Komisi II',
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Tarumajaya, Babelan, Muaragembong',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'Ade Jenah Fajarwati, S.Pd.I.',
                'jabatan' => 'Anggota DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 6',
                'no_hp' => null,
                'suara_2024' => 5826,
                'status_petahana' => false,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => null,
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Karang Bahagia, Kedung Waringin, Pebayuran, Sukakarya, Cabangbungin',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
            [
                'nama' => 'Saeful Islam, S.H.',
                'jabatan' => 'Anggota DPRD Kab. Bekasi',
                'dapil' => 'BEKASI 7',
                'no_hp' => null,
                'suara_2024' => 6661,
                'status_petahana' => false,
                'jabatan_fraksi' => null,
                'jabatan_dprd' => null,
                'jabatan_partai' => null,
                'wilayah_dapil' => 'Cikarang Utara, Cikarang Timur, Cikarang Selatan',
                'instagram' => null,
                'ig_followers' => 0,
                'tiktok' => null,
                'tt_followers' => 0,
                'youtube' => null,
                'yt_subscribers' => 0,
                'twitter' => null,
                'tw_followers' => 0,
                'facebook' => null,
                'fb_followers' => 0,
            ],
        ];

        foreach ($dewanData as $data) {
            AnggotaDewan::query()->updateOrCreate(
                ['dapil' => $data['dapil']],
                [
                    'nama' => $data['nama'],
                    'jabatan' => $data['jabatan'],
                    'dapil' => $data['dapil'],
                    'suara_2024' => $data['suara_2024'],
                    'status_petahana' => $data['status_petahana'],
                    'jabatan_fraksi' => $data['jabatan_fraksi'],
                    'jabatan_dprd' => $data['jabatan_dprd'],
                    'jabatan_partai' => $data['jabatan_partai'],
                    'wilayah_dapil' => $data['wilayah_dapil'],
                    'no_hp' => $data['no_hp'],
                    'instagram' => $data['instagram'],
                    'ig_followers' => $data['ig_followers'],
                    'tiktok' => $data['tiktok'],
                    'tt_followers' => $data['tt_followers'],
                    'youtube' => $data['youtube'],
                    'yt_subscribers' => $data['yt_subscribers'],
                    'twitter' => $data['twitter'],
                    'tw_followers' => $data['tw_followers'],
                    'facebook' => $data['facebook'],
                    'fb_followers' => $data['fb_followers'],
                    'skor_popularitas' => 0,
                    'target_popularitas' => 70,
                    'status' => 'aktif',
                    'created_by' => $creatorId,
                ]
            );
        }

        $this->restoreAspirasiAssignments();
    }

    private function restoreAspirasiAssignments(): void
    {
        $dewanByDapil = AnggotaDewan::query()
            ->aktif()
            ->get()
            ->keyBy('dapil');

        Aspirasi::query()
            ->where('status', '!=', 'diterima')
            ->get()
            ->each(function (Aspirasi $aspirasi) use ($dewanByDapil): void {
                $dewan = $dewanByDapil->get($aspirasi->dapil);

                if (! $dewan) {
                    return;
                }

                $aspirasi->forceFill([
                    'assigned_dewan_id' => $dewan->id,
                    'assigned_at' => $aspirasi->assigned_at ?? $aspirasi->created_at ?? now(),
                ])->saveQuietly();
            });
    }
}
