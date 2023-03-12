<?php

namespace Tests\Unit;

use App\Models\{Role, User};
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class RoleModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Role::class;
    }

    /**
     * Role creating test
     *
     * @return void
     */
    public function testRoleCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * Role updating test
     *
     * @return void
     */
    public function testRoleModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'name' => $model->name,
                'slug' => $model->slug,
                'level' => $model->level
            ],
        );
    }

    /**
     * Role to users relation test
     *
     * @return void
     */
    public function testRoleToUsersRelation(): void
    {
        $model = self::$class::factory()->create();
        $user = User::factory()->create(['role_id' => $model->id]);

        $this->assertTrue(in_array($user->id, $model->users()->pluck('users.id')->toArray()));
    }

    /**
     * Role removing test
     *
     * @return void
     */
    public function testRoleRemove(): void
    {
        $this->modelRemovingTest(function ($model) {
            $this->assertDatabaseMissing('users', ['role_id' => $model->id]);
        });
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return Role::where('level', '>', 127)->where('level', '<', 255)->count()
            ? Role::where('level', '>', 127)->where('level', '<', 255)->first()
            : Role::factory()->create();
    }
}