<?php

use App\Http\Controllers\Auth\{AuthController, ResetPasswordController};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/user/login', [AuthController::class, 'index'])->name('auth.index');
Route::post('/user/login', [AuthController::class, 'login'])->name('auth.login');
Route::any('/user/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Forgot password routes
Route::group(['as' => 'reset.', 'prefix' => '/reset-password'], function () {
    // Reset password form
    Route::get('/{token}', [ResetPasswordController::class, 'index'])->name('index');
    Route::group(['middleware' => 'throttle:6,1'], function () {
        // Create reset password entry
        Route::post('/', [ResetPasswordController::class, 'send'])->name('send');
        // Reset Password handler
        Route::post('/{token}', [ResetPasswordController::class, 'update'])->name('update');
    });
});

Route::get('/', [\App\Http\Controllers\Main\HomeController::class, 'index'])->name('home.index')->middleware('auth');

