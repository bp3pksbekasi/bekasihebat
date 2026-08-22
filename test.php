<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $event = App\Models\Event::where('status', 'approved')->first();
    if ($event) {
        $event->update(['status' => 'ongoing']);
        echo "Success";
    } else {
        echo "No approved event found";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
