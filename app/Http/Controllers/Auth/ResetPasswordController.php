<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\{ForgotPasswordRequest, ResetPasswordRequest};
use App\Jobs\{SendRegistrationEmail, SendForgotPasswordEmail};
use App\Models\{PasswordResetToken, Settings, User};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class ResetPasswordController extends Controller
{
    /**
     * Handle the reset password link
     *
     * @param string $token
     * @return View|RedirectResponse
     */
    public function index(string $token): View|RedirectResponse
    {
        // Find the reset token record
        $reset = PasswordResetToken::whereRaw("md5(token) = '{$token}'")->firstOrFail();
        // Check if reset link not exists or link has benn expired
        if (time() > $reset->created_at->addWeek()->timestamp) {
            return redirect(route('auth.index') . '#forgot')->withErrors([
                'email' => 'The link is expired. Try sending your password reset request again.'
            ]);
        }
        // View the reset password form
        return view('auth.reset-password', [
            'email'    => $reset->user->email,
            'token'    => $token,
            'settings' => Settings::whereIn('key', ['site_title', 'header_code', 'footer_code', 'logo_img'])
                ->get()
                ->keyBy('key')
        ]);
    }

    /**
     * Send forgot password email
     *
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse
     */
    public function send(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::firstWhere('email', $request->get('email'));
        // Check if user has missing-details status
        if ('missing-details' === config('enums.user.statuses')[$user->status]) {
            // Send registration email
            SendRegistrationEmail::dispatch($user);

            return redirect()->route('auth.index')->withErrors([
                'email' => 'Registration email has been resent. Please check your inbox for the registration email or contact the administrator to resend it to you.'
            ]);
        }
        // Check if user is suspended
        if ('suspended' === config('enums.user.statuses')[$user->status]) {
            return redirect()->route('auth.index')->withErrors([
                'email' => 'You are account has been suspended. Please contact the admin or the support.'
            ]);
        }
        // Generate random token
        $token = Str::random() . uniqid();
        PasswordResetToken::updateOrCreate([
            'email' => $user->email
        ], [
            'token'      => $token,
            'created_at' => now()
        ]);
        // Send email
        SendForgotPasswordEmail::dispatch($user, md5($token));

        return redirect(route('auth.index') . '#forgot')->withErrors(['email' => 'Please check your email.']);
    }

    /**
     * Handle the update password request
     *
     * @param string $token
     * @param ResetPasswordRequest $request
     * @return View|RedirectResponse
     */
    public function update(string $token, ResetPasswordRequest $request): View|RedirectResponse
    {
        // Find the reset token record
        $reset = PasswordResetToken::whereRaw("md5(token) = '{$token}'")->firstOrFail();

        // Check if reset link not exists or link has benn expired
        if (time() > $reset->created_at->addWeek()->timestamp) {
            return redirect(route('auth.index') . '#forgot')->withErrors([
                'email' => 'The link is expired. Try sending your password reset request again.'
            ]);
        }
        // Set password
        $reset->user->password = $request->validated('password');
        $reset->user->save();
        // Remove the password reset record
        $reset->delete();

        return redirect()->route('auth.index')->withErrors(['email' => 'Now you can log in using new password.']);
    }
}