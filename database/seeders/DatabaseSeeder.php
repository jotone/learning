<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 0, $n = 5; $i < $n; $i++) {
            User::factory()->create()->toArray();
        }
    }
}
