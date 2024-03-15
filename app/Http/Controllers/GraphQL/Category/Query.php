<?php

namespace App\Http\Controllers\GraphQL\Category;

use App\Http\Controllers\GraphQL\GraphQlPaginatedQuery;
use App\Models\Category;
use App\Models\Course;
use Closure;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends GraphQlPaginatedQuery
{
    protected $attributes = [
        'name' => 'categories',
        'model' => Category::class,
    ];

    /**
     * Retrieves the type of data returned by this method.
     *
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::paginate('Category');
    }

    /**
     * Search fields list
     *
     * @var array|string[]
     */
    private array $fields = [
        'name',
        'url',
        'type'
    ];

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
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string()
            ],
            'created_at' => [
                'name' => 'created_at',
                'type' => Type::string(),
            ]
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


        $where = function ($query) use ($input, &$fields) {
            foreach ($this->fields as $field) {
                if ($field === 'type') {
                    switch($input[$field]) {
                        case 'course':
                        case 'courses':
                            $input[$field] = Course::class;
                            break;
                    }
                }
                if (isset($input[$field])) {
                    $query->where($field, $input[$field]);
                }
            }

            if (!empty($input['search'])) {
                $search = mb_strtolower($input['search']);
                $query->whereRaw("LOWER(name) LIKE '%$search%' OR LOWER(url) LIKE '%$search%'");
            }
        };

        return $this->getCollection(
            $where,
            $getSelectFields()->getRelations(),
            $getSelectFields()->getSelect()
        );
    }
}