<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Settings;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Session};
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function index(): View
    {
        return view('auth.login', [
            'settings' => Settings::whereIn('key', ['site_title', 'header_code', 'footer_code', 'logo_img'])
                ->get()
                ->keyBy('key')
        ]);
    }

    /**
     * Authorize user
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        // Run authentication
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        // Get authenticated user data
        $user = Auth::user();

        // Set api token to the current session
        Session::put('api-token', $user->createToken('token-name')->plainTextToken);

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
        Auth::user()->tokens()->delete();

        // Logout user session
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to the login page
        return redirect()->route('auth.index');
    }
}