<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\{App, DB, Schema, URL, View};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->dbConnected() && Schema::hasTable('settings')) {
            $settings = Settings::whereIn('key', ['site_title', 'main_language'])
                ->get()
                ->pluck('value', 'key')
                ->toArray();

            if (isset($settings['main_language'])) {
                App::setLocale($settings['main_language']);
            }

            if (!empty($settings)) {
                View::share('settings', $settings);
            }
        }

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

    }

    /**
     * Check database is connected
     * @return bool
     */
    protected function dbConnected(): bool
    {
        try {
            DB::connection()->getPDO();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
