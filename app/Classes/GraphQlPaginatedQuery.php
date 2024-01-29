<?php

namespace App\Classes;

use GraphQL\Type\Definition\Type;
use Illuminate\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Query;

abstract class GraphQlPaginatedQuery extends Query
{
    /**
     * Filter default values
     *
     * @var array
     */
    protected array $filters = [
        'order_by' => 'id',
        'order_dir' => 'asc',
        'per_page' => 25,
        'page' => 1,
        'search' => ''
    ];

    /**
     * Filter arguments
     * @return array
     */
    protected function filterArgs(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int()
            ],
            'order_by' => [
                'name' => 'order_by',
                'type' => Type::string()
            ],
            'order_dir' => [
                'name' => 'order_dir',
                'type' => Type::string()
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int()
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string()
            ]
        ];
    }

    /**
     * Get request fields and set them to the filter values
     *
     * @param array $input
     */
    protected function buildFilters(array $input): void
    {
        if (isset($input['order_by'])) {
            $this->filters['order_by'] = $input['order_by'];
        }
        if (isset($input['order_dir'])) {
            $this->filters['order_dir'] = $input['order_dir'];
        }
        if (isset($input['per_page'])) {
            $this->filters['per_page'] = $input['per_page'];
        }
        if (isset($input['page'])) {
            $this->filters['page'] = $input['page'];
        }
        if (!empty($input['search'])) {
            $this->filters['search'] = $input['search'];
        }
    }

    /**
     * Get the collection of models
     *
     * @param $where
     * @param $relations
     * @param $fields
     * @return LengthAwarePaginator
     */
    protected function getCollection($where, $relations, $fields): LengthAwarePaginator
    {
        return $this->attributes['model']::with($relations)
            ->where($where)
            ->orderBy($this->filters['order_by'], $this->filters['order_dir'])
            ->orderBy('id', 'desc')
            ->paginate($this->filters['per_page'], $fields, 'page', $this->filters['page']);
    }
}