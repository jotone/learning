<?php

namespace Database\Factories;

use App\Models\{Course, CourseTestimonial};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseTestimonial>
 */
class CourseTestimonialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseTestimonial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get courses that already have testimonial
        $used_courses = $this->model::pluck('course_id')->toArray();
        // Search course without testimonial
        $course = Course::whereNotIn('id', $used_courses)->count()
            ? Course::whereNotIn('id', $used_courses)->inRandomOrder()->first()
            : Course::factory()->create();

        return [
            'course_id' => $course->id,
            'enable' => mt_rand(0, 1),
            'min_progress' => mt_rand(0, 100),
            'show_on_skip' => mt_rand(0, 1),
            'testimonial_text' => $this->faker->text(mt_rand(20, 80)),
            'description_enable' => mt_rand(0, 1),
            'description_required' => mt_rand(0, 1),
            'video_enable' => mt_rand(0, 1),
            'video_required' => mt_rand(0, 1),
            'lessons_description_enable' => mt_rand(0, 1),
            'lessons_description_required' => mt_rand(0, 1),
            'lessons_description_text' => $this->faker->text(mt_rand(20, 80)),
            'lessons_video_enable' => mt_rand(0, 1),
            'lessons_video_required' => mt_rand(0, 1),
        ];
    }
}