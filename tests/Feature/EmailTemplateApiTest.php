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
                    'title' => [lang('validation.required', 'title')],
                    'slug'  => [lang('validation.required', 'slug')]
                ]
            ],
        ];
        foreach ($cases as $case) {
            $this
                ->actingAs(self::$actor)
                ->postJson(route(self::$route_prefix . 'store'), $case['send'])
                ->assertJson(['errors' => $case['assert']])
                ->assertUnprocessable();
        }
    }

    /**
     * Test email template store
     *
     * @return void
     */
    public function testEmailTemplateStore(): void
    {
        $model = EmailTemplate::factory()->make();

        $table = $model->getTable();

        $values = [
            'title' => $model->title,
            'slug'  => $model->slug,
        ];

        $response = $this
            ->actingAs(self::$actor)
            ->postJson(route(self::$route_prefix . 'store'), $values)
            ->assertJsonFragment($values)->assertCreated();

        $content = json_decode($response->content());
        $values['id'] = $content->id;
        $this->assertDatabaseHas($table, $values);
    }

    /**
     * Test EmailTemplate remove
     * @return void
     */
    public function testEmailTemplateDestroy(): void
    {
        $this->deleteTest($this->getModel());
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