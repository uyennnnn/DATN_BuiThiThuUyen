<?php

namespace App\Notifications;

use App\Models\Shop;
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
        $store_name = Shop::getShopName();
    
        return (new MailMessage)
            ->subject('[SpiSpi] Thông báo mật khẩu')
            ->line($store_name)
            ->line('Xin chào '.$this->user->name)
            ->line('Quy trình cấp tài khoản của bạn trên hệ thống chấm công đã được thực hiện.')
            ->line('Để đăng nhập, bạn cần địa chỉ email và mật khẩu.')
            ->line('Thông tin cần thiết để đăng nhập như sau:')
            ->line('— — — — — — — — —')
            ->line('【Email】')
            ->line($this->user->email)
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
