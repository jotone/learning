<?php

namespace Database\Factories;

use App\Models\{Role, User};
use App\Traits\FakerFactoryTrait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\{Arr, Str};

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    use FakerFactoryTrait;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_days = mt_rand(1, 5);

        $role = Role::where('level', '>', 127)->inRandomOrder()->first();

        if (!$role) {
            $role = Role::factory()->create();
        }

        return [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => uniqid() . fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'img_url' => $this->image(),
            'about' => fake()->realText(150),
            'status' => 'active',
            'activated_at' => now(),
            'last_activity' => now(),
            'role_id' => $role->id,
            'created_at' => now()->subDays($created_days),
            'timezone' => fake()->timezone,
            'country' => fake()->country,
            'city' => fake()->city,
            'region' => fake()->word,
            'address' => fake()->address,
            'ext_addr' => fake()->buildingNumber,
            'zip' => fake()->postcode,
            'phone' => fake()->e164PhoneNumber,
            'shirt_size' => Arr::random(config('enums.user.shirt_sizes'))
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
