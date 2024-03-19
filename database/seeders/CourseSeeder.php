<?php

namespace Database\Seeders;

use App\Models\{Category, Course};
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $courses = Course::factory(mt_rand(3, 7))->create();

        foreach ($courses as $course) {
            $categories = Category::inRandomOrder()->take(mt_rand(0, 2))->get();

            if ($categories->count()) {
                $course->categories()->sync($categories->pluck('id')->toArray());
            }
        }
    }
}