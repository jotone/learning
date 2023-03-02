<?php

use App\Http\Controllers\Api\{RoleController, UserController};
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

Route::group(['as' => 'api.'], function () {
    // Roles API routes
    Route::resource('/roles', RoleController::class)->except(['create', 'edit']);
    // User API routes
    Route::resource('/users', UserController::class)->except(['create', 'edit']);
});
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
