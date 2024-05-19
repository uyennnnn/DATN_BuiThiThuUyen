<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

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

        $workingTime = convertMinutesToHoursMinutes($user->month_working_minutes);

        $breakData = $user->reportDay();

        $todayTotalBreakTime = convertMinutesToHoursMinutes($breakData->breakTime);
        $checkInTime = formatTimeToHoursAndMinutes($breakData->checkIn);

        $todayBreakTimes = [];
        foreach ($breakData->breaks as $break) {
            $todayBreakTimes[] = convertMinutesToHoursMinutes($break['minute']);
        }

        return Inertia::render('User/Attendance/Home', [
            'mode' => $user->getAttendanceStatus(),
            'salary' => $user->month_salary,
            'dayWorking' => $user->month_days,
            'workingTime' => $workingTime,
            'shopName' => Option::get('name'),
            'todayBreakTimes' => $todayBreakTimes,
            'checkInTime' => $checkInTime,
            'todayTotalBreakTime' => $todayTotalBreakTime,
            'tempBreakTime' => $user->startBreakTime(),
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
        $url = URL::temporarySignedRoute(
            'user.attendance', now()->addMinutes(10)
        );
        Log::debug($url);

        return redirect($url);
    }
}
