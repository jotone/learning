<?php

namespace Database\Factories;

use App\Models\{Category, Course};
use App\Traits\FakerFactoryTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    use FakerFactoryTrait;

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->city;

        return [
            'name' => $name,
            'url' => generateUrl($name) . '-' . uniqid(),
            'img_url' => $this->imageUrl(5),
            'description' => fake()->text(mt_rand(40, 150)),
            'learn_more_link' => mt_rand(0, 1) ? fake()->url : null,
            'position' => $this->getPosition(),
            'type' => Course::class
        ];
    }
}
