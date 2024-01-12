<?php

namespace Feature;

use App\Http\Controllers\GraphQL\User\MutationStore;
use App\Models\Role;
use App\Models\Settings;
use App\Models\User;
use Tests\GraphQlTestCase;

class UserGraphQlTest extends GraphQlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->total = User::count();
        if ($this->total < 5) {
            User::factory(5 - $this->total + 1)->create();
        }
    }

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

    public function testStore(): void
    {
        $user = User::factory()->make();

        $this->runStoreTest(
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

    public function testStoreSecurity(): void
    {
        $user = User::factory()->make();
        $roles = Role::whereIn('slug', ['student', 'superuser'])->pluck('id', 'slug');
        $actor = User::factory()->create(['role_id' => $roles['student']]);
        Settings::where('key', 'students_max_count')->update(['value' => 1]);

        $test_cases = [
            // Attempt to create a user when the student limit is achieved
            [
                'actor' => $this->actor,
                'query' => sprintf(
                    'mutation {create (first_name: "%s", last_name: "%s", email: "%s") {id first_name last_name email}}',
                    $user->first_name,
                    $user->last_name,
                    $user->email
                ),
                'callback' => function($content) {
                    $this->assertTrue($content['errors'][0]['message'] === sprintf(MutationStore::STUDENT_LIMIT_MESSAGE, 1));
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
                'callback' => fn($content) => $this->assertTrue($content['errors'][0]['message'] === MutationStore::ACCESS_FORBIDDEN_MESSAGE)
            ]
        ];

        foreach ($test_cases as $case) {
            $response = $this->actingAs($case['actor'])
                ->post(route('graphql.user'), ['query' => $case['query']])
                ->assertOk()
                ->assertJsonStructure(['errors']);

            $case['callback'](json_decode($response->content(), 1));
        }
    }
}