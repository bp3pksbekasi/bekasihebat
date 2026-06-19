<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dapils = \App\Models\TargetWilayah::where('kecamatan', 'Tambun Utara')->pluck('dapil')->unique();
echo "Tambun Utara Dapils: " . implode(', ', $dapils->toArray()) . "\n";

$dapilsAll = \App\Models\TargetWilayah::pluck('dapil')->unique();
echo "All Dapils: " . implode(', ', $dapilsAll->toArray()) . "\n";
