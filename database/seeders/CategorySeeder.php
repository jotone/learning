<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Category::factory(mt_rand(1, 2))->create();
    }
}