<?php

namespace Tests\Unit;

use App\Models\Category;
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
        $this->assertModelExists(self::$class::factory()->create());
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