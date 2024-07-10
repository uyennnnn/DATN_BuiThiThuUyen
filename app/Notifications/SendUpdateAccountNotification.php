<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class SendUpdateAccountNotification extends Notification
{
    use Queueable;

    public $email;

    public $password;

    public $name;

    /**
     * Create a new notification instance.
     */
    public function __construct($email, $password, $name)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
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
        $store_name = Shop::getShopName();

        return (new MailMessage)
            ->subject('[SpiSpi] Thông báo mật khẩu')
            ->line($store_name)
            ->line('Xin chào '.$this->name)
            ->line('Thông tin tài khoản của bạn trên hệ thông chấm công đã được cập nhật.')
            ->line('Để đăng nhập, bạn cần địa chỉ email và mật khẩu.')
            ->line('Thông tin đăng nhập cần thiết như sau:')
            ->line('— — — — — — — — —')
            ->line('【Email】')
            ->line($this->email)
            ->line('【Mật khẩu】')
            ->line($this->password)
            ->line('— — — — — — — — —')
            ->line('Nếu có thắc mắc, vui lòng liên hệ với quản lý cửa hàng của bạn.')
            ->line(Lang::get('------------'))
            ->line('【Hệ thống chấm công SpiSpi】');
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
