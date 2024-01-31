<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Mail to ' . fake()->jobTitle . ' in ' . fake()->city;
        return [
            'title' => $title,
            'slug' => generateUrl($title) . uniqid(),
            'subject' => fake()->sentence,
            'body' => [sprintf('<td style="font-size: 14px; line-height: 18px; text-align: center">%s</td>', fake()->realText(mt_rand(10, 40)))]
        ];
    }
}
