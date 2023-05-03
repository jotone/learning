<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeded extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        User::factory(10)->create();
    }
}