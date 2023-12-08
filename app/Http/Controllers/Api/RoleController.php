<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Role\{RoleBulkDeleteRequest, RoleStoreRequest, RoleUpdateRequest};
use App\Http\Resources\RoleResource;
use App\Models\{Permission, Role};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RoleController extends BaseApiController
{
    /**
     * Index method for retrieving a paginated list of roles. (api.roles.index)
     *
     * @param Request $request The HTTP request object.
     *
     * @return AnonymousResourceCollection The paginated list of roles as a resource collection.
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
                return $q->whereRaw("LOWER(name) LIKE '%$search%' OR level LIKE '%$search%'");
            });
    }

    /**
     * Store a new role in the database. (api.roles.store)
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
                'level' => $args['level']
            ]);

            $this->savePermissions($role, $args['permissions'] ?? []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($role, 201);
    }

    /**
     * Update an existing role in the database. (api.roles.update)
     *
     * @param Role $role
     * @param RoleUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Role $role, RoleUpdateRequest $request): JsonResponse
    {
        // Get request data
        $args = $request->validated();
        // Update fields
        DB::beginTransaction();
        try {
            $role->name = $args['name'];
            $role->level = $args['level'];
            // Remove current role permissions
            $role->permissions()->each(fn($entity) => $entity->delete());
            // Set new permissions
            $this->savePermissions($role, $args['permissions'] ?? []);
            // Save role data
            $role->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }

        return response()->json($role);
    }

    /**
     * Delete multiple roles from the database. (api.roles.delete)
     *
     * @param RoleBulkDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(RoleBulkDeleteRequest $request): JsonResponse
    {
        // Request data
        $ids = $request->validated('ids');
        // User role level
        $user_role_level = auth()->user()->role->level;
        // Get roles with levels
        $roles = Role::whereIn('id', $ids)->pluck('level', 'id')->toArray();
        // Checking the user role allows him to remove roles
        foreach ($roles as $level) {
            if ($level < $user_role_level) {
                return response()->json([
                    'error' => 'You don\'t have permissions to remove this role'
                ], 403);
            }
        }
        // Remove roles
        Role::destroy($ids);

        return response()->json(null, 204);
    }

    /**
     * Saves the permissions for a given role.
     *
     * @param Role $role The role for which the permissions will be saved.
     * @param array $permissions An array of permissions to be saved. The array should be structured as follows:
     *  - The keys of the array represent the controllers.
     *  - The values of the array represent the methods within each controller, along with their allowance.
     *    The allowance can be any value other than '0'.
     *    If the allowance is not '0', the method will be considered permitted.
     * @return void
     */
    protected function savePermissions(Role $role, array $permissions): void
    {
        if (!empty($permissions)) {
            // Loop through the provided permissions
            foreach ($permissions as $controller => $controller_methods) {
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
    }
}