<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseInfo;
use App\Models\CourseProduct;
use App\Models\CourseTestimonial;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0, $n = mt_rand(4, 10); $i < $n; $i++) {
            $course = Course::factory()->create([
                'position' => $i
            ]);

            CourseInfo::factory()->create(['course_id' => $course->id]);
            CourseTestimonial::factory()->create(['course_id' => $course->id]);

            if (mt_rand(0, 1)) {
                CourseProduct::factory(mt_rand(1, 5))->create([
                    'course_id' => $course->id
                ]);
            }
        }
    }
}
