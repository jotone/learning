<?php

namespace App\Console\Commands;

use App\Classes\FileHelper;
use App\Models\{AdminMenu, Role, User};
use App\Traits\{CommandsTrait, LanguageHelper, PermissionListTrait, SettingsTrait};
use Illuminate\Console\Command;

class AppReinstall extends Command
{
    use CommandsTrait, LanguageHelper, PermissionListTrait, SettingsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reinstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will reinstall some application data.';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $this->components->info('Running reinstall.');

        $files = $this->installationFiles();

        $this->runWithTimer('Reinstalling user roles', function () use ($files) {
            // Get role list with users "$role_slug => $user_ids"
            $role_users = [];
            foreach (Role::pluck('slug', 'id')->toArray() as $role_id => $slug) {
                $users = User::where('role_id', $role_id)->pluck('id')->toArray();
                if (!empty($users)) {
                    $role_users[$slug] = $users;
                }
            }
            // Remove old roles with permissions
            Role::getQuery()->delete();
            // Reinstall roles
            $roles = $this->installRoles($files['roles']);
            // Set new roles to users
            foreach ($role_users as $role_slug => $user_ids) {
                // If there is no existent role for user, set it as student
                User::whereIn('id', $user_ids)->update(['role_id' => $roles[$role_slug] ?? $roles['student']]);
            }
        });

        // Install super user permissions
        $this->installSuperUser();

        // Install languages
        $this->runWithTimer('Reinstalling language packages', function () use ($files) {
            // Remove language files
            foreach (glob(lang_path('/*'), GLOB_ONLYDIR) as $path) {
                FileHelper::recursiveRemove($path);
            }
            $this->writeTranslationsToFiles($files['lang_en'], [], 'en', true);
            $this->writeTranslationsToFiles($files['lang_de'], [], 'de', true);
        });

        // Generate login css
        $this->runWithTimer('Generating css files', fn() => $this->generateLoginCSS());

        // Reinstalling side menu
        AdminMenu::truncate();
        $this->runWithTimer('Recreating dashboard side menu', fn() => $this->installAdminMenu($files['admin_menu']));
    }
}
