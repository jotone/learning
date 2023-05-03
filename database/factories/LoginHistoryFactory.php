<?php

namespace Database\Factories;

use App\Models\LoginHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Traits\ModelGeneratorsTrait;

/**
 * @extends Factory<LoginHistory>
 */
class LoginHistoryFactory extends Factory
{
    use ModelGeneratorsTrait;

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
        $user = $this->generateUser();
        return [
            'user_id'    => $user->id,
            'ip'         => fake()->ipv4,
            'user_agent' => fake()->userAgent
        ];
    }
}
