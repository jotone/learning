<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseProduct;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Tests\ModelTestCase;

class CourseModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Course::class;
    }

    /**
     * Tests the creation of a model instance and validates the uniqueness constraint on the "url" field.
     *
     * This method performs several actions:
     * - Asserts that another instance of the model can be created, aiming to confirm the factory setup works as expected.
     * - Validates the enforcement of the uniqueness constraint on the "url" field by attempting to create
     *   a duplicate entry and expecting a constraint violation exception.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $course = self::$class::factory()->create();
        $this->assertModelExists(self::$class::factory()->create());
        // Test the "courses" table "url" field is "unique"
        $this->dbErrorsTest([
            (object)[
                'field' => 'url',
                'value' => $course->url,
                'error_class' => UniqueConstraintViolationException::class,
                'error_message' => 'Integrity constraint violation'
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
        $this->modelModificationTest([
            'name',
            'url',
            'description',
            'tracking_status',
            'terms_conditions_text'
        ]);
    }

    /**
     * Tests the association between the Course and Category models.
     *
     * @return void
     */
    public function testRelationToCategory(): void
    {
        // Create a new category instance.
        $category = Category::factory()->create();
        // Create a new course instance and associate it with the previously created category.
        $course = Course::factory()->create();
        // Attach the category to the course
        $course->categories()->attach($category);
        // Check the relation exists on the database
        $this->assertDatabaseHas('category_relation', [
            'category_id' => $category->id,
            'entity_type' => $course::class,
            'entity_id' => $course->id
        ]);

        // Assert the course's associated category URL matches the category's URL.
        $this->assertTrue(in_array($category->url, $course->categories()->pluck('url')->toArray()));
    }

    /**
     * Tests the relationship between Course and CourseProduct models.
     *
     * @return void
     */
    public function testRelationToCourseProduct(): void
    {
        // Create a new Course instance
        $course = Course::factory()->create();
        // Create a new CourseProduct instance, related to the Course.
        $product = CourseProduct::factory()->create([
            'course_id' => $course->id
        ]);
        // Assert that the product's 'product' attribute is in the list of products associated with the course.
        $this->assertTrue(in_array($product->product, $course->products()->pluck('product')->toArray()));
    }

    public function testRelationToUsers(): void
    {
        // Create a student user
        $user = User::factory()->student()->create();
        // Create a course instance.
        $course = Course::factory()->create();
        // Associate the course with the user.
        $course->users()->attach($user);

        $this
            // Check the 'user_courses' table for the association between the user and the course.
            ->assertDatabaseHas('user_courses', [
                'user_id' => $user->id,
                'course_id' => $course->id
            ])
            // Verify the user email is in the course list of user emails, confirming the relationship.
            ->assertTrue(in_array($user->email, $course->users()->pluck('email')->toArray()));
    }

    /**
     * Tests the removal of a Course model and its effects on related data.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(
            self::$class::count() ? self::$class::inRandomOrder()->first() : self::$class::factory()->create(),
            fn($course) => $this
                // Assert there are courses on the category relations table
                ->assertDatabaseMissing('category_relation', [
                    'entity_type' => $course::class,
                    'entity_id' => $course->id
                ])
                // Assert there are no related course products
                ->assertDatabaseMissing('course_products', ['course_id' => $course->id])
                // Assert there are no relation between courses
                ->assertDatabaseMissing('course_relation', ['course_id' => $course->id])
                ->assertDatabaseMissing('course_relation', ['related_id' => $course->id])
                // Assert the course was detached from the user
                ->assertDatabaseMissing('user_courses', ['course_id' => $course->id])
        );
    }
}