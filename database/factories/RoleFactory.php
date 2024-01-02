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
        // Generate unique slug
        $slug = generateUrl($name);
        if ($this->model::where('slug', $slug)->count()) {
            $slug .= '-' . uniqid();
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'level' => mt_rand(128, 200)
        ];
    }
}
