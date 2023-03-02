<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Traits\ModelGeneratorsTrait;

class RoleApiTest extends TestCase
{
    use ModelGeneratorsTrait;

    protected static string $route_prefix = '';

    protected function setUp(): void
    {
        parent::setUp();

        self::$route_prefix = 'api.roles.';
    }

    /**
     * Test role list
     *
     * @return void
     */
    public function testRoleList(): void
    {
        $models = Role::factory(50)->create();

        $take = mt_rand(1, 5);
        $page = mt_rand(1, 4);

        // User list response
        $response = $this//->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
            ->get(route(self::$route_prefix . 'index') . "?take=$take&page=$page&order[by]=id&order[dir]=asc")
            ->assertJsonStructure([
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
            ])
            ->assertOk();
        //Response content
        $content = json_decode($response->content());

        $fillable = ['id', ...$models[0]->getFillable()];

        $this
            // Check total items equals database records
            ->assertDatabaseCount($models[0]->getTable(), $content->meta->total)
            // Check response random item exists on the database
            ->assertDatabaseHas(
                $models[0]->getTable(),
                array_intersect_key((array)Arr::random($content->data), array_flip($fillable))
            )
            // Check per_page value equals response collection
            ->assertCount($content->meta->per_page, $content->data);

        // Check the response contains proper models
        $query = Role::limit($take)->offset(($page - 1) * $take)->get()->pluck('id')->toArray();

        // Compare db query and api request
        $this->assertEmpty(array_diff($query, collect($content->data)->pluck('id')->toArray()));
    }

    /**
     * Role store request validation test
     *
     * @return void
     */
    public function testRoleStoreValidation(): void
    {
        $cases = [
            // Send empty request body
            [
                'send' => [],
                'assert' => [
                    'name' => [lang('validation.required', 'name')],
                    'slug' => [lang('validation.required', 'slug')],
                    'level' => [lang('validation.required', 'level')]
                ]
            ],
            // Send request body with empty values
            [
                'send' => [
                    'name' => null,
                    'slug' => null,
                    'level' => null,
                ],
                'assert' => [
                    'name' => [lang('validation.required', 'name')],
                    'slug' => [lang('validation.required', 'slug')],
                    'level' => [lang('validation.required', 'level')]
                ]
            ],
            // Send fail level value
            [
                'send' => [
                    'name' => $this->faker->name,
                    'slug' => $this->faker->slug,
                    'level' => $this->faker->name,
                ],
                'assert' => [
                    'level' => [lang('validation.numeric', 'level')],
                ]
            ],
            // Send fail outbound level value
            [
                'send' => [
                    'name' => $this->faker->name,
                    'slug' => $this->faker->slug,
                    'level' => 256,
                ],
                'assert' => [
                    'level' => [lang('validation.max.numeric', 'level', 255)],
                ]
            ]
        ];
        foreach ($cases as $case) {
            $this//->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
                ->post(route(self::$route_prefix . 'store'), $case['send'])
                ->assertJson(['errors' => $case['assert']])
                ->assertUnprocessable();
        }
    }

    /**
     * Test role store
     *
     * @return void
     */
    public function testRoleStore(): void
    {
        $model = Role::factory()->make();

        $table = $model->getTable();

        $values = [
            'name' => $model->name,
            'slug' => $model->slug,
            'level' => $model->level,
        ];

        $response = $this//->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
            ->post(route(self::$route_prefix . 'store'), $values)
            ->assertJsonFragment($values)->assertCreated();

        $content = json_decode($response->content());
        $values['id'] = $content->id;
        $this->assertDatabaseHas($table, $values);
    }

    /**
     * Test Role update
     * @return void
     */
    public function testRoleUpdate(): void
    {
        $new = Role::factory()->make();

        $fields = ['name', 'level'];

        $model = static::getRole();

        $missing = array_intersect_key($model->toArray(), array_flip(['id', ...$fields]));

        $update = [];
        foreach ($fields as $key) {
            $update[$key] = $new->$key;
        }

        $updated = ['id' => $model->id, ...$update];

        $this//->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
            ->put(route(self::$route_prefix . 'update', $model->id), $update)
            ->assertJsonFragment($updated)
            ->assertOk();

        $this->assertDatabaseMissing($model->getTable(), $missing)->assertDatabaseHas($model->getTable(), $updated);
    }

    /**
     * Test Role remove
     * @return void
     */
    public function testRoleDestroy(): void
    {
        $model = $this->getRole();

        $route = self::$route_prefix . 'destroy';
        $this->assertModelExists($model)
            //->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
            ->delete(route($route, $model->id))
            ->assertNoContent();

        $this->assertModelMissing($model);
    }
}
