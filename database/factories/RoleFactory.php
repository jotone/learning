<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = fake()->jobTitle;
        return [
            'name'  => $name,
            'slug'  => generateUrl($name),
            'level' => mt_rand(128, 200)
        ];
    }
}
