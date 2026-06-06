<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = resource_path('views/livewire/public-site/home.blade.php');
$content = file_get_contents($path);
$compiled = app('blade.compiler')->compileString($content);
file_put_contents('compiled-home.php', $compiled);
echo "Compiled file saved to compiled-home.php\n";

exec('php -l compiled-home.php', $output, $return_var);
echo implode("\n", $output) . "\n";
echo "Exit code: $return_var\n";
