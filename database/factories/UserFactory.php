<?php

namespace Database\Factories;

use App\Enums\ShirtSize;
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

    /**
     * The name of the factory's corresponding model.
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
            'img_url' => $this->imageUrl(),
            'about' => fake()->realText(150),
            'status' => 'active',
            'activated_at' => now(),
            'last_activity' => now(),
            'role_id' => $role->id,
            'created_at' => now()->subDays($created_days),
            'timezone' => fake()->timezone,
            'country' => fake()->country,
            'region' => fake()->word,
            'city' => fake()->city,
            'address' => preg_replace('/\n/', '', fake()->address),
            'ext_addr' => fake()->buildingNumber,
            'zip' => fake()->postcode,
            'phone' => fake()->e164PhoneNumber,
            'shirt_size' => Arr::random(ShirtSize::cases())->name
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

    public function admin(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => Role::where('level', '<', 127)->where('level', '>', 0)->inRandomOrder()->value('id')
        ]);
    }

    public function student(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => Role::where('level', 255)->value('id')
        ]);
    }
}
