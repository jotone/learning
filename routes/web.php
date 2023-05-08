<?php

use App\Http\Controllers\Auth\{AuthController, ResetPasswordController};
use App\Http\Controllers\Main\HomeController;
use Illuminate\Support\Facades\Route;
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

// Authentication routes
Route::group(['as' => 'auth.', 'prefix' => '/user'], function () {
    Route::get('/login', [AuthController::class, 'index'])->name('index');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
});
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

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
});
/*
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', [HomeController::class, 'show'])->middleware('')->name('home.show');

    Route::group(['as' => 'profile', 'prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});*/