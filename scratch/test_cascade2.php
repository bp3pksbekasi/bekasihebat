<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dapils = \App\Models\TargetWilayah::distinct()->pluck('dapil');
echo "Dapils:\n";
print_r($dapils->toArray());

$kecamatans = \App\Models\TargetWilayah::where('dapil', 'Dapil 1')->distinct()->pluck('kecamatan');
echo "\nKecamatans in Dapil 1:\n";
print_r($kecamatans->toArray());

$desas = \App\Models\TargetWilayah::where('kecamatan', $kecamatans[0] ?? '')->distinct()->pluck('desa');
echo "\nDesas in " . ($kecamatans[0] ?? '') . ":\n";
print_r($desas->toArray());
