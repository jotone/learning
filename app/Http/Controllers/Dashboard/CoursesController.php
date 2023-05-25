<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Illuminate\Http\Request;
use Inertia\Response;

class CoursesController extends BasicAdminController
{
    /**
     * Course list page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->view(
            template: 'Courses/Index',
            request: $request,
            share: [
                'routes' => []
            ]
        );
    }
}