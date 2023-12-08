<?php

namespace App\Traits;

use App\Classes\FileHelper;
use App\Models\Settings;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;
use function PHPUnit\Framework\returnArgument;

trait ModelTrait
{
    /**
     * Store image
     *
     * @param null|string|UploadedFile $value
     * @param string $entity
     * @param string $key
     * @return ?string
     */
    private function saveImage(null|string|UploadedFile $value, string $entity, string $key = ''): ?string
    {
        if ($value instanceof UploadedFile) {
            $folder = Str::finish('images/' . $entity . '/' . $this->getEntityId(), '/');
            $img_url = FileHelper::saveFile($value, $folder);

            if (!empty($key)) {
                $this->treatImage($key, $folder, $img_url);
            }

            return $img_url;
        } else {
            return $value;
        }
    }

    /**
     * Set thumbnail image
     *
     * @param string $type The type of thumbnail
     * @param array $path_info The path info of the image
     * @param array &$data The data to store the thumbnail image path
     * @return void
     */
    private function setThumbs(string $type, array $path_info, array &$data): void
    {
        $image = Str::finish($path_info['dirname'], '/') .
            Str::finish('thumb_' . $type, '/') .
            $path_info['basename'];

        if (is_file(public_path($image))) {
            $data[$type] = $image;
        }
    }

    /**
     * Get thumbnails of an image
     *
     * @param ?string $value The path or URL of the original image
     * @return array An array containing the URLs of the original image and its thumbnails
     */
    private function getThumbs(?string $value): array
    {
        if (!empty($value)) {
            $data = [
                'original' => $value
            ];
            if (!filter_var($value, FILTER_VALIDATE_URL) && is_file(public_path($value))) {
                $path_info = pathinfo($value);
                // Set large thumb image
                $this->setThumbs('large', $path_info, $data);
                // Set small thumb image
                $this->setThumbs('small', $path_info, $data);
            }

            return $data;
        } else {
            return [];
        }
    }

    /**
     * Get the entity ID
     *
     * @return int
     */
    private function getEntityId(): int
    {
        return $this->attributes['id'] ?? (1 + DB::table($this->getTable())->orderBy('id', 'desc')->value('id'));
    }

    /**
     * Resize image and/or create thumbs
     *
     * @param string $settingsKey
     * @param string $folder
     * @param string $img_url
     * @return void
     */
    public function treatImage(string $settingsKey, string $folder, string $img_url): void
    {
        $settings = Settings::where('key', $settingsKey)->first();

        foreach ($settings->val as $type => $dimensions) {
            if ($type === 'resize') {
                ImageManagerStatic::make(public_path($img_url))
                    ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                    ->save();
            } else {
                $dest = FileHelper::createFolder(public_path($folder . Str::finish($type, '/')));
                ImageManagerStatic::make(public_path($img_url))
                    ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                    ->save($dest . pathinfo($img_url, PATHINFO_BASENAME));
            }
        }
    }

    /**
     * Model "status" field getter/setter
     *
     * @param array $statuses
     * @return Attribute
     */
    protected function treatStatusField(array $statuses = []): Attribute
    {
        return Attribute::make(
            get: fn(?int $val) => $statuses[$val],
            set: function (int|string $val) use ($statuses) {
                $flipped_statuses = array_flip($statuses);
                $val = mb_strtolower($val);
                if (
                    (is_numeric($val) && !isset($statuses[$val]))
                    || (!is_numeric($val) && is_string($val) && !isset($flipped_statuses[$val]))
                ) {
                    throw new \Exception('Unknown status ' . $val);
                }

                return is_numeric($val) ? $val : $flipped_statuses[$val];
            }
        );
    }
}