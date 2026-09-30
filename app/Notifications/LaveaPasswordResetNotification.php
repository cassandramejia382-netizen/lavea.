<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LaveaPasswordResetNotification extends Notification
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Your LAVEA staff account')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A LAVEA staff account has been created for you. Use the link below to set or reset your password.')
            ->action('Set Your Password', $url)
            ->line('If you did not request this, you can ignore this email.');
    }
}
