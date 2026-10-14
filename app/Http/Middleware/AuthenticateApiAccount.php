<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('web');
        Auth::guard('web')->onceBasic('email');
        $user = $request->user();
        abort_unless(in_array($user->role, User::LOGIN_ROLES, true), 403);
        abort_if($user->role === 'customer' && ! $user->hasVerifiedEmail(), 403, 'Verify your email before accessing this API.');

        return $next($request);
    }
}
