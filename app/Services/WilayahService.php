<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class WilayahService
{
    public function getKabBekasiKecamatan(): Collection
    {
        return DB::table('indonesia_districts')
            ->where('code', 'LIKE', '3216%')
            ->orderBy('name')
            ->get(['code', 'name']);
    }

    public function getKelurahanByKecamatan(?string $kecamatanCode): Collection
    {
        if (empty($kecamatanCode)) {
            return collect([]);
        }

        return DB::table('indonesia_villages')
            ->where('code', 'LIKE', $kecamatanCode.'%')
            ->orderBy('name')
            ->get(['code', 'name']);
    }

    public function getWilayahDetailByKelurahan(?string $kelurahanCode): ?stdClass
    {
        if (empty($kelurahanCode)) {
            return null;
        }

        return DB::table('indonesia_villages as village')
            ->join('indonesia_districts as district', DB::raw('LEFT(village.code, 6)'), '=', 'district.code')
            ->leftJoin('indonesia_cities as city', DB::raw('LEFT(village.code, 4)'), '=', 'city.code')
            ->where('village.code', $kelurahanCode)
            ->select([
                'village.code as kelurahan_code',
                'village.name as kelurahan_name',
                'district.code as kecamatan_code',
                'district.name as kecamatan_name',
                'city.code as kabupaten_code',
                'city.name as kabupaten_name',
            ])
            ->first();
    }
}
