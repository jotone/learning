<?php

namespace App\GraphQL\Schemas;

use App\Http\Controllers\GraphQL\Category\{MutationDestroy, MutationSort, MutationStore, MutationUpdate, Query};
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CategorySchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                Query::class
            ],
            'mutation' => [
                'create' => MutationStore::class,
                'sort' => MutationSort::class,
                'update' => MutationUpdate::class,
                'destroy' => MutationDestroy::class
            ],
            'middleware' => ['auth:sanctum', 'admin']
        ];
    }
}