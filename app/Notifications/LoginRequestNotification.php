<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoginRequestNotification extends Notification
{
    use Queueable;

    protected $name;
    protected $email;
    protected $reason;
    protected $plainToken;
    protected $kinds;

    public function __construct($name, $email, $reason, $plainToken, $kinds)
    {
        $this->name = $name;
        $this->email = $email;
        $this->reason = $reason;
        $this->plainToken = $plainToken;
        $this->kinds = $kinds;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('要求通知:'.$this->kinds)
            ->line('ユーザー名: ' . $this->name)
            ->line('メールアドレス: ' . $this->email)
            ->line('閲覧理由: ' . $this->reason)
            ->line('認証コード：'.$this->plainToken)
            ->line('ログイン要求に対応してください。');
    }
}
