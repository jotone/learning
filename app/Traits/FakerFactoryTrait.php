<?php

namespace App\Traits;

use App\Models\{Role, User};
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait FakerFactoryTrait
{
    /**
     * Generate image url or file or null
     *
     * @return File|string|null
     */
    protected function image(): File|string|null
    {
        $rand = mt_rand(0, 2);
        $img = null;
        if ($rand === 0) {
            $dims = [320, 400, 480, 560, 640];
            $img = 'https://picsum.photos/' . Arr::random($dims) . '/' . Arr::random($dims);
        } else if ($rand === 1) {
            $img = UploadedFile::fake()->image(uniqid() . '.jpg');
        }

        return $img;
    }

    /**
     * Get user with admin or coach role
     *
     * @return User|null
     */
    protected function getAdmin(): ?User
    {
        return User::whereIn('role_id', Role::whereIn('slug', ['coach', 'admin'])->pluck('id')->toArray())
            ->inRandomOrder()
            ->first();
    }
}