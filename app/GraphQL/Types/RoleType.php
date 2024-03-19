<?php

namespace App\GraphQL\Types;

use App\Services\Str;
use App\Models\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoleType extends GraphQLType
{
    /**
     * @var string[] $attributes
     */
    protected $attributes = [
        'name' => 'Role',
        'description' => 'A role',
        'model' => Role::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::nonNull(Type::int())],
            'name' => ['type' => Type::string()],
            'slug' => [
                'type' => Type::string(),
                'resolve' => fn($role, $input) => Str::generateUrl(empty($role->slug) ? $input['name'] : $role->slug)
            ],
            'level' => ['type' => Type::int()],
            'created_at' => ['type' => Type::string()],
            'updated_at' => ['type' => Type::string()]
        ];
    }
}