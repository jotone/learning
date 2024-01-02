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

        $test_cases = [
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
        ];

        foreach ($test_cases as $case) {
            try {
                self::$class::factory()->create([$case->field => $case->value]);
            } catch (\Exception $e) {
                $this->assertTrue($case->error_class === get_class($e));
                $this->assertTrue(str_contains($e->getMessage(), $case->error_message));
            }
        }
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