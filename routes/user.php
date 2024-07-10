<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user'], function () {
    Route::middleware('user.guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'getFormUserLogin'])->name('user.get_login');
        Route::post('login', [AuthenticatedSessionController::class, 'userLogin'])->name('user.login');
    });

    Route::middleware('user.auth')->group(function () {
        Route::get('/nfc', [AttendanceController::class, 'nfc'])->name('nfc');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('user.attendance');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('user.attendance.store');
        Route::get('/attendance/request', [AttendanceController::class, 'request'])->name('request');
        Route::get('/request/create', [AttendanceController::class, 'requestCreate'])->name('user.attendance.request.create');
    });
});
