<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $dayNamesVietnamese = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
        if ($request->has('date')) {
            $date = Carbon::parse($request->get('date'));
        } else {
            $date = Carbon::now();
        }
        $currentYear = $date->year;
        $currentMonth = $date->month;
        $totalDays = $date->daysInMonth;
        $title = ['Chức vụ', 'Họ và tên', 'Biệt danh', 'Số ngày làm việc', 'Tổng thời gian nghỉ', 'Tổng thời gian làm việc', 'Lương dự kiến', 'Ngày'];
        for ($i = 1; $i <= $totalDays; $i++) {
            $date1 = Carbon::createFromDate($currentYear, $currentMonth, $i);
            $dayOfWeek = $date1->dayOfWeek;
            $title[] = $dayNamesVietnamese[$dayOfWeek] . ', ' . $i . ' tháng ' . $currentMonth;
        }

        $userList = User::where('role', 2)->get();
        $attendanceData = [$title];
        foreach ($userList as $user) {
            $user->reportMonth($currentYear, $currentMonth);
            $csvData = $user->csvData();

            $data = [
                $user->position,
                $user->name,
                $user->full_name,
                $user->month_days,
                formatHoursMinutes($user->month_break_hours),
                formatHoursMinutes($user->month_working_minutes),
                formatCurrencyWithText($user->month_salary),
                ['Giờ vào', 'Giờ tan', 'Thời gian nghỉ', 'Thời gian làm'],
            ];
            $data = array_merge($data, $csvData);
            $attendanceData[] = $data;
        }

        return $attendanceData;
    }

    public function today(){
        $userList = User::where('role', 2)->get();
        foreach ($userList as $user) {
            $data = $user->reportday();

            $attendanceData[] = [
                'user' => $user,
                'report' => $data
            ];
        }
        return Inertia::render('Admin/Today/Index', [
            'attendanceData' => $attendanceData
        ]);
    }

    public function wage(){
        $userList = User::where('role', 2)->get();
        foreach ($userList as $user) {
            $data = $user->reportMonth();

            $attendanceData[] = [
                'user' => $user,
                'report' => $data
            ];
        }
        return Inertia::render('Admin/Wage/Index', [
            'attendanceData' => $attendanceData
        ]);
    }
}
