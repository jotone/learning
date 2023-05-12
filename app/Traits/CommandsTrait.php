<?php

namespace App\Traits;

use App\Models\AdminMenu;

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
            'route' => str_starts_with($data['route'], 'http') || $data['route'] == '#'
                ? $data['route']
                : route($data['route'], [], false),
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
     * Create admin menu
     *
     * @param array $admin_menu
     * @return void
     */
    protected function installAdminMenu(array $admin_menu): void
    {
        foreach ($admin_menu as $top => $menu) {
            foreach ($menu as $position => $item) {
                $this->createAdminMenuItem($item, $top === 'top', $position);
            }
        }
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
        // Run routine
        $result = $callback();
        // Execution time
        $timestamp = number_format((microtime(true) - $timestamp) * 1000);
        // Show the console message
        $this->components->twoColumnDetail($message, '<fg=gray>' . $timestamp . 'ms</> <fg=green>DONE</>');

        return $result;
    }
}
