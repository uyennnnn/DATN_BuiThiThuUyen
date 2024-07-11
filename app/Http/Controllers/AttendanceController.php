<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->hasValidSignature()) {
            // return Inertia::render('User/LinkExpired');
        }
        $user = Auth::user();
        /** @var \App\Models\User $user * */
        try {
            $user->reportMonth();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        $url = $request->fullUrl();
        $temporaryLink =  \App\Models\NewTemporaryLink::where('url', $url)
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->first();

        if (!$temporaryLink) {
            abort(404, 'Không tìm thấy link');
        }

        if ($temporaryLink->used && $temporaryLink->first_accessed_by !== $user->id) {
            abort(403, 'Link này đã được sử dụng.');
        }

        // Đánh dấu link là đã sử dụng và lưu người dùng đầu tiên truy cập
        if (!$temporaryLink->used) {
            $temporaryLink->used = true;
            $temporaryLink->first_accessed_by = $user->id;
            $temporaryLink->save();
        }

        $workingTime = convertMinutesToHoursMinutes($user->month_working_minutes);

        $breakData = $user->reportDay();
        // $totalWorkingHoursDay = convertMinutesToHoursMinutes(array_sum($breakData->workingHours));
        if (is_array($breakData->workingHours)) {
            $totalWorkingHoursDay = convertMinutesToHoursMinutes(array_sum($breakData->workingHours));
        } else {
            $totalWorkingHoursDay = "0"; // Giả sử định dạng giờ:phút
        }

        $todayTotalBreakTime = convertMinutesToHoursMinutes($breakData->breakTime);
        $checkInTime = formatTimeToHoursAndMinutes($user->getTodayCheckIn());

        $todayBreakTimes = [];
        foreach ($breakData->breaks as $break) {
            $todayBreakTimes[] = convertMinutesToHoursMinutes($break['minute']);
        }

        return Inertia::render('User/Attendance/Home', [
            'mode' => $user->getAttendanceStatus(),
            'salary' => formatCurrency($user->month_salary),
            'dayWorking' => $user->month_days,
            'workingTime' => $workingTime,
            'shopName' =>  Shop::getShopName(),
            'todayBreakTimes' => $todayBreakTimes,
            'checkInTime' => $checkInTime,
            'todayTotalBreakTime' => $todayTotalBreakTime,
            'tempBreakTime' => $user->startBreakTime(),
            'totalWorkingHoursDay' => $totalWorkingHoursDay,
        ]);
    }


    public function store(Request $request)
    {
        /** @var \App\Models\User $user * */
        $user = Auth::user();

        $result = $user->storeAttendance($request->action_type, $request->date_time);

        return redirect()->back()->with('result', $result);
    }

    public function nfc()
    {
        Log::debug('Scanning NFC');
        Log::debug(request()->fullUrl());
        Log::debug(Auth::user()->id);

        $userId = Auth::user()->id;
        $url = URL::temporarySignedRoute(
            'user.attendance', now()->addMinutes(10)
        );

        // Lưu link vào bảng mới
        \App\Models\NewTemporaryLink::create([
            'url' => $url,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'used' => false,
            'first_accessed_by' => null,
        ]);

        Log::debug($url);

        return redirect($url);
    }

    public function request(Request $request)
    {
        $dayNamesVietnamese = [ 'Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
        if ($request->has('date')) {
            $date = Carbon::parse($request->get('date'));
        } else {
            $date = Carbon::now();
        }
        $currentYear = $date->year;
        $currentMonth = $date->month;
        $totalDays = $date->daysInMonth;
        $title = ['Chức vụ', 'Họ và tên', 'Biệt danh', 'Số ngày làm việc', 'Tổng thời gian nghỉ', 'Tổng thời gian làm việc', 'Ngày'];
        for ($i = 1; $i <= $totalDays; $i++) {
            $date1 = Carbon::createFromDate($currentYear, $date->month, $i);
            $dayOfWeek = $date1->dayOfWeek;
            $title[] = $dayNamesVietnamese[$dayOfWeek]. ', '.$i. ' tháng '.$currentMonth ;
        }

        /** @var \App\Models\User $user * */
        $user = Auth::user();
        try {
            $user->reportMonth($currentYear, $currentMonth);
            $csvData = $user->csvData();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $attendanceData = [$title];

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
            ['Giờ vào', 'Giờ tan', 'Thời gian nghỉ', 'Thời gian làm'],
        ];
        $data = array_merge($data, $csvData);
        $attendanceData[] = $data;

        return $attendanceData;
        // $admin = User::where('role', config('user.ROLE.ADMIN'))->first();

        // return Inertia::render('User/Attendance/Request', [
        //     'shopCreatedAt' => $admin->created_at,
        // ]);

        // return Inertia::render('User/Attendance/Request', [
        // ]);
    }

    public function requestCreate()
    {
        
    }


}
