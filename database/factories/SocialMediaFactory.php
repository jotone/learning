<?php

namespace Database\Factories;

use App\Models\SocialMedia;
use App\Traits\FakerFactoryTrait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    use FakerFactoryTrait;

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = SocialMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'facebook' => 'https://facebook/.com',
            'instagram' => 'https://www.instagram.com',
            'linkedin' => 'https://www.linkedin.com',
            'tiktok' => 'https://www.tiktok.com',
            'youtube' => 'https:/youtube.com',
        ];

        $type = Arr::random(array_keys($types));

        return [
            'type' => $type,
            'caption' => ucfirst($type) . ' in ' . fake()->country,
            'link' => $types[$type],
            'icon' => $type . '-icon',
            'position' => $this->getPosition()
        ];
    }
}