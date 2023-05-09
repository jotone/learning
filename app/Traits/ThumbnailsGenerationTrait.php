<?php

namespace App\Traits;

use App\Classes\FileHelper;
use App\Models\Settings;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;

trait ThumbnailsGenerationTrait
{
    /**
     * Get image thumbs
     *
     * @return array
     */
    public function getThumbnailsAttribute(): array
    {
        if (empty($this->attributes['img_url'])) {
            return [];
        }
        // get the original image
        $result = [
            'original' => $this->attributes['img_url']
        ];
        if (str_starts_with($this->attributes['img_url'], 'http')) {
            return $result;
        }
        // Get user's thumbnails folder
        $folder = $this->userThumbsFolder();
        $related_path = str_replace(public_path(''), '', $folder);
        // Add thumbs to result
        foreach (glob($folder . '*' . pathinfo($this->attributes['img_url'], PATHINFO_BASENAME)) as $img) {
            $filename = substr($img, strrpos($img, '/') + 1);
            if (str_starts_with($filename, 'thumb_large')) {
                $result['large'] = $related_path . $filename;
            } elseif (str_starts_with($filename, 'thumb_small')) {
                $result['small'] = $related_path . $filename;
            }
        }

        return $result;
    }

    /**
     * Create image thumbs
     *
     * @param $value
     * @return void
     */
    public function setImgUrlAttribute($value): void
    {
        // Save image
        $this->attributes['img_url'] = $value;
        // Check if settings setup key exists
        if (isset($this->thumbnail) && isset($this->attributes['id']) && !str_starts_with('http', $value)) {
            // Get thumbnails settings
            $settings = Settings::where('key', $this->thumbnail['key'])->first();
            // Get user's thumbnails folder
            $folder = $this->userThumbsFolder();
            // Get image ext
            $filename = pathinfo($value, PATHINFO_BASENAME);

            foreach ($settings->val as $type => $dimensions) {
                if ($type === 'resize') {
                    ImageManagerStatic::make(public_path($value))
                        ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                        ->save();
                } else {
                    ImageManagerStatic::make(public_path($value))
                        ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                        ->save($folder . $type . $filename);
                }
            }
        }
    }

    /**
     * Get user's folder
     *
     * @return string
     */
    private function userThumbsFolder(): string
    {
        return FileHelper::createFolder(
            public_path(Str::finish($this->thumbnail['folder'], '/') . $this->attributes['id'] . '/thumbs/')
        );
    }
}