<?php

namespace App\Traits;

use App\Classes\FileHelper;
use App\Models\Settings;
use Intervention\Image\ImageManagerStatic;

trait ThumbnailsGenerationTrait
{
    /**
     * Create image thumbs
     *
     * @param $value
     * @return void
     */
    protected function setImgUrlAttribute($value): void
    {
        // Save image
        $this->attributes['img_url'] = $value;
        // Check if settings setup key exists
        if (isset($this->thumbnail_setup) && isset($this->attributes['id'])) {
            // Get thumbnails settings
            $settings = Settings::where('key', $this->thumbnail_setup)->first();
            // Create thumbnails folder
            $folder = public_path('images/users/' . $this->attributes['id'] . '/thumbs/');
            FileHelper::createFolder($folder);
            // Get image ext
            $ext = pathinfo($value, PATHINFO_EXTENSION);

            foreach ($settings->val as $type => $dimensions) {
                if ($type === 'resize') {
                    ImageManagerStatic::make(public_path($value))
                        ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                        ->save();
                } else {
                    ImageManagerStatic::make(public_path($value))
                        ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                        ->save($folder . $type . '.' . $ext);
                }
            }
        }
    }
}