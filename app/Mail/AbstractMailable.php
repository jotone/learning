<?php

namespace App\Mail;

use App\Models\{EmailTemplate, Settings};
use Illuminate\Mail\Mailable;

class AbstractMailable extends Mailable
{
    /**
     * List of server settings
     *
     * @var array
     */
    protected array $settings;

    /**
     * Email template
     *
     * @var EmailTemplate
     */
    protected EmailTemplate $template;

    public function __construct()
    {
        // Get setting
        $this->settings = Settings::select(['key', 'value', 'data_type'])
            ->whereIn('section', ['email-settings', 'main-colors', 'site-info'])
            ->get()
            ->map(function ($model) {
                $model->value = $model->val;
                return $model;
            })
            ->keyBy('key')
            ->toArray();

        // Remove a logo image if it does not exist
        if (!file_exists(public_path($this->settings['logo_img']['value']))) {
            $this->settings['logo_img']['value'] = null;
        }
    }

    /**
     * Replace variables with given data
     *
     * @param array $content
     * @return array
     */
    protected function applyVariables(array $content): array
    {
        foreach ($content as $key => $value) {
            foreach ($this->template->variables as $name => $options) {
                [$type, $field] = $options;
                switch ($type) {
                    /* TODO: add course entity
                    case 'course':
                        break;*/
                    case 'custom':
                        $content[$key] = $field;
                        break;
                    case 'user':
                        $content[$key] = preg_replace('/%' . $name . '%/', $this->user->{$field}, $value);
                        break;
                    case 'settings':
                        $content[$key] = $this->settings[$field];
                        break;
                }
            }
        }
        return $content;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}