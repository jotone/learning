<?php

namespace App\GraphQL\Schemas;

use App\Http\Controllers\GraphQL\Course\{MutationDestroy, MutationStore, MutationUpdate, Query};
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CourseSchema implements ConfigConvertible
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
//            'middleware' => ['auth:sanctum', 'admin'],
        ];
    }
}