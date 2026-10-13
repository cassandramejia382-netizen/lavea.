<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (in_array($user->role, $roles, true) && in_array($user->role, User::LOGIN_ROLES, true)) {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        abort(403);
    }
}
