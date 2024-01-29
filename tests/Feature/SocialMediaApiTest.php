<?php

namespace Feature;

use App\Models\{SocialMedia, User};
use Tests\TestCase;

class SocialMediaApiTest extends TestCase
{
    /**
     * User that performs a request
     *
     * @var User|null
     */
    protected ?User $actor = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (is_null($this->actor)) {
            $this->actor = User::superuser()->first();
        }
    }

    /**
     * Tests the store functionality of the SocialMedia model.
     *
     * This function simulates a user (actingAs) making a POST request to the API route designated
     * for storing a new "SocialMedia" record.
     * It checks if the API correctly creates the resource and returns a 201 status code along
     * with the expected data. Finally, it verifies that the new data exists in the database.
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
        // Simulate a user being logged in and sending a POST request to the 'store' route,
        // then check if the response has the correct status and data.
        $this->actingAs($this->actor)
            ->postJson(route('api.socials.store'), $model->toArray())
            ->assertCreated()
            ->assertJsonFragment($data);
        // Verify that the database now contains a new record with the provided data.
        $this->assertDatabaseHas('social_media', $data);
    }

    /**
     * Tests the update functionality for a specific SocialMedia record.
     *
     * It initially creates a "SocialMedia" record and then attempts to update it with new data.
     * The test verifies that the API responds with a 200-status code and the updated data.
     * Furthermore, it checks that the database has been updated accordingly and no longer
     * contains the old data but the new updated data instead.
     */
    public function testUpdate(): void
    {
        // Create and persist an instance of the SocialMedia model to the database.
        $model = SocialMedia::factory()->create();
        // Generate new data for the update using the factory.
        $new_data = SocialMedia::factory()->make();
        // Prepare the new data for updating the model.
        $data = [
            'type' => $new_data->type,
            'caption' => $new_data->caption,
            'link' => $new_data->link,
            'icon' => $new_data->icon
        ];
        // Act as a specific user, send a PUT request to the 'update' route with the new data, and assert the response.
        $this->actingAs($this->actor)
            ->putJson(route('api.socials.update', $model->id), $data)
            ->assertOk()
            ->assertJsonFragment(array_merge(['id' => $model->id], $data));

        // Verify that the old data is not present and the new data is correctly stored in the database.
        $this
            ->assertDatabaseMissing('social_media', [
                'id' => $model->id,
                'type' => $model->type,
                'caption' => $model->caption,
                'link' => $model->link,
                'icon' => $model->icon
            ])
            ->assertDatabaseHas('social_media', array_merge(['id' => $model->id], $data));
    }

    /**
     * Tests the "delete" functionality for a SocialMedia record.
     *
     * This function simulates a user sending a DELETE request to the appropriate
     * API route to remove a "SocialMedia" record.
     * It verifies that the API responds with a 204 status code, indicating the
     * resource was successfully deleted.
     * Lastly, it confirms that the record is no longer present in the database.
     */
    public function testDestroy(): void
    {
        // Create and persist an instance of the SocialMedia model to the database.
        $model = SocialMedia::factory()->create();
        // Act as a specific user, send a DELETE request to the 'destroy' route for the created model, and assert the response status.
        $this->actingAs($this->actor)
            ->deleteJson(route('api.socials.destroy', $model->id))
            ->assertNoContent();
        // Verify that the database no longer contains the deleted record.
        $this->assertDatabaseMissing('social_media', ['id' => $model->id]);
    }
}