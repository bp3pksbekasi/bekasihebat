<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$period = \App\Models\PemiluPeriod::query()->where('is_default', true)->first();
$sums = \App\Models\PemiluDesaSummary::where('pemilu_period_id', $period->id)->get();

$found = false;
foreach ($sums as $sum) {
    if (!is_array($sum->rw_rows)) continue;
    foreach ($sum->rw_rows as $rwIndex => $rwData) {
        if (!isset($rwData['party_rows'])) continue;
        foreach ($rwData['party_rows'] as $pr) {
            if (!empty($pr['candidates'])) {
                echo "Found candidates in Desa {$sum->desa}, RW {$rwData['rw']}, Party {$pr['party_name']}:\n";
                echo json_encode(array_slice($pr['candidates'], 0, 3)) . "\n";
                $found = true;
                break 3;
            }
        }
    }
}

if (!$found) {
    echo "NO CANDIDATES FOUND ANYWHERE!\n";
}
