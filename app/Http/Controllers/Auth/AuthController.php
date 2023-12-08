<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Settings;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Session};
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Render the login view with the necessary data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        return view('auth.login', [
            'settings' => Settings::whereIn('key', ['footer_code', 'header_code', 'site_title'])->get()->keyBy('key')
        ]);
    }

    /**
     * Logs in a user with the given login request.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Run authentication
            $request->authenticate();

            // Regenerate session
            $request->session()->regenerate();

            // Get authenticated user data
            $user = Auth::user();

            // Set api token to the current session
            Session::put('api-token', $user->createToken('token-name')->plainTextToken);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        // Check if user has access to the dashboard
        return redirect()->route($user->role->level >= 255 ? 'home.index' : 'dashboard.index');
    }

    /**
     * Cease user session
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        // Remove user tokens
        Auth::check() && Auth::user()->tokens()->delete();

        // Logout user session
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to the login page
        return redirect()->route('auth.index');
    }
}