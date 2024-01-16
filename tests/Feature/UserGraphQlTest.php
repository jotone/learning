<?php

namespace Feature;

use App\Http\Controllers\GraphQL\User\UserMutation;
use App\Models\{Role, Settings, User};
use Tests\GraphQlTestCase;

class UserGraphQlTest extends GraphQlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->total = User::count();
        if (User::count() < 5) {
            User::factory(5 + $this->total)->create([
                'role_id' => Role::where('slug', 'student')->value('id')
            ]);
        }
    }

    /**
     * Test the 'users' GraphQL query.
     *
     * This method uses the runQueryTest function to send a GraphQL query for users and performs
     * an assertion on the response. The assertion is specified in the callback function.
     */
    public function testQuery(): void
    {
        $this->runQueryTest(
            route: route('graphql.user'),
            field: 'users',
            response_fields: 'id first_name last_name email',
            callback: fn($collection) => $this->assertTrue(
                $collection->data[0]->email === User::orderBy('id', 'desc')->value('email')
            )
        );
    }

    /**
     * Test the pagination feature of the 'users' GraphQL query.
     *
     * This method verifies that the GraphQL endpoint correctly handles pagination for user queries.
     * It checks the response's pagination properties and data consistency using a custom assertion in the callback function.
     */
    public function testPagination(): void
    {
        $this->runPaginationTest(
            route: route('graphql.user'),
            field: 'users',
            response_fields: 'id first_name last_name email',
            callback: fn($collection) => $this->assertTrue(
                $collection->data[0]->email === User::orderBy('id', 'desc')->value('email')
            )
        );
    }

    /**
     * Test the 'create' mutation for users in the GraphQL API.
     *
     * This method tests the GraphQL mutation for creating a new user It validates that the mutation
     * correctly stores the user in the database and that the response is as expected. A custom callback
     * is used to perform additional database assertions.
     */
    public function testStore(): void
    {
        $user = User::factory()->make();

        $this->runMutationTest(
            type: 'create',
            route: route('graphql.user'),
            query: 'first_name: "%s", last_name: "%s", email: "%s", timezone: "%s", country: "%s", region: "%s", city: "%s", address: "%s", zip: "%s", phone: "%s"',
            params: [
                $user->first_name,
                $user->last_name,
                $user->email,
                $user->timezone,
                $user->country,
                $user->region,
                $user->city,
                $user->address,
                $user->zip,
                $user->phone
            ],
            response_fields: 'id first_name last_name email timezone country region city address zip phone',
            callback: fn() => $this->assertDatabaseHas('users', [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'timezone' => $user->timezone,
                'country' => $user->country,
                'region' => $user->region,
                'city' => $user->city,
                'address' => $user->address,
                'zip' => $user->zip,
                'phone' => $user->phone
            ])
        );
    }

    /**
     * Test the security constraints of the 'create' mutation for users in the GraphQL API.
     *
     * This method performs two security-related tests:
     * 1. It checks whether the API correctly enforces a limit on the number of students
     *    that can be created, by trying to create a user when the student limit is reached.
     * 2. It verifies that a user with a lower privilege level (e.g., student) cannot create
     *    a user with higher privileges (e.g., superuser).
     *
     * Each test case is designed to trigger specific security rules and checks if the API
     * responds with the appropriate error messages when these rules are violated.
     */
    public function testStoreSecurity(): void
    {
        $user = User::factory()->make();
        $roles = Role::whereIn('slug', ['student', 'superuser'])->pluck('id', 'slug');
        $actor = User::factory()->create(['role_id' => $roles['student']]);
        Settings::where('key', 'students_max_count')->update(['value' => 1]);

        $this->runTestCases(route('graphql.user'), [
            // Attempt to create a user when the student limit is achieved
            [
                'actor' => $this->actor,
                'query' => sprintf(
                    'mutation {create (first_name: "%s", last_name: "%s", email: "%s") {id first_name last_name email}}',
                    $user->first_name,
                    $user->last_name,
                    $user->email
                ),
                'callback' => function ($content) {
                    $this->assertTrue($content['errors'][0]['message'] === sprintf(UserMutation::STUDENT_LIMIT_MESSAGE, 1));
                    Settings::where('key', 'students_max_count')->update(['value' => -1]);
                }
            ],
            // Attempt to create a superuser by the student role
            [
                'actor' => $actor,
                'query' => sprintf(
                    'mutation {create (first_name: "%s", last_name: "%s", email: "%s", role_id: %s) {id first_name last_name email role_id}}',
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $roles['superuser']
                ),
                'callback' => fn($content) => $this->assertTrue($content['errors'][0]['message'] === UserMutation::ACCESS_FORBIDDEN_MESSAGE)
            ]
        ]);
    }

    /**
     * Test the 'update' mutation for users in the GraphQL API.
     *
     * This method verifies the functionality of the GraphQL API for updating an existing user.
     * It ensures that the mutation updates the user with new data correctly and checks both the
     * response and the database to confirm the update.
     */
    public function testUpdate(): void
    {
        $user = User::factory()->create();

        $new_data = User::factory()->make();

        $data = [
            'id' => $user->id,
            'first_name' => $new_data->first_name,
            'last_name' => $new_data->last_name,
            'timezone' => $new_data->timezone,
            'country' => $new_data->country,
            'region' => $new_data->region,
            'city' => $new_data->city,
            'zip' => $new_data->zip,
            'phone' => $new_data->phone
        ];

        $this->runMutationTest(
            type: 'update',
            route: route('graphql.user'),
            query: 'id: %s, first_name: "%s", last_name: "%s", timezone: "%s", country: "%s", region: "%s", city: "%s", zip: "%s", phone: "%s"',
            params: array_values($data),
            response_fields: 'id first_name last_name timezone country region city zip phone',
            callback: fn() => $this
                ->assertDatabaseMissing('users', [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'timezone' => $user->timezone,
                    'country' => $user->country,
                    'region' => $user->region,
                    'city' => $user->city,
                    'zip' => $user->zip,
                    'phone' => $user->phone,
                ])
                ->assertDatabaseHas('users', $data)
        );
    }

    /**
     * Test the security constraints of the 'update' mutation for users in the GraphQL API.
     *
     * This method performs multiple security-related tests to ensure proper access control in user updates:
     * 1. Verifies that a user can update their own information.
     * 2. Checks that a user cannot update the information of another user.
     * 3. Tests whether a user is restricted from assigning a new role to themselves.
     *
     * Each test case is designed to assess specific security rules and validate if the API
     * responds with appropriate error messages or actions when these rules are either followed or violated.
     */
    public function testUpdateSecurity(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::where('slug', 'student')->value('id')
        ]);

        $new_data = User::factory()->create();

        // Test user can update himself
        $this->actingAs($user)
            ->post(route('graphql.user'), [
                'query' => sprintf(
                    'mutation {update (id: %s, first_name: "%s", last_name: "%s", city: "%s") {id first_name last_name email city}}',
                    $user->id,
                    $new_data->first_name,
                    $new_data->last_name,
                    $new_data->city
                )
            ])->assertOk()
            ->assertJsonMissing(['errors'])
            ->assertJsonFragment([
                'data' => [
                    'update' => [
                        'id' => $user->id,
                        'first_name' => $new_data->first_name,
                        'last_name' => $new_data->last_name,
                        'email' => $user->email,
                        'city' => $new_data->city
                    ]
                ]
            ]);

        $admin = User::select('users.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.level', '<', 128)
            ->inRandomOrder()
            ->first();

        $this->runTestCases(route('graphql.user'), [
            // Test user cannot update another user
            [
                'actor' => $user,
                'query' => sprintf(
                    'mutation {update (id: %s, first_name: "%s") {id first_name}}',
                    $admin->id,
                    $new_data->first_name
                ),
                'callback' => fn($content) => $this->assertTrue($content['errors'][0]['message'] === UserMutation::ACCESS_FORBIDDEN_MESSAGE)
            ],
            // Test user cannot set another role by himself
            [
                'actor' => $user,
                'query' => sprintf(
                    'mutation {update (id: %s, role_id: %s) {id role_id}}',
                    $user->id,
                    Role::where('level', '<', 255)->inRandomOrder()->value('id')
                ),
                'callback' => fn($content) => $this->assertTrue($content['errors'][0]['message'] === UserMutation::ACCESS_FORBIDDEN_MESSAGE)
            ]
        ]);
    }
}