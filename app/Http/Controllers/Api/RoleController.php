<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasicApiController;
use App\Http\Requests\Role\{RoleStoreRequest, RoleUpdateRequest};
use App\Http\Resources\RoleResource;
use App\Models\{Permission, Role};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\{DB, Log};

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
        // Get request data
        $args = $request->validated();

        DB::beginTransaction();
        try {
            // Create role entity
            $role = Role::create([
                'name' => $args['name'],
                'slug' => $args['slug'],
                'level' => $args['level'],
            ]);

            if (!empty($args['permissions'])) {
                // Loop through the provided permissions
                foreach($args['permissions'] as $controller => $controller_methods) {
                    $methods = [];
                    // Loop through the controller methods
                    foreach ($controller_methods as $method => $allowance) {
                        if ($allowance !== '0') {
                            $methods[] = $method;
                        }
                    }
                    // If there are permitted methods for this controller, create a permission
                    if (!empty($methods)) {
                        Permission::create([
                            'role_id' => $role->id,
                            'controller' => $controller,
                            'allowed_methods' => $methods
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            return response()->json([
                'errors' => [$e->getMessage()]
            ], 500);
        }

        return response()->json($role, 201);
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