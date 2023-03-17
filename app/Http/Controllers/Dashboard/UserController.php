<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return $this->view(
            template: 'Users/Index',
            request: $request,
            share: [
                'routes' => [
                    'students' => [
                        'edit' => route('dashboard.users.edit', 0)
                    ],
                    'users'    => [
                        'list'    => route('api.users.index'),
                        'create'  => route('dashboard.users.create'),
                        'edit'    => route('dashboard.users.edit', 0),
                        'destroy' => route('api.users.destroy', 0)
                    ]
                ]
            ]
        );
    }

    /**
     * User create page
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return $this->view(
            template: 'Users/Form',
            request: $request,
            share: [
                'routes' => [
                    'users' => [
                        'store' => route('api.users.store')
                    ]
                ],
                'roles'  => Role::where('level', '>=', Auth::user()->role->level)
                    ->orderBy('level')
                    ->get()
                    ->pluck('name', 'id')
            ],
            prevent_filters: true
        );
    }
}