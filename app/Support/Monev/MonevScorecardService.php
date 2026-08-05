<?php

declare(strict_types=1);

namespace App\Support\Monev;

use App\Models\DataRw;
use App\Models\Korte;
use App\Models\Korwe;
use App\Models\PenggalangSuara;
use App\Models\ProfilRw;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MonevScorecardService
{
    /**
     * Ambil semua flag monev, digabung dan diurutkan severity merah dulu.
     * Hasil di-cache selama MonevThresholds::CACHE_TTL_MENIT menit.
     *
     * @param  array<string, string>  $scopeFilter  ['kecamatan' => ..., 'desa' => ..., 'dapil' => ...]
     * @return Collection<int, array<string, mixed>>
     */
    public function semuaFlag(array $scopeFilter = []): Collection
    {
        $cacheKey = 'monev_flags_all';

        $flags = Cache::remember($cacheKey, MonevThresholds::CACHE_TTL_MENIT * 60, function () {
            return collect()
                ->merge($this->sisirStagnan())
                ->merge($this->korweKorteStagnan())
                ->merge($this->penggalangPasif())
                ->merge($this->profilBelumLengkap())
                ->sortByDesc(fn ($f) => $f['severity'] === 'merah' ? 1 : 0)
                ->values();
        });

        // Filter di PHP setelah cache, bukan di DB, agar satu cache key cukup
        if (! empty($scopeFilter['kecamatan'])) {
            $flags = $flags->filter(
                fn ($f) => strtoupper((string) $f['kecamatan']) === strtoupper((string) $scopeFilter['kecamatan'])
            )->values();
        }

        if (! empty($scopeFilter['desa'])) {
            $flags = $flags->filter(
                fn ($f) => strtoupper((string) $f['desa']) === strtoupper((string) $scopeFilter['desa'])
            )->values();
        }

        if (! empty($scopeFilter['dapil'])) {
            $flags = $flags->filter(
                fn ($f) => strtoupper((string) $f['dapil']) === strtoupper((string) $scopeFilter['dapil'])
            )->values();
        }

        return $flags;
    }

    /**
     * Invalidasi cache flag (dipanggil setelah ada perubahan data relevan)
     */
    public function invalidateCache(): void
    {
        Cache::forget('monev_flags_all');
    }

    /**
     * Flag: RW yang sudah lama tidak ada kegiatan sisir.
     *
     * @return array<int, array<string, mixed>>
     */
    public function sisirStagnan(): array
    {
        $now = Carbon::now();
        $flags = [];

        // Ambil semua RW beserta tanggal kegiatan terakhir
        $rwList = DB::table('data_rws as dr')
            ->select([
                'dr.target_wilayah_id',
                'dr.nomor_rw',
                'dr.kecamatan',
                'dr.desa',
                'dr.dapil',
                DB::raw('MAX(kr.tanggal_kegiatan) as last_kegiatan'),
            ])
            ->leftJoin('kegiatan_rws as kr', function ($join) {
                $join->on('kr.target_wilayah_id', '=', 'dr.target_wilayah_id')
                    ->on('kr.nomor_rw', '=', 'dr.nomor_rw');
            })
            ->groupBy('dr.target_wilayah_id', 'dr.nomor_rw', 'dr.kecamatan', 'dr.desa', 'dr.dapil')
            ->get();

        foreach ($rwList as $rw) {
            if ($rw->last_kegiatan === null) {
                // Belum pernah ada kegiatan → merah langsung
                $flags[] = [
                    'jenis'             => 'sisir_stagnan',
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw'          => $rw->nomor_rw,
                    'kecamatan'         => $rw->kecamatan,
                    'desa'              => $rw->desa,
                    'dapil'             => $rw->dapil,
                    'severity'          => 'merah',
                    'detail'            => 'Belum pernah ada kegiatan sisir RW',
                    'terdeteksi_sejak'  => $now,
                ];
                continue;
            }

            $lastKegiatan = Carbon::parse($rw->last_kegiatan);
            $hariLalu = $lastKegiatan->diffInDays($now);

            if ($hariLalu >= MonevThresholds::SISIR_MERAH_HARI) {
                $flags[] = [
                    'jenis'             => 'sisir_stagnan',
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw'          => $rw->nomor_rw,
                    'kecamatan'         => $rw->kecamatan,
                    'desa'              => $rw->desa,
                    'dapil'             => $rw->dapil,
                    'severity'          => 'merah',
                    'detail'            => "Belum ada kegiatan sisir selama {$hariLalu} hari",
                    'terdeteksi_sejak'  => $lastKegiatan->addDays(MonevThresholds::SISIR_MERAH_HARI),
                ];
            } elseif ($hariLalu >= MonevThresholds::SISIR_KUNING_HARI) {
                $flags[] = [
                    'jenis'             => 'sisir_stagnan',
                    'target_wilayah_id' => $rw->target_wilayah_id,
                    'nomor_rw'          => $rw->nomor_rw,
                    'kecamatan'         => $rw->kecamatan,
                    'desa'              => $rw->desa,
                    'dapil'             => $rw->dapil,
                    'severity'          => 'kuning',
                    'detail'            => "Belum ada kegiatan sisir selama {$hariLalu} hari",
                    'terdeteksi_sejak'  => $lastKegiatan->addDays(MonevThresholds::SISIR_KUNING_HARI),
                ];
            }
        }

        return $flags;
    }

    /**
     * Flag: Korwe atau Korte yang stuck di status 'proses' terlalu lama.
     *
     * @return array<int, array<string, mixed>>
     */
    public function korweKorteStagnan(): array
    {
        $cutoff = Carbon::now()->subDays(MonevThresholds::STAGNAN_HARI);
        $flags  = [];

        // Korwe stagnan
        $korwes = DB::table('korwes as k')
            ->join('target_wilayahs as tw', 'tw.id', '=', 'k.target_wilayah_id')
            ->select(['k.target_wilayah_id', 'k.nomor_rw', 'tw.kecamatan', 'tw.desa', 'tw.dapil', 'k.updated_at'])
            ->where('k.status', 'proses')
            ->where('k.updated_at', '<', $cutoff)
            ->get();

        foreach ($korwes as $row) {
            $hariLalu = Carbon::parse($row->updated_at)->diffInDays(Carbon::now());
            $flags[] = [
                'jenis'             => 'korwe_korte_stagnan',
                'target_wilayah_id' => $row->target_wilayah_id,
                'nomor_rw'          => $row->nomor_rw,
                'kecamatan'         => $row->kecamatan,
                'desa'              => $row->desa,
                'dapil'             => $row->dapil,
                'severity'          => $hariLalu >= MonevThresholds::STAGNAN_HARI * 2 ? 'merah' : 'kuning',
                'detail'            => "Korwe stagnan di status 'proses' selama {$hariLalu} hari",
                'terdeteksi_sejak'  => Carbon::parse($row->updated_at)->addDays(MonevThresholds::STAGNAN_HARI),
            ];
        }

        // Korte stagnan
        $kortes = DB::table('kortes as k')
            ->join('target_wilayahs as tw', 'tw.id', '=', 'k.target_wilayah_id')
            ->select(['k.target_wilayah_id', 'k.nomor_rw', 'tw.kecamatan', 'tw.desa', 'tw.dapil', 'k.updated_at'])
            ->where('k.status', 'proses')
            ->where('k.updated_at', '<', $cutoff)
            ->get();

        foreach ($kortes as $row) {
            $hariLalu = Carbon::parse($row->updated_at)->diffInDays(Carbon::now());
            $flags[] = [
                'jenis'             => 'korwe_korte_stagnan',
                'target_wilayah_id' => $row->target_wilayah_id,
                'nomor_rw'          => $row->nomor_rw,
                'kecamatan'         => $row->kecamatan,
                'desa'              => $row->desa,
                'dapil'             => $row->dapil,
                'severity'          => $hariLalu >= MonevThresholds::STAGNAN_HARI * 2 ? 'merah' : 'kuning',
                'detail'            => "Korte stagnan di status 'proses' selama {$hariLalu} hari",
                'terdeteksi_sejak'  => Carbon::parse($row->updated_at)->addDays(MonevThresholds::STAGNAN_HARI),
            ];
        }

        return $flags;
    }

    /**
     * Flag: Penggalang Suara aktif dengan realisasi jangkauan masih 0.
     *
     * @return array<int, array<string, mixed>>
     */
    public function penggalangPasif(): array
    {
        $cutoff = Carbon::now()->subDays(MonevThresholds::PENGGALANG_PASIF_HARI);
        $flags  = [];

        $rows = DB::table('penggalang_suaras as ps')
            ->join('target_wilayahs as tw', 'tw.id', '=', 'ps.target_wilayah_id')
            ->select(['ps.target_wilayah_id', 'ps.nomor_rw', 'tw.kecamatan', 'tw.desa', 'tw.dapil', 'ps.created_at', 'ps.nama'])
            ->where('ps.status', 'aktif')
            ->where(function ($q) {
                $q->where('ps.realisasi_jangkauan', 0)
                    ->orWhereNull('ps.realisasi_jangkauan');
            })
            ->where('ps.created_at', '<', $cutoff)
            ->get();

        // Kelompokkan per RW (satu flag per RW, bukan per orang)
        $grouped = collect($rows)->groupBy(fn ($r) => $r->target_wilayah_id . '|' . $r->nomor_rw);

        foreach ($grouped as $key => $group) {
            $first    = $group->first();
            $jumlah   = $group->count();
            $hariLalu = Carbon::parse($group->min('created_at'))->diffInDays(Carbon::now());

            $flags[] = [
                'jenis'             => 'penggalang_pasif',
                'target_wilayah_id' => $first->target_wilayah_id,
                'nomor_rw'          => $first->nomor_rw,
                'kecamatan'         => $first->kecamatan,
                'desa'              => $first->desa,
                'dapil'             => $first->dapil,
                'severity'          => $hariLalu >= MonevThresholds::PENGGALANG_PASIF_HARI * 2 ? 'merah' : 'kuning',
                'detail'            => "{$jumlah} penggalang suara aktif dengan realisasi jangkauan 0 selama lebih dari {$hariLalu} hari",
                'terdeteksi_sejak'  => Carbon::parse($group->min('created_at'))->addDays(MonevThresholds::PENGGALANG_PASIF_HARI),
            ];
        }

        return $flags;
    }

    /**
     * Flag: RW di wilayah prioritas dengan Profil RW belum lengkap.
     *
     * @return array<int, array<string, mixed>>
     */
    public function profilBelumLengkap(): array
    {
        $now   = Carbon::now();
        $flags = [];

        // Join profil_rws dengan data_rws untuk mendapatkan status_wilayah
        $rows = DB::table('data_rws as dr')
            ->join('profil_rws as pr', function ($join) {
                $join->on('pr.target_wilayah_id', '=', 'dr.target_wilayah_id')
                    ->on('pr.nomor_rw', '=', 'dr.nomor_rw');
            })
            ->select(['dr.target_wilayah_id', 'dr.nomor_rw', 'dr.kecamatan', 'dr.desa', 'dr.dapil', 'dr.status_wilayah', 'pr.completion_percent'])
            ->whereIn('dr.status_wilayah', MonevThresholds::PROFIL_STATUS_PRIORITAS)
            ->where('pr.completion_percent', '<', MonevThresholds::PROFIL_MIN_COMPLETION)
            ->get();

        foreach ($rows as $row) {
            $flags[] = [
                'jenis'             => 'profil_belum_lengkap',
                'target_wilayah_id' => $row->target_wilayah_id,
                'nomor_rw'          => $row->nomor_rw,
                'kecamatan'         => $row->kecamatan,
                'desa'              => $row->desa,
                'dapil'             => $row->dapil,
                'severity'          => $row->completion_percent < 40 ? 'merah' : 'kuning',
                'detail'            => "Profil RW baru {$row->completion_percent}% lengkap (wilayah {$row->status_wilayah})",
                'terdeteksi_sejak'  => $now,
            ];
        }

        return $flags;
    }
}
