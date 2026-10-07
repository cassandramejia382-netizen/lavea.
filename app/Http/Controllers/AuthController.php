<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\SystemNotification;
use Closure;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.'
                ])
                ->onlyInput('email');
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
                'bail',
                'required',
                'email',
                'max:255',
                'unique:users,email',

                function (string $attribute, mixed $value, Closure $fail): void {
                    foreach ([User::class, Customer::class, Staff::class] as $model) {
                        if (
                            $model::whereRaw(
                                'LOWER(TRIM(email)) = ?',
                                [$value]
                            )->exists()
                        ) {
                            $fail(
                                'This email is already associated with an existing account or customer/staff record. Sign in, reset your password, or contact the shop for help.'
                            );

                            return;
                        }
                    }
                },
            ],

            'phone' => ['required', 'string', 'max:20'],

            'address' => ['required', 'string', 'max:255'],

            'password' => [
                'required',
                'confirmed',
                'min:8'
            ],
        ], [
            'email.unique' =>
                'This email is already associated with an existing account or customer/staff record. Sign in, reset your password, or contact the shop for help.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create User and Customer
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Notify Admins
        |--------------------------------------------------------------------------
        */

        foreach (User::where('role', 'admin')->get() as $admin) {

            $admin->notify(
                new SystemNotification(
                    'New Customer',
                    $user->name . ' created a customer account.',
                    route('admin.customers.index')
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Login Customer
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Generate 6-Digit Verification Code
        |--------------------------------------------------------------------------
        */

        try {

            $verificationCode = (string) random_int(100000, 999999);

            $user->update([
                'verification_code' => $verificationCode,
                'verification_code_expires_at' => now()->addMinutes(10),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Send Verification Code
            |--------------------------------------------------------------------------
            */

            Mail::raw(
                "Hello {$user->name},\n\n" .
                "Thank you for registering with LAVEA Laundry Shop.\n\n" .
                "Your email verification code is:\n\n" .
                "{$verificationCode}\n\n" .
                "This code will expire in 10 minutes.\n\n" .
                "If you did not create this account, you can ignore this email.\n\n" .
                "Thank you,\n" .
                "LAVEA Laundry Shop",
                function ($message) use ($user) {

                    $message
                        ->to($user->email)
                        ->subject('LAVEA Email Verification Code');
                }
            );

            return redirect()
                ->route('verification.notice')
                ->with(
                    'status',
                    'A 6-digit verification code has been sent to your email address.'
                );

        } catch (Exception $exception) {

            report($exception);

            return redirect()
                ->route('verification.notice')
                ->with(
                    'email_error',
                    'Your account was created, but we could not send the verification code. Please try again.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Verification Code Page
    |--------------------------------------------------------------------------
    */

    public function verificationNotice()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.verify-email');
    }

    /*
    |--------------------------------------------------------------------------
    | Verify 6-Digit Code
    |--------------------------------------------------------------------------
    */

    public function verifyCode(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'verification_code' => [
                'required',
                'digits:6'
            ],
        ]);

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Already Verified
        |--------------------------------------------------------------------------
        */

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('customer.dashboard')
                ->with(
                    'status',
                    'Your email is already verified.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Check Code
        |--------------------------------------------------------------------------
        */

        if (
            empty($user->verification_code) ||
            $user->verification_code !== $request->verification_code
        ) {
            return back()
                ->withErrors([
                    'verification_code' =>
                        'The verification code is incorrect.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Check Expiration
        |--------------------------------------------------------------------------
        */

        if (
            empty($user->verification_code_expires_at) ||
            now()->greaterThan($user->verification_code_expires_at)
        ) {
            return back()
                ->withErrors([
                    'verification_code' =>
                        'The verification code has expired. Please request a new code.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Email
        |--------------------------------------------------------------------------
        */

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();

        return redirect()
            ->route('customer.dashboard')
            ->with(
                'status',
                'Your email has been verified successfully!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Resend Verification Code
    |--------------------------------------------------------------------------
    */

    public function resendVerification(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('customer.dashboard');
        }

        try {

            $verificationCode = (string) random_int(100000, 999999);

            $user->update([
                'verification_code' => $verificationCode,
                'verification_code_expires_at' => now()->addMinutes(10),
            ]);

            Mail::raw(
                "Hello {$user->name},\n\n" .
                "Here is your new LAVEA email verification code:\n\n" .
                "{$verificationCode}\n\n" .
                "This code will expire in 10 minutes.\n\n" .
                "If you did not request this code, you can ignore this email.\n\n" .
                "Thank you,\n" .
                "LAVEA Laundry Shop",
                function ($message) use ($user) {

                    $message
                        ->to($user->email)
                        ->subject('LAVEA New Verification Code');
                }
            );

            return back()
                ->with(
                    'status',
                    'A new verification code has been sent to your email.'
                );

        } catch (Exception $exception) {

            report($exception);

            return back()
                ->with(
                    'email_error',
                    'We could not send a new verification code right now. Please try again later.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Old Verification Link Method
    |--------------------------------------------------------------------------
    |
    | We no longer use Laravel's email verification link.
    |
    */

    public function verifyEmail(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! Auth::user()->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice');
        }

        return redirect()
            ->route('customer.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $details = $request->validate([
            'email' => ['required', 'email']
        ]);

        $details['email'] = $this->accountEmail(
            $details['email']
        );

        try {

            Password::sendResetLink($details);

        } catch (Exception $exception) {

            report($exception);

            return back()
                ->with(
                    'status',
                    'If an account exists for that email, a password reset link will be sent.'
                );
        }

        return back()
            ->with(
                'status',
                'If an account exists for that email, a password reset link will be sent.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function showResetPassword(
        Request $request,
        $token
    ) {
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
            'password' => [
                'required',
                'confirmed',
                'min:8'
            ],
        ]);

        $details['email'] = $this->accountEmail(
            $details['email']
        );

        $status = Password::reset(
            $details,
            function ($user, $password) {

                $user->password = $password;

                $user->remember_token = Str::random(60);

                $user->save();

            }
        );

        if ($status === Password::PASSWORD_RESET) {

            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Your password has been reset. You can now sign in.'
                );
        }

        return back()
            ->withErrors([
                'email' =>
                    'This password reset link is invalid or has expired.'
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Email
    |--------------------------------------------------------------------------
    */

    private function normalizeEmail(Request $request): void
    {
        if (! is_string($request->input('email'))) {
            return;
        }

        $request->merge([
            'email' => Str::lower(
                trim($request->input('email'))
            )
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Find Account Email
    |--------------------------------------------------------------------------
    */

    private function accountEmail(string $email): string
    {
        return User::whereRaw(
            'LOWER(TRIM(email)) = ?',
            [$email]
        )->value('email') ?? $email;
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Redirect
    |--------------------------------------------------------------------------
    */

    private function redirectToDashboard($user)
    {
        if ($user->role === 'admin') {

            return redirect()
                ->route('admin.dashboard');
        }

        if ($user->role === 'staff') {

            return redirect()
                ->route('staff.dashboard');
        }

        if (
            $user->role === 'customer' ||
            $user->role === 'user'
        ) {

            if (! $user->hasVerifiedEmail()) {

                return redirect()
                    ->route('verification.notice');
            }

            return redirect()
                ->route('customer.dashboard');
        }

        abort(403);
    }
}