<?php

namespace App\Console\Commands;

use App\Models\{Role, Settings, User};
use App\Traits\{CommandsTrait, LanguageHelper, SettingsTrait};
use Illuminate\Console\Command;

class AppInstall extends Command
{
    use CommandsTrait, LanguageHelper, SettingsTrait;

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

        $install_path = base_path('app/Console/Commands/InstallationData/');

        $files = [
            'admin_menu' => null,
            'lang_en'    => null,
            'lang_de'    => null,
            'roles'      => null,
            'settings'   => null,
        ];

        foreach ($files as $file => $data) {
            try {
                $files[$file] = json_decode(file_get_contents($install_path . $file . '.json'), 1);
            } catch (\Exception $exception) {
                $this->error('Error occurred while ' . $file . ' file reading. (app/Console/Commands/AppInstallData/' . $file . '.json)');
                $this->error($exception->getMessage());
            }
        }

        $this->runWithTimer('Creating user roles', function () use ($files) {
            foreach ($files['roles'] as $slug => $data) {
                Role::firstOrCreate([
                    'name'  => $data['name'],
                    'slug'  => $slug,
                    'level' => $data['level']
                ]);
            }
        });

        $this->runWithTimer('Creating superuser account', function () use ($files) {
            return User::whereHas('role', fn($q) => $q->where('level', '<', 1))->count()
                ? User::whereHas('role', fn($q) => $q->firstWhere('level', '<', 1))
                : User::create([
                    'first_name'        => 'Superuser',
                    'email'             => 'superadmin@mail.com',
                    'email_verified_at' => now(),
                    'password'          => base64_decode('U29mdDkzMjg2NA=='),
                    'activated_at'      => now(),
                    'role_id'           => Role::where('level', '0')->value('id'),
                    'status'            => 0
                ]);
        });

        $this->runWithTimer('Installing settings', function () use ($files) {
            foreach ($files['settings'] as $section => $settings) {
                foreach ($settings as $i => $setting) {
                    if ($setting['key'] == 'sign_up_date') {
                        $setting['value'] = now();
                    }
                    Settings::create([
                        'key'      => $setting['key'],
                        'value'    => $setting['value'],
                        'section'  => $section,
                        'caption'  => $setting['caption'],
                        'about'    => $setting['about'] ?? '',
                        'position' => $i
                    ]);
                }
            }
        });

        $this->runWithTimer('Installing language packages', function () use ($files) {
            $this->writeTranslationsToFiles($files['lang_en'], [], 'en', true);
        });

        // Generate login css
        $this->runWithTimer('Generating css files', fn () => $this->generateLoginCSS());

        $this->runWithTimer('Creating dashboard side menu', fn () => $this->installAdminMenu($files));
    }
}
