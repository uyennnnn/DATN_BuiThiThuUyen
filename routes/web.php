<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/version.txt', function () {
    return config('app.version');
});

Route::get('/', function () {
    return redirect(route('login'));
});

use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/test/{id}', function ($id) {
    $user = User::find($id);
    $user->calculateTempBreakTime();
    Auth::login($user);

    return $user;
});

Route::middleware(['admin.auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Admin/Home/Index');
    })->middleware(['admin.auth', 'verified'])->name('home');

    Route::get('/employee', [UserController::class, 'index'])->name('users.index');
    Route::get('/employee/get-list', [UserController::class, 'getListUser'])->name('users.list');
    Route::get('/employee/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/employee/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/employee/id-and-name', [UserController::class, 'getEmployeeIdAndName']);
    Route::get('/register-success', [UserController::class, 'alertSuccess'])->name('users.success');
    Route::get('/employee/{employee}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/employee/{employee}', [UserController::class, 'update'])->name('users.update');
    Route::get('/delete-employee', [UserController::class, 'delete'])->name('users.confirm_delete');
    Route::post('/delete-employee/{employee}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.edit');
    Route::post('/shop', [ShopController::class, 'update'])->name('shop.update');
    Route::get('/shop/update-success', [ShopController::class, 'success'])->name('shop.update_success');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');

    Route::get('/qrcode', [QRCodeController::class, 'index'])->name('qrCode');
    Route::get('/qrcode/image', [QRCodeController::class, 'image'])->name('qrCode.image');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/user/{user}/day/{day}/attendances', [UserController::class, 'getAttendancesByUserIdAndDay']);
    Route::put('/user/{user}/day/{day}/attendances', [UserController::class, 'updateAttendancesByUserIdAndDay']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/user.php';
