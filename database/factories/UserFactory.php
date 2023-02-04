<?php

namespace Database\Factories;

use App\Models\{Role, User};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\{Arr, Str};

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_days = mt_rand(1, 5);

        $dims = [200, 240, 280, 320, 480, 640];

        $role = Role::where('level', '>=', 127)->inRandomOrder()->first();

        if (!$role) {
            $role = Role::factory()->create();
        }

        return [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'img_url' => 'https://picsum.photos/' . Arr::random($dims) . '/' . Arr::random($dims),
            'about' => fake()->realText(150),
            'status' => 0,
            'activated_at' => now(),
            'last_activity' => now(),
            'role_id' => $role->id,
            'created_at' => now()->subDays($created_days)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
