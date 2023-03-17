<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\{Schema, View};
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
        if (Schema::hasTable('settings')) {
            $settings = Settings::whereIn('key', ['site_title', 'main_language'])
                ->get()
                ->pluck('value', 'key')
                ->toArray();

            if (!empty($settings)) {
                View::share('settings', $settings);
            }
        }
    }
}
