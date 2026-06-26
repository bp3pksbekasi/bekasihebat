<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = App\Models\AuditLog::where('action', 'reset_password')
    ->orderBy('created_at', 'desc')
    ->take(50)
    ->get();

$results = [];
foreach ($logs as $log) {
    if (!isset($log->properties['target_user_id']) || !isset($log->properties['temporary_password'])) {
        continue;
    }
    
    $user = App\Models\User::find($log->properties['target_user_id']);
    if (!$user) continue;
    
    // Check if user role is dapil_1 to dapil_7
    if (in_array($user->role, ['dapil_1', 'dapil_2', 'dapil_3', 'dapil_4', 'dapil_5', 'dapil_6', 'dapil_7'])) {
        $results[$user->id] = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => $log->properties['temporary_password']
        ];
    }
}

foreach ($results as $id => $data) {
    echo "Nama: {$data['name']}\n";
    echo "Email: {$data['email']}\n";
    echo "Role: {$data['role']}\n";
    echo "Password Sementara: {$data['password']}\n";
    echo "--------------------------\n";
}
