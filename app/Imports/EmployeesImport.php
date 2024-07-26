<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Jobs\SendPasswordMailToEmployee;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Set default values for specific fields
        $defaults = [
            // Add any default values if necessary
        ];

        // Merge default values with imported values
        $data = array_merge($defaults, $row);

        // Set default role if not provided in the CSV
        if (!isset($data['role'])) {
            $data['role'] = 2; // Default role value
        }

        // Extract password and hash it
        $password = $data['password'];
        $data['password'] = Hash::make($password);

        // Prepare data for salary details
        $data['salary_base'] = [
            'time_start' => '08:00',
            'time_end' => '17:00',
            'salary' => $data['salary'],
        ];
        $data['salary_night'] = [
            'time_start' => null,
            'time_end' => null,
            'salary' => null,
        ];
        $data['salary_overtime'] = [
            'time_start' => null,
            'time_end' => null,
            'salary' => null,
        ];
        $data['holiday_salary_base'] = [
            'time_start' => null,
            'time_end' => null,
            'salary' => null,
        ];
        $data['holiday_salary_night'] = [
            'time_start' => null,
            'time_end' => null,
            'salary' => null,
        ];
        $data['holiday_salary_overtime'] = [
            'time_start' => null,
            'time_end' => null,
            'salary' => null,
        ];

        // Set other default values
        $data['position'] = 1;
        $data['salary_type'] = 1;
        $data['salary_fixed'] = null;
        $data['set_holiday_salary'] = false;
        $data['set_saturday_salary'] = 0;
        $data['set_sunday_salary'] = 0;
        $data['set_celebrate_salary'] = 0;
        $data['one_way_travel_expense'] = null;
        $data['nearest_train_station'] = null;

        // Create new user with the data
        $user = User::create($data);

        // SendPasswordMailToEmployee::dispatch($user->id, $password);

        // Return the created user instance
        return $user;
    }
}
