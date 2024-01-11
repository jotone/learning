<?php

namespace Feature;

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

    public function testPagination()
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
}