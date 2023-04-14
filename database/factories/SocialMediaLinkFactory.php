<?php

namespace Database\Factories;

use App\Models\SocialMediaLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialMediaLink>
 */
class SocialMediaLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SocialMediaLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'     => 'facebook',
            'url'      => fake()->url,
            'position' => mt_rand(1000, 9999)
        ];
    }
}
