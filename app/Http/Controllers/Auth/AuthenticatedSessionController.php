<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function getFormAdminLogin(): Response
    {
        session(['user_type' => 'admin']);

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'isAdminLogin' => true,
            'userType' => session('user_type', 'admin'),
        ]);
    }

    public function getFormUserLogin(): Response
    {
        session(['user_type' => 'user']);

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'isAdminLogin' => false,
            'userType' => session('user_type', 'user'),
        ]);
    }

    public function adminLogin(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function userLogin(UserLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::USER_HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $routeName = Auth::user()->isAdmin() ? 'login' : 'user.login';

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route($routeName);
    }

    public function getFormLogout(): Response
    {
        return Inertia::render('Auth/Logout');
    }
}
