<?php

namespace Database\Factories;

use App\Classes\Str;
use App\Models\Course;
use App\Traits\FakerFactoryTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    use FakerFactoryTrait;

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company . ' of ' . fake()->lastName;

        $admin = $this->getAdmin();

        $free_trial_enable = mt_rand(0, 1);

        return [
            'name' => $name,
            'url' => Str::generateUrl($name),
            'description' => fake()->text(mt_rand(40, 150)),
            'img_url' => $this->imageUrl(),
            'invitation_email' => 0,
            'position' => $this->getPosition(),
            'status' => 'active',
            'tracking_type' => 'enable_for_every_submission',
            'tracking_status' => mt_rand(0, 12),
            'instructor_id' => $admin->id ?? null,
            'terms_conditions_enable' => 1,
            'terms_conditions_text' => fake()->text(mt_rand(40, 150)),
            'free_trial_enable' => $free_trial_enable,
            'free_trial_upgrade_url' => $free_trial_enable ? fake()->url : null,
            'free_trial_upgrade_title' => $free_trial_enable ? $name . ' Free Trial' : null
        ];
    }
}
