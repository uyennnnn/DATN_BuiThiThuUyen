<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'action_type',
        'date_time',
    ];

    public static function dailyData($userId, $date)
    {
        $firstDayOfMonth = $date->copy()->startOfMonth()->toDateString();
        $lastDayOfMonth = $date->copy()->endOfMonth()->toDateString();
        $totalDays = $date->daysInMonth;

        $attendances = Attendance::where([
            ['user_id', $userId],
            ['date_time', '>', $firstDayOfMonth],
            ['date_time', '<', $lastDayOfMonth],
        ])->select('date_time', 'action_type')->get();

        $attendanceData = [];
        foreach ($attendances as $attendance) {
            if ($attendance->action_type == 'checkIn' || $attendance->action_type == 'checkOut') {
                $dateTime = Carbon::parse($attendance->date_time);
                $attendanceData[$dateTime->day][$attendance->action_type] = $dateTime->format('H:i');
            }
        }
        // dd($attendanceData);

        $dailyData = [];
        for ($i = 1; $i <= $totalDays; $i++) {
            $baseData = ['', '', '', ''];
            if (isset($attendanceData[$i])) {
                $attendance = $attendanceData[$i];
                $baseData[0] = isset($attendance['checkIn']) ? $attendance['checkIn'] : '';
                $baseData[1] = isset($attendance['checkOut']) ? $attendance['checkOut'] : '';
            }
            $dailyData[] = $baseData;
        }

        return $dailyData;
    }

    public function scopeMonth($query, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if (Shop::getSettingNightStamp() == 1) {
            $startDate = Carbon::create($year, $month, 1)->setTime(6, 0, 0);
            $endDate = Carbon::create($year, $month, 1)->setTime(6, 0, 0)->addMonths();
        }

        return $query->whereBetween('date_time', [$startDate, $endDate]);
    }
}
