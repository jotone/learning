<?php

namespace Feature;

use App\Models\Course;
use Tests\GraphQlTestCase;

class CourseGraphqlQlTest extends GraphQlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Course::count() < 5) {
            Course::factory(6)->create();
        }
        $this->total = Course::count();
    }

    /**
     * Test the 'courses' GraphQL query.
     *
     * This method uses the runQueryTest function to send a GraphQL query for courses
     * and performs an assertion on the response.
     * The assertion is specified in the callback function.
     *
     * @return void
     */
    public function testQuery(): void
    {
        $this->runQueryTest(
            route: route('graphql.course'),
            field: 'courses',
            response_fields: 'id name url',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->url === Course::orderBy('id', 'desc')->value('url'))
        );
    }

    /**
     * Test the pagination feature of the 'courses' GraphQL query.
     *
     * This method verifies that the GraphQL endpoint correctly handles pagination for category queries.
     * It checks the response's pagination properties and data consistency using a custom
     * assertion in the callback function.
     *
     * @return void
     */
    public function testPagination(): void
    {
        $this->runPaginationTest(
            route: route('graphql.course'),
            field: 'courses',
            response_fields: 'id name url',
            callback: fn($collection) => $this->assertTrue($collection->data[0]->url === Course::orderBy('id', 'desc')->value('url'))
        );
    }
}