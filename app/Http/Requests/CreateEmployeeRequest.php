<?php

namespace App\Http\Requests;

use App\Rules\NoOverlap;
use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'full_name' => 'string|nullable|max:255',
            'position' => 'required|numeric',
            'email' => 'email|unique:users,email|max:255',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|in:'.$this->input('password'),
            'salary_type' => 'required|numeric',
            'salary_fixed' => 'nullable|numeric|max:99999999|required_if:salary_type,'.config('const.SALARY_TYPE.FULLTIME'),
            'salary_base.time_start' => 'required_if:salary_type,'.config('const.SALARY_TYPE.PARTIME'),
            'salary_base.time_end' => [
                'nullable',
                'required_if:salary_type,'.config('const.SALARY_TYPE.PARTIME'),
                // 'after:salary_base.time_start',
            ],
            'salary_base.salary' => 'nullable|numeric|required_if:salary_type,'.config('const.SALARY_TYPE.PARTIME'),
            'salary_night.time_start' => 'nullable|required_with:salary_night.time_end,salary_night.salary',
            'salary_night.time_end' => [
                'nullable',
                // 'after:salary_night.time_start',
                'required_with:salary_night.time_start,salary_night.salary',
            ],
            'salary_night.salary' => 'nullable|numeric|required_with:salary_night.time_start,salary_night.time_end',
            'salary_night.time_start' => 'nullable|required_with:salary_night.time_end,salary_night.salary',
            'salary_night.time_end' => [
                'nullable',
                // 'after:salary_night.time_start',
                'required_with:salary_night.time_start,salary_night.salary',
            ],
            'salary_night.salary' => 'nullable|numeric|required_with:salary_night.time_start,salary_night.time_end',
            'salary_overtime.time_start' => 'nullable|required_with:salary_overtime.time_end,salary_overtime.salary',
            'salary_overtime.time_end' => [
                'nullable',
                // 'after:salary_overtime.time_start',
                'required_with:salary_overtime.time_start,salary_overtime.salary',
            ],
            'salary_overtime.salary' => 'nullable|numeric|required_with:salary_overtime.time_start,salary_overtime.time_end',
            'set_holiday_salary' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($this->input('salary_type') == 1 && $value && $this->input('set_saturday_salary') == 0 && $this->input('set_sunday_salary') == 0 && $this->input('set_celebrate_salary') == 0) {
                        return $fail('選択してください');
                    }
                },
            ],
            'set_saturday_salary' => [
                'required',
                'boolean',
            ],
            'set_sunday_salary' => 'required|boolean',
            'set_celebrate_salary' => 'required|boolean',
            'holiday_salary_base.time_start' => 'nullable|required_if:set_holiday_salary,true|required_with:holiday_salary_base.time_end,holiday_salary_base.salary',
            'holiday_salary_base.time_end' => [
                'nullable',
                'required_if:set_holiday_salary,true',
                // 'after:holiday_salary_base.time_start',
                'required_with:holiday_salary_base.time_start,holiday_salary_base.salary',
            ],
            'holiday_salary_base.salary' => 'nullable|numeric|required_if:set_holiday_salary,true|required_with:holiday_salary_base.time_start,holiday_salary_base.time_end',
            'holiday_salary_night.time_start' => 'nullable|required_with:holiday_salary_night.time_end,holiday_salary_night.salary',
            'holiday_salary_night.time_end' => [
                'nullable',
                // 'after:holiday_salary_night.time_start',
                'required_with:holiday_salary_night.time_start,holiday_salary_night.salary',
            ],
            'holiday_salary_night.salary' => 'nullable|numeric|required_with:holiday_salary_night.time_start,holiday_salary_night.time_end',
            'holiday_salary_overtime.time_start' => 'nullable|required_with:holiday_salary_overtime.time_end,holiday_salary_overtime.salary',
            'holiday_salary_overtime.time_end' => [
                'nullable',
                // 'after:holiday_salary_overtime.time_start',
                'required_with:holiday_salary_overtime.time_start,holiday_salary_overtime.salary',
            ],
            'holiday_salary_overtime.salary' => 'nullable|numeric|required_with:holiday_salary_overtime.time_start,holiday_salary_overtime.time_end',
            'nearest_train_station' => 'nullable|string|max:255',
            'one_way_travel_expense' => 'nullable|numeric|max:99999999',

            'salary_base' => [
                new NoOverlap($this->input('salary_base'), $this->input('salary_night'), $this->input('salary_overtime')),
            ],
            'holiday_salary_base' => [
                new NoOverlap($this->input('holiday_salary_base'), $this->input('holiday_salary_night'), $this->input('holiday_salary_overtime')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'salary_base.salary.required_if' => '時給を入力してください',
            'position.required' => 'お店での役割を選択してください',
            'salary_fixed.required_if' => '給与を入力してください',
            'salary_night.salary.required_with' => '時給を入力してください',
            'salary_overtime.salary.required_with' => '時給を入力してください',
            'holiday_salary_base.salary.required_if' => '時給を入力してください',
            'holiday_salary_night.salary.required_with' => '時給を入力してください',
            'holiday_salary_overtime.salary.required_with' => '時給を入力してください',
            'password_confirmation.in' => '上記と同じパスワードを入力してください',
            'password_confirmation.required' => '上記と同じパスワードを入力してください',
            'salary_fixed.max' => '入力した金額が大きすぎます',
            'one_way_travel_expense.max' => '入力した金額が大きすぎます',
        ];
    }
}
