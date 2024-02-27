<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseDashboardController;
use Inertia\Response;

class CourseController extends BaseDashboardController
{
    public function index(): Response
    {
        return $this->view(
            view: 'Course/List/Index',
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