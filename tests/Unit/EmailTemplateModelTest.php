<?php

namespace Tests\Unit;

use App\Models\EmailTemplate;
use Illuminate\Database\UniqueConstraintViolationException;
use Tests\ModelTestCase;

class EmailTemplateModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = EmailTemplate::class;
    }

    public function testCreate(): void
    {
        $template = self::$class::factory()->create();
        $this->assertModelExists($template);
        // Test the "email_templates" table "slug" field is "unique"
        $this->dbErrorsTest([
            (object)[
                'field' => 'slug',
                'value' => $template->slug,
                'error_class' => UniqueConstraintViolationException::class,
                'error_message' => 'Integrity constraint violation'
            ]
        ]);
    }

    public function testModify(): void
    {
        $this->modelModificationTest([
            'title',
            'slug',
            'subject'
        ]);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest();
    }
}