<?php

namespace App\GraphQL\Schemas;

use App\GraphQL\Types\RoleType;
use App\Http\Controllers\GraphQL\Role\{MutationDestroy, MutationStore, MutationUpdate, Query};
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class RoleSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                Query::class
            ],
            'mutation' => [
                'create' => MutationStore::class,
                'update' => MutationUpdate::class,
                'destroy' => MutationDestroy::class
            ],
            'types' => [
                RoleType::class
            ],
            'middleware' => ['auth']
        ];
    }
}
