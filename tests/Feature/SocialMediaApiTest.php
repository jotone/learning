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
        $this->runStoreTest(['type', 'url']);
    }

    /**
     * Test SocialMedia update
     * @return void
     */
    public function testSocialMediaUpdate(): void
    {
        $this->runUpdateTest($this->getModel(), ['type', 'url']);
    }

    /**
     * Test SocialMediaLink remove
     * @return void
     */
    public function testSocialMediaLinkDestroy(): void
    {
        $this->runDeleteTest($this->getModel());
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