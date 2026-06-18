<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use App\Models\TargetWilayah;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = Schema::getColumnListing('target_wilayahs');
    echo "Columns in target_wilayahs:\n";
    foreach ($columns as $column) {
        echo " - $column\n";
    }
    
    $count = TargetWilayah::count();
    echo "\nTotal rows in target_wilayahs: $count\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
