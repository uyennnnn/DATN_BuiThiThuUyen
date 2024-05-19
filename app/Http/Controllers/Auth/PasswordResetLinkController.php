<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'userType' => session('user_type', 'user'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $user = User::where('email', $request->input('email'))->first();

        if ($status == Password::RESET_LINK_SENT) {
            return redirect()->route('password.sent_mail', ['isAdmin' => $user->isAdmin() ?? false]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function sentMailAlert(Request $request)
    {
        return Inertia::render('Auth/ForgotPasswordSentMail', [
            'isAdmin' => (bool) $request->input('isAdmin'),
        ]);
    }

    public function showPasswordResetSuccess(Request $request)
    {
        return Inertia::render('Auth/ResetPasswordSuccess', [
            'userType' => session('user_type', 'user'),
            'isAdmin' => (bool) $request->input('isAdmin'),
        ]);
    }

    /**
     * Send a password reset link to a user.
     *
     * @return string
     */
    public function sendResetPassword(array $credentials)
    {
        $user = Password::getUser($credentials);

        if (is_null($user)) {
            return Password::INVALID_USER;
        }
        /** @var \App\Models\User $user * */
        $password = $user->resetPassword();
        $user->sendPasswordResetNotification($password);

        return Password::RESET_LINK_SENT;
    }
}
