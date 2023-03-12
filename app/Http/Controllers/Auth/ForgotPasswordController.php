<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function sendMail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users.email'],
        ]);

    }
}