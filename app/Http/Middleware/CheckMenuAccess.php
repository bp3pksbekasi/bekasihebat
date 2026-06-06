<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, ...$menus)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $allowed = collect($menus)
            ->filter()
            ->contains(fn (string $menu): bool => method_exists($user, 'canAccessMenu') && $user->canAccessMenu($menu));

        if (! $allowed) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
