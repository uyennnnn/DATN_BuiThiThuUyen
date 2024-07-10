<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'setting_stamp', 'setting_night_stamp', 'setting_minute', 'rounding_type'];

    static function getShopName()
    {
        $shop = Shop::first();
        if ($shop) {
            return $shop->name;
        } else {
            return 'Shop not found';
        }
    }

    static function getSettingNightStamp()
    {
        $shop = Shop::first();
        if ($shop) {
            return $shop->setting_night_stamp;
        } else {
            return 'Shop not found';
        }
    }
}
