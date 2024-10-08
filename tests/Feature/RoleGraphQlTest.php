<?php

namespace Tests\Feature;

use App\Services\Str;
use App\Models\{Role, User};
use Tests\GraphQlTestCase;

class RoleGraphQlTest extends GraphQlTestCase
{
    const CONTROLLER = 'App\Http\Controllers\Dashboard\DashboardController';

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() < 5) {
            Role::factory(6)->create();
        }
        $this->total = Role::count();
    }

    /**
     * Test the 'roles' GraphQL query.
     *
     * This method uses the runQueryTest function to send a GraphQL query for roles and performs
     * an assertion on the response. The assertion is specified in the callback function.
     *
     * @return void
     */
    public function testQuery(): void
    {
        $this->runQueryTest(
            route: route('graphql.role'),
            field: 'roles',
            response_fields: 'id name slug level',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->slug === Role::orderBy('id', 'desc')->value('slug'))
        );
    }

    /**
     * Test the pagination feature of the 'roles' GraphQL query.
     *
     * This method verifies that the GraphQL endpoint correctly handles pagination for role queries.
     * It checks the response's pagination properties and data consistency using a custom assertion in the callback function.
     *
     * @return void
     */
    public function testPagination(): void
    {
        $this->runPaginationTest(
            route: route('graphql.role'),
            field: 'roles',
            response_fields: 'id name slug level',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->slug === Role::orderBy('id', 'desc')->value('slug'))
        );
    }

    /**
     * Test the 'create' mutation for roles in the GraphQL API.
     *
     * This method tests the GraphQL mutation for creating a new role. It validates that the mutation
     * correctly stores the role in the database and that the response is as expected. A custom callback
     * is used to perform additional database assertions.
     *
     * @return void
     */
    public function testStore(): void
    {
        $role = Role::factory()->make();
        $this->runMutationTest(
            type: 'create',
            route: route('graphql.role'),
            query: 'name: "%s", level: %s, permissions: "%s"',
            params: [
                $role->name,
                $role->level,
                base64_encode(json_encode([self::CONTROLLER => ['index']]))
            ],
            response_fields: 'id name slug level',
            callback: fn() => $this->assertDatabaseHas('roles', [
                'name' => $role->name,
                'slug' => Str::generateUrl($role->name),
                'level' => $role->level,
            ])
        );
    }

    /**
     * Test the security of the 'create' mutation for roles in the GraphQL API.
     *
     * This method checks whether the GraphQL API correctly restricts access to the role creation
     * mutation based on the user's role. It ensures that a user with insufficient permissions
     * cannot create a new role.
     *
     * @return void
     */
    public function testStoreSecurity(): void
    {
        $role = Role::factory()->make();

        $this->testMutationSecurity(sprintf(
            'mutation {create (name: "%s", level: 0, permissions: "%s") {id, name, slug, level}}',
            $role->name,
            base64_encode(json_encode([self::CONTROLLER => ['index']]))
        ));
    }

    /**
     * Test the 'update' mutation for roles in the GraphQL API.
     *
     * This method verifies the functionality of the GraphQL API for updating an existing role.
     * It ensures that the mutation updates the role with new data correctly and checks both the
     * response and the database to confirm the update.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $role = Role::factory()->create();

        $new_data = Role::factory()->make();

        $data = [
            'id' => $role->id,
            'name' => $new_data->name,
            'slug' => $new_data->slug,
            'level' => $new_data->level
        ];

        $this->runMutationTest(
            type: 'update',
            route: route('graphql.role'),
            query: 'id: %s, name: "%s", slug: "%s", level: %s',
            params: array_values($data),
            response_fields: 'id name slug level',
            callback: fn() => $this
                ->assertDatabaseMissing('roles', [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'level' => $role->level,
                ])
                ->assertDatabaseHas('roles', $data)
        );
    }

    /**
     * Test the security of the 'update' mutation for roles in the GraphQL API.
     *
     * This method checks whether the GraphQL API correctly restricts access to the role update
     * mutation based on the user's role. It ensures that a user with insufficient permissions
     * cannot update an existing role, and verifies that the appropriate access control measures
     * are in place by expecting a specific 'access forbidden' error message.
     *
     * @return void
     */
    public function testUpdateSecurity(): void
    {
        $role = Role::factory()->create();
        $this->testMutationSecurity('mutation {update (id: ' . $role->id . ', level: 0) {id, level}}');
    }

    /**
     * Test the 'destroy' mutation for roles in the GraphQL API.
     *
     * This method verifies the functionality of the GraphQL API for deleting an existing role.
     * It checks that the role is properly removed from the database and that the API response
     * confirms the successful deletion. The method sends a delete request for a specific role and
     * then uses database assertions to ensure that the role no longer exists in the database.
     *
     * @return void
     */
    public function testDestroy(): void
    {
        $this->runDeleteTest(route('graphql.role'), Role::factory()->create());
    }

    /**
     * Test the security of the 'destroy' mutation for roles in the GraphQL API.
     *
     * This method checks whether the GraphQL API correctly restricts access to the role deletion
     * mutation based on the user's role. It ensures that a user with insufficient permissions
     * cannot delete an existing role. The test verifies this by expecting an 'access forbidden'
     * error message in the response when a low-level user attempts to perform the deletion.
     *
     * @return void
     */
    public function testDestroySecurity(): void
    {
        $role = Role::factory()->create();
        $this->testMutationSecurity('mutation {destroy (id: ' . $role->id . ') {id}}');
    }

    /**
     * Run a default mutation query by the user with a low level role
     *
     * @param string $query
     * @return void
     */
    protected function testMutationSecurity(string $query): void
    {
        $low_level_actor = User::factory()->create([
            'role_id' => Role::orderBy('level', 'desc')->value('id')
        ]);

        $this->runTestCases(route('graphql.role'), [
            'actor' => $low_level_actor,
            'query' => $query,
            'status' => 403,
            'callback' => fn($response) => $this->assertTrue($response->content() === '"Forbidden operation"')
        ]);
    }
}