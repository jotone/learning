<?php

namespace App\GraphQL\Types;

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
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the role',
            ],
            'name' => [
                'type' => Type::string()
            ],
            'slug' => [
                'type' => Type::string(),
                'resolve' => fn($root, array $args) => generateUrl(empty($root->slug) ? $args['name'] : $root->slug)
            ],
            'level' => [
                'type' => Type::int()
            ]
        ];
    }
}