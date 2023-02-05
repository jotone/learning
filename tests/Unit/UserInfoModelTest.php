<?php

namespace Tests\Unit;

use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class UserInfoModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = UserInfo::class;
    }

    /**
     * UserInfo creating test
     *
     * @return void
     */
    public function testUserInfoCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * UserInfo updating test
     *
     * @return void
     */
    public function testUserInfoModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'user_id' => $model->user_id,
                'country' => $model->country,
                'city' => $model->city,
                'zip' => $model->zip,
                'phone' => $model->phone,
            ]
        );
    }

    /**
     * Test binding info to user
     *
     * @return void
     */
    public function testUserInfoToUserRelation(): void
    {
        $model = UserInfo::factory()->create();

        $this->assertTrue($model->user_id == $model->user->id);
    }

    /**
     * UserInfo removing test
     *
     * @return void
     */
    public function testUseInfoRemove(): void
    {
        $this->modelRemovingTest();
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return UserInfo::count() ? UserInfo::first() : UserInfo::factory()->create();
    }
}