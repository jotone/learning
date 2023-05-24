<?php

namespace App\Console\Commands;

use App\Classes\FileHelper;
use App\Models\{EmailTemplate, Settings, User};
use App\Traits\{CommandsTrait, LanguageHelper, PermissionListTrait, SettingsTrait};
use Illuminate\Console\Command;

class AppInstall extends Command
{
    use CommandsTrait, LanguageHelper, PermissionListTrait, SettingsTrait;

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
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->components->info('Running server installation.');

        $files = $this->installationFiles();

        // Install roles
        $this->runWithTimer('Creating user roles', function () use ($files) {
            $this->installRoles($files['roles']);
        });

        // Install super user permissions
        $super_user_roles = $this->installSuperUser();

        $this->runWithTimer('Creating superuser account', function () use ($files, $super_user_roles) {
            return User::whereHas('role', fn($q) => $q->where('level', '<', 1))->count()
                ? User::whereHas('role', fn($q) => $q->firstWhere('level', '<', 1))
                : User::create([
                    'first_name' => 'Superuser',
                    'email' => 'superadmin@mail.com',
                    'email_verified_at' => now(),
                    'password' => base64_decode('U29mdDkzMjg2NA=='),
                    'activated_at' => now(),
                    'role_id' => $super_user_roles[0]->id,
                    'status' => 0
                ]);
        });

        $this->runWithTimer('Creating email templates', function () use ($files) {
            foreach ($files['email_templates'] as $template) {
                EmailTemplate::create($template);
            }
        });

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
                        'about' => $setting['about'] ?? '',
                    ]);
                }
            }
        });

        // Install languages
        $this->runWithTimer('Installing language packages', function () use ($files) {
            // Remove language files
            foreach (glob(lang_path('/*'), GLOB_ONLYDIR) as $path) {
                FileHelper::recursiveRemove($path);
            }
            $this->writeTranslationsToFiles($files['lang_en'], [], 'en', true);
            $this->writeTranslationsToFiles($files['lang_de'], [], 'de', true);
        });

        // Generate login css
        $this->runWithTimer('Generating css files', fn() => $this->generateLoginCSS());

        // Installing side menu
        $this->runWithTimer('Creating dashboard side menu', fn() => $this->installAdminMenu($files['admin_menu']));
    }
}
