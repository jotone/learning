<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Role;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;

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
     * @param array $input
     * @return null|Error
     */
    public function resolve($root, array $input): ?Error
    {
        $role = Role::findOrFail($input['id']);

        if ($this->checkUserRoleLevel($role->level)) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        $role->delete();

        return null;
    }
}