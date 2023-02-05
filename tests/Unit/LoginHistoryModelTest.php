<?php

namespace Tests\Unit;

use App\Models\LoginHistory;
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class LoginHistoryModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = LoginHistory::class;
    }

    /**
     * LoginHistory creating test
     *
     * @return void
     */
    public function testLoginHistoryCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * LoginHistory updating test
     *
     * @return void
     */
    public function testLoginHistoryModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'user_id' => $model->user_id,
                'ip' => $model->ip,
                'user_agent' => $model->user_agent,
                'failed' => 0
            ]
        );
    }

    /**
     * LoginHistory to User relation test
     *
     * @return void
     */
    public function testLoginHistoryToUserRelation(): void
    {
        $model = self::$class::factory()->create();

        $this->assertTrue($model->user->id == $model->user_id);
    }

    /**
     * LoginHistory removing test
     *
     * @return void
     */
    public function testLoginHistoryRemove(): void
    {
        $this->modelRemovingTest();
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return LoginHistory::count() ? LoginHistory::first() : LoginHistory::factory()->create();
    }
}