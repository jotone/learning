<?php

namespace Tests\Feature;

use App\Models\{Permission, Role};
use Illuminate\Support\Arr;
use Tests\ApiTestCase;
use Tests\Traits\ModelGeneratorsTrait;

class RoleApiTest extends ApiTestCase
{
    use ModelGeneratorsTrait;

    const CONTROLLER = 'App\Http\Controllers\Dashboard\DashboardController';

    protected function setUp(): void
    {
        parent::setUp();

        self::$class = Role::class;
        self::$route_prefix = 'api.roles.';
    }

    /**
     * Test role list
     *
     * @return void
     */
    public function testRoleList(): void
    {
        $this->runListTest(function ($content, $models) {
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
        });
    }

    /**
     * Test role show
     *
     * @return void
     */
    public function testRoleShow(): void
    {
        $this->runShowTest($this->getRole(), [
            'id',
            'name',
            'slug',
            'level'
        ]);
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
            $this
                ->actingAs(self::$actor)
                ->postJson(route(self::$route_prefix . 'store'), $case['send'])
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
        $model = self::$class::factory()->make();

        $response = $this
            ->actingAs(self::$actor)
            ->postJson(route(self::$route_prefix . 'store'), [
                'name' => $model->name,
                'slug' => $model->slug,
                'level' => $model->level,
                'permissions' => [
                    self::CONTROLLER => ['index' => 1]
                ]
            ])
            ->assertJsonFragment([
                'name' => $model->name,
                'slug' => $model->slug,
                'level' => $model->level
            ])
            ->assertCreated();

        $content = json_decode($response->content());

        $this->assertDatabaseHas('roles', [
            'id' => $content->id,
            'name' => $model->name,
            'slug' => $model->slug,
            'level' => $model->level
        ])->assertDatabaseHas('permissions', [
            'role_id' => $content->id,
            'controller' => self::CONTROLLER
        ]);
    }

    /**
     * Test Role update
     *
     * @return void
     */
    public function testRoleUpdate(): void
    {
        $model = $this->getRole();

        $new = self::$class::factory()->make();

        $this->actingAs(self::$actor)
            ->putJson(route(self::$route_prefix . 'update', $model->id), [
                'name' => $new->name,
                'level' => $new->level,
                'permissions' => [
                    self::CONTROLLER => ['show' => 1]
                ]
            ])
            ->assertJsonFragment([
                'id' => $model->id,
                'name' => $new->name,
                'slug' => $model->slug,
                'level' => $new->level
            ])
            ->assertOk();

        $this->assertDatabaseMissing('roles', [
            'id' => $model->id,
            'name' => $model->name,
            'level' => $model->level
        ])->assertDatabaseHas('roles', [
            'id' => $model->id,
            'name' => $new->name,
            'level' => $new->level
        ]);

        $this->assertEquals(['show'], Permission::firstWhere([
            'role_id' => $model->id,
            'controller' => self::CONTROLLER
        ])->allowed_methods);
    }

    /**
     * Test Role remove
     *
     * @return void
     */
    public function testRoleDestroy(): void
    {
        $this->runDeleteTest(
            $this->getRole(),
            fn($model) => $this->assertDatabaseMissing('permissions', ['role_id' => $model->id])
        );
    }
}
