<?php

namespace Database\Factories;

use App\Services\Str;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Role::class;

    public function definition(): array
    {
        $name = fake()->jobTitle . ' in ' . fake()->company;
        // Generate unique slug
        $slug = Str::generateUrl($name);
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
