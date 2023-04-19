<?php

use App\Http\Controllers\Api\{
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
    // Email Templates API
    Route::apiResource('/email-templates', EmailTemplateController::class)->only(['store', 'update', 'destroy']);
    // Language API
    Route::group(['as' => 'language.', 'prefix' => '/language'], function () {
        // Install language
        Route::post('/', [LanguageController::class, 'store'])->name('store');
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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
