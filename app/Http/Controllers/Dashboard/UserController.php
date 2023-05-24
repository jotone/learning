<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\{Role, Settings, User};
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
                        'edit' => route('dashboard.users.edit', ':id')
                    ],
                    'users' => [
                        'list' => route('api.users.index'),
                        'create' => route('dashboard.users.create'),
                        'edit' => route('dashboard.users.edit', ':id'),
                        'destroy' => route('api.users.destroy', ':id')
                    ]
                ],
                'translations' => [
                    'user' => __('user'),
                    'role' => [
                        'single' => __('role.single')
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
        return $this->form(
            template: 'Users/Form',
            request: $request,
            share: [
                'enums' => config('enums')['user'],
                'routes' => [
                    'form' => route('api.users.store')
                ],
                'roles' => Role::where('level', '>=', Auth::user()->role->level)
                    ->orderBy('level')
                    ->get()
                    ->pluck('name', 'id'),
                'settings' => Settings::where('section', 'registration-process')->pluck('value', 'key')->toArray(),
                'translations' => [
                    'user' => [
                        'fields' => __('user.fields'),
                        'msg' => __('user.msg'),
                        'password' => __('user.password')
                    ],
                    'role' => [
                        'single' => __('role.single')
                    ]
                ]
            ]
        );
    }

    /**
     * User edit page
     *
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request): Response
    {
        return $this->form(
            template: 'Users/Form',
            request: $request,
            share: [
                'enums' => config('enums')['user'],
                'model' => $user,
                'routes' => [
                    'form' => route('api.users.update', $user->id)
                ],
                'roles' => Role::where('level', '>=', Auth::user()->role->level)
                    ->orderBy('level')
                    ->get()
                    ->pluck('name', 'id'),
                'settings' => Settings::where('section', 'registration-process')->pluck('value', 'key')->toArray(),
                'translations' => [
                    'user' => [
                        'fields' => __('user.fields'),
                        'msg' => __('user.msg'),
                        'password' => __('user.password')
                    ],
                    'role' => [
                        'single' => __('role.single')
                    ]
                ]
            ]
        );
    }

    /**
     * Admin profile page
     *
     * @param Request $request
     * @return Response
     */
    public function me(Request $request): Response
    {
        $user = auth()->user();

        return $this->form(
            template: 'Users/AdminForm',
            request: $request,
            share: [
                'model' => $user,
                'routes' => [
                    'form' => route('api.users.update', $user->id)
                ],
                'translations' => [
                    'user' => [
                        'fields' => __('user.fields'),
                        'password' => __('user.password'),
                    ]
                ]
            ]
        );
    }
}