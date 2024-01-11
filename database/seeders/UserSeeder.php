<?php

namespace Database\Seeders;

use App\Models\{Role, User};
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $roles = Role::whereIn('slug', ['admin', 'coach', 'student'])->pluck('id', 'slug');
        // Seed students
        User::factory(50)->create(['role_id' => $roles['student']]);
        // Seed coaches
        User::factory(5)->create(['role_id' => $roles['coach']]);
        // Seed admins
        User::factory(2)->create(['role_id' => $roles['admin']]);
    }
}