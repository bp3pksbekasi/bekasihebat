<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'menu.dashboard',
            'menu.infra-rtrw',
            'menu.sapa-warga',
            'menu.sisir-rw',
            'menu.kaderisasi',
            'menu.bedah-dapil',
            'menu.event',
            'menu.event-view',
            'menu.sosial-media',
            'menu.program-kerja',
            'menu.pengaturan',
            'menu.profil',
            'menu.rki',
            'menu.ksn',
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        // Define roles
        $roles = [
            'admin_dpd',
            'pengurus_bidang',
            'kader',
            'community_member',
            'dapil',
        ];

        // Create roles
        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName);
        }

        // Give all permissions to admin_dpd
        $adminRole = Role::findByName('admin_dpd');
        $adminRole->givePermissionTo(Permission::all());

        // Give permissions to dapil role
        $dapilRole = Role::findByName('dapil');
        $dapilPermissions = [
            'menu.dashboard',
            'menu.infra-rtrw',
            'menu.sapa-warga',
            'menu.sisir-rw',
            'menu.kaderisasi',
            'menu.bedah-dapil',
            'menu.event',
            'menu.event-view',
            'menu.sosial-media',
            'menu.program-kerja',
            'menu.profil',
            'menu.rki',
            'menu.ksn',
            'menu.aspirasi',
        ];
        foreach ($dapilPermissions as $permissionName) {
            $dapilRole->givePermissionTo(Permission::findOrCreate($permissionName));
        }
    }
}
