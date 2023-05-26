<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Support\Arr;
use Tests\ApiTestCase;

class CourseApiTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$class = Course::class;
        self::$route_prefix = 'api.courses.';
    }

    /**
     * Test Course list
     *
     * @return void
     */
    public function testCourseList(): void
    {
        $this->runListTest(function ($content, $models) {
            $item = Arr::random($content->data);
            $this
                // Check total items equals database records
                ->assertDatabaseCount($models[0]->getTable(), $content->meta->total)
                // Check response random item exists on the database
                ->assertDatabaseHas($models[0]->getTable(), [
                    'id' => $item->id,
                    'name' => $item->name,
                    'url' => $item->url,
                    'img_url' => $item->img_url
                ])
                // Check per_page value equals response collection
                ->assertCount($content->meta->per_page, $content->data);
        });
    }
}