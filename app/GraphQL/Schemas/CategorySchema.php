<?php

namespace App\GraphQL\Schemas;

use App\Http\Controllers\GraphQL\Category\Query;
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
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}