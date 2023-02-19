<?php

use App\Http\Controllers\Dashboard\{DashboardController, RolesController, UsersController};
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::get('/users', [UsersController::class, 'index'])->name('users.index');
Route::get('/users/roles', [RolesController::class, 'index'])->name('users.roles.index');
