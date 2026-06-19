<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$universal = ['dashboard', 'sapa-warga', 'profil', 'event-view', 'sisir-rw'];
$dapilAllowed = [
    'dashboard', 'kaderisasi', 'infra-rtrw', 'sisir-rw', 'sapa-warga',
    'sosial-media', 'rki', 'ksn', 'bedah-dapil', 'aspirasi',
    'program-kerja', 'event', 'event-view', 'profil'
];

foreach (Role::all() as $role) {
    if ($role->name === 'admin_dpd' || $role->name === 'super_admin') {
        continue;
    }
    
    // Give universal to all roles
    foreach ($universal as $menu) {
        $role->givePermissionTo("menu.{$menu}");
    }
    
    // Give dapil menus to dapil role
    if ($role->name === 'dapil') {
        foreach ($dapilAllowed as $menu) {
            $role->givePermissionTo("menu.{$menu}");
        }
    }
}

// Give all users their current role if they don't have it via Spatie?
// We only need the Roles to have the permissions.
echo "Fallbacks seeded.\n";
