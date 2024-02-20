<?php

namespace Tests\Unit;

use App\Models\{Course, Role, User};
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

    /**
     * Tests the creation of a User model and validates various constraints.
     *
     * This test covers:
     * - Verifying that a User model can be successfully created.
     * - Ensuring unique constraints on the "email" field.
     * - Validating acceptable values for the "status" field.
     * - Checking foreign key constraints on the "role_id" field.
     * - Ensuring acceptable values for the "shirt_size" field.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $user = self::$class::factory()->create();
        // Assert that the newly created user model actually exists in the database
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

    /**
     * Tests the modification capabilities of a model.
     *
     * This function specifically tests updating various fields of a model to ensure
     * that modifications are properly handled and persisted in the database.
     * The fields tested include personal information and contact details, which are
     * common requirements for user-related models.
     *
     * @return void
     */
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

    /**
     * Tests the association between the User and Course models.
     *
     * @return void
     */
    public function testRelationToCourse(): void
    {
        // Create a student user
        $user = User::factory()->student()->create();
        // Create a course instance.
        $course = Course::factory()->create();
        // Associate the user with the course.
        $user->courses()->attach($course);

        $this
            // Check the 'user_courses' table for the association between the user and the course.
            ->assertDatabaseHas('user_courses', [
                'user_id' => $user->id,
                'course_id' => $course->id
            ])
            // Verify the course URL is in the user's list of course URLs, confirming the relationship.
            ->assertTrue(in_array($course->url, $user->courses()->pluck('url')->toArray()));
    }

    /**
     * Tests the relationship between the User model and the Role model.
     *
     * This function verifies that the user model correctly establishes a relationship
     * with the role model via the 'role_id' foreign key. It ensures that a user is
     * associated with the correct role, both by checking the role's slug and id.
     *
     * @return void
     */
    public function testRelationToRole(): void
    {
        // Create a new role instance
        $role = Role::factory()->create();
        // Create a new user instance, explicitly setting the 'role_id' to link the user to the role.
        $user = self::$class::factory()->create([
            'role_id' => $role->id
        ]);
        // Assert that the user's role is correctly set by comparing the 'slug'
        $this->assertTrue($user->role->slug === $role->slug && $user->role->id === $role->id);
    }

    /**
     * Tests the removal of a User model and its effects on related data.
     *
     * This function verifies that when a User model is removed, any related
     * records in the database (e.g., in the 'user_courses' table) are also
     * appropriately handled.
     * Specifically, it ensures that no 'user_courses' records exist for
     * the removed user, thereby testing the cascading effects or cleanup
     * operations associated with deleting a user.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(
            self::$class::student()->count()
                ? self::$class::student()->inRandomOrder()->first()
                : self::$class::factory()->student()->create(),
            fn($user) => $this
                // Assert that the 'user_courses' table does not have any records
                ->assertDatabaseMissing('user_courses', ['user_id' => $user->id])
        );
    }
}