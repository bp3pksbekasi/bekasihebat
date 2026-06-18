<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['korwes', 'kortes', 'data_rws', 'penggalang_suaras', 'profil_rws', 'kegiatan_rws'];

foreach ($tables as $table) {
    $count = DB::table($table)->count();
    echo "Table $table count: $count\n";
}
