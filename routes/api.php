<?php

use App\Http\Controllers\Api\{EmailTemplateController, SettingsController, SocialMediaController};
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

Route::group(['middleware' => ['auth:sanctum']], function () {
    // Email Templates
    Route::apiResource('/templates', EmailTemplateController::class)->only(['store', 'update', 'destroy']);
    // Update settings
    Route::post('/settings', [SettingsController::class, 'smtp'])->name('settings.smtp');
    Route::match(['patch', 'put'], '/settings', [SettingsController::class, 'update'])->name('settings.update');
    // Social Media API
    Route::patch('/socials', [SocialMediaController::class, 'sort'])->name('socials.sort');
    Route::apiResource('/socials', SocialMediaController::class)->only(['store', 'update', 'destroy']);
});
