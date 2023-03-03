<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends BasicApiController
{
    /**
     * User list
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiList(
            $request,
            User::select(['users.*', 'roles.name as role_name'])
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id'),
            User::count(),
            UserResource::class
        );
    }

    /**
     * Specified user data
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->apiShow(
            $request,
            User::select(['users.*', 'roles.name as role_name'])
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id'),
            $id
        );
    }

    public function store()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}