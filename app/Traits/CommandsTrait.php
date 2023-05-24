<?php

namespace App\Traits;

use App\Models\{AdminMenu, Permission, Role};
use Illuminate\Database\Eloquent\Collection;

trait CommandsTrait
{
    /**
     * Get installation files data
     *
     * @return array
     */
    protected function installationFiles(): array
    {
        $install_path = base_path('app/Console/Commands/InstallationData/');

        $files = [
            'admin_menu' => [],
            'email_templates' => [],
            'lang_en' => [],
            'lang_de' => [],
            'roles' => [],
            'settings' => []
        ];

        foreach ($files as $file => $data) {
            try {
                $files[$file] = json_decode(file_get_contents($install_path . $file . '.json'), 1);
            } catch (\Exception $exception) {
                $this->error('Error occurred while ' . $file . ' file reading. (app/Console/Commands/AppInstallData/' . $file . '.json)');
                $this->error($exception->getMessage());
            }
        }

        return $files;
    }

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
     * Install roles
     *
     * @param array $roles
     * @return array
     */
    protected function installRoles(array $roles): array
    {
        $result = [];
        foreach ($roles as $slug => $data) {
            $role = Role::firstOrCreate([
                'name' => $data['name'],
                'slug' => $slug,
                'level' => $data['level']
            ]);

            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $controller => $methods) {
                    Permission::create([
                        'role_id' => $role->id,
                        'controller' => $controller,
                        'allowed_methods' => $methods
                    ]);
                }
            }
            $result[$role->slug] = $role->id;
        }
        return $result;
    }

    /**
     * Install super user permissions
     *
     * @return Collection
     * @throws \Exception
     */
    protected function installSuperUser(): Collection
    {
        $super_user_roles = Role::where('level', '0')->get();

        $this->runWithTimer('Binding permissions to roles', function () use ($super_user_roles) {
            $permission_list = $this->permissionList([
                app_path('Http/Controllers/Api/'),
                app_path('Http/Controllers/Dashboard/')
            ]);

            foreach ($super_user_roles as $role) {
                foreach ($permission_list as $permission) {
                    Permission::create([
                        'role_id' => $role->id,
                        'controller' => $permission['class'],
                        'allowed_methods' => $permission['methods']
                    ]);
                }
            }
        });

        return $super_user_roles;
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
