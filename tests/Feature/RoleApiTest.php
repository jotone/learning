<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Support\Arr;
use Tests\ApiTestCase;
use Tests\Traits\ModelGeneratorsTrait;

class RoleApiTest extends ApiTestCase
{
    use ModelGeneratorsTrait;

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
                'send'   => [],
                'assert' => [
                    'name'  => [lang('validation.required', 'name')],
                    'slug'  => [lang('validation.required', 'slug')],
                    'level' => [lang('validation.required', 'level')]
                ]
            ],
            // Send request body with empty values
            [
                'send'   => [
                    'name'  => null,
                    'slug'  => null,
                    'level' => null,
                ],
                'assert' => [
                    'name'  => [lang('validation.required', 'name')],
                    'slug'  => [lang('validation.required', 'slug')],
                    'level' => [lang('validation.required', 'level')]
                ]
            ],
            // Send fail level value
            [
                'send'   => [
                    'name'  => $this->faker->name,
                    'slug'  => $this->faker->slug,
                    'level' => $this->faker->name,
                ],
                'assert' => [
                    'level' => [lang('validation.numeric', 'level')],
                ]
            ],
            // Send fail outbound level value
            [
                'send'   => [
                    'name'  => $this->faker->name,
                    'slug'  => $this->faker->slug,
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
        $this->runStoreTest(['name', 'slug', 'level']);
    }

    /**
     * Test Role update
     * @return void
     */
    public function testRoleUpdate(): void
    {
        $this->runUpdateTest($this->getRole(), ['name', 'level']);
    }

    /**
     * Test Role remove
     * @return void
     */
    public function testRoleDestroy(): void
    {
        $this->runDeleteTest($this->getRole());
    }
}
