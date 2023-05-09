<?php

use App\Http\Controllers\Dashboard\{
    CoachesController, DashboardController, EmailTemplatesController, RoleController, SettingsController, UserController
};
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::as('users.')->group(function () {
    Route::get('/users/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/users/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/users/roles/edit/{role}', [RoleController::class, 'edit'])->name('roles.edit');

    Route::get('/users', [UserController::class, 'index'])->name('index');
    Route::get('/users/create', [UserController::class, 'create'])->name('create');
    Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('edit');
});

Route::as('settings.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'main'])->name('main');
    Route::get('/settings/login-page', [SettingsController::class, 'loginPage'])->name('login');
    Route::get('/settings/language', [SettingsController::class, 'language'])->name('language');
    Route::get('/settings/coaches', [SettingsController::class, 'coaches'])->name('coaches');

    Route::as('emails.')->group(function () {
        Route::get('/settings/emails', [EmailTemplatesController::class, 'index'])->name('index');
        Route::get('/settings/emails/create', [EmailTemplatesController::class, 'create'])->name('create');
        Route::get('/settings/emails/edit/{template}', [EmailTemplatesController::class, 'edit'])->name('edit');
    });

    Route::as('coaches.')->group(function () {
       Route::get('/settings/coaches', [CoachesController::class, 'index'])->name('index');
       Route::get('/settings/coaches/create', [CoachesController::class, 'create'])->name('create');
       Route::get('/settings/coaches/edit/{coach}', [CoachesController::class, 'edit'])->name('edit');
    });
});


// TODO: put it onto the proper route list
Route::get('/students', function ($id) {dd($id);})->name('student.edit');
