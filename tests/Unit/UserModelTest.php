<?php

namespace Tests\Unit;

use App\Models\{LoginHistory, User, UserInfo};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Tests\ModelTestCase;
use Tests\Traits\ModelGeneratorsTrait;

class UserModelTest extends ModelTestCase
{
    use ModelGeneratorsTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::$class = User::class;
    }

    /**
     * User creating test
     *
     * @return void
     */
    public function testUserCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * User updating test
     *
     * @return void
     */
    public function testUserModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'email' => $model->email,
                'password' => '123456'
            ],
            optional: ['password'],
            callback: function ($model) {
                $this->assertTrue(Hash::check('123456', $model->password));
            }
        );
    }

    public function testUserToLoginHistoryRelation()
    {
        $model = $this->generateUser();

        $history = LoginHistory::factory()->create(['user_id' => $model->id]);

        $this->assertTrue(in_array($model->id, $model->loginHistory()->pluck('user_id')->toArray()));
        $this->assertTrue(in_array($history->id, $model->loginHistory()->pluck('login_history.id')->toArray()));
    }

    /**
     * Test binding role to user
     *
     * @return void
     */
    public function testUserToRoleRelation(): void
    {
        $role = $this->getRole();
        $model = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($model->role->slug == $role->slug);
    }

    /**
     * Test binding info to user
     *
     * @return void
     */
    public function testUserToUserInfoRelation(): void
    {
        $model = $this->generateUser();
        $info = UserInfo::factory()->create(['user_id' => $model->id]);

        $this->assertTrue($model->info->user_id == $model->id);
        $this->assertTrue($info->user_id == $model->id);
    }

    /**
     * User removing test
     *
     * @return void
     */
    public function testUserRemove(): void
    {
        $this->modelRemovingTest(function ($model) {
            $this->assertDatabaseMissing('login_history', ['user_id' => $model->id]);
            $this->assertDatabaseMissing('user_info', ['user_id' => $model->id]);
        });
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return User::whereHas('role', fn($q) => $q->where('level', '>=', 127))->count()
            ? User::whereHas('role', fn($q) => $q->where('level', '>=', 127))->first()
            : User::factory()->create();
    }
}
