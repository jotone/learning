<?php

namespace App\Http\Controllers\GraphQL\User;

use App\Http\Controllers\GraphQL\GraphQlPaginatedQuery;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends GraphQlPaginatedQuery
{
    protected $attributes = [
        'name' => 'users',
        'model' => User::class,
    ];

    /**
     * Search fields list
     *
     * @var array|string[]
     */
    private array $fields = [
        'first_name',
        'last_name',
        'email',
        'status',
        'activated_at',
        'last_activity',
        'time_online',
        'role_id',
        'timezone',
        'country',
        'city',
        'region',
        'zip',
        'phone',
        'shirt_size',
        'created_at'
    ];

    /**
     * Retrieves the type of data returned by this method.
     *
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::paginate('User');
    }

    public function args(): array
    {
        return array_merge($this->filterArgs(), [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::string(),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
            ],
            'timezone' => [
                'name' => 'timezone',
                'type' => Type::string(),
            ],
            'country' => [
                'name' => 'country',
                'type' => Type::string(),
            ],
            'region' => [
                'name' => 'region',
                'type' => Type::string(),
            ],
            'city' => [
                'name' => 'city',
                'type' => Type::string(),
            ],
            'zip' => [
                'name' => 'zip',
                'type' => Type::string(),
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
            ],
            'shirt_size' => [
                'name' => 'status',
                'type' => Type::string(),
            ],
            'compromised' => [
                'name' => 'compromised',
                'type' => Type::int()
            ],
            'role_id' => [
                'name' => 'role_id',
                'type' => Type::listOf(Type::int())
            ],
            'activated_at' => [
                'name' => 'activated_at',
                'type' => Type::string(),
            ],
            'last_activity' => [
                'name' => 'last_activity',
                'type' => Type::string(),
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
                if (isset($input[$field])) {
                    if ($field === 'role_id') {
                        // Filter users based on multiple role IDs
                        $query->whereIn('role_id', $input['role_id']);
                    } else {
                        $query->where($field, $input[$field]);
                    }
                }
            }

            if (!empty($input['search'])) {
                $search = mb_strtolower($input['search']);
                $query->whereRaw("LOWER(first_name) LIKE '%$search%' OR LOWER(last_name) LIKE '%$search%' OR LOWER(email) LIKE '%$search%'");
            }
        };

        return $this->getCollection(
            $where,
            $getSelectFields()->getRelations(),
            $getSelectFields()->getSelect()
        );
    }
}