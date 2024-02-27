<?php

namespace Database\Seeders;

use App\Models\{Course, Role, User};
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
        $students = User::factory(mt_rand(30, 50))->student()->create();
        // Seed coaches
        User::factory(5)->create(['role_id' => $roles['coach']]);
        // Seed admins
        User::factory(2)->create(['role_id' => $roles['admin']]);

        foreach ($students as $student) {
            $courses = Course::inRandomOrder()->take(mt_rand(1, 3))->get();

            foreach ($courses as $course) {
                $enrolled = mt_rand(0, 3) ? null : now()->subWeeks(mt_rand(2, 52));
                $student->courses()->attach($course, [
                    'enrolled_at' => $enrolled,
                    'expires_at' => is_null($enrolled) ? null : now()->addWeeks(mt_rand(2, 52))
                ]);
            }
        }
    }
}