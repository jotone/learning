<?php

namespace Database\Factories;

use App\Models\SocialMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    protected $model = SocialMedia::class;

    protected static int $position = -1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (self::$position < 0) {
            self::$position = $this->model::count();
        }

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
            'position' => self::$position
        ];
    }
}