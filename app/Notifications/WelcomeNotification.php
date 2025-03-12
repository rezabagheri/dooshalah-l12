<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification // ShouldQueue رو حذف کن
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Doosh Chat!')
                    ->view('emails.welcome', ['notifiable' => $notifiable]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
