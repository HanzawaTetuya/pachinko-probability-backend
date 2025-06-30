<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Console\View\Components\Line;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorAuthNotification extends Notification
{
    use Queueable;

    protected $twoFactorToken;
    protected $ipAddress;

    public function __construct($twoFactorToken, $ipAddress)
    {
        $this->twoFactorToken = $twoFactorToken;
        $this->ipAddress = $ipAddress;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('二段階認証コード')
            ->greeting('こんにちは、')
            ->line('下記の認証コードを使用して、ログインを完了してください。')
            ->line('認証コード: ' . $this->twoFactorToken)
            ->line('このコードは10分間有効です。')
            ->line('ログイン試行があったIPアドレス: ' . $this->ipAddress);
    }

    public function toArray($notifiable)
    {
        return [
            'two_factor_token' => $this->twoFactorToken,
            'ip_address' => $this->ipAddress,
        ];
    }
}