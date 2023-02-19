<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', [HomeController::class, 'index'])->name('home.index');

/*
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', [HomeController::class, 'show'])->middleware('')->name('home.show');

    Route::group(['as' => 'profile', 'prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});*/