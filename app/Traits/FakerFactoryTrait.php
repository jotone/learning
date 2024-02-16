<?php

namespace App\Traits;

use App\Models\{Role, User};
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait FakerFactoryTrait
{
    /**
     * Model position generator
     * @var int
     */
    protected static int $position = -1;

    /**
     * Generate image url or file or null
     *
     * @param int $precision
     * @return File|string|null
     */
    protected function image(int $precision = 2): File|string|null
    {
        if ($precision < 1) {
            $precision = 1;
        }
        $rand = mt_rand(0, $precision);
        $img = null;
        if ($rand === 0) {
            $dims = [320, 400, 480, 560, 640];
            $img = 'https://picsum.photos/' . Arr::random($dims) . '/' . Arr::random($dims);
        } elseif ($rand === 1) {
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

    /**
     * Get the nested element position
     * @return int
     */
    protected function getPosition(): int
    {
        if (self::$position < 0) {
            self::$position = $this->model::count();
            return self::$position;
        }

        return ++self::$position;
    }
}