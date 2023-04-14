<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait ConfigTrait
{
    protected function setEmailConfig(array $args): void
    {
        if (config('app.env') != 'local') {
            Config::set('mail.mailers.smtp.username', $args['smtp_username']);
            Config::set('mail.mailers.smtp.password', $args['smtp_password']);
            Config::set('mail.mailers.smtp.host', $args['smtp_host']);
            Config::set('mail.mailers.smtp.port', $args['smtp_port']);
            Config::set('mail.mailers.smtp.encryption', $args['smtp_encryption']);
            Config::set('mail.mailers.smtp.encryption', $args['smtp_encryption']);
            Config::set('mail.from.address', $args['smtp_from_address']);
            Config::set('mail.from.name', $args['smtp_from_name']);
        }
    }
}