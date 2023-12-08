<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\ModelTestCase;

class RoleModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Role::class;
    }

    public function testCreate(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
    }

    public function testModify(): void
    {
        $this->modelModificationTest(['name', 'slug', 'level']);
    }

    public function testRelationToUsers(): void
    {
        $model = self::$class::factory()->create();
        $user = User::factory()->create(['role_id' => $model->id]);

        $this->assertTrue(in_array($user->id, $model->users()->pluck('users.id')->toArray()));
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($model) => $this->assertDatabaseMissing('users', ['role_id' => $model->id]));
    }
}