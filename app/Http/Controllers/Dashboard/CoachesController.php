<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BasicAdminController;
use App\Models\{Role, User};
use Illuminate\Http\Request;
use Inertia\Response;

class CoachesController extends BasicAdminController
{
    /**
     * Coach list page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $coach_role = Role::firstWhere('slug', 'coach');
        return $this->view(
            template: 'Coaches/Index',
            request: $request,
            share: [
                'routes' => [
                    'coaches' => [
                        'list' => route('api.users.index') . '?where[role_id]=' . $coach_role->id,
                        'create' => route('dashboard.settings.coaches.create'),
                        'edit' => route('dashboard.settings.coaches.edit', ':id'),
                        'destroy' => route('api.users.destroy', ':id')
                    ]
                ],
                'translations' => [
                    'coach' => __('coach'),
                    'user' => [
                        'fields' => __('user.fields')
                    ]
                ]
            ]
        );
    }

    /**
     * Coach create page
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return $this->form(
            template: 'Coaches/Form',
            request: $request,
            share: [
                'routes' => [
                    'coaches' => [
                        'form' => route('api.users.store')
                    ]
                ],
                'role' => Role::firstWhere('slug', 'coach')->id,
                'translations' => [
                    'coach' => [
                        'msg' => __('coach.msg'),
                    ],
                    'user' => [
                        'fields' => __('user.fields'),
                        'password' => __('user.password')
                    ]
                ]
            ]
        );
    }

    /**
     * Coach create page
     *
     * @param User $coach
     * @param Request $request
     * @return Response
     */
    public function edit(User $coach, Request $request): Response
    {
        // Coach role
        $role = Role::firstWhere('slug', 'coach');
        // Check if user is not a coach
        abort_if($role->id !== $coach->role_id, 404);

        return $this->form(
            template: 'Coaches/Form',
            request: $request,
            share: [
                'model' => $coach,
                'routes' => [
                    'coaches' => [
                        'form' => route('api.users.update', $coach->id)
                    ]
                ],
                'role' => $role->id,
                'translations' => [
                    'coach' => [
                        'msg' => __('coach.msg'),
                    ],
                    'user' => [
                        'fields' => __('user.fields'),
                        'password' => __('user.password')
                    ]
                ]
            ]
        );
    }
}