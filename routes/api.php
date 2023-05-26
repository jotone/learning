<?php

use App\Http\Controllers\Api\{
    CourseController,
    EmailTemplateController,
    LanguageController,
    RoleController,
    SettingsController,
    SocialMediaLinkController,
    UserController
};
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
    // Courses API
    Route::apiResource('/courses', CourseController::class)->only(['index']);

    // Email Templates API
    Route::apiResource('/email-templates', EmailTemplateController::class)->only(['store', 'update', 'destroy']);

    // Language API
    Route::group(['as' => 'language.', 'prefix' => '/language'], function () {
        // Get language file content
        Route::get('/{lang}/{file}', [LanguageController::class, 'show'])->name('show');
        // Install language
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        // Update language translation
        Route::patch('/', [LanguageController::class, 'update'])->name('update');
        // Remove language package
        Route::delete('/{name}', [LanguageController::class, 'destroy'])->name('destroy');
    });

    // Roles API routes
    Route::apiResource('/roles', RoleController::class);

    // Settings API
    Route::match(['patch', 'put'], '/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Social Media API
    Route::patch('/socials', [SocialMediaLinkController::class, 'sort'])->name('socials.sort');
    Route::apiResource('/socials', SocialMediaLinkController::class)->only(['store', 'update', 'destroy']);

    // User API routes
    Route::apiResource('/users', UserController::class);
});
