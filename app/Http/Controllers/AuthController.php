<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\SystemNotification;
use Closure;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $credentials['email'] = $this->accountEmail($credentials['email']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard(Auth::user());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $details = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'bail', 'required', 'email', 'max:255', 'unique:users,email',
                function (string $attribute, mixed $value, Closure $fail): void {
                    foreach ([User::class, Customer::class, Staff::class] as $model) {
                        if ($model::whereRaw('LOWER(TRIM(email)) = ?', [$value])->exists()) {
                            $fail('This email is already associated with an existing account or customer/staff record. Sign in, reset your password, or contact the shop for help.');

                            return;
                        }
                    }
                },
            ],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.unique' => 'This email is already associated with an existing account or customer/staff record. Sign in, reset your password, or contact the shop for help.',
        ]);

        $user = DB::transaction(function () use ($details) {
            $user = User::create([
                'name' => $details['name'],
                'email' => $details['email'],
                'password' => $details['password'],
                'role' => 'customer',
            ]);

            Customer::create([
                'email' => $details['email'],
                'name' => $details['name'],
                'phone' => $details['phone'],
                'address' => $details['address'],
            ]);

            return $user;
        });

        foreach (User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new SystemNotification(
                'New Customer',
                $user->name.' created a customer account.',
                route('admin.customers.index')
            ));
        }

        Auth::login($user);
        $request->session()->regenerate();

        try {
            event(new Registered($user));

            return redirect()->route('verification.notice')->with('status', 'Your account is ready. Check your email for the verification link.');
        } catch (Exception $exception) {
            report($exception);

            return redirect()->route('verification.notice')->with('email_error', 'Your account was created, but we could not send the verification email. Please try again.');
        }
    }

    public function verificationNotice()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.verify-email');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        try {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('status', 'A new verification link has been sent to your email address.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('email_error', 'We could not send the email right now. Please try again later.');
        }
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('customer.dashboard')->with('status', 'Your email has been verified.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $details = $request->validate(['email' => ['required', 'email']]);
        $details['email'] = $this->accountEmail($details['email']);

        try {
            Password::sendResetLink($details);
        } catch (Exception $exception) {
            report($exception);

            return back()->with('status', 'If an account exists for that email, a password reset link will be sent.');
        }

        return back()->with('status', 'If an account exists for that email, a password reset link will be sent.');
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $details = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $details['email'] = $this->accountEmail($details['email']);

        $status = Password::reset($details, function ($user, $password) {
            $user->password = $password;
            $user->remember_token = Str::random(60);
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
        }

        return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function normalizeEmail(Request $request): void
    {
        if (! is_string($request->input('email'))) {
            return;
        }

        $request->merge(['email' => Str::lower(trim($request->input('email')))]);
    }

    private function accountEmail(string $email): string
    {
        return User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->value('email') ?? $email;
    }

    private function redirectToDashboard($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        if ($user->role === 'customer' || $user->role === 'user') {
            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('customer.dashboard');
        }

        abort(403);
    }
}
