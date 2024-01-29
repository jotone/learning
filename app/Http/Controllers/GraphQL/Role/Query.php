<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Classes\GraphQlPaginatedQuery;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
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
     * @param array $input
     * @param $context
     * @param Request $request
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return LengthAwarePaginator
     */
    public function resolve($root, array $input, $context, Request $request, ResolveInfo $resolveInfo, Closure $getSelectFields): LengthAwarePaginator
    {
        $this->buildFilters($input);

        $where = function ($query) use ($input) {
            if (isset($input['id'])) {
                $query->where('id', $input['id']);
            }

            if (isset($input['slug'])) {
                $query->where('slug', $input['slug']);
            }

            if (!empty($input['search'])) {
                $search = mb_strtolower($input['search']);
                $query->whereRaw("LOWER(name) LIKE '%$search%' OR slug LIKE '%$search%' OR level LIKE '%$search%'");
            }
        };

        $fields = $getSelectFields()->getSelect();
        $relations = $getSelectFields()->getRelations();

        return $this->getCollection($where, $relations, $fields);
    }
}