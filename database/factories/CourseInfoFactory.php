<?php

namespace Database\Factories;

use App\Models\{Course, CourseInfo};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseInfo>
 */
class CourseInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get courses that already have info
        $used_courses = $this->model::pluck('course_id')->toArray();
        // Search course without info
        $course = Course::whereNotIn('id', $used_courses)->count()
            ? Course::whereNotIn('id', $used_courses)->inRandomOrder()->first()
            : Course::factory()->create();

        $enable_free_trial = mt_rand(0, 1);

        return [
            'course_id' => $course->id,
            'enable_terms_conditions' => 1,
            'terms_conditions_text' => $this->faker->text(mt_rand(40, 150)),
            'enable_free_trial' => $enable_free_trial,
            'free_trial_upgrade_url' => $enable_free_trial ? $this->faker->url : null,
            'free_trial_upgrade_title' => $enable_free_trial ? $course->name . ' Free Trial' : null
        ];
    }
}
