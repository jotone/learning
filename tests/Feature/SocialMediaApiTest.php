<?php
namespace Feature;

use App\Models\SocialMedia;
use Tests\ApiTestCase;

class SocialMediaApiTest extends ApiTestCase
{
    protected static string $route = 'api.socials.';

    /**
     * Tests the store functionality of the SocialMedia model.
     *
     * This function simulates a user (actingAs) making a POST request to the API route designated
     * for storing a new "SocialMedia" record.
     * It checks if the API correctly creates the resource and returns a 201 status code along
     * with the expected data. Finally, it verifies that the new data exists in the database.
     *
     * @return void
     */
    public function testStore(): void
    {
        // Create a new instance of the SocialMedia model using the factory but don't persist it to the database.
        $model = SocialMedia::factory()->make();
        // Prepare the data to be sent with the POST request.
        $data = [
            'type' => $model->type,
            'caption' => $model->caption,
            'icon' => $model->icon
        ];
        // Call a custom method to run the store test, passing in the model and prepared data.
        $this->runStoreTest($model, $data);
    }

    /**
     * Tests the update functionality for a specific SocialMedia record.
     *
     * It initially creates a "SocialMedia" record and then attempts to update it with new data.
     * The test verifies that the API responds with a 200-status code and the updated data.
     * Furthermore, it checks that the database has been updated accordingly and no longer
     * contains the old data but the new updated data instead.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        // Create and persist an instance of the SocialMedia model to the database.
        $model = SocialMedia::factory()->create();
        // Generate new data for the update using the factory.
        $new_data = SocialMedia::factory()->make();

        $this->runUpdateTest($model, $new_data, ['type', 'caption', 'link', 'icon']);
    }

    /**
     * Tests the sorting functionality for SocialMedia records.
     *
     * This test simulates the process of reordering social media records by sending a PATCH request
     * with a new order for the records. It then verifies that the database reflects the updated order
     * for each record.
     *
     * @return void
     */
    public function testSort(): void
    {
        // Retrieve a random order of social media record IDs.
        $models = SocialMedia::inRandomOrder()->pluck('id')->toArray();
        // Build the request data array with each model's ID and its new position.
        $request_data = [];
        foreach ($models as $i => $id) {
            $request_data[] = ['id' => $id, 'position' => $i];
        }
        // Sending a PATCH request to the sort endpoint with the new order of social media records.
        $this->actingAs($this->actor)
            ->patchJson(route('api.socials.sort'), ['list' => $request_data])
            ->assertOk();
        // Verify that the database has been updated to reflect the new order for each social media record.
        foreach ($models as $i => $id) {
            $this->assertDatabaseHas('social_media', [
                'id' => $id,
                'position' => $i
            ]);
        }
    }

    /**
     * Tests the "delete" functionality for a SocialMedia record.
     *
     * This function simulates a user sending a DELETE request to the appropriate
     * API route to remove a "SocialMedia" record.
     * It verifies that the API responds with a 204 status code, indicating the
     * resource was successfully deleted.
     * Lastly, it confirms that the record is no longer present in the database.
     *
     * @return void
     */
    public function testDestroy(): void
    {
        // Create and persist an instance of the SocialMedia model to the database.
        $this->runDestroyTest(SocialMedia::factory()->create());
    }
}