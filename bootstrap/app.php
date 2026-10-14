<?php

use App\Http\Middleware\PreventAuthenticatedPageCaching;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['lavea_theme']);
        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();

            return match ($user->role) {
                'admin' => route('admin.dashboard'),
                'manager' => route('manager.dashboard'),
                'customer' => route($user->hasVerifiedEmail() ? 'customer.dashboard' : 'verification.notice'),
                default => abort(403),
            };
        });

        $middleware->alias([
            'no-cache' => PreventAuthenticatedPageCaching::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $exception): bool => $request->is('api/*') || $request->expectsJson());
    })->create();
