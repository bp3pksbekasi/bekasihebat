<?php

namespace App\Traits;

use App\Models\TargetWilayah;
use App\Services\WilayahService;
use Livewire\Attributes\Computed;

trait WithWilayahScope
{
    #[Computed]
    public function accessScope(): array
    {
        $user = auth()->user();
        if (! $user) {
            return ['mode' => 'global', 'is_dapil' => false, 'locked_dapil' => '', 'kecamatan' => ''];
        }

        if ($user->isAdmin()) {
            return ['mode' => 'global', 'is_dapil' => false, 'locked_dapil' => '', 'kecamatan' => '', 'desa' => ''];
        }

        $isDapil = (bool) ($user->hasRole('dapil') || $user->isDapil());
        $isDpc = $user->isDpc();
        $isDpra = $user->isDpra();
        
        $kecamatan = '';
        $lockedDapil = '';

        if ($isDapil && ! empty($user->kelurahan_code)) {
            $wilayah = app(WilayahService::class)->getWilayahDetailByKelurahan($user->kelurahan_code);
            $kecamatan = trim((string) ($wilayah?->kecamatan_name ?? ''));

            if ($kecamatan !== '') {
                $lockedDapil = (string) (TargetWilayah::query()
                    ->where('kecamatan', mb_strtoupper($kecamatan))
                    ->value('dapil')
                    ?? TargetWilayah::query()->where('kecamatan', $kecamatan)->value('dapil')
                    ?? '');
            }
        } elseif ($isDapil && ! empty($user->dapil)) {
            $lockedDapil = preg_match('/^\d+$/', (string)$user->dapil) ? 'BEKASI ' . $user->dapil : $user->dapil;
        } elseif ($isDpc && ! empty($user->kecamatan)) {
            $kecamatan = mb_strtoupper((string) $user->kecamatan);
            $lockedDapil = (string) (TargetWilayah::query()
                ->where('kecamatan', $kecamatan)
                ->value('dapil') ?? '');
            $isDapil = true; // Act like dapil mode to restrict scope
        } elseif ($isDpra && ! empty($user->desa)) {
            $desa = mb_strtoupper((string) $user->desa);
            $target = TargetWilayah::query()->where('desa', $desa)->first();
            if ($target) {
                $kecamatan = mb_strtoupper((string) $target->kecamatan);
                $lockedDapil = (string) $target->dapil;
                $isDapil = true;
            }
        }

        return [
            'mode' => $isDapil || $isDpc || $isDpra ? 'dapil' : 'global',
            'is_dapil' => $isDapil || $isDpc || $isDpra,
            'locked_dapil' => $lockedDapil,
            'kecamatan' => $kecamatan,
            'desa' => $isDpra && !empty($user->desa) ? mb_strtoupper((string) $user->desa) : '',
        ];
    }

    public function applyUserScope($query, array $columns = ['dapil', 'kecamatan', 'desa'])
    {
        $scope = $this->accessScope();
        if (($scope['mode'] ?? 'global') === 'dapil') {
            if (in_array('dapil', $columns) && !empty($scope['locked_dapil'])) {
                $query->where('dapil', $scope['locked_dapil']);
            }
            if (in_array('kecamatan', $columns) && !empty($scope['kecamatan'])) {
                $query->where('kecamatan', $scope['kecamatan']);
            }
            if (in_array('desa', $columns) && !empty($scope['desa'])) {
                $query->where('desa', $scope['desa']);
            }
        }
        return $query;
    }
}
