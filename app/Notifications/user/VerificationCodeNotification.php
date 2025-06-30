<?php

namespace App\Notifications\user;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    protected $plainToken;

    public function __construct($plainToken)
    {
        $this->plainToken = $plainToken;
    }

    public function via($notifiable)
    {
        return ['mail']; // メールで通知を送る
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('認証コードの送信')
            ->line("あなたの認証コードは: {$this->plainToken}")
            ->line('こちらを入力しメールアドレスを認証してください。');
    }
}
