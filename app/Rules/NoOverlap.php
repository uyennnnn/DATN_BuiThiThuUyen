<?php

namespace App\Rules;

use App\Models\Option;
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
            $settingNightStamp = Option::get('setting_night_stamp', null, request()->getHost());
            if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_STAMP.ON')) {
                $fail('深夜の時間設定は6時より前の時間帯を入力してください。');
            } else {
                $fail('深夜の時間設定は0時より前の時間帯を入力してください。');
            }
        }

        if ($this->checkTimeOverlap($this->salaryBase, $this->salaryNight) ||
            $this->checkTimeOverlap($this->salaryBase, $this->salaryOvertime) ||
            $this->checkTimeOverlap($this->salaryNight, $this->salaryOvertime)) {
            $fail('24 時間以内に期間を重複させることはできません。');
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
        $settingNightStamp = Option::get('setting_night_stamp', null, request()->getHost());

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
