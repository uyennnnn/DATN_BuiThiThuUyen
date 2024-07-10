<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\SendPasswordNotification;
use App\Notifications\SendUpdateAccountNotification;
use HolidayJp\HolidayJp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const NOT_CHECKED_IN = 0;

    const WORKING = 1;

    const ON_BREAK = 2;

    const CHECKED_OUT = 3;

    public $month_days;

    public $month_salary;

    public $month_working_minutes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'full_name',
        'position',
        'salary_type',
        'salary_fixed',
        'salary_base',
        'salary_night',
        'salary_overtime',
        'set_holiday_salary',
        'set_saturday_salary',
        'set_sunday_salary',
        'set_celebrate_salary',
        'holiday_salary_base',
        'holiday_salary_night',
        'holiday_salary_overtime',
        'one_way_travel_expense',
        'nearest_train_station',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'salary_base' => 'array',
        'salary_night' => 'array',
        'salary_overtime' => 'array',
        'holiday_salary_base' => 'array',
        'holiday_salary_night' => 'array',
        'holiday_salary_overtime' => 'array',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $this->password;
        }
    }

    public function scopeEmployee($query)
    {
        return $query->where('role', 2);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this));
    }

    public function sendPasswordForEmployee($password): void
    {
        $this->notify(new SendPasswordNotification($password, $this));
    }

    public function isAdmin()
    {
        return $this->role === config('user.ROLE.ADMIN');
    }

    public function getDayWorking()
    {

        // dump($this->toArray());
    }

    private function getStartTimeWithSettingNightStamp()
    {
        $settingNightStamp = Shop::getSettingNightStamp();
        if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            if (now() < now()->setTime(6, 0, 0)) {
                return now()->subDay(1)->setTime(6, 0, 0);
            }

            return now()->setTime(6, 0, 0);
        }

        return Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
    }

    public function getAttendanceStatus()
    {
        $start = $this->getStartTimeWithSettingNightStamp();
        $end = Carbon::now()->format('Y-m-d H:i:s');
        $attendance = Attendance::where([
            ['user_id', $this->id],
            ['date_time', '>=', $start],
            ['date_time', '<=', $end],
        ])->orderBy('id', 'desc')->first();
        if (! $attendance) {
            return User::NOT_CHECKED_IN;
        }

        switch ($attendance->action_type) {
            case 'checkIn':
                return User::WORKING;
            case 'startBreak':
                return User::ON_BREAK;
            case 'endBreak':
                return User::WORKING;
            case 'checkOut':
                return User::CHECKED_OUT;
            default:
                break;
        }

        return User::NOT_CHECKED_IN;
    }

    public function storeAttendance($actionType, $time)
    {
        $attendanceStatus = $this->getAttendanceStatus();

        if ($attendanceStatus === User::CHECKED_OUT && $actionType === 'checkOut') {
            return false;
        }

        Attendance::create([
            'user_id' => $this->id,
            'action_type' => $actionType,
            'date_time' => date('Y-m-d H:i:s', $time / 1000),
        ]);

        return true;
    }

    public function startBreakTime()
    {
        $attendanceStatus = $this->getAttendanceStatus();
        if ($attendanceStatus === User::ON_BREAK) {
            $attendance = Attendance::where([
                ['user_id', $this->id],
                ['action_type', 'startBreak'],
            ])->orderBy('id', 'desc')->first();

            if ($attendance) {
                $startBreakTime = Carbon::parse($attendance['date_time'])->timestamp;
            }

            return $startBreakTime;
        }

        return 0;
    }

    public function sendAccountUpdateNotification($data)
    {
        if (! $data['password']) {
            return;
        }

        $this->notify(new SendUpdateAccountNotification($data['email'], $data['password'], $data['name']));
    }

    public function getUsersByPosition($position, $search = null)
    {
        $query = $this->employee();
    
        if ($position !== 'all') {
            $query->where('position', $position);
        }
    
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
    
        return $query->get();
    }
    
    private function dayWithSettingNightStamp($dateTime)
    {
        $settingNightStamp = Shop::getSettingNightStamp();
        $dateTime = Carbon::parse($dateTime);
        if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON') && $dateTime < Carbon::parse(formatDate($dateTime, 'Y-m-d 06:00:00'))) {
            return $dateTime->subDay()->format('Y-m-d');
        }

        return date('Y-m-d', strtotime($dateTime));
    }

    public function reportMonth($year = null, $month = null)
    {
        $retval = []; // khơi tao mang chua
        $attendances = $this->attendances()->month($year, $month)->get();  // lấy data chấm công trong phạm vi 1 tháng
        $days = []; // mảng ngày
        foreach ($attendances as $attendance) { 
            $day = $this->dayWithSettingNightStamp($attendance->date_time); // Nếu có làm đêm và thời gian chấm công < 6:00 thì tính là ngày hôm trước
            // $attendance->date_time = roundingDateTime($attendance->date_time);
            $days[$day][] = $attendance; // mảng day với dữ liệu chấm công chuẩn theo từng ngày
        }
        $this->month_salary = 0; // khởi tạo lương tháng
        $this->month_working_minutes = 0; //khởi tạo thời gian làm việc tháng
        $this->month_days = 0; // khởi tạo ngày làm việc tháng

        $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfMonth(); // ngày bắt đầu tháng
        $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth(); // ngày kết thúc tháng

        for ($day = $firstDayOfMonth; $day->lte($lastDayOfMonth); $day->addDay()) {
            $retval[$day->toDateString()] = new Day($day, []); // tạo mảng rỗng định dạng Y-m-d cho tháng này 
        }

        
        foreach ($days as $day => $checkInData) {
            $dayData = $this->buildDay($day, $checkInData);

            if ($this->salary_type == config('const.SALARY_TYPE.FULLTIME')) {
                $this->month_salary = $this->salary_fixed;
            } else {
                $this->month_salary += $dayData->salary;
            }

            $this->month_working_minutes += array_sum($dayData->workingHours);
            $this->month_break_hours += $dayData->breakTime;
            if ($dayData->checkIn) {
                $this->month_days++;
            }
            $retval[$day] = $dayData;
        }

        $this->report = $retval;

        return $retval;
    }

    public function reportDay($date = null)
    {
        if (is_null($date)) {
            $date = now()->toDateString();
        }

        return $this->reportMonth()[$date];
    }

    public function getTodayCheckIn($dateTime = null)
    {
        if (is_null($dateTime)) {
            $dateTime = Carbon::now();
        } else {
            $dateTime = Carbon::parse($dateTime);
        }
        
        $settingNightStamp = Shop::getSettingNightStamp();
        
        $today = $dateTime->format('Y-m-d');
        $yesterday = $dateTime->subDay()->format('Y-m-d');
        
        if ($settingNightStamp == config('const.SHOP_CONFIG.SETTINNG_NIGHT_STAMP.ON')) {
            $checkIn = $this->attendances()
                ->where('action_type', 'checkIn')
                ->where(function ($query) use ($today, $yesterday) {
                    $query->whereDate('date_time', $today)
                          ->orWhereDate('date_time', $yesterday);
                })
                ->orderBy('date_time', 'desc')
                ->first();
        } else {
            $checkIn = $this->attendances()
                ->where('action_type', 'checkIn')
                ->whereDate('date_time', $today)
                ->orderBy('date_time', 'desc')
                ->first();
        }
    
        return $checkIn ? $checkIn->date_time : null;
    }
    


    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    private function buildDayPartTime($date, $checkInData)
    {
        $workingHours = [
            'base' => 0,
            'night' => 0,
            'overtime' => 0,
        ];

        $day = new Day($date, $checkInData);
        $day->parse(); 
        $day->parseRanges();
        $isWeekendOrHoliday = Day::isWeekendOrHoliday($day->day);

        if ($isWeekendOrHoliday && $this->set_holiday_salary) {
            Log::info( $day->day.' là '.$isWeekendOrHoliday);
            $workingHours = [
                'base' => $day->calculateWorkingHoursPartime($this->holiday_salary_base),
                'night' => $day->calculateWorkingHoursPartime($this->holiday_salary_night),
                'overtime' => $day->calculateWorkingHoursPartime($this->holiday_salary_overtime),
            ];
        } else {
            $workingHours = [
                'base' => $day->calculateWorkingHoursPartime($this->salary_base),
                'night' => $day->calculateWorkingHoursPartime($this->salary_night),
                'overtime' => $day->calculateWorkingHoursPartime($this->salary_overtime),
            ];
        }

        $day->workingHours = $workingHours;
        $day->workTime = array_sum($workingHours);
        

        $day->salary = $this->calculateSalary($day, $workingHours);

        
        return $day;
    }

    private function buildDayFullTime($date, $checkInData)
    {
        $day = new Day($date, $checkInData);
        $day->parse();
        $day->parseRanges();

        $workingHours = [
            'base' => $day->calculateWorkingHoursFullTime(),
        ];

        $day->workingHours = $workingHours;
        $day->workTime = array_sum($workingHours);
        $day->salary = $day->workTime / 60 * $this->salary_fixed;

        return $day;
    }

    private function buildDay($date, $checkInData)
    {
        if ($this->salary_type == config('const.SALARY_TYPE.FULLTIME')) {
            return $this->buildDayFullTime($date, $checkInData);
        } else {
            return $this->buildDayPartTime($date, $checkInData);
        }
    }

    private function calculateSalary($day, $workingHours)
    {  
        if ($this->salary_type == config('const.SALARY_TYPE.FULLTIME')) {
            return 0;
        }

        $salary = 0;

        $isWeekendOrHoliday = Day::isWeekendOrHoliday($day->day);

        if ($isWeekendOrHoliday && $this->checkHolidaySalary($day)) {
                $salary = $this->calculateHolidaySalary($workingHours);
        } else {
            $salary = $this->calculateNormalSalary($workingHours);
        }


        return floor($salary);
    }

    private function checkHolidaySalary($day): bool
    {
        $carbonDay = Carbon::parse($day->day);
        $isSaturday = $carbonDay->isSaturday();
        $isSunday = $carbonDay->isSunday();
        $isHoliday = HolidayJp::isHoliday($carbonDay);
        if ($isSaturday && $this->set_saturday_salary) {
            return true;
        }
        if ($isSunday && $this->set_sunday_salary) {
            return true;
        }
        if ($isHoliday && $this->set_celebrate_salary) {
            return true;
        }

        Log::info ('checkHolidaySalary: '.$carbonDay . ' '.  $isHoliday);


        return false;
    }

    private function calculateNormalSalary($workingHours)
    {
        // Log::info ('calculateNormalSalary '.json_encode($workingHours));
        $salary = 0;
        if ($workingHours['base']) {
            $salary += $workingHours['base'] / 60 * $this->salary_base['salary'];
            Log::info('so gio lam '. $workingHours['base']. '  luong mot gio  '.$this->salary_base['salary']. " base ". $salary);   
        }
        if ($workingHours['night']) {
            $salary += $workingHours['night'] / 60 * $this->salary_night['salary'];
            Log::info("night". $salary);
        }
        if ($workingHours['overtime']) {
            $salary += $workingHours['overtime'] / 60 * $this->salary_overtime['salary'];
            Log::info("overtime". $salary);
        }

        
        return floor($salary);
    }

    private function calculateHolidaySalary($workingHours)
    {
        $salary = 0;
        if ($workingHours['base']) {
            $salary += $workingHours['base'] / 60 * $this->holiday_salary_base['salary'];
            Log::info('so gio lam holiday'. $workingHours['base']. '  luong mot gio  '.$this->salary_base['salary']. " base ". $salary);   
        }
        if ($workingHours['night']) {
            $salary += $workingHours['night'] / 60 * $this->holiday_salary_night['salary'];
        }
        if ($workingHours['overtime']) {
            $salary += $workingHours['overtime'] / 60 * $this->holiday_salary_overtime['salary'];
        }

        return $salary;
    }

    private function buildCsvItem($item)
    {
        return [
            $this->formatCsvTime($item->checkIn),
            $this->formatCsvTime($item->checkOut),
            formatHoursMinutes($item->breakTime),
            formatHoursMinutes($item->workTime),
            $item->isCheckInEdited,
            $item->isCheckOutEdited,
        ];
    }

    public function csvData()
    {
        $data = [];
        foreach ($this->report as $item) {
            $data[] = $this->buildCsvItem($item);
        }

        return $data;
    }

    public function formatCsvTime($time)
    {
        if ($time) {
            return Carbon::parse($time)->format('H:i');
        }
    }
}
