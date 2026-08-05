<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Buat permission menu.monev
        $permission = Permission::findOrCreate('menu.monev');

        // Berikan ke admin_dpd
        $adminRole = Role::findByName('admin_dpd');
        if ($adminRole && ! $adminRole->hasPermissionTo($permission)) {
            $adminRole->givePermissionTo($permission);
        }

        // Berikan ke dapil
        $dapilRole = Role::findByName('dapil');
        if ($dapilRole && ! $dapilRole->hasPermissionTo($permission)) {
            $dapilRole->givePermissionTo($permission);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where('name', 'menu.monev')->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
