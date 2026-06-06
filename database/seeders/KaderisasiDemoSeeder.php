<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DataRw;
use App\Models\DeploymentLog;
use App\Models\Kader;
use App\Models\Pelatihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KaderisasiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->value('id');
        $villages = $this->availableVillages();

        if ($villages->isEmpty()) {
            $this->command?->warn('Seeder demo Kaderisasi dilewati karena data RW belum tersedia.');

            return;
        }

        $anchorVillage = $villages->firstWhere('total_rw', '>=', 4) ?? $villages->first();
        $secondVillage = $villages
            ->first(fn (object $item) => $item->target_wilayah_id !== $anchorVillage->target_wilayah_id && $item->kecamatan === $anchorVillage->kecamatan)
            ?? $villages->first(fn (object $item) => $item->target_wilayah_id !== $anchorVillage->target_wilayah_id)
            ?? $anchorVillage;
        $thirdVillage = $villages
            ->first(fn (object $item) => ! in_array($item->target_wilayah_id, [$anchorVillage->target_wilayah_id, $secondVillage->target_wilayah_id], true))
            ?? $secondVillage
            ?? $anchorVillage;

        $anchorRws = $this->rwRows((string) $anchorVillage->target_wilayah_id);
        $secondRws = $this->rwRows((string) $secondVillage->target_wilayah_id);
        $thirdRws = $this->rwRows((string) $thirdVillage->target_wilayah_id);

        $kaders = [
            'purna_korwe' => $this->upsertKader([
                'nama' => 'Demo Purna Korwe',
                'no_hp' => '081900000001',
                'no_wa' => '081900000001',
                'email' => 'demo.purna.korwe@kbh.local',
                'no_kta' => 'KTA-DEMO-001',
                'jenjang' => 'purna',
                'is_korwe' => true,
                'is_upa' => true,
                'jabatan_upa' => 'ketua',
                'bisa_deploy' => false,
                'keahlian' => ['organisasi', 'public_speaking'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 0), '01', $creatorId),
            'dewasa_saksi' => $this->upsertKader([
                'nama' => 'Demo Dewasa Saksi',
                'no_hp' => '081900000002',
                'no_wa' => '081900000002',
                'email' => 'demo.dewasa.saksi@kbh.local',
                'no_kta' => 'KTA-DEMO-002',
                'jenjang' => 'dewasa',
                'is_korte' => true,
                'is_saksi' => true,
                'bisa_deploy' => false,
                'keahlian' => ['organisasi', 'dakwah'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 0), '02', $creatorId),
            'madya_upa' => $this->upsertKader([
                'nama' => 'Demo Madya UPA',
                'no_hp' => '081900000003',
                'no_wa' => '081900000003',
                'email' => 'demo.madya.upa@kbh.local',
                'no_kta' => 'KTA-DEMO-003',
                'jenjang' => 'madya',
                'is_upa' => true,
                'jabatan_upa' => 'sekretaris',
                'keahlian' => ['kesehatan', 'organisasi'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 0), '03', $creatorId),
            'pelopor_penggalang' => $this->upsertKader([
                'nama' => 'Demo Pelopor Penggalang',
                'no_hp' => '081900000004',
                'no_wa' => '081900000004',
                'email' => 'demo.pelopor.penggalang@kbh.local',
                'no_kta' => 'KTA-DEMO-004',
                'jenjang' => 'pelopor',
                'is_penggalang' => true,
                'keahlian' => ['public_speaking', 'ekonomi'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 0), '04', $creatorId),
            'pendukung_lapangan' => $this->upsertKader([
                'nama' => 'Demo Pendukung Lapangan',
                'no_hp' => '081900000005',
                'no_wa' => '081900000005',
                'email' => 'demo.pendukung.lapangan@kbh.local',
                'no_kta' => 'KTA-DEMO-005',
                'jenjang' => 'pendukung',
                'keahlian' => ['medsos', 'teknologi'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 0), '05', $creatorId),
            'penggerak_rw2' => $this->upsertKader([
                'nama' => 'Demo Penggerak RW 2',
                'no_hp' => '081900000006',
                'no_wa' => '081900000006',
                'email' => 'demo.penggerak.rw2@kbh.local',
                'no_kta' => 'KTA-DEMO-006',
                'jenjang' => 'penggerak',
                'is_upa' => true,
                'jabatan_upa' => 'anggota',
                'keahlian' => ['pendidikan'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($anchorRws, 1), '01', $creatorId),
            'pendukung_second' => $this->upsertKader([
                'nama' => 'Demo Pendukung UPA',
                'no_hp' => '081900000007',
                'no_wa' => '081900000007',
                'email' => 'demo.pendukung.upa@kbh.local',
                'no_kta' => 'KTA-DEMO-007',
                'jenjang' => 'pendukung',
                'is_upa' => true,
                'jabatan_upa' => 'anggota',
                'keahlian' => ['kesehatan'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($secondRws, 0), '01', $creatorId),
            'pelopor_second' => $this->upsertKader([
                'nama' => 'Demo Pelopor Desa 2',
                'no_hp' => '081900000008',
                'no_wa' => '081900000008',
                'email' => 'demo.pelopor.desa2@kbh.local',
                'no_kta' => 'KTA-DEMO-008',
                'jenjang' => 'pelopor',
                'is_korte' => true,
                'keahlian' => ['organisasi'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($secondRws, 1), '02', $creatorId),
            'madya_third' => $this->upsertKader([
                'nama' => 'Demo Madya Desa 3',
                'no_hp' => '081900000009',
                'no_wa' => '081900000009',
                'email' => 'demo.madya.desa3@kbh.local',
                'no_kta' => 'KTA-DEMO-009',
                'jenjang' => 'madya',
                'is_penggalang' => true,
                'keahlian' => ['ekonomi', 'medsos'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($thirdRws, 0), '01', $creatorId),
            'penggerak_third' => $this->upsertKader([
                'nama' => 'Demo Penggerak Desa 3',
                'no_hp' => '081900000010',
                'no_wa' => '081900000010',
                'email' => 'demo.penggerak.desa3@kbh.local',
                'no_kta' => 'KTA-DEMO-010',
                'jenjang' => 'penggerak',
                'keahlian' => ['pendidikan', 'teknologi'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($thirdRws, 1), '02', $creatorId),
            'pendukung_nonaktif' => $this->upsertKader([
                'nama' => 'Demo Pendukung Nonaktif',
                'no_hp' => '081900000011',
                'no_wa' => '081900000011',
                'email' => 'demo.pendukung.nonaktif@kbh.local',
                'no_kta' => 'KTA-DEMO-011',
                'jenjang' => 'pendukung',
                'status' => 'nonaktif',
                'bisa_deploy' => false,
                'keahlian' => ['dakwah'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($thirdRws, 2), '03', $creatorId),
            'dewasa_deployed' => $this->upsertKader([
                'nama' => 'Demo Dewasa Deploy',
                'no_hp' => '081900000012',
                'no_wa' => '081900000012',
                'email' => 'demo.dewasa.deploy@kbh.local',
                'no_kta' => 'KTA-DEMO-012',
                'jenjang' => 'dewasa',
                'keahlian' => ['organisasi', 'public_speaking'],
                'catatan' => 'Seeder demo Kaderisasi',
            ], $this->rwAt($secondRws, 2), '01', $creatorId),
        ];

        $pelatihanPenggerak = $this->upsertPelatihan([
            'nama_pelatihan' => 'Demo Diklat Penggerak Kaderisasi',
            'jenjang_target' => 'penggerak',
            'jenis' => 'diklat',
            'tanggal_mulai' => now()->addDays(7)->toDateString(),
            'tanggal_selesai' => now()->addDays(8)->toDateString(),
            'lokasi' => 'Aula DPD PKS Kabupaten Bekasi',
            'dapil_terkait' => $anchorVillage->dapil,
            'instruktur' => 'Tim Kaderisasi DPD',
            'kapasitas' => 25,
            'status' => 'dijadwalkan',
            'materi' => 'Penguatan peran penggerak, public speaking, dan pemetaan wilayah.',
            'catatan' => 'Seeder demo Kaderisasi',
            'created_by' => $creatorId,
        ]);

        $pelatihanKorwe = $this->upsertPelatihan([
            'nama_pelatihan' => 'Demo Workshop Penguatan KORWE',
            'jenjang_target' => 'pelopor',
            'jenis' => 'workshop',
            'tanggal_mulai' => now()->subDays(20)->toDateString(),
            'tanggal_selesai' => now()->subDays(19)->toDateString(),
            'lokasi' => 'Markaz Kader Bekasi Timur',
            'dapil_terkait' => $anchorVillage->dapil,
            'instruktur' => 'Ust. Hidayat dan Tim Organisasi',
            'kapasitas' => 18,
            'status' => 'selesai',
            'materi' => 'Penguatan struktur KORWE, pembagian tugas, dan manajemen lapangan.',
            'catatan' => 'Seeder demo Kaderisasi',
            'created_by' => $creatorId,
        ]);

        $pelatihanMadya = $this->upsertPelatihan([
            'nama_pelatihan' => 'Demo Kajian Madya Lapangan',
            'jenjang_target' => 'madya',
            'jenis' => 'kajian_rutin',
            'tanggal_mulai' => now()->subDays(4)->toDateString(),
            'tanggal_selesai' => now()->subDays(4)->toDateString(),
            'lokasi' => 'Rumah Kader Dapil ' . $secondVillage->dapil,
            'dapil_terkait' => $secondVillage->dapil,
            'instruktur' => 'Bidang Kaderisasi',
            'kapasitas' => 15,
            'status' => 'berlangsung',
            'materi' => 'Pendalaman mentoring wilayah dan strategi rekrutmen kader.',
            'catatan' => 'Seeder demo Kaderisasi',
            'created_by' => $creatorId,
        ]);

        $this->syncPeserta($pelatihanPenggerak->id, $kaders['penggerak_rw2']->id, 'terdaftar', false);
        $this->syncPeserta($pelatihanPenggerak->id, $kaders['pendukung_second']->id, 'terdaftar', false);
        $this->syncPeserta($pelatihanPenggerak->id, $kaders['penggerak_third']->id, 'terdaftar', false);
        $this->syncPeserta($pelatihanPenggerak->id, $kaders['pelopor_second']->id, 'terdaftar', false);
        $this->syncPeserta($pelatihanPenggerak->id, $kaders['pelopor_penggalang']->id, 'terdaftar', false);

        $this->syncPeserta($pelatihanKorwe->id, $kaders['purna_korwe']->id, 'lulus', true);
        $this->syncPeserta($pelatihanKorwe->id, $kaders['dewasa_saksi']->id, 'lulus', true);
        $this->syncPeserta($pelatihanKorwe->id, $kaders['madya_upa']->id, 'hadir', false);
        $this->syncPeserta($pelatihanKorwe->id, $kaders['pendukung_lapangan']->id, 'hadir', false);

        $this->syncPeserta($pelatihanMadya->id, $kaders['madya_third']->id, 'lulus', true);
        $this->syncPeserta($pelatihanMadya->id, $kaders['dewasa_deployed']->id, 'hadir', false);
        $this->syncPeserta($pelatihanMadya->id, $kaders['pelopor_second']->id, 'hadir', false);

        $pelatihanPenggerak->update(['peserta_hadir' => 5]);
        $pelatihanKorwe->update(['peserta_hadir' => 4]);
        $pelatihanMadya->update(['peserta_hadir' => 3]);

        $this->upsertDeployment(
            $kaders['dewasa_deployed'],
            $this->rwAt($thirdRws, 0),
            $this->rwAt($secondRws, 2),
            'kebutuhan_wilayah',
            now()->subDays(6),
            $creatorId
        );

        $this->upsertDeployment(
            $kaders['pendukung_second'],
            $this->rwAt($anchorRws, 2),
            $this->rwAt($secondRws, 0),
            'penguatan_upa',
            now()->subDays(2),
            $creatorId
        );

        $this->command?->info('Seeder demo Kaderisasi selesai dijalankan.');
    }

    private function availableVillages(): Collection
    {
        return DataRw::query()
            ->select('target_wilayah_id', 'dapil', 'kecamatan', 'desa')
            ->selectRaw('COUNT(*) as total_rw')
            ->groupBy('target_wilayah_id', 'dapil', 'kecamatan', 'desa')
            ->orderByDesc('total_rw')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();
    }

    private function rwRows(string $targetWilayahId): Collection
    {
        return DataRw::query()
            ->where('target_wilayah_id', $targetWilayahId)
            ->orderBy('nomor_rw')
            ->get();
    }

    private function rwAt(Collection $rows, int $index): DataRw
    {
        return $rows->get($index) ?? $rows->last();
    }

    private function upsertKader(array $attributes, DataRw $rw, string $rt, $creatorId): Kader
    {
        return Kader::query()->updateOrCreate(
            ['no_wa' => $attributes['no_wa']],
            [
                'nama' => $attributes['nama'],
                'no_hp' => $attributes['no_hp'] ?? null,
                'no_wa' => $attributes['no_wa'],
                'email' => $attributes['email'] ?? null,
                'nik' => $attributes['nik'] ?? null,
                'no_kta' => $attributes['no_kta'] ?? null,
                'jenjang' => $attributes['jenjang'] ?? 'penggerak',
                'tanggal_jenjang' => Carbon::now()->subDays(rand(10, 120))->toDateString(),
                'dapil' => $rw->dapil,
                'kecamatan' => $rw->kecamatan,
                'desa' => $rw->desa,
                'nomor_rw' => $rw->nomor_rw,
                'nomor_rt' => $rt,
                'target_wilayah_id' => $rw->target_wilayah_id,
                'is_korwe' => $attributes['is_korwe'] ?? false,
                'is_korte' => $attributes['is_korte'] ?? false,
                'is_upa' => $attributes['is_upa'] ?? false,
                'jabatan_upa' => ($attributes['is_upa'] ?? false) ? ($attributes['jabatan_upa'] ?? 'anggota') : null,
                'is_penggalang' => $attributes['is_penggalang'] ?? false,
                'is_saksi' => $attributes['is_saksi'] ?? false,
                'keahlian' => $attributes['keahlian'] ?? [],
                'bisa_deploy' => $attributes['bisa_deploy'] ?? true,
                'status' => $attributes['status'] ?? 'aktif',
                'catatan' => $attributes['catatan'] ?? null,
                'created_by' => $creatorId,
            ]
        );
    }

    private function upsertPelatihan(array $attributes): Pelatihan
    {
        return Pelatihan::query()->updateOrCreate(
            ['nama_pelatihan' => $attributes['nama_pelatihan']],
            $attributes
        );
    }

    private function syncPeserta(string $pelatihanId, string $kaderId, string $status, bool $naikJenjang): void
    {
        $existingId = DB::table('pelatihan_pesertas')
            ->where('pelatihan_id', $pelatihanId)
            ->where('kader_id', $kaderId)
            ->value('id');

        if ($existingId) {
            DB::table('pelatihan_pesertas')
                ->where('id', $existingId)
                ->update([
                    'status' => $status,
                    'naik_jenjang' => $naikJenjang,
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('pelatihan_pesertas')->insert([
            'id' => (string) Str::uuid(),
            'pelatihan_id' => $pelatihanId,
            'kader_id' => $kaderId,
            'status' => $status,
            'naik_jenjang' => $naikJenjang,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function upsertDeployment(Kader $kader, DataRw $sourceRw, DataRw $targetRw, string $alasan, Carbon $tanggal, $creatorId): void
    {
        DeploymentLog::query()->updateOrCreate(
            [
                'kader_id' => $kader->id,
                'ke_dapil' => $targetRw->dapil,
                'ke_kecamatan' => $targetRw->kecamatan,
                'ke_desa' => $targetRw->desa,
                'ke_rw' => $targetRw->nomor_rw,
                'tanggal_deploy' => $tanggal->toDateString(),
            ],
            [
                'dari_dapil' => $sourceRw->dapil,
                'dari_kecamatan' => $sourceRw->kecamatan,
                'dari_desa' => $sourceRw->desa,
                'dari_rw' => $sourceRw->nomor_rw,
                'alasan' => $alasan,
                'status' => 'selesai',
                'catatan' => 'Seeder demo Kaderisasi',
                'created_by' => $creatorId,
            ]
        );
    }
}
