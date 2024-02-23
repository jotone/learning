<?php

namespace Feature;

use App\Enums\CourseStatus;
use App\Models\{Course, Settings};
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
     * This method verifies that the GraphQL endpoint correctly handles pagination for course queries.
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

    /**
     * Test the 'create' mutation for courses in the GraphQL API.
     *
     * This method tests the GraphQL mutation for creating a new course.
     * It validates that the mutation correctly stores the course in the database and
     * that the response is as expected.
     * A custom callback is used to perform additional database assertions.
     *
     * @return void
     */
    public function testStore(): void
    {
        $lang = Settings::where('key', 'main_language')->value('value');
        $course = Course::factory()->make();
        $this->runMutationTest(
            type: 'create',
            route: route('graphql.course'),
            query: 'name: "%s", url: "%s", description: "%s"',
            params: [
                $course->name,
                $course->url,
                $course->description
            ],
            response_fields: 'id name url description lang status',
            callback: fn() => $this->assertDatabaseHas('courses', [
                'name' => $course->name,
                'url' => generateUrl($course->url),
                'description' => $course->description,
                'lang' => $lang,
                'status' => CourseStatus::fromName('draft')
            ])
        );
    }
}