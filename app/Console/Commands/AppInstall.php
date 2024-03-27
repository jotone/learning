<?php

namespace App\Console\Commands;

use App\Models\{AdminMenu, EmailTemplate, PageColumn, PageColumnSection, Permission, Role, Settings, SocialMedia, User};
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
     * Superuser data
     * @var array
     */
    protected array $superuser = [
        'name' => 'Superuser',
        'email' => 'superadmin@mail.com'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->components->info('Running server installation.');

        $files = $this->installationFiles();

        // Install roles
        $this->runWithTimer('Creating user roles', fn() => $this->installRoles($files['roles']));

        // Install superuser and admin accounts
        $this->runWithTimer('Creating superuser account', fn() => $this->installSuperuser());

        // Install email templates
        $this->runWithTimer('Installing email templates', function () use ($files) {
            foreach ($files['email_templates'] as $template) {
                EmailTemplate::create($template);
            }
        });

        // Install pages column settings
        $this->runWithTimer('Installing pages column settings', function () use ($files) {
            $this->installPageColumns($files['columns']);
        });

        // Install settings
        $this->runWithTimer('Installing settings', function () use ($files) {
            // Install main setting
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
            // Install social media
            $this->installSocialMedia($files['social_media']);
        });

        // Installing side menu
        $this->runWithTimer('Creating dashboard side menu', fn() => $this->installAdminMenu($files['admin_menu']));
    }

    /**
     * Create admin menu
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
     * Install page column structure
     * @param array $columns
     * @return void
     */
    public function installPageColumns(array $columns): void
    {
        foreach ($columns as $page => $section) {
            $pos = 0;
            $table_pos = 0;
            foreach ($section as $slug => $section_data) {
                $page_section = PageColumnSection::create([
                    'name' => $section_data['name'],
                    'slug' => $slug,
                    'page' => $page,
                    'icon' => $section_data['img'] ?? null,
                    'position' => $pos
                ]);
                foreach ($section_data['items'] as $i => $data) {
                    PageColumn::create(array_merge($data, [
                        'section_id' => $page_section->id,
                        'position' => $i,
                        'table_position' => $table_pos
                    ]));
                    $table_pos++;
                }
                $pos++;
            }
        }
    }

    /**
     * Install roles
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
     * Continue asking user to enter the right password until it's done
     * @return string
     */
    protected function recursiveAskPassword(): string
    {
        $password = $this->ask('The Superuser password should be more than 8 characters. Please enter a new Superuser password');

        return empty($password) || strlen($password) < 8
            ? $this->recursiveAskPassword()
            : $password;
    }

    /**
     * Create a superuser with its role
     * @return void
     */
    protected function installSuperuser(): void
    {
        // Get a password for the superuser
        if (empty(config('app.superuser_pwd'))) {
            $password = $this->ask('The Superuser password is not set. Please enter a new Superuser password');

            if (empty($password) || strlen($password) < 8) {
                $password = $this->recursiveAskPassword();
            }
        } else {
            $password = config('app.superuser_pwd');
        }

        // Get a superuser role
        $role = Role::where('slug', 'superuser')->value('id');

        // Create a superuser entity
        User::whereHas('role', fn($q) => $q->where('level', '<', 1))->count()
            ? User::whereHas('role', fn($q) => $q->firstWhere('level', '<', 1))
            : User::create([
            'first_name' => $this->superuser['name'],
            'email' => $this->superuser['email'],
            'email_verified_at' => now(),
            'password' => $password,
            'activated_at' => now(),
            'role_id' => $role,
            'status' => 'active'
        ]);

        // Set the permissions for the superuser
        $folders = [
            app_path('GraphQL/Schemas'),
            app_path('Http/Controllers/Api'),
            app_path('Http/Controllers/Dashboard'),
        ];
        foreach ($folders as $folder) {
            $files = new \DirectoryIterator($folder);

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $contents = file_get_contents($file->getRealPath());

                    // Find the namespace
                    if (preg_match('/namespace\s+([^;]+);/', $contents, $matches)) {
                        // The namespace is in the first capture group
                        $namespace = trim($matches[1]);
                        $controller = $namespace . '\\' . str_replace('.php', '', $file->getFileName());

                        if (str_contains($folder, 'GraphQL/Schemas')) {
                            $controller_config = (new $controller())->toConfig();
                            $methods = array_merge(['query'], array_keys($controller_config['mutation']));
                        } else {
                            $methods = (function ($methods = []) use ($controller) {
                                foreach ((new \ReflectionClass($controller))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                                    if ($method->class == $controller && $method->name !== '__construct') {
                                        $methods[] = $method->name;
                                    }
                                }
                                return $methods;
                            })();
                        }

                        Permission::create([
                            'role_id' => $role,
                            'controller' => $controller,
                            'allowed_methods' => $methods
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Create a list of social media
     *
     * @param array $list
     * @return void
     */
    protected function installSocialMedia(array $list): void
    {
        foreach ($list as $i => $item) {
            SocialMedia::create(array_merge($item, [
                'position' => $i
            ]));
        }
    }

    /**
     * Run function with microsecond timer
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
