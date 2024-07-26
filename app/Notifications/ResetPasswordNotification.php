<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends ResetPassword
{
    public $token;

    public $user;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        $store_name = Shop::getShopName();
        $user_name = $this->user->name;
        $contact_phrase = $this->user->isAdmin() ? 'お問い合わせより' : '店舗に';

        return (new MailMessage)
            ->subject('[SpiSpi] Thông báo cập nhật mật khẩu')
            ->line($store_name)
            ->line('Xin chào '.$user_name)
            ->line('SpiSpi勤怠のパスワードリセット手続きが行われました。')
            ->line('以下のリンクよりパスワードの再設定をお願いします。 ')
            ->line('— — — — — — — — —')
            ->line('【パスワード再設定リンク】')
            ->line(Lang::get($url))
            ->line('— — — — — — — — —')
            ->line('ご不明な点がございましたら'.$contact_phrase.'ご連絡ください。')
            ->line('このご連絡に心当たりがない場合は、このメールを無視してください')
            ->line('------------')
            ->line('【SpiSpi勤怠】');
    }
}
