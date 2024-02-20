<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;

class ModelTestCase extends TestCase
{
    /**
     * Testing model class
     *
     * @var string
     */
    protected static string $class;

    /**
     * Default test for model updating
     *
     * @param array $fields
     * @return void
     */
    protected function modelModificationTest(array $fields): void
    {
        $model = self::$class::factory()->create();

        $old = clone $model;

        $new = self::$class::factory()->make();

        $check_missing = ['id' => $model->id];
        $check_exists = ['id' => $model->id];
        foreach ($fields as $key) {
            $model->{$key} = $new->{$key};
            $check_missing[$key] = $old->{$key};
            $check_exists[$key] = $new->{$key};
        }

        $model->save();

        $this->assertDatabaseMissing($model->getTable(), $check_missing);
        $this->assertDatabaseHas($model->getTable(), $check_exists);
    }

    /**
     * Default test for model removing
     *
     * @param Model $model
     * @param callable|null $callback
     * @return void
     */
    protected function modelRemovingTest(Model $model, ?callable $callback = null): void
    {
        $model->delete();
        $this->assertModelMissing($model);

        is_callable($callback) && $callback($model);
    }

    /**
     * Run database saving model with errors response
     *
     * @param array $cases
     * @return void
     */
    protected function dbErrorsTest(array $cases): void
    {
        foreach ($cases as $case) {
            try {
                self::$class::factory()->create([$case->field => $case->value]);
            } catch (\Exception $e) {
                $this->assertTrue($case->error_class === get_class($e));
                $this->assertTrue(str_contains($e->getMessage(), $case->error_message));
            }
        }
    }
}