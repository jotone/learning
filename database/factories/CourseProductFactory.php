<?php

namespace Database\Factories;

use App\Models\{Course, CourseProduct};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CourseProduct>
 */
class CourseProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = CourseProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::count() ? Course::inRandomOrder()->first() : Course::factory()->create();

        return [
            'course_id' => $course->id,
            'product' => Str::random(),
            'driver' => 'test'
        ];
    }
}
