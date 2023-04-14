<?php

namespace Tests\Unit;

use App\Models\SocialMediaLink;
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class SocialMediaLinkModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = SocialMediaLink::class;
    }

    /**
     * SocialMediaLink creating test
     *
     * @return void
     */
    public function testSocialMediaLinkCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * SocialMediaLink updating test
     *
     * @return void
     */
    public function testSocialMediaLinkModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: ['url' => $model->url]
        );
    }

    /**
     * SocialMediaLink removing test
     *
     * @return void
     */
    public function testSocialMediaLinkRemove(): void
    {
        $this->modelRemovingTest();
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return SocialMediaLink::where('position', '>', 999)->count()
            ? SocialMediaLink::where('position', '>', 999)->first()
            : SocialMediaLink::factory()->create();
    }
}