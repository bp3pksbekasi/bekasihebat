<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements \Filament\Models\Contracts\FilamentUser
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($this->isAdmin()) {
            if (session('logged_in_via_admin') !== true) {
                session(['logged_in_via_admin' => true]);
            }
            return true;
        }
        return false;
    }

    public const ROLE_ADMIN = 'admin_dpd';
    public const ROLE_BIDANG = 'pengurus_bidang';
    public const ROLE_KADER = 'kader';
    public const ROLE_DAPIL = 'dapil';

    public const BIDANG_OPTIONS = [
        'advokasi' => 'Advokasi Partai',
        'relawan' => 'Relawan & Saksi Nasional',
        'polhukam' => 'Politik, Hukum & Keamanan',
        'ekonomi' => 'Ekonomi, Keuangan & Industri',
        'dikkes' => 'Pendidikan & Kesehatan',
        'dakwah' => 'Pembangunan Keumatan & Dakwah',
        'perempuan' => 'Perempuan & Ketahanan Keluarga',
        'pemuda' => 'Kepemudaan',
        'tani' => 'Tani, Nelayan & Lingkungan Hidup',
        'naker' => 'Ketenagakerjaan',
        'senbud' => 'Seni & Budaya',
        'humas' => 'Hubungan Masyarakat',
        'komdigi' => 'Komunikasi Digital',
    ];

    public const BIDANG_MENUS = [
        'relawan' => ['infra-rtrw', 'sapa-warga', 'sisir-rw', 'kaderisasi', 'bedah-dapil', 'aspirasi'],
        'polhukam' => ['bedah-dapil', 'aspirasi'],
        'dakwah' => ['sisir-rw', 'event', 'aspirasi'],
        'perempuan' => [],
        'pemuda' => ['kaderisasi'],
        'humas' => ['sosial-media', 'event'],
        'komdigi' => ['sosial-media', 'event'],
    ];

    public const SPLIT_MENU_PERMISSIONS = [
        'rki' => 'menu.rki',
        'ksn' => 'menu.ksn',
    ];

    protected $fillable = [
        'name',
        'email',
        'nia',
        'kader_id',
        'role',
        'bidang_slug',
        'dapil',
        'kecamatan',
        'desa',
        'nomor_rw',
        'status',
        'last_login_at',
        'last_login_ip',
        'phone',
        'password',
        'member_number',
        'nik',
        'ttl_tempat',
        'ttl_tanggal',
        'jenis_kelamin',
        'foto_path',
        'birth_date',
        'gender',
        'address',
        'kelurahan_code',
        'affiliate_code',
        'profile_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'ttl_tanggal' => 'date',
            'profile_completed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getTtlLengkapAttribute(): string
    {
        if (!$this->ttl_tempat && !$this->ttl_tanggal) return '-';
        $tempat = $this->ttl_tempat ?? '';
        $tanggal = $this->ttl_tanggal?->translatedFormat('d F Y') ?? '';
        return trim($tempat . ', ' . $tanggal, ', ');
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(Kader::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return in_array(mb_strtolower((string) $this->role), [
            self::ROLE_ADMIN,
            'admin',
            'super-admin',
            'super admin',
            'pengurus_dpd',
            'dpd',
        ], true);
    }

    public function isBidang(): bool
    {
        return in_array(mb_strtolower((string) $this->role), [
            self::ROLE_BIDANG,
            'pengurus',
        ], true);
    }

    public function isKader(): bool
    {
        return mb_strtolower((string) $this->role) === self::ROLE_KADER;
    }

    public function isDapil(): bool
    {
        return mb_strtolower((string) $this->role) === self::ROLE_DAPIL || $this->hasRole('dapil');
    }

    public function getBidangLabelAttribute(): string
    {
        return self::BIDANG_OPTIONS[$this->bidang_slug] ?? '-';
    }

    public function canAccessMenu(string $menuSlug): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $universal = ['dashboard', 'sapa-warga', 'profil', 'event-view', 'sisir-rw'];

        if (in_array($menuSlug, $universal, true)) {
            return true;
        }

        if ($this->isDapil()) {
            $allowed = [
                'dashboard', 'kaderisasi', 'infra-rtrw', 'sisir-rw', 'sapa-warga',
                'sosial-media', 'rki', 'ksn', 'bedah-dapil', 'aspirasi',
                'program-kerja', 'event', 'event-view', 'profil'
            ];

            return in_array($menuSlug, $allowed, true);
        }

        if ($this->hasMenuPermission($menuSlug)) {
            return true;
        }

        if (array_key_exists($menuSlug, self::SPLIT_MENU_PERMISSIONS)
            && $this->hasAnyMenuPermissions(array_keys(self::SPLIT_MENU_PERMISSIONS))) {
            return false;
        }

        if ($menuSlug === 'program-kerja' && $this->isBidang()) {
            return true;
        }

        if ($menuSlug === 'event' && $this->isBidang()) {
            return true;
        }

        if ($this->isBidang() && $this->bidang_slug) {
            $allowedMenus = self::BIDANG_MENUS[$this->bidang_slug] ?? [];

            return in_array($menuSlug, $allowedMenus, true);
        }

        return false;
    }

    public static function menuPermissionName(string $menuSlug): string
    {
        return self::SPLIT_MENU_PERMISSIONS[$menuSlug] ?? ('menu.'.$menuSlug);
    }

    public function hasMenuPermission(string $menuSlug): bool
    {
        $permissionNames = array_unique([
            self::menuPermissionName($menuSlug),
            $menuSlug,
        ]);

        return $this->getAllPermissions()
            ->pluck('name')
            ->intersect($permissionNames)
            ->isNotEmpty();
    }

    /**
     * @param  array<int, string>  $menuSlugs
     */
    public function hasAnyMenuPermissions(array $menuSlugs): bool
    {
        foreach ($menuSlugs as $menuSlug) {
            if ($this->hasMenuPermission($menuSlug)) {
                return true;
            }
        }

        return false;
    }

    public function canAccessMenuFromConfig(array $menu): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (isset($menu['access_slugs']) && is_array($menu['access_slugs'])) {
            foreach ($menu['access_slugs'] as $slug) {
                if ($this->canAccessMenu((string) $slug)) {
                    return true;
                }
            }
        }

        if (isset($menu['slug'])) {
            return $this->canAccessMenu((string) $menu['slug']);
        }

        return false;
    }

    public function landingRouteName(): string
    {
        if ($this->isAdmin() || $this->isBidang() || $this->isDapil()) {
            return 'dashboard';
        }

        return 'member.dashboard';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByBidang($query, string $slug)
    {
        return $query->where('bidang_slug', $slug);
    }
}
