<?php

namespace App\Http\Controllers\GraphQL\User;

use App\Models\User;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;

class MutationDestroy extends UserMutation
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
                'rules' => ['required', 'numeric', 'exists:users,id']
            ],
        ];
    }

    /**
     * Remove user
     *
     * @param $root
     * @param $args
     * @return null|Error
     */
    public function resolve($root, $args): ?Error
    {
        $user = User::findOrFail($args['id']);
        // Check user is not a student and check if user is deleting himself
        if ($this->checkUserRole($user->role) || $user->id == auth()->id()) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        $user->delete();

        return null;
    }
}