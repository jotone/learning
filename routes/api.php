<?php

use App\Http\Controllers\Api\{RoleController, SettingsController, UserController};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['as' => 'api.', 'middleware' => 'auth:sanctum'], function () {
    // Roles API routes
    Route::apiResource('/roles', RoleController::class);
    // User API routes
    Route::apiResource('/users', UserController::class);
    // Settings API
    Route::match(['patch', 'put'], '/settings', [SettingsController::class, 'update'])->name('settings.update');
});
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
