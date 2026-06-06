<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // #region debug-point C:profile-middleware-entry
        (function () use ($request, $user) {
            $u = 'http://127.0.0.1:7777/event';
            $s = 'bedah-dapil-redirect';
            $p = base_path('.dbg/bedah-dapil-redirect.env');
            if (is_file($p)) {
                $e = @file_get_contents($p) ?: '';
                preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                $u = $m1[1] ?? $u;
                $s = $m2[1] ?? $s;
            }
            @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'C', 'location' => 'EnsureProfileCompleted::handle', 'msg' => '[DEBUG] profile middleware entered', 'data' => ['route' => optional($request->route())->getName(), 'path' => $request->path(), 'user_id' => $user?->id, 'profile_completed_at' => $user?->profile_completed_at?->toIso8601String()], 'ts' => (int) round(microtime(true) * 1000)])]]));
        })();
        // #endregion

        if (! $user) {
            // #region debug-point C:profile-middleware-no-user
            (function () use ($request) {
                $u = 'http://127.0.0.1:7777/event';
                $s = 'bedah-dapil-redirect';
                $p = base_path('.dbg/bedah-dapil-redirect.env');
                if (is_file($p)) {
                    $e = @file_get_contents($p) ?: '';
                    preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                    preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                    $u = $m1[1] ?? $u;
                    $s = $m2[1] ?? $s;
                }
                @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'C', 'location' => 'EnsureProfileCompleted::handle', 'msg' => '[DEBUG] profile middleware redirecting to login', 'data' => ['route' => optional($request->route())->getName(), 'path' => $request->path()], 'ts' => (int) round(microtime(true) * 1000)])]]));
            })();
            // #endregion
            return redirect()->route('login');
        }

        if ($user->profile_completed_at !== null) {
            return $next($request);
        }

        if ($request->routeIs('profile.complete') || $request->routeIs('logout')) {
            return $next($request);
        }

        // #region debug-point C:profile-middleware-redirect-complete
        (function () use ($request, $user) {
            $u = 'http://127.0.0.1:7777/event';
            $s = 'bedah-dapil-redirect';
            $p = base_path('.dbg/bedah-dapil-redirect.env');
            if (is_file($p)) {
                $e = @file_get_contents($p) ?: '';
                preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                $u = $m1[1] ?? $u;
                $s = $m2[1] ?? $s;
            }
            @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'C', 'location' => 'EnsureProfileCompleted::handle', 'msg' => '[DEBUG] profile middleware redirecting to profile.complete', 'data' => ['route' => optional($request->route())->getName(), 'path' => $request->path(), 'user_id' => $user?->id], 'ts' => (int) round(microtime(true) * 1000)])]]));
        })();
        // #endregion

        return redirect()->route('profile.complete')
            ->with('info', 'Lengkapi profil Anda untuk melanjutkan.');
    }
}
