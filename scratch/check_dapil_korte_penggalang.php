<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TargetWilayah;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;

$selectedTahun = 2029;
$tahunField = min(max($selectedTahun, 2026), 2029);
echo "Active target year field: target_korwe_{$tahunField}\n";

$dapils = TargetWilayah::query()
    ->distinct()
    ->orderBy('dapil')
    ->pluck('dapil');

foreach ($dapils as $dapil) {
    // Korwe
    $targetKorwe = (int) TargetWilayah::query()->where('dapil', $dapil)->sum("target_korwe_{$tahunField}");
    $korweTerbentuk = (int) Korwe::query()
        ->whereHas('targetWilayah', fn ($q) => $q->where('dapil', $dapil))
        ->where('status', 'terbentuk')
        ->count();
    $korwePct = $targetKorwe > 0 ? (int) round(($korweTerbentuk / $targetKorwe) * 100) : 0;

    // Korte
    $targetKorte = (int) TargetWilayah::query()->where('dapil', $dapil)->sum("target_korte_{$tahunField}");
    $korteTerbentuk = (int) Korte::query()
        ->whereHas('targetWilayah', fn ($q) => $q->where('dapil', $dapil))
        ->where('status', 'terbentuk')
        ->count();
    $kortePct = $targetKorte > 0 ? (int) round(($korteTerbentuk / $targetKorte) * 100) : 0;

    // Penggalang
    $targetPenggalang = (int) TargetWilayah::query()->where('dapil', $dapil)->sum('target_penggalang');
    $penggalang = (int) PenggalangSuara::query()
        ->whereHas('targetWilayah', fn ($q) => $q->where('dapil', $dapil))
        ->where('status', 'aktif')
        ->count();
    $penggalangPct = $targetPenggalang > 0 ? (int) round(($penggalang / $targetPenggalang) * 100) : 0;

    echo "Dapil {$dapil}:\n";
    echo "  Korwe: {$korweTerbentuk}/{$targetKorwe} ({$korwePct}%)\n";
    echo "  Korte: {$korteTerbentuk}/{$targetKorte} ({$kortePct}%)\n";
    echo "  Penggalang: {$penggalang}/{$targetPenggalang} ({$penggalangPct}%)\n";
}
