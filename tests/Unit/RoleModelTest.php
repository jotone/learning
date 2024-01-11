<?php

namespace Tests\Unit;

use App\Models\{Role, User};
use Illuminate\Database\{QueryException, UniqueConstraintViolationException};
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
        $role = self::$class::factory()->create();
        $this->assertModelExists($role);

        $this->dbErrorsTest([
            // Test the "roles" table "slug" field is "unique"
            (object)[
                'field' => 'slug',
                'value' => $role->slug,
                'error_class' => UniqueConstraintViolationException::class,
                'error_message' => 'Integrity constraint violation'
            ],
            // Test the "roles" table "level" field limits
            (object)[
                'field' => 'level',
                'value' => -1,
                'error_class' => QueryException::class,
                'error_message' => 'Numeric value out of range'
            ],
            (object)[
                'field' => 'level',
                'value' => 256,
                'error_class' => QueryException::class,
                'error_message' => 'Numeric value out of range'
            ]
        ]);
    }

    public function testModify(): void
    {
        $this->modelModificationTest(['name', 'slug', 'level']);
    }

    public function testRelationToUsers(): void
    {
        $role = self::$class::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue(in_array($user->id, $role->users()->pluck('users.id')->toArray()));
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($role) => $this->assertDatabaseMissing('users', ['role_id' => $role->id]));
    }
}