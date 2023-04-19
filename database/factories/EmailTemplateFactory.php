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
     *
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
        $name = 'Email of the ' . fake()->company;
        return [
            'name'      => $name,
            'slug'      => generateUrl($name),
            'subject'   => 'Hello there, %username%',
            'body'      => fake()->text(),
            'variables' => ['%username%' => ['user' => 'full_name']]
        ];
    }
}
