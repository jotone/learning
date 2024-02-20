<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\{Permission, Role};
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

abstract class RoleMutation extends Mutation
{
    const ACCESS_FORBIDDEN_MESSAGE = 'Operation is forbidden.';

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Role');
    }

    /**
     * Save role permissions
     *
     * @param Role $role
     * @param array $permissions
     * @return array
     */
    protected function savePermissions(Role $role, array $permissions): array
    {
        $result = [];
        if (!empty($permissions)) {
            foreach ($permissions as $controller => $methods) {
                // If there are permitted methods for this controller, create a permission
                if (!empty($methods)) {
                    $result[] = Permission::create([
                        'role_id' => $role->id,
                        'controller' => $controller,
                        'allowed_methods' => $methods
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Check given role level with auth user role level
     *
     * @param int $level
     * @return bool
     */
    protected function checkUserRoleLevel(int $level): bool
    {
        return $level < auth()->user()->role->level;
    }
}