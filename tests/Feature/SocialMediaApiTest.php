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
     * Test the functionality of storing a SocialMedia model through an API endpoint
     *
     * @return void
     */
    public function testStore(): void
    {
        // Create a new instance of SocialMedia using the factory but do not persist it to the database.
        $model = SocialMedia::factory()->make();
        // Prepare the data that you expect to send in the request.
        $data = [
            'type' => $model->type,
            'caption' => $model->caption,
            'icon' => $model->icon
        ];
        // Send request as a superuser to create a SocialMedia entity
        $this->actingAs($this->actor)
            ->postJson(route('api.socials.store'), $model->toArray())
            ->assertCreated()
            ->assertJsonFragment($data);
        // Check the database to ensure that a record matching the provided data exists.
        $this->assertDatabaseHas('social_media', $data);
    }
}