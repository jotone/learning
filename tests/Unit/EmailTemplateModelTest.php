<?php

namespace Tests\Unit;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class EmailTemplateModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = EmailTemplate::class;
    }

    /**
     * EmailTemplate creating test
     *
     * @return void
     */
    public function testEmailTemplateCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * EmailTemplate updating test
     *
     * @return void
     */
    public function testEmailTemplateModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'name' => $model->name,
                'slug' => $model->slug
            ]
        );
    }

    /**
     * EmailTemplate removing test
     *
     * @return void
     */
    public function testEmailTemplateRemove(): void
    {
        $this->modelRemovingTest();
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return EmailTemplate::where('subject', 'Hello there, %username%')->count()
            ? EmailTemplate::where('subject', 'Hello there, %username%')->first()
            : EmailTemplate::factory()->create();
    }
}