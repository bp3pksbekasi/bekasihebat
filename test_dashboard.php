<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = app(\App\Models\User::class)->where('role', 'admin_dpd')->first();
Auth::login($user);

$c = new \App\Livewire\Dashboard;
$c->mount();

echo json_encode($c->accessScope);
echo "\n";
