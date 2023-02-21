<?php

use App\Http\Controllers\Dashboard\{DashboardController, RoleController, UsersController};
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::as('users.')->group(function () {
    Route::get('/users/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/users/role/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/users/role/{role}', [RoleController::class, 'edit'])->name('roles.edit');
});

Route::get('/users', [UsersController::class, 'index'])->name('users.index');
