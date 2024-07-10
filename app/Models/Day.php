<?php

namespace App\Models;

use Carbon\Carbon;
use HolidayJp\HolidayJp;
use Illuminate\Support\Facades\Log;

class Day
{
    private $data;

    public $day;

    public $checkIn;

    public $checkOut;

    public $breakTime;

    public $workTime;

    public $salary;

    public $workingHours;

    public $breaks = [];

    public $ranges;

    public $isCheckInEdited = false;

    public $isCheckOutEdited = false;

    public function __construct($date, $data)
    {

        $this->day = $date;
        $this->data = $data;
    }

    public function parse()
    {
        $data = $this->data; 
        $breakData = [];
        foreach ($data as $item) {
            $key = $item->action_type;
            switch ($key) {
                case 'startBreak':
                case 'endBreak':
                    $breakData[] = $item;
                    break;

                default:
                    $this->$key = $item['date_time'];
                    break;
            }
        }

        foreach ($data as $item) {
            $key = $item->action_type;
            if ($item->action_type == 'checkIn') {
                $this->isCheckInEdited = $item->is_edited;
            }

            if ($item->action_type == 'checkOut') {
                $this->isCheckOutEdited = $item->is_edited;
            }
        }

        foreach ($breakData as $item) {
            if ($item['action_type'] === 'startBreak') {
                $group = [];
                $group['startBreak'] = $item->date_time;
            } elseif ($item['action_type'] === 'endBreak') {
                $group['endBreak'] = $item->date_time;
                $this->breaks[] = $group;
            }
        }

        $this->breakTime = 0;
        foreach ($this->breaks as &$break) {
            $startBreak = Carbon::parse($break['startBreak']);
            $endBreak = Carbon::parse($break['endBreak']);
            $breakMinute = $startBreak->diffInMinutes($endBreak);
            $break['minute'] = $breakMinute;
            $this->breakTime += $breakMinute;
        }

    }

    public function parseRanges()
    {
        $data = $this->data;
        $this->ranges = [];

        foreach ($data as $item) {
            if ($item['action_type'] === 'checkIn' || $item['action_type'] === 'endBreak') { 
                $group = [];
                $group[] = $item->date_time;
            } else {
                $group[] = $item->date_time;
                $this->ranges[] = $group;
            }
        }
    }

    public function calculateWorkingHoursFullTime()
    {
        $totalWorkingHours = 0;
        foreach ($this->ranges as $range) {
            if (count($range) >= 2) {
                $startWorkingTime = Carbon::parse(formatDate($range[0], 'Y-m-d H:i:00'));
                $endWorkingTime = Carbon::parse(formatDate($range[1], 'Y-m-d H:i:00'));

                if ($startWorkingTime->lessThanOrEqualTo($endWorkingTime)) {
                    $workingHours = $endWorkingTime->diffInMinutes($startWorkingTime);
                    $totalWorkingHours += $workingHours;
                }
            }
        }

        return $totalWorkingHours;
    }

    public function calculateWorkingHoursPartime($salary)
    {
        if (! $salary) {
            return;
        }
        $totalWorkingHours = 0;

        $startTimeString = $this->day.' '.$salary['time_start'];
        $startTime = Carbon::parse($startTimeString);

        $endTimeString = $this->day.' '.$salary['time_end'];

        if ($salary['time_end'] < $salary['time_start']) {
            $endTime = Carbon::parse($endTimeString)->addDays(1);
        } else {
            $endTime = Carbon::parse($endTimeString);
        }

        foreach ($this->ranges as $range) {
            // Log::info($range);
            if (count($range) != 2) {
                continue;
            }
            $startWorkingTime = Carbon::parse(formatDate($range[0], 'Y-m-d H:i:00'));
            $endWorkingTime = Carbon::parse(formatDate($range[1], 'Y-m-d H:i:00'));

            if ($startWorkingTime->lessThanOrEqualTo($endTime)
                && $endWorkingTime->greaterThanOrEqualTo($startTime)
            ) {
                $startWorkingTime = $startWorkingTime->max($startTime);
                $endWorkingTime = $endWorkingTime->min($endTime);
                $workingHours = $endWorkingTime->diffInMinutes($startWorkingTime);
                $totalWorkingHours += $workingHours;
            }
         
        }

        // Log::info("totalWorkingHours".$totalWorkingHours);

        return $totalWorkingHours;
    }

    public static function isWeekendOrHoliday($day)
    {
        $carbonDay = Carbon::parse($day);

        return $carbonDay->isWeekend() || HolidayJp::isHoliday($carbonDay);
    }
}
