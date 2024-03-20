<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\CourseStatus;
use App\Http\Controllers\BaseDashboardController;
use App\Models\{Category, Course, Settings};
use Illuminate\Http\Request;
use Inertia\Response;

class CourseController extends BaseDashboardController
{
    /**
     * Returns the index page of the Courses module.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->contentTable(
            view: 'Course/List/Index',
            section: 'courses',
            shared: [
                'breadcrumbs' => [
                    ['name' => 'Courses']
                ],
                'statuses' => CourseStatus::forSelect(),
                'routes' => [
                    'category' => [
                        'api' => route('graphql.category'),
                        'edit' => route('dashboard.category.edit', ':id')
                    ],
                    'course' => [
                        'api' => route('graphql.course'),
                        'settings' => route('dashboard.courses.settings', ':id')
                    ],
                    'settings' => route('api.settings.update')
                ],
                'settings' => Settings::where('key', 'cats_inst_courses')->pluck('value', 'key')->toArray()
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }

    public function settings(Course $course, Request $request): Response
    {
        return $this->view(
            view: 'Course/Settings/Index',
            shared: [
                'breadcrumbs' => [
                    [
                        'name' => 'Courses',
                        'url' => route('dashboard.courses.index')
                    ],
                    [
                        'name' => $course->name
                    ]
                ],
                'categories' => Category::select(['id', 'name'])
                    ->where('type', Course::class)
                    ->orderBy('position')
                    ->get(),
                'course' => $course,
                'statuses' => CourseStatus::forSelect(),
                'top_menu' => $this->topMenu($request)
            ]
        );
    }
}