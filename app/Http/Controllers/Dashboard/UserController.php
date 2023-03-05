<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use Illuminate\Http\Request;
use Inertia\Response;

class UserController extends BasicAdminController
{
    /**
     * User list page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->view('Users/Index', $request, [
            'routes' => [
                'students' => [
                    'edit' => route('dashboard.student.edit', 0)
                ],
                'users' => [
                    'list'    => route('api.users.index'),
                    'edit'    => route('dashboard.users.edit', 0),
                    'destroy' => route('api.users.destroy', 0)
                ]
            ]
        ]);
    }
}