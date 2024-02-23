<?php

namespace App\GraphQL\Schemas;

use App\Http\Controllers\GraphQL\Course\Query;
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
            ],
            'middleware' => ['auth:sanctum', 'admin'],
        ];
    }
}