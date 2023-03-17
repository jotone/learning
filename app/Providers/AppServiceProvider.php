<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\{DB, Schema, View};
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

            if (!empty($settings)) {
                View::share('settings', $settings);
            }
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
