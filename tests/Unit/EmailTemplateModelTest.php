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

    /**
     * Tests the creation of a model instance and validates database constraints.
     *
     * This method performs the following actions:
     * 1. It creates an instance of the model using the factory method to ensure
     *    the model can be successfully created and persisted in the database.
     * 2. It tests database constraints, specifically the uniqueness of the "slug" field
     *    in the "email_templates" table, to ensure data integrity is maintained.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $template = self::$class::factory()->create();
        // Confirm that the model creation process works as expected.
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
            'title',
            'slug',
            'subject'
        ]);
    }

    /**
     * Tests the removal of a EmailTemplate model and its effects on related data.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->modelRemovingTest(self::$class::count() ? self::$class::inRandomOrder()->first() : self::$class::factory()->create());
    }
}