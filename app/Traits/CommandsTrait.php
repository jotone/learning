<?php

namespace App\Traits;

use App\Models\{AdminMenu, Permission, Role};

trait CommandsTrait
{
    /**
     * Process admin menu data
     *
     * @param array $data
     * @param bool $top
     * @param int $position
     * @param null $parent_id
     */
    protected function createAdminMenuItem(array $data, bool $top, int $position, $parent_id = null): void
    {
        $menu = AdminMenu::create([
            'name' => $data['name'],
            'route' => $data['route'],
            'img' => $data['img'] ?? null,
            'parent_id' => $parent_id,
            'position' => $position,
            'is_top' => $top
        ]);

        if (!empty($data['inner'])) {
            foreach ($data['inner'] as $position => $item) {
                $this->createAdminMenuItem($item, false, $position, $menu->id);
            }
        }
    }

    /**
     * Process front menu data
     * @param int $position
     * @param array $data
     * @param int $parent_id
     *
     * protected function createFrontMenuItem(int $position, array $data, int $parent_id = 0)
     * {
     * $menu = FrontMenu::create([
     * 'title'     => $data['title'],
     * 'route'     => $data['route'],
     * 'position'  => $position,
     * 'parent_id' => $parent_id,
     * 'img_url'   => $data['img_url'] ?? '',
     * 'enabled'   => $data['enabled'] ?? true
     * ]);
     *
     * if (!empty($data['inner'])) {
     * foreach ($data['inner'] as $position => $item) {
     * $this->createFrontMenuItem($position, $item, $menu->id);
     * }
     * }
     * }*/

    /**
     * Create admin menu
     *
     * @param $files
     * @return void
     */
    protected function installAdminMenu($files): void
    {
        $this->runWithTimer('Dashboard side menu', function () use ($files) {
            foreach ($files['admin_menu'] as $top => $menu) {
                foreach ($menu as $position => $item) {
                    $this->createAdminMenuItem($item, $top === 'top', $position);
                }
            }
        });
    }

    /**
     * Run function with microseconds timer
     *
     * @param string $message
     * @param callable $callback
     * @return mixed
     */
    protected function runWithTimer(string $message, callable $callback): mixed
    {
        // Get current timestamp
        $timestamp = microtime(true);
        // Show console message
        $this->line('<fg=yellow>Creating:</> ' . $message);
        // Run routine
        $result = $callback();
        // Show the complete message
        $this->line('<fg=green>Created:</> ' . $message . ' (' . number_format((microtime(true) - $timestamp) * 1000, 2) . 'ms)');

        return $result;
    }
}
