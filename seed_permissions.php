<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$menus = [
    'dashboard', 'kaderisasi', 'infra-rtrw', 'sisir-rw', 'sapa-warga', 
    'sosial-media', 'rki', 'ksn', 'bedah-dapil', 'aspirasi', 
    'event', 'profil', 'pengaturan-users', 'pengaturan-whatsapp', 'kartu-anggota', 'pengaturan-rule'
];

foreach ($menus as $menu) {
    Permission::firstOrCreate(['name' => "menu.{$menu}", 'guard_name' => 'web']);
    // Some logic checks menuSlug without 'menu.'
    Permission::firstOrCreate(['name' => "{$menu}", 'guard_name' => 'web']);
}

$strukturMenus = ['infra-rtrw', 'dashboard', 'sisir-rw', 'sapa-warga', 'bedah-dapil', 'kaderisasi', 'profil'];

$rolesToUpdate = ['pengurus_dpc', 'pengurus_dpra', 'pengurus_dpd'];

foreach ($rolesToUpdate as $roleName) {
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        $permissions = [];
        foreach ($strukturMenus as $menu) {
            $permissions[] = "menu.{$menu}";
        }
        $role->givePermissionTo($permissions);
        echo "Assigned structural defaults to {$roleName}\n";
    }
}

echo "Permissions seeded successfully.\n";
