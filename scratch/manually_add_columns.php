<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tableName = 'target_wilayahs';
    $newColumns = [
        'target_penggalang_2026',
        'target_penggalang_2027',
        'target_penggalang_2028',
        'target_penggalang_2029',
    ];
    
    $colsToAdd = [];
    foreach ($newColumns as $col) {
        if (!Schema::hasColumn($tableName, $col)) {
            $colsToAdd[] = $col;
        }
    }
    
    if (count($colsToAdd) > 0) {
        echo "Adding columns: " . implode(', ', $colsToAdd) . "\n";
        Schema::table($tableName, function (Blueprint $table) use ($colsToAdd) {
            foreach ($colsToAdd as $col) {
                $table->integer($col)->default(0)->after('target_penggalang');
            }
        });
        echo "Columns added successfully!\n";
    } else {
        echo "All target penggalang columns already exist.\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
