<?php

/**
 * Convert float numbers to hours and minutes
 */
if (! function_exists('formatHoursMinutes')) {
    function formatHoursMinutes($number)
    {
        $hours = floor($number / 60);
        $minutes = $number - $hours * 60;

        return $hours.' giờ '.$minutes.' phút ';
    }
}

if (! function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        return number_format($amount, 0, ',', '.') ;
    }
}

if (! function_exists('formatCurrencyWithText')) {
    function formatCurrencyWithText($amount)
    {
        return number_format($amount, 0, ',', '.') . ' VND';
    }
}

/**
 * Calculate minutes from number
 */
if (! function_exists('convertMinutesToHoursMinutes')) {
    function convertMinutesToHoursMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $remaining_minutes = $minutes % 60;

        return ['hours' => $hours, 'minutes' => $remaining_minutes];
    }
}

if (! function_exists('formatTimeToHoursAndMinutes')) {
    function formatTimeToHoursAndMinutes($timeString)
    {
        $timestamp = strtotime($timeString);
        $hours = date('H', $timestamp);
        $minutes = date('i', $timestamp);

        return ['hours' => $hours, 'minutes' => $minutes];
    }
}

if (! function_exists('formatDate')) {
    function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        try {
            if (! $date) {
                return;
            }

            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format($format);
        } catch (\Exception $e) {
            $date = date($format, strtotime($date));
        }

        return $date;
    }
}

use App\Models\Option;
use App\Models\Shop;

if (! function_exists('roundingDateTime')) {
    function roundingDateTime($dateTime)
    {
        if (! $dateTime) {
            return $dateTime;
        }

        $domain = request()->getHost();
        $settingStamp = Option::get('setting_stamp', null, $domain);
        if ($settingStamp == config('const.SHOP_CONFIG.SETTINNG_STAMP.ON')) {
            $settingMinute = Option::get('setting_minute', null, $domain);
            $roundingType = Option::get('rounding_type', null, $domain);

            $minutes = formatDate($dateTime, 'i');
            if ($roundingType == config('const.SHOP_CONFIG.ROUNDING_TYPE.UP')) {
                if ($minutes % $settingMinute > 0) {
                    $minutes = sprintf('%02d', $minutes - ($minutes % $settingMinute) + $settingMinute);

                    if ($minutes == 60) {
                        $minutes = sprintf('%02d', 0);
                        $hours = sprintf('%02d', formatDate($dateTime, 'H') + 1);
                        $dateTime = formatDate($dateTime, "Y-m-d $hours:$minutes:00");
                    } else {
                        $dateTime = formatDate($dateTime, "Y-m-d H:$minutes:00");
                    }
                }
            } elseif ($roundingType == config('const.SHOP_CONFIG.ROUNDING_TYPE.DOWN')) {
                if ($minutes % $settingMinute > 0) {
                    $minutes = sprintf('%02d', $minutes - ($minutes % $settingMinute));
                    $dateTime = formatDate($dateTime, "Y-m-d H:$minutes:00");
                }
            }
        }

        return formatDate($dateTime, 'Y-m-d H:i:00');
    }
}

if (! function_exists('getExpiredBySettingNightStamp')) {
    function getExpiredBySettingNightStamp()
    {
        $settingNightStamp = Shop::getSettingNightStamp();
        if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            $expired = now()->setTime(6, 0, 0);
            if (now() >= $expired) {
                $expired = now()->setTime(6, 0, 0)->addDays(1);
            }
        } else {
            $expired = now()->setTime(24, 0, 0);
        }

        return $expired;
    }
}
