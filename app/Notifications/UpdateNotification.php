<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateNotification extends Notification
{
    use Queueable;

    protected $type;  // 変更の種類

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = new MailMessage;
        $message->subject('プロフィール更新のお知らせ');

        switch ($this->type) {
            case 'name':
                $message->line('あなたの名前が正常に更新されました。');
                break;

            case 'email':  // 追加部分
                $message->line('あなたのメールアドレスが正常に更新されました。');
                break;

            case 'password':
                $message->line('あなたのパスワードが正常に更新されました。');
                break;

            default:
                $message->line('プロフィールに変更が加えられました。');
                break;
        }

        $message->line('この変更に心当たりがない場合は、直ちに管理者に連絡してください。');
        $message->line('ご利用いただきありがとうございます。');

        return $message;
    }
}

