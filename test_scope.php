<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = app(\App\Models\User::class)->where('role', 'admin_dpd')->first();
if (!$user) {
    echo "No admin_dpd found\n";
    exit;
}

Auth::login($user);
$scopeClass = new class { use \App\Traits\WithWilayahScope; };
echo json_encode($scopeClass->accessScope());
echo "\n";
