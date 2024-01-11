<?php

namespace Tests\Unit;

use App\Models\{Role, User};
use Illuminate\Database\{QueryException, UniqueConstraintViolationException};
use Illuminate\Support\Str;
use Tests\ModelTestCase;

class UserModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = User::class;
    }

    public function testCreate(): void
    {
        $user = self::$class::factory()->create();
        $this->assertModelExists($user);

        $this->dbErrorsTest([
            // Test the "users" table "email" field is "unique"
            (object)[
                'field' => 'email',
                'value' => $user->email,
                'error_class' => UniqueConstraintViolationException::class,
                'error_message' => 'Integrity constraint violation'
            ],
            // Test the "User" model does not accept a random status field value
            (object)[
                'field' => 'status',
                'value' => Str::random(),
                'error_class' => \Exception::class,
                'error_message' => 'Not a valid enum name'
            ],
            // Test the "users" table does not accept a nonexistent role
            (object)[
                'field' => 'role_id',
                'value' => 0,
                'error_class' => QueryException::class,
                'error_message' => 'Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails'
            ],
            // Test the "User" model does not accept a random shirt_size field value
            (object)[
                'field' => 'shirt_size',
                'value' => Str::random(),
                'error_class' => \Exception::class,
                'error_message' => 'Not a valid enum name'
            ]
        ]);
    }

    public function testModify(): void
    {
        $this->modelModificationTest([
            'first_name',
            'last_name',
            'email',
            'timezone',
            'country',
            'region',
            'city',
            'zip',
            'phone'
        ]);
    }

    public function testRelationToRole(): void
    {
        $role = Role::factory()->create();
        $user = self::$class::factory()->create([
            'role_id' => $role->id
        ]);

        $this->assertTrue($user->role->slug === $role->slug && $user->role->id === $role->id);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($user) => $this
            ->assertDatabaseMissing('user_courses', ['user_id' => $user->id])
        );
    }
}