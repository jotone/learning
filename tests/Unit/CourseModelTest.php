<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\CourseInfo;
use App\Models\CourseProduct;
use App\Models\CourseTestimonial;
use Illuminate\Database\Eloquent\Model;
use Tests\ModelTestCase;

class CourseModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Course::class;
    }

    /**
     * Course creating test
     *
     * @return void
     */
    public function testCourseCreate(): void
    {
        $this->modelCreatingTest();
    }

    /**
     * Course updating test
     *
     * @return void
     */
    public function testCourseModify(): void
    {
        $model = self::$class::factory()->make();

        $this->modelModifyingTest(
            values: [
                'name' => $model->name,
                'url' => $model->url
            ]
        );
    }

    /**
     * Test relation between Course and CourseInfo model
     *
     * @return void
     */
    public function testCourseToInfoRelation(): void
    {
        $model = Course::factory()->create();
        $info = CourseInfo::factory()->create([
            'course_id' => $model->id
        ]);

        $this->assertTrue($model->info->id === $info->id);
        $this->assertTrue($model->id === $info->course->id);
    }

    /**
     * Test relation between Course and CourseTestimonial model
     *
     * @return void
     */
    public function testCourseToTestimonialRelation(): void
    {
        $model = Course::factory()->create();
        $testimonial = CourseTestimonial::factory()->create([
            'course_id' => $model->id
        ]);

        $this->assertTrue($model->testimonial->id === $testimonial->id);
        $this->assertTrue($model->id === $testimonial->course->id);
    }

    /**
     * Test Course to CourseProduct relation
     *
     * @return void
     */
    public function testCourseToProductsRelation(): void
    {
        $model = Course::factory()->create();
        $products = CourseProduct::factory(mt_rand(1, 10))->create([
            'course_id' => $model->id
        ]);

        $this->assertEmpty(array_diff($model->products()->pluck('id')->toArray(), $products->pluck('id')->toArray()));

        $this->assertEmpty(array_diff($products->pluck('id')->toArray(), $model->products()->pluck('id')->toArray()));

        foreach ($products as $product) {
            $this->assertTrue($product->course_id === $model->id);
        }
    }

    /**
     * Course removing test
     *
     * @return void
     */
    public function testCourseRemove(): void
    {
        $this->modelRemovingTest();
    }

    /**
     * @return Model
     */
    protected static function getModel(): Model
    {
        return Course::count() ? Course::first() : Course::factory()->create();
    }
}