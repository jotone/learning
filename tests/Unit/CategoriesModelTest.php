<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Tests\ModelTestCase;

class CategoriesModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Category::class;
    }

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

    public function testModify(): void
    {
        $this->modelModificationTest([
            'name',
            'url',
            'description',
        ]);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($category) => $this->assertDatabaseMissing('category_relations', [
            'category_id' => $category->id
        ]));
    }
}