<?php

namespace App\Services;

use DirectoryIterator;
use Illuminate\Http\UploadedFile;

class FileHelper
{
    /**
     * Create folder if not exists
     *
     * @param string $folder
     * @return string
     */
    public static function createFolder(string $folder): string
    {
        // Check folder exists
        !is_dir($folder) && mkdir($folder, 0775, true);

        return $folder;
    }

    /**
     * Save $file to $path directory
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    public static function saveFile(UploadedFile $file, string $path): string
    {
        $path = FileHelper::createFolder(public_path($path));
        $filename = $file->getClientOriginalName();
        $file_info = pathinfo($filename);

        $ext = match ($file->getClientMimeType()) {
            'image/apng' => 'apng',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => $file_info['extension'],
        };

        $full_path = Str::finish($path, '/') . $filename;
        if (file_exists($full_path)) {
            $filename = sprintf('%s-%s.%s', $file_info['filename'], uniqid(), $ext);
            $full_path = Str::finish($path, '/') . $filename;
        }

        $file->move($path, $filename);

        return substr($full_path, strlen(public_path()));
    }

    /**
     * Recursive remove folder
     *
     * @param string $path
     */
    public static function recursiveRemove(string $path): void
    {
        if (file_exists($path)) {
            if (is_file($path)) {
                unlink($path);
            } else {
                $files = new DirectoryIterator($path);

                foreach ($files as $file) {
                    if (!$file->isDot()) {
                        if ($file->isDir()) {
                            self::recursiveRemove($file->getPathname());
                        } else {
                            unlink($file->getPathname());
                        }
                    }
                }

                rmdir($path);
            }
        }
    }
}