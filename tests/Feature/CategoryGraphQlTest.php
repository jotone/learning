<?php

namespace Tests\Feature;

use App\Services\Str;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Tests\GraphQlTestCase;

class CategoryGraphQlTest extends GraphQlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Category::count() < 5) {
            Category::factory(6)->create();
        }
        $this->total = Category::count();
    }

    /**
     * Test the 'categories' GraphQL query.
     *
     * This method uses the runQueryTest function to send a GraphQL query for categories
     * and performs an assertion on the response.
     * The assertion is specified in the callback function.
     *
     * @return void
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
     * This method verifies that the GraphQL endpoint correctly handles pagination for category queries.
     * It checks the response's pagination properties and data consistency using a custom
     * assertion in the callback function.
     *
     * @return void
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
     *
     * @return void
     */
    public function testStore(): void
    {
        $category = Category::factory()->make();
        $this->runMutationTest(
            type: 'create',
            route: route('graphql.category'),
            query: 'name: "%s", url: "%s", description: "%s", type: "course"',
            params: [
                $category->name,
                $category->url,
                $category->description
            ],
            response_fields: 'id name url description type',
            callback: fn() => $this->assertDatabaseHas('categories', [
                'name' => $category->name,
                'url' => Str::generateUrl($category->url),
                'description' => $category->description,
                'type' => Course::class
            ])
        );
    }

    /**
     * Tests the sorting functionality for categories.
     *
     * This test ensures that categories can be sorted as expected by verifying
     * the 'position' attribute in the database matches the expected order.
     * It retrieves a list of category IDs in a random order, performs a mutation
     * test to sort them, and then asserts that each category's 'position' in the
     * database reflects the sorted order.
     *
     * @return void
     */
    public function testSorting(): void
    {
        $categories = Category::inRandomOrder()->pluck('id')->toArray();

        $this->runMutationTest(
            type: 'sort',
            route: route('graphql.category'),
            query: 'list: [%s]',
            params: [implode(',', $categories)],
            response_fields: 'id',
            callback: function () use ($categories) {
                foreach ($categories as $pos => $id) {
                    $this->assertDatabaseHas('categories', [
                        'id' => $id,
                        'position' => $pos
                    ]);
                }
            }
        );
    }

    /**
     * Tests the update functionality for a Category model.
     *
     * This test ensures that an existing category can be updated with new information
     * through a GraphQL mutation.
     * It verifies that the update operation correctly changes the category's details
     * in the database and checks the integrity of the update by asserting the absence
     * of the old data and the presence of the new data in the database.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $category = Category::factory()->create();

        $new_data = Category::factory()->make();

        $data = [
            'id' => $category->id,
            'name' => $new_data->name,
            'url' => $new_data->url
        ];

        $this->runMutationTest(
            type: 'update',
            route: route('graphql.category'),
            query: 'id: %s, name: "%s", url: "%s"',
            params: array_values($data),
            response_fields: 'id name url',
            callback: fn() => $this
                ->assertDatabaseMissing('categories', [
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => $category->url,
                ])
                ->assertDatabaseHas('categories', $data)
        );
    }

    /**
     * Tests the image upload functionality for a Category model through a GraphQL mutation.
     *
     * This test case simulates an authenticated user uploading an image for a category.
     * It constructs a fake image file and sends a POST request to a specified GraphQL endpoint
     * designed to handle category updates, including image uploads.
     * The test verifies the response to ensure it matches the expected output, including
     * checking the URL of the uploaded image as stored in the database.
     *
     * @return void
     */
    public function testUploadImage(): void
    {
        $category = Category::factory()->create();

        $name = uniqid() . '.jpg';
        $expected_file_url = '/images/categories/' . $category->id . '/' . $name;
        $this
            ->actingAs($this->actor)
            ->post(route('graphql.category'), [
                'operations' => json_encode([
                    'query' => 'mutation UpdateCategory($id: Int!, $file: Upload!) {update(id: $id, img_url: $file) {id img_url}}',
                    'variables' => [
                        'id' => $category->id,
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
                        'id' => $category->id,
                        'img_url' => [
                            'original' => $expected_file_url,
                            'large' => '/images/categories/' . $category->id . '/thumb_large/' . $name,
                            'small' => '/images/categories/' . $category->id . '/thumb_small/' . $name,
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'img_url' => $expected_file_url
        ]);
    }

    /**
     * Test the 'destroy' mutation for categories in the GraphQL API.
     *
     * This method verifies the functionality of the GraphQL API for deleting an existing category.
     * It checks that the category is properly removed from the database and that the API response
     * confirms the successful deletion. The method sends a delete request for a specific category and
     * then uses database assertions to ensure that the category no longer exists in the database.
     *
     * @return void
     */
    public function testDestroy(): void
    {
        $this->runDeleteTest(route('graphql.category'), Category::factory()->create());
    }
}