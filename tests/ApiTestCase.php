<?php

namespace Tests;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Mod;

class ApiTestCase extends TestCase
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

    protected function runStoreTest(Model $model, array $data): void
    {
        // Send a POST request to the 'store' route, then check if the response has the correct status and data.
        $this->actingAs($this->actor)
            ->postJson(route(static::$route . 'store'), $model->toArray())
            ->assertCreated()
            ->assertJsonFragment($data);
        // Verify that the database now contains a new record with the provided data.
        $this->assertDatabaseHas($model->getTable(), $data);
    }

    /**
     * Performs an update operation test on a given model using new data and asserts the results.
     *
     * @param Model $model The existing model instance to be updated.
     * @param Model $new_data A model instance containing the new data for the update.
     * @param array $keys The keys indicating which model attributes should be updated.
     * @return void
     */
    protected function runUpdateTest(Model $model, Model $new_data, array $keys): void
    {
        // Initialize an array to hold new data for the update.
        $data = [];
        // Initialize array to hold original data for comparison.
        $original = [];
        // Assign the corresponding value from new_data to data, and from model to original, for later comparison.
        foreach ($keys as $key) {
            $data[$key] = $new_data->{$key};
            $original[$key] = $model->{$key};
        }
        // Act as a specific user, send a PUT request to the 'update' route with the new data, and assert the response.
        $this->actingAs($this->actor)
            ->putJson(route(static::$route . 'update', $model->id), $data)
            ->assertOk()
            ->assertJsonFragment(array_merge(['id' => $model->id], $data));
        // Verify that the old data is not present and the new data is correctly stored in the database.
        $this
            // Assert that the database no longer contains the original data
            ->assertDatabaseMissing($model->getTable(), array_merge(['id' => $model->id], $original))
            // Assert that the database contains the new data.
            ->assertDatabaseHas($model->getTable(), array_merge(['id' => $model->id], $data));
    }

    /**
     * Tests the deletion (destroy) functionality of a given model.
     *
     * This method simulates a user action to delete a specific model instance by sending
     * a DELETE request to the model's corresponding 'destroy' route. It asserts that
     * the response status indicates successful deletion (HTTP 204 No Content). Furthermore,
     * it verifies that the model instance no longer exists in the database.
     *
     * @param Model $model The model instance to be deleted.
     * @return void
     */
    protected function runDestroyTest(Model $model): void
    {
        // Act as a specific user, send a DELETE request to the 'destroy' route for the created model, and assert the response status.
        $this->actingAs($this->actor)
            ->deleteJson(route(static::$route . 'destroy', $model->id))
            ->assertNoContent();
        // Verify that the database no longer contains the deleted record.
        $this->assertDatabaseMissing($model->getTable(), ['id' => $model->id]);
    }
}