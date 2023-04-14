<?php

namespace Tests\Feature;

use App\Models\SocialMediaLink;
use Illuminate\Database\Eloquent\Model;
use Tests\ApiTestCase;

class SocialMediaApiTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$class = SocialMediaLink::class;
        self::$route_prefix = 'api.socials.';
    }

    /**
     * SocialMedia store request validation test
     *
     * @return void
     */
    public function testSocialMediaStoreValidation(): void
    {
        $cases = [
            // Send empty request body
            [
                'send'   => [],
                'assert' => [
                    'type' => [lang('validation.required', 'type')]
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
     * Test social media store
     *
     * @return void
     */
    public function testSocialMediaStore(): void
    {
        $model = SocialMediaLink::factory()->make();

        $table = $model->getTable();

        $values = [
            'type' => $model->type,
            'url'  => $model->url,
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
     * Test SocialMedia update
     * @return void
     */
    public function testSocialMediaUpdate(): void
    {
        $new = SocialMediaLink::factory()->make();

        $fields = ['type', 'url'];

        $model = $this->getModel();

        $missing = array_intersect_key($model->toArray(), array_flip(['id', ...$fields]));

        $update = [];
        foreach ($fields as $key) {
            $update[$key] = $new->$key;
        }

        $updated = ['id' => $model->id, ...$update];

        $this
            ->actingAs(self::$actor)
            ->putJson(route(self::$route_prefix . 'update', $model->id), $update)
            ->assertJsonFragment($updated)
            ->assertOk();

        $this->assertDatabaseMissing($model->getTable(), $missing)->assertDatabaseHas($model->getTable(), $updated);
    }



    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return SocialMediaLink::where('position', '>', 999)->count()
            ? SocialMediaLink::where('position', '>', 999)->first()
            : SocialMediaLink::factory()->create();
    }
}