<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::where('role', \App\Models\User::ROLE_DPC)
    ->get(['name', 'email', 'phone', 'kecamatan'])
    ->toArray();

$md = "| Nama | Username (Email) | No HP | Kecamatan |\n|---|---|---|---|\n";
foreach ($users as $u) {
    $md .= "| " . ($u['name'] ?? '-') . " | " . ($u['email'] ?? '-') . " | " . ($u['phone'] ?? '-') . " | " . ($u['kecamatan'] ?? '-') . " |\n";
}

file_put_contents('list_dpc.md', $md);
echo "Done.";
