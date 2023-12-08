<?php

namespace Database\Factories;

use App\Models\Course;
use App\Traits\FakerFactoryTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    use FakerFactoryTrait;

    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company . ' of ' . $this->faker->lastName;

        $admin = $this->getAdmin();

        $free_trial_enable = mt_rand(0, 1);

        return [
            'name' => $name,
            'url' => generateUrl($name),
            'description' => $this->faker->text(mt_rand(40, 150)),
            'img_url' => $this->image(),
            'invitation_email' => 0,
            'position' => $this->model::query()->count(),
            'status' => 'active',
            'tracking_type' => 1,
            'tracking_status' => mt_rand(0, 12),
            'instructor_id' => $admin->id ?? null,
            'terms_conditions_enable' => 1,
            'terms_conditions_text' => $this->faker->text(mt_rand(40, 150)),
            'free_trial_enable' => $free_trial_enable,
            'free_trial_upgrade_url' => $free_trial_enable ? $this->faker->url : null,
            'free_trial_upgrade_title' => $free_trial_enable ? $name . ' Free Trial' : null
        ];
    }
}
