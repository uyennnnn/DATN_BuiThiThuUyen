<?php

namespace App\Http\Controllers;

class CurentTimeController extends Controller
{
    public function getCurrentTime()
    {
        return response()->json(['timestamp' => time()]);
    }
}
