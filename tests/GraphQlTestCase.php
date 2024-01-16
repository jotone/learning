<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class GraphQlTestCase extends TestCase
{
    /**
     * User for authorisation in GraphQL requests
     * @var Authenticatable
     */
    protected Authenticatable $actor;

    /**
     * Total count of models
     * @var int
     */
    protected int $total = 0;

    /**
     * Array of default fields for GraphQL paginator
     * @var array|string[]
     */
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

        $this->actor = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')->where('level', 0)->first();
    }

    /**
     * Runs a test for a specific GraphQL query.
     *
     * This method sends a GraphQL query to a specified route and performs basic assertions
     * on the response. It also allows the execution of a custom callback for additional
     * assertions or logic with the response data.
     *
     * @param string $route The GraphQL endpoint or route where the query will be sent.
     * @param string $field The name of the GraphQL query field being tested.
     * @param string $response_fields The fields expected in the response of the GraphQL query.
     * @param callable|null $callback An optional callback for additional assertions or logic after receiving the response.
     */
    protected function runQueryTest(string $route, string $field, string $response_fields, ?callable $callback = null): void
    {
        // Randomly determine the number of items to take (between 4 and the total count).
        $take = mt_rand(4, $this->total);
        // Send a POST request to the specified GraphQL route.
        $response = $this
            // Act as a specific user or actor.
            ->actingAs($this->actor)
            ->post($route, [
                // Construct the GraphQL query string.
                'query' => sprintf(
                    '{%s(per_page:%s,order_by:"id",order_dir:"desc") {%s {%s}}}',
                    $field, // The field being queried.
                    $take, // Number of items to take.
                    implode(' ', $this->default_fields), // Default fields to be included in the response.
                    $response_fields // Specific fields to be returned in the response.
                )
            ])
            // Assert that the response status is 200 OK.
            ->assertOk()
            // Assert that the response JSON structure matches the expected format.
            ->assertJsonStructure([
                'data' => [
                    $field => array_merge(
                        $this->default_fields,
                        [
                            'data' => [explode(' ', $response_fields)]
                        ]
                    ),
                ]
            ])
            // Assert that certain JSON properties match the expected values.
            ->assertJson([
                'data' => [
                    $field => [
                        'current_page' => 1,
                        'per_page' => $take,
                        'total' => $this->total
                    ]
                ]
            ]);
        // Decode the JSON response and extract the specific collection field.
        $collection = json_decode($response->content())->data->{$field};
        // Assert that the per_page value is greater than or equal to the count of data items.
        $this->assertTrue($collection->per_page >= count($collection->data));
        // Assert that the total count matches the expected total.
        $this->assertTrue($collection->total === $this->total);

        // If a callback is provided and is callable, execute it, passing the collection for further processing or assertions.
        is_callable($callback) && $callback($collection);
    }

    /**
     * Runs a pagination test for a specific GraphQL query.
     *
     * This method sends a GraphQL query to a specified route with pagination parameters and performs
     * basic assertions on the response. It also allows the execution of a custom callback for
     * additional assertions or logic with the response data.
     *
     * @param string $route The GraphQL endpoint or route where the query will be sent.
     * @param string $field The name of the GraphQL query field being tested.
     * @param string $response_fields The fields expected in the response of the GraphQL query.
     * @param callable|null $callback An optional callback for additional assertions or logic after receiving the response.
     */
    protected function runPaginationTest(string $route, string $field, string $response_fields, ?callable $callback = null): void
    {
        // Send a POST request to the specified GraphQL route.
        // The query includes pagination parameters: per_page, order_by, order_dir, and page.
        $response = $this
            ->actingAs($this->actor)
            ->post($route, [
                // Construct the GraphQL query string with pagination.
                'query' => sprintf(
                    '{%s(per_page:1,order_by:"id",order_dir:"asc",page:%s) {%s {%s}}}',
                    $field, // The field being queried.
                    $this->total, // Total number of items for pagination.
                    implode(' ', $this->default_fields), // Default fields to be included in the response.
                    $response_fields // Specific fields to be returned in the response.
                )
            ])
            // Assert that the response status is 200 OK.
            ->assertOk();

        $collection = json_decode($response->content())->data->{$field};

        is_callable($callback) && $callback($collection);
    }

    /**
     * Runs a test for a specific GraphQL mutation.
     *
     * This method sends a GraphQL mutation request to a specified route and performs basic assertions
     * on the response. It can also execute a custom callback for additional assertions or logic.
     *
     * @param string $type The name of the GraphQL mutation being tested. (create, update, delete, etc.)
     * @param string $route The endpoint or route where the GraphQL server is accessible.
     * @param string $query The GraphQL mutation query, with placeholders for dynamic parameters.
     * @param array $params Parameters to replace placeholders in the query.
     * @param string $response_fields The fields expected in the response of the GraphQL mutation.
     * @param callable|null $callback An optional callback for additional assertions or logic after the request.
     */
    protected function runMutationTest(string $type, string $route, string $query, array $params, string $response_fields, ?callable $callback = null): void
    {
        // Send a POST request to the specified GraphQL route.
        $response = $this
            ->actingAs($this->actor)
            ->post($route, [
                'query' => preg_replace_array('/%s/', $params, "mutation {{$type} ($query) {{$response_fields}}}")
            ])
            // Assert that the response status is 200 OK.
            ->assertOk()
            // Assert that the response JSON structure matches the expected format.
            ->assertJsonStructure([
                'data' => [
                    // The expected fields in the mutation response.
                    $type => explode(' ', $response_fields)
                ]
            ]);

        is_callable($callback) && $callback($response);
    }

    /**
     * Runs a series of test cases against a specified GraphQL route.
     *
     * This method is designed to test different scenarios (test cases) for a given GraphQL route.
     * Each test case is expected to include an 'actor' (user performing the request), a 'query'
     * (GraphQL query to be tested), and a 'callback' (function to run assertions on the response).
     *
     * @param string $route
     * @param array $test_cases
     */
    protected function runTestCases(string $route, array $test_cases): void
    {
        // Check if the test_cases array is associative (non-numeric indexes). If so, wrap it in another array.
        if (!$this->hasNumericIndexes($test_cases)) {
            $test_cases = [$test_cases];
        }
        foreach ($test_cases as $case) {
            // Send a POST request as the specified 'actor' to the given route with the 'query'.
            $response = $this->actingAs($case['actor'])
                ->post($route, ['query' => $case['query']])
                // Assert that the response status is 200 OK.
                ->assertOk()
                // Assert that the response has an 'errors' JSON structure.
                ->assertJsonStructure(['errors']);
            // Decode the JSON response content and pass it to the callback function provided in the test case.
            if (isset($case['callback']) && is_callable($case['callback'])) {
                $case['callback'](json_decode($response->content(), 1));
            }
        }
    }

    /**
     * Checks if an array has numeric indexes.
     *
     * This function iterates through the array keys and checks if all keys are integers.
     * In PHP, array keys can be either integers or strings. This function specifically
     * checks for integer keys, which is a common representation of numeric indexes in arrays.
     *
     * @param array $array
     * @return bool
     */
    protected function hasNumericIndexes(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            // Check if the key is not an integer
            if (!is_int($key)) {
                return false;
            }
        }
        // If all keys are integers, return true
        return true;
    }
}