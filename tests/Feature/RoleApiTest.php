<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Arr;
use Tests\ApiTestCase;

class RoleApiTest extends ApiTestCase
{
    const CONTROLLER = 'App\Http\Controllers\Dashboard\DashboardController';

    protected function setUp(): void
    {
        parent::setUp();

        self::$class = Role::class;
        self::$route_prefix = 'api.roles.';
    }

    /**
     * Test the role list functionality.
     * This method is used to test the role list functionality by checking various conditions and assertions.
     *
     * @return void
     */
    public function testList(): void
    {
        $this->runListTest(function ($content, $models) {
            $fillable = ['id', ...$models[0]->getFillable()];

            $db_table = $models[0]->getTable();
            $this
                // Check total items equals database records
                ->assertDatabaseCount($db_table, $content->meta->total)
                // Check response random item exists on the database
                ->assertDatabaseHas($db_table, array_intersect_key((array)Arr::random($content->data), array_flip($fillable)))
                // Check per_page value equals response collection
                ->assertCount($content->meta->per_page, $content->data);
        });
    }

    /**
     * Test the role store validation.
     * This method is used to test the validation of the role store functionality
     * by sending different cases and checking the expected validation errors.
     *
     * @return void
     */
    public function testStoreValidation(): void
    {
        $cases = [
            // Send empty request body
            [
                'send' => [],
                'assert' => [
                    'name' => [$this->lang('validation.required', 'name')],
                    'slug' => [$this->lang('validation.required', 'slug')],
                    'level' => [$this->lang('validation.required', 'level')]
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
                    'name' => [$this->lang('validation.required', 'name')],
                    'slug' => [$this->lang('validation.required', 'slug')],
                    'level' => [$this->lang('validation.required', 'level')]
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
                    'level' => [$this->lang('validation.numeric', 'level')],
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
                    'level' => [$this->lang('validation.max.numeric', 'level', 255)],
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
     * Test the role store method.
     *
     * @return void
     */
    public function testStore(): void
    {
        $model = self::$class::factory()->make();

        $response = $this->actingAs(self::$actor)
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

    public function testUpdate(): void
    {
        $model = $this->getModel();

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

    public function testDestroy(): void
    {
        $this->runBulkDeleteTest('roles', Role::where('level', '>', 63)
            ->where('level', '<', 255)
            ->inRandomOrder()
            ->take(5)
            ->pluck('id')
            ->toArray()
        );
    }

    protected function getModel(): Role
    {
        return Role::where('level', '>', 127)->where('level', '<', 255)->count()
            ? Role::where('level', '>', 127)->where('level', '<', 255)->first()
            : Role::factory()->create();
    }
}
