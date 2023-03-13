<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Settings;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Auth;
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
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        Session::put('api-token', $user->createToken('token-name')->plainTextToken);

        $route = $user->role->level >= 255 ? 'home.index' : 'dashboard.index';
        return redirect()->route($route);
    }

    /**
     * Cease user session
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::user()->tokens()->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('auth.index');
    }
}