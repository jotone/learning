<?php

namespace Feature;

use App\Http\Controllers\GraphQL\Role\RoleMutation;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
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
     * This method uses the runQueryTest function to send a GraphQL query for categories
     * and performs an assertion on the response.
     * The assertion is specified in the callback function.
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

    /**
     * Test the pagination feature of the 'categories' GraphQL query.
     *
     * This method verifies that the GraphQL endpoint correctly handles pagination for
     * category queries.
     * It checks the response's pagination properties and data consistency using a custom
     * assertion in the callback function.
     */
    public function testPagination(): void
    {
        $this->runPaginationTest(
            route: route('graphql.category'),
            field: 'categories',
            response_fields: 'id name url',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->url === Category::orderBy('id', 'desc')->value('url'))
        );
    }

    /**
     * Test the 'create' mutation for categories in the GraphQL API.
     *
     * This method tests the GraphQL mutation for creating a new category.
     * It validates that the mutation correctly stores the category in the database and
     * that the response is as expected.
     * A custom callback is used to perform additional database assertions.
     */
    public function testStore(): void
    {
        $category = Category::factory()->make();
        $this->runMutationTest(
            type: 'create',
            route: route('graphql.category'),
            query: 'name: "%s", url: "%s", description: "%s"',
            params: [
                $category->name,
                $category->url,
                $category->description,
            ],
            response_fields: 'id name url description',
            callback: fn() => $this->assertDatabaseHas('categories', [
                'name' => $category->name,
                'url' => generateUrl($category->url),
                'description' => $category->description,
            ])
        );
    }
}