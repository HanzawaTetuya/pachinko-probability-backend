<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginRequestNotification extends Notification
{
    use Queueable;

    protected $loginRequest;

    public function __construct($loginRequest)
    {
        $this->loginRequest = $loginRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $approveUrl = route('login.approve', $this->loginRequest->id);  // 許可リンク
        $denyUrl = route('login.deny', $this->loginRequest->id);        // 拒否リンク

        return (new MailMessage)
            ->subject('ログイン要求')
            ->line('ユーザーがログインしようとしています。')
            ->action('許可', $approveUrl)
            ->action('拒否', $denyUrl)
            ->line('このリクエストに対応してください。');
    }
}
