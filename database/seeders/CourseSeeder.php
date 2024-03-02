<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $courses_count = mt_rand(3, 7);
        Course::factory($courses_count)->create();

        $categories = Category::factory(mt_rand(1, 2))->create();

        foreach ($categories as $category) {
            if ($courses_count > 0) {
                $courses = Course::whereNull('category_id')->inRandomOrder()->take(mt_rand(0, $courses_count - 1))->get();

                $courses_count -= $courses->count();

                $courses->each(fn($course) => $course->update(['category_id' => $category->id]));

                if ($courses_count < 1) {
                    $courses_count = 0;
                }
            }
        }
    }
}