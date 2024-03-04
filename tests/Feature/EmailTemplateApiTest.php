<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use Tests\ApiTestCase;

class EmailTemplateApiTest extends ApiTestCase
{
    protected static string $route = 'api.templates.';

    /**
     * Tests the store (create) functionality for the EmailTemplate model.
     *
     * This method simulates creating a new EmailTemplate without persisting it to the database,
     * prepares the necessary data for a POST request, and then delegates to runStoreTest
     * to perform the actual test assertions and validations.
     *
     * @return void
     */
    public function testStore(): void
    {
        // Create a new instance of the SocialMedia model using the factory but don't persist it to the database.
        $model = EmailTemplate::factory()->make();
        // Prepare the data to be sent with the POST request.
        $data = [
            'title' => $model->title,
            'slug' => $model->slug,
            'subject' => $model->subject,
        ];
        // Call a custom method to run the store test, passing in the model and prepared data.
        $this->runStoreTest($model, $data);
    }

    /**
     * Tests the update functionality for an existing EmailTemplate model.
     *
     * It creates a persisted instance of EmailTemplate, generates new data intended for update,
     * and uses the runUpdateTest method to ensure the update process behaves as expected.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        // Persist a new EmailTemplate instance to the database to set up the test condition.
        $model = EmailTemplate::factory()->create();
        // Create a new instance of EmailTemplate with mock data intended as new data for the update.
        $new_data = EmailTemplate::factory()->make();
        // Perform the update test, specifying which fields should be updated, by calling a custom method.
        $this->runUpdateTest($model, $new_data, ['title', 'slug', 'subject']);
    }

    /**
     * Tests the "delete" functionality for an Email Template record.
     *
     * This function simulates a user sending a DELETE request to the appropriate
     * API route to remove an "EmailTemplate" record.
     * It verifies that the API responds with a 204 status code, indicating the
     * resource was successfully deleted.
     * Lastly, it confirms that the record is no longer present in the database.
     *
     * @return void
     */
    public function testDestroy(): void
    {
        // Create and persist an instance of the Email Template model to the database.
        $this->runDestroyTest(EmailTemplate::factory()->create());
    }
}