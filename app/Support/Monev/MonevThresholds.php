<?php

declare(strict_types=1);

namespace App\Support\Monev;

class MonevThresholds
{
    /**
     * Jumlah hari tanpa kegiatan sisir RW → flag KUNING
     */
    public const SISIR_KUNING_HARI = 14;

    /**
     * Jumlah hari tanpa kegiatan sisir RW → flag MERAH
     */
    public const SISIR_MERAH_HARI = 30;

    /**
     * Jumlah hari Korwe/Korte stuck di status 'proses' tanpa perubahan → flag KUNING
     */
    public const STAGNAN_HARI = 14;

    /**
     * Jumlah hari Penggalang Suara aktif dengan realisasi masih 0 → flag KUNING
     */
    public const PENGGALANG_PASIF_HARI = 30;

    /**
     * Persentase minimum completion_percent Profil RW dianggap lengkap
     */
    public const PROFIL_MIN_COMPLETION = 80;

    /**
     * Status wilayah yang diprioritaskan untuk flag profil belum lengkap
     * (tidak semua RW di-flag, hanya yang prioritas)
     */
    public const PROFIL_STATUS_PRIORITAS = ['RAWAN', 'POTENSIAL'];

    /**
     * Durasi cache flag scorecard (dalam menit)
     */
    public const CACHE_TTL_MENIT = 120;
}
