<?php

namespace App\GraphQL\Schemas;

use App\GraphQL\Types\RoleType;
use App\Http\Controllers\GraphQL\Role\RolesQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class RoleSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                RolesQuery::class
            ],

            'mutation' => [
                // ExampleMutation::class,
            ],

            'types' => [
                RoleType::class
            ],
//            'middleware' => ['auth']
        ];
    }
}
