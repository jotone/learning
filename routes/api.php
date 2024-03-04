<?php

use App\Http\Controllers\Api\{EmailTemplateController, PageColumnController, SettingsController, SocialMediaController};
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
    // Page Columns API
    Route::group(['as' => 'page-columns.', 'prefix' => 'page-columns'], function () {
        Route::match(['patch', 'put'], '/{column}', [PageColumnController::class, 'update'])->name('update');
//        Route::patch('/sort', [PageColumnController::class, 'sort'])->name('sort');
    });
    // Email Templates API
    Route::apiResource('/templates', EmailTemplateController::class)->only(['store', 'update', 'destroy']);
    // Update settings API
    Route::post('/settings', [SettingsController::class, 'smtp'])->name('settings.smtp');
    Route::match(['patch', 'put'], '/settings', [SettingsController::class, 'update'])->name('settings.update');
    // Social Media API
    Route::patch('/socials', [SocialMediaController::class, 'sort'])->name('socials.sort');
    Route::apiResource('/socials', SocialMediaController::class)->only(['store', 'update', 'destroy']);
});
