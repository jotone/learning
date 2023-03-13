<?php

namespace Tests;

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
     * @var User
     */
    protected static User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        self::$actor = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')->firstWhere('roles.slug', 'superuser');
    }


    /**
     * Default routine for the API list request test
     *
     * @param callable $callback
     * @return self
     */
    protected function runListTest(callable $callback): self
    {
        $models = static::$class::factory(50)->create();
        $take = mt_rand(1, 5);
        $page = mt_rand(1, 4);

        // User list response
        $response = $this
            ->actingAs(self::$actor)
            ->getJson(route(static::$route_prefix . 'index') . "?take=$take&page=$page&order[by]=id&order[dir]=asc" . static::$uri_request)
            ->assertJsonStructure([
                'data',
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta'  => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ])
            ->assertOk();
        //Response content
        $content = json_decode($response->content());

        // Check the response contains proper models
        $query = static::$class::limit($take)->offset(($page - 1) * $take)->get()->pluck('id')->toArray();

        // Compare db query and api request
        $this->assertEmpty(array_diff($query, collect($content->data)->pluck('id')->toArray()));

        return $this;
    }

    /**
     * Default routine for the API show request test
     *
     * @param Model $model
     * @param array $fields
     * @return self
     */
    protected function runShowTest(Model $model, array $fields): self
    {
        // Get user response
        $response = $this->actingAs(self::$actor)
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

        return $this;
    }
}