<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\{Permission, Role};
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

abstract class RoleMutation extends Mutation
{
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