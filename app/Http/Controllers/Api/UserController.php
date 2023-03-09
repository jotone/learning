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
            request: $request,
            collection: User::select(['users.*', 'roles.name as role_name'])
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id'),
            total: User::count(),
            resource: UserResource::class,
            search_callback: function ($q, $search) {
                $search = mb_strtolower($search);
                return $q->where(
                    fn($query) => $query
                        ->whereRaw('CONCAT(`first_name`, \' \', `last_name`) LIKE \'%' . $search . '%\'')
                        ->orWhereRaw('CONCAT(`last_name`, \' \', `first_name`) LIKE \'%' . $search . '%\'')
                        ->orWhere('email', 'like', '%' . $search . '%')
                );
            }
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