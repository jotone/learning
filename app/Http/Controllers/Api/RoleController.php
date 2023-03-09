<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Resources\RoleResource;
use App\Http\Requests\Role\{RoleStoreRequest, RoleUpdateRequest};
use App\Models\Role;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends BasicApiController
{
    /**
     * Role list
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiList(
            request: $request,
            collection: Role::query(),
            total: Role::count(),
            resource: RoleResource::class,
            search_callback: function ($q, string $search) {
                $search = mb_strtolower($search);
                return $q->whereRaw("LOWER(name) LIKE '%$search%' OR LOWER(slug) LIKE '%$search%' OR level LIKE '%$search%'");
            });
    }

    /**
     * Specified role data
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->apiShow(request: $request, query: Role::query(), id: $id);
    }

    /**
     * Create Role
     *
     * @param RoleStoreRequest $request
     * @return JsonResponse
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        return response()->json(Role::create($request->validated()), 201);
    }

    /**
     * Update Role
     *
     * @param Role $role
     * @param RoleUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Role $role, RoleUpdateRequest $request): JsonResponse
    {
        $role->update($request->validated());

        return response()->json($role);
    }

    /**
     * Remove Role
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([], 204);
    }
}