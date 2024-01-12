<?php

namespace Feature;

use App\Http\Controllers\GraphQL\Role\RoleMutation;
use App\Models\{Role, User};
use Tests\GraphQlTestCase;

class RoleGraphQlTest extends GraphQlTestCase
{
    const CONTROLLER = 'App\Http\Controllers\Dashboard\DashboardController';

    protected array $default_fields = [
        'total',
        'per_page',
        'last_page',
        'has_more_pages',
        'current_page',
        'data'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->total = Role::count();
        if ($this->total < 5) {
            Role::factory(5 - $this->total + 1)->create();
        }
    }

    public function testQuery(): void
    {
        $this->runQueryTest(
            route: route('graphql.role'),
            field: 'roles',
            response_fields: 'id name slug level',
            callback: fn($collection) => $this->assertTrue(
                $collection->data[0]->slug === Role::orderBy('id', 'desc')->value('slug')
            )
        );
    }

    public function testPagination(): void
    {
        $this->runPaginationTest(
            route: route('graphql.role'),
            field: 'roles',
            response_fields: 'id name slug level',
            callback: fn($collection) => $this->assertTrue(
                $collection->data[0]->slug === Role::orderBy('id', 'desc')->value('slug')
            )
        );
    }

    public function testStore(): void
    {
        $role = Role::factory()->make();
        $this->runStoreTest(
            route: route('graphql.role'),
            query: 'name: "%s", level: %s, permissions: "%s"',
            params: [
                $role->name,
                $role->level,
                base64_encode(json_encode([self::CONTROLLER => ['index' => 1]]))
            ],
            response_fields: 'id name slug level',
            callback: fn() => $this->assertDatabaseHas('roles', [
                'name' => $role->name,
                'slug' => generateUrl($role->name),
                'level' => $role->level,
            ])
        );
    }

    public function testStoreSecurity(): void
    {
        $role = Role::factory()->make();

        $low_level_actor = User::factory()->create([
            'role_id' => Role::orderBy('level', 'desc')->value('id')
        ]);

        $response = $this
            ->actingAs($low_level_actor)
            ->post(route('graphql.role'), [
                'query' => sprintf(
                    'mutation {create (name: "%s", level: 0, permissions: "%s") {id, name, slug, level}}',
                    $role->name,
                    base64_encode(json_encode([
                        self::CONTROLLER => ['index' => 1]
                    ]))
                )
            ])
            ->assertOk()
            ->assertJsonStructure(['errors']);

        $content = json_decode($response->content(), 1);

        $this->assertTrue($content['errors'][0]['message'] === RoleMutation::ACCESS_FORBIDDEN_MESSAGE);
    }

    public function testUpdate(): void
    {
        $role = Role::factory()->create();

        $new_data = Role::factory()->make();

        $this
            ->actingAs($this->actor)
            ->post(route('graphql.role'), [
                'query' => sprintf(
                    'mutation {update (id: %s, name: "%s", slug: "%s", level: %s) {id, name, slug, level}}',
                    $role->id,
                    $new_data->name,
                    $new_data->slug,
                    $new_data->level
                )
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'update' => [
                        'id',
                        'name',
                        'slug',
                        'level',
                    ]
                ]
            ]);

        $this
            ->assertDatabaseHas('roles', [
                'id' => $role->id,
                'name' => $new_data->name,
                'slug' => $new_data->slug,
                'level' => $new_data->level,
            ])
            ->assertDatabaseMissing('roles', [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'level' => $role->level,
            ]);
    }

    public function testUpdateSecurity(): void
    {
        $role = Role::factory()->create();

        $low_level_actor = User::factory()->create([
            'role_id' => Role::orderBy('level', 'desc')->value('id')
        ]);

        $response = $this
            ->actingAs($low_level_actor)
            ->post(route('graphql.role'), [
                'query' => 'mutation {update (id: ' . $role->id . ', level: 0) {id, level}}'
            ])
            ->assertOk()
            ->assertJsonStructure(['errors']);

        $content = json_decode($response->content(), 1);

        $this->assertTrue($content['errors'][0]['message'] === RoleMutation::ACCESS_FORBIDDEN_MESSAGE);
    }

    public function testDestroy(): void
    {
        $role = Role::factory()->create();

        $this
            ->actingAs($this->actor)
            ->post(route('graphql.role'), [
                'query' => 'mutation {destroy (id: ' . $role->id . ') {id}}'
            ])
            ->assertOk()
            ->assertExactJson(['data' => ['destroy' => null]]);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function testDestroySecurity(): void
    {
        $role = Role::factory()->create();

        $low_level_actor = User::factory()->create([
            'role_id' => Role::orderBy('level', 'desc')->value('id')
        ]);

        $response = $this
            ->actingAs($low_level_actor)
            ->post(route('graphql.role'), [
                'query' => 'mutation {destroy (id: ' . $role->id . ') {id}}'
            ])
            ->assertOk()
            ->assertJsonStructure(['errors']);

        $content = json_decode($response->content(), 1);

        $this->assertTrue($content['errors'][0]['message'] === RoleMutation::ACCESS_FORBIDDEN_MESSAGE);
    }
}