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

    public function testCreate(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
    }

    public function testModify(): void
    {
        $this->modelModificationTest([
            'type',
            'caption',
            'link',
            'icon'
        ]);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest();
    }
}