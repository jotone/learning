<?php

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    /**
     * @var string[] $attributes
     */
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'first_name' => ['type' => Type::string()],
            'last_name' => ['type' => Type::string(),],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn($user) => mb_strtolower($user->email)
            ],
            'img_url' => ['type' => GraphQL::type('StringOrListOfStrings')],
            'about' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
            'activated_at' => ['type' => Type::string()],
            'last_activity' => ['type' => Type::string()],
            'time_online' => ['type' => Type::int()],
            'compromised' => ['type' => Type::boolean()],
            'compromised_threshold' => ['type' => Type::int()],
            'role_id' => ['type' => Type::int()],
            'timezone' => ['type' => Type::string()],
            'country' => ['type' => Type::string()],
            'region' => ['type' => Type::string()],
            'city' => ['type' => Type::string()],
            'address' => ['type' => Type::string()],
            'ext_addr' => ['type' => Type::string()],
            'zip' => ['type' => Type::string()],
            'phone' => ['type' => Type::string()],
            'shirt_size' => ['type' => Type::string()],
            'signature' => ['type' => Type::string()],
            'signature_ip' => ['type' => Type::string()],
            'signature_date' => ['type' => Type::string()],
            'created_at' => ['type' => Type::string()],
            'updated_at' => ['type' => Type::string()],
            'role' => [
                'type' => GraphQL::type('Role'),
                'resolve' => fn($user) => $user->role
            ]
        ];
    }
}