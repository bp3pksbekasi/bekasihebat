<?php

namespace App\Livewire\Pengaturan;

use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On;

class RuleManagement extends Component
{
    public $roles = [];
    public $menus = [];
    public $rolePermissions = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $rolesCollection = Role::whereNotIn('name', ['admin_dpd', 'super_admin'])->orderBy('name')->get();
        $this->menus = [
            'dashboard' => 'Dashboard',
            'kaderisasi' => 'Kaderisasi',
            'infra-rtrw' => 'Infrastruktur',
            'sisir-rw' => 'Sisir RW',
            'sapa-warga' => 'Sapa Warga',
            'sosial-media' => 'Sosial Media',
            'rki' => 'Bipeka',
            'ksn' => 'Binapora',
            'bedah-dapil' => 'Bedah Dapil',
            'aspirasi' => 'Aspirasi & POKIR',
            'event' => 'Program',
            'profil' => 'Profil',
            'pengaturan-users' => 'Kelola User',
            'pengaturan-whatsapp' => 'Setting WhatsApp',
            'kartu-anggota' => 'Kartu Anggota',
            'pengaturan-rule' => 'Pengaturan Rule'
        ];

        $this->rolePermissions = [];
        $this->roles = [];

        foreach ($rolesCollection as $role) {
            $this->roles[] = [
                'id' => $role->id,
                'name' => $role->name,
            ];
            $perms = $role->permissions->pluck('name')->toArray();
            foreach ($this->menus as $slug => $label) {
                // Check for either 'menu.slug' or just 'slug'
                $this->rolePermissions[$role->id][$slug] = in_array("menu.{$slug}", $perms) || in_array($slug, $perms);
            }
        }
    }

    public function togglePermission(int $roleId, string $menuSlug): void
    {
        $role = Role::findOrFail($roleId);
        $permName = "menu.{$menuSlug}";

        if (!Permission::where('name', $permName)->exists()) {
            Permission::create(['name' => $permName, 'guard_name' => 'web']);
        }

        if ($role->hasPermissionTo($permName)) {
            $role->revokePermissionTo($permName);
            $this->rolePermissions[$roleId][$menuSlug] = false;
        } else {
            $role->givePermissionTo($permName);
            $this->rolePermissions[$roleId][$menuSlug] = true;
        }
    }

    public function render(): View
    {
        return view('livewire.pengaturan.rule-management')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Pengaturan Rule']);
    }
}
