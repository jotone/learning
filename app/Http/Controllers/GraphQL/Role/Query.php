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
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
            ],
            'level' => [
                'name' => 'level',
                'type' => Type::int(),
            ],
            'created_at' => [
                'name' => 'created_at',
                'type' => Type::string(),
            ],
            'updated_at' => [
                'name' => 'updated_at',
                'type' => Type::string()
            ]
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

            if (isset($args['name'])) {
                $query->where('name', 'LIKE', "%{$args['name']}%");
            }

            if (isset($args['slug'])) {
                $query->where('slug', 'LIKE', "%{$args['slug']}%");
            }

            if (isset($args['level'])) {
                $query->where('level', 'LIKE', "%{$args['level']}%");
            }
        };

        $fields = $getSelectFields()->getSelect();
        $relations = $getSelectFields()->getRelations();

        return $this->getCollection($where, $relations, $fields);
    }
}