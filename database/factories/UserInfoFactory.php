<?php

namespace Database\Factories;

use App\Models\{User, UserInfo};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserInfo>
 */
class UserInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $size = array_rand(config('enums.user.shirt_sizes'));

        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'timezone' => fake()->timezone,
            'country' => fake()->country,
            'city' => fake()->city,
            'state_region' => fake()->word,
            'address' => fake()->address,
            'extended_address' => fake()->buildingNumber,
            'zip' => fake()->postcode,
            'phone' => fake()->e164PhoneNumber,
            'shirt_size' => $size
        ];
    }
}
