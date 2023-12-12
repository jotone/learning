<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

class RoleTestGraphQL extends TestCase
{
    protected array $default_fields = [
        'total',
        'per_page',
        'last_page',
        'has_more_pages',
        'current_page',
        'data'
    ];

    protected int $total = 0;

    protected Authenticatable $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->total = Role::count();
        if ($this->total < 5) {
            Role::factory(5 - $this->total + 1)->create();
        }

        $this->actor = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')->where('level', 0)->first();
    }

    public function testQuery(): void
    {
        $take = mt_rand(4, $this->total);

        $response = $this->actingAs($this->actor)
            ->post(route('graphql.role'), [
                'query' => '{roles(per_page:' . $take . ',order_by:"id",order_dir:"desc") {' . implode(' ', $this->default_fields) . ' {id name slug level}}}'
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'roles' => $this->default_fields
                ]
            ])
            ->assertJson([
                'data' => [
                    'roles' => [
                        'current_page' => 1,
                        'per_page' => $take,
                        'total' => $this->total
                    ]
                ]
            ]);

        $collection = json_decode($response->content())->data->roles;
        $this->assertTrue($collection->per_page >= count($collection->data));
        $this->assertTrue($collection->data[0]->slug === Role::orderBy('id', 'desc')->value('slug'));
        $this->assertTrue($collection->total === $this->total);
    }

    public function testPagination(): void
    {
        $response = $this->actingAs($this->actor)
            ->post(route('graphql.role'), [
                'query' => '{roles(per_page:1,order_by:"id",order_dir:"asc",page:' . $this->total . ') {' . implode(' ', $this->default_fields) . ' {id name slug level}}}'
            ])
            ->assertOk();

        $collection = json_decode($response->content())->data->roles;

        $this->assertTrue($collection->data[0]->id === Role::orderBy('id', 'desc')->value('id'));
    }
}