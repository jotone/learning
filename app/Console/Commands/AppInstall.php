<?php

namespace App\Console\Commands;

use App\Models\{AdminMenu, Permission, Role, Settings, User};
use Illuminate\Console\Command;

class AppInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will install the basic site data.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->components->info('Running server installation.');

        $files = $this->installationFiles();

        // Install roles
        $this->runWithTimer('Creating user roles', function () use ($files) {
            $this->installRoles($files['roles']);
        });

        // Install superuser and admin accounts
        $this->runWithTimer(
            'Creating superuser account',
            fn() => User::whereHas('role', fn($q) => $q->where('level', '<', 1))->count()
                ? User::whereHas('role', fn($q) => $q->firstWhere('level', '<', 1))
                : User::create([
                    'first_name' => 'Superuser',
                    'email' => 'superadmin@mail.com',
                    'email_verified_at' => now(),
                    'password' => base64_decode('OFNUUFQwbUJCMDZnXkV1Mg=='),
                    'activated_at' => now(),
                    'role_id' => Role::firstWhere('level', '<', 1)->id,
                    'status' => 'active'
                ])
        );

        // Install settings
        $this->runWithTimer('Installing settings', function () use ($files) {
            foreach ($files['settings'] as $section => $settings) {
                foreach ($settings as $setting) {
                    if ($setting['key'] == 'sign_up_date') {
                        $setting['value'] = now();
                    }
                    Settings::create([
                        'key' => $setting['key'],
                        'value' => $setting['value'],
                        'section' => $section,
                        'extra_data' => $setting['extra_data'] ?? '',
                    ]);
                }
            }
        });

        // Installing side menu
        $this->runWithTimer('Creating dashboard side menu', fn() => $this->installAdminMenu($files['admin_menu']));
    }

    /**
     * Create admin menu
     *
     * @param array $admin_menu
     * @return void
     */
    protected function installAdminMenu(array $admin_menu): void
    {
        foreach ($admin_menu as $section => $menu) {
            foreach ($menu as $position => $item) {
                $this->createAdminMenuItem($item, $section, $position);
            }
        }
    }

    /**
     * Process admin menu data
     *
     * @param array $data
     * @param int $section
     * @param int $position
     * @param null $parent_id
     */
    protected function createAdminMenuItem(array $data, int $section, int $position, $parent_id = null): void
    {
        $route_params = (
            str_starts_with($data['route'], 'dashboard.users.')
            || str_starts_with($data['route'], 'dashboard.courses.')
            || str_starts_with($data['route'], 'dashboard.communities.')
            || str_starts_with($data['route'], 'dashboard.refferals.')
            || str_starts_with($data['route'], 'dashboard.testimonials.')
        ) && !str_ends_with($data['route'], '.index')
            ? ':id'
            : [];

        $menu = AdminMenu::create([
            'name' => $data['name'],
            'route' => str_starts_with($data['route'], 'http') || $data['route'] == '#'
                ? $data['route']
                : route($data['route'], $route_params, false),
            'img' => $data['img'] ?? null,
            'parent_id' => $parent_id,
            'position' => $position,
            'section' => $section
        ]);

        if (!empty($data['inner'])) {
            foreach ($data['inner'] as $position => $item) {
                $this->createAdminMenuItem($item, $section, $position, $menu->id);
            }
        }
    }

    /**
     * Get installation files data
     *
     * @return array
     */
    protected function installationFiles(): array
    {
        $install_path = base_path('app/Console/Commands/InstallationData/');

        $files = [];

        foreach (glob($install_path . '*.json') as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            try {
                $files[$filename] = json_decode(file_get_contents($file), 1);
            } catch (\Exception $exception) {
                $this->error('Error occurred while ' . $filename . ' file reading. (' . $file . ')');
                $this->error($exception->getMessage());
            }
        }

        return $files;
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
}
