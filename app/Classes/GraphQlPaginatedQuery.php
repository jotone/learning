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
        'page' => 1
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
            ]
        ];
    }

    /**
     * Get request fields and set them to the filter values
     *
     * @param array $args
     */
    protected function buildFilters(array $args): void
    {
        if (isset($args['order_by'])) {
            $this->filters['order_by'] = $args['order_by'];
        }
        if (isset($args['order_dir'])) {
            $this->filters['order_dir'] = $args['order_dir'];
        }
        if (isset($args['per_page'])) {
            $this->filters['per_page'] = $args['per_page'];
        }
        if (isset($args['page'])) {
            $this->filters['page'] = $args['page'];
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
            ->paginate($this->filters['per_page'], $fields, 'page', $this->filters['page']);
    }
}