<?php

namespace App\Traits;

trait PermissionListTrait
{
    /**
     * Get list of files
     *
     * @param array $path_list
     * @return array
     * @throws \Exception
     */
    protected function permissionList(array $path_list): array
    {
        $result = [];
        foreach ($path_list as $path) {
            if (!file_exists($path)) {
                throw new \Exception('Folder "' . $path . '" does not exists.');
            }
            $files = new \DirectoryIterator($path);

            foreach ($files as $file) {
                if (!$file->isDot()) {
                    // File basename
                    $path = ucfirst(substr($file->getPath(), strlen(base_path()) + 1)) . '/' . pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    // Class name
                    $class_name = preg_replace('/\//', '\\', $path);
                    $result[] = [
                        'file' => str_replace('App\Http\Controllers\\', '', $class_name),
                        'class' => $class_name,
                        'methods' => (function ($methods = []) use ($class_name) {
                            foreach ((new \ReflectionClass($class_name))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                                if ($method->class == $class_name) {
                                    $methods[] = $method->name;
                                }
                            }
                            return $methods;
                        })()
                    ];
                }
            }
        }
        return $result;
    }
}