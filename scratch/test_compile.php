<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $bladeContent = file_get_contents(resource_path('views/livewire/kaderisasi/index.blade.php'));
    $compiler = app('blade.compiler');
    $compiler->compileString($bladeContent);
    echo "Blade Compiled Successfully!\n";
} catch (\Throwable $e) {
    echo "Compilation Error: " . $e->getMessage() . "\n";
}
