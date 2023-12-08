<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class BaseApiController extends Controller
{
    /**
     * Default paginator items per page. Number of items to take
     *
     * @var int
     */
    protected int $take = 25;

    /**
     * Model default order query parameters
     * Order details
     *
     * @var array $order
     *  - 'by'  : string The column name to sort the data by.
     *  - 'dir' : string The sorting direction ('asc' or 'desc').
     */
    protected array $order = [
        'by' => 'created_at',
        'dir' => 'desc'
    ];

    /**
     * Select statement fields
     *
     * @var array
     */
    protected array $select = [];

    /**
     * Model field cutter
     *
     * @var array
     */
    protected array $cut = [];

    /**
     * Query map function
     *
     * @var array
     */
    protected array $callable = [];

    /**
     * Get list of resources
     *
     * @param Request $request
     * @param Builder $collection
     * @param int $total
     * @param string $resource
     * @param callable|null $search_callback
     * @return AnonymousResourceCollection
     */
    protected function apiList(Request $request, Builder $collection, int $total, string $resource, ?callable $search_callback = null): AnonymousResourceCollection
    {
        $args = $this->getRequest($request);

        // Apply order query
        [$by, $dir] = array_values($this->order);
        if (str_contains($by, ',')) {
            $by = explode(',', $by);
            foreach ($by as $field) {
                $collection = $collection->orderBy($field, $dir);
            }
        } else {
            $collection = $collection->orderBy($by, $dir);
        }

        // Check if exists select option
        if (!empty($this->select)) {
            $probe_model = $collection->first();
            if ($probe_model) {
                // Check selected fields are in the model "fillable" attributes
                $collection = $collection->select(array_intersect($this->select, array_merge(['id'], app($probe_model::class)->getFillable())));
            }
        }

        // Check if take is 0 that means take all
        if ($this->take == 0) {
            $this->take = $total;
        }

        $collection = $collection
            // Apply search query
            ->when(!empty($args['search']) && is_callable($search_callback), fn($q) => $search_callback($q, $args['search']))
            // Apply "where" query
            ->when(!empty($args['where']), fn($q) => $this->applyWhereQuery($q, $args['where'], 'where'))
            // Apply "where greater" query
            ->when(!empty($args['whereGt']), fn($q) => $this->applyWhereQuery($q, $args['whereGt'], 'where', '>='))
            // Apply "where less" query
            ->when(!empty($args['whereLt']), fn($q) => $this->applyWhereQuery($q, $args['where'], 'where', '<='))
            // Apply "whereHas" query
            ->when(!empty($args['whereHas']), fn($q) => $this->applyWhereHasQuery($q, $args['whereHas']))
            // Apply "whereNot" query
            ->when(!empty($args['where_not']), fn($q) => $this->applyWhereQuery($q, $args['where_not'], 'whereNot'))
            // Apply "orWhere" query
            ->when(!empty($args['or_where']), fn($q) => $this->applyWhereQuery($q, $args['or_where'], 'orWhere'))
            // Apply additional query relationships
            ->when(!empty($args['with']), fn($q) => $this->applyWithQuery($q, $args['with']))
            // Paginate
            ->paginate($this->take);

        return $resource::collection(
        // Modify collection
            $collection->setCollection(
            // Apply map function
                $collection->getCollection()->transform(function ($model) {
                    foreach ($this->callable as $callback) {
                        $model->$callback = $model->$callback();
                    }
                    return !empty($this->cut) ? $this->applyCutQuery($model, $this->cut) : $model;
                })
            )
        );
    }

    /**
     * Apply "cut" query to model fields
     *
     * @param Model $model
     * @param array $cut
     * @return Model
     */
    protected function applyCutQuery(Model $model, array $cut): Model
    {
        foreach ($cut as $field => $opts) {
            switch ($opts[0]) {
                // Cut rows
                case 'row':
                    $tmp = explode("\n", $model->$field);
                    $model->$field = implode("\n", count($tmp) > $opts[1] ? array_slice($tmp, 0, $opts[1]) : $tmp);
                    $model->$field .= count($tmp) > $opts[1] ? '...' : '';
                    break;
                // Cut words
                case 'word':
                    $model->$field = Str::words($model->$field, $opts[1], '...');
                    break;
                // Cut symbols
                case 'char':
                    if (!empty($model->$field) && strlen($model->$field) > $opts[1]) {
                        $model->$field = mb_substr($model->$field, 0, $opts[1]) . '...';
                    }
                    break;
            }
        }

        return $model;
    }

    /**
     * Apply whereHas query for many-to-many relation
     *
     * @param Builder $collection
     * @param array $where Key-value pairs representing the table and field to apply the "whereHas" query, along with the operator and value.
     *    The key should be in the format "table,field" and the value should be in the format "operator,value".
     *    Supported operators are 'between', 'date', 'in', 'gt', 'lt'. If no operator is specified, the default operator is used.
     * @return Builder The updated collection with the "whereHas" query applied.
     */
    protected function applyWhereHasQuery(Builder $collection, array $where): Builder
    {
        foreach ($where as $key => $value) {
            // Split $key as table,field
            $options = explode(',', $key);
            if (2 >= count($options)) {
                $options[] = '';
            }

            $collection = $collection->whereHas(
                $options[0],
                match ($options[2]) {
                    'between' => fn($q) => $q->whereBetween($options[1], explode(',', $value)),
                    'date' => fn($q) => $q->whereBetween($options[1], [$value . ' 00:00:00', $value . ' 23:59:59']),
                    'in' => fn($q) => $q->whereIn($options[1], explode(',', $value)),
                    'gt' => fn($q) => $q->where($options[1], '>=', $value[0]),
                    'lt' => fn($q) => $q->where($options[1], '=<', $value[0]),
                    default => fn($q) => $q->where($options[1], $value[0])
                }
            );
        }

        return $collection;
    }

    /**
     * Apply different "where" queries on collection
     *
     * @param Builder $collection
     * @param array $where
     * @param string $func
     * @param string|null $equation
     * @return Builder
     */
    protected function applyWhereQuery(Builder $collection, array $where, string $func, ?string $equation = null): Builder
    {
        foreach ($where as $key => $value) {
            if (empty($value) && $value !== '0') {
                // Value is empty
                $collection = $collection->{$func . 'Null'}($key);
            } else {
                if (Carbon::hasFormat($value, 'Y-m-d')) {
                    // Value is a date range
                    $collection = $collection->{$func . 'Between'}($key, [$value . ' 00:00:00', $value . ' 23:59:59']);
                } else {
                    // Value is not a date range
                    $collection = str_contains($value, ',')
                        ? $collection->{$func . 'In'}($key, explode(',', $value))
                        : (empty($equation) ? $collection->{$func}($key, $value) : $collection->{$func}($key, $equation, $value));
                }
            }
        }

        return $collection;
    }

    /**
     * Apply with query to the given collection.
     *
     * @param Builder $collection The collection to apply the query to.
     * @param array|string $with The relationships to load in the query. If a string is provided, it will be converted to an array.
     * @return Builder The collection with the applied query.
     */
    protected function applyWithQuery(Builder $collection, array|string $with): Builder
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
                } else if (str_contains($val, '[')) {
                    preg_match("/\\[(.*?)\\]/", $val, $match);

                    $withQuery[strstr($val, '[', true)] = fn ($q) => $q->select(explode(";", $match[1]));
                }else {
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

    /**
     * Get the request data and set the options for the query.
     *
     * @param Request $request
     * @return array
     */
    protected function getRequest(Request $request): array
    {
        // Get request data
        $args = $request->only(['take', 'order', 'where', 'whereGt', 'whereLt', 'whereHas', 'select', 'search', 'with', 'cut', 'call']);

        // Set select option
        if (isset($args['select'])) {
            $this->select = explode(',', $args['select']);
        }
        // Set take option
        if (isset($args['take'])) {
            $this->take = $args['take'];
        }
        // Set order filter fields
        if (!empty($args['order']['by'])) {
            $this->order['by'] = $args['order']['by'];
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

        // Set query call option
        if (!empty($args['call'])) {
            $this->callable = explode(',', $args['call']);
        }

        return $args;
    }
}