<?php

use App\Http\Controllers\Api\RoleController;
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

Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
    // Roles API
    Route::delete('/roles', [RoleController::class, 'delete'])->name('roles.delete');
    Route::apiResource('/roles', RoleController::class)->except(['show', 'destroy']);
});
