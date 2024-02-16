<?php

namespace Feature;

use App\Models\Category;
use Tests\GraphQlTestCase;

class CategoryGraphQlTest extends GraphQlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Category::count() < 5) {
            Category::factory(5 - $this->total + 1)->create();
        }
        $this->total = Category::count();
    }

    /**
     * Test the 'categories' GraphQL query.
     *
     * This method uses the runQueryTest function to send a GraphQL query for categories and performs
     * an assertion on the response. The assertion is specified in the callback function.
     */
    public function testQuery(): void
    {
        $this->runQueryTest(
            route: route('graphql.category'),
            field: 'categories',
            response_fields: 'id name url',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->url === Category::orderBy('id', 'desc')->value('url'))
        );
    }
}