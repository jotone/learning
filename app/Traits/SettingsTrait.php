<?php

namespace App\Traits;

use App\Models\Settings;

trait SettingsTrait
{
    /**
     * Generate /css/login.css file.
     * Template is /views/custom-styles/login-css.blade.php
     *
     * @return void
     */
    protected function generateLoginCSS(): void
    {
        // Generate css
        $html = view('custom-styles.login-css', [
            'settings' => Settings::where('section', 'login-page')->get()->keyBy('key')
        ])->render();
        // Minify css file
        $html = preg_replace('/\s*([{}:;,])\s*/', '$1', str_replace("\n", "", $html));
        // Save file
        file_put_contents(public_path('/css/login.css'), $html);
    }
}