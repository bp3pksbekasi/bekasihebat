<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Kader;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use App\Models\UpaRwMember;
use Illuminate\Console\Command;

class ImportKadersFromInfra extends Command
{
    protected $signature = 'import:kaders-from-infra';

    protected $description = 'Sinkronkan data kader dari KORWE, KORTE, UPA RW, dan Penggalang Suara';

    public function handle(): int
    {
        $counts = [
            'korwe' => 0,
            'korte' => 0,
            'upa' => 0,
            'penggalang' => 0,
        ];

        Korwe::query()
            ->with('targetWilayah')
            ->where('status', 'terbentuk')
            ->chunk(100, function ($items) use (&$counts): void {
                foreach ($items as $item) {
                    $counts['korwe'] += $this->syncCandidate([
                        'nama' => (string) $item->nama_koordinator,
                        'no_hp' => $item->no_hp,
                        'no_wa' => $item->no_hp,
                        'dapil' => $item->targetWilayah?->dapil,
                        'kecamatan' => $item->targetWilayah?->kecamatan,
                        'desa' => $item->targetWilayah?->desa,
                        'nomor_rw' => $item->nomor_rw,
                        'target_wilayah_id' => $item->target_wilayah_id,
                        'flags' => ['is_korwe' => true],
                    ]);
                }
            });

        Korte::query()
            ->with('targetWilayah')
            ->where('status', 'terbentuk')
            ->chunk(200, function ($items) use (&$counts): void {
                foreach ($items as $item) {
                    $counts['korte'] += $this->syncCandidate([
                        'nama' => (string) $item->nama_koordinator,
                        'no_hp' => $item->no_hp,
                        'no_wa' => $item->no_hp,
                        'dapil' => $item->targetWilayah?->dapil,
                        'kecamatan' => $item->targetWilayah?->kecamatan,
                        'desa' => $item->targetWilayah?->desa,
                        'nomor_rw' => $item->nomor_rw,
                        'nomor_rt' => $item->nomor_rt,
                        'target_wilayah_id' => $item->target_wilayah_id,
                        'flags' => [
                            'is_korte' => true,
                            'is_saksi' => (bool) $item->is_saksi_tps,
                        ],
                    ]);
                }
            });

        UpaRwMember::query()
            ->where('status', 'aktif')
            ->chunk(200, function ($items) use (&$counts): void {
                foreach ($items as $item) {
                    $counts['upa'] += $this->syncCandidate([
                        'nama' => (string) $item->nama,
                        'no_hp' => $item->no_hp,
                        'no_wa' => $item->no_hp,
                        'dapil' => $item->dapil,
                        'kecamatan' => $item->kecamatan,
                        'desa' => $item->desa,
                        'nomor_rw' => $item->nomor_rw,
                        'target_wilayah_id' => $item->target_wilayah_id,
                        'flags' => [
                            'is_upa' => true,
                            'jabatan_upa' => $item->jabatan,
                        ],
                    ]);
                }
            });

        PenggalangSuara::query()
            ->where('status', 'aktif')
            ->chunk(200, function ($items) use (&$counts): void {
                foreach ($items as $item) {
                    $counts['penggalang'] += $this->syncCandidate([
                        'nama' => (string) $item->nama,
                        'no_hp' => $item->no_hp,
                        'no_wa' => $item->no_wa,
                        'dapil' => $item->dapil,
                        'kecamatan' => $item->kecamatan,
                        'desa' => $item->desa,
                        'nomor_rw' => $item->nomor_rw,
                        'nomor_rt' => $item->rt,
                        'target_wilayah_id' => $item->target_wilayah_id,
                        'flags' => [
                            'is_penggalang' => true,
                        ],
                    ]);
                }
            });

        $this->info(sprintf(
            'Imported %d kader dari KORWE, %d dari KORTE, %d dari UPA, %d dari Penggalang',
            $counts['korwe'],
            $counts['korte'],
            $counts['upa'],
            $counts['penggalang']
        ));

        return self::SUCCESS;
    }

    /**
     * @param array{
     *   nama:string,
     *   no_hp:?string,
     *   no_wa:?string,
     *   dapil:?string,
     *   kecamatan:?string,
     *   desa:?string,
     *   nomor_rw:?string,
     *   nomor_rt?:?string,
     *   target_wilayah_id:?string,
     *   flags:array<string,mixed>
     * } $payload
     */
    private function syncCandidate(array $payload): int
    {
        if ($payload['nama'] === '' || ($payload['nomor_rw'] ?? '') === '') {
            return 0;
        }

        $existing = Kader::query()
            ->where('nama', $payload['nama'])
            ->where('nomor_rw', $payload['nomor_rw'])
            ->where(function ($query) use ($payload): void {
                if (($payload['target_wilayah_id'] ?? null) !== null) {
                    $query->where('target_wilayah_id', $payload['target_wilayah_id']);
                } else {
                    $query->whereNull('target_wilayah_id')
                        ->where('desa', $payload['desa'])
                        ->where('kecamatan', $payload['kecamatan']);
                }
            })
            ->first();

        if ($existing) {
            $existing->update([
                'no_hp' => $existing->no_hp ?: $payload['no_hp'],
                'no_wa' => $existing->no_wa ?: $payload['no_wa'],
                'nomor_rt' => $existing->nomor_rt ?: ($payload['nomor_rt'] ?? null),
                'dapil' => $existing->dapil ?: $payload['dapil'],
                'kecamatan' => $existing->kecamatan ?: $payload['kecamatan'],
                'desa' => $existing->desa ?: $payload['desa'],
                'target_wilayah_id' => $existing->target_wilayah_id ?: $payload['target_wilayah_id'],
                'is_korwe' => $existing->is_korwe || (bool) ($payload['flags']['is_korwe'] ?? false),
                'is_korte' => $existing->is_korte || (bool) ($payload['flags']['is_korte'] ?? false),
                'is_upa' => $existing->is_upa || (bool) ($payload['flags']['is_upa'] ?? false),
                'jabatan_upa' => $existing->jabatan_upa ?: ($payload['flags']['jabatan_upa'] ?? null),
                'is_penggalang' => $existing->is_penggalang || (bool) ($payload['flags']['is_penggalang'] ?? false),
                'is_saksi' => $existing->is_saksi || (bool) ($payload['flags']['is_saksi'] ?? false),
                'status' => 'aktif',
            ]);

            return 0;
        }

        Kader::query()->create([
            'nama' => $payload['nama'],
            'no_hp' => $payload['no_hp'],
            'no_wa' => $payload['no_wa'],
            'jenjang' => 'penggerak',
            'tanggal_jenjang' => now(),
            'dapil' => $payload['dapil'],
            'kecamatan' => $payload['kecamatan'],
            'desa' => $payload['desa'],
            'nomor_rw' => $payload['nomor_rw'],
            'nomor_rt' => $payload['nomor_rt'] ?? null,
            'target_wilayah_id' => $payload['target_wilayah_id'],
            'is_korwe' => (bool) ($payload['flags']['is_korwe'] ?? false),
            'is_korte' => (bool) ($payload['flags']['is_korte'] ?? false),
            'is_upa' => (bool) ($payload['flags']['is_upa'] ?? false),
            'jabatan_upa' => $payload['flags']['jabatan_upa'] ?? null,
            'is_penggalang' => (bool) ($payload['flags']['is_penggalang'] ?? false),
            'is_saksi' => (bool) ($payload['flags']['is_saksi'] ?? false),
            'bisa_deploy' => true,
            'status' => 'aktif',
        ]);

        return 1;
    }
}
