<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Course::factory(mt_rand(3, 5))->create();
    }
}