<?php

namespace Tests;

class ModelTestCase extends TestCase
{
    /**
     * Testing model class
     *
     * @var string
     */
    protected static $class;

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
     * @param callable|null $callback
     * @return void
     */
    protected function modelRemovingTest(?callable $callback = null): void
    {
        $model = self::$class::factory()->create();
        $model->delete();
        $this->assertModelMissing($model);

        is_callable($callback) && $callback($model);
    }
}