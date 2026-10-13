<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('print-receipt', function (User $user, Payment $payment): Response {
            if ($user->role === 'admin') {
                return $payment->status === 'Confirmed' ? Response::allow() : Response::deny('Only confirmed payments have official receipts.');
            }

            if ($user->role === 'manager') {
                return $payment->status === 'Confirmed'
                    ? Response::allow()
                    : Response::denyAsNotFound();
            }

            if (in_array($user->role, ['customer'], true)
                && $user->customerProfile?->id === $payment->order?->customer_id) {
                return $payment->status === 'Confirmed'
                    ? Response::allow()
                    : Response::deny('Only confirmed payments can be printed.');
            }

            return Response::denyAsNotFound();
        });

        ResetPassword::createUrlUsing(function (User $notifiable, string $token): string {
            return rtrim(config('app.url'), '/').route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false);
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify your LAVEA email address')
                ->greeting('Hello '.$notifiable->name.',')
                ->line('Please verify your email address to finish setting up your LAVEA customer account.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create this account, you can ignore this email.');
        });
    }
}
