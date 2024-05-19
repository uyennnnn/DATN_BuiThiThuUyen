<?php

namespace App\Notifications;

use App\Models\Option;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class SendPasswordNotification extends Notification
{
    public $password;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($password, $user)
    {
        $this->password = $password;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $store_name = Option::get('name', 'shop A', request()->getHost());

        return (new MailMessage)
            ->subject('[SpiSpi] パスワード通知')
            ->line($store_name)
            ->line($this->user->name.'様')
            ->line('SpiSpi勤怠にて'.$this->user->name.'様のアカウント発行手続きが行われました。')
            ->line('ログイン時にはメールアドレスとパスワードが必要になります。')
            ->line('ログイン時に必要な情報は以下です。')
            ->line('— — — — — — — — —')
            ->line('【メールアドレス】')
            ->line($this->user->email)
            ->line('【パスワード】')
            ->line($this->password)
            ->line('— — — — — — — — —')
            ->line('ご不明な点がございましたら店舗にご連絡ください。')
            ->line('このご連絡に心当たりがない場合は、このメールを無視してください。')
            ->line(Lang::get('------------'))
            ->line('【SpiSpi勤怠】');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
