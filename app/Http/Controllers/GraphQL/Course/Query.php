<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Http\Controllers\GraphQL\GraphQlPaginatedQuery;
use App\Models\Course;
use Closure;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\{ResolveInfo, Type};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Query extends GraphQlPaginatedQuery
{
    protected $attributes = [
        'name' => 'courses',
        'model' => Course::class,
    ];

    /**
     * Search fields list
     *
     * @var array|string[]
     */
    private array $fields = [
        'name',
        'url',
        'lang',
        'sale_page_url',
        'expire_url',
        'status',
        'invitation_email',
        'tracking_type',
        'tracking_status',
        'optional_duration',
        'instructor_id',
        'published_at',
        'terms_conditions_enable',
        'signature_enable',
        'certificate_enable',
        'free_trial_enable',
        'free_trial_upgrade_url',
        'free_trial_upgrade_title'
    ];

    /**
     * Retrieves the type of data returned by this method.
     *
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::paginate('Course');
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
            'url' => [
                'name' => 'url',
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
                    if ($field === 'instructor_id') {
                        // Filter users based on multiple instructor IDs
                        $query->whereIn('instructor_id', $input['instructor_id']);
                    } else {
                        $query->where($field, $input[$field]);
                    }
                }
            }

            if (!empty($input['search'])) {
                $search = mb_strtolower($input['search']);
                $query->whereRaw("LOWER(name) LIKE '%$search%' OR LOWER(url) LIKE '%$search%'");
            }
        };

        $fields = $getSelectFields()->getSelect();

        // Provide a custom fields resolver
        $query = [
            'select' => ['courses.*'],
            'custom' => []
        ];
        // Get "users_count" property
        if (in_array('courses.users_count', $fields)) {
            unset($fields[array_search('courses.users_count', $fields)]);
            $fields = array_values($fields);
            $query['count'] = ['users'];
        }
        // Get "instructor_email" property
        if (in_array('courses.instructor_email', $fields)) {
            unset($fields[array_search('courses.instructor_email', $fields)]);

            $query['select'][] = 'instructors.email as instructor_email';
            $query['custom'][] = fn($q) => $q->leftJoin(
                DB::raw('(SELECT id, email FROM users) instructors'),
                fn($join) => $join->on('courses.instructor_id', '=', 'instructors.id')
            );
        }

        return $this->getCollection(
            $where,
            $getSelectFields()->getRelations(),
            array_values($fields),
            $query
        );
    }
}