<?php

namespace App\Rules;

use App\Models\Shop;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlap implements ValidationRule
{
    private $salaryBase;

    private $salaryNight;

    private $salaryOvertime;

    public function __construct($salaryBase, $salaryNight, $salaryOvertime)
    {
        $this->salaryBase = $salaryBase;
        $this->salaryNight = $salaryNight;
        $this->salaryOvertime = $salaryOvertime;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ($this->exceedTime()) {
            $settingNightStamp =  Shop::getSettingNightStamp();
            if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_STAMP.ON')) {
                $fail('Vui lòng nhập thời gian trước 6 giờ sáng cho cài đặt thời gian ban đêm.');
            } else {
                $fail('Vui lòng nhập thời gian trước 0 giờ cho cài đặt thời gian ban đêm.');
            }
        }

        if ($this->checkTimeOverlap($this->salaryBase, $this->salaryNight) ||
            $this->checkTimeOverlap($this->salaryBase, $this->salaryOvertime) ||
            $this->checkTimeOverlap($this->salaryNight, $this->salaryOvertime)) {
            $fail('Không thể có thời gian trùng lặp trong vòng 24 giờ.');
        }
    }

    public function checkTimeOverlap($timeRange1, $timeRange2)
    {
        if (! $timeRange1['time_start'] || ! $timeRange2['time_start']) {
            return false;
        }

        $start1 = $this->convertToMinutes($timeRange1['time_start']);
        $end1 = $this->convertToMinutes($timeRange1['time_end']);
        $start2 = $this->convertToMinutes($timeRange2['time_start']);
        $end2 = $this->convertToMinutes($timeRange2['time_end']);

        if ($start1 > $start2 && $start1 < $end2) {
            return true;
        }

        if ($end1 > $start2 && $end1 < $end2) {
            return true;
        }

        if ($start2 > $start1 && $start2 < $end1) {
            return true;
        }

        if ($end2 > $start1 && $end2 < $end1) {
            return true;
        }

        return false;
    }

    public function convertToMinutes($time)
    {
        [$hours, $minutes] = explode(':', $time);

        return intval($hours) * 60 + intval($minutes);
    }

    public function exceedTime()
    {
        $settingNightStamp = Shop::getSettingNightStamp();

        if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_STAMP.ON')) {
            $exceedTime = '05:59:59';
        } else {
            $exceedTime = '23:59:59';
        }

        if ($this->isTimeInRange($exceedTime, $this->salaryBase) ||
            $this->isTimeInRange($exceedTime, $this->salaryNight) ||
            $this->isTimeInRange($exceedTime, $this->salaryOvertime)
        ) {
            return true;
        }

        return false;
    }

    public function isTimeInRange($exceedTime, $range)
    {
        if (is_null($range['time_start']) || is_null($range['time_end'])) {
            return false;
        }

        $currentDate = date('Y-m-d');
        $timeStamp = strtotime("$currentDate $exceedTime");
        $startStamp = strtotime("$currentDate {$range['time_start']}");
        $endStamp = strtotime("$currentDate {$range['time_end']}");

        if ($exceedTime === '05:59:59' && $startStamp > strtotime("$currentDate 06:00")) {
            $timeStamp += 86400;
        }

        if ($endStamp < $startStamp) {
            $endStamp += 86400;
        }

        if ($timeStamp >= $startStamp && $timeStamp <= $endStamp) {
            return true;
        } else {
            return false;
        }
    }
}
