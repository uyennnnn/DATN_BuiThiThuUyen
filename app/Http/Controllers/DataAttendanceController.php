<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DataAttendanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Attendance/Index');
    }

    public function load()
    {
        return Inertia::render('Admin/Attendance/Loader');
    }

    public function exportCsv()
    {
        return Inertia::render('Admin/Attendance/ExportCsv');
    }
}
