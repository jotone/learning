<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Illuminate\Http\Request;
use Inertia\Response;

class CourseController extends BasicAdminController
{
    /**
     * Set default order as "BY position ASC"
     *
     * @var array
     */
    protected array $order = [
        'by' => 'position',
        'dir' => 'asc'
    ];

    protected int $take = 10;

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
                'routes' => [
                    'courses' => [
                        'list' => route('api.courses.index'),
                        'create' => route('dashboard.courses.create')
                    ]
                ]
            ]
        );
    }

    public function create()
    {
    }
}