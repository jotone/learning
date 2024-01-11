<?php

namespace App\GraphQL\Schemas;

use App\GraphQL\Types\UserType;
use App\Http\Controllers\GraphQL\User\Query;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class UserSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                Query::class
            ],
            'mutation' => [
//                'create' => MutationStore::class,
//                'update' => MutationUpdate::class,
//                'destroy' => MutationDestroy::class
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}