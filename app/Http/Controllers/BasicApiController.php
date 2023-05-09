<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class BasicApiController extends Controller
{
    /**
     * Model default field selection
     *
     * @var string
     */
    protected string $select = '*';

    /**
     * Model field cutter
     *
     * @var array
     */
    protected array $cut = [];

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
                    $model->$field = implode("\n", count($temp) > $options[1] ? array_slice($temp, 0, $options[1])
                        : $temp);
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
     * Get list of resources
     *
     * @param Request $request
     * @param Builder $collection
     * @param int $total
     * @param string $resource
     * @param ?callable $search_callback
     * @return AnonymousResourceCollection
     */
    protected function apiList(Request $request, Builder $collection, int $total, string $resource, ?callable $search_callback = null): AnonymousResourceCollection
    {
        $args = $this->getRequest($request);

        // Apply order query
        [$by, $dir] = array_values($this->order);

        // Check if take is 0 that means take all
        if ($this->take == 0) {
            $this->take = $total;
        }

        foreach ($by as $field) {
            $collection = $collection->orderBy($field, $dir);
        }

        $collection = $collection
            // Apply search query
            ->when(!empty($args['search']) && is_callable($search_callback), fn($q) => $search_callback($q, $args['search']))
            // Apply "where" query
            ->when(!empty($args['where']), fn($q) => $this->applyWhereQuery($q, $args['where'], 'where'))
            // Apply "whereNot" query
            ->when(!empty($args['where_not']), fn($q) => $this->applyWhereQuery($q, $args['where_not'], 'whereNot'))
            // Apply "orWhere" query
            ->when(!empty($args['or_where']), fn($q) => $this->applyWhereQuery($q, $args['or_where'], 'orWhere'))
            // Apply additional query relationships
            ->when(!empty($args['with']), fn($q) => $this->applyWithQuery($q, $args['with']))
            // Paginate
            ->paginate($this->take, $this->select);

        return $resource::collection(
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
     * Get a single resource instance
     *
     * @param Request $request
     * @param Builder $query
     * @param int $id
     * @return JsonResponse
     */
    protected function apiShow(Request $request, Builder $query, int $id): JsonResponse
    {
        // >get request data
        $args = $request->only(['cut', 'select', 'with']);
        return response()->json(
            $query
                // Select specified fields
                ->when(!empty($args['select']), fn($q) => $q->select(explode(',', $args['select'])))
                // Select relation
                ->when(!empty($args['with']), fn($q) => $this->applyWithQuery($q, $args['with']))
                // Cut specified fields
                ->when(!empty($args['cut']), fn($q) => $this->applyCutQuery($q, $args['cut']))
                // Find model entity with the given ID
                ->findOrFail($id));
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
}