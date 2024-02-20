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

    /**
     * Tests the creation of a Role model and validates database constraints.
     *
     * This method performs two main checks:
     * 1. It verifies that a Role model can be successfully created and exists in the database.
     * 2. It conducts targeted tests to ensure database constraints are enforced, specifically:
     *    - The uniqueness of the "slug" field in the "roles" table.
     *    - The numeric limits on the "level" field in the "roles" table.
     *
     * @return void
     */
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

    /**
     * Tests the modification capabilities of a model.
     *
     * This function specifically tests updating various fields of a model to ensure
     * that modifications are properly handled and persisted in the database.
     *
     * @return void
     */
    public function testModify(): void
    {
        $this->modelModificationTest(['name', 'slug', 'level']);
    }

    /**
     * Tests the relationship between the Role model and associated User models.
     *
     * @return void
     */
    public function testRelationToUsers(): void
    {
        // Create a new role instance.
        $role = self::$class::factory()->create();
        // Create a new user instance and associate it with the created role.
        $user = User::factory()->create(['role_id' => $role->id]);
        // Verify that the created user is correctly associated with the role.
        $this->assertTrue(in_array($user->id, $role->users()->pluck('users.id')->toArray()));
    }

    /**
     * Tests the removal of a Role model and its effect on associated User models.
     *
     * This method ensures that when a Role model is removed from the database, any User models
     * associated with it through the 'role_id' field are also appropriately handled.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(
            self::$class::whereNotIn('slug', ['admin', 'coach', 'student', 'superuser'])->count()
                ? self::$class::whereNotIn('slug', ['admin', 'coach', 'student', 'superuser'])->inRandomOrder()->first()
                : self::$class::factory()->create(),
            fn($role) => $this->assertDatabaseMissing('users', ['role_id' => $role->id])
        );
    }
}