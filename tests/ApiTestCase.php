<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApiTestCase extends TestCase
{
    /**
     * Tested entity class
     *
     * @var string
     */
    protected static string $class;

    /**
     * Tested api route prefix
     *
     * @var string
     */
    protected static string $route_prefix;

    /**
     * url query string
     *
     * @var string
     */
    protected static string $uri_request = '';

    /**
     * User with access to api
     *
     * @var User|null
     */
    protected static ?User $actor;

    /**
     * Response structure for API response.
     *
     * @var array
     * @property array $data The main data of the response.
     * @property array $links The pagination links of the response.
     *      - string $first The link to the first page.
     *      - string $last The link to the last page.
     *      - string $prev The link to the previous page.
     *      - string $next The link to the next page.
     * @property array $meta The metadata of the response.
     *      - int $current_page The current page number.
     *      - int $from The starting item index.
     *      - int $last_page The last page number.
     *      - array $links The pagination links.
     *      - string $path The URL path of the current page.
     *      - int $per_page The number of items per page.
     *      - int $to The ending item index.
     *      - int $total The total number of items.
     */
    protected array $responseStructure = [
        'data',
        'links' => [
            'first',
            'last',
            'prev',
            'next'
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'links',
            'path',
            'per_page',
            'to',
            'total'
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();

        static::$actor = User::select('users.*')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->firstWhere('roles.slug', 'superuser');

        if (!static::$actor) {
            static::$actor = User::factory()->create([
                'role_id' => Role::where('level', 0)->value('id')
            ]);
        }
    }


    /**
     * Default routine for the API list request test
     *
     * @param callable $callback
     * @return void
     */
    protected function runListTest(callable $callback): void
    {
        $models = static::$class::factory(50)->create();
        $take = mt_rand(1, 5);
        $page = mt_rand(1, 4);

        // User list response
        $response = $this->actingAs(static::$actor)
            ->getJson(route(static::$route_prefix . 'index') . '?' . http_build_query([
                    'take' => $take,
                    'page' => $page,
                    'order' => [
                        'by' => 'id',
                        'dir' => 'asc'
                    ]
                ]))
            ->assertJsonStructure($this->responseStructure)
            ->assertOk();
        //Response content
        $content = json_decode($response->content());

        // Check the response contains proper models
        $query = static::$class::limit($take)->offset(($page - 1) * $take)->get()->pluck('id')->toArray();

        // Compare db query and api request
        $this->assertEmpty(array_diff($query, collect($content->data)->pluck('id')->toArray()));

        $callback($content, $models);
    }

    /**
     * Default routine for the API show request test
     *
     * @param Model $model
     * @param array $fields
     * @return void
     */
    protected function runShowTest(Model $model, array $fields): void
    {
        // Get user response
        $response = $this->actingAs(static::$actor)
            ->getJson(route(static::$route_prefix . 'show', $model->id))
            ->assertJsonStructure($fields)
            ->assertOk();
        //Response content
        $content = json_decode($response->content(), 1);
        // Flip awaiting fields to apply them on the key filter functions
        $flipped_fields = array_flip($fields);
        // Get necessary fields from response content
        $response_result = array_intersect_key($content, $flipped_fields);
        // Check the fields exists on the database
        $this->assertDatabaseHas($model->getTable(), $response_result);
        // Check response is equal to the actual model
        $this->assertTrue(array_intersect_key($model->toArray(), $flipped_fields) == $response_result);
    }

    /**
     * Default routine for the API store request test
     *
     * @param array $fields
     * @param callable|null $callback
     * @return void
     */
    protected function runStoreTest(array $fields, ?callable $callback = null): void
    {
        $model = static::$class::factory()->make();
        $table = $model->getTable();
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $model->{$field};
        }

        $response = $this->actingAs(static::$actor)
            ->postJson(route(static::$route_prefix . 'store'), $values)
            ->assertJsonFragment($values)->assertCreated();

        $content = json_decode($response->content());
        $values['id'] = $content->id;
        $this->assertDatabaseHas($table, $values);

        // Find model entity and run callback function
        is_callable($callback) && $callback($model::class::find($content->id));
    }

    /**
     * Default routine for the API update request test
     *
     * @param Model $model
     * @param array $fields
     * @return void
     */
    protected function runUpdateTest(Model $model, array $fields): void
    {
        $new = static::$class::factory()->make();

        $missing = array_intersect_key($model->toArray(), array_flip(['id', ...$fields]));

        $update = [];
        foreach ($fields as $key) {
            $update[$key] = trim($new->$key);
        }

        $modified = ['id' => $model->id, ...$update];

        $this->actingAs(static::$actor)
            ->putJson(route(static::$route_prefix . 'update', $model->id), $update)
            ->assertJsonFragment($modified)
            ->assertOk();

        $this->assertDatabaseHas($model->getTable(), $modified)->assertDatabaseMissing($model->getTable(), $missing);
    }

    /**
     * Default routine for the API delete request test
     *
     * @param array $items
     * @param string $table
     * @return void
     */
    protected function runSortingTest(array $items, string $table): void
    {
        $this->actingAs(static::$actor)
            ->patch(route(static::$route_prefix . 'sort'), [
                'items' => $items
            ])
            ->assertOk();

        foreach ($items as $pos => $id) {
            $this->assertDatabaseHas($table, [
                'id' => $id,
                'position' => $pos
            ]);
        }
    }

    /**
     * Default routine for the API delete request test
     *
     * @param Model $model
     * @param ?callable $callback
     * @return void
     */
    protected function runDeleteTest(Model $model, ?callable $callback = null): void
    {
        $route = static::$route_prefix . 'destroy';
        $this->actingAs(static::$actor)
            ->assertModelExists($model)
            ->deleteJson(route($route, $model->id))
            ->assertNoContent();

        $this->assertModelMissing($model);

        is_callable($callback) && $callback($model);
    }

    /**
     * Default routine for the API bulkDelete request test
     *
     * @param string $table
     * @param array $ids
     * @return void
     */
    protected function runBulkDeleteTest(string $table, array $ids): void
    {
        $route = static::$route_prefix . 'delete';

        $this->actingAs(static::$actor)
            ->deleteJson(route($route), ['ids' => $ids])
            ->assertNoContent();

        foreach ($ids as $id) {
            $this->assertDatabaseMissing($table, ['id' => $id]);
        }
    }

    /**
     * Build the route URL with parameters for the given type
     *
     * @param int $take The number of items to retrieve per page
     * @param int $page The page number to retrieve
     * @param string $type The type of route (default is 'index')
     * @return string The constructed URL with parameters
     */
    protected function buildRouteWithParams(int $take, int $page, string $type = 'index'): string
    {
        return route(static::$route_prefix . $type) . '?' . http_build_query([
            'take' => $take,
            'page' => $page,
            'order' => [
                'by' => 'id',
                'dir' => 'asc'
            ]
        ]);
    }
}