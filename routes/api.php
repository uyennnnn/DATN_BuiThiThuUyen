<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\CurentTimeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ValidateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/migrate', [ShopController::class, 'migrate']);
Route::post('/init', [ShopController::class, 'init']);
Route::post('/validate/no-overlap', [ValidateController::class, 'noOverlap']);
Route::get('/current-time', [CurentTimeController::class, 'getCurrentTime']);
Route::get('attendance/report', [AttendanceController::class, 'report'])->name('report');
