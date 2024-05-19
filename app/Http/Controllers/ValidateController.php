<?php

namespace App\Http\Controllers;

use App\Rules\NoOverlap;
use App\Rules\RequiredSalaryBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateController extends Controller
{
    public function noOverlap(Request $request)
    {
        $validator = Validator::make(
            [
                'data' => $request->all(),
            ],
            [
                'data' => new NoOverlap($request->input('salary_base'), $request->input('salary_night'), $request->input('salary_overtime')),
                'data.salary_base' => new RequiredSalaryBase($request->input('salary_base')),
            ]
        );

        if ($validator->fails()) {
            return [
                'result' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return [
            'result' => true,
            'message' => null,
        ];
    }
}
