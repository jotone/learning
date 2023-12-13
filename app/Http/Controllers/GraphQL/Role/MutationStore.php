<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use GraphQL\Type\Definition\{Type, ResolveInfo};
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationStore extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'create'
    ];

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Role');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string', 'unique:roles,slug'],
            ],
            'level' => [
                'name' => 'level',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'numeric', 'min:0', 'max:255']
            ],
            'permissions' => [
                'name' => 'permissions',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['nullable', 'string']
            ]
        ];
    }

    /**
     * Store role
     *
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return Role
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Role
    {
        $role = Role::create($args);

        $this->savePermissions($role, json_decode(base64_decode($args['permissions']), true));

        return $role;
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