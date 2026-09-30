<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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
