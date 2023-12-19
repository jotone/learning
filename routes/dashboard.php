<?php

use App\Http\Controllers\Dashboard\{
    AppearanceController,
    CommentController,
    CommunityController,
    CourseController,
    DashboardController,
    EventsController,
    GetStartedController,
    HelpCenterController,
    ReferralController,
    RoleController,
    SettingsController,
    TestimonialController,
    UserController
};
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::get('/appearance', [AppearanceController::class, 'index'])->name('appearance.index');

Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');

Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

Route::get('/events', [EventsController::class, 'index'])->name('events.index');

Route::get('/get-started', [GetStartedController::class, 'index'])->name('get-started.index');

Route::get('/help-center', [HelpCenterController::class, 'index'])->name('help-center.index');

Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
