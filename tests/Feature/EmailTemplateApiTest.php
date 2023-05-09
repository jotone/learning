<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Tests\ApiTestCase;

class EmailTemplateApiTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$class = EmailTemplate::class;
        self::$route_prefix = 'api.email-templates.';
    }

    /**
     * EmailTemplate store request validation test
     *
     * @return void
     */
    public function testEmailTemplateStoreValidation(): void
    {
        $cases = [
            // Send empty request body
            [
                'send'   => [],
                'assert' => [
                    'name' => [lang('validation.required', 'name')],
                    'slug'  => [lang('validation.required', 'slug')]
                ]
            ],
        ];
        foreach ($cases as $case) {
            $this->actingAs(self::$actor)
                ->postJson(route(self::$route_prefix . 'store'), $case['send'])
                ->assertUnprocessable()
                ->assertJson(['errors' => $case['assert']]);
        }
    }

    /**
     * Test email template store
     *
     * @return void
     */
    public function testEmailTemplateStore(): void
    {
        $this->runStoreTest(['name', 'slug']);
    }

    /**
     * Test EmailTemplate update
     * @return void
     */
    public function testEmailTemplateUpdate(): void
    {
        $this->runUpdateTest($this->getModel(), ['name', 'slug']);
    }

    /**
     * Test EmailTemplate remove
     * @return void
     */
    public function testEmailTemplateDestroy(): void
    {
        $this->runDeleteTest($this->getModel());
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