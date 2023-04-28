<?php

namespace App\Mail;

use App\Models\{EmailTemplate, Settings, SocialMediaLink, User};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * List of server settings
     * @var
     */
    protected $settings;

    /**
     * Email template
     * @var EmailTemplate
     */
    protected EmailTemplate $template;

    /**
     * Create a new message instance.
     */
    public function __construct(protected User $user)
    {
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

        $this->template = EmailTemplate::firstWhere('slug', 'registration-email');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Email',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.registration',
            with: [
                'activation_url' => '#',
                'content'        => $this->applyVariables([
                    'body'        => $this->template->body,
                    'footer_text' => $this->template->footer_text,
                    'subject'     => $this->template->subject
                ]),
                'settings'       => $this->settings,
                'social_links'   => SocialMediaLink::select(['type', 'url'])->orderBy('position')->get()
            ]
        );
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
                    case 'user':
                        $content[$key] = preg_replace('/%' . $name . '%/', $this->user->{$field}, $value);
                        break;
                    case 'settings':
                        $content[$key] = $this->settings->{$field};
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
