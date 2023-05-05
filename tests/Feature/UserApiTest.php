<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use App\Models\{Role, User};
use Illuminate\Support\Arr;
use Tests\ApiTestCase;
use Tests\Traits\ModelGeneratorsTrait;

class UserApiTest extends ApiTestCase
{
    use ModelGeneratorsTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $superuser = Role::firstWhere(['level' => 0]);

        self::$class = User::class;
        self::$route_prefix = 'api.users.';
        self::$uri_request = '&where_not[role_id]=' . $superuser->id;
    }

    /**
     * Test user list
     *
     * @return void
     */
    public function testUserList(): void
    {
        $this->runListTest(function ($content, $models) {
            $item = Arr::random($content->data);
            $this
                // Check total items equals database records
                ->assertDatabaseCount($models[0]->getTable(), $content->meta->total)
                // Check response random item exists on the database
                ->assertDatabaseHas($models[0]->getTable(), [
                    'id'         => $item->id,
                    'first_name' => $item->first_name ?? '',
                    'last_name'  => empty($item->last_name) ? null : $item->last_name,
                    'email'      => $item->email
                ])
                // Check per_page value equals response collection
                ->assertCount($content->meta->per_page, $content->data);
        });
    }

    /**
     * Test user show
     *
     * @return void
     */
    public function testUserShow(): void
    {
        $this->runShowTest($this->getUser(), [
            'id',
            'first_name',
            'last_name',
            'email'
        ]);
    }

    /**
     * User store request validation test
     *
     * @return void
     */
    public function testUserStoreValidation(): void
    {
        $cases = [
            // Send empty request body
            [
                'send'   => [],
                'assert' => [
                    'first_name'   => [lang('validation.required', 'first name')],
                    'email'        => [lang('validation.required', 'email')],
                    'password'     => [lang('validation.required', 'password')],
                    'confirmation' => [lang('validation.required', 'confirmation')],
                ],
            ],
            // Send already taken email
            [
                'send'   => [
                    'first_name'   => $this->faker->firstName,
                    'last_name'    => $this->faker->lastName,
                    'email'        => User::orderBy('id', 'desc')->value('email'),
                    'password'     => 'password',
                    'confirmation' => 'password'
                ],
                'assert' => [
                    'email' => [lang('validation.unique', 'email')]
                ]
            ],
            // Send unconfirmed password
            [
                'send'   => [
                    'first_name'   => $this->faker->firstName,
                    'last_name'    => $this->faker->lastName,
                    'email'        => $this->faker->email,
                    'password'     => 'password',
                    'confirmation' => Str::random()
                ],
                'assert' => [
                    'confirmation' => [lang('validation.same', 'confirmation', 'password')]
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
     * Test User store
     *
     * @return void
     */
    public function testUserStore(): void
    {
        $model = self::$class::factory()->make();
        $table = $model->getTable();
        $values = [
            'first_name'   => $model->first_name,
            'last_name'    => $model->last_name,
            'email'        => $model->email,
            'password'     => 'password',
            'confirmation' => 'password'
        ];
        $response = $this
            ->actingAs(self::$actor)
            ->postJson(route(self::$route_prefix . 'store'), $values)
            ->assertJsonFragment([
                'first_name' => $model->first_name,
                'last_name'  => $model->last_name,
                'email'      => $model->email,
            ])->assertCreated();

        $content = json_decode($response->content());
        $this->assertDatabaseHas($table, [
            'id'         => $content->id,
            'first_name' => $model->first_name,
            'last_name'  => $model->last_name,
            'email'      => $model->email,
        ]);
    }

    /**
     * Test user with the lower role level cannot update user with the greater role level
     *
     * @return void
     */
    public function testLowerLevelRoleUserUpdate()
    {
        $this->actingAs($this->getUser())
            ->putJson(route(self::$route_prefix . 'update', self::$actor->id), [
                'first_name' => Str::random()
            ])
            ->assertForbidden();
    }

    /**
     * Test User update
     *
     * @return void
     */
    public function testUserUpdate(): void
    {
        $this->runUpdateTest($this->getUser(), ['first_name', 'last_name', 'email']);
    }

    /**
     * Test user with the lower role level cannot remove user with the greater role level
     *
     * @return void
     */
    public function testLowerLevelRoleUserRemove()
    {
        $this->actingAs($this->getUser())
            ->deleteJson(route(self::$route_prefix . 'destroy', self::$actor->id))
            ->assertForbidden();
    }

    /**
     * Test user cannot remove himself
     *
     * @return void
     */
    public function testUserSelfRemove()
    {
        $this->actingAs(self::$actor)
            ->deleteJson(route(self::$route_prefix . 'destroy', self::$actor->id))
            ->assertForbidden();
    }

    /**
     * Test User remove
     * @return void
     */
    public function testUserDestroy(): void
    {
        $this->runDeleteTest($this->getUser());
    }
}