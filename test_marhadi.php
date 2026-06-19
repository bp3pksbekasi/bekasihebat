<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('name', 'LIKE', '%Marhadi%')->first();
echo "Role: " . $user->role . "\n";
echo "Org Level: " . $user->org_level . "\n";
echo "Kecamatan: " . $user->kecamatan . "\n";
echo "Dapil: " . $user->dapil . "\n";
echo "isAdmin: " . ($user->isAdmin() ? 'true' : 'false') . "\n";
echo "isDpc: " . ($user->isDpc() ? 'true' : 'false') . "\n";
