<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class GraphQlTestCase extends TestCase
{
    protected Authenticatable $actor;

    protected int $total = 0;

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

    protected function runQueryTest(string $route, string $field, string $response_fields, ?callable $callback = null): void
    {
        $take = mt_rand(4, $this->total);

        $response = $this->actingAs($this->actor)
            ->post($route, [
                'query' => sprintf(
                    '{%s(per_page:%s,order_by:"id",order_dir:"desc") {%s {%s}}}',
                    $field,
                    $take,
                    implode(' ', $this->default_fields),
                    $response_fields
                )
            ])
            ->assertOk()
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
            ->assertJson([
                'data' => [
                    $field => [
                        'current_page' => 1,
                        'per_page' => $take,
                        'total' => $this->total
                    ]
                ]
            ]);

        $collection = json_decode($response->content())->data->{$field};
        $this->assertTrue($collection->per_page >= count($collection->data));
        $this->assertTrue($collection->total === $this->total);

        is_callable($callback) && $callback($collection);
    }

    protected function runPaginationTest(string $route, string $field, string $response_fields, ?callable $callback = null): void
    {
        $response = $this
            ->actingAs($this->actor)
            ->post($route, [
                'query' => sprintf(
                    '{%s(per_page:1,order_by:"id",order_dir:"asc",page:%s) {%s {%s}}}',
                    $field,
                    $this->total,
                    implode(' ', $this->default_fields),
                    $response_fields
                )
            ])
            ->assertOk();

        $collection = json_decode($response->content())->data->{$field};

        is_callable($callback) && $callback($collection);
    }

    protected function runMutationTest(string $type, string $route, string $query, array $params, $response_fields, ?callable $callback = null): void
    {
        $this
            ->actingAs($this->actor)
            ->post($route, [
                'query' => preg_replace_array('/%s/', $params, "mutation {{$type} ($query) {{$response_fields}}}")
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    $type => explode(' ', $response_fields)
                ]
            ]);

        is_callable($callback) && $callback();
    }
}