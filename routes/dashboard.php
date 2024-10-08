<?php

use App\Http\Controllers\Dashboard\{
    AppearanceController,
    CategoryController,
    CoachController,
    CommentController,
    CommunityController,
    CourseController,
    DashboardController,
    EmailTemplateController,
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

Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');

Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');

Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');

Route::group(['as' => 'courses.'], function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('create');
    Route::get('/courses/{course}/certificate', function () {})->name('certificate');
    Route::get('/courses/{course}/comments', function () {})->name('comments');
    Route::get('/courses/{course}/curriculum', function () {})->name('curriculum');
    Route::get('/courses/{course}/settings', [CourseController::class, 'settings'])->name('settings');
    Route::get('/courses/{course}/user-access', function () {})->name('user-access');
    Route::get('/courses/{course}/worksheets', function () {})->name('worksheets');
});

Route::get('/events', [EventsController::class, 'index'])->name('events.index');

Route::get('/get-started', [GetStartedController::class, 'index'])->name('get-started.index');

Route::get('/help-center', [HelpCenterController::class, 'index'])->name('help-center.index');

Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

Route::group(['as' => 'roles.'], function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('edit');
});

Route::group(['as' => 'settings.'], function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('index');
    Route::get('/settings/coaches', [CoachController::class, 'create'])->name('coaches.create');
    Route::get('/settings/coaches/{coach}/edit', [CoachController::class, 'edit'])->name('coaches.edit');
    Route::get('/settings/templates', [EmailTemplateController::class, 'create'])->name('templates.create');
    Route::get('/settings/templates/{template}/edit', [EmailTemplateController::class, 'edit'])->name('templates.edit');
});

Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
