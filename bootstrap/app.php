<?php

use App\Http\Middleware\CheckMenuAccess;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureProfileCompleted;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'profile.completed' => EnsureProfileCompleted::class,
            'menu' => CheckMenuAccess::class,
            'role' => CheckRole::class,
        ]);

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            return route('member.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 403 && $request->is('admin*')) {
                if (auth()->check() && !auth()->user()->isAdmin()) {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/login')->with('error', 'Sesi Anda telah dikeluarkan karena Anda tidak memiliki akses ke halaman admin.');
                }
            }
        });
    })->create();
