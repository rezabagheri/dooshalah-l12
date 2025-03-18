<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public $subject;
    public $message;
    public $actionUrl;
    public $actionText;

    public function __construct(string $subject, string $message, ?string $actionUrl = null, string $actionText = 'View Details')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
                    ->subject($this->subject)
                    ->line('Dear ' . $notifiable->display_name . ',')
                    ->line($this->message);

        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        return $mail->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
