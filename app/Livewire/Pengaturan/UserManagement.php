<?php

declare(strict_types=1);

namespace App\Livewire\Pengaturan;

use App\Models\AuditLog;
use App\Models\Kader;
use App\Models\TargetWilayah;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class UserManagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $filterRole = '';
    public string $filterBidang = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public ?int $editUserId = null;
    public string $fName = '';
    public string $fEmail = '';
    public string $fPhone = '';
    public string $fNia = '';
    public string $fPassword = '';
    public string $fPasswordConfirmation = '';
    public string $fRole = User::ROLE_KADER;
    public string $fBidangSlug = '';
    public string $fStatus = 'aktif';

    public bool $showAuditLog = false;
    public bool $showBidangBreakdown = false;
    public string $filterAction = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function getKpiProperty(): array
    {
        $perRole = User::query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $bidangBreakdown = User::query()
            ->where('role', User::ROLE_BIDANG)
            ->whereNotNull('bidang_slug')
            ->selectRaw('bidang_slug, count(*) as total')
            ->groupBy('bidang_slug')
            ->pluck('total', 'bidang_slug');

        return [
            'totalKader' => (int) Kader::query()->aktif()->count(),
            'sudahAktivasi' => (int) User::query()->aktif()->count(),
            'belumAktivasi' => (int) Kader::query()->aktif()->where('is_activated', false)->count(),
            'onlineHariIni' => (int) User::query()->whereDate('last_login_at', today())->count(),
            'perRole' => $perRole,
            'perBidangPengurus' => $bidangBreakdown,
        ];
    }

    public function getBidangOptionsProperty(): Collection
    {
        return collect(User::BIDANG_OPTIONS)
            ->map(fn (string $label, string $slug) => ['slug' => $slug, 'label' => $label])
            ->values();
    }

    public function getSplitMenuOptionsProperty(): Collection
    {
        return collect([
            'rki' => 'Bipeka',
            'ksn' => 'Binapora',
        ])->map(fn (string $label, string $slug) => ['slug' => $slug, 'label' => $label])
            ->values();
    }

    public function getDapilListProperty(): Collection
    {
        return TargetWilayah::query()
            ->whereNotNull('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    public function getKecamatanListProperty(): Collection
    {
        return TargetWilayah::query()
            ->whereNotNull('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getDesaListProperty(): Collection
    {
        return TargetWilayah::query()
            ->whereNotNull('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    public function getUserListProperty(): LengthAwarePaginator
    {
        return User::query()
            ->with('permissions')
            ->leftJoin('kaders', 'users.kader_id', '=', 'kaders.id')
            ->select([
                'users.*',
                'kaders.nama as kader_nama',
                'kaders.nia as kader_nia',
                'kaders.jenjang as kader_jenjang',
                'kaders.bidang_slug as kader_bidang_slug',
                'kaders.is_activated as kader_is_activated',
            ])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $nested): void {
                    $nested->where('users.name', 'like', '%'.$this->search.'%')
                        ->orWhere('users.nia', 'like', '%'.$this->search.'%')
                        ->orWhere('kaders.nia', 'like', '%'.$this->search.'%')
                        ->orWhere('kaders.nama', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterRole !== '', fn (Builder $query) => $query->where('users.role', $this->filterRole))
            ->when($this->filterBidang !== '', fn (Builder $query) => $query->where('users.bidang_slug', $this->filterBidang))
            ->when($this->filterStatus !== '', fn (Builder $query) => $query->where('users.status', $this->filterStatus))
            ->orderByDesc('users.last_login_at')
            ->orderBy('users.name')
            ->paginate(20, ['*'], 'userPage');
    }

    public function getAuditLogProperty(): Collection
    {
        return AuditLog::query()
            ->with('user')
            ->when($this->filterAction !== '', fn (Builder $query) => $query->where('action', $this->filterAction))
            ->latest()
            ->limit(20)
            ->get();
    }

    public function getAuditActionOptionsProperty(): Collection
    {
        return AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
    }

    public function toggleAuditLog(): void
    {
        $this->showAuditLog = ! $this->showAuditLog;
    }

    public function toggleBidangBreakdown(): void
    {
        $this->showBidangBreakdown = ! $this->showBidangBreakdown;
    }

    public function openForm(): void
    {
        $this->resetFormState();
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->resetFormState();
        $this->showForm = false;
    }

    public function ubahRole(int $userId, string $newRole): void
    {
        abort_unless(in_array($newRole, [User::ROLE_ADMIN, User::ROLE_BIDANG, User::ROLE_KADER, User::ROLE_DPC, User::ROLE_DPRA, User::ROLE_DAPIL], true), 422);

        $user = User::query()->findOrFail($userId);
        $before = $user->role;

        $payload = ['role' => $newRole];

        if ($newRole !== User::ROLE_BIDANG) {
            $payload['bidang_slug'] = null;
        }

        $user->update($payload);

        if ($user->kader_id && $newRole !== User::ROLE_BIDANG) {
            Kader::query()->whereKey($user->kader_id)->update(['bidang_slug' => null]);
        }

        AuditLog::log('ubah_role', "Ubah role {$user->name}: {$before} -> {$newRole}", [
            'target_user_id' => $user->id,
            'before' => $before,
            'after' => $newRole,
        ]);

        session()->flash('message', 'Role user berhasil diperbarui.');
    }

    public function assignBidang(int $userId, string $bidangSlug): void
    {
        $user = User::query()->findOrFail($userId);

        if ($bidangSlug !== '' && ! array_key_exists($bidangSlug, User::BIDANG_OPTIONS)) {
            abort(422, 'Bidang tidak valid.');
        }

        $before = $user->bidang_slug;
        $beforeRole = $user->role;
        $newRole = $bidangSlug !== '' ? User::ROLE_BIDANG : User::ROLE_KADER;

        $user->update([
            'bidang_slug' => $bidangSlug !== '' ? $bidangSlug : null,
            'role' => $newRole,
        ]);

        if ($user->kader_id) {
            Kader::query()->whereKey($user->kader_id)->update([
                'bidang_slug' => $bidangSlug !== '' ? $bidangSlug : null,
            ]);
        }

        $this->grantDefaultPerempuanMenuAccess($user);

        AuditLog::log('assign_bidang', "Assign bidang {$user->name}: ".($before ?: '-').' -> '.($bidangSlug ?: '-'), [
            'target_user_id' => $user->id,
            'before' => $before,
            'after' => $bidangSlug,
            'before_role' => $beforeRole,
            'after_role' => $newRole,
        ]);

        session()->flash('message', 'Bidang user berhasil diperbarui.');
    }

    public function assignDapil(int $userId, string $dapil): void
    {
        $user = User::query()->findOrFail($userId);
        $before = $user->dapil;
        $user->update(['dapil' => $dapil !== '' ? $dapil : null]);

        AuditLog::log('assign_dapil', "Assign dapil {$user->name}: ".($before ?: '-').' -> '.($dapil ?: '-'), [
            'target_user_id' => $user->id,
            'before' => $before,
            'after' => $dapil,
        ]);
        session()->flash('message', 'Dapil user berhasil diperbarui.');
    }

    public function assignKecamatan(int $userId, string $kecamatan): void
    {
        $user = User::query()->findOrFail($userId);
        $before = $user->kecamatan;
        $user->update(['kecamatan' => $kecamatan !== '' ? $kecamatan : null]);

        AuditLog::log('assign_kecamatan', "Assign kecamatan {$user->name}: ".($before ?: '-').' -> '.($kecamatan ?: '-'), [
            'target_user_id' => $user->id,
            'before' => $before,
            'after' => $kecamatan,
        ]);
        session()->flash('message', 'Kecamatan DPC berhasil diperbarui.');
    }

    public function assignDesa(int $userId, string $desa): void
    {
        $user = User::query()->findOrFail($userId);
        $before = $user->desa;
        $user->update(['desa' => $desa !== '' ? $desa : null]);

        AuditLog::log('assign_desa', "Assign desa {$user->name}: ".($before ?: '-').' -> '.($desa ?: '-'), [
            'target_user_id' => $user->id,
            'before' => $before,
            'after' => $desa,
        ]);
        session()->flash('message', 'Desa DPRa berhasil diperbarui.');
    }

    public function toggleMenuAccess(int $userId, string $menuSlug): void
    {
        abort_unless(array_key_exists($menuSlug, User::SPLIT_MENU_PERMISSIONS), 422);

        $user = User::query()->findOrFail($userId);
        abort_if($user->isAdmin(), 422, 'Admin DPD selalu memiliki akses penuh.');

        $permission = Permission::findOrCreate(User::menuPermissionName($menuSlug), 'web');
        $enabled = ! $user->hasMenuPermission($menuSlug);

        if ($enabled) {
            $user->givePermissionTo($permission);
        } else {
            $user->revokePermissionTo($permission);
        }

        AuditLog::log('toggle_menu_access', "Ubah akses {$menuSlug} untuk {$user->name}: ".($enabled ? 'aktif' : 'nonaktif'), [
            'target_user_id' => $user->id,
            'menu_slug' => $menuSlug,
            'enabled' => $enabled,
        ]);

        session()->flash('message', 'Hak akses user berhasil diperbarui.');
    }

    public function nonaktifkanUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $user->update(['status' => 'nonaktif']);

        AuditLog::log('nonaktifkan_user', "Nonaktifkan user: {$user->name}", [
            'target_user_id' => $user->id,
            'status' => 'nonaktif',
        ]);

        session()->flash('message', 'User berhasil dinonaktifkan.');
    }

    public function aktifkanUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $user->update(['status' => 'aktif']);

        AuditLog::log('aktifkan_user', "Aktifkan user: {$user->name}", [
            'target_user_id' => $user->id,
            'status' => 'aktif',
        ]);

        session()->flash('message', 'User berhasil diaktifkan.');
    }

    public function resetPassword(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $temporaryPassword = Str::upper(Str::random(4)).random_int(1000, 9999);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        AuditLog::log('reset_password', "Reset password user: {$user->name}", [
            'target_user_id' => $user->id,
            'temporary_password' => $temporaryPassword,
        ]);

        $message = "Password sementara {$user->name}: {$temporaryPassword}";
        session()->flash('message', $message);
        $this->js("alert('".addslashes($message)."');");
    }

    public function simpanUser(): void
    {
        $validated = $this->validate([
            'fName' => ['required', 'string', 'max:255'],
            'fEmail' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'fPhone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')],
            'fNia' => ['nullable', 'regex:/^\d{2}\.\d{2}\.\d{2}\.\d{2}\.\d{4}$/', Rule::unique('users', 'nia')],
            'fPassword' => ['required', 'string', 'min:6'],
            'fPasswordConfirmation' => ['required', 'same:fPassword'],
            'fRole' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_BIDANG, User::ROLE_KADER, User::ROLE_DPC, User::ROLE_DPRA, User::ROLE_DAPIL])],
            'fBidangSlug' => ['nullable', Rule::in(array_keys(User::BIDANG_OPTIONS))],
            'fStatus' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'fNia.regex' => 'Format NIA harus 32.16.06.10.0065.',
            'fPasswordConfirmation.same' => 'Konfirmasi password tidak sama.',
        ]);

        if ($validated['fRole'] === User::ROLE_BIDANG && $validated['fBidangSlug'] === '') {
            $this->addError('fBidangSlug', 'Bidang wajib dipilih untuk pengurus bidang.');

            return;
        }

        $user = User::query()->create([
            'name' => $validated['fName'],
            'email' => $validated['fEmail'],
            'phone' => $validated['fPhone'],
            'nia' => $validated['fNia'] !== '' ? $validated['fNia'] : null,
            'password' => Hash::make($validated['fPassword']),
            'role' => $validated['fRole'],
            'bidang_slug' => $validated['fRole'] === User::ROLE_BIDANG && $validated['fBidangSlug'] !== '' ? $validated['fBidangSlug'] : null,
            'status' => $validated['fStatus'],
            'email_verified_at' => now(),
            'profile_completed_at' => now(),
        ]);

        $this->grantDefaultPerempuanMenuAccess($user);

        AuditLog::log('buat_user', "Buat user baru: {$user->name}", [
            'target_user_id' => $user->id,
            'role' => $user->role,
            'bidang_slug' => $user->bidang_slug,
            'status' => $user->status,
        ]);

        $this->closeForm();
        session()->flash('message', 'User baru berhasil ditambahkan.');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterRole = '';
        $this->filterBidang = '';
        $this->filterStatus = '';
        $this->resetPage('userPage');
    }

    public function updatingSearch(): void
    {
        $this->resetPage('userPage');
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage('userPage');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage('userPage');
    }

    public function updatingFilterBidang(): void
    {
        $this->resetPage('userPage');
    }

    public function updatingFilterAction(): void
    {
        $this->showAuditLog = true;
    }

    public function updatedFRole(string $value): void
    {
        if ($value !== User::ROLE_BIDANG) {
            $this->fBidangSlug = '';
        }
    }

    private function resetFormState(): void
    {
        $this->resetValidation();
        $this->editUserId = null;
        $this->fName = '';
        $this->fEmail = '';
        $this->fPhone = '';
        $this->fNia = '';
        $this->fPassword = '';
        $this->fPasswordConfirmation = '';
        $this->fRole = User::ROLE_KADER;
        $this->fBidangSlug = '';
        $this->fStatus = 'aktif';
    }

    private function grantDefaultPerempuanMenuAccess(User $user): void
    {
        if ($user->bidang_slug !== 'perempuan' || $user->hasAnyMenuPermissions(array_keys(User::SPLIT_MENU_PERMISSIONS))) {
            return;
        }

        foreach (User::SPLIT_MENU_PERMISSIONS as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $user->givePermissionTo(array_values(User::SPLIT_MENU_PERMISSIONS));
    }

    public function render()
    {
        return view('livewire.pengaturan.user-management')
            ->layout('components.layouts.app.sidebar');
    }
}
