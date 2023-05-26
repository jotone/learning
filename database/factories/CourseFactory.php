<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
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
        $dims = [320, 400, 480, 560, 640];

        $name = $this->faker->company;

        return [
            'name' => $name,
            'url' => generateUrl($name),
            'description' => $this->faker->text(mt_rand(40, 150)),
            'img_url' => 'https://picsum.photos/' . Arr::random($dims) . '/' . Arr::random($dims),
            'invitation_email' => 0,
            'tracking_type' => 1,
            'tracking_status' => mt_rand(0, 12),
            'position' => $this->model::query()->count()
        ];
    }
}
