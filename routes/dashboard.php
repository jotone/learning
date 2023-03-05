<?php

use App\Http\Controllers\Dashboard\{DashboardController, RoleController, SettingsController, UserController};
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::as('users.')->group(function () {
    Route::get('/users/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/users/role/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/users/role/{role}', [RoleController::class, 'edit'])->name('roles.edit');

    Route::get('/users', [UserController::class, 'index'])->name('index');
    Route::get('/users/create', [UserController::class, 'create'])->name('create');
    Route::get('/users/{user}', [UserController::class, 'edit'])->name('edit');
});

Route::as('settings.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'main'])->name('main');
});



Route::get('/students', function ($id) {dd($id);})->name('student.edit');
