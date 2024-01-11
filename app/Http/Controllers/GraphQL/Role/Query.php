<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Classes\GraphQlPaginatedQuery;
use App\Models\Role;
use Closure;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends GraphQlPaginatedQuery
{
    protected $attributes = [
        'name' => 'roles',
        'model' => Role::class,
    ];

    /**
     * Retrieves the type of data returned by this method.
     *
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::paginate('Role');
    }

    public function args(): array
    {
        return array_merge($this->filterArgs(), [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
            ],
        ]);
    }

    /**
     * Run query
     *
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return LengthAwarePaginator
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): LengthAwarePaginator
    {
        $this->buildFilters($args);

        $where = function ($query) use ($args) {
            if (isset($args['id'])) {
                $query->where('id', $args['id']);
            }

            if (isset($args['slug'])) {
                $query->where('slug', $args['slug']);
            }

            if (!empty($args['search'])) {
                $search = mb_strtolower($args['search']);
                $query->whereRaw("LOWER(name) LIKE '%$search%' OR slug LIKE '%$search%' OR level LIKE '%$search%'");
            }
        };

        $fields = $getSelectFields()->getSelect();
        $relations = $getSelectFields()->getRelations();

        return $this->getCollection($where, $relations, $fields);
    }
}