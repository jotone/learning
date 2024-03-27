<?php

namespace App\Http\Controllers\GraphQL;

use GraphQL\Type\Definition\Type;
use Illuminate\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Query;

abstract class GraphQlPaginatedQuery extends Query
{
    const PHP_INT32 = 2147483647;

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
     * @param array $extra_queries
     * @return LengthAwarePaginator
     */
    protected function getCollection($where, $relations, $fields, array $extra_queries = []): LengthAwarePaginator
    {
        $query = $this->attributes['model']::with($relations)
            ->where($where)
            ->orderBy($this->filters['order_by'], $this->filters['order_dir'])
            ->orderBy('id', 'desc');

        if (!empty($extra_queries)) {
            if (isset($extra_queries['select'])) {
                $query->select($extra_queries['select']);
            }
            if (isset($extra_queries['count']) && (is_array($extra_queries['count']) || is_string($extra_queries['count']))) {
                $query->withCount($extra_queries['count']);
            }
            if (isset($extra_queries['custom']) && is_array($extra_queries['custom'])) {
                foreach ($extra_queries['custom'] as $custom_query) {
                    if (is_callable($custom_query)) {
                        $query = $custom_query($query);
                    }
                }
            }
        }

        return $query->paginate(
            $this->filters['per_page'] < 1 ? self::PHP_INT32 : $this->filters['per_page'],
            $fields,
            'page',
            $this->filters['page']
        );
    }
}