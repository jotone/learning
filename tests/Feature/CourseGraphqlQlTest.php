<?php

namespace Feature;

use App\Enums\CourseStatus;
use App\Models\{Course, Settings};
use Illuminate\Http\UploadedFile;
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
     * @throws \Exception
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

    /**
     * Tests the update functionality for a Course model.
     *
     * This test ensures that an existing course can be updated with new information
     * through a GraphQL mutation.
     * It verifies that the update operation correctly changes the course's details
     * in the database and checks the integrity of the update by asserting the absence
     * of the old data and the presence of the new data in the database.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $course = Course::factory()->create();

        $new_data = Course::factory()->make();

        $data = [
            'id' => $course->id,
            'name' => $new_data->name,
            'url' => $new_data->url,
            'description' => $new_data->description
        ];

        $this->runMutationTest(
            type: 'update',
            route: route('graphql.course'),
            query: 'id: %s, name: "%s", url: "%s", description: "%s"',
            params: array_values($data),
            response_fields: 'id name url',
            callback: fn() => $this
                ->assertDatabaseMissing('courses', [
                    'id' => $course->id,
                    'name' => $course->name,
                    'url' => $course->url,
                    'description' => $course->description,
                ])
                ->assertDatabaseHas('courses', $data)
        );
    }

    /**
     * Tests the image upload functionality for a Course model through a GraphQL mutation.
     *
     * This test case simulates an authenticated user uploading an image for a course.
     * It constructs a fake image file and sends a POST request to a specified GraphQL endpoint
     * designed to handle course updates, including image uploads.
     * The test verifies the response to ensure it matches the expected output, including
     * checking the URL of the uploaded image as stored in the database.
     *
     * @return void
     */
    public function testUploadImage(): void
    {
        $course = Course::factory()->create();

        $name = uniqid() . '.jpg';
        $expected_file_url = '/images/courses/' . $course->id . '/' . $name;
        $this
            ->actingAs($this->actor)
            ->post(route('graphql.course'), [
                'operations' => json_encode([
                    'query' => 'mutation UpdateCourse($id: Int!, $file: Upload!) {update(id: $id, img_url: $file) {id img_url}}',
                    'variables' => [
                        'id' => $course->id,
                        'file' => null // The actual file will be mapped in the 'map' section
                    ],
                ]),
                'map' => json_encode([
                    '0' => ['variables.file']
                ]),
                '0' => UploadedFile::fake()->image($name),
            ], [
                'Content-Type' => 'multipart/form-data'
            ])
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    // The expected fields in the mutation response.
                    'update' => [
                        'id' => $course->id,
                        'img_url' => [
                            'original' => $expected_file_url,
                            'large' => '/images/courses/' . $course->id . '/thumb_large/' . $name,
                            'small' => '/images/courses/' . $course->id . '/thumb_small/' . $name,
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'img_url' => $expected_file_url
        ]);
    }

    /**
     * Tests the certificate image upload functionality for a Course model through a GraphQL mutation.
     *
     * This test case simulates an authenticated user uploading a certificate image for a course.
     * The test verifies the response to ensure it matches the expected output, including
     * checking the URL of the uploaded certificate image as stored in the database.
     *
     * @return void
     */
    public function testUploadCertificateImage(): void
    {
        $course = Course::factory()->create(['img_url' => null]);

        $name = uniqid() . '.jpg';

        $expected_file_url = '/images/courses/' . $course->id . '/' . $name;

        $this
            ->actingAs($this->actor)
            ->post(route('graphql.course'), [
                'operations' => json_encode([
                    'query' => 'mutation UpdateCourse($id: Int!, $file: Upload!) {update(id: $id, certificate_img_url: $file) {id certificate_img_url}}',
                    'variables' => [
                        'id' => $course->id,
                        'file' => null // The actual file will be mapped in the 'map' section
                    ],
                ]),
                'map' => json_encode([
                    '0' => ['variables.file']
                ]),
                '0' => UploadedFile::fake()->image($name),
            ], [
                'Content-Type' => 'multipart/form-data'
            ])
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    // The expected fields in the mutation response.
                    'update' => [
                        'id' => $course->id,
                        'certificate_img_url' => $expected_file_url
                    ]
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'certificate_img_url' => $expected_file_url
        ]);
    }
}