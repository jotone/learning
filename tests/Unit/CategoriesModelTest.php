<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\UniqueConstraintViolationException;
use Tests\ModelTestCase;

class CategoriesModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Category::class;
    }

    /**
     * Tests the creation of a model instance and validates database constraints, particularly uniqueness.
     *
     * This method performs several key actions:
     * - Test asserts that a newly created model instance exists in the database.
     * - Validates the uniqueness constraint of the "url" field in the model's table to ensure data integrity.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $category = self::$class::factory()->create();
        $this->assertModelExists(self::$class::factory()->create());
        // Test the "email_templates" table "slug" field is "unique"
        $this->dbErrorsTest([
            (object)[
                'field' => 'url',
                'value' => $category->url,
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
        ]);
    }

    /**
     * Tests the association between the Category and Course models.
     * This method verifies that a Course can be associated with a Category through the has-many relationships.
     *
     * @return void
     */
    public function testRelatedCourses(): void
    {
        // Creating a new Course instance
        $course = Course::factory()->create();
        // Creating a new Category instance
        $category = Category::factory()->create();
        // Attach the course to the category
        $category->courses()->attach($course);
        // Check the relation exists on the database
        $this->assertDatabaseHas('category_relation', [
            'category_id' => $category->id,
            'entity_type' => $course::class,
            'entity_id' => $course->id
        ]);

        $this->assertTrue(in_array($course->url, $category->courses()->pluck('url')->toArray()));
    }

    /**
     * Tests the removal of a Category model and its effects on related data.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(
            self::$class::count() ? self::$class::inRandomOrder()->first() : self::$class::factory()->create(),
            fn($category) => $this
                // Assert the related courses were removed
                ->assertDatabaseMissing('category_relation', ['category_id' => $category->id])
        );
    }
}