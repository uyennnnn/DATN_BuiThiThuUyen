<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Jobs\SendPasswordMailToEmployee;
use App\Models\Attendance;
use App\Models\Option;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $totalUser = User::employee()->count();

        return Inertia::render('Admin/Employee/Index', [
            'totalUser' => $totalUser,
            'positions' => config('const.POSITION'),
            'postionType' => $request->get('position') ? $request->get('position') : 'all',
        ]);
    }

    public function getListUser(Request $request)
    {
        $user = new User();

        return [
            'data' => $user->getUsersByPosition($request->get('position')),
        ];
    }

    public function create()
    {
        return Inertia::render('Admin/Employee/Editor', [
            'positions' => config('const.POSITION'),
        ]);
    }

    public function store(CreateEmployeeRequest $request)
    {
        $user = User::create($request->all());
        $password = $request->password;

        $domain = request()->getHost();
        SendPasswordMailToEmployee::dispatch($domain, $user->id, $password);

        return redirect()->route('users.success', ['type' => 'create']);
    }

    public function alertSuccess(Request $request)
    {
        return Inertia::render('Admin/Employee/Success', [
            'type' => $request->input('type'),
        ]);
    }

    public function edit($user_id)
    {
        $user = User::find($user_id);
        $config = Option::getConfig();

        return Inertia::render('Admin/Employee/Editor', [
            'positions' => config('const.POSITION'),
            'user' => $user,
            'storeName' => $config['name'] ?? null,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, $user_id)
    {
        $user = User::find($user_id);

        $user->update($request->all());
        $user->sendAccountUpdateNotification($request->all());

        return redirect()->route('users.success', ['type' => 'update']);
    }

    public function delete(Request $request)
    {
        $employee = User::find($request->input('id'));
        $config = Option::getConfig();

        return Inertia::render('Admin/Employee/Delete', [
            'employee' => $employee,
            'storeName' => $config['name'] ?? null,
        ]);
    }

    public function destroy($user_id)
    {
        User::find($user_id)->delete();

        return redirect()->route('users.success', ['type' => 'delete']);
    }

    public function getAttendancesByUserIdAndDay(User $user, $day)
    {
        $query = $user->attendances();

        if (Option::get('setting_night_stamp') == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            $day = date('Y-m-d H:i:s', strtotime($day.' +6 hours'));
        }

        $query->where('date_time', '>=', $day);
        $nextDay = date('Y-m-d H:i:s', strtotime($day.'+1 day'));
        $query->where('date_time', '<', $nextDay);

        $cloneQuery = clone $query;
        $checkInRecord = $query->where('action_type', 'checkIn')->first();
        $checkOutRecord = $cloneQuery->where('action_type', 'checkOut')->first();

        return [
            'checkIn' => $this->convertToHourMinuteFormat($checkInRecord?->date_time),
            'checkOut' => $this->convertToHourMinuteFormat($checkOutRecord?->date_time),
        ];
    }

    public function convertToHourMinuteFormat($time)
    {
        if (! $time) {
            return;
        }
        $date = new DateTime($time);

        return $date->format('H:i');
    }

    public function updateAttendancesByUserIdAndDay(Request $request, User $user, $day)
    {
        $cloneDay = $day;

        $query = $user->attendances();

        if (Option::get('setting_night_stamp') == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            $day = date('Y-m-d H:i:s', strtotime($day.' +6 hours'));
        }

        $query->where('date_time', '>=', $day);
        $nextDay = date('Y-m-d H:i:s', strtotime($day.'+1 day'));
        $query->where('date_time', '<', $nextDay);

        $cloneQuery = clone $query;

        $checkInRecord = $query->where('action_type', 'checkIn')->first();
        if (! $checkInRecord) {
            $checkInRecord = new Attendance;
        }
        $checkInRecord->user_id = $user->id;
        $checkInRecord->action_type = 'checkIn';
        $checkInRecord->date_time = $this->calculateTimeFromDayHourAndMinute($cloneDay, $request->checkIn);
        $checkInRecord->is_edited = true;
        $checkInRecord->save();

        $checkOutRecord = $cloneQuery->where('action_type', 'checkOut')->first();
        if (! $checkOutRecord) {
            $checkOutRecord = new Attendance;
        }
        $checkOutRecord->user_id = $user->id;
        $checkOutRecord->action_type = 'checkOut';
        $checkOutRecord->date_time = $this->calculateTimeFromDayHourAndMinute($cloneDay, $request->checkOut);
        $checkOutRecord->is_edited = true;
        $checkOutRecord->save();

        return response()->json(['success' => true]);

    }

    public function calculateTimeFromDayHourAndMinute($day, $hourMinute)
    {
        $time = "$day $hourMinute:00";
        $time = strtotime($time);
        if (Option::get('setting_night_stamp') == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            if (date('H', $time) < 6) {
                $time += 24 * 3600;
            }
        }
        $time = date('Y-m-d H:i:s', $time);

        return $time;

    }
}
