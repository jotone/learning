<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\CourseStatus;
use App\Http\Controllers\BaseDashboardController;
use App\Models\Settings;
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
                        'edit' => route('dashboard.courses.edit', ':id')
                    ],
                    'settings' => route('api.settings.update')
                ],
                'settings' => Settings::where('key', 'cats_inst_courses')->pluck('value', 'key')->toArray()
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }
}