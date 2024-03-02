<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
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
                'routes' => [
                    'api' => route('graphql.course'),
                    'create' => route('dashboard.courses.create'),
                    'edit' => route('dashboard.courses.edit', ':id')
                ],
            ],
            scripts: [
                'css' => ['resources/assets/css/admin/content-table.scss']
            ]);
    }
}