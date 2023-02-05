<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\{RoleStoreRequest, RoleUpdateRequest};
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Str;

class RoleController extends Controller
{

    /**
     * Default paginator items per page
     *
     * @var int
     */
    protected int $take = 25;

    /**
     * Model default field selection
     *
     * @var string
     */
    protected string $select = '*';

    /**
     * Model default order query parameters
     *
     * @var array
     */
    protected array $order = [
        'by' => ['id'],
        'dir' => 'desc'
    ];

    /**
     * Model field cutter
     *
     * @var array
     */
    protected array $cut = [];

    /**
     * Roles list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $args = $this->getRequest($request);

        $collection = Role::query();

        // Apply order query
        [$by, $dir] = array_values($this->order);

        foreach ($by as $field) {
            $collection = $collection->orderBy($field, $dir);
        }

        // Apply search query
        if (!empty($args['search'])) {
            $search = mb_strtolower($args['search']);
            $collection = $collection->whereRaw("LOWER(name) LIKE '%$search%' OR LOWER(slug) LIKE '%$search%' OR level LIKE '%$search%'");
        }

        // Apply "where" query
        if (!empty($args['where'])) {
            $collection = $this->applyWhereQuery($collection, $args['where'], 'where');
        }
        // Apply "whereNot" query
        if (!empty($args['where_not'])) {
            $collection = $this->applyWhereQuery($collection, $args['where_not'], 'whereNot');
        }
        // Apply "orWhere" query
        if (!empty($args['or_where'])) {
            $collection = $this->applyWhereQuery($collection, $args['or_where'], 'orWhere');
        }
        // Apply additional query relationships
        if (!empty($args['with'])) {
            $collection = $this->applyWithQuery($collection, $args['with']);
        }

        // Check if take is 0 that means take all
        if ($this->take == 0) {
            $this->take = Role::count();
        }

        $collection = $collection->paginate($this->take, $this->select);
        return response()->json(
        // Modify collection
            $collection->setCollection(
                $collection->getCollection()
                    // Apply map function
                    ->transform(function ($model) {
                        if (!empty($this->cut)) {
                            $model = $this->applyCutQuery($model, $this->cut);
                        }
                        return $model;
                    })
            )
        );
    }

    /**
     * Specified role data
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $args = $request->only(['cut', 'select', 'with']);
        $role = Role::query();
        // Select specified fields
        if (!empty($args['select'])) {
            $role = $role->select(explode(',', $args['select']));
        }
        // Select relation
        if (!empty($args['with'])) {
            $role = $this->applyWithQuery($role, $args['with']);
        }

        if (!empty($args['cut'])) {
            $role = $this->applyCutQuery($role, $args['cut']);
        }
        return response()->json($role->findOrFail($id));
    }

    /**
     * Create Role
     *
     * @param RoleStoreRequest $request
     * @return JsonResponse
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        return response()->json(Role::create($request->validated()), 201);
    }

    /**
     * Update Role
     *
     * @param Role $role
     * @param RoleUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Role $role, RoleUpdateRequest $request): JsonResponse
    {
        $role->update($request->validated());

        return response()->json($role);
    }

    /**
     * Remove Role
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([], 204);
    }

    /**
     * Cutting fields apply
     *
     * @param Model $model
     * @param array $cut
     * @return Model
     */
    protected function applyCutQuery(Model $model, array $cut): Model
    {
        foreach ($cut as $field => $options) {
            switch ($options[0]) {
                // Cut rows
                case 'row':
                    $temp = explode("\n", $model->$field);
                    $model->$field = implode(
                        "\n",
                        count($temp) > $options[1]
                            ? array_slice($temp, 0, $options[1])
                            : $temp
                    );
                    $model->$field .= count($temp) > $options[1] ? '...' : '';
                    break;
                // Cut words
                case 'word':
                    $model->$field = Str::words($model->$field, $options[1], '...');
                    break;
                // Cut symbols
                case 'char':
                    if (!empty($model->$field) && strlen($model->$field) > $options[1]) {
                        $model->$field = mb_substr($model->$field, 0, $options[1]) . '...';
                    }
                    break;
            }
        }

        return $model;
    }

    /**
     * Set class variables and filter request data
     *
     * @param Request $request
     * @return array
     */
    protected function getRequest(Request $request): array
    {
        // Get request data
        $args = $request->only(['take', 'order', 'select', 'where', 'search', 'with', 'cut']);

        // Set query selectable fields
        if (!empty($args['select'])) {
            $this->select = $args['select'];
        }
        // Set take option
        if (isset($args['take'])) {
            $this->take = $args['take'];
        }
        // Set order filter fields
        if (!empty($args['order']['by'])) {
            $this->order['by'] = str_contains($args['order']['by'], ',')
                ? explode(',', $args['order']['by'])
                : [$args['order']['by']];
        }
        // Set order direction
        if (!empty($args['order']['dir'])) {
            $this->order['dir'] = $args['order']['dir'] == 'desc' ? 'desc' : 'asc';
        }

        // Set fields cut options
        if (!empty($args['cut'])) {
            foreach ($args['cut'] as $field => $options) {
                $this->cut[$field] = explode(',', $options);
            }
        }

        return $args;
    }

    /**
     * Apply different "where" queries on collection
     *
     * @param Builder $collection
     * @param array $where
     * @param string $func
     * @return Builder
     */
    protected function applyWhereQuery(Builder $collection, array $where, string $func): Builder
    {
        foreach ($where as $key => $value) {
            if (empty($value)) {
                $collection = $collection->{$func . 'Null'}($key);
            } else {
                $collection = str_contains($value, ',')
                    ? $collection->{$func . 'In'}($key, explode(',', $value))
                    : $collection->{$func}($key, $value);
            }
        }

        return $collection;
    }

    /**
     * Apply "with" query
     *
     * @param Builder $collection
     * @param string|array $with
     * @return Builder
     */
    protected function applyWithQuery(Builder $collection, $with): Builder
    {
        // Check if "with" parameter is string and convert it to array
        if (!is_array($with)) {
            $with = [$with];
        }
        // Request query values
        $values = [];
        // Countable results
        $withQuery = [];
        // Relationships result
        $withCount = [];
        // Fill "values" array
        foreach ($with as $val) {
            // Convert value to array if the string contains coma
            if (str_contains($val, ',')) {
                $values = array_merge($values, explode(',', $val));
            } else {
                $values[] = $val;
            }
        }
        // Treat "values" array
        foreach ($values as $val) {
            // Dot signifies that string contains sub-query
            if (str_contains($val, '.')) {
                // Separate string value as target field and relation property
                [$field, $prop] = explode('.', $val);
                // The 'count' property signifies that the request wants just a relation amount number as result
                if ($prop == 'count') {
                    $withCount[] = $field;
                } else {
                    // Other values are treating as relations on the query model
                    $withQuery[$field] = function ($q) use ($prop) {
                        return $q->with(explode(',', $prop));
                    };
                }
            } else {
                $withQuery[] = $val;
            }
        }
        // Apply relation "count" sub-query
        if (!empty($withCount)) {
            $collection = $collection->withCount($withCount);
        }
        // Apply "with" query
        if (!empty($withQuery)) {
            $collection = $collection->with($withQuery);
        }

        return $collection;
    }

}