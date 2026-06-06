<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Permission::findOrCreate(User::menuPermissionName('rki'), 'web');
        Permission::findOrCreate(User::menuPermissionName('ksn'), 'web');

        User::query()
            ->where('bidang_slug', 'perempuan')
            ->cursor()
            ->each(function (User $user): void {
                $user->givePermissionTo([
                    User::menuPermissionName('rki'),
                    User::menuPermissionName('ksn'),
                ]);
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        User::query()
            ->cursor()
            ->each(function (User $user): void {
                foreach (['rki', 'ksn'] as $menuSlug) {
                    if ($user->hasMenuPermission($menuSlug)) {
                        $user->revokePermissionTo(User::menuPermissionName($menuSlug));
                    }
                }
            });

        Permission::query()
            ->whereIn('name', [
                User::menuPermissionName('rki'),
                User::menuPermissionName('ksn'),
            ])
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
