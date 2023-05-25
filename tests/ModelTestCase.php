<?php

namespace Tests;

class ModelTestCase extends TestCase
{
    /**
     * Testing model class
     * @var string|null
     */
    protected static $class = null;

    /**
     * Default test for creating model
     *
     * @return void
     */
    protected function modelCreatingTest(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
    }

    /**
     * Default test for model updating
     *
     * @param array $values
     * @param array $optional
     * @param callable|null $callback
     * @return void
     */
    protected function modelModifyingTest(array $values, array $optional = [], ?callable $callback = null): void
    {
        $model = static::getModel();

        $old = $model->toArray();

        foreach ($values as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        if(!empty($optional)) {
            foreach ($values as $key => $value) {
                if (in_array($key, $optional)) unset($values[$key]);
            }
        }

        $missing = $has = ['id' => $model->id,];
        foreach ($values as $key => $value) {
            $missing[$key] = $old[$key];
            $has[$key] = $value;
        }

        $table = $model::factory()->make()->getTable();

        if (is_callable($callback)) {
            $callback($model);
        }

        $this->assertDatabaseMissing($table, $missing)->assertDatabaseHas($table, $has);
    }

    /**
     * Default test for model removing
     *
     * @param callable|null $callback
     * @return void
     */
    protected function modelRemovingTest(?callable $callback = null): void
    {
        $model = static::getModel();
        $model->delete();
        $this->assertModelMissing($model);

        if (is_callable($callback)) {
            $callback($model);
        }
    }
}