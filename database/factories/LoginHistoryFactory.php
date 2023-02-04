<?php

namespace Database\Factories;

use App\Models\{LoginHistory, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginHistory>
 */
class LoginHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoginHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::whereHas('role', fn ($q) => $q->where('level', '>=', 127))->inRandomOrder()->first();
        return [
            'user_id' => $user->id,
            'ip' => fake()->ipv4,
            'user_agent' => fake()->userAgent
        ];
    }
}
