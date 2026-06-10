<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $this->canonicalRole((string) $user->role);
        $allowedRoles = array_map(fn ($role) => $this->canonicalRole((string) $role), $roles);

        $hasAccess = in_array($userRole, $allowedRoles, true);

        if (! $hasAccess && method_exists($user, 'hasAnyRole')) {
            $hasAccess = $user->hasAnyRole($allowedRoles);
        }

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }

    private function canonicalRole(string $role): string
    {
        return match (mb_strtolower(trim($role))) {
            'admin_dpd', 'admin', 'super-admin', 'super admin', 'pengurus_dpd', 'dpd' => 'admin_dpd',
            'pengurus_bidang', 'pengurus' => 'pengurus_bidang',
            default => $role,
        };
    }
}
