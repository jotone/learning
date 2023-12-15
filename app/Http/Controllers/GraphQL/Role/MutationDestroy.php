<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Role;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\{Type, ResolveInfo};

class MutationDestroy extends RoleMutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'destroy'
    ];
    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'numeric', 'exists:roles,id']
            ],
        ];
    }

    /**
     * Remove role
     *
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return null
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $role = Role::findOrFail($args['id']);

        if ($this->checkUserRoleLevel($role->level)) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        $role->delete();

        return null;
    }
}