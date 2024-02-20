<?php

namespace Tests\Unit;

use App\Models\SocialMedia;
use Tests\ModelTestCase;

class SocialMediaModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = SocialMedia::class;
    }

    /**
     *  Tests the creation of a SocialMedia model and validates various constraints.
     *
     *  This test covers:
     *  - Verifying that a SocialMedia model can be successfully created.
     * @return void
     */
    public function testCreate(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
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
            'type',
            'caption',
            'link',
            'icon'
        ]);
    }

    /**
     * Tests the removal of a SocialMedia model and its effects on related data.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(self::$class::count() ? self::$class::inRandomOrder()->first() : self::$class::factory()->create());
    }
}