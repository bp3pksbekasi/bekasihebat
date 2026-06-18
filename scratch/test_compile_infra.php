<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $files = [
        'index' => __DIR__ . '/../resources/views/livewire/infra-rtrw/index.blade.php',
        'detail' => __DIR__ . '/../resources/views/livewire/infra-rtrw/detail.blade.php',
    ];
    
    foreach ($files as $name => $path) {
        if (!is_file($path)) {
            echo "File not found: $path\n";
            continue;
        }
        $content = file_get_contents($path);
        Illuminate\Support\Facades\Blade::compileString($content);
        echo "Blade '$name' compiled successfully!\n";
    }
    
    // Also test loading the Livewire component classes
    class_exists(\App\Livewire\InfraRtRw\Index::class);
    class_exists(\App\Livewire\InfraRtRw\Detail::class);
    echo "Livewire component classes loaded successfully!\n";
    
} catch (\Exception $e) {
    echo "Error compiling: " . $e->getMessage() . "\n";
}
