<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index()
    {
        $admin = User::where('role', config('user.ROLE.ADMIN'))->first();
        $employee = User::where('role', 2)->get(['id', 'name', 'full_name']);

        return Inertia::render('Admin/Attendance/Index', [
            'shopCreatedAt' => $admin->created_at,
            'positions' => config('const.POSITION'),
            'employee' => $employee,
        ]);
    }

    public function exportCsv()
    {
        return Inertia::render('Admin/Attendance/ExportCsv');
    }

    public function report(Request $request)
    {
        $dayNamesJapanese = ['日', '月', '火', '水', '木', '金', '土'];
        if ($request->has('date')) {
            $date = Carbon::parse($request->get('date'));
        } else {
            $date = Carbon::now();
        }
        $currentYear = $date->year;
        $currentMonth = $date->month;
        $totalDays = $date->daysInMonth;
        $title = ['役職', '本名', 'スタッフ名', '出勤日数', '合計休憩時間', '合計勤務時間', '日付'];
        for ($i = 1; $i <= $totalDays; $i++) {
            $date1 = Carbon::createFromDate($currentYear, $date->month, $i);
            $dayOfWeek = $date1->dayOfWeek;
            $title[] = $currentYear.'年'.$currentMonth.'月'.$i.'日（'.$dayNamesJapanese[$dayOfWeek].'）';
        }

        $userList = User::where('role', 2)->get();
        $attendanceData = [$title];
        foreach ($userList as $user) {
            $user->reportMonth($currentYear, $currentMonth);
            $csvData = $user->csvData();

            // $result = array_filter(array_values(config('const.POSITION')), function ($item) use ($user) {
            //     return $item['ID'] == $user->position;
            // });
            // $positionName = $result[$user->position - 1]['NAME'];

            $data = [
                $user->position,
                $user->name,
                $user->full_name,
                $user->month_days,
                formatHoursMinutes($user->month_break_hours),
                formatHoursMinutes($user->month_working_minutes),
                ['出勤', '退勤', '休憩', '勤務時間'],
            ];
            $data = array_merge($data, $csvData);
            $attendanceData[] = $data;
        }

        return $attendanceData;
    }
}
